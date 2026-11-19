<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: HasilTopsis
 * Menyimpan OUTPUT kalkulasi TOPSIS per pengajuan:
 * - D+ (jarak ke ideal positif)
 * - D- (jarak ke ideal negatif)
 * - Vi (nilai preferensi) = D- / (D+ + D-)
 * - Ranking akhir
 *
 * Diisi oleh TopsisCalculationService setelah kalkulasi selesai.
 */
class HasilTopsis extends Model
{
    use HasFactory;

    protected $table = 'hasil_topsis';

    protected $fillable = [
        'pengajuan_bantuan_id',
        'batch_kalkulasi_id',
        'jarak_ideal_positif',   // D+
        'jarak_ideal_negatif',   // D-
        'nilai_preferensi',      // Vi = D- / (D+ + D-)
        'ranking',
        'dihitung_pada',
        'dihitung_oleh',
    ];

    protected $casts = [
        'jarak_ideal_positif' => 'float',
        'jarak_ideal_negatif' => 'float',
        'nilai_preferensi'    => 'float',
        'ranking'             => 'integer',
        'dihitung_pada'       => 'datetime',
    ];

    // Hasil milik satu pengajuan
    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class);
    }

    // Hasil dihitung oleh user Admin
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dihitung_oleh');
    }

    // Helper: format nilai preferensi dalam persen (untuk tampilan)
    public function getNilaiPreferensiPersenAttribute(): string
    {
        return number_format($this->nilai_preferensi * 100, 4) . '%';
    }
}

