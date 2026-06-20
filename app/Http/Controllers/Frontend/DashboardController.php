<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AlamatUser;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil data alamat user
        $alamats = $user->alamat_users;
        // Ambil data verifikasi identitas (jika ada)
        $verifikasi = $user->verifikasi_identitas;

        return view('frontend.dashboard', compact('user', 'alamats', 'verifikasi'));
    }

    // Menyimpan alamat baru
    public function storeAlamat(Request $request)
    {
        $request->validate([
            'label_alamat' => 'required|string|max:255',
            'provinsi' => 'required|string',
            'kota_kabupaten' => 'required|string',
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'kode_pos' => 'required|numeric',
            'detail_alamat' => 'required|string',
        ]);

        $alamatLengkap = 'Kel. ' . $request->kelurahan . ', ' . $request->detail_alamat;

        AlamatUser::create([
            'user_id' => Auth::id(),
            'label_alamat' => $request->label_alamat,
            'provinsi' => $request->provinsi,
            'kota_kabupaten' => $request->kota_kabupaten,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'detail_alamat' => $alamatLengkap,
        ]);

        return redirect()->back()->with('success', 'Alamat berhasil ditambahkan!');
    }
}
