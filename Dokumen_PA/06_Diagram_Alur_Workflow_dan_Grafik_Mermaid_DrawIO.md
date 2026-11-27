# Kumpulan Diagram Alur Sistem, Workflow, Arsitektur, dan ERD (Mermaid untuk Draw.io & Word)
## Sistem Pendukung Keputusan Bantuan Alsintan Dinas Pertanian Kabupaten Subang
**Bahan Penyusunan Laporan Proyek Akhir: Bab I, Bab III (Perancangan), dan Bab IV (Implementasi)**

---

## Panduan Impor Kode Mermaid ke Draw.io & Tempel ke Microsoft Word

Seluruh blok kode `mermaid` di bawah ini dirancang agar **kompatibel penuh dengan Draw.io (*diagrams.net*)** untuk dikonversi menjadi gambar vektor berkualitas tinggi sebelum ditempel ke naskah **Microsoft Word**:

1. Buka situs **[app.diagrams.net](https://app.diagrams.net/)** atau aplikasi desktop **Draw.io**.
2. Pada menu atas, klik **Arrange** > **Insert** > **Advanced** > **Mermaid...**
3. Salin (*copy*) seluruh isi kode di dalam salah satu blok `mermaid` di dokumen ini, lalu tempel (*paste*) ke kotak dialog Mermaid di Draw.io dan klik **Insert**.
4. Diagram akan langsung terbentuk sebagai objek vektor yang masih bisa Anda geser, beri warna *Harvesta Green* (`#14532D` / `#E1F12E`), atau rapikan tata letaknya.
5. Untuk memindahkan ke Microsoft Word:
   - Klik **File** > **Export as** > **PNG...** (pilih resolusi *Zoom 200%* atau *300%* agar tajam saat dicetak, centang *Transparent Background* jika perlu), lalu sisipkan gambar PNG tersebut ke Word (*Insert > Pictures*).

---

## 1. Diagram Use Case Sistem (3 Aktor & 16 Fungsionalitas)

Gunakan diagram ini pada **Bab III (Analisis Sistem / Use Case Diagram)**.

```mermaid
flowchart LR
    P["Aktor: Kelompok Tani (Poktan)"]
    A["Aktor: Admin Verifikator Dinas"]
    K["Aktor: Kepala Bidang (Approver)"]
    S["Aktor: Sistem (Scheduler)"]

    subgraph SPK["Sistem Informasi & SPK Bantuan Alsintan Kab. Subang"]
        UC1("UC-01 Autentikasi & Registrasi Mandiri")
        UC2("UC-02 Kelola Profil Lahan & 30 Kecamatan")
        UC3("UC-03 Pengajuan Proposal & Lampiran PDF")
        UC4("UC-04 Pantau Status Permohonan Real-Time")
        UC15("UC-15 Isi Survei Kepuasan IKM (Opsional)")

        UC5("UC-05 Kelola Master Alat & Kuota Unit")
        UC6("UC-06 Verifikasi Kelengkapan Berkas PDF")
        UC7("UC-07 Input Nilai Matriks Kriteria C1-C5")
        UC8("UC-08 Eksekusi Algoritma TOPSIS (m=1 & m>=2)")
        UC13("UC-13 Kelola Akun & Reset Kata Sandi")

        UC9("UC-09 Analisis Peringkat Preferensi Vi")
        UC10("UC-10 Keputusan Final ACC / Tolak")
        UC11("UC-11 Cetak Rekapitulasi Laporan")
        UC16("UC-16 Kurasi Ulasan Beranda & Evaluasi IKM")

        UC12("UC-12 Auto-Pruning Berkas 90 Hari / 13 Bulan")
        UC14("UC-14 Navigasi Paginasi Ringkas & Jump Page")
    end

    P --- UC1
    P --- UC2
    P --- UC3
    P --- UC4
    P --- UC15
    P --- UC14

    A --- UC1
    A --- UC5
    A --- UC6
    A --- UC7
    A --- UC8
    A --- UC9
    A --- UC11
    A --- UC13
    A --- UC14

    K --- UC1
    K --- UC9
    K --- UC10
    K --- UC11
    K --- UC12
    K --- UC16
    K --- UC14

    S --- UC12
```

---

## 2. Alur Kerja Bisnis Sistem End-to-End (Activity / Workflow Diagram)

Gunakan diagram ini pada **Bab III (Perancangan Alur Proses Bisnis / Flowchart Sistem)**. Menggambarkan perjalanan utuh usulan bantuan dari pendaftaran poktan hingga evaluasi pimpinan dan pembersihan berkas.

```mermaid
flowchart TD
    Start(["Mulai: Pengurus Poktan Mengakses Portal"]) --> CekAkun{"Sudah Punya Akun?"}
    CekAkun -- "Belum" --> Reg["Registrasi Mandiri Poktan\n(Live Password Checklist, 30 Kec. Subang, WA Numerik)"]
    Reg --> LoginPage["Dialihkan ke Halaman Login\n(Notifikasi Sukses Daftar)"]
    CekAkun -- "Sudah" --> LoginPage

    LoginPage --> AuthAttempt{"Autentikasi Login\n(Maks. 3x Salah)"}
    AuthAttempt -- "Gagal 3x" --> Ban["Blokir Sementara 5 Menit\n(Rate Limiter Anti-Spam)"]
    Ban --> LoginPage
    AuthAttempt -- "Berhasil" --> DashPoktan["Dashboard Kelompok Tani"]

    DashPoktan --> BuatUsulan["Isi Form Pengajuan Alsintan\n(Pilih Alat, Jumlah, Alasan, PDF Opsional + Tombol Batal PDF)"]
    BuatUsulan --> SimpanUsulan["Simpan Data Usulan\n(Status: diajukan | Masa Retensi: +90 Hari)"]
    SimpanUsulan --> NotifSwipe["Muncul Notifikasi Swipeable\n(Hilang Otomatis 30 Detik / Geser Kiri-Kanan)"]
    SimpanUsulan --> PopupIKM{"Pop-up Survei Kepuasan IKM\n(Opsional / Sukarela)"}

    PopupIKM -- "Isi Bintang & Komentar" --> SimpanIKM["Simpan ke Tabel ulasan_layanans\n(Status: Internal / Menunggu Kurasi KepBid)"]
    PopupIKM -- "Nanti Saja / Lewati" --> AntreanAdmin

    SimpanIKM --> AntreanAdmin["Admin Dinas Membuka Antrean\n(Menu Verifikasi & Nilai)"]
    AntreanAdmin --> CekBerkas{"Verifikasi Berkas\n& Keabsahan SK?"}

    CekBerkas -- "Tidak Memenuhi Syarat" --> TolakAdmin["Status: ditolak_admin\n(Catat Alasan + Jadwal Pruning 90 Hari)"]
    CekBerkas -- "Lolos Administrasi" --> StatusVerif["Status: diverifikasi"]

    StatusVerif --> InputNilai["Admin Input Nilai Kriteria\n(C1 Lahan, C2 Anggota, C3 Produktivitas,\nC4 Kesiapan, C5 Eksisting)"]
    InputNilai --> StatusDinilai["Status: dinilai"]
    StatusDinilai --> RunTOPSIS["Admin Eksekusi Hitung SPK TOPSIS"]
    RunTOPSIS --> StatusRanking["Status: dirangking\n(Urut Preferensi Vi Tertinggi ke Terendah)"]

    StatusRanking --> KepbidReview["Kepala Bidang Meninjau Antrean Keputusan"]
    KepbidReview --> KeputusanKabid{"Keputusan Final\nKepala Bidang?"}

    KeputusanKabid -- "Tolak" --> TolakKabid["Status: ditolak_kabid\n(Masuk Riwayat | Retensi Tetap 90 Hari)"]
    KeputusanKabid -- "Setujui (ACC)" --> SetujuKabid["Status: disetujui_kabid\n(Potong Kuota Alat Otomatis |\nMasuk Riwayat | Retensi Diperpanjang 13 Bulan)"]

    SetujuKabid --> CetakSK["Cetak Rekapitulasi Penerima Bantuan\n(Standar Audit BPKP / ANRI)"]
    KepbidReview --> KurasiIKM["Halaman Khusus Survei IKM KepBid\n(/kepbid/ulasan)"]
    KurasiIKM --> ToggleBeranda{"Setujui Tampil di Beranda?"}
    ToggleBeranda -- "Ya (Maks. 3 Ulasan Teratas)" --> UpdateLanding["Tampil di Carousel Landing Page\n(Format: Nama Ketua + Nama Poktan)"]
    ToggleBeranda -- "Tidak / Sembunyikan" --> ArsipIKM["Tetap Tersimpan sebagai Evaluasi Mutu Internal"]

    TolakAdmin --> Pruning["Scheduler Harian 01:00 WIB / Manual On-Demand\n(Auto-Pruning Hapus File PDF Kedaluwarsa)"]
    TolakKabid --> Pruning
    CetakSK --> EndNode(["Selesai"])
```

---

## 3. Flowchart Mesin Algoritma TOPSIS (6 Langkah + Jalur Adaptif Calon Tunggal)

Gunakan diagram ini pada **Bab III / Bab IV (Rancangan Algoritma SPK TOPSIS)** untuk menjelaskan perhitungan matematis beserta penanganan kasus batas *Single Candidate* dan *Zero Division Guard*.

```mermaid
flowchart TD
    StartAlgo(["Mulai: Panggil TopsisCalculationService::hitung(jenisAlsintanId)"]) --> LoadKriteria["Ambil Kriteria Aktif (C1 - C5) beserta Bobot (wj) & Tipe (Benefit/Cost)"]
    LoadKriteria --> CekKriteria{"Kriteria Aktif\nTersedia?"}
    CekKriteria -- "Kosong" --> ErrKriteria["Lempar RuntimeException:\nTidak ada kriteria aktif"]
    CekKriteria -- "Ada" --> LoadAlternatif["Ambil Usulan Siap Nilai (Status: dinilai) pada Kategori Terpilih"]

    LoadAlternatif --> CekJumlah{"Hitung Jumlah\nAlternatif (m)"}
    CekJumlah -- "m = 0 (Kosong)" --> ErrKosong["Lempar RuntimeException:\nBelum ada alternatif yang siap dinilai"]

    CekJumlah -- "m = 1 (Calon Tunggal)" --> DirectPass["Jalur Adaptif Penugasan Langsung:\nTetapkan D+ = 0.0, D- = 1.0,\nSkor Preferensi V1 = 1.0000000000,\nPeringkat #1"]

    CekJumlah -- "m >= 2 (Multi-Kandidat)" --> Step1["Langkah 1: Bangun Matriks Keputusan X (m x n)\nNilai kosong otomatis diisi 0"]
    Step1 --> Step2["Langkah 2: Normalisasi Matriks R\nPembagi r_j = sqrt(sum(x_ij^2))\n(Guard: jika r_j == 0 pakai epsilon 1e-12)"]
    Step2 --> Step3["Langkah 3: Matriks Ternormalisasi Terbobot Y\ny_ij = w_j * r_ij (Presisi Float Penuh)"]
    Step3 --> Step4["Langkah 4: Solusi Ideal Positif (A+) & Negatif (A-)\nBenefit: A+ = max(y), A- = min(y)\nCost: A+ = min(y), A- = max(y)"]
    Step4 --> Step5["Langkah 5: Hitung Jarak Euclidean (D+ & D-)\nD_i+ = sqrt(sum((y_ij - A_j+)^2))\nD_i- = sqrt(sum((y_ij - A_j-)^2))"]
    Step5 --> Step6["Langkah 6: Hitung Nilai Preferensi (Vi)\nVi = D_i- / (D_i+ + D_i-)\n(Guard Tie-Breaker: jika D+ + D- == 0, Vi = 0.5)\nPembulatan Akhir: round(Vi, 10)"]
    Step6 --> UrutRank["Urutkan Vi Descending untuk Menetapkan Ranking 1..m"]

    DirectPass --> SimpanDB["DB Transaction:\nSimpan ke hasil_topsis (UUID Batch) &\nUpdate Status Pengajuan menjadi dirangking"]
    UrutRank --> SimpanDB
    SimpanDB --> SelesaiAlgo(["Selesai: Kembalikan Matriks Transparan 1-6 ke Controller"])
```

---

## 4. Sequence Diagram Interaksi Sistem (Pengajuan, Penetapan, & Survei IKM)

Gunakan diagram ini pada **Bab III (Perancangan Perilaku Sistem / Sequence Diagram)**.

```mermaid
sequenceDiagram
    actor Poktan as Kelompok Tani
    actor Admin as Admin Dinas
    actor Kabid as Kepala Bidang
    participant UI as View (Blade & Alpine.js)
    participant Ctrl as Laravel Controllers & Policy
    participant Svc as TopsisCalculationService
    participant DB as Database (10 Tabel)

    Poktan->>UI: Isi formulir usulan + pilih/batal PDF
    UI->>Ctrl: POST /poktan/pengajuan (CSRF Token)
    Ctrl->>DB: Simpan Pengajuan (Status: diajukan, +90 hari)
    DB-->>Ctrl: ID Usulan Baru
    Ctrl-->>UI: Redirect Status + Trigger Flash & Pop-up IKM
    UI-->>Poktan: Tampil Toast Swipeable (30s) & Modal Rating IKM (Opsional)

    opt Poktan Mengisi Survei Kepuasan
        Poktan->>UI: Pilih 1-5 Bintang + Komentar (Opsional)
        UI->>Ctrl: POST /poktan/ulasan
        Ctrl->>DB: Simpan ke ulasan_layanans (is_tampil_landing = false)
        DB-->>UI: Notifikasi Terima Kasih
    end

    Admin->>Ctrl: POST /admin/pengajuan/{id}/verifikasi
    Ctrl->>DB: Update Status = diverifikasi + Catat Audit Trail
    Admin->>Ctrl: POST /admin/pengajuan/{id}/penilaian (C1-C5)
    Ctrl->>DB: Simpan Nilai ke penilaian_topsis (Status = dinilai)
    Admin->>Ctrl: POST /admin/topsis/hitung
    Ctrl->>Svc: hitung(jenisAlsintanId, adminId)
    Svc->>DB: Simpan Skor Vi & Ranking ke hasil_topsis (Status = dirangking)
    Svc-->>UI: Tampil Tabel Matriks & Peringkat Rekomendasi

    Kabid->>Ctrl: POST /kepbid/pengajuan/{id}/putuskan (ACC)
    Ctrl->>DB: Update Status = disetujui_kabid, Potong Kuota Alat, Retensi 13 Bulan
    DB-->>UI: Pindah Otomatis ke Tab Riwayat Keputusan

    Kabid->>UI: Buka Halaman Survei IKM (/kepbid/ulasan)
    Kabid->>Ctrl: POST /kepbid/ulasan/{id}/toggle-landing
    Ctrl->>DB: Set is_tampil_landing = true, catat ditinjau_oleh & ditinjau_pada
    DB-->>UI: Maksimal 3 Ulasan Terkurasi Tayang pada Landing Page Publik
```

---

## 5. Diagram Arsitektur Aplikasi & Keamanan Berlapis (System Architecture)

Gunakan diagram ini pada **Bab III / Bab IV (Arsitektur Deployment Serverless & Pengamanan Aplikasi)**.

```mermaid
flowchart TB
    Client["Perangkat Pengguna\n(Browser Laptop / Ponsel Mobile 360px+)"]

    subgraph Cloud["Platform Deployment Serverless (Vercel Edge & PHP Runtime)"]
        SecMW["SecurityHeadersMiddleware\n(X-Frame-Options, XSS, Nosniff, Referrer-Policy)"]
        AuthRL["Rate Limiter Auth\n(Blokir 5 Menit setelah 3x Gagal Login)"]
        RBAC["RoleMiddleware\n(Isolasi Rute: Poktan, Admin, Kepala Bidang)"]
        Policy["PengajuanBantuanPolicy\n(Proteksi Anti-IDOR Unduhan Dokumen)"]
        Controllers["Layer Controller (MVC)\nAuth, Poktan, Admin, KepBid, UlasanLayanan"]
        Service["Service Layer:\nTopsisCalculationService (Presisi Float Penuh)"]
        Models["Eloquent ORM & Observer\n(Audit Trail Otomatis + MassPrunable 90 Hari)"]
    end

    subgraph Storage["Infrastruktur Data Terkelola"]
        NeonPG[("Neon PostgreSQL Cloud\n10 Tabel Relasional KKNI + Penunjang")]
        PrivDisk["Private Storage Disk\n(storage/app/private PDF Maks. 5 MB)"]
    end

    Client -- "HTTPS Request" --> SecMW
    SecMW --> AuthRL
    AuthRL --> RBAC
    RBAC --> Controllers
    Controllers --> Policy
    Controllers --> Service
    Service --> Models
    Controllers --> Models
    Models -- "PDO SSL Connection" --> NeonPG
    Policy -- "Otorisasi Pemilik / Pejabat" --> PrivDisk
```

---

## 6. Diagram Relasi Entitas / ERD (10 Tabel Utama + Penunjang) Siap Impor Draw.io

Gunakan kode berikut pada **Bab III (Perancangan Basis Data / ERD)**. Versi lengkap beserta kamus data rinci tersedia pada dokumen [03_ERD_dan_Skema_Database_PA.md](file:///d:/Alfath%20Kuliah/PA/Dokumen_PA/03_ERD_dan_Skema_Database_PA.md).

```mermaid
erDiagram
    KECAMATANS ||--o{ KELOMPOK_TANIS : "menaungi (1:N)"
    USERS ||--|| KELOMPOK_TANIS : "memiliki profil (1:1)"
    USERS ||--o{ PENGAJUAN_BANTUANS : "mengajukan (1:N)"
    USERS ||--o{ RIWAYAT_STATUS_PENGAJUANS : "mencatat jejak aktor (1:N)"
    USERS ||--o{ ULASAN_LAYANANS : "mengkurasi ulasan beranda (1:N)"
    
    PERIODE_BANTUANS ||--o{ JENIS_ALSINTANS : "mengalokasikan kuota (1:N)"
    JENIS_ALSINTANS ||--o{ PENGAJUAN_BANTUANS : "menjadi objek usulan (1:N)"
    
    KELOMPOK_TANIS ||--o{ ULASAN_LAYANANS : "mengisi survei IKM (1:N)"
    PENGAJUAN_BANTUANS ||--o| ULASAN_LAYANANS : "memiliki ulasan terkait (1:0..1)"
    
    PENGAJUAN_BANTUANS ||--o{ BERKAS_PENGAJUANS : "melampirkan dokumen (1:N)"
    PENGAJUAN_BANTUANS ||--o{ PENILAIAN_TOPSIS : "dinilai kriteria x_ij (1:N)"
    PENGAJUAN_BANTUANS ||--o{ RIWAYAT_STATUS_PENGAJUANS : "memiliki rekam status (1:N)"
    PENGAJUAN_BANTUANS ||--o| HASIL_TOPSIS : "menghasilkan preferensi Vi (1:1)"
    
    KRITERIAS ||--o{ PENILAIAN_TOPSIS : "menjadi parameter evaluasi (1:N)"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role
        boolean is_active
    }

    KECAMATANS {
        bigint id PK
        string nama_kecamatan
        string kode_kecamatan UK
        enum zona_wilayah
    }

    KELOMPOK_TANIS {
        bigint id PK
        bigint user_id FK
        bigint kecamatan_id FK
        string nama_poktan
        string nomor_badan_hukum UK
        string nama_ketua
        string no_hp_ketua
        string desa
        decimal luas_lahan_total
        integer jumlah_anggota
    }

    PERIODE_BANTUANS {
        bigint id PK
        string nama_periode
        smallint tahun_anggaran
        date tanggal_buka
        date tanggal_tutup
        integer kuota_total_unit
    }

    JENIS_ALSINTANS {
        bigint id PK
        bigint periode_bantuan_id FK
        string nama_alsintan
        string kode_alsintan UK
        integer kuota_unit
    }

    KRITERIAS {
        bigint id PK
        string kode_kriteria UK
        string nama_kriteria
        decimal bobot
        enum jenis_kriteria
    }

    PENGAJUAN_BANTUANS {
        bigint id PK
        bigint user_id FK
        bigint jenis_alsintan_id FK
        string nomor_pengajuan UK
        smallint tahun_pengajuan
        integer jumlah_diminta
        enum status
        datetime expired_at
        datetime files_pruned_at
    }

    BERKAS_PENGAJUANS {
        bigint id PK
        bigint pengajuan_bantuan_id FK
        string file_path
        string file_name_original
        bigint file_size
    }

    PENILAIAN_TOPSIS {
        bigint id PK
        bigint pengajuan_bantuan_id FK
        bigint kriteria_id FK
        decimal nilai
    }

    HASIL_TOPSIS {
        bigint id PK
        bigint pengajuan_bantuan_id FK
        string batch_kalkulasi_id
        decimal jarak_ideal_positif
        decimal jarak_ideal_negatif
        decimal nilai_preferensi
        integer ranking
    }

    RIWAYAT_STATUS_PENGAJUANS {
        bigint id PK
        bigint pengajuan_bantuan_id FK
        bigint user_id FK
        string status_lama
        string status_baru
        timestamp waktu_perubahan
    }

    ULASAN_LAYANANS {
        bigint id PK
        bigint kelompok_tani_id FK
        bigint pengajuan_bantuan_id FK
        tinyint rating_bintang
        text komentar
        boolean is_tampil_landing
        bigint ditinjau_oleh FK
        timestamp ditinjau_pada
    }
```
