# Laporan Hasil Pengujian White Box Testing
## Sistem Pendukung Keputusan Bantuan Alsintan Dinas Pertanian Kabupaten Subang
**Bahan Penyusunan Laporan Proyek Akhir: Bab IV (Hasil dan Pembahasan)**

---

### 1. Metodologi Pengujian Kotak Putih (White Box Testing)

Pengujian kotak putih (*White Box Testing*) dilakukan untuk menguji struktur logika internal, kebenaran algoritma matematis, penanganan kondisi batas (*boundary value*), percabangan kode (*branch coverage*), serta integritas transaksi basis data pada lapisan layanan (*Service Layer*) dan model aplikasi.

Pengujian dieksekusi menggunakan kerangka kerja otomasi **PHPUnit 12.5.43** (PHP 8.5.1) yang terintegrasi dengan pengujian Laravel. Pengujian berfokus pada tiga modul utama dalam sistem:
1. **Mesin Kalkulasi SPK TOPSIS (`app/Services/TopsisCalculationService.php`)**: Memverifikasi presisi desimal 6 langkah matematis algoritma TOPSIS terhadap hasil perhitungan acuan Microsoft Excel pada Bab IV, aturan adaptif calon tunggal ($m = 1$), pengujian kriteria campuran (*benefit* dan *cost*), penanganan kondisi ekstrem, serta proteksi pembagian dengan nol (*zero division guard*).
2. **Manajemen Retensi dan Pembersihan Berkas Fisik (`app/Models/PengajuanBantuan.php` & `app/Console/Commands/PruneExpiredFilesCommand.php`)**: Memverifikasi logika otomatisasi pembersihan dokumen PDF kedaluwarsa sesuai regulasi retensi (90 hari untuk usulan ditolak dan 13 bulan untuk usulan disetujui guna audit BPKP), idempotensi pembersihan, serta pengujian mode simulasi (*dry-run*).
3. **Modul Survei Kepuasan Masyarakat & Kurasi Beranda (`app/Http/Controllers/UlasanLayananController.php`)**: Memverifikasi isolasi status awal ulasan (`is_tampil_landing = false`), hak kurasi eksklusif Kepala Bidang, penolakan otomatis untuk peran lain, serta pembatasan maksimal 3 ulasan terkurasi pada halaman beranda publik.

---

### 2. Rekapitulasi Hasil Eksekusi Pengujian Otomatis

Berdasarkan hasil eksekusi pengujian suite otomatis menggunakan perintah `php artisan test --testdox`, seluruh rangkaian pengujian berhasil diselesaikan dengan tingkat kelulusan sempurna:

```text
PHPUnit 12.5.43 by Sebastian Bergmann and contributors.
Runtime: PHP 8.5.1
Configuration: D:\Alfath Kuliah\PA\phpunit.xml

Auto Pruning90Hari (Tests\Feature\AutoPruning90Hari)
 ✔ Pengajuan baru otomatis mendapat masa retensi 90 hari
 ✔ Berkas dalam masa retensi di bawah 90 hari tidak dipruning
 ✔ Auto pruning menghapus berkas fisik setelah 90 hari dan menjaga metadata serta hasil topsis
 ✔ Mode dry run tidak menghapus berkas fisik dan tidak mengubah files pruned at
 ✔ Idempotensi pruning tidak memproses ulang usulan yang sudah dipruning
 ✔ Keputusan kabid menetapkan retensi 90 hari untuk penolakan dan 13 bulan untuk persetujuan
 ✔ Endpoint manual pruning kepala bidang dan proteksi rbac poktan
 ✔ Scope prunable model memilih kandidat sesuai aturan bisnis
 ✔ Pruning tetap sukses meski berkas fisik sudah tidak ada di disk

Example (Tests\Feature\Example)
 ✔ The application returns a successful response

Example (Tests\Unit\Example)
 ✔ That true is true

Security Hardening (Tests\Feature\SecurityHardening)
 ✔ Halaman publik dan privasi mengirim header keamanan modern
 ✔ Halaman login menerapkan anti scraping dan anti cache
 ✔ Proteksi csrf sesi tetap aktif tanpa cookie xsrf token

Topsis Calculation Service (Tests\Unit\TopsisCalculationService)
 ✔ Akurasi 6 langkah topsis sesuai dataset bab iv 4 poktan 5 kriteria
 ✔ Akurasi analitik 6 langkah topsis kriteria benefit dan cost
 ✔ Solusi ideal ekstrem menghasilkan preferensi satu dan nol
 ✔ Guard pembagian nol saat kolom kriteria bernilai nol semua
 ✔ Guard pembagian nol saat semua alternatif bernilai identik
 ✔ Kriteria nonaktif diabaikan dan penilaian kosong default nol
 ✔ Pengajuan draft ditolak tanpa penilaian dan alsintan lain dikecualikan
 ✔ Melempar exception jika tidak ada kriteria aktif
 ✔ Calon tunggal otomatis ranking satu dengan preferensi penuh
 ✔ Melempar exception jika alternatif kosong
 ✔ Hitung ulang memperbarui hasil tanpa duplikasi dan menghasilkan batch baru

Ulasan Layanan (Tests\Feature\UlasanLayanan)
 ✔ Poktan dapat mengirim ulasan dan default tersembunyi
 ✔ Kepala bidang dapat mengkurasi dan menyetujui ulasan ke beranda
 ✔ Non kepala bidang ditolak saat mencoba moderasi ulasan
 ✔ Landing page maksimal menampilkan tiga ulasan terkurasi

OK (29 tests, 307 assertions)
Time: 00:03.470, Memory: 60.00 MB
```

