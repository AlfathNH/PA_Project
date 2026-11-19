<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: kelompok_tanis
 * Menyimpan profil lengkap Kelompok Tani yang mendaftar di sistem.
 * Relasi: satu User (role=kelompok_tani) memiliki satu KelompokTani.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_tanis', function (Blueprint $table) {
            $table->id();

            // Foreign key ke tabel users (aktor Kelompok Tani)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nama_poktan');           // Nama resmi Kelompok Tani
            $table->string('nomor_badan_hukum')->nullable(); // No. SK pendirian
            $table->string('nama_ketua');            // Nama ketua Poktan
            $table->string('no_hp_ketua', 20);       // No HP ketua (untuk koordinasi)
            $table->string('desa');                  // Desa/Kelurahan
            $table->string('kecamatan');             // Kecamatan
            $table->text('alamat_lengkap');          // Alamat detail sekretariat Poktan

            // Data lahan untuk keperluan kriteria TOPSIS
            $table->decimal('luas_lahan_total', 10, 2)->default(0); // Satuan: Hektar
            $table->integer('jumlah_anggota')->default(0);           // Jumlah anggota aktif

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_tanis');
    }
};

