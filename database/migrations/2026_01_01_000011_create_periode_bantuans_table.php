<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: periode_bantuans (Tabel ke-8)
 * Master data gelombang / periode tahun anggaran bantuan alsintan.
 * Mengelompokkan jenis alsintan berdasarkan masa berlaku alokasi anggaran APBD/APBN.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_bantuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->unsignedInteger('kuota_total_unit')->default(100);
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tahun_anggaran', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_bantuans');
    }
};

