# Arsitektur dan Struktur Direktori Proyek
## Sistem Pendukung Keputusan Bantuan Alsintan Dinas Pertanian Kabupaten Subang
**Bahan Penyusunan Laporan Proyek Akhir: Bab III (Perancangan) dan Bab IV (Implementasi Sistem)**

---

### 1. Ringkasan Pola Arsitektur Sistem

Aplikasi Sistem Pendukung Keputusan (SPK) Penyaluran Bantuan Alat dan Mesin Pertanian (Alsintan) dibangun di atas kerangka kerja **Laravel 11** berbasis PHP 8.2+. Sistem ini menerapkan pola arsitektur **Model-View-Controller (MVC)** yang disempurnakan dengan **Service Layer Pattern** untuk memisahkan logika matematis kompleks dari lapisan pengendali (*controller*), serta **Policy-Based Authorization** untuk menjamin keamanan akses objek dokumen (*anti-IDOR*).

Prinsip arsitektur yang diterapkan meliputi:
1. **Pemisahan Tanggung Jawab (Separation of Concerns):** Logika kalkulasi matriks 6 langkah TOPSIS diisolasi sepenuhnya di dalam `app/Services/TopsisCalculationService.php`, sehingga controller hanya bertindak sebagai pengatur lalu lintas permintaan HTTP dan penyuplai data ke tampilan.
2. **Keamanan Berlapis (Defense in Depth):** Perlindungan rute dilindungi oleh `RoleMiddleware`, proteksi berkas melalui *Private Storage Driver*, dan proteksi peramban menggunakan `SecurityHeadersMiddleware`.
3. **Penyimpanan Berkelanjutan (Storage Retention & Pruning):** Menerapkan kontrak `MassPrunable` pada model Eloquent untuk membersihkan berkas usulan kedaluwarsa secara otomatis melalui scheduler berkala.
4. **Antarmuka Responsif Ringan (Mobile-First Blade & TailwindCSS):** Seluruh antarmuka dirancang menggunakan TailwindCSS dengan palet resmi *Harvesta Agrarian* Dinas Pertanian Kabupaten Subang, bebas ketergantungan JavaScript berat, serta aman dari *horizontal overflow* di perangkat seluler.

---

### 2. Pohon Struktur Direktori Proyek (Directory Tree)

Berikut adalah struktur hirarki berkas dan direktori utama dari aplikasi SPK Bantuan Alsintan:

