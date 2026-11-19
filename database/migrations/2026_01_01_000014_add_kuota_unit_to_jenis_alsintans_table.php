<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Tambah kolom kuota_unit ke jenis_alsintans
 * Menyimpan jumlah jatah / kuota alokasi unit bantuan yang disediakan Dinas
 * untuk setiap kategori alsintan dalam tahun anggaran berjalan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_alsintans', function (Blueprint $table) {
            $table->unsignedInteger('kuota_unit')
                  ->default(10)
                  ->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_alsintans', function (Blueprint $table) {
            $table->dropColumn('kuota_unit');
        });
    }
};

