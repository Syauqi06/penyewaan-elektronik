<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Menangani kembalian (callback) dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user dengan email atau google_id tersebut sudah ada
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Jika email sudah ada, update google_id (berjaga-jaga jika dulunya daftar manual)
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                ]);
                Auth::login($user);
            } else {
                // Jika belum ada, buat user baru sebagai 'penyewa'
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'role' => 'penyewa',
                    // Password dikosongkan karena login via Google
                ]);
                Auth::login($newUser);
            }

            // Arahkan ke halaman dashboard penyewa (katalog) setelah berhasil login
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Gagal login menggunakan Google. Silakan coba lagi.');
        }
    }
}