```text
pa-spk-alsintan/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── PruneExpiredFilesCommand.php       # Perintah Artisan pembersihan berkas fisik kedaluwarsa
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php        # Ringkasan statistik, CRUD master alsintan, dan alokasi kuota
│   │   │   │   ├── PengajuanController.php        # Verifikasi proposal, berkas PDF, dan input nilai kriteria
│   │   │   │   ├── TopsisController.php           # Eksekusi kalkulasi TOPSIS, tabel matriks, dan pemeringkatan
│   │   │   │   └── UserController.php             # Manajemen akun, aktivasi/pembekuan, dan reset kata sandi
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php             # Registrasi mandiri poktan, login, logout, dan pengalihan peran
│   │   │   ├── KepBid/
│   │   │   │   └── DashboardController.php        # Persetujuan (ACC), penolakan, cetak rekapitulasi, dan retensi
│   │   │   ├── Poktan/
│   │   │   │   └── DashboardController.php        # Pengajuan alat, unggah/reset PDF, kelola profil 30 kec, pantau status
│   │   │   ├── Controller.php                     # Base Controller induk Laravel
│   │   │   └── UlasanLayananController.php        # Simpan survei IKM poktan (opsional) & moderasi kurasi KepBid
│   │   └── Middleware/
│   │       ├── PreventRequestForgeryMiddleware.php # Pengaman CSRF sesi HttpOnly tanpa cookie XSRF-TOKEN non-HttpOnly
│   │       ├── RoleMiddleware.php                 # Proteksi akses rute berdasarkan peran pengguna
│   │       └── SecurityHeadersMiddleware.php      # Hardening HTTP Headers modern (CSP, HSTS, Anti-Scraping, No-Store)
│   ├── Models/
│   │   ├── BerkasPengajuan.php                    # Metadata berkas proposal PDF dan path privat storage
│   │   ├── HasilTopsis.php                        # Nilai preferensi Vi, jarak D+, D-, dan peringkat rekomendasi
│   │   ├── JenisAlsintan.php                      # Master kategori mesin alsintan dan sisa kuota fisik
│   │   ├── Kecamatan.php                          # Master 30 kecamatan di Kab. Subang dan zonasi pertanian
│   │   ├── KelompokTani.php                       # Profil legalitas poktan, luas hamparan (Ha), dan kontak ketua
│   │   ├── Kriteria.php                           # Master kriteria TOPSIS, bobot matematis, dan tipe benefit/cost
│   │   ├── PengajuanBantuan.php                   # Entitas utama usulan bantuan + trait MassPrunable 90 hari
│   │   ├── PenilaianTopsis.php                    # Nilai x_ij matriks keputusan per pengajuan per kriteria
│   │   ├── PeriodeBantuan.php                     # Master periode tahun anggaran dan kuota alokasi dinas
│   │   ├── RiwayatStatusPengajuan.php             # Audit trail kronologis perubahan status usulan (Observer)
│   │   ├── UlasanLayanan.php                      # Entitas Tabel ke-10: Rating & ulasan kepuasan (IKM) terkurasi
│   │   └── User.php                               # Data akun login, peran otoritas, status aktif, dan relasi poktan
│   ├── Policies/
│   │   └── PengajuanBantuanPolicy.php             # Otorisasi anti-IDOR untuk akses berkas dan data usulan
│   ├── Providers/
│   │   └── AppServiceProvider.php                 # Pendaftaran policy, registrasi custom view pagination default
│   └── Services/
│       └── TopsisCalculationService.php           # Mesin inti 6 langkah kalkulasi matematis TOPSIS
├── bootstrap/
│   └── app.php                                    # Konfigurasi middleware, exception handling, dan routing v11
├── config/
│   ├── app.php                                    # Konfigurasi locale Bahasa Indonesia (id), zona waktu WIB
│   ├── auth.php                                   # Konfigurasi guard autentikasi dan model User
│   ├── database.php                               # Konfigurasi koneksi MySQL db_spk_alsintan
│   └── filesystems.php                            # Konfigurasi Private Storage Driver (storage/app/private)
├── database/
│   ├── factories/                                 # Factory data pengujian otomatis
│   ├── migrations/                                # 15 berkas migrasi skema 10 tabel basis data relasional (+ penunjang)
│   └── seeders/
│       ├── DatabaseSeeder.php                     # Master seeder orkestrasi pemanggilan seluruh seeder
│       ├── JenisAlsintanSeeder.php                # Data awal mesin alsintan (Traktor, Pompa Air, Combine Harvester)
│       ├── KecamatanSeeder.php                    # Data 30 kecamatan di Subang (Pantura, Tengah, Selatan)
│       ├── KriteriaSeeder.php                     # 5 kriteria standar dinas (C1 sampai C5) beserta bobot resmi
│       ├── PengajuanDanPenilaianSeeder.php        # Data usulan percontohan dan nilai matriks awal
│       ├── PeriodeBantuanSeeder.php               # Data periode tahun anggaran berjalan 2026
│       ├── StressTestSeeder.php                   # Seeder massal 1.000 poktan & pengajuan untuk uji beban
│       └── UserSeeder.php                         # Akun bawaan Admin, Kepala Bidang, dan Kelompok Tani
├── lang/
│   ├── en/
│   │   └── pagination.php                         # Terjemahan cadangan bahasa Inggris
│   └── id/
│       └── pagination.php                         # Terjemahan resmi navigasi paginasi Bahasa Indonesia baku
├── public/
│   ├── css/                                       # Stylesheet terkompilasi lokal (tailwind.min.css, fonts.css)
│   ├── fonts/                                     # Font WOFF2 self-hosted (Urbanist, Newsreader, Material Symbols)
│   ├── images/                                    # Aset statis logo Pemkab Subang, foto hero lokal, & emblem resmi
│   ├── index.php                                  # Pintu masuk utama eksekusi web server
│   └── robots.txt                                 # Pembatasan perayapan AI Scraper & proteksi endpoint privat
├── resources/
│   ├── css/
│   │   └── app.css                                # Konfigurasi gaya CSS tambahan dan animasi
│   ├── js/
│   │   └── app.js                                 # Bootstrap skrip frontend JavaScript
│   └── views/
│       ├── admin/
│       │   ├── dashboard.blade.php                # Dashboard Admin, manajemen alsintan, dan antrean usulan
│       │   ├── pengajuan/
│       │   │   ├── index.blade.php                # Tabel verifikasi berkas dan penyaringan status permohonan
│       │   │   └── penilaian.blade.php            # Formulir input nilai parameter kriteria (C1-C5)
│       │   ├── topsis/
│       │   │   └── index.blade.php                # Hasil kalkulasi SPK TOPSIS, matriks 1-5, dan tabel ranking
│       │   └── users/
│       │       └── index.blade.php                # Panel manajemen akun pengguna, reset sandi, dan pembekuan
│       ├── auth/
│       │   ├── forgot-password.blade.php          # Formulir bantuan kendala kata sandi
│       │   ├── login.blade.php                    # Halaman masuk portal resmi 3 role
│       │   └── register.blade.php                 # Formulir pendaftaran mandiri kelompok tani baru
│       ├── kepbid/
│       │   ├── dashboard.blade.php                # Dashboard Kepala Bidang (Antrean Keputusan ACC/Tolak)
│       │   ├── rekap_cetak.blade.php              # Template cetak laporan resmi penetapan penerima bantuan
│       │   ├── riwayat.blade.php                  # Arsip riwayat usulan yang telah diputuskan pimpinan
│       │   └── ulasan.blade.php                   # Halaman khusus Survei Kepuasan (IKM) & Kurasi Ulasan Beranda
│       ├── layouts/
│       │   └── app.blade.php                      # Kerangka induk layout, floating pill bar, swipe toast 30s, loader
│       ├── poktan/
│       │   └── dashboard.blade.php                # Portal Poktan (Profil hamparan, form permohonan, status alat)
│       ├── vendor/
│       │   └── pagination/
│       │       └── custom-tailwind.blade.php      # Template paginasi kustom responsif + fitur Jump to Page
│       ├── landing.blade.php                      # Halaman profil informasi publik bantuan alsintan & banner cookie UU PDP
│       ├── privacy.blade.php                      # Halaman Kebijakan Privasi, Retensi Data, dan Transparansi Cookie
│       └── welcome.blade.php                      # Halaman perantara selamat datang
├── routes/
│   ├── console.php                                # Penjadwalan perintah berkala (Auto-pruning harian 01:00 WIB)
│   └── web.php                                    # Definisi seluruh endpoint URL sistem dan pengelompokan grup peran
├── storage/
│   ├── app/
│   │   └── private/                               # Penyimpanan terisolasi dokumen proposal PDF pemohon
│   ├── framework/                                 # Berkas cache framework, sesi login, dan kompilasi blade
│   └── logs/                                      # Catatan log aktivitas dan galat sistem
├── tests/
│   ├── Feature/
│   │   ├── AutoPruning90HariTest.php              # Pengujian otomatis fitur retensi dan pembersihan berkas fisik
│   │   ├── ExampleTest.php                        # Uji integrasi dasar HTTP
│   │   ├── SecurityHardeningTest.php              # Uji HTTP headers (CSP, HSTS, X-Robots-Tag) & penghapusan XSRF-TOKEN
│   │   └── UlasanLayananTest.php                  # Uji alur survei IKM poktan, moderasi KepBid, & batas 3 beranda
│   ├── Unit/
│   │   ├── ExampleTest.php                        # Uji unit dasar
│   │   └── TopsisCalculationServiceTest.php       # Uji matematis TOPSIS (6 langkah + aturan adaptif calon tunggal m=1)
│   └── TestCase.php                               # Kelas dasar pengujian Laravel
├── .env.example                                   # Contoh konfigurasi variabel lingkungan sistem
├── composer.json                                  # Manajemen dependensi pustaka PHP
├── package.json                                   # Manajemen dependensi pustaka aset frontend
├── phpunit.xml                                    # Konfigurasi parameter eksekusi pengujian PHPUnit
├── tailwind.config.js                             # Konfigurasi palet warna resmi Harvesta Agrarian dan breakpoint
└── test_security_audit.php                        # Skrip audit keamanan 22 parameter AppSec & OWASP Top 10
```

