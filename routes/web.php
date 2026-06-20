<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Frontend\KatalogController;
use App\Http\Controllers\Frontend\PemesananController;
use App\Http\Controllers\Frontend\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/kategori', [KatalogController::class, 'kategori'])->name('kategori.index');
Route::get('/katalog/{id}', [KatalogController::class, 'show'])->name('katalog.show');

Route::middleware('auth')->group(function () {
    Route::get('/sewa/{katalog}', [PemesananController::class, 'create'])->name('booking.create');
});

Route::middleware(['auth'])->group(function () {
    // Route Dashboard Utama Penyewa
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route Simpan Alamat
    Route::post('/dashboard/alamat', [DashboardController::class, 'storeAlamat'])->name('alamat.store');
    
    // Route form booking yang sebelumnya kita buat (biarkan tetap ada)
    Route::post('/sewa/{katalog}/init', [PemesananController::class, 'initBooking'])->name('booking.init');
    Route::get('/sewa/{katalog}', [PemesananController::class, 'create'])->name('booking.create');
    Route::post('/sewa/{katalog}', [PemesananController::class, 'store'])->name('booking.store');

    Route::get('/dashboard/verifikasi', [DashboardController::class, 'uploadKtp'])->name('ktp.upload');
    Route::post('/dashboard/verifikasi', [DashboardController::class, 'storeKtp'])->name('ktp.store');
});

require __DIR__.'/auth.php';
