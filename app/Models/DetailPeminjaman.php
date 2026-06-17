<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    protected $table = 'detail_peminjaman';
    protected $guarded = ['id'];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function unit_barang()
    {
        return $this->belongsTo(UnitBarang::class);
    }
}
