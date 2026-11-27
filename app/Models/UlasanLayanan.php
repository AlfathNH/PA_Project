<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: UlasanLayanan (Tabel ke-10 Standar KKNI Level 5)
 * Survei Kepuasan Pengguna & Indeks Kepuasan Masyarakat (IKM).
 * Dimoderasi oleh Kepala Bidang sebelum ditampilkan ke Landing Page publik.
 */
class UlasanLayanan extends Model
{
    use HasFactory;

    protected $table = 'ulasan_layanans';

    protected $fillable = [
        'kelompok_tani_id',
        'pengajuan_bantuan_id',
        'rating_bintang',
        'komentar',
        'is_tampil_landing',
        'ditinjau_oleh',
        'ditinjau_pada',
    ];

    protected $casts = [
        'rating_bintang'    => 'integer',
        'is_tampil_landing' => 'boolean',
        'ditinjau_pada'     => 'datetime',
    ];

    /**
     * Relasi ke entitas Kelompok Tani (Pemberi Ulasan)
     */
    public function kelompokTani(): BelongsTo
    {
        return $this->belongsTo(KelompokTani::class, 'kelompok_tani_id');
    }

    /**
     * Relasi opsional ke transaksi pengajuan bantuan terkait
     */
    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class, 'pengajuan_bantuan_id');
    }

    /**
     * Relasi ke pimpinan/Kepala Bidang yang meninjau & menyetujui ulasan
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditinjau_oleh');
    }

    /**
     * Scope ulasan yang disetujui untuk ditampilkan di landing page
     */
    public function scopeTampilLanding($query)
    {
        return $query->where('is_tampil_landing', true);
    }
}
