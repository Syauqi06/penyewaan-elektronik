<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    public function create(Request $request, int $id)
    {
        $katalog = KatalogBarang::findOrFail($id);
        
        // Ambil data user, alamat, dan KTP
        $user = Auth::user();
        $alamats = $user->alamat_users; 
        $verifikasi = $user->verifikasi_identitas;

        // Tangkap request tanggal dari halaman detail jika ada
        $tglPesan = $request->query('tgl_pesan');
        $tglKembali = $request->query('tgl_kembali');

        if (!$tglPesan || !$tglKembali) {
            return redirect()->back()->withErrors(['pesan' => 'Mohon lengkapi Tanggal Mulai dan Tanggal Selesai penyewaan.']);
        }

        if (strtotime($tglKembali) <= strtotime($tglPesan)) {
            return redirect()->back()->withErrors(['pesan' => 'Durasi sewa minimal adalah 1 hari']);
        }

        return view('frontend.booking', compact('katalog', 'alamats', 'verifikasi', 'tglPesan', 'tglKembali'));
    }
}
