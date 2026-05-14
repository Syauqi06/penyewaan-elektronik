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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->string('id_transaksi_midtrans')->nullable(); // Untuk menyimpan Order ID Midtrans
            $table->integer('jumlah_bayar');
            $table->enum('jenis_pembayaran', ['dp', 'pelunasan', 'denda']);
            $table->string('metode_pembayaran')->nullable(); // e.g., bank_transfer, gopay
            $table->string('bukti_pembayaran')->nullable(); // Jika manual
            $table->enum('status_pembayaran', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->dateTime('tgl_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
