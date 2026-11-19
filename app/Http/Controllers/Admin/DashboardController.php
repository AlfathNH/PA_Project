<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelompokTani;
use App\Models\PengajuanBantuan;
use App\Models\Kriteria;
use App\Models\JenisAlsintan;
use Illuminate\Http\Request;

/**
 * Controller: Admin DashboardController
 * Portal bagi Admin Verifikator & Operator SPK Dinas Pertanian Kab. Subang.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $stats = [
            'total_poktan'      => KelompokTani::count(),
            'total_pengajuan'   => PengajuanBantuan::count(),
            'perlu_verifikasi'  => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_DIAJUKAN)->count(),
            'siap_dihitung'     => PengajuanBantuan::siapDihitung()->count(),
            'selesai_dirangking'=> PengajuanBantuan::where('status', PengajuanBantuan::STATUS_DIRANGKING)->count(),
            'disetujui'         => PengajuanBantuan::where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)->count(),
        ];

        // Daftar pengajuan terbaru beserta relasi & fitur pencarian
        $query = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pengajuan', 'like', "%{$search}%")
                  ->orWhere('alasan_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user.kelompokTani', function ($poktanQ) use ($search) {
                      $poktanQ->where('nama_poktan', 'like', "%{$search}%")
                              ->orWhere('desa', 'like', "%{$search}%")
                              ->orWhere('kecamatan', 'like', "%{$search}%")
                              ->orWhere('nama_ketua', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jenisAlsintan', function ($alsintanQ) use ($search) {
                      $alsintanQ->where('nama_alsintan', 'like', "%{$search}%")
                                ->orWhere('kode_alsintan', 'like', "%{$search}%");
                  });
            });
        }

        $pengajuans = $query->paginate(10)->withQueryString();

        $kriterias = Kriteria::orderBy('urutan')->get();
        $totalBobot = $kriterias->where('is_active', true)->sum('bobot');

        // Master Alsintan beserta pencarian, filter status, urutan, dan paginasi (maks 6 per halaman agar tetap ringkas meski 100+ data)
        $alsintanSearch = trim((string) $request->query('alsintan_search', ''));
        $alsintanStatus = $request->query('alsintan_status');
        $alsintanSort   = $request->query('alsintan_sort', 'default');

        $alsintanQuery = JenisAlsintan::with('pengajuanBantuans');

        if ($alsintanSearch !== '') {
            $alsintanQuery->where(function ($q) use ($alsintanSearch) {
                $q->where('kode_alsintan', 'like', "%{$alsintanSearch}%")
                  ->orWhere('nama_alsintan', 'like', "%{$alsintanSearch}%")
                  ->orWhere('deskripsi', 'like', "%{$alsintanSearch}%");
            });
        }

        if ($alsintanStatus === 'aktif') {
            $alsintanQuery->where('is_active', true);
        } elseif ($alsintanStatus === 'nonaktif') {
            $alsintanQuery->where('is_active', false);
        }

        match ($alsintanSort) {
            'terbaru'   => $alsintanQuery->orderByDesc('id'),
            'kuota_asc' => $alsintanQuery->orderByDesc('is_active')->orderBy('kuota_unit', 'asc'),
            default     => $alsintanQuery->orderByDesc('is_active')->orderBy('id', 'asc'),
        };

        $jenisAlsintans = $alsintanQuery
            ->paginate(6, ['*'], 'alsintan_page')
            ->withQueryString()
            ->fragment('master-alsintan');

        $alsintanSummary = [
            'total'    => JenisAlsintan::count(),
            'aktif'    => JenisAlsintan::where('is_active', true)->count(),
            'nonaktif' => JenisAlsintan::where('is_active', false)->count(),
        ];

        return view('admin.dashboard', compact(
            'stats', 
            'pengajuans', 
            'kriterias', 
            'totalBobot', 
            'jenisAlsintans',
            'alsintanSummary',
            'alsintanSearch',
            'alsintanStatus',
            'alsintanSort',
            'search'
        ));
    }

    /**
     * Menyimpan jenis mesin alsintan baru ke master database (CREATE).
     */
    public function storeAlsintan(Request $request)
    {
        $request->validate([
            'kode_alsintan' => 'required|string|max:10|unique:jenis_alsintans,kode_alsintan',
            'nama_alsintan' => 'required|string|max:255',
            'satuan'        => 'required|string|max:50',
            'kuota_unit'    => 'required|integer|min:0|max:1000',
            'deskripsi'     => 'nullable|string|max:500',
        ], [
            'kode_alsintan.required' => 'Kode singkatan alsintan wajib diisi.',
            'kode_alsintan.unique'   => 'Kode alsintan ini sudah terdaftar sebelumnya.',
            'nama_alsintan.required' => 'Nama jenis mesin pertanian wajib diisi.',
            'kuota_unit.required'    => 'Kuota unit alokasi wajib diisi.',
            'kuota_unit.min'         => 'Kuota unit minimal 0 unit.',
        ]);

        $alsintan = JenisAlsintan::create([
            'kode_alsintan' => strtoupper(trim($request->kode_alsintan)),
            'nama_alsintan' => trim($request->nama_alsintan),
            'satuan'        => trim($request->satuan),
            'kuota_unit'    => (int) $request->kuota_unit,
            'deskripsi'     => $request->deskripsi,
            'is_active'     => true,
        ]);

        return redirect()->to(route('admin.dashboard', ['alsintan_sort' => 'terbaru']) . '#master-alsintan')
            ->with('success', "Jenis Alsintan baru [{$alsintan->kode_alsintan}] {$alsintan->nama_alsintan} berhasil ditambahkan ke inventaris dinas (Kuota TA 2026: {$alsintan->kuota_unit} {$alsintan->satuan}).");
    }

    /**
     * Memperbarui kuota alokasi alsintan (misal ada penambahan unit dari APBD-P atau Kementan).
     */
    public function updateKuotaAlsintan(Request $request, JenisAlsintan $alsintan)
    {
        $minKuota = max(0, (int) $alsintan->tersalurkan);

        $request->validate([
            'kuota_unit' => 'required|integer|min:' . $minKuota . '|max:1000',
        ], [
            'kuota_unit.required' => 'Jumlah kuota unit wajib diisi.',
            'kuota_unit.integer'  => 'Jumlah kuota harus berupa bilangan bulat.',
            'kuota_unit.min'      => "Jumlah kuota minimal {$minKuota} unit (sesuai total unit yang sudah tersalurkan).",
        ]);

        $kuotaLama = (int) $alsintan->kuota_unit;
        $alsintan->update([
            'kuota_unit' => (int) $request->kuota_unit,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', "Alokasi kuota [{$alsintan->kode_alsintan}] {$alsintan->nama_alsintan} berhasil diperbarui dari {$kuotaLama} unit menjadi {$alsintan->kuota_unit} unit.");
    }

    /**
     * Memperbarui data keseluruhan Jenis Alsintan (UPDATE).
     */
    public function updateAlsintan(Request $request, JenisAlsintan $alsintan)
    {
        $minKuota = max(0, (int) $alsintan->tersalurkan);

        $request->validate([
            'kode_alsintan' => 'required|string|max:10|unique:jenis_alsintans,kode_alsintan,' . $alsintan->id,
            'nama_alsintan' => 'required|string|max:255',
            'satuan'        => 'required|string|max:50',
            'kuota_unit'    => 'required|integer|min:' . $minKuota . '|max:1000',
            'deskripsi'     => 'nullable|string|max:500',
            'is_active'     => 'nullable|boolean',
        ], [
            'kode_alsintan.required' => 'Kode alsintan wajib diisi.',
            'kode_alsintan.unique'   => 'Kode alsintan ini sudah terpakai oleh jenis alat lain.',
            'nama_alsintan.required' => 'Nama alsintan wajib diisi.',
            'kuota_unit.min'         => "Kuota unit tidak boleh lebih kecil dari unit yang sudah disalurkan/ACC ({$minKuota} unit).",
        ]);

        $alsintan->update([
            'kode_alsintan' => strtoupper(trim($request->kode_alsintan)),
            'nama_alsintan' => trim($request->nama_alsintan),
            'satuan'        => trim($request->satuan),
            'kuota_unit'    => (int) $request->kuota_unit,
            'deskripsi'     => $request->deskripsi,
            'is_active'     => $request->boolean('is_active'),
        ]);

        return back()
            ->with('success', "Data dan alokasi kuota [{$alsintan->kode_alsintan}] {$alsintan->nama_alsintan} berhasil diperbarui.");
    }

    /**
     * Menghapus atau menonaktifkan Jenis Alsintan (DELETE).
     */
    public function destroyAlsintan(JenisAlsintan $alsintan)
    {
        $nama = $alsintan->nama_alsintan;
        $kode = $alsintan->kode_alsintan;

        // Cek apakah jenis alsintan ini sudah dipakai pada pengajuan bantuan Poktan
        $hasPengajuan = $alsintan->pengajuanBantuans()->exists();

        if ($hasPengajuan) {
            // Soft-deactivate agar tidak merusak data historis audit dan perankingan BAST
            $alsintan->update(['is_active' => false]);
            return back()
                ->with('success', "Jenis Alsintan [{$kode}] {$nama} dinonaktifkan dari daftar formulir usulan baru, namun data permohonan historis tetap terjaga untuk audit BAST.");
        }

        // Jika belum ada relasi permohonan, hapus fisik
        $alsintan->delete();

        return back()
            ->with('success', "Jenis Alsintan [{$kode}] {$nama} berhasil dihapus dari sistem.");
    }
}

