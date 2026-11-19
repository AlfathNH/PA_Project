<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: penilaian_topsis
 * Menyimpan nilai (skor) yang diberikan Admin untuk setiap Pengajuan
 * pada masing-masing Kriteria TOPSIS.
 *
 * Ini adalah tabel PIVOT antara pengajuan_bantuans dan kriterias,
 * dengan tambahan kolom nilai numerik.
 *
 * Juga menyimpan HASIL KALKULASI TOPSIS (output 6 langkah) sebagai
 * kolom agregat di sini agar tidak perlu kalkulasi ulang saat render halaman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_topsis', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengajuan yang dinilai
            $table->foreignId('pengajuan_bantuan_id')
                  ->constrained('pengajuan_bantuans')
                  ->onDelete('cascade');

            // Relasi ke kriteria TOPSIS yang digunakan
            $table->foreignId('kriteria_id')
                  ->constrained('kriterias')
                  ->onDelete('restrict');

            /**
             * Nilai mentah (raw score) yang diinput Admin untuk pengajuan ini
             * pada kriteria ini. Skala bebas (misalnya 1-10 atau nilai aktual).
             * Kolom ini digunakan sebagai x_ij dalam Matriks Keputusan (Langkah 1).
             */
            $table->decimal('nilai', 10, 4)->default(0); // x_ij: nilai asli

            $table->timestamps();

            // Pastikan satu pengajuan hanya punya satu nilai per kriteria
            $table->unique(['pengajuan_bantuan_id', 'kriteria_id'], 'unique_penilaian_per_kriteria');
        });

        /**
         * Tabel terpisah untuk menyimpan HASIL TOPSIS PER PENGAJUAN
         * (output agregat dari seluruh 6 langkah kalkulasi).
         * Dipisah agar tabel penilaian tidak bloat dengan kolom hasil.
         */
        Schema::create('hasil_topsis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_bantuan_id')
                  ->constrained('pengajuan_bantuans')
                  ->onDelete('cascade');

            // Referensi batch kalkulasi (ID unik per sesi perhitungan TOPSIS)
            $table->string('batch_kalkulasi_id')->nullable();

            /**
             * Hasil 6 Langkah TOPSIS (disimpan untuk transparansi & audit):
             *
             * Langkah 5: Jarak Euclidean
             * - jarak_ideal_positif (D+) : Jarak dari solusi ideal positif A+
             * - jarak_ideal_negatif (D-) : Jarak dari solusi ideal negatif A-
             *
             * Langkah 6: Nilai Preferensi
             * - nilai_preferensi (Vi)    : Vi = D- / (D+ + D-)  [0.0000 - 1.0000]
             * - ranking                  : Urutan dari Vi tertinggi (1 = terbaik)
             */
            $table->decimal('jarak_ideal_positif', 15, 10)->default(0); // D+
            $table->decimal('jarak_ideal_negatif', 15, 10)->default(0); // D-
            $table->decimal('nilai_preferensi', 15, 10)->default(0);    // Vi
            $table->unsignedInteger('ranking')->nullable();              // Urutan ranking

            $table->timestamp('dihitung_pada')->nullable(); // Waktu kalkulasi terakhir
            $table->foreignId('dihitung_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // User admin yang memicu kalkulasi

            $table->timestamps();

            // Index untuk performa sorting ranking
            $table->index(['batch_kalkulasi_id', 'nilai_preferensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_topsis');
        Schema::dropIfExists('penilaian_topsis');
    }
};

