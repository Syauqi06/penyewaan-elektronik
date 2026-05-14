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
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')->constrained('pengembalians')->cascadeOnDelete();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->integer('jumlah_hari_telat')->default(0);
            $table->integer('tarif_denda_perhari')->default(0);
            $table->integer('total_denda');
            $table->enum('status_denda', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->dateTime('tgl_denda');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
