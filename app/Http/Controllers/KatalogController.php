<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class KatalogController extends Controller
{
    public function index()
    {
        // Mengambil semua barang yang statusnya tersedia, urutkan dari yang terbaru
        $barangs = Barang::with('kategori')
                    ->where('status', 'tersedia')
                    ->latest()
                    ->get();

        return view('welcome', compact('barangs'));
    }
}