---

### 3. Penjelasan Rinci Komponen Utama Sistem

#### A. Direktori `app/Services/`
Direktori ini menampung logika bisnis utama yang berdiri sendiri:
* **`TopsisCalculationService.php`**: Berisi implementasi murni algoritma TOPSIS. Layanan ini menerima koleksi alternatif usulan bantuan dan kriteria aktif, lalu memprosesnya melalui 6 langkah: pembentukan matriks keputusan asli ($X$), normalisasi matriks ($R$), normalisasi terbobot ($Y$), penetapan solusi ideal positif ($A^+$) dan negatif ($A^-$), kalkulasi jarak Euclidean ($D^+$ dan $D^-$), hingga perolehan nilai preferensi ($V_i$) serta penentuan peringkat. Layanan ini dilengkapi *guard* matematis pencegah galat pembagian dengan nol (*division by zero*).

#### B. Direktori `app/Http/Controllers/`
Mengatur alur permintaan HTTP berdasarkan isolasi wilayah wewenang:
* **Sub-direktori `Admin/`**: Menangani fungsi operasional dinas, mulai dari pemantauan kuota alsintan, verifikasi kelayakan proposal pemohon, pembacaan dokumen PDF di private storage, pengisian nilai kriteria, pemicu kalkulasi TOPSIS, hingga manajemen akun dan reset kredensial pengguna.
* **Sub-direktori `KepBid/`**: Menangani fungsi pengambil kebijakan, meliputi persetujuan usulan (*ACC*) yang secara otomatis memotong stok kuota fisik alat, penolakan usulan dengan catatan resmi pimpinan, penyediaan tampilan cetak rekapitulasi bantuan, serta pemantauan efisiensi ruang penyimpanan data.
* **Sub-direktori `Poktan/`**: Menangani interaksi mandiri kelompok tani, mencakup pembaharuan data profil hamparan sawah dan kontak pengurus, pembuatan permohonan bantuan mesin pertanian baru, unggah berkas proposal PDF, serta pemantauan tahapan status usulan.
* **Sub-direktori `Auth/`**: Mengatur proses otentikasi aman, pendaftaran mandiri kelompok tani dengan validasi legalitas poktan, serta proteksi sesi login.

