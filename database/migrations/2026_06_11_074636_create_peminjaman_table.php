<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('alamat_user_id')->constrained('alamat_user'); // Wajib diisi untuk kurir 100% online
            $table->date('tanggal_pesan');
            $table->date('tanggal_kembali_rencana');
            $table->bigInteger('total_biaya_sewa');
            $table->string('foto_kondisi_awal')->nullable();
            $table->enum('status_peminjaman', ['pending', 'disetujui', 'ditolak', 'aktif', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
