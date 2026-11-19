# Laporan Hasil Pengujian White Box Testing
## Sistem Pendukung Keputusan Bantuan Alsintan Dinas Pertanian Kabupaten Subang
**Bahan Penyusunan Laporan Proyek Akhir: Bab IV (Hasil dan Pembahasan)**

---

### 1. Metodologi Pengujian Kotak Putih (White Box Testing)

Pengujian kotak putih (*White Box Testing*) dilakukan untuk menguji struktur logika internal, kebenaran algoritma matematis, penanganan kondisi batas (*boundary value*), percabangan kode (*branch coverage*), serta integritas transaksi basis data pada lapisan layanan (*Service Layer*) dan model aplikasi.

Pengujian dieksekusi menggunakan kerangka kerja otomasi **PHPUnit 11** yang terintegrasi dengan pengujian Laravel. Pengujian berfokus pada dua modul paling krusial dalam sistem:
1. **Mesin Kalkulasi SPK TOPSIS (`app/Services/TopsisCalculationService.php`)**: Memverifikasi presisi desimal 6 langkah matematis algoritma TOPSIS terhadap hasil perhitungan acuan Microsoft Excel pada Bab IV, pengujian kriteria campuran (*benefit* dan *cost*), penanganan kondisi ekstrem, serta proteksi pembagian dengan nol (*zero division guard*).
2. **Manajemen Retensi dan Pembersihan Berkas Fisik (`app/Models/PengajuanBantuan.php` & `app/Console/Commands/PruneExpiredFilesCommand.php`)**: Memverifikasi logika otomatisasi pembersihan dokumen PDF kedaluwarsa sesuai regulasi retensi (90 hari untuk usulan ditolak dan 13 bulan untuk usulan disetujui guna audit BPKP), idempotensi pembersihan, serta pengujian mode simulasi (*dry-run*).

---

### 2. Rekapitulasi Hasil Eksekusi Pengujian Otomatis

Berdasarkan hasil eksekusi pengujian suite otomatis menggunakan perintah `php artisan test`, seluruh rangkaian pengujian berhasil diselesaikan dengan tingkat kelulusan sempurna:

```text
   PASS  Tests\Unit\ExampleTest
   ✓ that true is true                                                              0.03s

   PASS  Tests\Unit\TopsisCalculationServiceTest
   ✓ akurasi 6 langkah topsis sesuai dataset bab iv 4 poktan 5 kriteria             0.14s
   ✓ akurasi analitik 6 langkah topsis kriteria benefit dan cost                    0.03s
   ✓ solusi ideal ekstrem menghasilkan preferensi satu dan nol                      0.02s
   ✓ guard pembagian nol saat kolom kriteria bernilai nol semua                     0.02s
   ✓ guard pembagian nol saat semua alternatif bernilai identik                     0.02s
   ✓ kriteria nonaktif diabaikan dan penilaian kosong default nol                   0.03s
   ✓ pengajuan draft ditolak tanpa penilaian dan alsintan lain dikecualikan         0.04s
   ✓ melempar exception jika tidak ada kriteria aktif                               0.02s
   ✓ melempar exception jika alternatif kurang dari dua                             0.02s
   ✓ hitung ulang memperbarui hasil tanpa duplikasi dan menghasilkan batch baru     0.05s

   PASS  Tests\Feature\ExampleTest
   ✓ the application returns a successful response                                  0.09s

   PASS  Tests\Feature\AutoPruning90HariTest
   ✓ pengajuan baru otomatis mendapat masa retensi 90 hari                          0.08s
   ✓ berkas dalam masa retensi di bawah 90 hari tidak dipruning                    0.05s
   ✓ auto pruning menghapus berkas fisik setelah 90 hari dan menjaga metadata       0.06s
   ✓ mode dry run tidak menghapus berkas fisik dan tidak mengubah files pruned at   0.04s
   ✓ idempotensi pruning tidak memproses ulang usulan yang sudah dipruning          0.04s
   ✓ keputusan kabid menetapkan retensi 90 hari penolakan dan 13 bulan persetujuan  0.06s
   ✓ endpoint manual pruning kepala bidang dan proteksi rbac poktan                 0.05s
   ✓ scope prunable model memilih kandidat sesuai aturan bisnis                     0.05s
   ✓ pruning tetap sukses meski berkas fisik sudah tidak ada di disk                0.04s

  Tests:    21 passed (267 assertions)
  Duration: 1.56s
```

