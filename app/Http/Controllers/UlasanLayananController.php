<?php

namespace App\Http\Controllers;

use App\Models\UlasanLayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UlasanLayananController extends Controller
{
    /**
     * Simpan ulasan / survei kepuasan layanan dari Poktan (Sifatnya Opsional)
     */
    public function storePoktan(Request $request): RedirectResponse
    {
        $request->validate([
            'rating_bintang'       => 'required|integer|min:1|max:5',
            'komentar'             => 'nullable|string|max:1000',
            'pengajuan_bantuan_id' => 'nullable|exists:pengajuan_bantuans,id',
        ], [
            'rating_bintang.required' => 'Silakan pilih rating bintang penilaian (1 s.d. 5).',
            'rating_bintang.min'      => 'Rating minimal adalah 1 bintang.',
            'rating_bintang.max'      => 'Rating maksimal adalah 5 bintang.',
            'komentar.max'            => 'Komentar atau saran maksimal 1000 karakter.',
        ]);

        $user = Auth::user();
        $poktan = $user->kelompokTani;

        if (!$poktan) {
            return back()->with('error', 'Profil Kelompok Tani tidak ditemukan.');
        }

        UlasanLayanan::create([
            'kelompok_tani_id'     => $poktan->id,
            'pengajuan_bantuan_id' => $request->pengajuan_bantuan_id,
            'rating_bintang'       => (int) $request->rating_bintang,
            'komentar'             => $request->komentar ? strip_tags(trim($request->komentar)) : null,
            'is_tampil_landing'    => false, // Menunggu persetujuan / kurasi Kepala Bidang
        ]);

        return back()->with('success', 'Terima kasih atas penilaian dan ulasan kepuasan Anda untuk peningkatan layanan Dinas Pertanian.');
    }

    /**
     * Moderasi / Kurasi Ulasan oleh Kepala Bidang
     * Memilih apakah ulasan ditampilkan di Landing Page publik (Anti-Spam / Filter Mutu)
     */
    public function toggleLanding(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->isKepalaBidang()) {
            abort(403, 'Hanya Kepala Bidang yang berwenang memoderasi ulasan kepuasan layanan.');
        }

        $ulasan = UlasanLayanan::with('kelompokTani')->findOrFail($id);
        $ulasan->is_tampil_landing = !$ulasan->is_tampil_landing;
        $ulasan->ditinjau_oleh = $user->id;
        $ulasan->ditinjau_pada = now();
        $ulasan->save();

        $namaPoktan = $ulasan->kelompokTani?->nama_poktan ?? 'Kelompok Tani';
        $statusMsg = $ulasan->is_tampil_landing
            ? "Ulasan dari {$namaPoktan} disetujui dan kini ditampilkan di halaman depan (Landing Page)."
            : "Ulasan dari {$namaPoktan} berhasil disembunyikan dari halaman depan.";

        return back()->with('success', $statusMsg);
    }
}
