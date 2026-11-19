<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: RiwayatStatusPengajuan (Tabel ke-9)
 * Audit Trail / Tracking Log perubahan status usulan bantuan.
 */
class RiwayatStatusPengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_bantuan_id',
        'user_id',
        'status_lama',
        'status_baru',
        'catatan',
        'waktu_perubahan',
    ];

    protected $casts = [
        'waktu_perubahan' => 'datetime',
    ];

    /**
     * Relasi Many-to-One: Log ini milik satu pengajuan bantuan.
     */
    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class, 'pengajuan_bantuan_id');
    }

    /**
     * Relasi Many-to-One: Log ini dicatat oleh user tertentu (Admin/Kepbid/Poktan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

