# 📋 PROJECT BLUEPRINT
## Sistem Pendukung Keputusan Kelayakan Usulan Program Bantuan Pertanian
### Metode TOPSIS Berbasis Web — Dinas Pertanian Kabupaten Subang

> **Status:** 🔴 Inisialisasi  
> **Versi Dokumen:** 1.0.0  
> **Tanggal:** 2026-11-04  
> **Dikunci oleh:** Antigravity AI (Technical Co-Supervisor)

---

## 1. IDENTITAS PROYEK

| Atribut | Nilai |
|---|---|
| **Nama Sistem** | SPK Kelayakan Bantuan Alsintan Dinas Pertanian Kab. Subang |
| **Jenis Proyek** | Tugas Akhir / Proyek Akhir (PA) |
| **Institusi** | Dinas Pertanian Kabupaten Subang |
| **Metode SPK** | TOPSIS (Technique for Order Preference by Similarity to Ideal Solution) |
| **Stack** | PHP 8.5 · Laravel 11 · Blade · TailwindCSS · MySQL |
| **Pola Arsitektur** | MVC + Service Layer |

---

## 2. BATASAN MASALAH (TERKUNCI — TIDAK BOLEH BERUBAH)

> PERINGATAN: Ruang lingkup berikut adalah BATAS KERAS proyek ini.
> Setiap fitur di luar daftar ini dianggap scope creep dan wajib ditolak.

### IN-SCOPE (DALAM LINGKUP)

1. **Modul Pengajuan Bantuan Alsintan**
   - Kelompok Tani (Poktan) dapat mendaftar & login ke sistem
   - Poktan mengisi formulir pengajuan bantuan Alat dan Mesin Pertanian (Alsintan)
   - Jenis Alsintan: Traktor Roda 2/4, Sprayer/PUTS, Pompa Air, Rice Transplanter, Combine Harvester
   - Poktan mengunggah berkas persyaratan dalam format PDF (maks. 5MB per file)
   - Sistem menyimpan metadata pengajuan beserta path file di database

2. **Modul Auto-Expired & File Pruning**
   - Setiap pengajuan memiliki kolom expired_at (default: 90 hari sejak dibuat)
   - Scheduler Laravel menjalankan pruning otomatis setiap hari jam 01:00
   - Berkas PDF dihapus jika status = 'ditolak' ATAU expired_at sudah lewat
   - Implementasi menggunakan trait MassPrunable pada Model PengajuanBantuan

3. **Modul SPK TOPSIS**
   - Admin menetapkan bobot dan tipe (benefit/cost) untuk setiap kriteria
   - Kriteria bersifat dinamis (dapat ditambah/ubah/hapus)
   - Admin memberikan nilai penilaian per kriteria untuk pengajuan yang lolos verifikasi
   - Sistem menghitung perangkingan TOPSIS otomatis via TopsisCalculationService.php
   - Hasil ranking ditampilkan per kategori jenis Alsintan

4. **Modul Verifikasi & Persetujuan (ACC)**
   - Admin memverifikasi kelengkapan berkas (setujui/tolak/minta revisi)
   - Kepala Bidang melihat ranking TOPSIS dan memberikan keputusan akhir

5. **Modul Laporan**
   - Ekspor laporan hasil seleksi ke format PDF

6. **Manajemen 3 Role:** kelompok_tani | admin | kepala_bidang

### OUT-OF-SCOPE (DILARANG)

| Fitur Terlarang | Alasan |
|---|---|
| Chat real-time / WebSocket | Scope creep, butuh infrastruktur tambahan |
| Marketplace hasil panen | Menyimpang dari judul TA |
| IoT / sensor traktor | Di luar domain web-based SPK |
| Payment gateway | Tidak relevan |
| Mobile App native | Di luar stack yang ditetapkan |
| Machine Learning / AI prediktif | TOPSIS sudah cukup |
| Multi-tenancy | Hanya untuk Dinas Kab. Subang |
| GPS tracking alat | Di luar ruang lingkup |

---

## 3. AKTOR SISTEM & HAK AKSES

```
┌─────────────────┬──────────────────────┬────────────────────────┐
│ Kelompok Tani   │  Admin / Verifikator │  Kepala Bidang         │
│ (Pengaju)       │  Dinas               │  (Approver Final)      │
├─────────────────┼──────────────────────┼────────────────────────┤
│ Daftar/Login    │ Login                │ Login                  │
│ Isi formulir    │ Lihat semua pengajuan│ Lihat ranking TOPSIS   │
│ Upload PDF      │ Verifikasi berkas    │ ACC / Tolak pengajuan  │
│ Pantau status   │ Input nilai TOPSIS   │ Cetak laporan          │
│                 │ Kelola kriteria+bobot│ Lihat statistik        │
│                 │ Jalankan kalkulasi   │                        │
│                 │ Kelola user Poktan   │                        │
└─────────────────┴──────────────────────┴────────────────────────┘
```

---

## 4. ATURAN BISNIS KRITIS

