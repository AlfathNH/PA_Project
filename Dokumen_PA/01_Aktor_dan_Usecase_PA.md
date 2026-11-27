# Identifikasi Aktor dan Spesifikasi Use Case Sistem
## Sistem Pendukung Keputusan Bantuan Alsintan Dinas Pertanian Kabupaten Subang
**Bahan Penyusunan Laporan Proyek Akhir: Bab III (Analisis dan Perancangan Sistem)**

---

### 1. Profil dan Karakteristik Aktor Sistem

Sistem Pendukung Keputusan (SPK) Penyaluran Bantuan Alat dan Mesin Pertanian (Alsintan) pada Dinas Pertanian Kabupaten Subang melibatkan tiga aktor utama dengan batasan wewenang yang terisolasi secara ketat melalui mekanisme *Role-Based Access Control* (RBAC).

| No | Nama Aktor | Peran dan Tanggung Jawab Operasional | Lingkup Hak Akses Sistem |
|:--:|:---|:---|:---|
| 1 | **Kelompok Tani (Poktan)** | Pengurus atau ketua kelompok tani terdaftar di Kabupaten Subang yang bertindak sebagai pemohon bantuan alat pertanian. | Mengakses portal publik, mendaftarkan akun mandiri (dengan dropdown 30 kecamatan dan input kontak numerik), mengelola data profil hamparan sawah dan anggota poktan, mengajukan proposal permohonan bantuan beserta unggahan berkas persyaratan PDF (maksimal 5 MB) yang dapat dibatalkan/direset, memantau riwayat status pengajuan secara langsung (*realtime*), mengunduh bukti permohonan, serta mengisi survei kepuasan layanan (IKM) secara opsional. |
| 2 | **Admin Verifikator Dinas** | Staf teknis Seksi Pengelolaan Alsintan Dinas Pertanian Kabupaten Subang yang bertugas memeriksa keabsahan administratif dan teknis lapangan. | Mengelola data master jenis alsintan dan kuota bantuan, memverifikasi kelengkapan berkas fisik proposal pemohon, menginput nilai parameter kriteria penilaian (C1 sampai C5), menjalankan algoritma perhitungan SPK metode TOPSIS secara terotomatisasi (mendukung kondisi multi-kandidat maupun kandidat tunggal), serta mengelola akun pengguna (pembekuan akun atau reset kata sandi petani). |
| 3 | **Kepala Bidang (Approver)** | Pejabat struktural pengambil keputusan final (Kepala Bidang Prasarana dan Sarana Pertanian / Tanaman Pangan) Dinas Pertanian Kabupaten Subang. | Meninjau antrean usulan yang telah dihitung dan dirangking oleh algoritma TOPSIS, menganalisis matriks preferensi dan skor akhir ($V_i$), memberikan keputusan persetujuan rekomendasi (*ACC*) atau penolakan dengan catatan resmi, memantau sisa kuota fisik alat yang tersedia, mencetak laporan rekapitulasi bantuan, mengelola retensi pembersihan berkas (*pruning archive*), serta memantau skor Indeks Kepuasan Masyarakat (IKM) dan mengkurasi maksimal 3 ulasan yang tayang di halaman beranda publik. |

---

### 2. Matriks Hak Akses Berbasis Peran (RBAC Matrix)

Pemberian hak akses fungsionalitas diatur secara terpusat melalui *RoleMiddleware* dan *Policy* objek otorisasi (`PengajuanBantuanPolicy`) untuk mencegah eskalasi wewenang dan manipulasi data antar-pengguna (*anti-IDOR*).

