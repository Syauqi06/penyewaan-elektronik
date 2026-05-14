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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->cascadeOnDelete();
            $table->integer('jumlah_deposit');
            $table->enum('status_deposit', ['ditahan', 'dikembalikan_penuh', 'dipotong'])->default('ditahan');
            $table->integer('jumlah_dikembalikan')->default(0);
            $table->integer('jumlah_potongan')->default(0);
            $table->dateTime('tgl_proses')->nullable();
            $table->text('alasan_potongan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