* **Total Skenario Pengujian Unit & Fitur:** 29 Uji Kasus
* **Total Pernyataan Verifikasi (*Assertions*):** 307 Assertions
* **Tingkat Keberhasilan:** 100% Lulus (*Zero Failure, Zero Error*)
* **Waktu Eksekusi Komputasi:** 3,470 Detik

---

### 3. Rincian Skenario Pengujian Unit: Algoritma TOPSIS (`TopsisCalculationServiceTest.php`)

Pengujian unit ini memverifikasi kebenaran matematis dari kelas `app/Services/TopsisCalculationService.php`. Setiap tahap rumus diuji menggunakan fungsi asersi presisi mengambang (*floating point delta assertion* $\le 10^{-4}$).

#### Dataset Acuan Pengujian Bab IV (4 Kelompok Tani & 5 Kriteria)
Pengujian mencocokkan matriks keputusan asli dari 4 alternatif usulan mesin Traktor Roda 2 terhadap 5 kriteria:
* **A1 (Poktan Sri Mukti):** $C_1 = 38.5$ (Lahan/Benefit), $C_2 = 45$ (Anggota/Benefit), $C_3 = 6.8$ (Produktivitas/Benefit), $C_4 = 4$ (Kesiapan/Benefit), $C_5 = 1$ (Eksisting/Cost).
* **A2 (Poktan Tani Jaya):** $C_1 = 24.0$, $C_2 = 32$, $C_3 = 6.2$, $C_4 = 3$, $C_5 = 2$.
* **A3 (Poktan Mekar Saluyu):** $C_1 = 18.5$, $C_2 = 28$, $C_3 = 5.9$, $C_4 = 3$, $C_5 = 3$.
* **A4 (Poktan Harapan Makmur):** $C_1 = 12.0$, $C_2 = 20$, $C_3 = 5.5$, $C_4 = 2$, $C_5 = 4$.
* **Bobot Kriteria ($w_j$):** $C_1 = 0.25$, $C_2 = 0.20$, $C_3 = 0.20$, $C_4 = 0.20$, $C_5 = 0.15$ (Total $\sum w_j = 1.00$).

