<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: PeriodeBantuan (Tabel ke-8)
 * Master data periode / gelombang tahun anggaran penyaluran Alsintan.
 */
class PeriodeBantuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_periode',
        'tahun_anggaran',
        'tanggal_buka',
        'tanggal_tutup',
        'kuota_total_unit',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'tahun_anggaran'   => 'integer',
        'kuota_total_unit' => 'integer',
        'is_active'        => 'boolean',
        'tanggal_buka'     => 'date',
        'tanggal_tutup'    => 'date',
    ];

    /**
     * Relasi One-to-Many: Satu periode anggaran mengelompokkan banyak jenis Alsintan.
     */
    public function jenisAlsintans(): HasMany
    {
        return $this->hasMany(JenisAlsintan::class, 'periode_bantuan_id');
    }

    /**
     * Scope: Hanya periode anggaran aktif saat ini.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}

