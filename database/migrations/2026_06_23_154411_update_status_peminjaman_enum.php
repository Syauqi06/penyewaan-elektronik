<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_peminjaman ENUM('pending', 'disetujui', 'ditolak', 'aktif', 'menunggu_refund', 'menunggu_pelunasan', 'selesai') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_peminjaman ENUM('pending', 'disetujui', 'ditolak', 'aktif', 'selesai') DEFAULT 'pending'");
    }
};