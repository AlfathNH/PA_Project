<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: PenilaianTopsis
 * Nilai (skor) yang diberikan Admin untuk satu Pengajuan pada satu Kriteria.
 * Ini adalah data sumber untuk Langkah 1 (Matriks Keputusan) TOPSIS.
 *
 * x_ij = nilai Poktan ke-i (pengajuan) pada Kriteria ke-j
 */
class PenilaianTopsis extends Model
{
    use HasFactory;

    protected $table = 'penilaian_topsis';

    protected $fillable = [
        'pengajuan_bantuan_id',
        'kriteria_id',
        'nilai',  // x_ij: nilai asli pada matriks keputusan
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    // Penilaian milik satu pengajuan
    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class);
    }

    // Penilaian mengacu pada satu kriteria
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }
}

