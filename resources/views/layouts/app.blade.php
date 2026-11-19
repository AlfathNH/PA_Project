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

    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        // User Reference Exact Palette (media_1793774852232_3ac505b4)
                        "lime-harvest": "#E1F12E",
                        "lime-harvest-hover": "#CDE01F",
                        "amber-harvest": "#FFB555",
                        "amber-harvest-hover": "#ECA03C",
                        "celery-cream": "#EDF0C2",
                        "bone-slate": "#E3E4D6",
                        "bone-canvas": "#F4F5ED",
                        
                        // Institutional Authority - Dinas Pertanian Kabupaten Subang
                        "forest-dark": "#14532D",
                        "forest-deep": "#0F3F22",
                        "forest-hover": "#166534",
                        "obsidian": "#1C241D",
                        "muted-green": "#4D564F",
                        "leaf-emerald": "#16A34A"
                    },
                    fontFamily: {
                        "sans": ["Urbanist", "Plus Jakarta Sans", "system-ui", "sans-serif"],
                        "urbanist": ["Urbanist", "sans-serif"],
                        "editorial": ["Newsreader", "Georgia", "serif"]
                    },
                    borderRadius: {
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        "4xl": "2rem"
                    }
                }
            }
        };
    </script>

    <!-- Alpine.js for Modern Reactive Components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Google Fonts: Urbanist (Reference) & Newsreader (Editorial) & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,300..900;1,300..900&family=Newsreader:ital,opsz,wght@0,6..72,300..800;1,6..72,300..800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

    <!-- Flash Notifications Container -->
    <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 mt-5 w-full">
        @if(isset($errors) && $errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 shadow-sm space-y-1">
                <div class="flex items-center space-x-2 font-bold text-sm text-red-800">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span>Terdapat kesalahan pada data yang Anda masukkan:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-700 pl-4 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-sm">
                <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
                <div class="text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 flex items-start space-x-3 shadow-sm">
                <span class="material-symbols-outlined text-red-600 text-xl">cancel</span>
                <div class="text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 p-4 rounded-2xl bg-amber-50 border border-amber-harvest/40 text-amber-900 flex items-start space-x-3 shadow-sm">
                <span class="material-symbols-outlined text-amber-harvest text-xl">warning</span>
                <div class="text-sm font-semibold">{{ session('warning') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content Area (Kunci Layout Utama Anti-Offside & Anti-Geser Mobile) -->
    <main class="flex-grow max-w-[1536px] w-full min-w-0 mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 overflow-x-hidden">
        @yield('content')
    </main>

    <!-- Municipal Footer (Dinas Pertanian Kabupaten Subang) -->
    <footer class="bg-white border-t border-bone-slate mt-auto pt-6 @auth pb-24 lg:pb-6 @else py-6 @endauth">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-muted-green">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                     alt="Emblem Subang" 
                     class="h-6 w-auto object-contain">
                <span>&copy; 2026 <strong>Dinas Pertanian Pemerintah Kabupaten Subang</strong> &bull; SPK Bantuan Alsintan (Metode TOPSIS)</span>
            </div>
            <div class="flex items-center space-x-4 text-[11px] font-semibold text-forest-dark uppercase tracking-wider">
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
            <!-- FLOATING MOBILE BOTTOM NAVIGATION BAR: KEPALA BIDANG (Compact 2 Menu Desktop: Keputusan & Riwayat) -->
            <div class="lg:hidden fixed bottom-3 inset-x-0 z-40 flex justify-center px-4 pointer-events-none">
                <nav class="pointer-events-auto bg-forest-dark/95 backdrop-blur-md text-white rounded-full px-3 py-1.5 shadow-2xl border-2 border-forest-deep flex items-center justify-around w-full max-w-[200px] transition-all" aria-label="Navigasi Mobile Kepala Bidang">
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
                </nav>
            </div>
        @endif
    @endauth

    @if(app()->environment(['local', 'testing']) || config('app.debug'))
    <!-- Floating Discreet Role Switcher (Ditempatkan di atas Bottom Bar pada layar mobile agar tidak bertumpuk) -->
    <div x-data="{ openSwitcher: false }" @click.outside="openSwitcher = false" class="fixed @auth bottom-24 lg:bottom-4 @else bottom-4 @endauth right-4 z-40">
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
                    class="px-4 py-2.5 min-h-[44px] rounded-full bg-forest-dark/95 hover:bg-forest-dark text-lime-harvest border border-white/20 shadow-lg backdrop-blur-md flex items-center gap-1.5 text-xs font-bold transition">
                <span class="material-symbols-outlined text-sm">swap_horiz</span>
                <span>Ganti Peran</span>
            </button>
        </div>
    </div>
    @endif

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
