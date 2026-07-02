<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Midtrans\Transaction;

class MidtransController extends Controller
{
    // public function callback(Request $request)
    // {
    //     // \Illuminate\Support\Facades\Log::info('Callback Midtrans diterima: ', $request->all());
    //     \Midtrans\Config::$serverKey = config('midtrans.server_key');
    //     \Midtrans\Config::$isProduction = config('midtrans.is_production');

    //     try {
    //         $notif = new \Midtrans\Notification();
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Invalid Notification'], 400);
    //     }

    //     $transaction = $notif->transaction_status;
    //     $type = $notif->payment_type;
    //     $orderId = $notif->order_id;

    //     $pembayaran = Pembayaran::where('kode_transaksi_gateway', $orderId)->first();

    //     if (!$pembayaran) {
    //         return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    //     }

    //     $peminjaman = $pembayaran->peminjaman;
        
    //     // --- DETEKSI APAKAH INI DENDA ATAU SEWA ---
    //     $isDenda = str_starts_with($orderId, 'DNDA-');

    //     if ($transaction == 'settlement' || $transaction == 'capture') {
            
    //         // Update status pembayaran
    //         $pembayaran->update([
    //             'status_pembayaran' => 'settlement',
    //             'metode_pembayaran' => $type,
    //             'tanggal_bayar' => Carbon::now()
    //         ]);

    //         if ($isDenda) {
    //             // --- LOGIKA SETELAH BAYAR DENDA ---
    //             $peminjaman->pengembalian()->update(['status_denda' => 'sudah_bayar']);
    //             $peminjaman->update(['status_peminjaman' => 'menunggu_konfirmasi_denda']);
    //         } else {
    //             // --- LOGIKA SETELAH BAYAR SEWA ---
    //             $peminjaman->update(['status_peminjaman' => 'disetujui']);
    //         }

    //     } elseif ($transaction == 'pending') {
    //         $pembayaran->update(['status_pembayaran' => 'pending']);

    //     } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
            
    //         $pembayaran->update(['status_pembayaran' => $transaction]);
            
    //         // HANYA lepas unit jika ini pembayaran SEWA (bukan denda)
    //         if (!$isDenda) {
    //             $peminjaman->update(['status_peminjaman' => 'ditolak']);
                
    //             foreach ($peminjaman->detail_peminjaman as $detail) {
    //                 $detail->unit_barang?->update(['status_ketersediaan' => 'tersedia']);
    //             }
    //         }
    //     }

    //     return response()->json(['message' => 'Callback diproses dengan sukses']);
    // }

    public function checkStatus(string $order_id)
    {

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        
        try {
            $status = (array) Transaction::status($order_id);
            $transactionStatus = $status['transaction_status'];
        
            // ✅ CARI DI TABEL PEMBAYARAN (bukan Peminjaman)
            $pembayaran = Pembayaran::where('kode_transaksi_gateway', $order_id)->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tidak ditemukan'
                ], 404);
            }

            $peminjaman = $pembayaran->peminjaman;

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                // ✅ UPDATE STATUS DI TABEL PEMBAYARAN
                $pembayaran->update([
                    'status_pembayaran' => 'settlement',
                    'metode_pembayaran' => $status['payment_type'] ?? null,
                    'tanggal_bayar' => Carbon::now()
                ]);

                // ✅ CEK APAKAH INI PEMBAYARAN DENDA
                $isDenda = str_starts_with($order_id, 'DNDA-');
                
                if ($isDenda && $peminjaman) {
                    // ✅ UPDATE STATUS DENDA DI TABEL PENGEMBALIAN
                    if ($peminjaman->pengembalian) {
                        $peminjaman->pengembalian->update([
                            'status_denda' => 'sudah_bayar'
                        ]);
                    }
                    
                    // ✅ UPDATE STATUS PEMINJAMAN
                    $peminjaman->update([
                        'status_peminjaman' => 'menunggu_konfirmasi_denda'
                    ]);
                } else {
                    // Logika untuk pembayaran sewa biasa
                    $peminjaman->update([
                        'status_peminjaman' => 'disetujui'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'midtrans_status' => $transactionStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function processTransaction(string $orderId, string $transaction, string $type, ?string $fraudStatus = null)
    {
        $pembayaran = Pembayaran::where('kode_transaksi_gateway', $orderId)->first();
        if (!$pembayaran) return;

        $peminjaman = $pembayaran->peminjaman;
        $isDenda = str_starts_with($orderId, 'DNDA-');

        if ($transaction == 'settlement' || ($transaction == 'capture' && $fraudStatus == 'accept')) {
            $pembayaran->update([
                'status_pembayaran' => 'settlement',
                'metode_pembayaran' => $type,
                'tanggal_bayar' => Carbon::now()
            ]);

            if ($isDenda) {
                $peminjaman->pengembalian()->update(['status_denda' => 'sudah_bayar']);
                $peminjaman->update(['status_peminjaman' => 'menunggu_konfirmasi_denda']);
            } else {
                $peminjaman->update(['status_peminjaman' => 'disetujui']);
            }
        } elseif ($transaction == 'pending') {
            $pembayaran->update(['status_pembayaran' => 'pending']);
        } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
            $pembayaran->update(['status_pembayaran' => $transaction]);
            if (!$isDenda) {
                $peminjaman->update(['status_peminjaman' => 'ditolak']);
                foreach ($peminjaman->detail_peminjaman as $detail) {
                    $detail->unit_barang?->update(['status_ketersediaan' => 'tersedia']);
                }
            }
        }
    }

    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Notification'], 400);
        }

        $this->processTransaction($notif->order_id, $notif->transaction_status, $notif->payment_type, $notif->fraud_status ?? null);

        return response()->json(['message' => 'Callback diproses dengan sukses']);
    }
}