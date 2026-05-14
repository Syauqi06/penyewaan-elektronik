<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $guarded = ['id'];
    protected $table = 'deposits';

    protected function casts(): array
    {
        return [
            'tgl_proses' => 'datetime',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}