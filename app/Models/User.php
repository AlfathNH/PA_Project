<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model: User
 * Tabel autentikasi utama untuk semua aktor sistem.
 * Role: 'kelompok_tani' | 'admin' | 'kepala_bidang'
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // Konstanta Role untuk kemudahan referensi di seluruh aplikasi
    const ROLE_POKTAN  = 'kelompok_tani';
    const ROLE_ADMIN   = 'admin';
    const ROLE_KEPBID  = 'kepala_bidang';

    // =========================================================
    // RELASI
    // =========================================================

    // User Poktan memiliki satu profil KelompokTani
    public function kelompokTani(): HasOne
    {
        return $this->hasOne(KelompokTani::class);
    }

    // Relasi One-to-Many ke jejak audit status yang dilakukan user ini (Tabel ke-9)
    public function riwayatStatusDibuat(): HasMany
    {
        return $this->hasMany(RiwayatStatusPengajuan::class, 'user_id');
    }

    // =========================================================
    // HELPER METHODS
    // =========================================================

    // Cek apakah user adalah Kelompok Tani
    public function isPoktan(): bool
    {
        return $this->role === self::ROLE_POKTAN;
    }

    // Cek apakah user adalah Admin
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Cek apakah user adalah Kepala Bidang
    public function isKepBid(): bool
    {
        return $this->role === self::ROLE_KEPBID;
    }

    public function isKepalaBidang(): bool
    {
        return $this->isKepBid();
    }
}
