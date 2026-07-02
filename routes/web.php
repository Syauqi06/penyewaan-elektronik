<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Frontend\KatalogController;
use App\Http\Controllers\Frontend\PemesananController;
use App\Http\Controllers\Frontend\DashboardController;
use App\Http\Controllers\Frontend\MidtransController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. ROUTE PUBLIC (Tidak butuh login)
// ==========================================
Route::get('/', [KatalogController::class, 'beranda'])->name('beranda');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{id}', [KatalogController::class, 'show'])->name('katalog.show');
Route::get('/kategori', [KatalogController::class, 'kategori'])->name('kategori.index');

// ==========================================
// 2. ROUTE AUTH GOOGLE
// ==========================================
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ==========================================
// 3. ROUTE WEBHOOK MIDTRANS
// ==========================================
// Wajib ditaruh di luar auth karena yang akses ini adalah server Midtrans!
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

// ==========================================
// 4. ROUTE PRIVATE (Wajib Login)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Utama Penyewa
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pesanan/{id}', [DashboardController::class, 'showPesanan'])->name('pesanan.show');
    
    // Manajemen Alamat & Verifikasi KTP
    Route::post('/dashboard/alamat', [DashboardController::class, 'storeAlamat'])->name('alamat.store');
    Route::get('/dashboard/verifikasi', [DashboardController::class, 'uploadKtp'])->name('ktp.upload');
    Route::post('/dashboard/verifikasi', [DashboardController::class, 'storeKtp'])->name('ktp.store');

    // Alur Pemesanan (Sewa)
    Route::post('/sewa/{katalog}/init', [PemesananController::class, 'initBooking'])->name('booking.init');
    Route::get('/sewa/{katalog}', [PemesananController::class, 'create'])->name('booking.create');
    Route::post('/sewa/{katalog}', [PemesananController::class, 'store'])->name('booking.store');

    // Profile Bawaan Laravel (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/midtrans/check-status/{order_id}', [MidtransController::class, 'checkStatus'])->name('midtrans.check-status');
    
    Route::post('/dashboard/pesanan/{id}/kembali', [DashboardController::class, 'prosesKembalikanBarang'])->name('pesanan.kembali');
    Route::post('/pesanan/{id}/bayar-denda', [PemesananController::class, 'bayarDenda'])->name('pesanan.bayar-denda');

    Route::post('/pesanan/{id}/kembalikan', [PemesananController::class, 'kembalikanBarang'])->name('pesanan.kembalikan');

});

require __DIR__.'/auth.php';