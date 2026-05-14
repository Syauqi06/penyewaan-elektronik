<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerahTerima extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tgl_serah' => 'datetime',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function admin()
    {
        // Relasi ke tabel user (sebagai admin yang memproses)
        return $this->belongsTo(User::class, 'user_id'); 
    }
}