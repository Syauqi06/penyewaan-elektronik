<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alamat_user()
    {
        return $this->belongsTo(AlamatUser::class);
    }

    public function detail_peminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
