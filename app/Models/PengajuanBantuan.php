<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Model: PengajuanBantuan
 *
 * Model INTI aplikasi. Mewakili satu pengajuan bantuan Alsintan dari Poktan.
 *
 * FITUR KHUSUS: MassPrunable
 * ===========================
 * Trait MassPrunable dari Laravel digunakan untuk menghapus berkas PDF
 * pengajuan yang sudah KEDALUWARSA atau DITOLAK secara otomatis.
 *
 * Pruning dijalankan via: php artisan model:prune
 * Dijadwalkan: setiap hari pukul 01:00 WIB di routes/console.php
 *
 * Kriteria pruning (method prunable()):
 *   - Status = 'ditolak_admin' ATAU 'ditolak_kabid'
 *   - ATAU tanggal expired_at sudah terlewati
 *   - DAN file belum pernah dipruning (files_pruned_at = NULL)
 */
class PengajuanBantuan extends Model
{
    use HasFactory;
    use MassPrunable; // <-- Aktifkan fitur Auto-Pruning Laravel

    protected $fillable = [
        'user_id',
        'jenis_alsintan_id',
        'nomor_pengajuan',
        'tahun_pengajuan',
        'jumlah_diminta',
        'jumlah_alsintan_eksisting',
        'alasan_pengajuan',
        'catatan_admin',
        'catatan_kepbid',
        'status',
        'expired_at',
        'files_pruned_at',
        'verified_by',
        'approved_by',
        'verified_at',
        'approved_at',
    ];

    protected $casts = [
        'expired_at'      => 'datetime',
        'files_pruned_at' => 'datetime',
        'verified_at'     => 'datetime',
        'approved_at'     => 'datetime',
        'tahun_pengajuan' => 'integer',
        'jumlah_diminta'  => 'integer',
    ];

    // =========================================================
    // KONSTANTA STATUS (State Machine)
    // =========================================================
    const STATUS_DRAFT           = 'draft';
    const STATUS_DIAJUKAN        = 'diajukan';
    const STATUS_DIVERIFIKASI    = 'diverifikasi';
    const STATUS_DINILAI         = 'dinilai';
    const STATUS_DIRANGKING      = 'dirangking';
    const STATUS_DISETUJUI_KABID = 'disetujui_kabid';
    const STATUS_DITOLAK_ADMIN   = 'ditolak_admin';
    const STATUS_DITOLAK_KABID   = 'ditolak_kabid';

    // =========================================================
    // METODE MassPrunable — INTI AUTO-PRUNING
    // =========================================================

    /**
     * Mendefinisikan query untuk menentukan record mana yang akan dipruning.
     *
     * Kriteria pruning (sesuai aturan bisnis):
     * 1. Status ditolak (admin atau kepbid)
     * 2. ATAU tanggal expired_at sudah lewat
     * 3. DAN file belum dipruning sebelumnya (files_pruned_at = null)
     *
     * Metode ini dipanggil oleh: php artisan model:prune
     */
    public function prunable(): Builder
    {
        // Ambil record yang memenuhi kriteria penghapusan file
        return static::query()
            ->where(function (Builder $query) {
                // Kondisi 1: Pengajuan yang sudah ditolak
                $query->whereIn('status', [
                    self::STATUS_DITOLAK_ADMIN,
                    self::STATUS_DITOLAK_KABID,
                ])
                // Kondisi 2: ATAU pengajuan yang sudah kedaluwarsa
                ->orWhere('expired_at', '<', now());
            })
            // Kondisi 3: Hanya yang belum pernah dipruning sebelumnya
            ->whereNull('files_pruned_at');
    }

    /**
     * Hook yang dijalankan SEBELUM setiap record yang masuk query prunable() dihapus.
     * Di sinilah logika penghapusan file fisik PDF dilakukan.
     *
     * PENTING: MassPrunable tidak memanggil event deleting/deleted model,
     * jadi kita override pruning() untuk menambahkan logika custom.
     */
    protected function pruning(): void
    {
        // Ambil semua berkas yang terkait dengan pengajuan ini
        $berkas = $this->berkasPengajuans;

        foreach ($berkas as $item) {
            // Hapus file fisik PDF dari storage jika file tersebut ada
            if (Storage::disk('private')->exists($item->file_path)) {
                Storage::disk('private')->delete($item->file_path);

                // Catat log penghapusan file ke pruning.log
                Log::channel('pruning')->info("File dihapus oleh pruner", [
                    'pengajuan_id' => $this->id,
                    'nomor'        => $this->nomor_pengajuan,
                    'file_path'    => $item->file_path,
                    'status'       => $this->status,
                    'expired_at'   => $this->expired_at?->toDateTimeString(),
                ]);
            }
        }

        // Tandai bahwa file sudah dipruning (agar tidak diproses ulang)
        // Gunakan updateQuietly agar tidak memicu event lain
        $this->updateQuietly(['files_pruned_at' => now()]);

        // Coba hapus folder kosong pengajuan setelah semua file terhapus
        $folderPath = "pengajuan/{$this->id}";
        if (Storage::disk('private')->exists($folderPath)) {
            // Cek apakah folder sudah kosong
            $remainingFiles = Storage::disk('private')->files($folderPath);
            if (empty($remainingFiles)) {
                Storage::disk('private')->deleteDirectory($folderPath);
            }
        }
    }

    // =========================================================
    // RELASI (Eloquent Relationships)
    // =========================================================

    // Pengajuan milik seorang User (Poktan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Pengajuan terkait satu jenis Alsintan
    public function jenisAlsintan(): BelongsTo
    {
        return $this->belongsTo(JenisAlsintan::class);
    }

