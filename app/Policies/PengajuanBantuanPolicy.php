<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PengajuanBantuan;
use App\Models\BerkasPengajuan;

/**
 * Policy: PengajuanBantuanPolicy
 * 
 * Lapisan Keamanan Akses Objek (Anti-IDOR: Insecure Direct Object References).
 * Memastikan setiap aksi terhadap data atau berkas proposal pengajuan bantuan divalidasi
 * secara ketat berdasarkan hak kepemilikan akun (User ID) dan Peran (Role RBAC).
 */
class PengajuanBantuanPolicy
{
    /**
     * Menentukan apakah pengguna memiliki hak akses melihat rincian pengajuan bantuan.
     * 
     * Aturan:
     * - Admin Dinas & Kepala Bidang: Memiliki akses penuh (full view) untuk kebutuhan verifikasi & persetujuan.
     * - Kelompok Tani (Poktan): HANYA boleh melihat data pengajuan miliknya sendiri ($pengajuan->user_id === $user->id).
     */
    public function view(User $user, PengajuanBantuan $pengajuan): bool
    {
        if ($user->isAdmin() || $user->isKepBid()) {
            return true;
        }

        if (empty($user->id) || empty($pengajuan->user_id)) {
            return false;
        }

        return (int) $pengajuan->user_id === (int) $user->id;
    }

    /**
     * Menentukan apakah pengguna boleh mengunduh berkas fisik PDF proposal.
     * 
     * Aturan Anti-IDOR:
     * - Pengguna harus lolos otorisasi view() pengajuan.
     * - Jika objek berkas disertakan, pastikan berkas tersebut secara relasional benar milik pengajuan yang diminta.
     */
    public function download(User $user, PengajuanBantuan $pengajuan, ?BerkasPengajuan $berkas = null): bool
    {
        // 1. Cek hak akses ke pengajuan induk (Anti-IDOR)
        if (!$this->view($user, $pengajuan)) {
            return false;
        }

        // 2. Cegah manipulasi ID berkas silang (Cross-Resource Tampering)
        if ($berkas && (int) $berkas->pengajuan_bantuan_id !== (int) $pengajuan->id) {
            return false;
        }

        return true;
    }

    /**
     * Menentukan apakah pengguna berhak memperbarui data usulan.
     * Poktan hanya boleh mengedit jika usulan masih berstatus 'draft' atau 'diajukan'.
     */
    public function update(User $user, PengajuanBantuan $pengajuan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $pengajuan->user_id === (int) $user->id
            && in_array($pengajuan->status, [PengajuanBantuan::STATUS_DRAFT, PengajuanBantuan::STATUS_DIAJUKAN]);
    }

    /**
     * Menentukan apakah pengguna berhak menghapus berkas / data pengajuan.
     */
    public function delete(User $user, PengajuanBantuan $pengajuan): bool
    {
        return $user->isAdmin();
    }
}

