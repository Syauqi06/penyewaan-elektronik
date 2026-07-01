<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KOSONGIN AJA BRO! 
        // Biar Laravel nggak nyoba bikin kolom 'nama_bank' dua kali.
    }

    public function down(): void
    {
        // Ini juga biarin kosong
    }
};