    // Pengajuan memiliki banyak berkas PDF
    public function berkasPengajuans(): HasMany
    {
        return $this->hasMany(BerkasPengajuan::class);
    }

    // Pengajuan memiliki banyak nilai per kriteria TOPSIS
    public function penilaianTopsis(): HasMany
    {
        return $this->hasMany(PenilaianTopsis::class);
    }

    // Pengajuan memiliki satu hasil TOPSIS (ranking)
    public function hasilTopsis(): HasOne
    {
        return $this->hasOne(HasilTopsis::class);
    }

    // Relasi ke Admin yang memverifikasi
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Relasi ke Kepala Bidang yang menyetujui
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relasi One-to-Many ke log audit trail perubahan status (Tabel ke-9)
    public function riwayatStatus(): HasMany
    {
        return $this->hasMany(RiwayatStatusPengajuan::class, 'pengajuan_bantuan_id')->latest('waktu_perubahan');
    }

    /**
     * Booted: Otomatis mencatat riwayat perubahan status (Audit Trail)
     * tanpa perlu memodifikasi satupun Controller atau View.
     */
    protected static function booted(): void
    {
        // Saat pengajuan pertama kali dibuat, set expired_at = +90 hari
        static::creating(function (PengajuanBantuan $pengajuan) {
            if (is_null($pengajuan->expired_at)) {
                $pengajuan->setAttribute('expired_at', now()->addDays(90));
            }
        });

        static::created(function (PengajuanBantuan $pengajuan) {
            try {
                RiwayatStatusPengajuan::create([
                    'pengajuan_bantuan_id' => $pengajuan->id,
                    'user_id'              => Auth::id() ?? $pengajuan->user_id,
                    'status_lama'          => null,
                    'status_baru'          => $pengajuan->status ?? self::STATUS_DIAJUKAN,
                    'catatan'              => 'Pengajuan bantuan perdana berhasil didaftarkan ke sistem.',
                    'waktu_perubahan'      => now(),
                ]);
            } catch (\Throwable $e) {
                // Silently bypass to ensure backward-compatibility
            }
        });

        static::updated(function (PengajuanBantuan $pengajuan) {
            if ($pengajuan->isDirty('status')) {
                try {
                    $catatan = match($pengajuan->status) {
                        self::STATUS_DIVERIFIKASI    => $pengajuan->catatan_admin ? 'Berkas diverifikasi oleh Admin Dinas: ' . $pengajuan->catatan_admin : 'Berkas persyaratan berhasil diverifikasi oleh Admin Dinas.',
                        self::STATUS_DINILAI         => 'Penilaian kriteria CPCL telah diinput oleh Verifikator.',
                        self::STATUS_DIRANGKING      => 'Kalkulasi algoritma SPK TOPSIS 6 langkah selesai dijalankan.',
                        self::STATUS_DISETUJUI_KABID => $pengajuan->catatan_kepbid ? 'Disetujui (ACC) oleh Kepala Bidang: ' . $pengajuan->catatan_kepbid : 'Disetujui (ACC) oleh Kepala Bidang untuk penyaluran bantuan.',
                        self::STATUS_DITOLAK_ADMIN   => $pengajuan->catatan_admin ? 'Ditolak pada verifikasi berkas: ' . $pengajuan->catatan_admin : 'Usulan ditolak pada tahap verifikasi berkas oleh Dinas.',
                        self::STATUS_DITOLAK_KABID   => $pengajuan->catatan_kepbid ? 'Ditolak oleh Kepala Bidang: ' . $pengajuan->catatan_kepbid : 'Usulan ditolak oleh Kepala Bidang.',
                        default                      => 'Status pengajuan diperbarui.',
                    };

                    RiwayatStatusPengajuan::create([
                        'pengajuan_bantuan_id' => $pengajuan->id,
                        'user_id'              => Auth::id(),
                        'status_lama'          => $pengajuan->getOriginal('status'),
                        'status_baru'          => $pengajuan->status,
                        'catatan'              => $catatan,
                        'waktu_perubahan'      => now(),
                    ]);
                } catch (\Throwable $e) {
                    // Silently bypass
                }
            }
        });
    }

    // =========================================================
    // HELPER METHODS & SCOPES
    // =========================================================

    // Scope: pengajuan yang siap dihitung TOPSIS (sudah diverifikasi, dinilai, atau dirangking untuk hitung ulang)
    public function scopeSiapDihitung(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_DIVERIFIKASI,
            self::STATUS_DINILAI,
            self::STATUS_DIRANGKING,
        ]);
    }

    // Scope: pengajuan yang sedang menunggu keputusan akhir pimpinan (sudah dirangking TOPSIS)
    public function scopeMenungguKeputusan(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIRANGKING);
    }

    // Scope: pengajuan yang sudah diputuskan oleh Kepala Bidang (Disetujui / Ditolak)
    public function scopeSudahDiputuskan(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_DISETUJUI_KABID,
            self::STATUS_DITOLAK_KABID,
        ]);
    }

    // Helper: hitung sisa hari sebelum berkas fisik kedaluwarsa / dipruning
    public function sisaHariRetensi(): ?int
    {
        if (!$this->expired_at) {
            return null;
        }

        return (int) round(now()->diffInDays($this->expired_at, false));
    }

    // Helper: apakah pengajuan ini sudah kedaluwarsa?
    public function isExpired(): bool
    {
        return $this->expired_at && $this->expired_at->isPast();
    }

    // Helper: apakah file sudah dihapus oleh pruner?
    public function isFilesPruned(): bool
    {
        return !is_null($this->files_pruned_at);
    }
}

