<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanBantuan;
use App\Models\Kriteria;
use App\Models\PenilaianTopsis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller: Admin PengajuanController
 * Mengelola verifikasi kelengkapan berkas usulan dan penginputan nilai kriteria TOPSIS.
 */
class PengajuanController extends Controller
{
    /**
     * Menampilkan daftar semua usulan permohonan bantuan dari Poktan.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = PengajuanBantuan::with(['user.kelompokTani', 'jenisAlsintan', 'hasilTopsis', 'penilaianTopsis', 'berkasPengajuans'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

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

        return view('admin.pengajuan.index', compact('pengajuans', 'status', 'search'));
    }

    /**
     * Memverifikasi berkas persyaratan usulan Poktan.
     */
    public function verifikasi(Request $request, PengajuanBantuan $pengajuan)
    {
        $request->validate([
            'status'        => 'required|in:diverifikasi,ditolak_admin',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pengajuan->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
            'verified_by'   => Auth::id(),
            'verified_at'   => now(),
        ]);

        $pesan = ($request->status === PengajuanBantuan::STATUS_DIVERIFIKASI)
            ? "Usulan {$pengajuan->nomor_pengajuan} berhasil diverifikasi. Silakan lanjutkan ke pengisian skor kriteria TOPSIS."
            : "Usulan {$pengajuan->nomor_pengajuan} telah ditolak saat verifikasi berkas.";

        return back()->with('success', $pesan);
    }

    /**
     * Menampilkan form input nilai kriteria TOPSIS untuk satu usulan Poktan.
     */
    public function formPenilaian(PengajuanBantuan $pengajuan)
    {
        $pengajuan->load(['user.kelompokTani', 'jenisAlsintan', 'penilaianTopsis']);
        $kriterias = Kriteria::aktif()->get();

        // Ambil nilai yang sudah pernah diinput sebelumnya (jika ada)
        $nilaiEksisting = $pengajuan->penilaianTopsis->pluck('nilai', 'kriteria_id');

        return view('admin.pengajuan.penilaian', compact('pengajuan', 'kriterias', 'nilaiEksisting'));
    }

    /**
     * Menyimpan nilai kriteria TOPSIS (matriks x_ij) untuk usulan Poktan.
     */
    public function simpanPenilaian(Request $request, PengajuanBantuan $pengajuan)
    {
        // Standarisasi koma (,) menjadi titik (.) agar input desimal khas Indonesia (cth: 38,5) tetap valid dan tidak error
        if ($request->has('nilai') && is_array($request->input('nilai'))) {
            $cleaned = [];
            foreach ($request->input('nilai') as $kId => $val) {
                $cleaned[$kId] = is_string($val) ? str_replace(',', '.', trim($val)) : $val;
            }
            $request->merge(['nilai' => $cleaned]);
        }

        $kriterias = Kriteria::aktif()->get();

        $rules = [];
        $messages = [];
        foreach ($kriterias as $kriteria) {
            $field = 'nilai.' . $kriteria->id;
            if (in_array($kriteria->kode_kriteria, ['C3', 'C4'])) {
                $rules[$field] = 'required|integer|min:0';
                $messages["{$field}.integer"] = "Nilai {$kriteria->kode_kriteria} ({$kriteria->nama_kriteria}) harus berupa bilangan bulat utuh (bukan pecahan/desimal).";
            } elseif ($kriteria->kode_kriteria === 'C5') {
                $rules[$field] = 'required|integer|between:1,5';
                $messages["{$field}.between"] = "Nilai {$kriteria->kode_kriteria} ({$kriteria->nama_kriteria}) harus dalam rentang skala 1 sampai 5.";
            } else {
                $rules[$field] = 'required|numeric|min:0';
                $messages["{$field}.numeric"] = "Nilai {$kriteria->kode_kriteria} ({$kriteria->nama_kriteria}) harus berupa angka valid.";
            }
            $messages["{$field}.required"] = "Nilai {$kriteria->kode_kriteria} wajib diisi.";
        }
        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $pengajuan, $kriterias) {
            foreach ($kriterias as $kriteria) {
                $nilai = $request->input('nilai.' . $kriteria->id);

                PenilaianTopsis::updateOrCreate(
                    [
                        'pengajuan_bantuan_id' => $pengajuan->id,
                        'kriteria_id'          => $kriteria->id,
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
            }

            // Ubah status pengajuan menjadi 'dinilai' (siap masuk engine TOPSIS)
            $pengajuan->update([
                'status' => PengajuanBantuan::STATUS_DINILAI,
            ]);
        });

        return redirect()->route('admin.pengajuan.index')
            ->with('success', "Skor kriteria untuk usulan {$pengajuan->nomor_pengajuan} ({$pengajuan->user?->kelompokTani?->nama_poktan}) berhasil disimpan! Status kini 'Siap Dihitung TOPSIS'.");
    }

    /**
     * Fitur Khusus Sidang / Prototipe:
     * Reset seluruh pengajuan yang sudah di-ACC/Tolak agar kembali ke status 'dinilai'
     * sehingga dapat didemonstrasikan hitung TOPSIS berkali-kali di depan dosen!
     */
    public function resetSimulasi(Request $request)
    {
        $validated = $request->validate([
            'jenis_alsintan_id' => 'required|integer|exists:jenis_alsintans,id',
        ], [
            'jenis_alsintan_id.required' => 'Jenis alsintan wajib dipilih untuk reset simulasi.',
            'jenis_alsintan_id.exists'   => 'Data jenis alsintan tidak ditemukan.',
        ]);

        $jenisId = (int) $validated['jenis_alsintan_id'];

        \Illuminate\Support\Facades\DB::transaction(function () use ($jenisId) {
            PengajuanBantuan::where('jenis_alsintan_id', $jenisId)
                ->update([
                    'status'         => PengajuanBantuan::STATUS_DINILAI,
                    'approved_by'    => null,
                    'approved_at'    => null,
                    'catatan_kepbid' => null,
                ]);
        });

        return back()->with('success', 'Data usulan berhasil di-reset ke status awal (Siap Dihitung TOPSIS)! Anda sekarang dapat mendemonstrasikan perhitungan ulang secara bersih.');
    }
}