| Kode UC | Nama Fungsionalitas / Fitur | Kelompok Tani | Admin Dinas | Kepala Bidang | Catatan Teknis Otorisasi |
|:---:|:---|:---:|:---:|:---:|:---|
| UC-01 | Autentikasi dan Registrasi Akun Mandiri | Ya | Tidak (Internal) | Tidak (Internal) | Verifikasi email unik dan hashing kata sandi Bcrypt. |
| UC-02 | Manajemen Profil Hamparan dan Anggota | Ya | Ya (Lihat/Bantu) | Ya (Lihat) | Validasi luas lahan (Ha) dan jumlah anggota aktif. |
| UC-03 | Pengajuan Proposal Permohonan Alsintan | Ya | Tidak | Tidak | Validasi kuota tahunan dan kelengkapan lampiran PDF. |
| UC-04 | Pemantauan Status Usulan dan Riwayat | Ya | Ya | Ya | Isolasi data poktan hanya dapat melihat data miliknya. |
| UC-05 | Manajemen Master Jenis dan Kuota Alsintan | Tidak | Ya | Ya (Lihat) | CRUD data alsintan, batas kuota unit, dan status keaktifan. |
| UC-06 | Verifikasi Kelengkapan Berkas Proposal | Tidak | Ya | Tidak | Pemeriksaan format PDF, keaslian dokumen, dan keabsahan SK. |
| UC-07 | Input Nilai Kriteria Matriks Keputusan | Tidak | Ya | Tidak | Pengisian nilai kriteria benefit (C1-C4) dan cost (C5). |
| UC-08 | Eksekusi Mesin Algoritma SPK TOPSIS | Tidak | Ya | Tidak | Pemrosesan 6 tahap matematis dan penentuan nilai $V_i$. |
| UC-09 | Analisis Peringkat Rekomendasi Bantuan | Tidak | Ya | Ya | Urutan prioritas kandidat tertinggi berdasarkan skor preferensi. |
| UC-10 | Keputusan Akhir (Persetujuan ACC / Penolakan) | Tidak | Tidak | Ya | Pengurangan stok kuota fisik alat secara otomatis saat ACC. |
| UC-11 | Cetak Rekapitulasi Laporan Keputusan | Tidak | Ya | Ya | Format cetak dokumen standar audit BPKP dan kedinasan. |
| UC-12 | Manajemen Retensi dan Pembersihan Berkas | Tidak | Ya (Lihat) | Ya (Pemicu) | Mekanisme *Auto-Pruning* berkas kedaluwarsa 90 hari. |
| UC-13 | Manajemen Akun dan Reset Kredensial | Tidak | Ya | Tidak | Pengaktifan, pembekuan, dan reset kata sandi petani senior. |
| UC-14 | Navigasi Halaman dan Paginasi Ringkas | Ya | Ya | Ya | Paginasi responsif mobile dan fitur langsung menuju halaman (*Jump to Page*). |
| UC-15 | Pengisian Survei Kepuasan Layanan (IKM) | Ya | Tidak | Tidak | Penilaian opsional 1 sampai 5 bintang dan komentar masukan poktan pasca-pengajuan. |
| UC-16 | Kurasi Ulasan Beranda & Evaluasi Skor IKM | Tidak | Tidak | Ya | Moderasi persetujuan ulasan (*toggle landing*), maksimal 3 ulasan terpilih tayang publik. |

---

### 3. Deskripsi Rinci Kasus Penggunaan (Use Case Specification)

#### UC-01: Autentikasi dan Registrasi Akun Mandiri
* **Aktor:** Kelompok Tani, Admin Verifikator, Kepala Bidang.
* **Deskripsi:** Pengguna masuk ke dalam portal menggunakan email dan kata sandi yang telah terdaftar, dengan pembatasan kegagalan maksimal 3 kali sebelum terkunci sementara selama 5 menit (300 detik). Kelompok tani baru dapat melakukan registrasi mandiri menggunakan indikator kekuatan sandi langsung (*live password checklist*), pilihan 30 kecamatan Kabupaten Subang, serta input nomor WhatsApp numerik, lalu diarahkan ke halaman login setelah pendaftaran berhasil.
* **Pra-kondisi:** Pengguna berada pada halaman portal login atau halaman registrasi mandiri.
* **Pasca-kondisi:** Pengguna terotentikasi diarahkan ke dashboard sesuai peran masing-masing (*role-based redirection*).

