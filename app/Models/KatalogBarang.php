<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KatalogBarang extends Model
{
    protected $table = 'katalog_barang';
    protected $guarded = ['id'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function unit_barangs()
    {
        return $this->hasMany(UnitBarang::class);
    }
}
