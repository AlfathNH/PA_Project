<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: pengajuan_bantuans
 * Tabel INTI: menyimpan setiap pengajuan bantuan Alsintan dari Kelompok Tani.
 *
 * FITUR KRITIS:
 * 1. Kolom 'expired_at'      : Tanggal kedaluwarsa pengajuan (90 hari dari dibuat)
 * 2. Kolom 'files_pruned_at' : Dicatat saat berkas PDF dihapus oleh pruner
 * 3. Status state machine    : draft -> diajukan -> diverifikasi -> ... -> disetujui
 *
 * Digunakan oleh MassPrunable trait pada Model PengajuanBantuan untuk
 * menghapus file PDF yang sudah kedaluwarsa atau ditolak secara otomatis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_bantuans', function (Blueprint $table) {
            $table->id();

            // Relasi ke Kelompok Tani yang mengajukan
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Relasi ke jenis Alsintan yang diminta
            $table->foreignId('jenis_alsintan_id')
                  ->constrained('jenis_alsintans')
                  ->onDelete('restrict'); // Jangan hapus jenis jika ada pengajuan

            $table->string('nomor_pengajuan')->unique(); // Nomor urut (ex: PA-2026-001)
            $table->year('tahun_pengajuan');             // Tahun anggaran yang diinginkan
            $table->integer('jumlah_diminta');           // Jumlah unit Alsintan yang diminta
            $table->text('alasan_pengajuan');            // Narasi kebutuhan dari Poktan
            $table->text('catatan_admin')->nullable();   // Catatan dari Admin saat verifikasi
            $table->text('catatan_kepbid')->nullable();  // Catatan dari Kepala Bidang saat ACC

            /**
             * STATUS STATE MACHINE (Alur Pengajuan):
             * draft           : Baru dibuat, belum disubmit
             * diajukan        : Sudah disubmit oleh Poktan, menunggu verifikasi Admin
             * diverifikasi    : Lolos verifikasi Admin, siap dinilai TOPSIS
             * dinilai         : Sudah diberikan nilai oleh Admin, siap dihitung TOPSIS
             * dirangking      : Sudah masuk kalkulasi TOPSIS, ada nilai preferensi Vi
             * disetujui_kabid : Kepala Bidang sudah ACC
             * ditolak_admin   : Ditolak saat verifikasi (berkas tidak lengkap, dll.)
             * ditolak_kabid   : Ditolak oleh Kepala Bidang setelah ranking TOPSIS
             */
            $table->enum('status', [
                'draft',
                'diajukan',
                'diverifikasi',
                'dinilai',
                'dirangking',
                'disetujui_kabid',
                'ditolak_admin',
                'ditolak_kabid',
            ])->default('draft');

            /**
             * KOLOM KRITIS UNTUK AUTO-PRUNING:
             *
             * expired_at      : Batas waktu pengajuan berlaku (default: +90 hari dari created_at)
             *                   Setelah lewat tanggal ini, file PDF akan dihapus otomatis.
             *
             * files_pruned_at : Timestamp saat file PDF berhasil dihapus oleh pruner.
             *                   NULL berarti file masih ada di storage.
             */
            $table->timestamp('expired_at')->nullable();         // Tanggal kedaluwarsa
            $table->timestamp('files_pruned_at')->nullable();    // Tanggal file sudah dipruning

            // Relasi ke user yang memverifikasi dan yang menyetujui
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Index untuk mempercepat query pruning (cek expired_at setiap hari)
            $table->index(['status', 'expired_at']);
            $table->index(['status', 'files_pruned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_bantuans');
    }
};

