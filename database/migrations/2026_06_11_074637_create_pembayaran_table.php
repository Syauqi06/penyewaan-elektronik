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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->bigInteger('jumlah_bayar');
            $table->enum('jenis_pembayaran', ['dp', 'pelunasan', 'deposit', 'denda']);
            $table->string('metode_pembayaran')->nullable(); // Diisi otomatis dari response Midtrans
            $table->string('kode_transaksi_gateway')->nullable(); // Order ID / Transaction ID Midtrans
            $table->string('status_pembayaran')->default('pending'); // pending, settlement, expire, deny
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
