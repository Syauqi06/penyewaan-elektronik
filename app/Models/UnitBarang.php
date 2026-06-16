<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitBarang extends Model
{
    protected $table = 'unit_barang';

    protected $guarded = ['id'];

    public function katalog_barang()
    {
        return $this->belongsTo(KatalogBarang::class);
    }

    public function detail_peminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
