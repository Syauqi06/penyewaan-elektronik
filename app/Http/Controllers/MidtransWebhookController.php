<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id; // Format yang akan kita buat: PEMBAYARAN-{ID}

            // Cari data pembayaran berdasarkan order ID
            $pembayaran = Pembayaran::where('id_transaksi_midtrans', $orderId)->first();

            if (!$pembayaran) {
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // Update status pembayaran
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $pembayaran->update(['status_pembayaran' => 'success']);
                
                // Update status peminjaman jadi 'diproses' jika bayar DP/Lunas awal
                Peminjaman::where('id', $pembayaran->peminjaman_id)
                    ->where('status', 'menunggu_pembayaran')
                    ->update(['status' => 'diproses']);

            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $pembayaran->update(['status_pembayaran' => 'failed']);
            } elseif ($transactionStatus == 'pending') {
                $pembayaran->update(['status_pembayaran' => 'pending']);
            }

            return response()->json(['message' => 'Webhook berhasil diproses']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}