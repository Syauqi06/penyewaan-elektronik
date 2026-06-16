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
        Schema::create('unit_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('katalog_barang_id')->constrained('katalog_barang')->onDelete('cascade');
            $table->string('serial_number')->unique();
            $table->string('kondisi_fisik');
            $table->enum('status_ketersediaan', ['tersedia', 'disewa', 'perawatan'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_barang');
    }
};
