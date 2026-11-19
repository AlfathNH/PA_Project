<?php

namespace App\Http\Controllers\KepBid;

use App\Http\Controllers\Controller;
use App\Models\JenisAlsintan;
use App\Models\PengajuanBantuan;
use App\Models\HasilTopsis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Controller: KepBid DashboardController
 * Portal persetujuan akhir (ACC) hasil rekomendasi SPK TOPSIS oleh Kepala Bidang.
 * 
 * ALUR BISNIS KEPUTUSAN & PRUNING:
 * 1. Antrean Aktif: Hanya menampilkan usulan yang berstatus 'dirangking' (belum diputuskan).
 * 2. Begitu di-ACC atau Ditolak, usulan langsung berpindah ke Riwayat/Histori.
 * 3. Aturan Retensi Berkas:
 *    - Disetujui (ACC): expired_at = approved_at + 13 BULAN (1 tahun anggaran + 1 bulan rekapitulasi audit).
 *    - Ditolak: expired_at = approved_at + 90 HARI.
 * 4. Berkas PDF fisik dibersihkan oleh engine pruning saat expired_at lewat,
 *    namun rekap data teks dan ranking TOPSIS tetap tersimpan abadi di database.
 */
class DashboardController extends Controller
{
    /**
     * Tampilan utama: Antrean Keputusan Rekomendasi TOPSIS
     */
    public function index(Request $request)
    {
        $jenisAlsintans = JenisAlsintan::aktif()->with('pengajuanBantuans')->get();
        $selectedId     = $request->query('jenis_id', $jenisAlsintans->first()?->id);
        $tab            = $request->query('tab', 'antrean'); // 'antrean' atau 'riwayat'

        $selectedAlsintan = $jenisAlsintans->firstWhere('id', $selectedId) ?? $jenisAlsintans->first();

        // Hitung statistik ringkas secara langsung via agregasi database (anti-OOM)
        $baseQuery = PengajuanBantuan::where('jenis_alsintan_id', $selectedId)
            ->whereHas('hasilTopsis');

        $stats = [
            'total_rekomendasi' => (clone $baseQuery)->count(),
            'menunggu_acc'      => (clone $baseQuery)->where('status', PengajuanBantuan::STATUS_DIRANGKING)->count(),
            'disetujui'         => (clone $baseQuery)->where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)->count(),
            'ditolak'           => (clone $baseQuery)->where('status', PengajuanBantuan::STATUS_DITOLAK_KABID)->count(),
        ];

        // Antrean Aktif (Menunggu ACC): Eager loading lengkap + join hasil_topsis untuk sorting database + pagination
        $antrean = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis', 'berkasPengajuans'])
            ->where('jenis_alsintan_id', $selectedId)
            ->where('status', PengajuanBantuan::STATUS_DIRANGKING)
            ->join('hasil_topsis', 'hasil_topsis.pengajuan_bantuan_id', '=', 'pengajuan_bantuans.id')
            ->select('pengajuan_bantuans.*')
            ->orderBy('hasil_topsis.ranking', 'asc')
            ->paginate(50, ['*'], 'antrean_page')
            ->withQueryString();

        // Riwayat Keputusan: Eager loading lengkap + join hasil_topsis + pagination
        $riwayat = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis', 'berkasPengajuans'])
            ->where('jenis_alsintan_id', $selectedId)
            ->whereIn('status', [
                PengajuanBantuan::STATUS_DISETUJUI_KABID,
                PengajuanBantuan::STATUS_DITOLAK_KABID,
            ])
            ->join('hasil_topsis', 'hasil_topsis.pengajuan_bantuan_id', '=', 'pengajuan_bantuans.id')
            ->select('pengajuan_bantuans.*')
            ->orderBy('hasil_topsis.ranking', 'asc')
            ->paginate(50, ['*'], 'riwayat_page')
            ->withQueryString();

