<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tgl_denda' => 'datetime',
        ];
    }

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}