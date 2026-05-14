<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiPinjam extends Model
{
    protected $guarded = ['id'];
    protected $table = 'verifikasi_pinjams';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}