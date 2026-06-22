<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AlamatUser;
use App\Models\Peminjaman;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $alamats = $user->alamat_users; 
        $verifikasi = $user->verifikasi_identitas;
        
        $peminjamans = Peminjaman::with(['detail_peminjaman.unit_barang.katalog_barang', 'pembayaran'])
                        ->where('user_id', $user->id)
                        ->latest('tanggal_pesan')
                        ->get();

        return view('frontend.dashboard', compact('user', 'alamats', 'verifikasi', 'peminjamans'));
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

    public function uploadKtp()
    {
        $user = Auth::user();
        $verifikasi = $user->verifikasi_identitas;

        // Jika sudah disetujui atau pending, jangan izinkan upload ulang
        if ($verifikasi && in_array($verifikasi->status, ['pending', 'disetujui'])) {
            return redirect()->route('dashboard')->with('success', 'KTP Anda sedang diproses atau sudah disetujui.');
        }

        return view('frontend.upload-ktp', compact('user'));
    }

    // Menyimpan file KTP ke storage
    public function storeKtp(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // Simpan gambar ke folder storage/app/public/ktp
        $path = $request->file('foto_ktp')->store('ktp', 'public');

        // Simpan atau update ke database
        \App\Models\VerifikasiIdentitas::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'foto_ktp' => $path,
                'status' => 'pending',
                'catatan' => null 
            ]
        );

        return redirect()->route('dashboard')->with('success', 'KTP berhasil diunggah! Mohon tunggu verifikasi dari Admin.');
    }
}
