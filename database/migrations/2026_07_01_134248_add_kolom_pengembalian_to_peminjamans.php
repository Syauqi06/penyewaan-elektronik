<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('foto_pengembalian')->nullable()->after('status_peminjaman');
        });

        DB::statement("ALTER TABLE peminjaman MODIFY status_peminjaman VARCHAR(255) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('foto_pengembalian');
        });
    }
};