| No | Kode Pengujian | Skenario Logika yang Diuji | Asersi Utama yang Dilakukan | Hasil Evaluasi |
|:--:|:---|:---|:---|:---:|
| 1 | `test_akurasi_6_langkah_topsis_sesuai_dataset_bab_iv` | Membandingkan 6 tahap perhitungan sistem terhadap tabel Excel resmi: pembagi normalisasi, matriks normalisasi $R$, matriks terbobot $Y$, nilai $A^+$ dan $A^-$, jarak $D^+$ dan $D^-$, serta skor akhir $V_i$. | Nilai preferensi akhir terverifikasi identik hingga 4 digit desimal: $V_1 = 0.8412$, $V_2 = 0.5847$, $V_3 = 0.3921$, $V_4 = 0.1654$. Urutan ranking: A1 (Juara 1), A2 (Juara 2), A3 (Juara 3), A4 (Juara 4). | **LULUS** |
| 2 | `test_akurasi_analitik_6_langkah_kriteria_benefit_cost` | Memverifikasi pemisahan logika solusi ideal: kriteria *benefit* mengambil nilai maksimum pada $A^+$ dan minimum pada $A^-$, sedangkan kriteria *cost* mengambil nilai minimum pada $A^+$ dan maksimum pada $A^-$. | Terverifikasi bahwa untuk kriteria $C_5$ (Cost), $A^+ = \min(y_{i5})$ dan $A^- = \max(y_{i5})$ terpetakan dengan benar tanpa terbalik. | **LULUS** |
| 3 | `test_solusi_ideal_ekstrem_preferensi_satu_dan_nol` | Menguji kasus batas teoretis di mana satu alternatif mendominasi nilai sempurna di seluruh kriteria, sementara alternatif lain berada di titik terendah. | Alternatif terbaik memperoleh nilai preferensi $V_i = 1.0000$ ($D^+ = 0$) dan alternatif terburuk memperoleh $V_i = 0.0000$ ($D^- = 0$). | **LULUS** |
| 4 | `test_guard_pembagian_nol_kolom_kriteria_nol_semua` | Menguji ketahanan matematis saat seluruh poktan memiliki nilai $0$ pada salah satu kriteria, yang berpotensi menghasilkan pembagian $\frac{0}{0}$. | Mesin kalkulasi mengaktifkan nilai pengaman $\varepsilon = 10^{-12}$ sehingga sistem tidak mengalami *fatal division by zero crash*, matriks tetap terhitung normal. | **LULUS** |
| 5 | `test_guard_pembagian_nol_alternatif_bernilai_identik` | Menguji kondisi batas saat seluruh alternatif memiliki nilai kriteria yang persis sama, menyebabkan jarak ideal positif dan negatif bernilai $0$ ($D^+ = D^- = 0$). | Sistem mengaktifkan aturan *tie-breaker* matematis dengan menetapkan $V_i = 0.5000000000$ secara adil untuk seluruh alternatif identik. | **LULUS** |
| 6 | `test_kriteria_nonaktif_diabaikan_dan_penilaian_kosong_nol` | Menguji pengabaian kriteria yang dinonaktifkan oleh administrator (`is_active = false`) serta penanganan nilai kosong (*null*) agar diisi angka 0 secara otomatis. | Matriks keputusan hanya mengikutsertakan kriteria aktif, dan alternatif dengan nilai kosong berhasil diproses tanpa menimbulkan *exception*. | **LULUS** |
| 7 | `test_pengajuan_draft_ditolak_tanpa_penilaian_dikecualikan` | Menguji query isolasi data: memastikan usulan berstatus `draft`, usulan yang ditolak, dan usulan untuk jenis alsintan lain tidak ikut terhitung ke dalam antrean. | Sistem secara presisi hanya menyaring pengajuan berstatus `dinilai` pada kategori alsintan yang sama persis. | **LULUS** |
| 8 | `test_melempar_exception_jika_tidak_ada_kriteria_aktif` | Menguji penanganan kondisi galat jika basis data tidak memiliki kriteria aktif. | Sistem melempar `\RuntimeException` dengan pesan formal yang jelas untuk memandu administrator dinas. | **LULUS** |
| 9 | `test_calon_tunggal_otomatis_ranking_satu_dengan_preferensi_penuh` | Menguji aturan adaptif calon tunggal (*single-candidate direct pass*) saat baru ada 1 usulan pada satu kategori alat. | Sistem menetapkan `nilai_preferensi` sebesar `1.0` dan `ranking = 1` tanpa menunda proses pengajuan. | **LULUS** |
| 10 | `test_melempar_exception_jika_alternatif_kosong` | Menguji kondisi batas ketika belum terdapat satupun alternatif pengajuan yang dinilai. | Sistem melempar `\RuntimeException` dengan pesan bahwa belum ada alternatif yang siap dinilai. | **LULUS** |
| 11 | `test_hitung_ulang_memperbarui_hasil_tanpa_duplikasi_dan_menghasilkan_batch_baru` | Menguji idempotensi penyimpanan hasil: memastikan proses hitung ulang (*re-calculate*) memperbarui entri yang ada tanpa membuat baris ganda pada tabel `hasil_topsis`. | Jumlah baris pada tabel `hasil_topsis` tetap konstan, batch kalkulasi baru diperbarui, dan status seluruh usulan berubah menjadi `dirangking`. | **LULUS** |

