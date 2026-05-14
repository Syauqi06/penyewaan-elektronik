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
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Admin yang memproses
            $table->dateTime('tgl_kembali_aktual');
            $table->string('kondisi_barang_kembali');
            $table->string('foto_kondisi_kembali')->nullable();
            $table->text('catatan_kerusakan')->nullable();
            $table->enum('status_pengembalian', ['aman', 'rusak', 'hilang'])->default('aman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
