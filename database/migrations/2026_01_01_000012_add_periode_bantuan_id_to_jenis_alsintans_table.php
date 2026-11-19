<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah foreign key periode_bantuan_id ke jenis_alsintans
 * Menghubungkan setiap kategori alat pertanian dengan periode aktif anggaran.
 * Bersifat nullable agar tidak merusak alur pengajuan alsintan yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_alsintans', function (Blueprint $table) {
            $table->foreignId('periode_bantuan_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('periode_bantuans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jenis_alsintans', function (Blueprint $table) {
            $table->dropForeign(['periode_bantuan_id']);
            $table->dropColumn('periode_bantuan_id');
        });
    }
};

