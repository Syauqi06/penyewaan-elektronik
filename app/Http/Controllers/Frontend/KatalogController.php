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
}
