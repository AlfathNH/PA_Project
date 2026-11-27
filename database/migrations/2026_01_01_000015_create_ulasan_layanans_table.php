<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ke-10: Ulasan Layanan & Indeks Kepuasan Masyarakat (IKM)
     * Standar KKNI Level 5 (Rentang 8-12 Tabel Relasional)
     */
    public function up(): void
    {
        Schema::create('ulasan_layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained('kelompok_tanis')->cascadeOnDelete();
            $table->foreignId('pengajuan_bantuan_id')->nullable()->constrained('pengajuan_bantuans')->nullOnDelete();
            $table->unsignedTinyInteger('rating_bintang'); // Skala Likert 1 s.d. 5 bintang
            $table->text('komentar')->nullable();
            $table->boolean('is_tampil_landing')->default(false); // Moderasi oleh Kepala Bidang (Anti-Spam / Filter Selektif)
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->timestamps();

            $table->index(['is_tampil_landing', 'rating_bintang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan_layanans');
    }
};
