<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * JADWAL AUTO-EXPIRED & FILE PRUNING
 * 
 * Dijalankan setiap hari pukul 01:00 WIB (dini hari saat beban server rendah).
 * Menghapus berkas lampiran PDF yang masa retensinya sudah habis:
 * - Usulan Disetujui (ACC): Retensi 13 Bulan (1 tahun anggaran + 1 bulan rekapitulasi audit LPJ)
 * - Usulan Ditolak: Retensi 90 Hari
 * Metadata dan rekap historis pada database tetap aman dan tidak terhapus.
 */
Schedule::command('spk:prune-files')
    ->dailyAt('01:00')
    ->appendOutputTo(storage_path('logs/pruning.log'));
