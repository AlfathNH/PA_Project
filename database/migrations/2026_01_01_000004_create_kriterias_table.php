<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: kriterias
 * Menyimpan daftar kriteria TOPSIS yang bersifat DINAMIS.
 * Admin dapat menambah, mengubah, atau menonaktifkan kriteria sesuai kebutuhan.
 *
 * Kolom krusial untuk TOPSIS:
 * - bobot : Tingkat kepentingan kriteria (0 < bobot < 1, total semua = 1.0)
 * - tipe  : 'benefit' (nilai besar = baik) atau 'cost' (nilai kecil = baik)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kriteria');      // Nama kriteria (ex: "Luas Lahan")
            $table->string('kode_kriteria')->unique(); // Kode unik (ex: "C1", "C2")
            $table->text('deskripsi')->nullable(); // Penjelasan kriteria untuk tampilan

            /**
             * Bobot kriteria untuk TOPSIS (Langkah 3: Normalisasi Terbobot)
             * Tipe data: decimal(5,4) untuk presisi 4 angka di belakang koma
             * Contoh: 0.3000 = 30%, 0.2500 = 25%
             * Validasi: jumlah semua bobot aktif harus = 1.0000
             */
            $table->decimal('bobot', 5, 4)->default(0.0000);

            /**
             * Tipe kriteria untuk menentukan Solusi Ideal (Langkah 4 TOPSIS):
             * - 'benefit' : A+ = max(kolom), A- = min(kolom)
             * - 'cost'    : A+ = min(kolom), A- = max(kolom)
             */
            $table->enum('tipe', ['benefit', 'cost'])->default('benefit');

            $table->integer('urutan')->default(0); // Urutan tampil di form penilaian
            $table->boolean('is_active')->default(true); // Aktif/nonaktif

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};