### 4.1 Auto-Expired & File Pruning Policy (Kebijakan Retensi Berkas)
```
ATURAN MASA RETENSI:
1. Usulan Baru (Draft/Diajukan) : expired_at = created_at + 90 hari
2. Usulan Ditolak (Admin/Kabid) : expired_at = approved_at/verified_at + 90 hari
3. Usulan Disetujui (ACC Bantuan): expired_at = approved_at + 13 BULAN (395 hari)
   * Alasan Akademis 13 Bulan: 1 Tahun Anggaran (12 bulan) + 1 Bulan Masa Tutup Buku Rekapitulasi Audit LPJ Tahunan Dinas Pertanian di bulan Januari tahun berikutnya.

EKSEKUSI AUTO-PRUNING:
JIKA (pengajuan.expired_at < NOW()) DAN (pengajuan.files_pruned_at IS NULL)
MAKA:
  1. Hapus semua file PDF fisik lampiran di storage/app/private/pengajuan/{id}/
  2. Set pengajuan.files_pruned_at = NOW()
  3. Catat log ke storage/logs/pruning.log
  4. RECORD DI DATABASE TETAP DIPERTAHANKAN (TIDAK BOLEH DIHAPUS)
     agar data teks, skor TOPSIS Vi, dan rekapitulasi audit tahunan tetap utuh!

Jadwal: Setiap hari pukul 01:00 WIB (via command: php artisan spk:prune-files)
```

### 4.2 State Machine Pengajuan
```
[draft] -> [diajukan] -> [diverifikasi] -> [dinilai] -> [dirangking]
                    \-> [ditolak_admin]
                                      \-> [ditolak_kabid]
                                                        \-> [disetujui_kabid]
```

### 4.3 Aturan TOPSIS
- Dihitung per batch per kategori jenis Alsintan
- Bobot semua kriteria harus berjumlah = 1.0
- Hanya pengajuan berstatus 'diverifikasi' yang masuk kalkulasi

### 4.4 Kebijakan Kuota Alokasi Alsintan (Dynamic Inventory Allocation)
1. **Penyimpanan Kuota:** Atribut `kuota_unit` disimpan langsung pada tabel `jenis_alsintans` (tidak menambah tabel baru sehingga menjaga batas 9 tabel KKNI Level 5).
2. **Perhitungan Real-Time:**
   - $\text{Tersalurkan} = \sum \text{jumlah\_diminta}$ untuk seluruh usulan berstatus `disetujui_kabid`.
   - $\text{Sisa Kuota} = \max(0, \text{kuota\_unit} - \text{Tersalurkan})$.
3. **Otoritas Admin Dinas:** Admin dapat memperbarui `kuota_unit` secara real-time via Dashboard Admin ketika Dinas Pertanian menerima drop tambahan bantuan unit dari APBD Perubahan atau Kementerian Pertanian (Kementan).
4. **Safety Guard Kepala Bidang:** Kepala Bidang hanya dapat menyetujui (ACC) usulan jika $\text{Sisa Kuota} \ge \text{jumlah\_diminta}$. Jika kuota telah mencapai 0, tombol ACC otomatis terkunci dan sistem menolak tindakan penandatanganan dengan pesan peringatan edukatif. Begitu Admin menaikkan kuota (misal $+2$ unit), slot ACC terbuka kembali secara instan.

---

## 5. STRUKTUR DIREKTORI PROYEK LARAVEL 11

```
pa-spk-alsintan/
├── app/
│   ├── Console/
│   │   └── Kernel.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/AuthController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── PengajuanController.php   <- Verifikasi pengajuan
│   │   │   │   ├── KriteriaController.php    <- CRUD kriteria & bobot
│   │   │   │   ├── PenilaianController.php   <- Input nilai per kriteria
│   │   │   │   ├── TopsisController.php      <- Trigger kalkulasi TOPSIS
│   │   │   │   ├── LaporanController.php
│   │   │   │   └── UserController.php
│   │   │   ├── KepBid/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── RankingController.php     <- Lihat & ACC hasil ranking
│   │   │   │   └── LaporanController.php
│   │   │   └── Poktan/
│   │   │       ├── DashboardController.php
│   │   │       └── PengajuanController.php   <- Submit + upload
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── StorePengajuanRequest.php
│   │       └── StoreKriteriaRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── KelompokTani.php
│   │   ├── JenisAlsintan.php
│   │   ├── Kriteria.php
│   │   ├── PengajuanBantuan.php              <- MassPrunable + expired_at
│   │   ├── BerkasPengajuan.php
│   │   └── PenilaianTopsis.php
│   ├── Services/
│   │   └── TopsisCalculationService.php      <- INTI: 6 Langkah TOPSIS
│   └── Providers/AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_kelompok_tanis_table.php
│   │   ├── ..._create_jenis_alsintans_table.php
│   │   ├── ..._create_kriterias_table.php
│   │   ├── ..._create_pengajuan_bantuans_table.php
│   │   ├── ..._create_berkas_pengajuans_table.php
│   │   └── ..._create_penilaian_topsis_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── JenisAlsintanSeeder.php
│       └── KriteriaSeeder.php
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── auth.blade.php
│   ├── admin/
│   ├── kepbid/
│   └── poktan/
├── routes/
│   └── web.php
├── storage/app/private/pengajuan/{id}/       <- File PDF tersimpan
├── PROJECT_BLUEPRINT.md
├── TOPSIS_FORMULA_SPEC.md
└── PROGRESS_TRACKER.md
```

