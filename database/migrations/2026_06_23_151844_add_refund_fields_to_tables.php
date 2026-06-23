<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah data rekening di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('atas_nama_rekening')->nullable();
        });

        // 2. Tambah data refund di tabel pengembalian
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->bigInteger('nominal_refund')->default(0);
            $table->string('bukti_refund')->nullable();
            $table->enum('status_refund', ['tidak_ada', 'menunggu', 'selesai'])->default('tidak_ada');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'nomor_rekening', 'atas_nama_rekening']);
        });
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn(['nominal_refund', 'bukti_refund', 'status_refund']);
        });
    }
};