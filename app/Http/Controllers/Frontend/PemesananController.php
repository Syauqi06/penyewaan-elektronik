<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request, $id)
    {
        // Validasi data yang dikirim dari form checkout
        $request->validate([
            'alamat_id' => 'required|exists:alamat_user,id',
            'tgl_pesan' => 'required|date',
            'tgl_kembali' => 'required|date|after:tgl_pesan',
        ]);

        return redirect()->route('dashboard')->with('success', 'Hore! Pesanan Anda berhasil dibuat dan sedang diproses.');
    }
}