#### UC-02: Manajemen Profil Hamparan dan Anggota
* **Aktor:** Kelompok Tani (Pemilik data), Admin Verifikator (Pengawas).
* **Deskripsi:** Mengelola data legalitas kelompok tani, meliputi nomor SK pengesahan dari Dinas/Kemenkumham, nama ketua kelompok, nomor telepon seluler aktif, lokasi desa/kelurahan, luas total hamparan sawah binaan (hektar), dan jumlah petani anggota aktif.
* **Pra-kondisi:** Pengguna kelompok tani telah berhasil login.
* **Pasca-kondisi:** Informasi profil diperbarui pada tabel `kelompok_tanis` dan siap dijadikan rujukan validasi pengajuan.

#### UC-03: Pengajuan Proposal Permohonan Alsintan
* **Aktor:** Kelompok Tani.
* **Deskripsi:** Poktan memilih jenis alsintan yang dibutuhkan (Traktor Roda 2, Pompa Air, dsb.), menginput jumlah unit yang diminta, menyertakan alasan urgensi kebutuhan lapangan, serta mengunggah berkas proposal resmi dalam format PDF yang aman.
* **Pra-kondisi:** Periode anggaran bantuan dibuka dan kuota alat masih tersedia.
* **Pasca-kondisi:** Sistem menerbitkan nomor pengajuan unik (contoh: `REQ-2026-0001`), mencatat waktu kedaluwarsa berkas (+90 hari), dan status usulan tercatat sebagai `diajukan`.

#### UC-04: Pemantauan Status Usulan dan Riwayat
* **Aktor:** Kelompok Tani.
* **Deskripsi:** Memantau alur perkembangan permohonan melalui lencana status interaktif, mulai dari tahap diajukan, diverifikasi berkas, dinilai kriteria, dirangking oleh SPK, hingga persetujuan akhir atau penolakan.
* **Pra-kondisi:** Poktan memiliki minimal satu usulan permohonan yang tersimpan di sistem.
* **Pasca-kondisi:** Poktan menerima informasi transparan mengenai status permohonan beserta catatan resmi dari dinas jika ada kekurangan berkas.

#### UC-05: Manajemen Master Jenis dan Kuota Alsintan
* **Aktor:** Admin Verifikator Dinas.
* **Deskripsi:** Mengelola inventaris kategori bantuan, kode alat, deskripsi spesifikasi mesin, alokasi total unit per tahun anggaran, serta status keaktifan alat agar dapat dipilih oleh pemohon.
* **Pra-kondisi:** Pengguna login dengan otoritas Admin Dinas.
* **Pasca-kondisi:** Daftar master alsintan tersinkronisasi dan kuota unit terpasang sebagai pembatas alokasi bantuan.

#### UC-06: Verifikasi Kelengkapan Berkas Proposal
* **Aktor:** Admin Verifikator Dinas.
* **Deskripsi:** Membuka dan mengunduh berkas PDF yang diunggah pemohon secara aman melalui *private storage*, memeriksa keabsahan stempel kelompok tani dan legalitas desa, kemudian menetapkan status `diverifikasi` atau `ditolak_admin`.
* **Pra-kondisi:** Terdapat usulan permohonan berstatus `diajukan`.
* **Pasca-kondisi:** Usulan yang lolos verifikasi siap dilanjutkan ke tahap penilaian kriteria lapangan. Usulan yang ditolak diberi catatan perbaikan dan berkas fisiknya dijadwalkan untuk pembersihan otomatis.

#### UC-07: Input Nilai Kriteria Matriks Keputusan
* **Aktor:** Admin Verifikator Dinas.
* **Deskripsi:** Mengisi nilai evaluasi riil terhadap usulan berdasarkan 5 kriteria standar: Luas Hamparan Sawah (C1 - Benefit), Jumlah Anggota Poktan (C2 - Benefit), Produktivitas Lahan Ton/Ha (C3 - Benefit), Kesiapan Operator dan Bengkel (C4 - Benefit), serta Jumlah Alsintan Eksisting yang Pernah Diterima (C5 - Cost).
* **Pra-kondisi:** Usulan berada pada status `diverifikasi`.
* **Pasca-kondisi:** Data tersimpan pada tabel `penilaian_topsis` dan status usulan berubah menjadi `dinilai`.