---

## 6. SKEMA RELASI DATABASE (10 TABEL UTAMA — STANDAR KKNI LEVEL 5)

Sesuai dengan **Standar KKNI Level 5 (Program D3 Sistem Informasi)** yang mensyaratkan skema basis data berkisar antara **8 hingga 12 tabel yang saling berelasi**, arsitektur sistem ini mengimplementasikan **10 tabel utama berelasi**:

```
[kecamatans] (1) ─────────── (N) [kelompok_tanis] (1) ──── (1) [users]
                                        │ (1)                 │ (1)
                                        │ (N)                 │ (N)
[periode_bantuans] (1) ──── (N) [jenis_alsintans] (1) ── (N) [pengajuan_bantuans]   [ulasan_layanans] (Tabel ke-10)
                                                                │ (1)       │ (1)         ▲ (dimoderasi kepbid)
                                 ┌──────────────────────────────┤           │ (N)         │
                                 │ (N)                          │ (N)       ▼             │
                       [penilaian_topsis]             [berkas_pengajuans] [riwayat_status_pengajuans]
                                 ▲
                                 │ (N)
                            [kriterias]
```

### Rincian 10 Tabel Relasional:
1. **`users`**: Entitas aktor sistem (Admin Verifikator Dinas, Kepala Bidang, Kelompok Tani).
2. **`kelompok_tanis`**: Profil kelembagaan tani, legalitas SK badan hukum, luas hamparan, dan jumlah anggota.
3. **`kecamatans`** *(Tabel ke-7)*: Master wilayah 30 kecamatan di Kabupaten Subang (terbagi dalam zona Pantura, Tengah, dan Selatan). Berelasi One-to-Many ke `kelompok_tanis`.
4. **`periode_bantuans`** *(Tabel ke-8)*: Master gelombang tahun anggaran APBD/APBN alsintan beserta kuota dan masa aktif. Berelasi One-to-Many ke `jenis_alsintans`.
5. **`jenis_alsintans`**: Master kategori mesin pertanian (Traktor R2, Traktor R4, Pompa, Sprayer, Transplanter, Combine Harvester). Berelasi ke `periode_bantuans`.
6. **`pengajuan_bantuans`**: Transaksi permohonan hibah alsintan dari Poktan, memuat status alur dan masa retensi berkas (`expired_at`).
7. **`riwayat_status_pengajuans`** *(Tabel ke-9)*: Log audit trail jejak perubahan status pengajuan yang dicatat otomatis via Model Observer.
8. **`kriterias`**: Parameter penilaian SPK TOPSIS (Luas Lahan, Produktivitas, Kesiapan Poktan, dll.) dengan tipe Benefit/Cost dan bobot dinamis.
9. **`penilaian_topsis`**: Tabel pivot penilaian alternatif ($x_{ij}$) yang menghubungkan setiap pengajuan bantuan dengan kriteria TOPSIS.
10. **`ulasan_layanans`** *(Tabel ke-10)*: Survei Kepuasan Masyarakat (IKM) berdasar Permenpan-RB No. 14/2017. Berisi rating bintang 1-5 dan ulasan poktan, dengan moderasi persetujuan oleh Kepala Bidang sebelum ditampilkan ke Landing Page publik.

*Tabel Penunjang:*
- `hasil_topsis`: Menyimpan nilai jarak Euclidean ($D^+$, $D^-$), nilai preferensi ($V_i$), dan ranking final hasil langkah 5 & 6.
- `berkas_pengajuans`: Menyimpan path penyimpanan dokumen PDF proposal dan surat permohonan.

---

## 7. TEKNOLOGI & VERSI

| Teknologi | Versi | Keterangan |
|---|---|---|
| PHP | 8.5 | Backend |
| Laravel | 11.x | Framework |
| TailwindCSS | 3.x / Play | Styling & Bespoke Editorial Architecture |
| Google Stitch | Design System | AgroSubang Executive & Golden Ratio Editorial |
| MySQL | 8.x | Database |
| DomPDF (barryvdh) | - | Ekspor PDF |
| Laravel Scheduler | - | Auto-pruning |

---

## 8. PERINGATAN TEKNIS

> CAUTION: Jika muncul keinginan menambah fitur di luar Section 2 IN-SCOPE,
> konsultasikan dulu ke pembimbing. Penambahan fitur mendekati deadline
> adalah penyebab utama PA tidak selesai tepat waktu.

> TIP: Prioritas pengerjaan: jalur kritis terlebih dahulu:
> Pengajuan -> Verifikasi -> Penilaian -> TOPSIS -> ACC
> Fitur laporan & dashboard statistik dikerjakan terakhir.

