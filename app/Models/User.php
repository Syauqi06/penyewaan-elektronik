<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Method wajib dari Filament untuk membatasi akses dashboard
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya user dengan role 'admin' yang bisa masuk panel Filament
        return $this->hasRole('admin'); 
    }

    // Relasi
    public function verifikasiPinjam()
    {
        return $this->hasOne(VerifikasiPinjam::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}