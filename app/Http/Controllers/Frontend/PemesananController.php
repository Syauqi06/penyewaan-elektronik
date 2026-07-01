<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Pembayaran;
use App\Models\UnitBarang;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use Midtrans\Config;

class PemesananController extends Controller
{
    public function initBooking(Request $request, int $id)
    {
        $request->validate([
            'tgl_pesan' => 'required|date',
            'tgl_kembali' => 'required|date|after:tgl_pesan',
        ]);

        session([
            'booking_tgl_pesan' => $request->tgl_pesan,
            'booking_tgl_kembali' => $request->tgl_kembali,
        ]);

        return redirect()->route('booking.create', $id);
    }

    public function create(Request $request, int $id)
    {
        $katalog = KatalogBarang::findOrFail($id);
        $user = Auth::user();
        
        $alamats = $user->alamat_users; 
        $verifikasi = $user->verifikasi_identitas;

        $tglPesan = session('booking_tgl_pesan');
        $tglKembali = session('booking_tgl_kembali');

        if (!$tglPesan || !$tglKembali) {
            return redirect()->back()->withErrors(['pesan' => 'Mohon pilih tanggal penyewaan terlebih dahulu di halaman detail produk.']);
        }

        if (strtotime($tglKembali) <= strtotime($tglPesan)) {
            return redirect()->back()->withErrors(['pesan' => 'Durasi sewa minimal adalah 1 hari. Tanggal Selesai tidak boleh mundur!']);
        }

        return view('frontend.booking', compact('katalog', 'alamats', 'verifikasi', 'tglPesan', 'tglKembali'));
    }

    public function store(Request $request, int $id)
    {
        // 1. Validasi input
        $request->validate([
            'alamat_id' => 'required|exists:alamat_user,id',
            'tgl_pesan' => 'required|date',
            'tgl_kembali' => 'required|date|after:tgl_pesan',
        ]);

        $katalog = KatalogBarang::findOrFail($id);

        // 2. Cari unit barang fisik yang tersedia
        $unitBarang = UnitBarang::where('katalog_barang_id', $katalog->id)
                            ->where('status_ketersediaan', 'tersedia')
                            ->first();

        // Jika tidak ada unit fisik yang nganggur, tolak!
        if (!$unitBarang) {
            return redirect()->back()->withErrors(['pesan' => 'Mohon maaf, semua unit untuk barang ini sedang disewa.']);
        }

        // 3. Kalkulasi Biaya (MURNI 100% SEWA)
        $tglPesan = Carbon::parse($request->tgl_pesan);
        $tglKembali = Carbon::parse($request->tgl_kembali);
        $durasiHari = $tglPesan->diffInDays($tglKembali);

        if ($durasiHari < 1) {
            return redirect()->back()->withErrors(['pesan' => 'Durasi sewa minimal 1 hari!']);
        }

        $totalSewa = $durasiHari * $katalog->harga_sewa_per_hari;

        // 4. Simpan ke Tabel Peminjaman (Nota Induk)
        $peminjaman = Peminjaman::create([
            'user_id' => Auth::id(),
            'alamat_user_id' => $request->alamat_id,
            'tanggal_pesan' => $tglPesan->format('Y-m-d'),
            'tanggal_kembali_rencana' => $tglKembali->format('Y-m-d'),
            'total_biaya_sewa' => $totalSewa,
            'status_peminjaman' => 'pending', // Menunggu bayar lunas di depan
        ]);

        // 5. Simpan ke Tabel Detail Peminjaman
        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'unit_barang_id' => $unitBarang->id,
            'harga_sewa_satuan' => $katalog->harga_sewa_per_hari,
        ]);

        // 6. Integrasi Midtrans API & Request Snap Token
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        // Bikin ID transaksi gateway unik untuk Midtrans
        $orderId = 'RENT-' . $peminjaman->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalSewa, // Pembayaran Full
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => $katalog->id,
                    'price' => (int) $totalSewa,
                    'quantity' => 1,
                    'name' => 'Sewa ' . $durasiHari . ' Hari - ' . substr($katalog->nama_barang, 0, 20),
                ]
            ]
        ];

        try {
            // Ambil Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Buat data tagihan di tabel Pembayaran
            Pembayaran::create([
                'peminjaman_id' => $peminjaman->id,
                'jumlah_bayar' => $totalSewa,
                'jenis_pembayaran' => 'sewa', // Ubah jadi 'sewa'
                'kode_transaksi_gateway' => $orderId,
                'snap_token' => $snapToken,
                'status_pembayaran' => 'pending',
            ]);

        } catch (\Exception $e) {
            // Jika API Midtrans bermasalah, hapus instansi peminjaman agar tidak gantung, lalu tampilkan error
            $peminjaman->delete();
            return redirect()->back()->withErrors(['pesan' => 'Gagal menghubungkan ke payment gateway Midtrans: ' . $e->getMessage()]);
        }

        // 7. Kunci (Booking) Unit Barang tersebut agar statusnya berubah
        $unitBarang->update([
            'status_ketersediaan' => 'disewa'
        ]);

        // Bersihkan session data booking tanggal
        session()->forget(['booking_tgl_pesan', 'booking_tgl_kembali']);

        return redirect()->route('dashboard')->with('success', 'Hore! Pesanan berhasil dibuat. Silakan cek dashboard Anda untuk menyelesaikan pembayaran.');
    }
}