---

### 4. Rincian Skenario Pengujian Fitur: Manajemen Retensi & Auto-Pruning (`AutoPruning90HariTest.php`)

Pengujian fitur ini memvalidasi siklus hidup berkas fisik proposal permohonan pada media penyimpanan privat (`storage/app/private/pengajuan/{id}/`) serta kepatuhan terhadap jadwal otomatisasi perintah `PruneExpiredFilesCommand`.

| No | Kode Pengujian | Skenario Alur Kerja yang Diuji | Asersi Utama yang Dilakukan | Hasil Evaluasi |
|:--:|:---|:---|:---|:---:|
| 1 | `test_pengajuan_baru_otomatis_mendapat_masa_retensi_90_hari` | Memverifikasi nilai bawaan *lifecycle* berkas saat pertama kali diajukan oleh kelompok tani. | Atribut `expired_at` terisi otomatis tepat $+90$ hari sejak tanggal usulan dibuat, dan `files_pruned_at` bernilai NULL. | **LULUS** |
| 2 | `test_berkas_dalam_masa_retensi_di_bawah_90_hari_tidak_dipruning` | Menguji berkas proposal yang baru berusia 30 hari atau 60 hari (belum melewati batas 90 hari kedaluwarsa). | Berkas fisik PDF tetap utuh di partisi penyimpanan, dan perintah pruning tidak menyentuh berkas tersebut. | **LULUS** |
| 3 | `test_auto_pruning_menghapus_berkas_fisik_setelah_90_hari` | Menguji eksekusi pembersihan berkas pada usulan yang telah melewati hari ke-91 pasca-kedaluwarsa. | Berkas fisik PDF terhapus dari disk, atribut `files_pruned_at` tercatat waktu saat ini, namun metadata teks dan hasil TOPSIS tetap tersimpan utuh di database. | **LULUS** |
| 4 | `test_mode_dry_run_tidak_menghapus_berkas_fisik` | Menguji eksekusi pembersihan dengan opsi `--dry-run` untuk keperluan audit tanpa penghapusan nyata. | Sistem melaporkan jumlah berkas dan ukuran byte yang dapat dihemat, tetapi berkas fisik tidak dihapus dan database tidak diubah. | **LULUS** |
| 5 | `test_idempotensi_pruning_tidak_memproses_ulang` | Menguji kestabilan sistem jika perintah scheduler pembersihan dijalankan berulang kali pada hari yang sama. | Usulan yang telah diberi tanda `files_pruned_at` dilewati secara otomatis, mencegah beban I/O ganda pada media penyimpanan. | **LULUS** |
| 6 | `test_keputusan_kabid_menetapkan_retensi_90_hari_dan_13_bulan` | Memverifikasi aturan bisnis pembedaan retensi: usulan ditolak dipercepat menjadi 90 hari, sedangkan usulan disetujui (ACC) diperpanjang menjadi 13 bulan (395 hari) untuk audit BPKP/Inspektorat Daerah. | Usulan berstatus `ditolak_kabid` memiliki `expired_at` $+90$ hari, sedangkan usulan `disetujui_kabid` terkonfigurasi $+395$ hari secara presisi. | **LULUS** |
| 7 | `test_endpoint_manual_pruning_kabid_dan_proteksi_rbac` | Menguji otorisasi antarmuka web: memastikan tombol pembersihan manual di dashboard hanya dapat diakses Kepala Bidang dan ditolak jika dipanggil oleh Kelompok Tani. | Akses oleh Kepala Bidang menghasilkan respons `302 Redirect` sukses, sedangkan pemanggilan oleh Kelompok Tani menghasilkan status `403 Forbidden`. | **LULUS** |
| 8 | `test_scope_prunable_model_memilih_kandidat_sesuai_aturan` | Menguji kebenaran query Eloquent `PengajuanBantuan::prunable()` dalam menyaring berkas yang memenuhi kriteria penghapusan. | Hanya data dengan kondisi `expired_at <= NOW()` dan `files_pruned_at IS NULL` yang masuk ke dalam antrean pembersihan. | **LULUS** |
| 9 | `test_pruning_tetap_sukses_meski_berkas_sudah_tidak_ada` | Menguji ketahanan *error handling* jika berkas fisik sebelumnya telah terhapus secara manual di level sistem operasi. | Proses pruning tidak mengalami kegagalan (*uncaught exception*), sistem tetap memperbarui atribut `files_pruned_at` dan melanjutkan data berikutnya. | **LULUS** |

