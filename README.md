# Sistem Pendukung Keputusan (SPK) Penyaluran Bantuan Alsintan
### Dinas Pertanian Pemerintah Kabupaten Subang
**Berbasis Metode TOPSIS (*Technique for Order of Preference by Similarity to Ideal Solution*)**

---

## 📌 Tentang Proyek

Aplikasi **SPK Bantuan Alsintan** adalah sistem informasi berbasis web yang dikembangkan untuk mengotomatisasi proses seleksi dan perankingan penerima bantuan Alat dan Mesin Pertanian (Alsintan) pada Dinas Pertanian Kabupaten Subang. Sistem ini menerapkan metode **TOPSIS** secara objektif dan matematis guna meminimalisasi subjektivitas, mencegah penyaluran bantuan yang tidak tepat sasaran, serta memenuhi standar akuntabilitas kearsipan pemerintah (ANRI dan audit BPKP/Inspektorat Daerah).

---

## 👥 Hak Akses & Peran Pengguna (3-Role System)

Sistem menerapkan isolasi wewenang ketat berbasis *Role-Based Access Control* (RBAC) dan *Policy* objek otorisasi (*Anti-IDOR*):

1. **Kelompok Tani (Poktan):**
   - Registrasi akun mandiri dan pengelolaan profil legalitas poktan.
   - Pemutakhiran data luas hamparan sawah binaan (Ha) dan jumlah anggota aktif.
   - Pengajuan proposal permohonan alsintan secara daring beserta lampiran berkas PDF.
   - Pemantauan status permohonan secara waktu nyata (*realtime status tracking*).

2. **Admin Verifikator Dinas:**
   - Manajemen master jenis alsintan, kode alat, dan alokasi kuota bantuan fisik tahunan.
   - Verifikasi administratif kelengkapan dan keabsahan berkas proposal pemohon di *private storage*.
   - Input nilai parameter evaluasi untuk 5 kriteria standar dinas (C1 sampai C5).
   - Eksekusi komputasi 6 langkah algoritma SPK TOPSIS secara terotomatisasi.
   - Manajemen akun pengguna (pembekuan akun dan bantuan reset kata sandi petani senior).

3. **Kepala Bidang (Approver / Pengambil Kebijakan):**
   - Meninjau antrean rekomendasi usulan hasil perankingan TOPSIS.
   - Memberikan keputusan final: **Persetujuan (ACC)** yang otomatis memotong sisa kuota unit alat, atau **Penolakan** disertai catatan pertimbangan kedinasan.
   - Pencetakan rekapitulasi laporan penetapan penerima bantuan dalam format ramah cetak (*print-friendly view*).
   - Pemantauan efisiensi penyimpanan server dan eksekusi retensi berkas fisik (*Auto-Pruning*).

---

## 🧮 Model Keputusan: Kriteria Evaluasi TOPSIS

Penilaian alternatif usulan didasarkan pada 5 kriteria resmi Dinas Pertanian Kabupaten Subang:

| Kode | Nama Kriteria | Sifat Kriteria | Bobot ($w_j$) | Keterangan |
|:---:|:---|:---:|:---:|:---|
| **C1** | Luas Hamparan Sawah Poktan | *Benefit* | **25% (0.25)** | Makin luas hamparan lahan, makin tinggi prioritas kebutuhan mekanisasi. |
| **C2** | Jumlah Anggota Poktan Aktif | *Benefit* | **20% (0.20)** | Menilai dampak pemanfaatan alat terhadap jumlah petani binaan. |
| **C3** | Tingkat Produktivitas Lahan (Ton/Ha) | *Benefit* | **20% (0.20)** | Mengukur potensi surplus gabah dan efektivitas hasil panen. |
| **C4** | Kesiapan Operator & Bengkel | *Benefit* | **20% (0.20)** | Menilai kapasitas pemeliharaan unit agar alsintan tidak terbengkalai. |
| **C5** | Jumlah Alsintan Eksisting yang Dimiliki | *Cost* | **15% (0.15)** | Menjamin pemerataan; poktan dengan alsintan sejenis lebih sedikit diprioritaskan. |

---

## 🛠️ Tumpukan Teknologi (Technology Stack)

* **Backend Framework:** Laravel 11 (PHP 8.2+)
* **Basis Data Relasional:** MySQL 8.0+ (Skema 9 Tabel Relasional 3NF)
* **Frontend:** Tailwind CSS (Palet Resmi *Harvesta Agrarian*), Alpine.js, Laravel Blade Components
* **Keamanan Aplikasi:**
  - *Anti-IDOR Object Authorization Policy* (`PengajuanBantuanPolicy`)
  - *Hardening Security Headers* (`SecurityHeadersMiddleware`)
  - *Private Encrypted Storage Driver* untuk dokumen proposal PDF
  - *Anti-Brute Force Rate Limiting* & Kebijakan Kata Sandi Kuat
* **Pengujian Otomatis:** PHPUnit 11 (21 Uji Skenario, 267 Assertions, **100% PASS**)

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
* PHP $\ge$ 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`)
* Composer $\ge$ 2.x
* MySQL Server $\ge$ 8.0
* Git CLI

### 2. Kloning Repositori & Instalasi Dependensi
```bash
git clone https://github.com/AlfathNH/PA_Project.git
cd PA_Project
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
Salin berkas konfigurasi contoh dan buat *Application Key*:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan parameter basis data pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_spk_alsintan
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Basis Data & Pemuatan Data Awal (Seeding)
```bash
php artisan migrate --seed
```

### 5. Konfigurasi Storage & Eksekusi Server
```bash
php artisan storage:link
php artisan serve
```
Akses aplikasi melalui peramban pada alamat: `http://localhost:8000`.

---

## 🔑 Akun Uji Coba Bawaan Sistem (Demo Credentials)

| Peran | Alamat Email | Kata Sandi | Wewenang Utama |
|:---|:---|:---|:---|
| **Admin Verifikator** | `admin@subang.go.id` | `Subang2026!` | Verifikasi Berkas, Input Kriteria, Eksekusi TOPSIS |
| **Kepala Bidang** | `kabid@subang.go.id` | `Subang2026!` | Persetujuan ACC, Penolakan Usulan, Cetak Rekap |
| **Kelompok Tani** | `srimukti@poktan.id` | `Subang2026!` | Pengajuan Permohonan, Upload PDF, Pantau Status |

---

## 🧪 Eksekusi Pengujian Otomatis

Jalankan pengujian unit matematis TOPSIS dan fitur retensi berkas:
```bash
php artisan test
```
Jalankan audit keamanan independen (22 indikator AppSec):
```bash
php test_security_audit.php
```

---

## 📄 Lisensi & Hak Cipta
&copy; 2026 **Dinas Pertanian Pemerintah Kabupaten Subang**.
Dikembangkan untuk memenuhi syarat kelulusan Proyek Akhir Program Diploma Tiga (D3) Sistem Informasi.
*Karya Utama Satya Nagara — Sinergi Menuju Kedaulatan Pangan Berkelanjutan.*