#### C. Direktori `app/Http/Middleware/`
Menyediakan lapisan perlindungan gerbang sebelum permintaan masuk ke controller:
* **`RoleMiddleware.php`**: Memeriksa apakah pengguna yang telah terautentikasi memiliki atribut peran (*role*) yang sesuai dengan rute yang dituju. Mencegah kelompok tani mengakses halaman admin atau kepala bidang, dan sebaliknya.
* **`SecurityHeadersMiddleware.php`**: Menyisipkan header keamanan modern standar OWASP & PCI DSS pada setiap respons HTTP, meliputi `Content-Security-Policy` (CSP), `Strict-Transport-Security` (HSTS), `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy`, `X-Robots-Tag`, penghapusan header usang `X-XSS-Protection` dan `X-Powered-By`, serta kontrol `Cache-Control: no-store` untuk rute privat.
* **`PreventRequestForgeryMiddleware.php`**: Mengamankan formulir terhadap serangan CSRF menggunakan token terenkripsi pada sesi `HttpOnly` serta menonaktifkan penerbitan cookie `XSRF-TOKEN` non-HttpOnly.

#### D. Direktori `app/Models/`
Menghubungkan aplikasi dengan tabel basis data melalui Eloquent ORM:
* **`PengajuanBantuan.php`**: Model inti yang merepresentasikan transaksi permohonan bantuan. Mengimplementasikan antarmuka `Illuminate\Database\Eloquent\MassPrunable` untuk memilih data usulan kedaluwarsa secara otomatis dan memicu penghapusan berkas fisik di disk penyimpanan.
* **`RiwayatStatusPengajuan.php`**: Mencatat setiap perubahan status usulan secara otomatis melalui *Eloquent Observer* pada saat event `PengajuanBantuan::booted()` terpanggil, menghasilkan jejak audit (*audit trail*) yang tidak dapat dimanipulasi.

#### E. Direktori `app/Policies/`
* **`PengajuanBantuanPolicy.php`**: Memvalidasi kepemilikan data saat pengguna berupaya melihat detail pengajuan atau mengunduh berkas fisik proposal PDF. Memastikan pengguna kelompok tani A tidak dapat membuka atau mengunduh proposal milik kelompok tani B (*anti-IDOR protection*).

#### F. Direktori `resources/views/`
Mengorganisasi tata letak antarmuka pengguna berbasis komponen Blade:
* **`layouts/app.blade.php`**: Berkas induk layout yang menyatukan navigasi atas kedinasan, floating pill menu untuk perangkat desktop saat digulir, navigasi melengkung modern untuk perangkat seluler di bagian bawah layar (*mobile bottom navigation*), serta pemantau pemuatan data global (*lightweight CSS/SVG loader*).
* **`vendor/pagination/custom-tailwind.blade.php`**: Komponen paginasi kustom yang dirancang hemat ruang, menyajikan navigasi ringkas untuk ponsel agar bebas dari pergeseran horizontal (*anti-overflow*), serta dilengkapi formulir pencarian nomor halaman tujuan secara langsung (*Jump to Page*).

#### G. Direktori `tests/`
Menyimpan skenario pengujian otomatis untuk memastikan keandalan sistem:
* **`tests/Unit/TopsisCalculationServiceTest.php`**: Memuat 10 skenario uji matematis presisi tinggi yang membandingkan keluaran desimal sistem dengan hasil perhitungan acuan Microsoft Excel pada Bab IV.
* **`tests/Feature/AutoPruning90HariTest.php`**: Memuat 9 skenario uji integrasi alur penghapusan berkas kedaluwarsa, pengujian hak akses pemicu pembersihan, dan verifikasi keamanan metadata pasca-pembersihan.
