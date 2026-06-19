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
        Schema::table('katalog_barang', function (Blueprint $table) {
            // Menambahkan kolom harga_asli tepat di bawah harga_sewa_per_hari
            $table->bigInteger('harga_asli')->after('harga_sewa_per_hari')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('katalog_barang', function (Blueprint $table) {
            $table->dropColumn('harga_asli');
        });
    }
};
