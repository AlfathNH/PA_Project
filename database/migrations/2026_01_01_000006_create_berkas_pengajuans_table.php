<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: berkas_pengajuans
 * Menyimpan metadata file PDF yang diunggah bersama pengajuan bantuan.
 * Satu pengajuan dapat memiliki beberapa berkas (surat permohonan, profil poktan, dll.)
 *
 * File fisik disimpan di: storage/app/private/pengajuan/{pengajuan_id}/
 * File TIDAK disimpan langsung di DB, hanya path-nya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_pengajuans', function (Blueprint $table) {
            $table->id();

            // Relasi ke pengajuan induk
            $table->foreignId('pengajuan_bantuan_id')
                  ->constrained('pengajuan_bantuans')
                  ->onDelete('cascade'); // Hapus berkas jika pengajuan dihapus

            $table->string('nama_berkas');         // Nama tampilan (ex: "Surat Permohonan")
            $table->string('jenis_berkas');        // Jenis (ex: "surat_permohonan", "profil_poktan")
            $table->string('file_path');           // Path relatif di storage (ex: pengajuan/5/surat.pdf)
            $table->string('file_name_original'); // Nama file asli saat diupload
            $table->string('file_mime_type')->default('application/pdf'); // MIME type
            $table->unsignedBigInteger('file_size'); // Ukuran file dalam bytes (maks. 5MB = 5242880)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berkas_pengajuans');
    }
};

