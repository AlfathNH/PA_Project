<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: kecamatans (Tabel ke-7)
 * Master data wilayah Kecamatan di Kabupaten Subang.
 * Mengklasifikasikan wilayah ke dalam 3 zona pertanian strategis:
 * 1. Zona Pantura (Sentra Padi Nasional & Pompanisasi)
 * 2. Zona Tengah (Lumbung Padi & Palawija)
 * 3. Zona Selatan (Dataran Tinggi & Hortikultura)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan')->unique();
            $table->string('kode_kecamatan', 20)->unique();
            $table->enum('zona_wilayah', ['Pantura', 'Tengah', 'Selatan'])->default('Tengah');
            $table->text('deskripsi_wilayah')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kecamatans');
    }
};

