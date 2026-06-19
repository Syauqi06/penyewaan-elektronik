<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KatalogBarang;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index()
    {
        $katalog = KatalogBarang::with(['kategori'])->withCount(['unit_barangs as stok_tersedia' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->latest()->get();

        return view('frontend.katalog', compact('katalog'));
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
