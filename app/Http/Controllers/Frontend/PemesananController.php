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

        // 2. CARI UNIT BARANG FISIK YANG TERSEDIA
        $unitBarang = UnitBarang::where('katalog_barang_id', $katalog->id)
                            ->where('status_ketersediaan', 'tersedia')
                            ->first();

        // Jika tidak ada unit fisik yang nganggur, tolak!
        if (!$unitBarang) {
            return redirect()->back()->withErrors(['pesan' => 'Mohon maaf, semua unit untuk barang ini sedang disewa.']);
        }

        // 3. Kalkulasi Biaya
        $tglPesan = Carbon::parse($request->tgl_pesan);
        $tglKembali = Carbon::parse($request->tgl_kembali);
        $durasiHari = $tglPesan->diffInDays($tglKembali);

        if ($durasiHari < 1) {
            return redirect()->back()->withErrors(['pesan' => 'Durasi sewa minimal 1 hari!']);
        }

        $totalSewa = $durasiHari * $katalog->harga_sewa_per_hari;
        $deposit = $katalog->harga_asli * 0.3;
        $dpSewa = $totalSewa * 0.5;
        $totalTagihanAwal = $dpSewa + $deposit;


        // 4. Simpan ke Tabel PEMINJAMAN (Nota Induk)
        $peminjaman = Peminjaman::create([
            'user_id' => Auth::id(),
            'alamat_user_id' => $request->alamat_id,
            'tanggal_pesan' => $tglPesan->format('Y-m-d'),
            'tanggal_kembali_rencana' => $tglKembali->format('Y-m-d'),
            'total_biaya_sewa' => $totalSewa,
            'jumlah_dp' => $dpSewa,
            'jumlah_deposit' => $deposit,
            'status_peminjaman' => 'pending',
        ]);

        // 5. Simpan ke Tabel DETAIL PEMINJAMAN (Anak)
        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'unit_barang_id' => $unitBarang->id,
            'tanggal_mulai' => $tglPesan->format('Y-m-d'),
            'tanggal_selesai' => $tglKembali->format('Y-m-d'),
            'harga_sewa_harian' => $katalog->harga_sewa_per_hari,
            'subtotal' => $totalSewa,
        ]);

        // 6. Buat Tagihan di Tabel PEMBAYARAN (Agar user bisa upload bukti TF)
        Pembayaran::create([
            'peminjaman_id' => $peminjaman->id,
            'jumlah_bayar' => $totalTagihanAwal,
            'metode_pembayaran' => 'transfer', // default
            'status_pembayaran' => 'belum_dibayar', // Sesuaikan enum lu
        ]);

        // 7. Kunci (Booking) Unit Barang tersebut!
        $unitBarang->update([
            'status_ketersediaan' => 'disewa'
        ]);

        // Bersihkan session
        session()->forget(['booking_tgl_pesan', 'booking_tgl_kembali']);

        return redirect()->route('dashboard')->with('success', 'Hore! Pesanan berhasil dibuat. Silakan cek tagihan pembayaran Anda.');
    }
}
