<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $guarded = ['id'];
    protected $table = 'kategoris';

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}