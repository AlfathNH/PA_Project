<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: jenis_alsintans
 * Master data kategori Alat dan Mesin Pertanian (Alsintan) yang tersedia
 * untuk diajukan oleh Kelompok Tani.
 *
 * Contoh data: Traktor Roda 2, Traktor Roda 4, Sprayer/PUTS,
 *              Pompa Air, Rice Transplanter, Combine Harvester
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_alsintans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alsintan');          // Nama jenis alat
            $table->string('kode_alsintan')->unique(); // Kode unik (ex: "TR2", "POMPA")
            $table->text('deskripsi')->nullable();     // Deskripsi singkat alat
            $table->string('satuan')->default('unit'); // Satuan alat (unit, set, dll.)
            $table->boolean('is_active')->default(true); // Apakah masih bisa diajukan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_alsintans');
    }
};