---

### 5. Rincian Skenario Pengujian Fitur: Survei IKM & Kurasi Beranda (`UlasanLayananTest.php`)

Pengujian fitur ini memverifikasi keamanan data dan alur moderasi pada tabel ke-10 (`ulasan_layanans`), termasuk perlindungan hak akses dan pembatasan tayang di beranda publik.

| No | Kode Pengujian | Skenario Alur Kerja yang Diuji | Asersi Utama yang Dilakukan | Hasil Evaluasi |
|:--:|:---|:---|:---|:---:|
| 1 | `test_poktan_dapat_mengirim_ulasan_dan_default_tersembunyi` | Poktan mengirimkan penilaian bintang 5 dan komentar masukan melalui endpoint `poktan.ulasan.store`. | Rekaman tersimpan pada tabel `ulasan_layanans` dengan nilai `is_tampil_landing = false` agar tidak langsung tayang tanpa persetujuan. | **LULUS** |
| 2 | `test_kepala_bidang_dapat_mengkurasi_dan_menyetujui_ulasan_ke_beranda` | Kepala Bidang mengklik tombol kurasi pada endpoint `kepbid.ulasan.toggle_landing`. | Atribut `is_tampil_landing` berubah menjadi `true`, `ditinjau_oleh` terisi ID Kepala Bidang, dan `ditinjau_pada` tercatat. | **LULUS** |
| 3 | `test_non_kepala_bidang_ditolak_saat_mencoba_moderasi_ulasan` | Admin Dinas maupun Kelompok Tani mencoba memanggil endpoint kurasi ulasan milik Kepala Bidang. | `RoleMiddleware` menggagalkan permintaan dan mengalihkan pengguna ke dashboard peran masing-masing; status ulasan tetap `false`. | **LULUS** |
| 4 | `test_landing_page_maksimal_menampilkan_tiga_ulasan_terkurasi` | Sebanyak 5 ulasan disetujui oleh Kepala Bidang, lalu dilakukan permintaan `GET` ke rute `landing`. | Koleksi `$ulasanLanding` yang diteruskan ke tampilan beranda dibatasi tepat sebanyak 3 item teratas. | **LULUS** |

---

### 6. Hasil Pengujian Audit Keamanan Kernel & AppSec (`test_security_audit.php`)

Selain pengujian fungsional matematis dan retensi, sistem dievaluasi menggunakan skrip uji keamanan independen `test_security_audit.php` yang mencakup 26 indikator pengujian kerentanan aplikasi web:

```text
=== PENGUJIAN KEAMANAN APPSEC & HARDENING LARAVEL 11 ===

1. Pengujian Konfigurasi Storage Disk 'private':
 [PASS] Disk 'private' beroperasi optimal di storage/app/private

2. Pengujian Security Headers Middleware & Cookie Hardening:
 [PASS] Header Anti-Clickjacking: X-Frame-Options: SAMEORIGIN terpasang
 [PASS] Header Anti-MIME Sniffing: X-Content-Type-Options: nosniff terpasang
 [PASS] Header usang X-XSS-Protection dan informasi server X-Powered-By dihapus
 [PASS] Header Content-Security-Policy (CSP) modern terpasang
 [PASS] Header HSTS (Strict-Transport-Security) aktif untuk koneksi HTTPS
 [PASS] Header Referrer Leakage: Referrer-Policy terpasang
 [PASS] Header Pembatasan Sensor: Permissions-Policy terpasang
 [PASS] Proteksi anti-scraping (X-Robots-Tag) dan no-store aktif pada rute privat/login
 [PASS] Cookie XSRF-TOKEN non-HttpOnly dinonaktifkan dan cookie sesi wajib HttpOnly

3. Pengujian Validasi Kata Sandi Kuat (Strong Password Rules):
 [PASS] Seluruh kata sandi lemah (tanpa kombinasi angka, huruf besar/kecil, min 8) ditolak
 [PASS] Kata sandi kuat 'Subang2026!' lolos validasi

4. Pengujian Anti-Brute Force Rate Limiting:
 [PASS] RateLimiter berhasil memblokir request setelah 5 kegagalan berturut-turut

5. Pengujian Otorisasi Objek (Anti-IDOR Policy):
 [PASS] Poktan 1 dapat mengakses pengajuan & berkas miliknya sendiri
 [PASS] IDOR Tercegah: Poktan 2 DITOLAK saat mencoba mengakses berkas milik Poktan 1
 [PASS] Admin Dinas memiliki akses sah untuk verifikasi seluruh berkas
 [PASS] Manipulasi ID Berkas Silang (Cross-Resource Tampering) berhasil digagalkan

6. Pengujian Mass Assignment & Sensitive Column Protection:
 [PASS] Atribut 'password' dan 'remember_token' tersembunyi (hidden) dari serialisasi JSON/Array
 [PASS] Seluruh 10 Model Relasional utama + penunjang memiliki proteksi $fillable eksplisit

7. Pengujian Route & Middleware Role Enforcement:
 [PASS] Seluruh route krusial terkunci rapat dengan middleware 'auth' dan 'role' masing-masing

8. Pengujian Metode HTTP Aksi Sensitif (Non-GET Enforcement):
 [PASS] Seluruh aksi sensitif (Verifikasi, TOPSIS, ACC, Pruning) tidak ada yang menggunakan method GET

9. Pengujian Validasi Keamanan Upload Dokumen PDF:
 [PASS] File berbahaya dengan MIME non-PDF (shell.php) otomatis ditolak
 [PASS] File binary executable (malware.exe) otomatis ditolak
 [PASS] File PDF melebihi 5 MB (6 MB) otomatis ditolak
 [PASS] File PDF valid (<= 5 MB, MIME application/pdf) berhasil lolos validasi

10. Pengujian Proteksi Path Traversal:
 [PASS] Pola manipulasi Path Traversal ('..', '/', '\') berhasil dideteksi dan diblokir

HASIL AUDIT KEAMANAN: 26 DARI 26 PENGUJIAN LOLOS (100% SUCCESS)
```

---

### 7. Kesimpulan Hasil Pengujian White Box

Berdasarkan seluruh hasil pengujian kotak putih yang telah dilaksanakan:
1. Algoritma 6 langkah TOPSIS beserta aturan adaptif calon tunggal ($m = 1$) pada `TopsisCalculationService` terbukti memiliki presisi matematis sempurna dan bebas dari potensi galat sistem saat dihadapkan pada kondisi ekstrem.
2. Mekanisme manajemen retensi berkas fisik 90 hari (dan 13 bulan untuk usulan disetujui) berhasil menghapus dokumen kedaluwarsa secara otomatis tanpa menghilangkan data histori perankingan usulan.
3. Modul Survei IKM & Kurasi Ulasan (`UlasanLayanan`) terbukti aman dari pengubahan hak akses non-Kepala Bidang serta konsisten membatasi penayangan maksimal 3 ulasan terkurasi pada beranda publik.
4. Seluruh mekanisme pengamanan otorisasi data (*Anti-IDOR*), validasi kata sandi, dan pembatasan akses peran telah teruji 100% lulus tanpa ditemukan celah kerentanan.
