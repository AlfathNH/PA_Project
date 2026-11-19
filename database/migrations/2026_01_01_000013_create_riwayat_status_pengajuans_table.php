<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: riwayat_status_pengajuans (Tabel ke-9)
 * Audit Trail / Log Tracking kronologis tahapan pengajuan bantuan alsintan.
 * Mencatat setiap transisi status (diajukan -> diverifikasi -> dinilai -> dirangking -> disetujui/ditolak/expired)
 * beserta user pelaksana, catatan, dan timestamp audit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status_pengajuans', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengajuan bantuan induk
            $table->foreignId('pengajuan_bantuan_id')
                  ->constrained('pengajuan_bantuans')
                  ->onDelete('cascade');

            // Relasi ke user yang memicu perubahan status (bisa Admin, Kepbid, atau Poktan)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_perubahan')->useCurrent();
            $table->timestamps();

            $table->index(['pengajuan_bantuan_id', 'waktu_perubahan'], 'idx_riwayat_pengajuan_waktu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_pengajuans');
    }
};
