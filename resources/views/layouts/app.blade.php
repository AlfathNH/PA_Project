<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden w-full max-w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Bantuan Alsintan') - Dinas Pertanian Kab. Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">

    <!-- Pre-compiled Tailwind CSS Engine (First-Party) -->
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}?v={{ file_exists(public_path('css/tailwind.min.css')) ? filemtime(public_path('css/tailwind.min.css')) : time() }}">

    <!-- Alpine.js (Terbaru v3.17.5 dengan Subresource Integrity SHA-384) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.5/dist/cdn.min.js"
            integrity="sha384-FYLKjwG3aohNhNZoVG8sY/5ntunm+MqylL90Q+rT3OM7AwV9/BKFjk/RjoIpIgsI"
            crossorigin="anonymous"></script>

    <!-- Local Self-Hosted Fonts & Material Symbols -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ file_exists(public_path('css/fonts.css')) ? filemtime(public_path('css/fonts.css')) : time() }}">
    <!-- Font Awesome (v6.7.2 dengan Subresource Integrity SHA-384) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
          integrity="sha384-nRgPTkuX86pH8yjPJUAFuASXQSSl2/bBUiNV47vSYpKFxHJhbcrGnmlYpYJMeD7a"
          crossorigin="anonymous"
          referrerpolicy="no-referrer">

    <style>
        body { 
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #F4F5ED;
            color: #1C241D;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        .frosted-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(227, 228, 214, 0.9);
        }
        .topographic-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(20, 83, 45, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(20, 83, 45, 0.035) 1px, transparent 1px);
        }
        /* Hilangkan spinner bawaan browser pada input number berkelas no-spin */
        .no-spin::-webkit-inner-spin-button,
        .no-spin::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .no-spin {
            -moz-appearance: textfield;
            appearance: textfield;
        }
        [x-cloak] {
            display: none !important;
        }
        @keyframes alertCountdown {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<body class="bg-bone-canvas text-obsidian antialiased min-h-screen flex flex-col topographic-grid overflow-x-hidden w-full max-w-full">

    <!-- Top Navigation Bar (Dinas Pertanian Subang Modern Agrarian Header - Mobile Sticky Topbar, Desktop Condenses to Floating Pill on Scroll) -->
    <nav class="bg-forest-dark text-white shadow-md sticky top-0 lg:relative z-40 border-b border-forest-deep">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                
                <!-- Brand & Logo Pemkab Subang (Responsive Sizing Anti-Tumpuk) -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2.5 sm:space-x-3.5 group shrink-0">
                    <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                         alt="Logo Pemkab Subang" 
                         class="h-9 sm:h-12 w-auto object-contain transition-transform group-hover:scale-105 filter drop-shadow">
                    
                    <!-- Text Ringkas di Mobile (< sm) -->
                    <div class="border-l border-emerald-700/60 pl-2.5 sm:hidden">
                        <span class="font-extrabold text-xs tracking-tight block leading-tight text-white font-urbanist">
                            SPK ALSINTAN
                        </span>
                        <span class="text-[9px] text-celery-cream block font-medium">
                            Distan Subang
                        </span>
                    </div>

                    <!-- Text Lengkap di Tablet & Desktop (>= sm) -->
                    <div class="border-l border-emerald-700/60 pl-3 hidden sm:block">
                        <span class="font-extrabold text-sm sm:text-base tracking-tight block leading-tight text-white font-urbanist">
                            SPK BANTUAN ALSINTAN
                        </span>
                        <span class="text-[11px] text-celery-cream block font-medium tracking-wide">
                            Dinas Pertanian Pemerintah Kabupaten Subang
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (>= lg) -->
                @auth
                    @php
                        $role = Auth::user()->role;
                        $roleLabel = match($role) {
                            'admin'         => 'Admin Verifikator',
                            'kepala_bidang' => 'Kepala Bidang',
                            'kelompok_tani' => 'Kelompok Tani',
                            default         => $role,
                        };
                        $roleColor = match($role) {
                            'admin'         => 'bg-blue-500/20 text-blue-200 border-blue-400/40',
                            'kepala_bidang' => 'bg-amber-harvest/20 text-amber-200 border-amber-400/40',
                            'kelompok_tani' => 'bg-lime-harvest/20 text-lime-harvest border-lime-harvest/40',
                            default         => 'bg-slate-500/20 text-slate-300',
                        };
                    @endphp

                    <!-- Menu Horizontal Desktop (Tampil Hanya di Layar Besar lg ke atas) -->
                    <div class="hidden lg:flex items-center space-x-2 min-w-0">
                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shrink-0 whitespace-nowrap {{ $roleColor }}">
                            <span class="material-symbols-outlined text-sm mr-1.5">verified_user</span> 
                            {{ $roleLabel }}
                        </span>

                        <!-- User Profile Info (Tampil Penuh di xl untuk Mencegah Header Penuh di 1024px) -->
                        <div class="hidden xl:block text-right pr-2 max-w-[190px] min-w-0">
                            <span class="text-xs sm:text-sm font-bold block text-white leading-tight truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                            <span class="text-[11px] text-celery-cream/80 block truncate" title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</span>
                        </div>

                        <!-- Role Navigation Shortcuts -->
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                               class="px-3 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.pengajuan.index') }}" 
                               class="px-3 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('admin.pengajuan.*') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">checklist</span>
                                <span>Verifikasi & Nilai</span>
                            </a>
                            <a href="{{ route('admin.topsis.index') }}" 
                               class="px-3 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('admin.topsis.*') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">calculate</span>
                                <span>SPK TOPSIS</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" 
                               class="px-3 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">manage_accounts</span>
                                <span>Kelola Akun</span>
                            </a>
                        @elseif(Auth::user()->isKepBid())
                            <a href="{{ route('kepbid.dashboard') }}" 
                               class="px-3.5 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('kepbid.dashboard') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">approval</span>
                                <span>Rekomendasi & ACC</span>
                            </a>
                            <a href="{{ route('kepbid.riwayat') }}" 
                               class="px-3.5 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('kepbid.riwayat') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">inventory_2</span>
                                <span>Riwayat & Rekap</span>
                            </a>
                            <a href="{{ route('kepbid.ulasan') }}" 
                               class="px-3.5 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('kepbid.ulasan') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">hotel_class</span>
                                <span>Survei IKM &amp; Ulasan</span>
                            </a>
                        @else
                            <a href="{{ route('poktan.dashboard') }}" 
                               class="px-3.5 py-2 rounded-full text-xs font-bold transition flex items-center gap-1.5 shrink-0 whitespace-nowrap {{ request()->routeIs('poktan.dashboard') ? 'bg-lime-harvest text-obsidian shadow-sm' : 'bg-white/10 hover:bg-white/20 text-white' }}">
                                <span class="material-symbols-outlined text-sm">edit_document</span>
                                <span>Usulan Bantuan</span>
                            </a>
                        @endif

                        <!-- Logout Form Desktop -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari sistem" aria-label="Keluar dari sistem"
                                    class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-white/10 hover:bg-red-600/80 text-white flex items-center justify-center transition">
                                <span class="material-symbols-outlined text-base">logout</span>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Top Bar Controls (< lg): Role Badge & Quick Logout (Navigasi Utama ada di Bottom Bar Melengkung) -->
                    <div class="flex lg:hidden items-center space-x-2">
                        <!-- Role Badge Ringkas Mobile -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $roleColor }}">
                            {{ $roleLabel }}
                        </span>

                        <!-- Quick Logout Mobile -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari sistem" aria-label="Keluar dari sistem"
                                    class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-xl bg-white/10 hover:bg-red-600/80 text-white flex items-center justify-center transition border border-white/15">
                                <span class="material-symbols-outlined text-base">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-bold text-xs transition shadow-sm">
                        Masuk Portal
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    @auth
        <!-- DESKTOP SCROLL FLOATING PILL (Icon-Only Logo UI, Muncul Otomatis Saat Scroll ke Bawah di Komputer) -->
        <div x-data="{ scrolled: false, poktanSection: 'status' }"
             x-init="scrolled = window.scrollY > 72; window.addEventListener('scroll', () => { scrolled = window.scrollY > 72 }, { passive: true })"
             x-cloak
             :class="scrolled ? 'opacity-100 translate-y-0 scale-100' : 'opacity-0 -translate-y-4 scale-95 pointer-events-none'"
             class="hidden lg:flex fixed top-3 inset-x-0 z-40 justify-center px-4 pointer-events-none transition-all duration-200 ease-out">
            <nav :class="scrolled ? 'pointer-events-auto' : 'pointer-events-none'"
                 class="bg-forest-dark/95 backdrop-blur-md text-white rounded-full px-2.5 py-1.5 shadow-2xl border border-emerald-700/60 flex items-center gap-1"
                 aria-label="Navigasi Cepat Desktop">

                <!-- Mini Emblem Pemkab Subang -->
                <a href="{{ url('/') }}" aria-label="Beranda Portal"
                   class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                    <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}"
                         alt="Pemkab Subang" class="h-6 w-auto object-contain">
                    <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Beranda</span>
                </a>

                <span class="w-px h-6 bg-white/15 mx-0.5" aria-hidden="true"></span>

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" aria-label="Dashboard"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('admin.dashboard') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.pengajuan.index') }}" aria-label="Verifikasi & Nilai"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('admin.pengajuan.*') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">checklist</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Verifikasi &amp; Nilai</span>
                    </a>
                    <a href="{{ route('admin.topsis.index') }}" aria-label="SPK TOPSIS"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('admin.topsis.*') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">calculate</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">SPK TOPSIS</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" aria-label="Kelola Akun"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('admin.users.*') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Kelola Akun</span>
                    </a>
                @elseif(Auth::user()->isKepBid())
                    <a href="{{ route('kepbid.dashboard') }}" aria-label="Rekomendasi & ACC"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('kepbid.dashboard') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">approval</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Rekomendasi &amp; ACC</span>
                    </a>
                    <a href="{{ route('kepbid.riwayat') }}" aria-label="Riwayat & Rekap"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('kepbid.riwayat') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Riwayat &amp; Rekap</span>
                    </a>
                    <a href="{{ route('kepbid.ulasan') }}" aria-label="Survei IKM & Ulasan"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest {{ request()->routeIs('kepbid.ulasan') ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white' }}">
                        <span class="material-symbols-outlined text-[20px]">hotel_class</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Survei IKM &amp; Ulasan</span>
                    </a>
                @else
                    <a href="{{ route('poktan.dashboard') }}#poktan-status-section"
                       @click="const el = document.getElementById('poktan-status-section'); if (el) { $event.preventDefault(); poktanSection = 'status'; el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }"
                       aria-label="Pantau Status Usulan"
                       :class="poktanSection === 'status' ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white'"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                        <span class="material-symbols-outlined text-[20px]">track_changes</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Pantau Status</span>
                    </a>
                    <a href="{{ route('poktan.dashboard') }}#poktan-form-section"
                       @click="const el = document.getElementById('poktan-form-section'); if (el) { $event.preventDefault(); poktanSection = 'form'; el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }"
                       aria-label="Ajukan Alat Baru"
                       :class="poktanSection === 'form' ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white'"
                       class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                        <span class="material-symbols-outlined text-[20px]">post_add</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Ajukan Alat</span>
                    </a>
                    <button type="button"
                            @click="poktanSection = 'profil'; window.dispatchEvent(new CustomEvent('open-poktan-profile-modal'))"
                            aria-label="Perbarui Profil Poktan"
                            :class="poktanSection === 'profil' ? 'bg-lime-harvest text-obsidian shadow-md' : 'text-celery-cream hover:bg-white/15 hover:text-white'"
                            class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Profil Poktan</span>
                    </button>
                @endif

                <span class="w-px h-6 bg-white/15 mx-0.5" aria-hidden="true"></span>

                <!-- Kembali ke Atas -->
                <button type="button" @click="window.scrollTo({ top: 0, behavior: 'smooth' })" aria-label="Kembali ke Atas"
                        class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-white/10 hover:bg-white/20 text-celery-cream hover:text-white flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                    <span class="material-symbols-outlined text-[20px]">arrow_upward</span>
                    <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Ke Atas</span>
                </button>

                <!-- Keluar Sistem -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" aria-label="Keluar dari sistem"
                            class="group relative w-11 h-11 min-w-[44px] min-h-[44px] rounded-full bg-white/10 hover:bg-red-600/85 text-white flex items-center justify-center transition focus:outline-none focus-visible:ring-2 focus-visible:ring-lime-harvest">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span class="pointer-events-none opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity absolute top-full left-1/2 -translate-x-1/2 mt-2 px-2.5 py-1 rounded-lg bg-obsidian text-white text-[11px] font-bold whitespace-nowrap shadow-lg">Keluar</span>
                    </button>
                </form>

            </nav>
        </div>
    @endauth

    <!-- Flash Notifications Container (Interaktif: Auto-Dismiss 30 Detik & Swipe Gesture Kiri/Kanan) -->
    <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 mt-5 w-full">
        @if(isset($errors) && $errors->any())
            <div x-data="{
                    show: true,
                    startX: 0,
                    offsetX: 0,
                    isSwiping: false,
                    timer: null,
                    dismiss() {
                        if (this.timer) clearTimeout(this.timer);
                        this.show = false;
                    },
                    init() {
                        this.timer = setTimeout(() => this.dismiss(), 30000);
                    },
                    onTouchStart(e) {
                        this.isSwiping = true;
                        this.startX = e.touches ? e.touches[0].clientX : e.clientX;
                    },
                    onTouchMove(e) {
                        if (!this.isSwiping) return;
                        const currentX = e.touches ? e.touches[0].clientX : e.clientX;
                        this.offsetX = currentX - this.startX;
                    },
                    onTouchEnd() {
                        if (!this.isSwiping) return;
                        this.isSwiping = false;
                        if (Math.abs(this.offsetX) > 80) {
                            this.dismiss();
                        } else {
                            this.offsetX = 0;
                        }
                    }
                }"
                x-show="show"
                x-transition:leave="transition ease-out duration-300 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                :style="isSwiping 
                    ? 'transform: translateX(' + offsetX + 'px); opacity: ' + Math.max(0.2, 1 - Math.abs(offsetX)/250) + '; transition: none;' 
                    : 'transform: translateX(' + offsetX + 'px); transition: transform 0.25s ease-out, opacity 0.25s ease-out;'"
                @touchstart="onTouchStart($event)"
                @touchmove="onTouchMove($event)"
                @touchend="onTouchEnd()"
                @mousedown="onTouchStart($event)"
                @mousemove="onTouchMove($event)"
                @mouseup="onTouchEnd()"
                @mouseleave="if(isSwiping) onTouchEnd()"
                class="relative mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm space-y-1 select-none cursor-grab active:cursor-grabbing overflow-hidden group touch-pan-y"
                role="alert">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center space-x-2 font-bold text-sm text-red-800">
                        <span class="material-symbols-outlined text-red-600">error</span>
                        <span>Terdapat kesalahan pada data yang Anda masukkan:</span>
                    </div>
                    <span class="text-[10px] font-semibold text-red-600/70 bg-red-100/70 px-2 py-0.5 rounded-full shrink-0 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">swipe</span> Geser / 30s
                    </span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-700 pl-4 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <div class="absolute bottom-0 left-0 h-0.5 bg-red-500/40 rounded-full" style="animation: alertCountdown 30s linear forwards;"></div>
            </div>
        @endif

        @if(session('success'))
            <div x-data="{
                    show: true,
                    startX: 0,
                    offsetX: 0,
                    isSwiping: false,
                    timer: null,
                    dismiss() {
                        if (this.timer) clearTimeout(this.timer);
                        this.show = false;
                    },
                    init() {
                        this.timer = setTimeout(() => this.dismiss(), 30000);
                    },
                    onTouchStart(e) {
                        this.isSwiping = true;
                        this.startX = e.touches ? e.touches[0].clientX : e.clientX;
                    },
                    onTouchMove(e) {
                        if (!this.isSwiping) return;
                        const currentX = e.touches ? e.touches[0].clientX : e.clientX;
                        this.offsetX = currentX - this.startX;
                    },
                    onTouchEnd() {
                        if (!this.isSwiping) return;
                        this.isSwiping = false;
                        if (Math.abs(this.offsetX) > 80) {
                            this.dismiss();
                        } else {
                            this.offsetX = 0;
                        }
                    }
                }"
                x-show="show"
                x-transition:leave="transition ease-out duration-300 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                :style="isSwiping 
                    ? 'transform: translateX(' + offsetX + 'px); opacity: ' + Math.max(0.2, 1 - Math.abs(offsetX)/250) + '; transition: none;' 
                    : 'transform: translateX(' + offsetX + 'px); transition: transform 0.25s ease-out, opacity 0.25s ease-out;'"
                @touchstart="onTouchStart($event)"
                @touchmove="onTouchMove($event)"
                @touchend="onTouchEnd()"
                @mousedown="onTouchStart($event)"
                @mousemove="onTouchMove($event)"
                @mouseup="onTouchEnd()"
                @mouseleave="if(isSwiping) onTouchEnd()"
                class="relative mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between gap-3 shadow-sm select-none cursor-grab active:cursor-grabbing overflow-hidden group touch-pan-y"
                role="alert">
                <div class="flex items-center space-x-3 min-w-0">
                    <span class="material-symbols-outlined text-emerald-600 text-xl shrink-0">check_circle</span>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('success') }}</div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="hidden sm:inline-flex text-[10px] font-semibold text-emerald-700/70 bg-emerald-100/70 px-2 py-0.5 rounded-full items-center gap-1">
                        <span class="material-symbols-outlined text-xs">swipe</span> Geser / 30s
                    </span>
                    <button type="button" @click="dismiss()" class="text-emerald-700/60 hover:text-emerald-900 transition p-1 rounded-lg hover:bg-emerald-100">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 h-0.5 bg-emerald-500/40 rounded-full" style="animation: alertCountdown 30s linear forwards;"></div>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{
                    show: true,
                    startX: 0,
                    offsetX: 0,
                    isSwiping: false,
                    timer: null,
                    dismiss() {
                        if (this.timer) clearTimeout(this.timer);
                        this.show = false;
                    },
                    init() {
                        this.timer = setTimeout(() => this.dismiss(), 30000);
                    },
                    onTouchStart(e) {
                        this.isSwiping = true;
                        this.startX = e.touches ? e.touches[0].clientX : e.clientX;
                    },
                    onTouchMove(e) {
                        if (!this.isSwiping) return;
                        const currentX = e.touches ? e.touches[0].clientX : e.clientX;
                        this.offsetX = currentX - this.startX;
                    },
                    onTouchEnd() {
                        if (!this.isSwiping) return;
                        this.isSwiping = false;
                        if (Math.abs(this.offsetX) > 80) {
                            this.dismiss();
                        } else {
                            this.offsetX = 0;
                        }
                    }
                }"
                x-show="show"
                x-transition:leave="transition ease-out duration-300 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                :style="isSwiping 
                    ? 'transform: translateX(' + offsetX + 'px); opacity: ' + Math.max(0.2, 1 - Math.abs(offsetX)/250) + '; transition: none;' 
                    : 'transform: translateX(' + offsetX + 'px); transition: transform 0.25s ease-out, opacity 0.25s ease-out;'"
                @touchstart="onTouchStart($event)"
                @touchmove="onTouchMove($event)"
                @touchend="onTouchEnd()"
                @mousedown="onTouchStart($event)"
                @mousemove="onTouchMove($event)"
                @mouseup="onTouchEnd()"
                @mouseleave="if(isSwiping) onTouchEnd()"
                class="relative mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 flex items-center justify-between gap-3 shadow-sm select-none cursor-grab active:cursor-grabbing overflow-hidden group touch-pan-y"
                role="alert">
                <div class="flex items-center space-x-3 min-w-0">
                    <span class="material-symbols-outlined text-red-600 text-xl shrink-0">cancel</span>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('error') }}</div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="hidden sm:inline-flex text-[10px] font-semibold text-red-700/70 bg-red-100/70 px-2 py-0.5 rounded-full items-center gap-1">
                        <span class="material-symbols-outlined text-xs">swipe</span> Geser / 30s
                    </span>
                    <button type="button" @click="dismiss()" class="text-red-700/60 hover:text-red-900 transition p-1 rounded-lg hover:bg-red-100">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 h-0.5 bg-red-500/40 rounded-full" style="animation: alertCountdown 30s linear forwards;"></div>
            </div>
        @endif

        @if(session('warning'))
            <div x-data="{
                    show: true,
                    startX: 0,
                    offsetX: 0,
                    isSwiping: false,
                    timer: null,
                    dismiss() {
                        if (this.timer) clearTimeout(this.timer);
                        this.show = false;
                    },
                    init() {
                        this.timer = setTimeout(() => this.dismiss(), 30000);
                    },
                    onTouchStart(e) {
                        this.isSwiping = true;
                        this.startX = e.touches ? e.touches[0].clientX : e.clientX;
                    },
                    onTouchMove(e) {
                        if (!this.isSwiping) return;
                        const currentX = e.touches ? e.touches[0].clientX : e.clientX;
                        this.offsetX = currentX - this.startX;
                    },
                    onTouchEnd() {
                        if (!this.isSwiping) return;
                        this.isSwiping = false;
                        if (Math.abs(this.offsetX) > 80) {
                            this.dismiss();
                        } else {
                            this.offsetX = 0;
                        }
                    }
                }"
                x-show="show"
                x-transition:leave="transition ease-out duration-300 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                :style="isSwiping 
                    ? 'transform: translateX(' + offsetX + 'px); opacity: ' + Math.max(0.2, 1 - Math.abs(offsetX)/250) + '; transition: none;' 
                    : 'transform: translateX(' + offsetX + 'px); transition: transform 0.25s ease-out, opacity 0.25s ease-out;'"
                @touchstart="onTouchStart($event)"
                @touchmove="onTouchMove($event)"
                @touchend="onTouchEnd()"
                @mousedown="onTouchStart($event)"
                @mousemove="onTouchMove($event)"
                @mouseup="onTouchEnd()"
                @mouseleave="if(isSwiping) onTouchEnd()"
                class="relative mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-harvest/40 text-amber-900 flex items-center justify-between gap-3 shadow-sm select-none cursor-grab active:cursor-grabbing overflow-hidden group touch-pan-y"
                role="alert">
                <div class="flex items-center space-x-3 min-w-0">
                    <span class="material-symbols-outlined text-amber-harvest text-xl shrink-0">warning</span>
                    <div class="text-xs sm:text-sm font-semibold leading-relaxed">{{ session('warning') }}</div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="hidden sm:inline-flex text-[10px] font-semibold text-amber-700/70 bg-amber-100/70 px-2 py-0.5 rounded-full items-center gap-1">
                        <span class="material-symbols-outlined text-xs">swipe</span> Geser / 30s
                    </span>
                    <button type="button" @click="dismiss()" class="text-amber-700/60 hover:text-amber-900 transition p-1 rounded-lg hover:bg-amber-100">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 h-0.5 bg-amber-500/40 rounded-full" style="animation: alertCountdown 30s linear forwards;"></div>
            </div>
        @endif
    </div>

    <!-- Main Content Area (Kunci Layout Utama Anti-Offside & Anti-Geser Mobile) -->
    <main class="flex-grow max-w-[1536px] w-full min-w-0 mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 overflow-x-hidden">
        @yield('content')
    </main>

    <!-- Municipal Footer (Dinas Pertanian Kabupaten Subang) -->
    <footer class="bg-white border-t border-bone-slate mt-auto pt-6 @auth pb-32 sm:pb-36 lg:pb-6 @else py-6 @endauth">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-muted-green">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                     alt="Emblem Subang" 
                     class="h-6 w-auto object-contain">
                <span>&copy; 2026 <strong>Dinas Pertanian Pemerintah Kabupaten Subang</strong> &bull; SPK Bantuan Alsintan (Metode TOPSIS)</span>
            </div>
            <div class="flex flex-wrap items-center justify-center gap-3 text-[11px] font-semibold text-forest-dark uppercase tracking-wider">
                <a href="{{ route('privacy') }}" class="hover:underline">Privasi &amp; Cookie</a>
                <span>&bull;</span>
                <span>Karya Utama Satya Nagara</span>
                <span>&bull;</span>
                <span>Standar BPKP / ANRI</span>
            </div>
        </div>
    </footer>

    @auth
        @if(Auth::user()->isAdmin())
            <!-- FLOATING MOBILE BOTTOM NAVIGATION BAR: ADMIN (Compact Curved Scoop, Tanpa Hamburger) -->
            <div class="lg:hidden fixed bottom-3 inset-x-0 z-40 flex justify-center px-3 pointer-events-none">
                <nav class="pointer-events-auto bg-forest-dark/95 backdrop-blur-md text-white rounded-full px-2.5 py-1.5 shadow-2xl border-2 border-forest-deep flex items-center justify-around w-full max-w-[300px] transition-all" aria-label="Navigasi Mobile Admin">
                    <a href="{{ route('admin.dashboard') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('admin.dashboard') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">dashboard</span>
                        </div>
                        <span class="{{ request()->routeIs('admin.dashboard') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">Beranda</span>
                    </a>

                    <a href="{{ route('admin.pengajuan.index') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('admin.pengajuan.*') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">checklist</span>
                        </div>
                        <span class="{{ request()->routeIs('admin.pengajuan.*') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">Verifikasi</span>
                    </a>

                    <a href="{{ route('admin.topsis.index') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('admin.topsis.*') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">calculate</span>
                        </div>
                        <span class="{{ request()->routeIs('admin.topsis.*') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">TOPSIS</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('admin.users.*') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">manage_accounts</span>
                        </div>
                        <span class="{{ request()->routeIs('admin.users.*') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">Akun</span>
                    </a>
                </nav>
            </div>
        @elseif(Auth::user()->isKepBid())
            <!-- FLOATING MOBILE BOTTOM NAVIGATION BAR: KEPALA BIDANG (Compact 3 Menu: Keputusan, Riwayat, & IKM) -->
            <div class="lg:hidden fixed bottom-3 inset-x-0 z-40 flex justify-center px-4 pointer-events-none">
                <nav class="pointer-events-auto bg-forest-dark/95 backdrop-blur-md text-white rounded-full px-3 py-1.5 shadow-2xl border-2 border-forest-deep flex items-center justify-around w-full max-w-[280px] transition-all" aria-label="Navigasi Mobile Kepala Bidang">
                    <a href="{{ route('kepbid.dashboard') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('kepbid.dashboard') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">approval</span>
                        </div>
                        <span class="{{ request()->routeIs('kepbid.dashboard') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">Keputusan</span>
                    </a>

                    <a href="{{ route('kepbid.riwayat') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('kepbid.riwayat') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">inventory_2</span>
                        </div>
                        <span class="{{ request()->routeIs('kepbid.riwayat') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">Riwayat</span>
                    </a>

                    <a href="{{ route('kepbid.ulasan') }}" class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all">
                        <div class="{{ request()->routeIs('kepbid.ulasan') ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white' }} w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                            <span class="material-symbols-outlined text-[19px] font-bold">hotel_class</span>
                        </div>
                        <span class="{{ request()->routeIs('kepbid.ulasan') ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold' }} text-[10px] leading-tight tracking-tight block mt-0.5">IKM</span>
                    </a>
                </nav>
            </div>
        @endif
    @endauth

    <!-- Global Lightweight SVG/CSS Loading Overlay (Instant Render & Smooth Fade-Out) -->
    <div id="global-3d-loader" 
         class="fixed inset-0 z-[9999] bg-forest-dark/60 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none"
         style="display: none;">
        
        <div class="bg-white border border-bone-slate rounded-3xl p-7 sm:p-8 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center text-center space-y-5 relative overflow-hidden">
            
            <!-- Subtle Agrarian Ambient Glow -->
            <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full bg-lime-harvest/25 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full bg-forest-dark/10 blur-2xl pointer-events-none"></div>

            <!-- Dual-Ring Harvesta CSS/SVG Spinner -->
            <div class="relative w-16 h-16 flex items-center justify-center" role="status" aria-label="Memproses data">
                <svg class="w-16 h-16 animate-spin text-emerald-100" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="6"></circle>
                    <path d="M58 32a26 26 0 0 0-26-26" stroke="#14532D" stroke-width="6" stroke-linecap="round"></path>
                </svg>
                <svg class="w-11 h-11 absolute animate-spin" style="animation-duration: 1.4s; animation-direction: reverse;" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M40 24a16 16 0 0 1-16 16" stroke="#E1F12E" stroke-width="4.5" stroke-linecap="round"></path>
                </svg>
                <span class="material-symbols-outlined text-lg text-forest-dark absolute">eco</span>
            </div>

            <!-- Loading Status Text -->
            <div class="space-y-1.5 w-full relative z-10">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-celery-cream text-forest-dark text-[11px] font-extrabold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-forest-dark animate-ping"></span>
                    <span id="loader-status-badge">Sedang Memproses</span>
                </div>
                <h3 class="text-base font-extrabold text-obsidian font-urbanist" id="loader-title">
                    Memproses Data Sistem...
                </h3>
                <p class="text-xs text-muted-green leading-snug px-2" id="loader-subtitle">
                    Mohon tunggu sebentar, sistem sedang menyinkronkan data.
                </p>
            </div>
        </div>
    </div>

    <!-- Script Global Lightweight Loader -->
    <script>
        let _loaderWatchdogTimer = null;

        window.show3DLoader = function(title, subtitle, badge) {
            const loader = document.getElementById('global-3d-loader');
            if (!loader) return;
            if (title) document.getElementById('loader-title').textContent = title;
            if (subtitle) document.getElementById('loader-subtitle').textContent = subtitle;
            if (badge) document.getElementById('loader-status-badge').textContent = badge;
            
            loader.style.display = 'flex';
            requestAnimationFrame(() => {
                loader.classList.remove('opacity-0', 'pointer-events-none');
                loader.classList.add('opacity-100', 'pointer-events-auto');
            });

            // Watchdog Timer (Maksimal 6 detik auto-dismiss agar tidak pernah terjadi loading loop tanpa akhir)
            if (_loaderWatchdogTimer) clearTimeout(_loaderWatchdogTimer);
            _loaderWatchdogTimer = setTimeout(() => {
                window.hide3DLoader();
            }, 6000);
        };

        window.hide3DLoader = function() {
            if (_loaderWatchdogTimer) clearTimeout(_loaderWatchdogTimer);
            const loader = document.getElementById('global-3d-loader');
            if (!loader) return;
            loader.classList.remove('opacity-100', 'pointer-events-auto');
            loader.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 250);
        };

        window.addEventListener('pageshow', window.hide3DLoader);
        window.addEventListener('load', window.hide3DLoader);
        window.addEventListener('popstate', window.hide3DLoader);

        // Otomatis aktifkan loader HANYA saat submit form POST/PUT/DELETE (Bukan form GET / pencarian / filter)
        document.addEventListener('DOMContentLoaded', function () {
            window.hide3DLoader();

            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (e.defaultPrevented) {
                        return;
                    }
                    const method = (form.getAttribute('method') || 'GET').toUpperCase();
                    // ABAIKAN form GET, form pencarian, form filter, dan form dengan class 'no-loader'
                    if (method === 'GET' || form.classList.contains('no-loader')) {
                        return;
                    }

                    if (!form.checkValidity || form.checkValidity()) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        const customTitle = submitBtn?.getAttribute('data-loading-title') || 'Memproses Permintaan...';
                        const customSubtitle = submitBtn?.getAttribute('data-loading-subtitle') || 'Mohon tunggu, sinkronisasi data sedang berlangsung.';
                        window.show3DLoader(customTitle, customSubtitle);
                    }
                });
            });

            // Otomatis aktifkan saat link dengan class 'trigger-3d-loader' diklik
            document.querySelectorAll('.trigger-3d-loader').forEach(link => {
                link.addEventListener('click', function () {
                    const title = this.getAttribute('data-loading-title') || 'Memuat Halaman...';
                    const subtitle = this.getAttribute('data-loading-subtitle') || 'Mohon tunggu sebentar.';
                    window.show3DLoader(title, subtitle);
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
