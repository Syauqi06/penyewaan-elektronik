<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Pembayaran;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function generateToken(Peminjaman $peminjaman)
    {
        // Set konfigurasi midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'PAY-' . $peminjaman->id . '-' . Str::random(5);
        $jumlahBayar = $peminjaman->jumlah_dp > 0 ? $peminjaman->jumlah_dp : $peminjaman->total_biaya_sewa;

        // Simpan ke tabel pembayaran
        $pembayaran = Pembayaran::create([
            'peminjaman_id' => $peminjaman->id,
            'id_transaksi_midtrans' => $orderId,
            'jumlah_bayar' => $jumlahBayar,
            'jenis_pembayaran' => $peminjaman->jumlah_dp > 0 ? 'dp' : 'pelunasan',
            'status_pembayaran' => 'pending',
        ]);

        $user = Auth::user();
        
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $jumlahBayar,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->telepon,
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}