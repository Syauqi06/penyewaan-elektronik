<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MidtransWebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute Google Login
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::get('/', [KatalogController::class, 'index'])->name('home');

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);

Route::middleware(['auth'])->group(function () {
    // Rute untuk memproses pembuatan data Peminjaman
    Route::post('/checkout/{barang_id}', [CheckoutController::class, 'store'])->name('checkout.store');
    
    // Rute halaman pembayaran
    Route::get('/pembayaran/{peminjaman_id}', [CheckoutController::class, 'halamanBayar'])->name('checkout.bayar');
    
    // API untuk ambil Token Snap Midtrans
    Route::get('/pembayaran/{peminjaman}/token', [CheckoutController::class, 'generateToken'])->name('checkout.token');
});

require __DIR__.'/auth.php';
