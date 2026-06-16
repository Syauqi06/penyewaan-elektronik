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
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->date('tanggal_kembali_aktual');
            $table->text('kondisi_barang_kembali');
            $table->string('foto_kondisi_kembali')->nullable();
            $table->integer('jumlah_hari_telat')->default(0);
            $table->bigInteger('tarif_denda_per_hari')->default(0);
            $table->bigInteger('total_denda')->default(0);
            $table->enum('status_denda', ['tidak_ada', 'belum_bayar', 'lunas'])->default('tidak_ada');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
