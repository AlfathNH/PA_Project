<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah foreign key kecamatan_id ke kelompok_tanis
 * Menghubungkan Kelompok Tani dengan master Kecamatan secara aman dan backward-compatible.
 * Kolom teks 'kecamatan' lama tetap dipertahankan agar View tidak rusak.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelompok_tanis', function (Blueprint $table) {
            $table->foreignId('kecamatan_id')
                  ->nullable()
                  ->after('desa')
                  ->constrained('kecamatans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kelompok_tanis', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
        });
    }
};

