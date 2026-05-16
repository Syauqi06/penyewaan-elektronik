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

    public function store(Request $request, int $barang_id)
    {
        // 1. Ambil data barang
        $barang = \App\Models\Barang::findOrFail($barang_id);

        // Validasi sederhana (hardcode 1 hari sewa untuk testing, nanti bisa dinamis dari form)
        $hari_sewa = 1; 
        $total_biaya = $barang->harga_sewa_perhari * $hari_sewa;

        // 2. Buat data Peminjaman
        $peminjaman = \App\Models\Peminjaman::create([
            'user_id' => Auth::id(),
            'tanggal_pinjam' => now(),
            'tanggal_kembali_rencana' => now()->addDays($hari_sewa),
            'total_biaya_sewa' => $total_biaya,
            'sisa_pembayaran' => $total_biaya,
            'status' => 'menunggu_pembayaran',
        ]);

        // 3. Buat Detail Pinjam
        \App\Models\DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'harga_sewa_satuan' => $barang->harga_sewa_perhari,
            'subtotal' => $total_biaya,
        ]);

        // Redirect ke halaman pembayaran
        return redirect()->route('checkout.bayar', $peminjaman->id);
    }

    public function halamanBayar(int $peminjaman_id)
    {
        $peminjaman = \App\Models\Peminjaman::with('detailPinjams.barang')->findOrFail($peminjaman_id);
        
        // Cek jika status bukan menunggu pembayaran, lempar ke dashboard
        if($peminjaman->status !== 'menunggu_pembayaran'){
            return redirect('/dashboard')->with('error', 'Pesanan ini sudah diproses.');
        }

        return view('checkout.bayar', compact('peminjaman'));
    }
}