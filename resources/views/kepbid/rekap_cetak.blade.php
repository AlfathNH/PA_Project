<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Rekapitulasi Bantuan Alsintan - Dinas Pertanian Kab. Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    
    <!-- Local Self-Hosted Fonts -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ file_exists(public_path('css/fonts.css')) ? filemtime(public_path('css/fonts.css')) : time() }}">
    
    <!-- Font Awesome (v6.7.2 dengan Subresource Integrity SHA-384) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          integrity="sha384-nRgPTkuX86pH8yjPJUAFuASXQSSl2/bBUiNV47vSYpKFxHJhbcrGnmlYpYJMeD7a"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            color: #111827;
            background-color: #f3f4f6;
            line-height: 1.4;
        }

        .no-print-bar {
            background: #14532D;
            color: white;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #16a34a;
            color: white;
        }
        .btn-primary:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: #374151;
            color: #e5e7eb;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }

        /* Lembar Kertas Dokumen Formal */
        .page-container {
            max-width: 210mm; /* Standar A4 */
            margin: 20px auto;
            background: white;
            padding: 20mm 20mm;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }

        /* KOP SURAT RESMI PEMKAB SUBANG */
        .kop-surat {
            border-bottom: 3px double #111827;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            text-align: center;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-header {
            flex: 1;
        }

        .kop-header h3 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-header h2 {
            font-size: 16pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .kop-header p {
            font-size: 9pt;
            line-height: 1.3;
            color: #374151;
        }

        /* Judul Berita Acara */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        .doc-title .nomor-surat {
            font-size: 10pt;
            font-family: 'Times New Roman', serif;
        }

        /* Paragraf Pengantar */
        .intro-text {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        /* Filter Info Badge */
        .filter-badge {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            margin-bottom: 16px;
            font-size: 9pt;
            display: flex;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Tabel Rekapitulasi Formal */
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }

        table.rekap-table th, 
        table.rekap-table td {
            border: 1px solid #1f2937;
            padding: 6px 8px;
            vertical-align: middle;
        }

        table.rekap-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Plus Jakarta Sans', ui-monospace, monospace; font-variant-numeric: tabular-nums; }
        .font-bold { font-weight: bold; }

        /* Ringkasan */
        .summary-box {
            margin-bottom: 24px;
            font-size: 10pt;
        }

        /* Tanda Tangan */
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd-box {
            width: 45%;
            text-align: center;
            font-size: 10pt;
        }

        .ttd-space {
            height: 65px;
        }

        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .ttd-nip {
            font-size: 9pt;
        }

        /* Media Print Khusus A4 Presisi Dinas Pertanian */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
            html, body {
                background: #ffffff !important;
                color: #111827 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .kop-surat {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            table.rekap-table {
                page-break-inside: auto;
            }
            table.rekap-table thead {
                display: table-header-group;
            }
            table.rekap-table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            .ttd-container {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Navigasi Atas (Disembunyikan saat dicetak) -->
    <div class="no-print-bar">
        <div>
            <i class="fa-solid fa-file-pdf mr-1 text-emerald-400"></i>
            <strong>Pratinjau Cetak Lembar Rekapitulasi Penetapan Alsintan</strong>
            <span class="text-slate-400 text-xs ml-2">Tahun Anggaran {{ $filterTahun }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Lembar Dokumen A4 -->
    <div class="page-container">

        <!-- KOP SURAT RESMI -->
        <div class="kop-surat">
            <!-- Lambang Resmi Kabupaten Subang -->
            <div style="width: 75px; text-align: center;">
                <img src="{{ asset('images/logo-subang-emblem.png') }}" 
                     alt="Logo Pemkab Subang" 
                     style="height: 75px; width: auto; object-fit: contain;">
            </div>

            <div class="kop-header">
                <h3>Pemerintah Daerah Kabupaten Subang</h3>
                <h2>Dinas Pertanian</h2>
                <p>
                    Jl. Piere Tendean No. 1, Pasirkareumbi, Kec. Subang, Kabupaten Subang, Jawa Barat 41211<br>
                    Pos-el: <u>distan@subang.go.id</u> &bull; Laman Resmi: <u>pertanian.subang.go.id</u>
                </p>
            </div>
        </div>

        <!-- JUDUL SURAT RESMI -->
        <div class="doc-title">
            <h1>BERITA ACARA REKAPITULASI PENETAPAN KELOMPOK TANI PENERIMA BANTUAN ALAT DAN MESIN PERTANIAN (ALSINTAN)</h1>
            <div class="nomor-surat">
                Nomor: 521.1 / BA-{{ $filterTahun }} / SPK-TOPSIS / DISTAN
            </div>
        </div>

        <!-- NARASI PENGANTAR -->
        <p class="intro-text">
            Pada hari ini, berdasarkan hasil pemrosesan Sistem Pendukung Keputusan (SPK) menggunakan metode <em>Technique for Order Preference by Similarity to Ideal Solution</em> (TOPSIS) multi-kriteria secara objektif terhadap usulan Kelompok Tani (Poktan), Kepala Bidang Sarana dan Prasarana Dinas Pertanian Kabupaten Subang menetapkan rekapitulasi daftar penerima bantuan Alsintan sebagai berikut:
        </p>

        <!-- INFORMASI PERIODE & ALAT -->
        <div class="filter-badge">
            <span><strong>Tahun Anggaran:</strong> {{ $filterTahun }}</span>
            <span><strong>Periode Bulan:</strong> {{ $filterBulan ? ($namaBulan[$filterBulan] ?? $filterBulan) : 'Semua Bulan (Tahunan)' }}</span>
            <span><strong>Kategori Alsintan:</strong> {{ $selectedJenis ? $selectedJenis->nama_alsintan : 'Seluruh Kategori Alat' }}</span>
            <span><strong>Status:</strong> {{ $filterStatus === 'disetujui_kabid' ? 'Disetujui (ACC)' : ($filterStatus === 'ditolak_kabid' ? 'Ditolak' : 'Semua Status') }}</span>
        </div>

        <!-- TABEL HASIL PENETAPAN BERITA ACARA -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">No. Usulan</th>
                    <th style="width: 25%;">Kelompok Tani & Wilayah</th>
                    <th style="width: 20%;">Jenis Alat Pertanian</th>
                    <th style="width: 8%;">Unit</th>
                    <th style="width: 14%;">Nilai Preferensi (<i>V</i><sub>i</sub>)</th>
                    <th style="width: 14%;">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($daftarPenerima as $index => $item)
                    @php
                        $isAcc = $item->status === 'disetujui_kabid';
                    @endphp
                    <tr>
                        <td class="text-center font-mono">{{ $index + 1 }}</td>
                        <td class="font-mono text-center">{{ $item->nomor_pengajuan }}</td>
                        <td>
                            <strong>{{ $item->user?->kelompokTani?->nama_poktan }}</strong><br>
                            <span style="font-size: 8.5pt; color: #4b5563;">
                                Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}
                            </span>
                        </td>
                        <td>
                            {{ $item->jenisAlsintan?->nama_alsintan }}
                        </td>
                        <td class="text-center font-mono font-bold">
                            {{ $item->jumlah_diminta }}
                        </td>
                        <td class="text-right font-mono font-bold">
                            {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 6) }}
                            <div style="font-size: 8pt; font-weight: normal; color: #6b7280;">(Peringkat #{{ $item->hasilTopsis?->ranking ?? '-' }})</div>
                        </td>
                        <td class="text-center font-bold">
                            {{ $isAcc ? 'DISETUJUI (ACC)' : 'DITOLAK' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 24px; color: #6b7280;">
                            Tidak ada data penetapan bantuan pada kriteria filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAL ALOKASI UNIT BANTUAN DISETUJUI:</td>
                    <td class="text-center font-mono" style="font-size: 11pt;">
                        {{ $daftarPenerima->where('status', 'disetujui_kabid')->sum('jumlah_diminta') }} Unit
                    </td>
                    <td colspan="2">
                        Dari total {{ $daftarPenerima->where('status', 'disetujui_kabid')->count() }} Poktan
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- KETENTUAN MASA BERLAKU / RETENSI ARSIP -->
        <p class="intro-text" style="font-size: 9pt; color: #4b5563; border-left: 2px solid #9ca3af; padding-left: 8px;">
            <strong>Catatan Retensi Dokumen:</strong> Berdasarkan kebijakan efisiensi penyimpanan server, berkas fisik PDF proposal Poktan yang disetujui (ACC) disimpan dengan masa retensi <strong>13 bulan</strong> untuk kebutuhan audit Laporan Pertanggungjawaban (LPJ) tahunan Dinas Pertanian. Setelah masa retensi terlewati, sistem akan melakukan <em>Auto-Pruning</em> terhadap file fisik, sedangkan data rekapitulasi teks dan nilai matematis TOPSIS tetap tersimpan permanen.
        </p>

        <!-- BLOK TANDA TANGAN RESMI -->
        <div class="ttd-container">
            <!-- Tim Verifikator / Operator SPK -->
            <div class="ttd-box">
                <p>Mengetahui / Diverifikasi oleh,</p>
                <p><strong>Admin Verifikator & Tim Teknis SPK</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">Ahmad Fathoni, S.P.</p>
                <p class="ttd-nip">NIP. 19850214 201001 1 012</p>
            </div>

            <!-- Kepala Bidang (Pengesah Akhir) -->
            <div class="ttd-box">
                <p>Subang, {{ now()->translatedFormat('d F Y') }}</p>
                <p><strong>Kepala Bidang Sarana & Prasarana Pertanian</strong></p>
                <div class="ttd-space"></div>
                <p class="ttd-name">Ir. H. Dedi Kusnadi, M.M.</p>
                <p class="ttd-nip">NIP. 19680512 199403 1 008</p>
            </div>
        </div>

    </div>

</body>
</html>

