<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PengajuanBantuan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Command: spk:prune-files
 * 
 * Auto-Expired & File Pruning Engine:
 * Menghapus file fisik PDF usulan bantuan Alsintan yang sudah melewati tanggal
 * kedaluwarsa (expired_at < now) untuk menghemat ruang penyimpanan server.
 * 
 * ATURAN RETENSI (Single Source of Truth):
 * 1. Usulan Disetujui (ACC): Masa retensi berkas = 13 BULAN (1 tahun anggaran + 1 bulan rekapitulasi audit).
 * 2. Usulan Ditolak: Masa retensi berkas = 90 HARI sejak tanggal diputuskan.
 * 3. HANYA file fisik PDF yang dihapus dari disk penyimpanan; record data tekstual
 *    dan hasil perhitungan TOPSIS pada database TETAP TERJAGA UTUH untuk rekap historis.
 */
class PruneExpiredFilesCommand extends Command
{
    protected $signature = 'spk:prune-files 
                            {--dry-run : Menjalankan simulasi pengecekan tanpa benar-benar menghapus file fisik}
                            {--force : Memaksa pruning tanpa prompt konfirmasi}';

    protected $description = 'Menghapus berkas fisik PDF pengajuan yang sudah kedaluwarsa (expired_at < now) untuk efisiensi penyimpanan server dinas';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info("==================================================================");
        $this->info("  ENGINE AUTO-PRUNING BERKAS SPK ALSINTAN — DINAS PERTANIAN SUBANG");
        $this->info("==================================================================");
        if ($isDryRun) {
            $this->warn("MODE SIMULASI (DRY-RUN): File fisik tidak akan dihapus.");
        }

        // Cari seluruh pengajuan yang kedaluwarsa dan berkas fisiknya belum dipruning
        $kandidatPruning = PengajuanBantuan::with('berkasPengajuans')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->whereNull('files_pruned_at')
            ->get();

        $totalKandidat = $kandidatPruning->count();

        if ($totalKandidat === 0) {
            $this->info("Tidak ada berkas pengajuan yang kedaluwarsa saat ini. Penyimpanan server optimal.");
            return Command::SUCCESS;
        }

        $this->line("Ditemukan {$totalKandidat} usulan yang masa retensi berkasnya telah habis:");

        $totalFileDihapus = 0;
        $totalBytesDibebaskan = 0;

        foreach ($kandidatPruning as $pengajuan) {
            $statusLabel = match($pengajuan->status) {
                PengajuanBantuan::STATUS_DISETUJUI_KABID => 'Disetujui (Retensi 13 Bln Habis)',
                PengajuanBantuan::STATUS_DITOLAK_KABID   => 'Ditolak Kabid (Retensi 90 Hr Habis)',
                PengajuanBantuan::STATUS_DITOLAK_ADMIN   => 'Ditolak Admin (Retensi 90 Hr Habis)',
                default                                  => 'Kedaluwarsa Lainnya',
            };

            $this->line("- [{$pengajuan->nomor_pengajuan}] {$pengajuan->user?->kelompokTani?->nama_poktan} | {$statusLabel} | Expired: {$pengajuan->expired_at->format('d/m/Y')}");

            $berkasList = $pengajuan->berkasPengajuans;

            foreach ($berkasList as $berkas) {
                $filePath = $berkas->file_path;

                if (Storage::disk('private')->exists($filePath)) {
                    $fileSize = Storage::disk('private')->size($filePath);
                    $totalBytesDibebaskan += $fileSize;
                    $totalFileDihapus++;

                    if (!$isDryRun) {
                        Storage::disk('private')->delete($filePath);

                        Log::channel('single')->info("Auto-Pruning: Berkas PDF {$filePath} berhasil dihapus dari server.", [
                            'pengajuan_id' => $pengajuan->id,
                            'nomor'        => $pengajuan->nomor_pengajuan,
                            'status'       => $pengajuan->status,
                            'expired_at'   => $pengajuan->expired_at->toDateTimeString(),
                            'size_bytes'   => $fileSize,
                        ]);
                    }
                }
            }

            if (!$isDryRun) {
                // Tandai files_pruned_at agar tidak diproses ulang di masa mendatang
                $pengajuan->updateQuietly([
                    'files_pruned_at' => now(),
                ]);

                // Bersihkan direktori usulan jika sudah kosong
                $dirPath = "pengajuan/{$pengajuan->id}";
                if (Storage::disk('private')->exists($dirPath)) {
                    $remaining = Storage::disk('private')->files($dirPath);
                    if (empty($remaining)) {
                        Storage::disk('private')->deleteDirectory($dirPath);
                    }
                }
            }
        }

        $mbDibebaskan = round($totalBytesDibebaskan / (1024 * 1024), 2);

        $this->info("------------------------------------------------------------------");
        if ($isDryRun) {
            $this->warn("Simulasi selesai. Total file yang siap dipruning: {$totalFileDihapus} file (~{$mbDibebaskan} MB).");
        } else {
            $this->info("Berhasil! {$totalFileDihapus} file PDF kedaluwarsa telah dibersihkan (~{$mbDibebaskan} MB dibebaskan).");
            $this->info("Record database dan metadata rekapitulasi tetap tersimpan utuh.");
        }

        return Command::SUCCESS;
    }
}

