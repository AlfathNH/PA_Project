<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Kecamatan (Tabel ke-7)
 * Master data wilayah Kecamatan di Kabupaten Subang.
 */
class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kecamatan',
        'kode_kecamatan',
        'zona_wilayah',
        'deskripsi_wilayah',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi One-to-Many: Satu kecamatan menaungi banyak Kelompok Tani (Poktan).
     */
    public function kelompokTanis(): HasMany
    {
        return $this->hasMany(KelompokTani::class, 'kecamatan_id');
    }

    /**
     * Scope: Hanya kecamatan aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}

