<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlamatUser extends Model
{
    protected $table = 'alamat_user';
    protected $guarded = ['id'];

    // Relasi kembali ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Peminjaman (1 Alamat bisa dipakai di banyak transaksi)
    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
