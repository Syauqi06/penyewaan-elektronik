<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Carbon\Carbon;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // 1. Setup konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            // 2. Ambil data notifikasi resmi dari Midtrans
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Notification'], 400);
        }

        // 3. Ambil variabel penting dari response Midtrans
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $donation = $notif->fraud_status;

        // 4. Cari data Pembayaran di database berdasarkan Order ID Midtrans
        $pembayaran = Pembayaran::where('kode_transaksi_gateway', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Ambil data Peminjaman induknya
        $peminjaman = $pembayaran->peminjaman;

        // 5. LOGIKA STATUS PEMBAYARAN BERDASARKAN SIGNAL MIDTRANS
        if ($transaction == 'settlement' || $transaction == 'capture') {
            
            // --- KONDISI 1: PEMBAYARAN LUNAS ---
            $pembayaran->update([
                'status_pembayaran' => 'settlement',
                'metode_pembayaran' => $type,
                'tanggal_bayar' => Carbon::now()
            ]);

            // Jika yang lunas adalah DP/Tagihan Awal, ubah status peminjaman jadi 'disetujui'
            if ($pembayaran->jenis_pembayaran == 'tagihan_awal') {
                $peminjaman->update([
                    'status_peminjaman' => 'disetujui' // Sesuai enum lu ['pending', 'disetujui', ...]
                ]);
            }

        } elseif ($transaction == 'pending') {
            
            // --- KONDISI 2: MENUNGGU PEMBAYARAN (USER DAPET KODE VA) ---
            $pembayaran->update(['status_pembayaran' => 'pending']);

        } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
            
            // --- KONDISI 3: GAGAL / KADALUARSA / DIBATALKAN ---
            $pembayaran->update(['status_pembayaran' => $transaction]);
            
            $peminjaman->update(['status_peminjaman' => 'ditolak']);

            // LEPAS KEMBALI UNIT BARANG YANG TADI DI-BOOKING BIAR BISA DISEWA ORANG LAIN
            // Kita loop semua detail barang di nota ini
            foreach ($peminjaman->detail_peminjaman as $detail) {
                if ($detail->unit_barang) {
                    $detail->unit_barang->update([
                        'status_ketersediaan' => 'tersedia' // Kembalikan status unit jadi 'tersedia'
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Callback Midtrans Sukses Diproses']);
    }
}