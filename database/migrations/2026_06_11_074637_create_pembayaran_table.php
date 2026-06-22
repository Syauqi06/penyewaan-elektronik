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
            
            $table->enum('jenis_pembayaran', ['tagihan_awal', 'pelunasan', 'denda']); 
            
            $table->string('metode_pembayaran')->nullable(); 
            $table->string('kode_transaksi_gateway')->nullable(); // Order ID Midtrans
            $table->string('snap_token')->nullable(); // Token untuk manggil popup Midtrans
            $table->dateTime('tanggal_bayar')->nullable(); // Tanggal user lunas bayar
            $table->string('status_pembayaran')->default('pending'); // pending, settlement, expire, cancel
            
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