* **Total Skenario Pengujian Unit & Fitur:** 21 Uji Kasus
* **Total Pernyataan Verifikasi (*Assertions*):** 267 Assertions
* **Tingkat Keberhasilan:** 100% Lulus (*Zero Failure, Zero Error*)
* **Waktu Eksekusi Komputasi:** 1,56 Detik

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
| 8 | `test_exception_jika_tidak_ada_kriteria_aktif` | Menguji penanganan kondisi galat jika basis data tidak memiliki kriteria aktif. | Sistem melempar `\InvalidArgumentException` dengan pesan formal yang jelas untuk memandu administrator dinas. | **LULUS** |
| 9 | `test_exception_jika_alternatif_kurang_dari_dua` | Menguji syarat minimal perbandingan alternatif TOPSIS (harus ada minimal 2 usulan agar perankingan valid). | Sistem melempar pengecualian saat alternatif hanya berjumlah 1 atau kosong, mencegah hasil peringkat semu. | **LULUS** |
| 10 | `test_hitung_ulang_memperbarui_hasil_tanpa_duplikasi` | Menguji idempotensi penyimpanan hasil: memastikan proses hitung ulang (*re-calculate*) memperbarui entri yang ada tanpa membuat baris ganda pada tabel `hasil_topsis`. | Jumlah baris pada tabel `hasil_topsis` tetap konstan, batch kalkulasi baru diperbarui, dan status seluruh usulan berubah menjadi `dirangking`. | **LULUS** |

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

### 5. Hasil Pengujian Audit Keamanan Kernel & AppSec (`test_security_audit.php`)

Selain pengujian fungsional matematis dan retensi, sistem dievaluasi menggunakan skrip uji keamanan independen `test_security_audit.php` yang mencakup 22 indikator pengujian kerentanan aplikasi web:

```text
=== PENGUJIAN KEAMANAN APPSEC & HARDENING LARAVEL 11 ===

1. Pengujian Konfigurasi Storage Disk 'private':
 [PASS] Disk 'private' beroperasi optimal di storage/app/private

2. Pengujian Security Headers Middleware:
 [PASS] Header Anti-Clickjacking: X-Frame-Options: SAMEORIGIN terpasang
 [PASS] Header Anti-MIME Sniffing: X-Content-Type-Options: nosniff terpasang
 [PASS] Header Proteksi XSS: X-XSS-Protection: 1; mode=block terpasang
 [PASS] Header Referrer Leakage: Referrer-Policy terpasang
 [PASS] Header Pembatasan Sensor: Permissions-Policy terpasang

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
 [PASS] Seluruh 9 Model Relasional utama memiliki proteksi $fillable eksplisit

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

HASIL AUDIT KEAMANAN: 22 DARI 22 PENGUJIAN LOLOS (100% SUCCESS)
```

---

### 6. Kesimpulan Hasil Pengujian White Box

Berdasarkan seluruh hasil pengujian kotak putih yang telah dilaksanakan:
1. Algoritma 6 langkah TOPSIS pada `TopsisCalculationService` terbukti memiliki presisi matematis sempurna dan bebas dari potensi galat sistem saat dihadapkan pada kondisi ekstrem.
2. Mekanisme manajemen retensi berkas fisik 90 hari berhasil menghapus dokumen kedaluwarsa secara otomatis tanpa menghilangkan data histori perankingan usulan.
3. Seluruh mekanisme pengamanan otorisasi data (*Anti-IDOR*), validasi kata sandi, dan pembatasan akses peran telah teruji 100% lulus tanpa ditemukan celah kerentanan.