#### UC-08: Eksekusi Mesin Algoritma SPK TOPSIS
* **Aktor:** Admin Verifikator Dinas (Pemicu proses).
* **Deskripsi:** Memproses seluruh alternatif usulan siap nilai secara kolektif per jenis alsintan. Pada kondisi multi-kandidat ($m \ge 2$), algoritma mengeksekusi matriks keputusan, normalisasi Euclidean, pembobotan kriteria, penetapan solusi ideal positif dan negatif, penghitungan jarak Euclidean, hingga nilai preferensi ($V_i$). Pada kondisi kandidat tunggal ($m = 1$), diterapkan aturan penugasan langsung (*single-candidate direct pass*) yang menetapkan nilai preferensi $V_1 = 1.0000$ dan Peringkat #1 agar proses penyaluran tidak terhambat.
* **Pra-kondisi:** Terdapat minimal 1 usulan permohonan yang telah dinilai lengkap pada kategori alsintan terkait.
* **Pasca-kondisi:** Hasil nilai jarak dan preferensi tersimpan pada tabel `hasil_topsis`, peringkat rekomendasi diterbitkan, dan status usulan berganti menjadi `dirangking`.

#### UC-09: Analisis Peringkat Rekomendasi Bantuan
* **Aktor:** Admin Verifikator Dinas, Kepala Bidang.
* **Deskripsi:** Menampilkan urutan prioritas rekomendasi penerima bantuan mulai dari nilai preferensi $V_i$ tertinggi ($V_i \to 1$) hingga terendah. Dilengkapi transparansi rincian matematis 6 langkah algoritma untuk keperluan akuntabilitas data.
* **Pra-kondisi:** Mesin SPK TOPSIS telah berhasil menghitung kumpulan data usulan.
* **Pasca-kondisi:** Pengambil keputusan memperoleh informasi objektif mengenai kelompok tani mana yang paling berhak menerima alokasi alsintan.

#### UC-10: Keputusan Akhir (Persetujuan ACC / Penolakan)
* **Aktor:** Kepala Bidang.
* **Deskripsi:** Kepala Bidang memberikan keputusan definitif berupa persetujuan (*ACC*) atau penolakan terhadap usulan kandidat. Saat disetujui, kuota alat otomatis terpotong sesuai unit yang diminta dan masa retensi berkas diperpanjang menjadi 13 bulan untuk keperluan audit. Jika ditolak, usulan dialihkan ke riwayat arsip dengan retensi 90 hari.
* **Pra-kondisi:** Usulan berstatus `dirangking` dan sisa kuota unit mencukupi.
* **Pasca-kondisi:** Status berubah menjadi `disetujui_kabid` atau `ditolak_kabid`, stok kuota fisik berkurang, dan catatan pertimbangan pimpinan tersimpan permanen.

#### UC-11: Cetak Rekapitulasi Laporan Keputusan
* **Aktor:** Admin Verifikator Dinas, Kepala Bidang.
* **Deskripsi:** Mencetak dokumen rekapitulasi daftar penerima bantuan alsintan berdasarkan hasil keputusan pimpinan per kategori alat dan tahun anggaran dalam format cetak ramah kertas (*print-friendly view*) yang memuat kop surat dinas, tabel urutan penerima, nomor SK poktan, dan kolom tanda tangan pengesahan.
* **Pra-kondisi:** Telah terdapat pengajuan yang disetujui pada periode berjalan.
* **Pasca-kondisi:** Lembar dokumen laporan fisik siap dicetak atau disimpan ke format PDF untuk pelaporan kepada Kepala Dinas dan Bupati Subang.

