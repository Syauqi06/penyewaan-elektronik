<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $guarded = ['id'];
    protected $table = 'peminjamans';

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali_rencana' => 'date',
            'tgl_setuju_syarat' => 'datetime',
            'persetujuan_syarat' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjams()
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function serahTerimas()
    {
        return $this->hasMany(SerahTerima::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}