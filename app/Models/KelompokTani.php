<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: KelompokTani
 * Profil lengkap Kelompok Tani, berelasi 1-to-1 dengan User.
 */
class KelompokTani extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kecamatan_id',
        'nama_poktan',
        'nomor_badan_hukum',
        'nama_ketua',
        'no_hp_ketua',
        'desa',
        'kecamatan',
        'alamat_lengkap',
        'luas_lahan_total',
        'jumlah_anggota',
    ];

    protected $casts = [
        'luas_lahan_total' => 'float',
        'jumlah_anggota'   => 'integer',
        'kecamatan_id'     => 'integer',
    ];

    // Profil Poktan milik satu User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Many-to-One ke master Kecamatan (Tabel ke-7)
    public function kecamatanDetail(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    // Poktan dapat memiliki banyak pengajuan bantuan
    public function pengajuanBantuans(): HasMany
    {
        return $this->hasMany(PengajuanBantuan::class, 'user_id', 'user_id');
    }

    // Relasi ke tabel ke-10: Ulasan Layanan & IKM
    public function ulasans(): HasMany
    {
        return $this->hasMany(UlasanLayanan::class, 'kelompok_tani_id');
    }
}

