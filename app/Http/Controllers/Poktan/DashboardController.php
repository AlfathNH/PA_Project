<?php

namespace App\Http\Controllers\Poktan;

use App\Http\Controllers\Controller;
use App\Models\JenisAlsintan;
use App\Models\PengajuanBantuan;
use App\Models\BerkasPengajuan;
use App\Models\RiwayatStatusPengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Controller: Poktan DashboardController
 * Portal bagi Kelompok Tani untuk memantau status usulan dan membuat pengajuan baru.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $poktan = $user->kelompokTani;

        // Ambil riwayat pengajuan milik Poktan yang sedang login (Maksimal 5 data per halaman sesuai aturan UI)
        $pengajuans = PengajuanBantuan::with(['jenisAlsintan', 'hasilTopsis', 'berkasPengajuans'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(5);

        $jenisAlsintans = JenisAlsintan::aktif()->get();
        $sudahUlasan = false;
        if ($poktan) {
            $sudahUlasan = \App\Models\UlasanLayanan::where('kelompok_tani_id', $poktan->id)->exists();
        }
        $latestPengajuan = PengajuanBantuan::where('user_id', $user->id)->latest()->first();

        return view('poktan.dashboard', compact('user', 'poktan', 'pengajuans', 'jenisAlsintans', 'sudahUlasan', 'latestPengajuan'));
    }

    /**
     * Menyimpan permohonan pengajuan bantuan baru dari Poktan dengan upload berkas aman.
     */
    public function storePengajuan(Request $request)
    {
        $request->validate([
            'jenis_alsintan_id'         => 'required|exists:jenis_alsintans,id',
            'tahun_pengajuan'           => 'required|integer|min:2024|max:2030',
            'jumlah_diminta'            => 'required|integer|min:1|max:10',
            'jumlah_alsintan_eksisting' => 'nullable|integer|min:0',
            'alasan_pengajuan'          => 'required|string|min:5',
            'berkas_proposal'           => [
                'nullable',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'max:5120', // Maksimal 5 MB
            ],
        ], [
            'jenis_alsintan_id.required'         => 'Silakan pilih kategori alat (alsintan) yang diajukan.',
            'jumlah_diminta.integer'             => 'Jumlah unit yang diminta harus berupa bilangan bulat.',
            'jumlah_alsintan_eksisting.integer'  => 'Jumlah alat eksisting harus berupa bilangan bulat (unit fisik utuh, bukan desimal).',
            'alasan_pengajuan.required'          => 'Urgensi & alasan pengajuan wajib diisi.',
            'alasan_pengajuan.min'               => 'Urgensi pengajuan minimal 5 karakter.',
            'berkas_proposal.mimes'              => 'Berkas proposal harus berformat PDF.',
            'berkas_proposal.mimetypes'          => 'Format berkas tidak valid. Berkas wajib berupa dokumen PDF asli (Anti-Malware).',
            'berkas_proposal.max'                => 'Ukuran berkas proposal tidak boleh melebihi 5 MB (5.120 KB).',
        ]);

        $user = Auth::user();

        // Hitung nomor pengajuan unik & aman dari tabrakan
        $tahun = (int) $request->tahun_pengajuan;
        $countThisYear = PengajuanBantuan::where('tahun_pengajuan', $tahun)->count();
        $nomorPengajuan = sprintf('PB-%d-%03d', $tahun, $countThisYear + 1);

        // Jika nomor sudah terpakai karena penghapusan/pruning, loop hingga nomor unik
        $suffix = $countThisYear + 1;
        while (PengajuanBantuan::where('nomor_pengajuan', $nomorPengajuan)->exists()) {
            $suffix++;
            $nomorPengajuan = sprintf('PB-%d-%03d', $tahun, $suffix);
        }

        $eksisting = (int) ($request->input('jumlah_alsintan_eksisting', 0) ?? 0);
        $storedPath = null;

        try {
            $pengajuan = DB::transaction(function () use ($user, $request, $nomorPengajuan, $tahun, $eksisting, &$storedPath) {
                $pengajuan = PengajuanBantuan::create([
                    'user_id'                   => $user->id,
                    'jenis_alsintan_id'         => $request->jenis_alsintan_id,
                    'nomor_pengajuan'           => $nomorPengajuan,
                    'tahun_pengajuan'           => $tahun,
                    'jumlah_diminta'            => (int) $request->jumlah_diminta,
                    'jumlah_alsintan_eksisting' => $eksisting,
                    'alasan_pengajuan'          => $request->alasan_pengajuan,
                    'status'                    => PengajuanBantuan::STATUS_DIAJUKAN,
                    'expired_at'                => now()->addDays(90), // Aturan Auto-Pruning: masa berlaku 90 hari
                ]);

                // Pemrosesan Unggah Berkas Proposal Terverifikasi & Aman (Anti-Malware & Anti-Path-Traversal)
                if ($request->hasFile('berkas_proposal')) {
                    try {
                        $file = $request->file('berkas_proposal');
                        if ($file->isValid()) {
                            // Gunakan UUID acak sebagai nama file fisik di server (mencegah double extension & overwrite)
                            $safeFileName = Str::uuid()->toString() . '.pdf';
                            $folderPath   = "pengajuan/{$pengajuan->id}";

                            // Simpan di disk private
                            $storedPath = Storage::disk('private')->putFileAs($folderPath, $file, $safeFileName);

                            BerkasPengajuan::create([
                                'pengajuan_bantuan_id' => $pengajuan->id,
                                'nama_berkas'          => 'Surat Proposal Usulan ' . $pengajuan->nomor_pengajuan,
                                'jenis_berkas'         => 'surat_permohonan',
                                'file_path'            => $storedPath,
                                'file_name_original'   => $file->getClientOriginalName(),
                                'file_mime_type'       => 'application/pdf',
                                'file_size'            => $file->getSize(),
                            ]);
                        }
                    } catch (\Throwable $fileEx) {
                        Log::warning('Unggah berkas fisik proposal dilewati atau gagal di serverless', [
                            'pengajuan_id' => $pengajuan->id,
                            'error'        => $fileEx->getMessage(),
                        ]);
                    }
                }

                return $pengajuan;
            });

            return redirect()->route('poktan.dashboard', ['tab' => 'status'])
                ->with('success', "Permohonan usulan {$nomorPengajuan} berhasil diajukan dan tercatat dalam sistem. Menunggu tahapan verifikasi berkas oleh Admin Dinas.")
                ->with('show_rating_popup', true)
                ->with('baru_diajukan_id', $pengajuan->id);
        } catch (\Throwable $e) {
            // Hapus berkas fisik jika transaksi database dibatalkan (rollback)
            if ($storedPath && Storage::disk('private')->exists($storedPath)) {
                Storage::disk('private')->delete($storedPath);
            }

            Log::error('Gagal menyimpan usulan Poktan', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return redirect()->route('poktan.dashboard')
                ->withInput()
                ->with('error', 'Terjadi kendala saat menyimpan usulan bantuan. Pastikan seluruh data dan berkas PDF valid, lalu coba kembali.');
        }
    }

    /**
     * Mengunduh berkas proposal pengajuan dengan Proteksi Anti-IDOR ketat.
     */
    public function unduhBerkas(PengajuanBantuan $pengajuan, BerkasPengajuan $berkas)
    {
        // 1. Verifikasi Otorisasi via Gate/Policy (Anti-IDOR)
        if (!Gate::allows('download', [$pengajuan, $berkas])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengunduh dokumen proposal dari kelompok tani lain (Proteksi Anti-IDOR aktif).');
        }

        // 2. Proteksi Anti-Path-Traversal (Cegah karakter traversal atau path absolut ilegal)
        if (str_contains($berkas->file_path, '..') || str_starts_with($berkas->file_path, '/') || str_starts_with($berkas->file_path, '\\')) {
            abort(403, 'Akses Ditolak: Deteksi manipulasi path berkas ilegal.');
        }

        // 3. Cek integritas berkas pada disk penyimpanan private
        if (!Storage::disk('private')->exists($berkas->file_path)) {
            return back()->with('error', 'Dokumen PDF ini sudah tidak tersedia di server penyimpanan (telah dibersihkan secara otomatis oleh engine Auto-Pruning karena melewati batas masa retensi arsip).');
        }

        $namaRaw     = $berkas->file_name_original ?: ($berkas->nama_berkas . '.pdf');
        $namaUnduhan = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($namaRaw));
        if (trim((string) $namaUnduhan, '._') === '') {
            $namaUnduhan = 'dokumen_usulan.pdf';
        } elseif (!str_ends_with(strtolower($namaUnduhan), '.pdf')) {
            $namaUnduhan .= '.pdf';
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $privateDisk */
        $privateDisk = Storage::disk('private');

        return $privateDisk->download($berkas->file_path, $namaUnduhan);
    }

    /**
     * Memperbarui data profil Kelompok Tani (luas hamparan, jumlah anggota, ketua, kontak, desa).
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();
        $poktan = $user->kelompokTani;

        if (!$poktan) {
            return back()->with('error', 'Profil Kelompok Tani tidak ditemukan untuk akun ini.');
        }

        $validated = $request->validate([
            'nama_poktan'        => 'required|string|max:255',
            'nomor_badan_hukum'  => 'nullable|string|max:100',
            'nama_ketua'         => 'required|string|max:255',
            'no_hp_ketua'        => 'required|string|max:20',
            'desa'               => 'required|string|max:100',
            'kecamatan'          => 'required|string|max:100',
            'luas_lahan_total'   => 'required|numeric|min:0.1|max:10000',
            'jumlah_anggota'     => 'required|integer|min:1|max:1000',
            'alamat_lengkap'     => 'nullable|string|max:500',
        ], [
            'nama_poktan.required'      => 'Nama kelompok tani wajib diisi.',
            'nama_ketua.required'       => 'Nama ketua kelompok tani wajib diisi.',
            'no_hp_ketua.required'      => 'Nomor HP ketua wajib diisi untuk koordinasi dinas.',
            'desa.required'             => 'Nama desa wilayah hamparan sawah wajib diisi.',
            'kecamatan.required'        => 'Nama kecamatan wajib diisi.',
            'luas_lahan_total.required' => 'Luas lahan total hamparan wajib diisi.',
            'luas_lahan_total.numeric'  => 'Luas lahan harus berupa angka (hektar).',
            'luas_lahan_total.min'      => 'Luas lahan minimal 0.1 Hektar.',
            'jumlah_anggota.required'   => 'Jumlah anggota aktif kelompok tani wajib diisi.',
            'jumlah_anggota.integer'    => 'Jumlah anggota harus berupa bilangan bulat orang.',
            'jumlah_anggota.min'        => 'Jumlah anggota minimal 1 orang.',
        ]);

        $poktan->update($validated);

        return back()->with('success', "Profil {$poktan->nama_poktan} berhasil diperbarui! Data luas hamparan ({$poktan->luas_lahan_total} Ha) dan anggota aktif ({$poktan->jumlah_anggota} orang) telah tersinkronisasi.");
    }
}

