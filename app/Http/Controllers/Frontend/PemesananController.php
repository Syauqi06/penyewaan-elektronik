<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    public function create(KatalogBarang $katalog)
    {
        $stokTersedia = $katalog->unit_barangs()->where('status_ketersediaan', 'tersedia')->count();
        if ($stokTersedia < 1) {
            return redirect()->route('katalog.index')->with('error', 'Maaf, stok barang sedang kosong.');
        }

        $alamats = Auth::user()->alamat_users;

        return view('frontend.booking', compact('katalog', 'alamats'));
    }
}