#### UC-12: Manajemen Retensi dan Pembersihan Berkas (Auto-Pruning)
* **Aktor:** Sistem (Otomatis via Task Scheduler), Kepala Bidang (Manual On-Demand).
* **Deskripsi:** Menjalankan pembersihan berkas fisik PDF proposal yang telah melewati batas kedaluwarsa retensi (90 hari untuk usulan ditolak dan 13 bulan untuk usulan disetujui) guna menghemat kapasitas ruang penyimpanan server dinas. Metadata usulan dan rekapitulasi matematis TOPSIS tetap dipertahankan utuh.
* **Pra-kondisi:** Terdapat berkas usulan dengan tanggal `expired_at` lebih lampau dari tanggal saat ini dan belum pernah dipruning (`files_pruned_at IS NULL`).
* **Pasca-kondisi:** Berkas PDF pada disk privat terhapus secara aman, riwayat pencatatan log tersimpan pada tabel `prune_logs`, dan ruang penyimpanan disk server berhasil dioptimalkan.

#### UC-13: Manajemen Akun dan Reset Kredensial
* **Aktor:** Admin Verifikator Dinas.
* **Deskripsi:** Mengelola status keaktifan akun pengguna seluruh sistem. Membekukan akun yang terindikasi melanggar aturan, serta menyediakan fasilitas bantuan reset kata sandi sementara untuk pengurus kelompok tani senior yang mengalami kendala lupa kata sandi.
* **Pra-kondisi:** Pengguna login sebagai Admin Verifikator.
* **Pasca-kondisi:** Kata sandi baru terenkripsi tersimpan pada database dan status akun diperbarui.

#### UC-14: Navigasi Halaman dan Paginasi Ringkas
* **Aktor:** Seluruh Aktor.
* **Deskripsi:** Memfasilitasi perpindahan data pada antarmuka tabel berskala besar (hingga 1.000 data usulan) secara responsif tanpa geser horizontal di layar ponsel. Dilengkapi dengan kontrol ringkas navigasi halaman serta formulir langsung menuju halaman (*Jump to Page*) tanpa beban pemuatan ulang berlebih.
* **Pra-kondisi:** Data yang ditampilkan pada antarmuka melebihi batasan baris per halaman.
* **Pasca-kondisi:** Halaman terpilih dimuat secara cepat dengan tetap mempertahankan parameter pencarian dan penyaringan aktif.

#### UC-15: Pengisian Survei Kepuasan Layanan dan Rating Bintang (IKM)
* **Aktor:** Kelompok Tani.
* **Deskripsi:** Mengisi survei kepuasan layanan secara sukarela (opsional) melalui pop-up interaktif pasca-pengajuan atau tombol khusus di dashboard poktan. Pengguna memilih rating 1 sampai 5 bintang beserta komentar masukan yang akan masuk ke database dengan status tersembunyi (*default: false*) hingga dikurasi pimpinan.
* **Pra-kondisi:** Pengguna login sebagai Kelompok Tani dan mengakses portal dashboard poktan.
* **Pasca-kondisi:** Data penilaian tersimpan pada tabel ke-10 (`ulasan_layanans`) tanpa mengganggu jalannya proses pengajuan utama apabila poktan memilih tombol "Nanti Saja / Lewati".

#### UC-16: Kurasi Ulasan Beranda dan Pemantauan Skor IKM
* **Aktor:** Kepala Bidang.
* **Deskripsi:** Mengakses halaman khusus Survei IKM & Ulasan melalui menu atas maupun floating pill bar (`/kepbid/ulasan`) untuk mengevaluasi rata-rata skor Indeks Kepuasan Masyarakat sesuai standar Permenpan-RB No. 14/2017. Kepala Bidang berwenang mengkurasi ulasan mana saja yang disetujui untuk tayang di Landing Page publik (dibatasi maksimal 3 ulasan teratas) guna mencegah komentar tidak layak tampil ke masyarakat.
* **Pra-kondisi:** Pengguna login sebagai Kepala Bidang.
* **Pasca-kondisi:** Status `is_tampil_landing`, ID peninjau (`ditinjau_oleh`), dan waktu kurasi (`ditinjau_pada`) diperbarui, lalu tampilan Landing Page publik tersinkronisasi seketika.
