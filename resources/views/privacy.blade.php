<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi &amp; Cookie | SPK Bantuan Alsintan Kab. Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ file_exists(public_path('css/fonts.css')) ? filemtime(public_path('css/fonts.css')) : time() }}">
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}?v={{ file_exists(public_path('css/tailwind.min.css')) ? filemtime(public_path('css/tailwind.min.css')) : time() }}">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
    </style>
</head>
<body class="bg-bone-canvas text-obsidian antialiased font-sans min-h-screen flex flex-col">

    <header class="bg-forest-dark text-white border-b border-forest-deep">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo-subang-emblem.png') }}" alt="Logo Kabupaten Subang" class="h-9 w-auto object-contain">
                <div>
                    <div class="text-sm font-extrabold font-urbanist leading-tight">Dinas Pertanian Kab. Subang</div>
                    <div class="text-[11px] text-lime-harvest font-semibold">SPK Bantuan Alsintan (TOPSIS)</div>
                </div>
            </a>
            <a href="{{ route('landing') }}" class="px-4 py-2 min-h-[44px] inline-flex items-center gap-1.5 rounded-full bg-white/10 hover:bg-white/20 text-xs font-bold transition">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-12">
        <article class="bg-white rounded-3xl border border-bone-slate shadow-sm p-6 sm:p-10 space-y-6">
            <div class="border-b border-bone-slate pb-5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold mb-3">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    Kepatuhan UU PDP No. 27 Tahun 2022 &amp; Transparansi Cookie
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-forest-dark font-urbanist">
                    Kebijakan Privasi, Keamanan Data &amp; Pemberitahuan Cookie
                </h1>
                <p class="text-xs text-muted-green mt-2">
                    Berlaku untuk Portal Resmi Pelayanan Penyaluran Bantuan Alat dan Mesin Pertanian (Alsintan) Dinas Pertanian Pemerintah Daerah Kabupaten Subang.
                </p>
            </div>

            <section class="space-y-2 text-sm text-obsidian/90 leading-relaxed">
                <h2 class="text-base font-extrabold text-forest-dark">1. Penggunaan Cookie Sesi Fungsional</h2>
                <p>
                    Portal ini <strong>hanya menggunakan cookie sesi fungsional yang bersifat wajib</strong> untuk menjaga autentikasi masuk (<em>login</em>) yang terenkripsi, melindungi pengiriman formulir dari pemalsuan permintaan lintas situs (<em>CSRF</em>), serta memastikan sesi Kelompok Tani dan Pejabat Dinas tetap aman.
                </p>
                <p>
                    Seluruh cookie sesi dilindungi dengan atribut keamanan <code class="px-1.5 py-0.5 rounded bg-bone-canvas text-xs font-mono">HttpOnly</code>, <code class="px-1.5 py-0.5 rounded bg-bone-canvas text-xs font-mono">Secure</code> (HTTPS), dan <code class="px-1.5 py-0.5 rounded bg-bone-canvas text-xs font-mono">SameSite=Lax</code>. Portal ini <strong>tidak menggunakan cookie pelacak iklan atau analitik pihak ketiga</strong>.
                </p>
            </section>

            <section class="space-y-2 text-sm text-obsidian/90 leading-relaxed">
                <h2 class="text-base font-extrabold text-forest-dark">2. Pelindungan Data Kelembagaan &amp; Dokumen Proposal</h2>
                <p>
                    Data profil Kelompok Tani (nama ketua, nomor telepon aktif, luas hamparan sawah, nomor SK badan hukum, dan desa/kecamatan) digunakan secara eksklusif untuk keperluan verifikasi Calon Petani dan Calon Lokasi (CPCL) serta pemeringkatan objektif berbasis metode TOPSIS.
                </p>
                <p>
                    Dokumen proposal berformat PDF disimpan di ruang penyimpanan privat terisolasi dan hanya dapat diakses oleh Kelompok Tani pemilik berkas serta pejabat verifikator Dinas Pertanian yang berwenang.
                </p>
            </section>

            <section class="space-y-2 text-sm text-obsidian/90 leading-relaxed">
                <h2 class="text-base font-extrabold text-forest-dark">3. Retensi Berkas &amp; Survei Kepuasan (IKM)</h2>
                <ul class="list-disc list-inside space-y-1.5 pl-1">
                    <li>Berkas proposal fisik untuk usulan yang ditolak memiliki masa retensi <strong>90 hari</strong> sebelum dibersihkan secara otomatis oleh sistem (<em>auto-pruning</em>).</li>
                    <li>Berkas proposal untuk usulan yang disetujui (ACC) disimpan selama <strong>13 bulan</strong> sebagai dokumen pendukung audit akuntabilitas publik.</li>
                    <li>Pengisian survei kepuasan layanan (IKM) bersifat sukarela (opsional), dan ulasan yang tampil pada beranda publik wajib melalui kurasi Kepala Bidang terlebih dahulu.</li>
                </ul>
            </section>

            <div class="pt-4 border-t border-bone-slate flex flex-wrap items-center justify-between gap-3 text-xs text-muted-green">
                <span>Pengelola: Seksi Alsintan Dinas Pertanian Kab. Subang</span>
                <a href="{{ route('login') }}" class="font-bold text-forest-dark hover:underline">Masuk ke Portal Akun &rarr;</a>
            </div>
        </article>
    </main>

</body>
</html>
