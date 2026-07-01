<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    // 1. Method untuk Landing Page (Beranda)
    public function beranda()
    {
        // Ambil 4 produk terbaru aja buat dipajang di halaman depan
        $katalog = KatalogBarang::with(['kategori'])->withCount(['unit_barangs as stok_tersedia' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->latest()->take(4)->get();

        return view('frontend.beranda', compact('katalog'));
    }

    // 2. Method untuk Halaman Katalog Full
    public function index(Request $request)
    {
        // 1. Tangkap kata kunci pencarian dari URL
        $search = $request->input('search');

        // 2. Query data dengan fitur pencarian
        $katalog = KatalogBarang::with(['kategori'])
            ->withCount(['unit_barangs as stok_tersedia' => function ($query) {
                $query->where('status_ketersediaan', 'tersedia');
            }])
            ->when($search, function ($query, $search) {
                // Filter berdasarkan nama barang ATAU nama kategori
                return $query->where('nama_barang', 'like', '%' . $search . '%')
                             ->orWhereHas('kategori', function ($q) use ($search) {
                                 $q->where('nama_kategori', 'like', '%' . $search . '%');
                             });
            })
            ->latest()
            ->paginate(12)
            ->appends(['search' => $search]); // Wajib pakai ini biar pas klik Page 2, kata kuncinya gak hilang!

        // 3. Lempar datanya ke view
        return view('frontend.katalog', compact('katalog', 'search'));
    }

    public function show(int $id)
    {
        $katalog = KatalogBarang::with(['kategori'])->withCount(['unit_barangs as stok_tersedia' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->findOrFail($id);

        return view('frontend.detail', compact('katalog'));
    }

    public function kategori()
    {
        $kategori = \App\Models\Kategori::all();
        return view('frontend.kategori', compact('kategori'));
    }
}