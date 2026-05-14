<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $guarded = ['id'];
    protected $table = 'pengembalians';

    protected function casts(): array
    {
        return [
            'tgl_kembali_aktual' => 'datetime',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}