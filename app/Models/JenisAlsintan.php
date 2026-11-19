<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: JenisAlsintan
 * Master data kategori Alat dan Mesin Pertanian (Alsintan).
 * Contoh: Traktor Roda 2, Pompa Air, Sprayer, dll.
 */
class JenisAlsintan extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_bantuan_id',
        'nama_alsintan',
        'kode_alsintan',
        'deskripsi',
        'satuan',
        'kuota_unit',
        'is_active',
    ];

    protected $casts = [
        'periode_bantuan_id' => 'integer',
        'kuota_unit'         => 'integer',
        'is_active'          => 'boolean',
    ];

    // Relasi Many-to-One ke master Periode Anggaran (Tabel ke-8)
    public function periodeBantuan(): BelongsTo
    {
        return $this->belongsTo(PeriodeBantuan::class, 'periode_bantuan_id');
    }

    // Satu jenis Alsintan dapat memiliki banyak pengajuan
    public function pengajuanBantuans(): HasMany
    {
        return $this->hasMany(PengajuanBantuan::class);
    }

    /**
     * Hitung total unit yang telah disetujui (ACC) oleh Kepala Bidang.
     */
    public function getTersalurkanAttribute(): int
    {
        if ($this->relationLoaded('pengajuanBantuans')) {
            return (int) $this->pengajuanBantuans
                ->where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)
                ->sum('jumlah_diminta');
        }
        return (int) $this->pengajuanBantuans()
            ->where('status', PengajuanBantuan::STATUS_DISETUJUI_KABID)
            ->sum('jumlah_diminta');
    }

    /**
     * Hitung sisa kuota alsintan yang masih tersedia untuk disalurkan.
     */
    public function getSisaKuotaAttribute(): int
    {
        return max(0, (int) ($this->kuota_unit ?? 10) - $this->tersalurkan);
    }

    /**
     * Persentase serapan kuota (0 - 100%).
     */
    public function getPersentaseTersalurkanAttribute(): int
    {
        $kuota = (int) ($this->kuota_unit ?? 10);
        if ($kuota <= 0) return 100;
        return (int) min(100, round(($this->tersalurkan / $kuota) * 100));
    }

    // Scope: hanya yang aktif
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}