        return view('kepbid.dashboard', compact(
            'jenisAlsintans', 
            'selectedId', 
            'selectedAlsintan',
            'antrean', 
            'riwayat', 
            'stats', 
            'tab'
        ));
    }

    /**
     * Memberikan persetujuan akhir (ACC) atau penolakan.
     * Usulan akan langsung keluar dari antrean aktif dan masuk ke riwayat/rekapitulasi.
     */
    public function putuskan(Request $request, PengajuanBantuan $pengajuan)
    {
        $request->validate([
            'keputusan'      => 'required|in:setuju,tolak',
            'catatan_kepbid' => 'nullable|string|max:500',
        ]);

        $isSetuju = ($request->keputusan === 'setuju');

        return DB::transaction(function () use ($request, $pengajuan, $isSetuju) {
            $pengajuanLocked = PengajuanBantuan::lockForUpdate()->findOrFail($pengajuan->id);

            if ($isSetuju) {
                $alsintan = JenisAlsintan::lockForUpdate()->find($pengajuanLocked->jenis_alsintan_id);
                // Hitung sisa kuota aktual saat pengajuan ini belum berstatus disetujui
                $sudahDisetujuiSebelumnya = ($pengajuanLocked->status === PengajuanBantuan::STATUS_DISETUJUI_KABID);
                if ($alsintan && !$sudahDisetujuiSebelumnya && $alsintan->sisa_kuota < $pengajuanLocked->jumlah_diminta) {
                    return back()->with('error', "Gagal menyetujui usulan {$pengajuanLocked->nomor_pengajuan}. Sisa kuota {$alsintan->nama_alsintan} tidak mencukupi (Sisa: {$alsintan->sisa_kuota} unit, Permintaan: {$pengajuanLocked->jumlah_diminta} unit). Silakan minta Admin Dinas memperbarui kuota alokasi terlebih dahulu.");
                }
            }

            $statusSebelumnya = $pengajuanLocked->status;
            $status           = $isSetuju
                ? PengajuanBantuan::STATUS_DISETUJUI_KABID
                : PengajuanBantuan::STATUS_DITOLAK_KABID;

            // ATURAN RETENSI MASA BERLAKU BERKAS (Auto-Pruning Policy):
            // 1. Disetujui (ACC): Retensi 13 Bulan (1 tahun anggaran + 1 bulan rekapitulasi audit LPJ tahunan dinas)
            // 2. Ditolak: Retensi 90 Hari (3 bulan)
            $expiredAt = $isSetuju ? now()->addMonths(13) : now()->addDays(90);

            $pengajuanLocked->update([
                'status'         => $status,
                'catatan_kepbid' => $request->catatan_kepbid,
                'approved_by'    => Auth::id(),
                'approved_at'    => now(),
                'expired_at'     => $expiredAt,
            ]);

            $namaPoktan = $pengajuanLocked->user?->kelompokTani?->nama_poktan ?? 'Kelompok Tani';
            $pesan = $isSetuju
                ? "Usulan {$pengajuanLocked->nomor_pengajuan} ({$namaPoktan}) berhasil disetujui. Berkas masuk arsip riwayat dengan masa retensi 13 bulan audit."
                : "Usulan {$pengajuanLocked->nomor_pengajuan} ({$namaPoktan}) ditolak. Berkas masuk antrean pembersihan otomatis (masa retensi 90 hari).";

            return back()->with('success', $pesan);
        });
    }

    /**
     * Halaman Riwayat & Rekapitulasi Keputusan Bantuan Alsintan
     * Dilengkapi filter per tahun anggaran, bulan, jenis alsintan, dan status keputusan.
     */
    public function riwayat(Request $request)
    {
        $jenisAlsintans = JenisAlsintan::aktif()->get();

        // Parameter Filter
        $filterTahun  = $request->query('tahun', date('Y'));
        $filterBulan  = $request->query('bulan'); // null = semua bulan
        $filterJenis  = $request->query('jenis_id');
        $filterStatus = $request->query('status'); // 'semua', 'disetujui_kabid', 'ditolak_kabid'

        $query = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis', 'approver'])
            ->whereIn('status', [
                PengajuanBantuan::STATUS_DISETUJUI_KABID,
                PengajuanBantuan::STATUS_DITOLAK_KABID,
            ]);

        // Filter Tahun (Berdasarkan tahun anggaran pengajuan atau tahun disetujui)
        if ($filterTahun) {
            $query->where(function ($q) use ($filterTahun) {
                $q->where('tahun_pengajuan', $filterTahun)
                  ->orWhereYear('approved_at', $filterTahun);
            });
        }

        // Filter Bulan Disetujui
        if ($filterBulan) {
            $query->whereMonth('approved_at', $filterBulan);
        }

        // Filter Jenis Alsintan
        if ($filterJenis) {
            $query->where('jenis_alsintan_id', $filterJenis);
        }

        // Filter Status Keputusan
        if ($filterStatus && $filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        }

        // Statistik Rekapitulasi (Dihitung langsung via agregasi database tanpa memuat koleksi model ke memori)
        $baseStatsQuery = clone $query;
        $rekapStats = [
            'total_diputuskan' => (clone $baseStatsQuery)->count(),
            'total_disetujui'  => (clone $baseStatsQuery)->where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)->count(),
            'total_unit_acc'   => (int) ((clone $baseStatsQuery)->where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)->sum('jumlah_diminta') ?? 0),
            'total_ditolak'    => (clone $baseStatsQuery)->where('status', PengajuanBantuan::STATUS_DITOLAK_KABID)->count(),
            'berkas_aktif'     => (clone $baseStatsQuery)->whereNull('files_pruned_at')->count(),
            'berkas_dipruning' => (clone $baseStatsQuery)->whereNotNull('files_pruned_at')->count(),
        ];

        // Pagination maksimal 10 data per halaman
        $riwayatList = $query->orderByDesc('approved_at')->paginate(10)->withQueryString();

        return view('kepbid.riwayat', compact(
            'riwayatList', 
            'jenisAlsintans', 
            'rekapStats', 
            'filterTahun', 
            'filterBulan', 
            'filterJenis', 
            'filterStatus'
        ));
    }

    /**
     * Lembar Cetak Rekapitulasi Resmi (Berita Acara Penetapan Penerima Alsintan)
     * Format siap cetak A4 dengan Kop Surat Dinas Pertanian Kabupaten Subang.
     */
    public function cetakRekap(Request $request)
    {
        $filterTahun  = $request->query('tahun', date('Y'));
        $filterBulan  = $request->query('bulan');
        $filterJenis  = $request->query('jenis_id');
        $filterStatus = $request->query('status', PengajuanBantuan::STATUS_DISETUJUI_KABID); // Default: Hanya yang di-ACC

        $query = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis', 'approver'])
            ->whereNotNull('approved_at');

        if ($filterStatus && $filterStatus !== 'semua') {
            $query->where('status', $filterStatus);
        } else {
            $query->whereIn('status', [
                PengajuanBantuan::STATUS_DISETUJUI_KABID,
                PengajuanBantuan::STATUS_DITOLAK_KABID,
            ]);
        }

        if ($filterTahun) {
            $query->where(function ($q) use ($filterTahun) {
                $q->where('tahun_pengajuan', $filterTahun)
                  ->orWhereYear('approved_at', $filterTahun);
            });
        }

        if ($filterBulan) {
            $query->whereMonth('approved_at', $filterBulan);
        }

        if ($filterJenis) {
            $query->where('jenis_alsintan_id', $filterJenis);
        }

        $daftarPenerima = $query->leftJoin('hasil_topsis', 'hasil_topsis.pengajuan_bantuan_id', '=', 'pengajuan_bantuans.id')
            ->select('pengajuan_bantuans.*')
            ->orderBy('pengajuan_bantuans.jenis_alsintan_id')
            ->orderByRaw('COALESCE(hasil_topsis.ranking, 999999) ASC')
            ->get();

        $selectedJenis = $filterJenis ? JenisAlsintan::find($filterJenis) : null;

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('kepbid.rekap_cetak', compact(
            'daftarPenerima',
            'filterTahun',
            'filterBulan',
            'filterStatus',
            'selectedJenis',
            'namaBulan'
        ));
    }

    /**
     * Trigger Simulasi / Eksekusi Pruning Berkas Kedaluwarsa secara Manual
     * Memudahkan pengujian dan demonstrasi saat Sidang TA.
     */
    public function jalankanPruning(Request $request)
    {
        // Hitung usulan yang kedaluwarsa sebelum eksekusi
        $kandidatCount = PengajuanBantuan::whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->whereNull('files_pruned_at')
            ->count();

        Artisan::call('spk:prune-files', ['--force' => true]);

        if ($kandidatCount === 0) {
            return back()->with('success', 'Pengecekan Retensi Berkas Selesai: Tidak ada berkas yang kedaluwarsa saat ini. Seluruh berkas proposal usulan masih berada dalam masa retensi yang sah (aktif), dan kapasitas penyimpanan server dalam kondisi optimal.');
        }

        return back()->with('success', "Proses Auto-Pruning Berhasil: {$kandidatCount} berkas usulan yang masa retensinya telah habis berhasil dibersihkan dari penyimpanan fisik. Data rekapitulasi teks dan hasil pemeringkatan TOPSIS tetap tersimpan aman di database untuk keperluan audit BPKP/ANRI.");
    }
}
