<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use App\Mail\PengingatBatasWaktuMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendRentalReminders extends Command
{
    // Nama command yang bakal dipanggil oleh sistem cron
    protected $signature = 'rental:send-reminders';

    // Deskripsi gunanya command ini
    protected $description = 'Mengirim email pengingat otomatis ke user yang masa sewanya habis besok';

    public function handle()
    {
        // 1. Cari tanggal besok
        $besok = Carbon::tomorrow()->format('Y-m-d');

        // 2. Cari semua peminjaman yang statusnya 'aktif' dan harus kembali besok
        $peminjamans = Peminjaman::with(['user', 'detail_peminjaman.unit_barang.katalog_barang'])
                        ->where('status_peminjaman', 'aktif')
                        ->whereDate('tanggal_kembali_rencana', $besok)
                        ->get();

        if ($peminjamans->isEmpty()) {
            $this->info('Tidak ada transaksi sewa yang berakhir besok.');
            return;
        }

        // 3. Loop dan tembak email satu per satu!
        foreach ($peminjamans as $peminjaman) {
            if ($peminjaman->user && $peminjaman->user->email) {
                Mail::to($peminjaman->user->email)->send(new PengingatBatasWaktuMail($peminjaman));
            }
        }

        $this->info('Sukses mengirim email pengingat ke ' . $peminjamans->count() . ' user!');
    }
}