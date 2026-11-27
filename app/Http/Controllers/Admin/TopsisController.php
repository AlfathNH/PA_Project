<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisAlsintan;
use App\Models\Kriteria;
use App\Models\PengajuanBantuan;
use App\Services\TopsisCalculationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: Admin TopsisController
 * Menangani simulasi dan eksekusi 6 langkah perhitungan SPK TOPSIS.
 */
class TopsisController extends Controller
{
    protected TopsisCalculationService $topsisService;

    public function __construct(TopsisCalculationService $topsisService)
    {
        $this->topsisService = $topsisService;
    }

    /**
     * Halaman Utama Modul TOPSIS:
     * Menampilkan pilihan kategori alat dan hasil kalkulasi 6 langkah.
     */
    public function index(Request $request)
    {
        $jenisAlsintans = JenisAlsintan::aktif()->get();
        $selectedId     = $request->query('jenis_id', $jenisAlsintans->first()?->id);

        $kriterias  = Kriteria::aktif()->get();
        $totalBobot = $kriterias->sum('bobot');

        // Cek jumlah kandidat yang siap dihitung via query agregasi cepat (mencegah double-fetch 1000 record)
        $kandidatQuery = PengajuanBantuan::where('jenis_alsintan_id', $selectedId)
            ->whereHas('penilaianTopsis')
            ->whereNotIn('status', [
                PengajuanBantuan::STATUS_DRAFT,
                PengajuanBantuan::STATUS_DITOLAK_ADMIN,
            ]);

        $totalKandidat = $kandidatQuery->count();
        $kandidats     = collect();
        $kandidatsById = collect();
        $hasilKalkulasi = null;
        $paginatedRanking = null;

        // Jika kandidat sudah ada minimal 1 alternatif yang telah dinilai, jalankan kalkulasi
        if ($totalKandidat >= 1) {
            try {
                $hasilKalkulasi = $this->topsisService->hitung((int)$selectedId, (int)(Auth::id() ?? 1));
                $kandidatsById  = $hasilKalkulasi['pengajuans'];
                $kandidats      = $kandidatsById->values();

                // Paginasi hasil ranking (50 data per halaman) untuk mencegah browser crash / freeze saat ada 1000 data
                $perPage = 50;
                $currentPage = Paginator::resolveCurrentPage('page');
                $rankingCollection = collect($hasilKalkulasi['ranking']);
                $paginatedRanking = new LengthAwarePaginator(
                    $rankingCollection->forPage($currentPage, $perPage)->values(),
                    $rankingCollection->count(),
                    $perPage,
                    $currentPage,
                    [
                        'path'     => Paginator::resolveCurrentPath(),
                        'pageName' => 'page',
                    ]
                );
                $paginatedRanking->withQueryString();
            } catch (\Exception $e) {
                $hasilKalkulasi = null;
            }
        }

        return view('admin.topsis.index', compact(
            'jenisAlsintans',
            'selectedId',
            'kriterias',
            'totalBobot',
            'kandidats',
            'kandidatsById',
            'hasilKalkulasi',
            'paginatedRanking'
        ));
    }

    /**
     * Menjalankan kalkulasi 6 Langkah TOPSIS via TopsisCalculationService.
     */
    public function hitung(Request $request)
    {
        $request->validate([
            'jenis_alsintan_id' => 'required|exists:jenis_alsintans,id',
        ]);

        $jenisId    = (int) $request->jenis_alsintan_id;
        $operatorId = Auth::id() ?? 1;

        try {
            $this->topsisService->hitung($jenisId, $operatorId);

            return redirect()->route('admin.topsis.index', ['jenis_id' => $jenisId])
                ->with('success', 'Perhitungan TOPSIS 6 Langkah berhasil diperbarui!');
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.topsis.index', ['jenis_id' => $jenisId])
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menjalankan perhitungan TOPSIS', [
                'jenis_id' => $jenisId,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->route('admin.topsis.index', ['jenis_id' => $jenisId])
                ->with('error', 'Terjadi kesalahan sistem saat memperbarui perhitungan TOPSIS. Silakan coba kembali.');
        }
    }
}
