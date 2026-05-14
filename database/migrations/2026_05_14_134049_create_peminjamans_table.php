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
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->integer('total_biaya_sewa');
            $table->integer('jumlah_dp')->default(0);
            $table->integer('sisa_pembayaran');
            $table->boolean('persetujuan_syarat')->default(false);
            $table->dateTime('tgl_setuju_syarat')->nullable();
            $table->enum('status', ['menunggu_pembayaran', 'diproses', 'aktif', 'selesai', 'dibatalkan'])->default('menunggu_pembayaran');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
