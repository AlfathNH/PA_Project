<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: BerkasPengajuan
 * Menyimpan metadata file PDF yang diunggah bersama pengajuan.
 * File fisik: storage/app/private/pengajuan/{pengajuan_id}/
 */
class BerkasPengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajuan_bantuan_id',
        'nama_berkas',
        'jenis_berkas',
        'file_path',
        'file_name_original',
        'file_mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Berkas milik satu pengajuan
    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class);
    }

    // Helper: tampilkan ukuran file dalam format manusiawi (KB/MB)
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}

