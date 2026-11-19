<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SPK Bantuan Alsintan | Dinas Pertanian Kabupaten Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,300..900;1,300..900&family=Newsreader:ital,opsz,wght@0,6..72,300..800;1,6..72,300..800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        // User Reference Palette (media_1793774852232_3ac505b4)
                        "lime-harvest": "#E1F12E",
                        "lime-harvest-hover": "#CDE01F",
                        "amber-harvest": "#FFB555",
                        "celery-cream": "#EDF0C2",
                        "bone-slate": "#E3E4D6",
                        "bone-canvas": "#F4F5ED",
                        
                        "sage-bone": "#F1F3EB",
                        "sage-subtle": "#E6E9DF",
                        "chartreuse": "#E1F12E",
                        "chartreuse-hover": "#CDE01F",
                        "forest-dark": "#14532D",
                        "forest-deep": "#0F3F22",
                        "forest-hover": "#166534",
                        "obsidian": "#1C241D",
                        "amber-gold": "#FFB555",
                        "leaf-emerald": "#16A34A"
                    },
                    fontFamily: {
                        "editorial": ["Newsreader", "Georgia", "serif"],
                        "sans": ["Urbanist", "Plus Jakarta Sans", "system-ui", "sans-serif"],
                        "urbanist": ["Urbanist", "sans-serif"]
                    }
                }
            }
        };
    </script>

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .watermark-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            user-select: none;
            pointer-events: none;
        }
        .frosted-panel {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(20, 83, 45, 0.12);
        }
        .editorial-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(20, 83, 45, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(20, 83, 45, 0.035) 1px, transparent 1px);
        }
        .golden-ratio-primary {
            flex: 0 0 61.8%;
        }
        .golden-ratio-secondary {
            flex: 0 0 38.2%;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-sage-bone text-obsidian antialiased relative selection:bg-chartreuse selection:text-obsidian editorial-grid min-h-screen overflow-x-hidden font-sans">

    <!-- Monumental Ghost Watermark Layer (Editorial Swiss Style) -->
    <div class="fixed inset-0 pointer-events-none select-none z-0 overflow-hidden opacity-[0.03] flex justify-between px-6">
        <div class="text-[13vw] font-black uppercase tracking-tighter text-forest-dark watermark-text rotate-180">
            KARYA UTAMA SATYA NAGARA
        </div>
        <div class="text-[18vw] font-black uppercase tracking-tighter text-forest-dark watermark-text">
            SUBANG
        </div>
        <div class="hidden lg:block text-[13vw] font-black uppercase tracking-tighter text-forest-dark watermark-text rotate-180">
            SWASEMBADA PANGAN
        </div>
    </div>

    <!-- Government Protocol Top Banner -->
    <section class="relative z-40 bg-forest-dark text-white text-xs border-b border-forest-dark/30">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-8 py-2 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-chartreuse/15 text-chartreuse font-semibold text-[10px] tracking-wider uppercase border border-chartreuse/25">
                    KARYA UTAMA SATYA NAGARA
                </span>
                <span class="hidden md:inline text-white/80 font-medium tracking-wide">
                    PEMERINTAH DAERAH KABUPATEN SUBANG • DINAS PERTANIAN
                </span>
            </div>
            <div class="flex items-center gap-4 text-white/90 text-[11px]">
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-chartreuse">schedule</span>
                    <span id="live-clock">--:--:-- WIB • Subang, Jawa Barat</span>
                </span>
                <div class="h-3 w-px bg-white/20 hidden md:block"></div>
                <span class="hidden md:flex items-center gap-1 text-chartreuse">
                    <span class="material-symbols-outlined text-sm">verified_user</span>
                    Audit BPKP & Kementan Ready
                </span>
            </div>
        </div>
    </section>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/85 backdrop-blur-md border-b border-forest-dark/10 shadow-sm transition-all duration-200">
        <div class="flex justify-between items-center w-full px-4 sm:px-8 max-w-[1536px] mx-auto h-20">
            <!-- Brand & Transparent Emblem -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                     alt="Logo Pemkab Subang" 
                     class="h-12 w-auto object-contain transition-transform group-hover:scale-105 filter drop-shadow-sm">
                <div class="flex flex-col">
                    <span class="font-bold text-base sm:text-lg text-forest-dark tracking-tight leading-tight group-hover:text-emerald-700 transition-colors">
                        Dinas Pertanian Kabupaten Subang
                    </span>
                    <span class="text-[11px] font-semibold text-gray-500 tracking-wider uppercase mt-0.5">
                        Sistem Penunjang Keputusan Bantuan Alsintan (Metode TOPSIS)
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-7 text-xs uppercase tracking-wider" id="landing-navbar">
                <a href="#tentang" data-target="tentang" class="nav-item text-forest-dark font-bold border-b-2 border-forest-dark pb-1 transition-all">
                    Tentang Alsintan
                </a>
                <a href="#alur-pengajuan" data-target="alur-pengajuan" class="nav-item text-gray-600 hover:text-forest-dark font-semibold pb-1 transition-all border-b-2 border-transparent">
                    Layanan & Alur Alsintan
                </a>
                <a href="#retensi" data-target="retensi" class="nav-item text-gray-600 hover:text-forest-dark font-semibold pb-1 transition-all border-b-2 border-transparent">
                    Kebijakan Retensi
                </a>
            </nav>

            <!-- Right Portal CTAs -->
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-forest-dark text-white font-bold text-xs hover:bg-forest-hover transition-all shadow-sm">
                            <span class="material-symbols-outlined text-base text-chartreuse">dashboard</span>
                            <span>Dashboard Admin</span>
                        </a>
                    @elseif(auth()->user()->role === 'kepala_bidang')
                        <a href="{{ route('kepbid.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-forest-dark text-white font-bold text-xs hover:bg-forest-hover transition-all shadow-sm">
                            <span class="material-symbols-outlined text-base text-chartreuse">dashboard</span>
                            <span>Dashboard Pimpinan</span>
                        </a>
                    @else
                        <a href="{{ route('poktan.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-forest-dark text-white font-bold text-xs hover:bg-forest-hover transition-all shadow-sm">
                            <span class="material-symbols-outlined text-base text-chartreuse">dashboard</span>
                            <span>Dashboard Poktan</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-forest-dark/30 text-forest-dark hover:bg-forest-dark/5 text-xs font-bold transition">
                        Daftar Poktan
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-chartreuse text-obsidian font-bold text-xs hover:bg-chartreuse-hover active:scale-[0.98] transition-all shadow-sm border border-black/10">
                        <span>Masuk Portal</span>
                        <span class="w-6 h-6 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center text-xs">
                            <span class="material-symbols-outlined text-sm leading-none">arrow_forward</span>
                        </span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Editorial Canvas -->
    <main class="relative z-10">

        <!-- =================================================================== -->
        <!-- HERO SECTION: Asymmetric 2-Column Executive Split (7 / 5 Columns)   -->
        <!-- =================================================================== -->
        <section class="pt-8 pb-14 lg:py-14 border-b border-forest-dark/10 relative" id="tentang">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-8">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">

                    <!-- KOLOM KIRI (7 Kolom): Badge Instansi, Headline, Deskripsi, CTA, & Testimoni -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Eyebrow Badge Instansi -->
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-forest-dark/10 text-forest-dark text-xs font-bold border border-forest-dark/20 uppercase tracking-wider">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                SK Dinas Pertanian No. 521.1/KPTS-ALSINTAN
                            </span>
                            <span class="hidden sm:block h-px w-8 bg-forest-dark/30"></span>
                            <span class="text-xs font-bold text-gray-600 tracking-wider uppercase">
                                Tahun Anggaran 2026 &bull; Kabupaten Subang
                            </span>
                        </div>

                        <!-- Editorial Headline -->
                        <h1 class="font-editorial text-3xl sm:text-5xl xl:text-[3.5rem] font-light text-obsidian tracking-tight leading-[1.09]">
                            Menyemai Kesejahteraan
                            <span class="italic font-normal font-editorial text-forest-dark">Petani Subang</span>
                            Melalui Seleksi Bantuan Alsintan Terpadu.
                        </h1>

                        <!-- Deskripsi Singkat -->
                        <p class="text-sm sm:text-base lg:text-lg text-gray-700 leading-relaxed max-w-2xl">
                            Sistem seleksi dan pemeringkatan prioritas penerima bantuan
                            <strong class="text-forest-dark font-semibold">Alat dan Mesin Pertanian (Alsintan)</strong>
                            untuk Kelompok Tani di 30 kecamatan Kabupaten Subang secara transparan, adil, dan akuntabel.
                        </p>

                        <!-- Action Bar CTAs -->
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-between gap-4 px-6 py-3.5 min-h-[48px] rounded-full bg-chartreuse text-obsidian hover:bg-chartreuse-hover active:scale-[0.98] transition-all shadow-md group border border-black/10">
                                <span class="font-extrabold text-sm tracking-tight">Mulai Ajukan Bantuan</span>
                                <span class="w-8 h-8 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center group-hover:rotate-45 transition-transform">
                                    <span class="material-symbols-outlined text-base">north_east</span>
                                </span>
                            </a>
                            <a href="#alur-pengajuan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 min-h-[48px] rounded-full bg-white hover:bg-bone-canvas text-forest-dark border border-forest-dark/25 hover:border-forest-dark text-sm font-bold transition-all shadow-xs">
                                <span class="material-symbols-outlined text-forest-dark text-lg">route</span>
                                <span>Pelajari Alur</span>
                            </a>
                        </div>

                        <!-- Compact Testimonials Carousel Card (Terintegrasi Rapi Tanpa Menumpuk Banner) -->
                        <div class="bg-white rounded-3xl p-5 border border-forest-dark/15 text-obsidian shadow-sm"
                             x-data="{
                                 activeSlide: 0,
                                 totalSlides: 3,
                                 timer: null,
                                 testimonials: [
                                     {
                                         nama: 'Bpk. Sutarman, Ketua Poktan Sri Rahayu (Pagaden)',
                                         skor: 'Vi = 0,892 (Peringkat #1)',
                                         alat: 'Combine Harvester 2 Unit',
                                         quote: 'Transparansi perhitungan sistem TOPSIS ini menghilangkan keraguan kami. Nilai kedekatan solusi ideal dihitung murni dari luas hamparan sawah dan kesiapan operator poktan tanpa ada intervensi subjektif.',
                                         wilayah: 'Pagaden &bull; Pantura Subang'
                                     },
                                     {
                                         nama: 'H. Endang Sujana, Ketua Poktan Sri Mukti (Gunungsari)',
                                         skor: 'Vi = 0,812 (Peringkat #1)',
                                         alat: 'Traktor Roda 2 (Hand Tractor)',
                                         quote: 'Sebelumnya kami sering terlambat mengolah sawah seluas 38,5 Ha sebelum hujan. Dengan pemeringkatan ilmiah TOPSIS, bantuan traktor terbukti tepat sasaran kepada kelompok yang paling mendesak.',
                                         wilayah: 'Gunungsari &bull; Subang Tengah'
                                     },
                                     {
                                         nama: 'Bpk. Wawan Hermawan, Ketua Poktan Tunas Jaya (Ciasem)',
                                         skor: 'Vi = 0,764 (Peringkat #2)',
                                         alat: 'Pompa Air Irigasi 3-4 Inch',
                                         quote: 'Saat musim kemarau di Pantura Subang, sistem ini adil memprioritaskan hamparan sawah tadah hujan kami. Kami kelompok tani juga bisa memantau tahapan persetujuan langsung lewat HP dengan tenang.',
                                         wilayah: 'Ciasem &bull; Pantura Subang'
                                     }
                                 ],
                                 startAutoRotate() {
                                     this.timer = setInterval(() => {
                                         this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                                     }, 5500);
                                 },
                                 stopAutoRotate() {
                                     if (this.timer) clearInterval(this.timer);
                                 },
                                 next() {
                                     this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                                 },
                                 prev() {
                                     this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                                 }
                             }"
                             x-init="startAutoRotate()"
                             @mouseenter="stopAutoRotate()"
                             @mouseleave="startAutoRotate()">

                            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                <div>
                                    <div class="text-[10px] uppercase tracking-wider text-amber-800 font-extrabold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        Profil Penerima Bantuan Terpilih TOPSIS
                                    </div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-forest-dark font-urbanist mt-0.5"
                                        x-text="testimonials[activeSlide].nama">
                                        Bpk. Sutarman, Ketua Poktan Sri Rahayu (Pagaden)
                                    </h3>
                                </div>
                                <div class="shrink-0 text-forest-dark bg-lime-harvest/50 border border-lime-harvest px-2.5 py-1 rounded-full text-[11px] font-black tabular-nums"
                                     x-text="testimonials[activeSlide].skor">
                                    Vi = 0,892 (Peringkat #1)
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-obsidian/90 italic leading-relaxed my-2"
                               x-text="'\u201C' + testimonials[activeSlide].quote + '\u201D'">
                                &ldquo;Transparansi perhitungan sistem TOPSIS ini menghilangkan keraguan kami. Nilai kedekatan solusi ideal dihitung murni dari luas hamparan sawah dan kesiapan operator poktan.&rdquo;
                            </p>

                            <div class="flex items-center justify-between text-xs pt-2.5 border-t border-forest-dark/10 gap-2">
                                <div class="flex items-center gap-1.5 text-forest-dark font-bold text-[11px] min-w-0 truncate">
                                    <span class="material-symbols-outlined text-sm text-emerald-700 shrink-0">verified</span>
                                    <span class="truncate" x-text="testimonials[activeSlide].alat">Combine Harvester 2 Unit</span>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button"
                                            @click="prev()"
                                            aria-label="Testimoni sebelumnya"
                                            class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-bone-canvas hover:bg-celery-cream text-forest-dark border border-bone-slate flex items-center justify-center transition">
                                        <span class="material-symbols-outlined text-base">chevron_left</span>
                                    </button>
                                    <button type="button"
                                            @click="next()"
                                            aria-label="Testimoni selanjutnya"
                                            class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-bone-canvas hover:bg-celery-cream text-forest-dark border border-bone-slate flex items-center justify-center transition">
                                        <span class="material-symbols-outlined text-base">chevron_right</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (5 Kolom): Visual Showcase Petani Subang + Status Akreditasi -->
                    <div class="lg:col-span-5 flex flex-col gap-5">

                        <!-- Kartu Atas: Showcase Foto Petani & Hamparan Sawah (Badge Rapi di Dalam Frame) -->
                        <div class="relative rounded-3xl overflow-hidden border border-forest-dark/20 bg-forest-dark shadow-xl min-h-[320px] sm:min-h-[360px] flex flex-col justify-between p-5 sm:p-6 group">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCanV_Hq3DCJJg4-gaEEKZjcTzNVixk7v90BM04Bc17glF4ZppaTRnzoZIlWrBnueOnv-Tfj12R1nSqUauRw3TPH06E2MnWcN-XxdoA9nQ37hqfcieJNqeZ-7WIeZZRndmJ4Z-FQQqOVZcsJHJw7ddF4nHDwHDeXZNuwNMzV661oFDFKV-opkpvnl9UHGox_9d0q_PSnWk61bHOXgKYv2fVr3t4ng8EkPZY3RhRWDXkCjNufzdu3yLw4krwIVTHro4YsLwkDS8GaAg"
                                 alt="Petani Kabupaten Subang"
                                 class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 brightness-[0.88]">
                            <div class="absolute inset-0 bg-gradient-to-t from-obsidian/95 via-obsidian/45 to-obsidian/20"></div>

                            <!-- Status Badges Terkandung Rapi di Atas -->
                            <div class="relative z-10 flex flex-wrap items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white text-forest-dark text-[11px] font-bold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                    Verifikasi Lapangan Sah (CPCL)
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-forest-dark/90 text-chartreuse text-[11px] font-bold border border-white/15">
                                    <span class="material-symbols-outlined text-xs">public</span>
                                    Spasial GIS
                                </span>
                            </div>

                            <!-- Caption Bawah -->
                            <div class="relative z-10 text-white pt-10">
                                <span class="text-[11px] uppercase tracking-wider text-chartreuse font-bold block">
                                    Zona Lumbung Padi Nasional
                                </span>
                                <div class="font-editorial text-2xl sm:text-3xl font-light text-white leading-tight mt-0.5 tabular-nums">
                                    12.450 Ha Sawah Terpetakan
                                </div>
                                <p class="text-xs text-white/85 mt-1">
                                    Wilayah Pantura, Tengah &amp; Selatan terintegrasi Simluhtan di 30 kecamatan.
                                </p>
                            </div>
                        </div>

                        <!-- Kartu Bawah: Trust & Audit Card (Solid White) -->
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-forest-dark/15 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-4">
                                <div class="w-12 h-12 shrink-0 rounded-2xl bg-forest-dark text-chartreuse flex items-center justify-center shadow-inner">
                                    <span class="material-symbols-outlined text-2xl">workspace_premium</span>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1 text-amber-600">
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="ml-1 text-base font-black text-forest-dark font-urbanist tabular-nums">4,92 / 5,0</span>
                                    </div>
                                    <h4 class="font-bold text-xs sm:text-sm text-obsidian leading-snug">
                                        Kelompok Tani Binaan Resmi Subang
                                    </h4>
                                </div>
                            </div>

                            <div class="pt-3 sm:pt-0 sm:pl-4 border-t sm:border-t-0 sm:border-l border-bone-slate flex sm:flex-col items-center sm:items-end justify-between gap-1 shrink-0">
                                <span class="text-[11px] font-semibold text-gray-600">Status LPJ</span>
                                <span class="text-xs font-extrabold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200 tabular-nums">100% WTP</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- =================================================================== -->
        <!-- SECTION 2: INTELLIGENCE & IMPACT DATA TIERS (Monumental Numerics)   -->
        <!-- =================================================================== -->
        <section class="py-16 bg-white/70 border-b border-forest-dark/10 relative">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs text-amber-800 uppercase tracking-widest font-extrabold">
                        Indikator Capaian Kinerja Utama (IKU)
                    </span>
                    <h2 class="font-editorial text-3xl sm:text-4xl text-obsidian mt-1.5">
                        Akuntabilitas Berbasis Data Lapangan Riil
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2">
                        Parameter agregat pembobotan penerima hibah mesin pertanian di seluruh unit pelaksana dinas.
                    </p>
                </div>

                <!-- 4 Monumental Numerics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-14">
                    <div class="bg-white p-6 rounded-2xl border border-forest-dark/15 shadow-sm relative overflow-hidden group hover:border-forest-dark/35 transition-all">
                        <div class="absolute top-0 left-0 h-1.5 w-full bg-chartreuse"></div>
                        <div class="text-xs font-bold uppercase tracking-wider text-forest-dark mb-2">
                            Efisiensi Panen &amp; Tanam
                        </div>
                        <div class="font-editorial text-4xl sm:text-5xl font-bold text-obsidian tracking-tight tabular-nums">
                            128<span class="text-forest-dark font-sans text-3xl font-light">%</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 leading-snug">
                            Peningkatan Produktivitas Alsintan per Hektar lahan sawah terverifikasi.
                        </p>
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-emerald-800 font-semibold tabular-nums">
                            <span>Rerata Indeks Pertanaman</span>
                            <span>IP 2.4 &rarr; IP 2.8</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-forest-dark/15 shadow-sm relative overflow-hidden group hover:border-forest-dark/35 transition-all">
                        <div class="absolute top-0 left-0 h-1.5 w-full bg-amber-gold"></div>
                        <div class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-2">
                            Baku Lahan Poktan
                        </div>
                        <div class="font-editorial text-4xl sm:text-5xl font-bold text-obsidian tracking-tight tabular-nums">
                            12.450 <span class="text-2xl font-sans font-normal text-gray-600">Ha</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 leading-snug">
                            Luas Lahan Baku Sawah Terverifikasi Spasial polygon BPN &amp; LP2B Subang.
                        </p>
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-700 tabular-nums">
                            <span>Polygon Geotagging</span>
                            <span class="font-mono text-forest-dark font-semibold">100% Selesai</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-forest-dark/15 shadow-sm relative overflow-hidden group hover:border-forest-dark/35 transition-all">
                        <div class="absolute top-0 left-0 h-1.5 w-full bg-forest-dark"></div>
                        <div class="text-xs font-bold uppercase tracking-wider text-forest-dark mb-2">
                            Database Kelembagaan
                        </div>
                        <div class="font-editorial text-4xl sm:text-5xl font-bold text-obsidian tracking-tight tabular-nums">
                            {{ $totalPoktan ?? 418 }} <span class="text-2xl font-sans font-normal text-gray-600">Poktan</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 leading-snug">
                            Kelompok Tani Terdaftar di 30 Kecamatan Subang teregister Simluhtan aktif.
                        </p>
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-emerald-800 font-semibold tabular-nums">
                            <span>Total Usulan Bantuan</span>
                            <span>{{ $totalPengajuan ?? 12 }} Proposal</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-forest-dark/15 shadow-sm relative overflow-hidden group hover:border-forest-dark/35 transition-all">
                        <div class="absolute top-0 left-0 h-1.5 w-full bg-emerald-700"></div>
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-800 mb-2">
                            Anggaran Hibah APBD/DAK
                        </div>
                        <div class="font-editorial text-4xl sm:text-5xl font-bold text-obsidian tracking-tight tabular-nums">
                            Rp 4,2 <span class="text-2xl font-sans font-normal text-gray-600">Miliar</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-3 leading-snug">
                            Alokasi DAK Pertanian Tepat Sasaran tanpa deviasi pengadaan fisik.
                        </p>
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-forest-dark font-semibold tabular-nums">
                            <span>Audit Efisiensi</span>
                            <span class="text-emerald-800">+18.4% Net Output</span>
                        </div>
                    </div>
                </div>

                <!-- 4 Architectural Feature Cards (Solid White Cards, Kontras Bersih) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-forest-dark/15 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-forest-dark text-chartreuse flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined">tune</span>
                            </div>
                            <h3 class="font-bold text-base text-obsidian mb-2">
                                Seleksi 5 Kriteria Objektif
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Penilaian berdasarkan 5 aspek utama:
                                <strong>Luas Lahan</strong>, 
                                <strong>Produktivitas Panen</strong>, 
                                <strong>Kepemilikan Alsintan</strong>, 
                                <strong>Jumlah Anggota</strong>, dan 
                                <strong>Kesiapan Operator</strong>.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-forest-dark/10 text-xs font-mono text-forest-dark font-bold">
                            Total Bobot = 100% (Valid)
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-forest-dark/15 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-forest-dark text-chartreuse flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined">balance</span>
                            </div>
                            <h3 class="font-bold text-base text-obsidian mb-2">
                                Prinsip Pemerataan Berkeadilan
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Kelompok tani yang belum memiliki alsintan akan mendapat prioritas lebih tinggi guna menjamin azas pemerataan bantuan di seluruh wilayah kecamatan.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-forest-dark/10 text-xs font-mono text-emerald-800 font-bold">
                            Keadilan Wilayah Terjamin
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-forest-dark/15 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-forest-dark text-chartreuse flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined">auto_delete</span>
                            </div>
                            <h3 class="font-bold text-base text-obsidian mb-2">
                                Siklus Retensi 13-Bulan
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Berkas proposal dan riwayat penerimaan disimpan aman selama 1 tahun anggaran penuh ditambah 1 bulan masa rekapitulasi audit sebelum jadwal <em>auto-pruning</em> terverifikasi otomatis.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-forest-dark/10 text-xs font-mono text-amber-800 font-bold">
                            Protokol Arsip BPKP / ANRI
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-forest-dark/15 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-forest-dark text-chartreuse flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined">qr_code_2</span>
                            </div>
                            <h3 class="font-bold text-base text-obsidian mb-2">
                                Rekap &amp; BA-Alsintan Resmi
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Lembar Rekapitulasi dan Berita Acara Serah Terima (BAST) berformat A4 resmi Dinas Pertanian Kabupaten Subang siap diunduh dan dicetak dengan stempel digital otentik.
                            </p>
                        </div>
                        <div class="mt-6 pt-3 border-t border-forest-dark/10 text-xs font-mono text-forest-dark font-bold">
                            Format Cetak A4 Standar
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- =================================================================== -->
        <!-- SECTION 3: 6 TAHAP MATRIKS TOPSIS (Mathematical Engine Preview)     -->
        <!-- =================================================================== -->
        {{-- SECTION 3 HIDDEN: 6 Tahap TOPSIS dihilangkan dari landing publik (tetap tersedia di dashboard admin/kepbid)
        <section class="py-20 border-b border-forest-dark/10 relative" id="tahap-topsis">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-8">
                
                <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-xs text-forest-dark uppercase tracking-widest font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-forest-dark"></span>
                            Metodologi Saintifik & Komputasi
                        </span>
                        <h2 class="font-editorial text-3xl sm:text-5xl text-obsidian mt-2 font-normal">
                            Alur 6 Tahap Algoritma TOPSIS
                        </h2>
                    </div>
                    <p class="text-sm text-gray-600 max-w-xl">
                        Dari input proposal CPCL hingga penetapan rekomendasi Kepala Bidang dan Kepala Dinas Pertanian Kabupaten Subang secara objektif.
                    </p>
                </div>

                <!-- Horizontal Flow Step Strip -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Step 1 -->
                    <div class="bg-white p-5 rounded-2xl border border-forest-dark/15 shadow-sm relative group hover:border-forest-dark transition-all">
                        <span class="text-xs font-mono text-amber-gold font-bold block mb-1">01 / TAHAP</span>
                        <h4 class="font-bold text-base text-forest-dark mb-1">Matriks X</h4>
                        <div class="text-xs text-gray-500 mb-2 font-mono">X = [x_ij] (m &times; n)</div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Kompilasi nilai mentah seluruh Poktan terhadap 5 kriteria teknis lapangan.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white p-5 rounded-2xl border border-forest-dark/15 shadow-sm relative group hover:border-forest-dark transition-all">
                        <span class="text-xs font-mono text-amber-gold font-bold block mb-1">02 / TAHAP</span>
                        <h4 class="font-bold text-base text-forest-dark mb-1">Normalisasi R</h4>
                        <div class="text-xs text-gray-500 mb-2 font-mono">r_ij = x_ij / √(Σ x²_kj)</div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Vektorisasi skala agar satuan hektar, unit, dan orang menjadi sepadan.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white p-5 rounded-2xl border border-forest-dark/15 shadow-sm relative group hover:border-forest-dark transition-all">
                        <span class="text-xs font-mono text-amber-gold font-bold block mb-1">03 / TAHAP</span>
                        <h4 class="font-bold text-base text-forest-dark mb-1">Terbobot Y</h4>
                        <div class="text-xs text-gray-500 mb-2 font-mono">y_ij = w_j &times; r_ij</div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Kalibrasi matriks ternormalisasi dengan bobot prioritas dinas pertanian.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-white p-5 rounded-2xl border border-forest-dark/15 shadow-sm relative group hover:border-forest-dark transition-all">
                        <span class="text-xs font-mono text-amber-gold font-bold block mb-1">04 / TAHAP</span>
                        <h4 class="font-bold text-base text-forest-dark mb-1">Ideal A⁺ / A⁻</h4>
                        <div class="text-xs text-gray-500 mb-2 font-mono">A⁺ = max(y), A⁻ = min(y)</div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Penetapan batas solusi ideal positif dan batas solusi ideal negatif.
                        </p>
                    </div>

                    <!-- Step 5 -->
                    <div class="bg-white p-5 rounded-2xl border border-forest-dark/15 shadow-sm relative group hover:border-forest-dark transition-all">
                        <span class="text-xs font-mono text-amber-gold font-bold block mb-1">05 / TAHAP</span>
                        <h4 class="font-bold text-base text-forest-dark mb-1">Euclidean D</h4>
                        <div class="text-xs text-gray-500 mb-2 font-mono">D⁺ = √Σ(y_ij - y⁺_j)²</div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Jarak geometrik alternatif terhadap titik solusi ekstrem ideal.
                        </p>
                    </div>

                    <!-- Step 6 -->
                    <div class="bg-forest-dark text-white p-5 rounded-2xl border border-forest-dark shadow-md relative group">
                        <span class="text-xs font-mono text-chartreuse font-bold block mb-1">06 / FINAL</span>
                        <h4 class="font-bold text-base text-white mb-1">Nilai Preferensi (<i>V</i><sub>i</sub>)</h4>
                        <div class="text-xs text-chartreuse mb-2 font-mono"><i>V</i><sub>i</sub> = <i>D</i><sup>&minus;</sup> / (<i>D</i><sup>&minus;</sup> + <i>D</i><sup>+</sup>)</div>
                        <p class="text-xs text-white/80 leading-relaxed">
                            Skor preferensi akhir [0.0000 - 1.0000]. Nilai tertinggi berhak menerima Alsintan.
                        </p>
                    </div>
                </div>

                <!-- Sample Live Verification Matrix Preview -->
                <div class="mt-12 frosted-panel rounded-3xl p-6 sm:p-8 border border-forest-dark/20 shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-forest-dark/10">
                        <div>
                            <span class="text-xs text-emerald-800 font-bold uppercase tracking-wider">
                                Simulasi Transparansi Keputusan
                            </span>
                            <h3 class="text-lg font-bold text-obsidian">
                                Tabel Sampel Pemeringkatan (<i>V</i><sub>i</sub>) Alsintan Kabupaten Subang
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded bg-forest-dark text-chartreuse text-xs font-mono font-bold">
                                Status: Validasi TOPSIS Aktif
                            </span>
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-white border border-forest-dark/20 hover:bg-forest-dark/5 text-xs text-forest-dark font-semibold inline-flex items-center gap-1.5 transition-colors">
                                <span class="material-symbols-outlined text-sm">calculate</span>
                                Masuk ke Hitung TOPSIS
                            </a>
                        </div>
                    </div>

                    <!-- Mobile Card View (< md): Bebas Geser Samping -->
                    <div class="space-y-3 mt-5 md:hidden">
                        @php
                            $sampelLanding = [
                                ['rank' => 1, 'poktan' => 'Poktan Sri Rahayu', 'kec' => 'Pagaden', 'lahan' => '68.5 Ha', 'prod' => '6.8 Ton/Ha', 'eksis' => '0 Unit', 'vi' => '0.8924', 'alat' => 'Traktor Roda 4 (TR4)', 'top' => true],
                                ['rank' => 2, 'poktan' => 'Poktan Berkah', 'kec' => 'Purwadadi', 'lahan' => '25.0 Ha', 'prod' => '6.2 Ton/Ha', 'eksis' => '0 Unit', 'vi' => '0.8140', 'alat' => 'Combine Harvester', 'top' => false],
                                ['rank' => 3, 'poktan' => 'Poktan Bina Warga', 'kec' => 'Ciasem', 'lahan' => '49.2 Ha', 'prod' => '5.5 Ton/Ha', 'eksis' => '0 Unit', 'vi' => '0.7815', 'alat' => 'Pompa Air 6 Inch', 'top' => false],
                                ['rank' => 4, 'poktan' => 'Poktan Tani Jaya Mukti', 'kec' => 'Binong', 'lahan' => '41.8 Ha', 'prod' => '5.2 Ton/Ha', 'eksis' => '1 Unit', 'vi' => '0.7208', 'alat' => 'Rice Transplanter', 'top' => false],
                                ['rank' => 5, 'poktan' => 'Poktan Suka Maju', 'kec' => 'Compreng', 'lahan' => '37.0 Ha', 'prod' => '5.0 Ton/Ha', 'eksis' => '2 Unit', 'vi' => '0.6930', 'alat' => 'Traktor Roda 2 (TR2)', 'top' => false],
                            ];
                        @endphp
                        @foreach($sampelLanding as $s)
                            <div class="p-4 rounded-2xl bg-white border border-forest-dark/15 shadow-2xs space-y-2 text-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-full inline-flex items-center justify-center text-xs font-black {{ $s['top'] ? 'bg-chartreuse text-obsidian' : 'bg-gray-200 text-obsidian' }}">{{ $s['rank'] }}</span>
                                        <div>
                                            <div class="font-bold text-obsidian">{{ $s['poktan'] }}</div>
                                            <div class="text-[11px] text-gray-600">Kec. {{ $s['kec'] }}</div>
                                        </div>
                                    </div>
                                    <span class="font-mono tabular-nums font-bold text-forest-dark bg-celery-cream px-2 py-0.5 rounded-lg border border-bone-slate">
                                        <i>V</i><sub>i</sub> {{ $s['vi'] }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-forest-dark/10 text-[11px] text-gray-600 font-mono tabular-nums">
                                    <span>{{ $s['lahan'] }} &bull; {{ $s['prod'] }} &bull; Eksis {{ $s['eksis'] }}</span>
                                    <span class="font-sans font-semibold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $s['alat'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Responsive Data Table (Desktop & Tablet >= md) -->
                    <div class="hidden md:block overflow-x-auto mt-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs uppercase tracking-wider text-forest-dark border-b border-forest-dark/10 bg-forest-dark/5">
                                    <th class="py-3 px-4 rounded-l-lg font-bold">Peringkat</th>
                                    <th class="py-3 px-4 font-bold">Nama Poktan & Desa</th>
                                    <th class="py-3 px-4 font-bold">Kecamatan</th>
                                    <th class="py-3 px-4 font-bold text-right">Lahan (C1)</th>
                                    <th class="py-3 px-4 font-bold text-right">Produktivitas (C2)</th>
                                    <th class="py-3 px-4 font-bold text-center">Alsintan Eksisting (C3)</th>
                                    <th class="py-3 px-4 font-bold text-right">Skor Preferensi (<i>V</i><sub>i</sub>)</th>
                                    <th class="py-3 px-4 rounded-r-lg font-bold text-center">Rekomendasi Bantuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-forest-dark/10 text-xs">
                                <tr class="hover:bg-forest-dark/[0.02] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-forest-dark">
                                        <span class="w-6 h-6 rounded-full bg-chartreuse text-obsidian inline-flex items-center justify-center text-xs font-black">1</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-obsidian">Poktan Sri Rahayu</td>
                                    <td class="py-3.5 px-4 text-gray-600">Pagaden</td>
                                    <td class="py-3.5 px-4 text-right font-mono">68.5 Ha</td>
                                    <td class="py-3.5 px-4 text-right font-mono">6.8 Ton/Ha</td>
                                    <td class="py-3.5 px-4 text-center font-mono">0 Unit (Prioritas)</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-forest-dark text-sm">0.8924</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Traktor Roda 4 (TR4)
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-forest-dark/[0.02] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-forest-dark">
                                        <span class="w-6 h-6 rounded-full bg-gray-200 text-obsidian inline-flex items-center justify-center text-xs font-bold">2</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-obsidian">Poktan Berkah</td>
                                    <td class="py-3.5 px-4 text-gray-600">Purwadadi</td>
                                    <td class="py-3.5 px-4 text-right font-mono">25.0 Ha</td>
                                    <td class="py-3.5 px-4 text-right font-mono">6.2 Ton/Ha</td>
                                    <td class="py-3.5 px-4 text-center font-mono">0 Unit (Prioritas)</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-forest-dark text-sm">0.8140</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Combine Harvester
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-forest-dark/[0.02] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-forest-dark">
                                        <span class="w-6 h-6 rounded-full bg-gray-200 text-obsidian inline-flex items-center justify-center text-xs font-bold">3</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-obsidian">Poktan Bina Warga</td>
                                    <td class="py-3.5 px-4 text-gray-600">Ciasem</td>
                                    <td class="py-3.5 px-4 text-right font-mono">49.2 Ha</td>
                                    <td class="py-3.5 px-4 text-right font-mono">5.5 Ton/Ha</td>
                                    <td class="py-3.5 px-4 text-center font-mono">0 Unit (Prioritas)</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-forest-dark text-sm">0.7815</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Pompa Air 6 Inch
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-forest-dark/[0.02] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-forest-dark">
                                        <span class="w-6 h-6 rounded-full bg-gray-200 text-obsidian inline-flex items-center justify-center text-xs font-bold">4</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-obsidian">Poktan Tani Jaya Mukti</td>
                                    <td class="py-3.5 px-4 text-gray-600">Binong</td>
                                    <td class="py-3.5 px-4 text-right font-mono">41.8 Ha</td>
                                    <td class="py-3.5 px-4 text-right font-mono">5.2 Ton/Ha</td>
                                    <td class="py-3.5 px-4 text-center font-mono">1 Unit</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-forest-dark text-sm">0.7208</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            Rice Transplanter
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-forest-dark/[0.02] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-forest-dark">
                                        <span class="w-6 h-6 rounded-full bg-gray-200 text-obsidian inline-flex items-center justify-center text-xs font-bold">5</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-obsidian">Poktan Suka Maju</td>
                                    <td class="py-3.5 px-4 text-gray-600">Compreng</td>
                                    <td class="py-3.5 px-4 text-right font-mono">37.0 Ha</td>
                                    <td class="py-3.5 px-4 text-right font-mono">5.0 Ton/Ha</td>
                                    <td class="py-3.5 px-4 text-center font-mono">2 Unit</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-bold text-forest-dark text-sm">0.6930</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                            Traktor Roda 2 (TR2)
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
        END SECTION 3 HIDDEN --}}

        <!-- =================================================================== -->
        <!-- SECTION 4: ALUR PENGAJUAN POKTAN & CEK STATUS                       -->
        <!-- =================================================================== -->
        <section class="py-20 bg-white/70 border-b border-forest-dark/10 relative" id="alur-pengajuan">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    
                    <!-- Left: Alur Pengajuan Guide -->
                    <div class="lg:col-span-6">
                        <span class="text-xs text-forest-dark uppercase tracking-widest font-bold">
                            Pedoman Resmi Kelompok Tani
                        </span>
                        <h2 class="font-editorial text-3xl sm:text-5xl text-obsidian mt-2 font-normal">
                            Alur Pengajuan Hibah Alsintan Kabupaten Subang
                        </h2>
                        <p class="text-sm text-gray-600 mt-4 leading-relaxed">
                            Setiap kelompok tani yang terdaftar di Simluhtan berhak mengajukan usulan bantuan melalui koordinasi dengan Penyuluh Pertanian Lapangan (PPL) di BPP masing-masing kecamatan.
                        </p>

                        <div class="mt-8 space-y-6">
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center font-bold text-sm shrink-0">1</div>
                                <div>
                                    <h4 class="font-bold text-base text-obsidian">Registrasi & Usulan Daring Poktan</h4>
                                    <p class="text-xs text-gray-600">Kelompok Tani (Poktan) mendaftar akun, melengkapi profil wilayah, dan mengunggah berkas proposal permohonan Alsintan.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center font-bold text-sm shrink-0">2</div>
                                <div>
                                    <h4 class="font-bold text-base text-obsidian">Verifikasi Berkas & CPCL oleh Admin Dinas</h4>
                                    <p class="text-xs text-gray-600">Pemeriksaan Calon Petani Calon Lokasi (CPCL), validasi luas hamparan sawah, dan kesiapan operator Kelompok Tani (Poktan).</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center font-bold text-sm shrink-0">3</div>
                                <div>
                                    <h4 class="font-bold text-base text-obsidian">Kalkulasi Matematis SPK TOPSIS</h4>
                                    <p class="text-xs text-gray-600">Sistem memproses normalisasi, pembobotan, dan jarak Euclidean untuk menghasilkan peringkat preferensi (<i>V</i><sub>i</sub>) secara transparan.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-chartreuse text-obsidian flex items-center justify-center font-bold text-sm shrink-0 border border-forest-dark/20">4</div>
                                <div>
                                    <h4 class="font-bold text-base text-obsidian">Persetujuan Pimpinan & Cetak Rekap BAST</h4>
                                    <p class="text-xs text-gray-600">Kepala Bidang mengesahkan persetujuan (ACC), penerima otomatis masuk rekapitulasi, dan dokumen siap dicetak.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Dedicated Portal Entry & Access Gateway -->
                    <div class="lg:col-span-6" id="portal-layanan">
                        <div class="rounded-3xl p-6 sm:p-8 border border-forest-dark/30 shadow-2xl relative overflow-hidden text-white">
                            <img src="{{ asset('images/bg-grain-green.jpg') }}" alt="" aria-hidden="true"
                                 class="absolute inset-0 w-full h-full object-cover pointer-events-none">
                            <div class="absolute inset-0 pointer-events-none"
                                 style="background: linear-gradient(135deg, rgba(20, 83, 45, 0.92), rgba(15, 63, 34, 0.96));"></div>
                            <div class="relative z-10 space-y-6">
                            
                            <!-- Header Info -->
                            <div class="flex items-center justify-between pb-5 border-b border-white/15">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white/10 text-chartreuse flex items-center justify-center shadow-inner border border-white/20">
                                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-lg text-white font-urbanist">Portal Pelayanan Bantuan Alsintan</h3>
                                        <p class="text-xs text-chartreuse/90">Dinas Pertanian Pemerintah Daerah Kabupaten Subang</p>
                                    </div>
                                </div>
                                <span class="inline-flex px-3 py-1 rounded-full bg-chartreuse/20 text-chartreuse text-[11px] font-bold border border-chartreuse/30">
                                    Akses Terverifikasi
                                </span>
                            </div>

                            <!-- Explanation on Privacy & Account Flow -->
                            <div class="space-y-3 text-xs text-white/90 leading-relaxed">
                                <p>
                                    Untuk menjamin kerahasiaan data kelembagaan tani dan akuntabilitas perhitungan seleksi matematis TOPSIS, seluruh data usulan, berkas CPCL, dan pelacakan persetujuan hanya dapat diakses melalui <strong>Portal Resmi</strong> setelah masuk ke akun masing-masing.
                                </p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-1 shadow-sm">
                                        <div class="flex items-center gap-1.5 font-bold text-chartreuse text-xs">
                                            <span class="material-symbols-outlined text-base">groups</span>
                                            Kelompok Tani (Poktan)
                                        </div>
                                        <p class="text-[11px] text-white/80">Ajukan permohonan Alsintan secara mandiri, pantau status verifikasi, dan pemeringkatan secara transparan.</p>
                                    </div>
                                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-1 shadow-sm">
                                        <div class="flex items-center gap-1.5 font-bold text-chartreuse text-xs">
                                            <span class="material-symbols-outlined text-base">shield_person</span>
                                            Dinas & Pimpinan
                                        </div>
                                        <p class="text-[11px] text-white/80">Verifikasi berkas, olah algoritma 6 langkah TOPSIS, dan sahkan SK penyaluran bantuan.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Primary CTAs -->
                            <div class="space-y-3 pt-2">
                                <a href="{{ route('login') }}" 
                                   class="w-full py-4 px-6 rounded-full bg-chartreuse hover:bg-chartreuse-hover text-obsidian font-extrabold text-sm transition shadow-lg flex items-center justify-between group active:scale-[0.99] border border-black/10">
                                    <span class="tracking-tight">Masuk ke Portal untuk Mengajukan & Memantau Usulan</span>
                                    <span class="w-8 h-8 rounded-full bg-forest-dark text-chartreuse flex items-center justify-center text-xs group-hover:rotate-45 transition-transform">
                                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                                    </span>
                                </a>

                                <a href="{{ route('register') }}" 
                                   class="w-full py-3 px-6 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/25 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base text-chartreuse">person_add</span>
                                    <span>Belum Memiliki Akun? Daftarkan Kelompok Tani Baru (Mandiri)</span>
                                </a>
                            </div>

                            <!-- Security / Standard Badge -->
                            <div class="pt-2 flex items-center justify-between text-[11px] text-white/70 border-t border-white/15">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs text-chartreuse">lock</span>
                                    Enkripsi Data Sesuai Standar Kearsipan ANRI
                                </span>
                                <span>Retensi Berkas 13-Bulan</span>
                            </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- =================================================================== -->
        <!-- SECTION 5: RETENSI 13-BULAN & ARSIP DIGITAL (Regulatory Governance) -->
        <!-- =================================================================== -->
        <section class="py-16 border-b border-forest-dark/10 relative" id="retensi">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-8">
                <div class="frosted-panel rounded-3xl p-8 sm:p-12 border border-forest-dark/20 relative overflow-hidden bg-gradient-to-br from-white via-sage-bone/40 to-white">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-8">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-forest-dark/10 text-forest-dark text-xs font-bold uppercase tracking-wider mb-4">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                Manajemen Siklus Hidup Arsip Elektronik
                            </span>
                            <h2 class="font-editorial text-3xl sm:text-4xl text-obsidian leading-tight">
                                Kebijakan Retensi Berkas 13-Bulan & Auto-Pruning Akuntabel
                            </h2>
                            <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                                Selaras dengan Permendagri No. 78/2012 dan Standar Kearsipan Nasional (ANRI). Sistem menyimpan dokumen proposal, riwayat penilaian TOPSIS, dan Berita Acara selama 12 bulan masa anggaran berjalan ditambah 1 bulan masa toleransi rekapitulasi LPJ audit Inspektorat Daerah sebelum masuk ke arsip kedaluwarsa.
                            </p>
                            <div class="flex flex-wrap gap-4 mt-6">
                                <div class="flex items-center gap-2 text-xs text-forest-dark font-semibold">
                                    <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                                    <span>Integritas SHA-256 Checksum</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-forest-dark font-semibold">
                                    <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                                    <span>Notifikasi Pra-Kedaluwarsa H-30</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-forest-dark font-semibold">
                                    <span class="material-symbols-outlined text-base text-emerald-600">check_circle</span>
                                    <span>Ekspor Rekapitulasi Tahunan PDF</span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-4 flex flex-col items-center justify-center p-6 rounded-2xl text-white text-center shadow-lg relative overflow-hidden border border-forest-dark/30">
                            <img src="{{ asset('images/bg-grain-green.jpg') }}" alt="" aria-hidden="true"
                                 class="absolute inset-0 w-full h-full object-cover pointer-events-none">
                            <div class="absolute inset-0 pointer-events-none"
                                 style="background: linear-gradient(145deg, rgba(20, 83, 45, 0.88), rgba(15, 63, 34, 0.94));"></div>
                            <span class="relative z-10 material-symbols-outlined text-4xl text-chartreuse mb-2">policy</span>
                            <div class="relative z-10 text-base font-bold text-white">Siklus Audit Terjadwal</div>
                            <p class="relative z-10 text-xs text-white/80 mt-1 mb-4 leading-relaxed">
                                Memastikan database tetap ringan, efisien, dan bersih dari data usulan lampau yang telah terealisasi.
                            </p>
                            <a href="{{ route('login') }}" class="relative z-10 px-5 py-2.5 rounded-full bg-chartreuse text-obsidian font-bold text-xs hover:bg-chartreuse-hover transition-all flex items-center gap-1.5 shadow">
                                <span class="material-symbols-outlined text-sm">login</span>
                                <span>Masuk Portal</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- =================================================================== -->
    <!-- MUNICIPAL FOOTER (Dinas Pertanian Pemerintah Kabupaten Subang)       -->
    <!-- =================================================================== -->
    <footer class="bg-forest-dark text-white pt-16 pb-12 border-t-4 border-chartreuse relative z-20">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-white/10">
                <!-- Identity Col -->
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                             alt="Logo Pemkab Subang" 
                             class="h-12 w-auto object-contain filter drop-shadow">
                        <div>
                            <div class="text-base font-bold text-white tracking-tight leading-none">
                                Pemerintah Kabupaten Subang
                            </div>
                            <div class="text-xs font-semibold text-chartreuse uppercase tracking-wider mt-1">
                                Dinas Pertanian
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-white/80 leading-relaxed mb-4">
                        Portal Resmi Sistem Penunjang Keputusan (SPK) Alokasi Hibah Bantuan Alat dan Mesin Pertanian (Alsintan) berbasis Metode TOPSIS untuk kedaulatan dan swasembada pangan berkelanjutan.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-white/90">
                        <span class="w-2 h-2 rounded-full bg-chartreuse"></span>
                        <span>Motto Daerah: <em>Karya Utama Satya Nagara</em></span>
                    </div>
                </div>

                <!-- Kantor & Alamat -->
                <div class="lg:col-span-3">
                    <div class="text-xs font-bold text-chartreuse uppercase tracking-wider mb-4">
                        Kantor Dinas Pertanian
                    </div>
                    <p class="text-xs text-white/85 leading-relaxed">
                        Jl. KS Tubun No. 2, Cigadung<br>
                        Kecamatan Subang, Kabupaten Subang<br>
                        Jawa Barat 41211, Indonesia
                    </p>
                    <div class="mt-4 space-y-1.5 text-xs text-white/80">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-chartreuse">call</span>
                            <span>(0260) 411032 / 411033</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-chartreuse">mail</span>
                            <span>dispertan@subangkab.go.id</span>
                        </div>
                    </div>
                </div>

                <!-- Wilayah Pertanian Subang -->
                <div class="lg:col-span-2">
                    <div class="text-xs font-bold text-chartreuse uppercase tracking-wider mb-4">
                        Wilayah Komoditas
                    </div>
                    <ul class="space-y-2 text-xs text-white/80">
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-chartreuse">chevron_right</span>
                            <span>Zona Pantura (Padi Sawah)</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-chartreuse">chevron_right</span>
                            <span>Zona Tengah (Palawija & Padi)</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-chartreuse">chevron_right</span>
                            <span>Zona Selatan (Hortikultura)</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-xs text-chartreuse">chevron_right</span>
                            <span>30 Kantor BPP Kecamatan</span>
                        </li>
                    </ul>
                </div>

                <!-- Layanan Terpadu -->
                <div class="lg:col-span-3">
                    <div class="text-xs font-bold text-chartreuse uppercase tracking-wider mb-4">
                        Akses Portal Layanan
                    </div>
                    <div class="space-y-2 text-xs">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition">
                            &rarr; Masuk ke Portal Akun
                        </a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition">
                            &rarr; Registrasi Mandiri Kelompok Tani
                        </a>
                    </div>
                    <div class="mt-4 p-3 rounded-xl bg-white/5 border border-white/10 text-[11px] text-white/70">
                        Sistem Informasi Penyaluran Bantuan Alsintan (Metode TOPSIS) &bull; Dinas Pertanian Pemerintah Daerah Kabupaten Subang
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-white/60">
                <div>
                    &copy; 2026 Dinas Pertanian Pemerintah Kabupaten Subang. Seluruh hak cipta dilindungi.
                </div>
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <span>Standar Pelayanan Publik</span>
                    <span>Karya Utama Satya Nagara</span>
                </div>
            </div>

        </div>
    </footer>

    @if(app()->environment(['local', 'testing']) || config('app.debug'))
    <!-- Floating Discreet Role Switcher (Development/Demo Only, Touch & Desktop Friendly) -->
    <div x-data="{ openSwitcher: false }" @click.outside="openSwitcher = false" class="fixed bottom-4 right-4 z-40">
        <div class="relative flex flex-col items-end">
            <div x-show="openSwitcher" x-cloak x-transition
                 class="flex flex-col gap-1.5 p-2 bg-obsidian/95 backdrop-blur-md rounded-2xl border border-bone-slate shadow-2xl mb-2 text-xs text-white min-w-[170px]">
                <span class="text-[10px] uppercase font-bold tracking-wider text-celery-cream px-2 py-0.5">Simulasi Akses Peran:</span>
                <a href="{{ route('demo.login', 'admin') }}" class="px-3 py-2 min-h-[44px] rounded-xl bg-blue-600/80 hover:bg-blue-600 flex items-center gap-1.5 font-bold transition">
                    <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                    Admin Dinas
                </a>
                <a href="{{ route('demo.login', 'kepbid') }}" class="px-3 py-2 min-h-[44px] rounded-xl bg-amber-600/85 hover:bg-amber-600 flex items-center gap-1.5 font-bold transition">
                    <span class="material-symbols-outlined text-sm">workspace_premium</span>
                    Kepala Bidang
                </a>
                <a href="{{ route('demo.login', 'poktan') }}" class="px-3 py-2 min-h-[44px] rounded-xl bg-emerald-600/80 hover:bg-emerald-600 flex items-center gap-1.5 font-bold transition">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    Poktan Berkah
                </a>
            </div>
            <button type="button" @click="openSwitcher = !openSwitcher" aria-label="Buka menu ganti peran demo"
                    class="px-4 py-2.5 min-h-[44px] rounded-full bg-forest-dark/95 hover:bg-forest-dark text-chartreuse border border-white/20 shadow-lg backdrop-blur-md flex items-center gap-1.5 text-xs font-bold transition">
                <span class="material-symbols-outlined text-sm">swap_horiz</span>
                <span>Ganti Peran</span>
            </button>
        </div>
    </div>
    @endif

    <!-- Interactive Navbar ScrollSpy & Active Tab Switching Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('#landing-navbar .nav-item');
            if (!navLinks.length) return;

            // Target section elements
            const sections = [];
            navLinks.forEach(link => {
                const targetId = link.getAttribute('data-target') || link.getAttribute('href').replace('#', '');
                const section = document.getElementById(targetId);
                if (section) {
                    sections.push({ id: targetId, el: section, link: link });
                }
            });

            const activeClasses = ['text-forest-dark', 'font-bold', 'border-forest-dark'];
            const inactiveClasses = ['text-gray-600', 'font-semibold', 'border-transparent'];

            function setActiveLink(activeLink) {
                navLinks.forEach(link => {
                    if (link === activeLink) {
                        link.classList.remove(...inactiveClasses);
                        link.classList.add(...activeClasses);
                    } else {
                        link.classList.remove(...activeClasses);
                        link.classList.add(...inactiveClasses);
                    }
                });
            }

            // Click Handler: Smooth Scroll + Immediate Tab Active Highlight
            navLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('data-target') || this.getAttribute('href').replace('#', '');
                    const targetEl = document.getElementById(targetId);
                    if (targetEl) {
                        e.preventDefault();
                        setActiveLink(this);

                        const headerOffset = 88;
                        const elementPosition = targetEl.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        if (history.pushState) {
                            history.pushState(null, null, '#' + targetId);
                        }
                    }
                });
            });

            // ScrollSpy: Real-Time Active Tab on Scrolling
            let isTicking = false;
            function updateScrollSpy() {
                const scrollPosition = window.pageYOffset + 120; // sticky header detection offset

                let currentSection = sections[0];
                for (let i = 0; i < sections.length; i++) {
                    const section = sections[i];
                    const top = section.el.offsetTop;
                    const height = section.el.offsetHeight;

                    if (scrollPosition >= top && scrollPosition < top + height) {
                        currentSection = section;
                        break;
                    } else if (scrollPosition >= top) {
                        currentSection = section;
                    }
                }

                if (currentSection && currentSection.link) {
                    setActiveLink(currentSection.link);
                }
                isTicking = false;
            }

            window.addEventListener('scroll', function () {
                if (!isTicking) {
                    window.requestAnimationFrame(updateScrollSpy);
                    isTicking = true;
                }
            }, { passive: true });

            // Initial check
            updateScrollSpy();
        });

        // Live Time Script
        function updateClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta', hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const timeStr = now.toLocaleTimeString('id-ID', options);
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.textContent = `${timeStr} WIB • Subang, Jawa Barat`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>

