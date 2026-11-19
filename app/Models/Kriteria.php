<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: Kriteria
 * Mewakili satu kriteria TOPSIS yang bersifat dinamis.
 * Kolom paling penting: 'bobot' (weight) dan 'tipe' (benefit/cost).
 *
 * Digunakan oleh TopsisCalculationService dalam:
 * - Langkah 3: Normalisasi Terbobot (y_ij = w_j * r_ij)
 * - Langkah 4: Menentukan A+ dan A- berdasarkan tipe
 */
class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kriteria',
        'kode_kriteria',
        'deskripsi',
        'bobot',
        'tipe',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'bobot'     => 'float',
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    // Satu kriteria dapat digunakan untuk penilaian banyak pengajuan
    public function penilaianTopsis(): HasMany
    {
        return $this->hasMany(PenilaianTopsis::class);
    }

    // Scope: hanya kriteria yang aktif (digunakan dalam kalkulasi TOPSIS)
    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    // Helper: apakah kriteria ini bertipe benefit?
    public function isBenefit(): bool
    {
        return $this->tipe === 'benefit';
    }
}

