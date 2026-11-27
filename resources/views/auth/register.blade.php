<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Akun Kelompok Tani (Poktan) - Dinas Pertanian Kabupaten Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">

    <!-- Local Self-Hosted Fonts & Icons -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ file_exists(public_path('css/fonts.css')) ? filemtime(public_path('css/fonts.css')) : time() }}">

    <!-- Alpine.js (Terbaru v3.17.5 dengan Subresource Integrity SHA-384) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.17.5/dist/cdn.min.js"
            integrity="sha384-FYLKjwG3aohNhNZoVG8sY/5ntunm+MqylL90Q+rT3OM7AwV9/BKFjk/RjoIpIgsI"
            crossorigin="anonymous"></script>

    <!-- Google 3D Model Viewer Engine (v4.4.0 dengan Subresource Integrity SHA-384) -->
    <script type="module"
            src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js"
            integrity="sha384-wjsPzEO+lXOf7d9csIDfAQpPt2OKuUU3m0K9qsdKIuzX4N8czSF6hdh23eAbZa3p"
            crossorigin="anonymous"></script>

    <!-- Pre-compiled Tailwind CSS Engine (First-Party) -->
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}?v={{ file_exists(public_path('css/tailwind.min.css')) ? filemtime(public_path('css/tailwind.min.css')) : time() }}">

    <style>
        [x-cloak] { display: none !important; }
        .topographic-grid {
            background-size: 36px 36px;
            background-image: 
                linear-gradient(to right, rgba(20, 83, 45, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(20, 83, 45, 0.04) 1px, transparent 1px);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
            line-height: 1;
        }
        /* Hilangkan spinner panah browser pada input angka */
        .no-spin::-webkit-inner-spin-button,
        .no-spin::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .no-spin {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>
</head>
<body class="bg-bone-canvas text-obsidian min-h-screen flex flex-col justify-between topographic-grid antialiased font-sans relative overflow-x-hidden selection:bg-lime-harvest selection:text-obsidian">

    <!-- 3D Living Background Canvas (Full Screen, Ambient Rotation with Soft Cinematic Blur) -->
    <div class="fixed inset-0 w-full h-full z-0 overflow-hidden pointer-events-none select-none">
        <div class="absolute inset-0 bg-radial from-transparent via-bone-canvas/30 to-bone-canvas/85 z-2"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-bone-canvas/90 via-transparent to-bone-canvas/60 z-2"></div>

        <div class="w-full h-full filter blur-[5px] scale-105 transition-all duration-700 opacity-80">
            <model-viewer
                src="{{ asset('models/low_poly_little_farm_field.glb') }}?v={{ file_exists(public_path('models/low_poly_little_farm_field.glb')) ? filemtime(public_path('models/low_poly_little_farm_field.glb')) : time() }}"
                alt="3D Ambient Background"
                auto-rotate
                rotation-per-second="6deg"
                shadow-intensity="1.5"
                shadow-softness="0.8"
                exposure="1.2"
                tone-mapping="neutral"
                environment-image="neutral"
                camera-orbit="30deg 60deg 115%"
                field-of-view="35deg"
                interaction-prompt="none"
                style="width: 100vw; height: 100vh; outline: none; background: transparent;">
            </model-viewer>
        </div>
    </div>

    <!-- Top Navigation Header (Floating z-10) -->
    <header class="w-full max-w-6xl mx-auto px-4 sm:px-6 pt-5 sm:pt-6 flex items-center justify-between relative z-10">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 sm:gap-2 text-xs font-extrabold text-forest-dark hover:text-forest-deep transition group px-3 py-1.5 sm:px-3.5 rounded-full bg-white/80 backdrop-blur-md border border-bone-slate shadow-2xs">
            <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
            <span class="hidden sm:inline">Kembali ke Halaman Masuk</span>
            <span class="sm:hidden">Masuk</span>
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-white/85 backdrop-blur-md border border-bone-slate text-forest-dark shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
            Registrasi Mandiri Poktan
        </span>
    </header>

    @php
        // Otomatis arahkan ke langkah yang memuat error jika validasi gagal
        $initialStep = 1;
        if ($errors->hasAny(['nama_poktan', 'nomor_badan_hukum', 'nama_ketua', 'no_hp_ketua'])) {
            $initialStep = 2;
        } elseif ($errors->hasAny(['kecamatan', 'desa', 'luas_lahan_total', 'jumlah_anggota', 'alamat_lengkap'])) {
            $initialStep = 3;
        }
    @endphp

    <!-- Main Registration Container (Interactive Stepper Wizard with Live Progress Bar) -->
    <main x-data="{
            step: {{ $initialStep }},
            toastMessage: '',
            name: @js(old('name', '')),
            email: @js(old('email', '')),
            password: '',
            password_confirmation: '',
            nama_poktan: @js(old('nama_poktan', '')),
            nomor_badan_hukum: @js(old('nomor_badan_hukum', '')),
            nama_ketua: @js(old('nama_ketua', '')),
            no_hp_ketua: @js(old('no_hp_ketua', '')),
            kecamatan: @js(old('kecamatan', '')),
            desa: @js(old('desa', '')),
            luas_lahan_total: @js(old('luas_lahan_total', '25.0')),
            jumlah_anggota: @js(old('jumlah_anggota', '30')),
            alamat_lengkap: @js(old('alamat_lengkap', '')),

            showNotification(msg) {
                this.toastMessage = msg;
                window.scrollTo({ top: 120, behavior: 'smooth' });
                setTimeout(() => { if (this.toastMessage === msg) this.toastMessage = ''; }, 5000);
            },

            pwHasMin() { return (this.password || '').length >= 8; },
            pwHasUpper() { return /[A-Z]/.test(this.password || ''); },
            pwHasLower() { return /[a-z]/.test(this.password || ''); },
            pwHasNumber() { return /[0-9]/.test(this.password || ''); },
            pwIsStrong() { return this.pwHasMin() && this.pwHasUpper() && this.pwHasLower() && this.pwHasNumber(); },
            pwMatches() { return Boolean(this.password) && this.password === (this.password_confirmation || ''); },

            isStep1Valid() {
                return (this.name || '').trim().length > 0 && 
                       (this.email || '').trim().length > 0 && 
                       (this.email || '').includes('@') && 
                       this.pwIsStrong() &&
                       this.pwMatches();
            },

            isStep2Valid() {
                return (this.nama_poktan || '').trim().length > 0 && 
                       (this.nama_ketua || '').trim().length > 0 && 
                       (this.no_hp_ketua || '').trim().length > 0;
            },

            isStep3Valid() {
                return (this.kecamatan || '').trim().length > 0 && 
                       (this.desa || '').trim().length > 0 && 
                       parseFloat(this.luas_lahan_total || 0) > 0 && 
                       parseInt(this.jumlah_anggota || 0) > 0 && 
                       (this.alamat_lengkap || '').trim().length > 0;
            },

            isAllValid() {
                return this.isStep1Valid() && this.isStep2Valid() && this.isStep3Valid();
            },

            progressPercent() {
                if (this.isAllValid()) return 100;
                let p = 0;
                if (this.isStep1Valid()) p += 33;
                else if (this.step === 1) p += 15;

                if (this.isStep2Valid()) p += 33;
                else if (this.step === 2) p += 15;

                if (this.isStep3Valid()) p += 34;
                else if (this.step === 3) p += 15;

                return Math.min(p, 95);
            },

            progressLabel() {
                if (this.isAllValid()) return '100% Siap Kirim';
                if (this.isStep1Valid() && this.isStep2Valid()) return '66% Selesai';
                if (this.isStep1Valid()) return '33% Selesai';
                return this.progressPercent() + '% Terisi';
            },

            setStep(s) {
                this.toastMessage = '';
                if (s === 2 && !this.isStep1Valid()) {
                    this.showNotification('Harap lengkapi Kredensial Akun (Nama, Email, dan Kata Sandi) di Langkah 1 terlebih dahulu.');
                    this.step = 1;
                    this.focusFirstInvalid(1);
                    return;
                }
                if (s === 3) {
                    if (!this.isStep1Valid()) {
                        this.showNotification('Harap lengkapi Kredensial Akun (Nama, Email, dan Kata Sandi) di Langkah 1 terlebih dahulu.');
                        this.step = 1;
                        this.focusFirstInvalid(1);
                        return;
                    }
                    if (!this.isStep2Valid()) {
                        this.showNotification('Harap lengkapi Profil Lembaga Kelompok Tani di Langkah 2 terlebih dahulu.');
                        this.step = 2;
                        this.focusFirstInvalid(2);
                        return;
                    }
                }
                this.step = s;
                window.scrollTo({ top: 120, behavior: 'smooth' });
            },

            nextStep() {
                this.toastMessage = '';
                if (this.step === 1) {
                    if (!this.isStep1Valid()) {
                        this.showNotification('Harap lengkapi seluruh kolom Kredensial Akun dengan benar sebelum lanjut.');
                        this.focusFirstInvalid(1);
                        return;
                    }
                    this.step = 2;
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                } else if (this.step === 2) {
                    if (!this.isStep2Valid()) {
                        this.showNotification('Harap lengkapi data kelembagaan Poktan (Nama Poktan, Ketua, dan No. HP/WA) sebelum lanjut.');
                        this.focusFirstInvalid(2);
                        return;
                    }
                    this.step = 3;
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                }
            },

            prevStep() {
                this.toastMessage = '';
                if (this.step > 1) {
                    this.step--;
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                }
            },

            focusFirstInvalid(st) {
                setTimeout(() => {
                    if (st === 1) {
                        const n = document.getElementById('name');
                        const e = document.getElementById('email');
                        const p = document.getElementById('password');
                        const pc = document.getElementById('password_confirmation');
                        if (!this.name || !this.name.trim()) { n.focus(); }
                        else if (!this.email || !this.email.trim() || !this.email.includes('@')) { e.focus(); }
                        else if (!this.pwIsStrong()) { this.toastMessage = 'Kata sandi belum memenuhi kriteria (min. 8 karakter, huruf besar, huruf kecil, dan angka).'; p.focus(); }
                        else if (!this.pwMatches()) { this.toastMessage = 'Konfirmasi kata sandi tidak cocok. Mohon samakan.'; pc.focus(); }
                    } else if (st === 2) {
                        const pk = document.getElementById('nama_poktan');
                        const k = document.getElementById('nama_ketua');
                        const hp = document.getElementById('no_hp_ketua');
                        if (!this.nama_poktan || !this.nama_poktan.trim()) { pk.focus(); }
                        else if (!this.nama_ketua || !this.nama_ketua.trim()) { k.focus(); }
                        else if (!this.no_hp_ketua || !this.no_hp_ketua.trim()) { hp.focus(); }
                    }
                }, 150);
            }
          }" 
          @change-step.window="setStep($event.detail);"
          class="w-full max-w-3xl mx-auto px-3.5 sm:px-6 py-6 sm:py-8 relative z-10 flex-1 flex flex-col justify-center">
        
        <!-- Flash Messages -->
        @if(session('error'))
            <div class="mb-4 p-4 rounded-2xl bg-red-50/95 backdrop-blur-md border border-red-200 text-red-900 flex items-start gap-2.5 shadow-sm text-xs font-semibold">
                <span class="material-symbols-outlined text-red-600 text-lg">cancel</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 rounded-2xl bg-red-50/95 backdrop-blur-md border border-red-200 text-red-900 text-xs font-semibold space-y-1">
                <div class="flex items-center gap-2 font-bold text-red-950">
                    <span class="material-symbols-outlined text-red-600 text-base">error</span>
                    <span>Mohon periksa kembali isian formulir:</span>
                </div>
                <ul class="list-disc list-inside pl-1 text-[11px] text-red-900/90 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Register (Solid White - Clean, High Contrast & Non-Transparent) -->
        <div class="bg-white rounded-3xl border border-bone-slate shadow-2xl p-6 sm:p-9 space-y-6 sm:space-y-7 relative overflow-hidden">
            
            <!-- Dinas Subang Emblem Header -->
            <div class="text-center space-y-2.5 sm:space-y-3">
                <div class="inline-block p-2 sm:p-2.5 rounded-2xl bg-bone-canvas/70 border border-bone-slate shadow-2xs">
                    <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                         alt="Logo Pemkab Subang" 
                         class="h-14 sm:h-16 w-auto mx-auto object-contain filter drop-shadow">
                </div>
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold tracking-widest text-muted-green uppercase block">
                        Pemerintah Daerah Kabupaten Subang
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black text-obsidian tracking-tight font-urbanist mt-0.5">
                        Pendaftaran Kelompok Tani (Poktan)
                    </h1>
                    <p class="text-[11px] sm:text-xs text-muted-green font-medium mt-1 max-w-lg mx-auto leading-relaxed">
                        Lengkapi 3 langkah formulir berikut untuk mendapatkan akun mandiri permohonan bantuan Alsintan resmi.
                    </p>
                </div>
            </div>

            <!-- Dynamic Inline Validation Toast Alert -->
            <div x-show="toastMessage" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-bold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-lg">error</span>
                    <span x-text="toastMessage"></span>
                </div>
                <button type="button" @click="toastMessage = ''" class="text-amber-700 hover:text-amber-950 p-1">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            <!-- ========================================================================= -->
            <!-- LIVE ANIMATED PROGRESS BAR & INTERACTIVE STEPPER BUTTONS                  -->
            <!-- Responsif: status terverifikasi sesuai data riil, terkunci jika belum isi  -->
            <!-- ========================================================================= -->
            <div class="space-y-2.5 p-3 sm:p-3.5 bg-bone-canvas/60 border border-bone-slate rounded-2xl">
                <!-- Info Status & Persentase Progres Real-Time -->
                <div class="flex items-center justify-between text-xs font-urbanist">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-forest-dark animate-pulse"></span>
                        <span class="font-extrabold text-forest-dark uppercase tracking-wider text-[11px] sm:text-xs">
                            <span x-show="step === 1 && !isStep1Valid()">Langkah 1 dari 3: Akun Kredensial Login</span>
                            <span x-show="step === 1 && isStep1Valid()" x-cloak class="text-emerald-800">Langkah 1 Selesai &bull; Siap ke Lembaga</span>
                            
                            <span x-show="step === 2 && !isStep2Valid()" x-cloak>Langkah 2 dari 3: Profil Lembaga Kelompok Tani</span>
                            <span x-show="step === 2 && isStep2Valid()" x-cloak class="text-emerald-800">Langkah 2 Selesai &bull; Siap ke Luas Sawah</span>
                            
                            <span x-show="step === 3 && !isAllValid()" x-cloak>Langkah 3 dari 3: Hamparan Lahan & Wilayah</span>
                            <span x-show="step === 3 && isAllValid()" x-cloak class="text-emerald-800 font-black">Semua Data Lengkap &bull; Siap Kirim</span>
                        </span>
                    </div>
                    <span class="font-mono font-black text-xs px-2.5 py-0.5 rounded-full bg-celery-cream text-forest-dark border border-bone-slate shadow-2xs"
                          x-text="progressLabel()">
                    </span>
                </div>

                <!-- Animated Progress Bar Track -->
                <div class="w-full bg-white rounded-full h-3 p-0.5 border border-bone-slate overflow-hidden relative shadow-inner">
                    <div class="h-full rounded-full bg-gradient-to-r from-forest-dark via-emerald-600 to-lime-harvest-hover transition-all duration-500 ease-out shadow-xs flex items-center justify-end pr-1"
                         :style="'width: ' + progressPercent() + '%'">
                        <div class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></div>
                    </div>
                </div>

                <!-- Clickable Interactive Step Pills (Strict Data Verification Status) -->
                <div class="grid grid-cols-3 gap-1.5 sm:gap-2 pt-1 text-center">
                    <!-- Tab 1: Akun Login -->
                    <button type="button" 
                            @click="setStep(1)"
                            :class="step === 1 
                                ? 'bg-forest-dark text-lime-harvest shadow-md font-black ring-2 ring-forest-dark/30 scale-[1.02]' 
                                : (isStep1Valid() 
                                    ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold hover:bg-emerald-200' 
                                    : 'bg-white text-muted-green hover:bg-bone-canvas font-bold border border-bone-slate')"
                            class="py-2.5 px-1.5 sm:px-2 rounded-xl text-[10px] sm:text-xs transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer active:scale-95">
                        <span class="material-symbols-outlined text-xs sm:text-sm">
                            <span x-show="isStep1Valid()" x-cloak class="text-emerald-700 font-bold">check_circle</span>
                            <span x-show="!isStep1Valid()" :class="step === 1 ? 'text-lime-harvest font-bold' : 'text-muted-green'">key</span>
                        </span>
                        <span class="hidden sm:inline">1. Akun Login</span>
                        <span class="sm:hidden">1. Akun</span>
                    </button>

                    <!-- Tab 2: Kelembagaan -->
                    <button type="button" 
                            @click="setStep(2)"
                            :class="step === 2 
                                ? 'bg-forest-dark text-lime-harvest shadow-md font-black ring-2 ring-forest-dark/30 scale-[1.02]' 
                                : (isStep2Valid() 
                                    ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold hover:bg-emerald-200' 
                                    : (!isStep1Valid() 
                                        ? 'bg-bone-canvas/70 text-muted-green/60 border border-bone-slate cursor-not-allowed opacity-75' 
                                        : 'bg-white text-muted-green hover:bg-bone-canvas font-bold border border-bone-slate'))"
                            class="py-2.5 px-1.5 sm:px-2 rounded-xl text-[10px] sm:text-xs transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 active:scale-95"
                            :class="!isStep1Valid() ? 'cursor-not-allowed' : 'cursor-pointer'">
                        <span class="material-symbols-outlined text-xs sm:text-sm">
                            <span x-show="isStep2Valid()" x-cloak class="text-emerald-700 font-bold">check_circle</span>
                            <span x-show="!isStep2Valid() && !isStep1Valid()" class="text-muted-green/60">lock</span>
                            <span x-show="!isStep2Valid() && isStep1Valid()" :class="step === 2 ? 'text-lime-harvest font-bold' : 'text-muted-green'">potted_plant</span>
                        </span>
                        <span class="hidden sm:inline">2. Kelembagaan</span>
                        <span class="sm:hidden">2. Lembaga</span>
                    </button>

                    <!-- Tab 3: Luas Sawah -->
                    <button type="button" 
                            @click="setStep(3)"
                            :class="step === 3 
                                ? 'bg-forest-dark text-lime-harvest shadow-md font-black ring-2 ring-forest-dark/30 scale-[1.02]' 
                                : (isStep3Valid() 
                                    ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold hover:bg-emerald-200' 
                                    : ((!isStep1Valid() || !isStep2Valid()) 
                                        ? 'bg-bone-canvas/70 text-muted-green/60 border border-bone-slate cursor-not-allowed opacity-75' 
                                        : 'bg-white text-muted-green hover:bg-bone-canvas font-bold border border-bone-slate'))"
                            class="py-2.5 px-1.5 sm:px-2 rounded-xl text-[10px] sm:text-xs transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 active:scale-95"
                            :class="(!isStep1Valid() || !isStep2Valid()) ? 'cursor-not-allowed' : 'cursor-pointer'">
                        <span class="material-symbols-outlined text-xs sm:text-sm">
                            <span x-show="isStep3Valid()" x-cloak class="text-emerald-700 font-bold">check_circle</span>
                            <span x-show="!isStep3Valid() && (!isStep1Valid() || !isStep2Valid())" class="text-muted-green/60">lock</span>
                            <span x-show="!isStep3Valid() && isStep1Valid() && isStep2Valid()" :class="step === 3 ? 'text-lime-harvest font-bold' : 'text-muted-green'">landscape</span>
                        </span>
                        <span class="hidden sm:inline">3. Luas Sawah</span>
                        <span class="sm:hidden">3. Lahan</span>
                    </button>
                </div>
            </div>

            <!-- Formulir Registrasi Mandiri (Multi-Step Form) -->
            <form action="{{ route('register.post') }}" method="POST" id="register-form" novalidate class="space-y-6">
                @csrf

                <!-- ============================================================= -->
                <!-- LANGKAH 1: KREDENSIAL AKUN LOGIN                              -->
                <!-- ============================================================= -->
                <div x-show="step === 1" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">
                    
                    <div class="flex items-center gap-2 border-b border-bone-slate pb-2">
                        <span class="w-7 h-7 rounded-xl bg-forest-dark text-lime-harvest flex items-center justify-center text-xs font-black shadow-xs">1</span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-extrabold text-obsidian uppercase tracking-wider font-urbanist">
                                Kredensial Akun Pengguna
                            </h2>
                            <p class="text-[11px] text-muted-green">Digunakan oleh operator/ketua untuk login ke portal</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <!-- Nama Lengkap Operator -->
                        <div>
                            <label for="name" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Nama Operator / Pengelola <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">person</span>
                                </div>
                                <input type="text" id="name" name="name" x-model="name" value="{{ old('name') }}" 
                                       placeholder="Contoh: H. Ahmad Subandi"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('name') border-red-500 @enderror">
                            </div>
                            @error('name') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email Aktif -->
                        <div>
                            <label for="email" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Alamat Email Aktif <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">mail</span>
                                </div>
                                <input type="email" id="email" name="email" x-model="email" value="{{ old('email') }}" 
                                       placeholder="poktan.nama@gmail.com"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('email') border-red-500 @enderror">
                            </div>
                            @error('email') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Kata Sandi -->
                        <div>
                            <label for="password" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Kata Sandi (Min. 8 Karakter) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">lock</span>
                                </div>
                                <input type="password" id="password" name="password" x-model="password" 
                                       placeholder="Minimal 8 karakter"
                                       class="w-full pl-10 pr-10 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePasswordVisibility('password', 'pw-icon-1')" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted-green hover:text-obsidian transition">
                                    <span class="material-symbols-outlined text-lg" id="pw-icon-1">visibility</span>
                                </button>
                            </div>

                            <!-- Indikator Ceklis Ketentuan Sandi Interaktif -->
                            <div class="mt-2.5 p-3 rounded-2xl bg-bone-canvas/70 border border-bone-slate space-y-1.5 text-[11px]">
                                <div class="font-extrabold text-obsidian flex items-center justify-between">
                                    <span>Ketentuan Keamanan Sandi:</span>
                                    <span class="text-[10px] font-bold" :class="pwIsStrong() ? 'text-forest-dark' : 'text-muted-green'">
                                        <span x-show="pwIsStrong()" x-cloak class="inline-flex items-center gap-1 text-emerald-700">
                                            <span class="material-symbols-outlined text-xs">verified</span>
                                            Sandi Kuat & Terpenuhi
                                        </span>
                                        <span x-show="!pwIsStrong()">Wajib Terpenuhi</span>
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 pt-1">
                                    <div class="flex items-center gap-1.5 transition-colors" :class="pwHasMin() ? 'text-forest-dark font-extrabold' : 'text-muted-green'">
                                        <span class="material-symbols-outlined text-sm shrink-0" :class="pwHasMin() ? 'text-emerald-700 font-bold' : 'text-muted-green/50'">
                                            <span x-text="pwHasMin() ? 'check_circle' : 'radio_button_unchecked'"></span>
                                        </span>
                                        <span>Min. 8 Karakter</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors" :class="pwHasUpper() && pwHasLower() ? 'text-forest-dark font-extrabold' : 'text-muted-green'">
                                        <span class="material-symbols-outlined text-sm shrink-0" :class="pwHasUpper() && pwHasLower() ? 'text-emerald-700 font-bold' : 'text-muted-green/50'">
                                            <span x-text="pwHasUpper() && pwHasLower() ? 'check_circle' : 'radio_button_unchecked'"></span>
                                        </span>
                                        <span>Huruf Besar & Kecil</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 transition-colors" :class="pwHasNumber() ? 'text-forest-dark font-extrabold' : 'text-muted-green'">
                                        <span class="material-symbols-outlined text-sm shrink-0" :class="pwHasNumber() ? 'text-emerald-700 font-bold' : 'text-muted-green/50'">
                                            <span x-text="pwHasNumber() ? 'check_circle' : 'radio_button_unchecked'"></span>
                                        </span>
                                        <span>Mengandung Angka</span>
                                    </div>
                                </div>
                            </div>
                            @error('password') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Konfirmasi Kata Sandi -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Ulangi Kata Sandi <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">lock_reset</span>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" x-model="password_confirmation" 
                                       placeholder="Ulangi kata sandi di atas"
                                       class="w-full pl-10 pr-10 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'pw-icon-2')" aria-label="Tampilkan atau sembunyikan ulangi kata sandi" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted-green hover:text-obsidian transition">
                                    <span class="material-symbols-outlined text-lg" id="pw-icon-2">visibility</span>
                                </button>
                            </div>

                            <!-- Indikator Kecocokan Konfirmasi Kata Sandi -->
                            <div x-show="(password_confirmation || '').length > 0" x-cloak class="mt-1.5 flex items-center gap-1.5 text-xs font-bold"
                                 :class="pwMatches() ? 'text-forest-dark' : 'text-red-600'">
                                <span class="material-symbols-outlined text-sm" x-text="pwMatches() ? 'check_circle' : 'cancel'"></span>
                                <span x-text="pwMatches() ? 'Konfirmasi kata sandi cocok.' : 'Konfirmasi kata sandi belum sama.'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Navigasi Bawah Langkah 1 -->
                    <div class="pt-4 border-t border-bone-slate flex items-center justify-end">
                        <button type="button" 
                                @click="nextStep()"
                                class="w-full sm:w-auto px-7 py-3 rounded-full bg-forest-dark hover:bg-forest-deep text-white font-black text-xs sm:text-sm transition shadow-md flex items-center justify-center gap-2 active:scale-95">
                            <span>Lanjut ke Data Kelembagaan</span>
                            <span class="w-6 h-6 rounded-full bg-lime-harvest text-obsidian flex items-center justify-center text-xs font-bold">
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================= -->
                <!-- LANGKAH 2: PROFIL LEMBAGA KELOMPOK TANI                       -->
                <!-- ============================================================= -->
                <div x-show="step === 2" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">
                    
                    <div class="flex items-center gap-2 border-b border-bone-slate pb-2">
                        <span class="w-7 h-7 rounded-xl bg-forest-dark text-lime-harvest flex items-center justify-center text-xs font-black shadow-xs">2</span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-extrabold text-obsidian uppercase tracking-wider font-urbanist">
                                Profil Kelembagaan Kelompok Tani
                            </h2>
                            <p class="text-[11px] text-muted-green">Identitas organisasi Poktan resmi terdaftar di Kabupaten Subang</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <!-- Nama Kelompok Tani -->
                        <div>
                            <label for="nama_poktan" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Nama Kelompok Tani <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">groups</span>
                                </div>
                                <input type="text" id="nama_poktan" name="nama_poktan" x-model="nama_poktan" value="{{ old('nama_poktan') }}" 
                                       placeholder="Contoh: Poktan Berkah Tani"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('nama_poktan') border-red-500 @enderror">
                            </div>
                            @error('nama_poktan') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nomor SK Pengukuhan / Badan Hukum -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="nomor_badan_hukum" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider">
                                    No. SK / Badan Hukum
                                </label>
                                <span class="shrink-0 whitespace-nowrap text-[10px] text-muted-green font-bold bg-bone-canvas px-2 py-0.5 rounded-full border border-bone-slate">Opsional</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">verified</span>
                                </div>
                                <input type="text" id="nomor_badan_hukum" name="nomor_badan_hukum" x-model="nomor_badan_hukum" value="{{ old('nomor_badan_hukum') }}" 
                                       placeholder="Contoh: 521.1/SK-089/DISTAN/2023"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                            </div>
                        </div>

                        <!-- Nama Ketua Poktan -->
                        <div>
                            <label for="nama_ketua" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Nama Ketua Kelompok Tani <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">badge</span>
                                </div>
                                <input type="text" id="nama_ketua" name="nama_ketua" x-model="nama_ketua" value="{{ old('nama_ketua') }}" 
                                       placeholder="Nama lengkap ketua poktan"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('nama_ketua') border-red-500 @enderror">
                            </div>
                            @error('nama_ketua') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nomor Kontak / WhatsApp Ketua -->
                        <div>
                            <label for="no_hp_ketua" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Kontak WhatsApp / HP Ketua <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">call</span>
                                </div>
                                <input type="tel" 
                                       inputmode="numeric" 
                                       pattern="[0-9]*"
                                       id="no_hp_ketua" 
                                       name="no_hp_ketua" 
                                       x-model="no_hp_ketua" 
                                       value="{{ old('no_hp_ketua') }}" 
                                       @input="no_hp_ketua = no_hp_ketua.replace(/[^0-9]/g, '')"
                                       @keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
                                       placeholder="Contoh: 081234567890"
                                       maxlength="15"
                                       class="no-spin tabular-nums w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('no_hp_ketua') border-red-500 @enderror">
                            </div>
                            @error('no_hp_ketua') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Tombol Navigasi Bawah Langkah 2 -->
                    <div class="pt-4 border-t border-bone-slate flex items-center justify-between gap-3">
                        <button type="button" 
                                @click="prevStep()"
                                class="px-5 py-3 rounded-full bg-bone-canvas hover:bg-bone-slate text-forest-dark font-extrabold text-xs transition border border-bone-slate flex items-center gap-1.5 active:scale-95">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            <span>Kembali</span>
                        </button>

                        <button type="button" 
                                @click="nextStep()"
                                class="px-7 py-3 rounded-full bg-forest-dark hover:bg-forest-deep text-white font-black text-xs sm:text-sm transition shadow-md flex items-center gap-2 active:scale-95">
                            <span>Lanjut ke Hamparan Sawah</span>
                            <span class="w-6 h-6 rounded-full bg-lime-harvest text-obsidian flex items-center justify-center text-xs font-bold">
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================= -->
                <!-- LANGKAH 3: WILAYAH & KAPASITAS SAWAH HAMPARAN                 -->
                <!-- ============================================================= -->
                <div x-show="step === 3" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">
                    
                    <div class="flex items-center gap-2 border-b border-bone-slate pb-2">
                        <span class="w-7 h-7 rounded-xl bg-forest-dark text-lime-harvest flex items-center justify-center text-xs font-black shadow-xs">3</span>
                        <div>
                            <h2 class="text-xs sm:text-sm font-extrabold text-obsidian uppercase tracking-wider font-urbanist">
                                Wilayah & Hamparan Lahan Pertanian
                            </h2>
                            <p class="text-[11px] text-muted-green">Digunakan sebagai kriteria pembobotan ilmiah SPK TOPSIS</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                        <!-- Pilihan Kecamatan di Subang (30 Kecamatan) -->
                        <div>
                            <label for="kecamatan" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Kecamatan di Kab. Subang <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">location_city</span>
                                </div>
                                <select id="kecamatan" name="kecamatan" x-model="kecamatan" required
                                        class="w-full pl-10 pr-8 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('kecamatan') border-red-500 @enderror">
                                    <option value="">-- Pilih Kecamatan di Subang --</option>
                                    @php
                                        $daftarKecamatan = [
                                            'Binong', 'Blanakan', 'Ciasem', 'Ciater', 'Cibogo', 'Cijambe', 'Cikaum', 
                                            'Cipeundeuy', 'Cipunagara', 'Cisalak', 'Compreng', 'Dawuan', 'Jalancagak', 
                                            'Kalijati', 'Kasomalang', 'Legonkulon', 'Pabuaran', 'Pagaden', 'Pagaden Barat', 
                                            'Pamanukan', 'Patokbeusi', 'Purwadadi', 'Pusakajaya', 'Pusakanagara', 
                                            'Sagalaherang', 'Serangpanjang', 'Subang', 'Sukasari', 'Tambakdahan', 'Tanjungsiang'
                                        ];
                                    @endphp
                                    @foreach($daftarKecamatan as $kec)
                                        <option value="{{ $kec }}" {{ old('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kecamatan') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Desa / Kelurahan -->
                        <div>
                            <label for="desa" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Desa / Kelurahan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">home_pin</span>
                                </div>
                                <input type="text" id="desa" name="desa" x-model="desa" value="{{ old('desa') }}" 
                                       placeholder="Nama Desa"
                                       class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('desa') border-red-500 @enderror">
                            </div>
                            @error('desa') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Luas Lahan Hamparan -->
                        <div>
                            <label for="luas_lahan_total" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Luas Total Hamparan (Hektar) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">landscape</span>
                                </div>
                                <input type="number" step="0.1" min="0.1" id="luas_lahan_total" name="luas_lahan_total" x-model="luas_lahan_total" value="{{ old('luas_lahan_total', '25.0') }}" 
                                       class="no-spin tabular-nums w-full pl-10 pr-14 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-extrabold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('luas_lahan_total') border-red-500 @enderror">
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-bold text-muted-green">Ha</span>
                            </div>
                            @error('luas_lahan_total') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jumlah Anggota Petani Aktif -->
                        <div>
                            <label for="jumlah_anggota" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Anggota Petani Aktif (Orang) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                                    <span class="material-symbols-outlined text-lg">group</span>
                                </div>
                                <input type="number" min="1" id="jumlah_anggota" name="jumlah_anggota" x-model="jumlah_anggota" value="{{ old('jumlah_anggota', '30') }}" 
                                       class="no-spin tabular-nums w-full pl-10 pr-16 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-extrabold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('jumlah_anggota') border-red-500 @enderror">
                                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-bold text-muted-green">Orang</span>
                            </div>
                            @error('jumlah_anggota') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alamat Lengkap Sekretariat -->
                        <div class="sm:col-span-2">
                            <label for="alamat_lengkap" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1">
                                Alamat Sekretariat Poktan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea id="alamat_lengkap" name="alamat_lengkap" x-model="alamat_lengkap" rows="2" required 
                                          placeholder="Contoh: Dusun Sukamaju RT 03 RW 01, Desa Sukamaju..."
                                          class="w-full px-4 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('alamat_lengkap') border-red-500 @enderror">{{ old('alamat_lengkap') }}</textarea>
                            </div>
                            @error('alamat_lengkap') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Tombol Navigasi Bawah Langkah 3 (Final Submit) -->
                    <div class="pt-4 border-t border-bone-slate space-y-3">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                            <button type="button" 
                                    @click="prevStep()"
                                    class="w-full sm:w-auto px-5 py-3 rounded-full bg-bone-canvas hover:bg-bone-slate text-forest-dark font-extrabold text-xs transition border border-bone-slate flex items-center justify-center gap-1.5 active:scale-95 order-2 sm:order-1">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                <span>Kembali ke Data Lembaga</span>
                            </button>

                            <button type="submit" 
                                    class="w-full sm:w-auto py-3.5 px-7 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-black text-sm sm:text-base transition shadow-md flex items-center justify-between sm:justify-center gap-3 group active:scale-[0.99] border border-black/10 order-1 sm:order-2">
                                <span class="tracking-tight flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">how_to_reg</span>
                                    <span>Daftarkan Kelompok Tani Sekarang</span>
                                </span>
                                <span class="w-7 h-7 rounded-full bg-forest-dark text-lime-harvest flex items-center justify-center text-xs group-hover:rotate-45 transition-transform">
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </button>
                        </div>

                        <!-- Real-time Status Validation Helper -->
                        <div class="text-center text-xs pt-1">
                            <template x-if="isAllValid()">
                                <p class="text-emerald-700 font-bold inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200">
                                    <span class="material-symbols-outlined text-base">verified</span>
                                    <span>Seluruh 3 tahapan data telah lengkap & siap didaftarkan.</span>
                                </p>
                            </template>
                            <template x-if="!isAllValid()">
                                <p class="text-amber-800 font-bold inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200">
                                    <span class="material-symbols-outlined text-amber-600 text-base">info</span>
                                    <span x-show="!isStep1Valid()">Perhatian: Kredensial Akun (Langkah 1) belum lengkap.</span>
                                    <span x-show="isStep1Valid() && !isStep2Valid()">Perhatian: Data Profil Poktan (Langkah 2) belum lengkap.</span>
                                    <span x-show="isStep1Valid() && isStep2Valid() && !isStep3Valid()">Harap lengkapi semua isian wajib pada Langkah 3.</span>
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

            </form>

            <!-- Already Have an Account Link -->
            <div class="border-t border-bone-slate/80 pt-3 text-center">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-forest-dark hover:text-emerald-700 hover:underline">
                    <span class="material-symbols-outlined text-base">login</span>
                    <span>Sudah memiliki akun terdaftar? Kembali ke Halaman Masuk</span>
                </a>
            </div>

        </div>
    </main>

    <!-- Government Clean Footer -->
    <footer class="w-full text-center py-6 text-xs text-muted-green border-t border-bone-slate/60 relative z-10 backdrop-blur-xs">
        <div class="max-w-4xl mx-auto px-4">
            <p class="font-medium">
                &copy; {{ date('Y') }} Dinas Pertanian Pemerintah Daerah Kabupaten Subang. Hak Cipta Dilindungi.
            </p>
            <p class="text-[11px] text-muted-green/80 mt-0.5">
                Karya Utama Satya Nagara &bull; Standar Akuntabilitas & Penyaluran Alsintan
            </p>
        </div>
    </footer>

    <!-- Lightweight CSS/SVG Spinner Overlay on Registration Submit -->
    <div id="register-3d-loader" 
         class="fixed inset-0 z-[9999] bg-forest-dark/65 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none"
         style="display: none;">
        
        <div class="bg-white rounded-3xl border border-bone-slate p-7 sm:p-8 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center text-center space-y-5 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full bg-lime-harvest/25 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full bg-forest-dark/10 blur-2xl pointer-events-none"></div>

            <!-- Dual-Ring Harvesta CSS/SVG Spinner -->
            <div class="relative w-16 h-16 flex items-center justify-center" role="status" aria-label="Mendaftarkan akun">
                <svg class="w-16 h-16 animate-spin text-emerald-100" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="6"></circle>
                    <path d="M58 32a26 26 0 0 0-26-26" stroke="#14532D" stroke-width="6" stroke-linecap="round"></path>
                </svg>
                <svg class="w-11 h-11 absolute animate-spin" style="animation-duration: 1.4s; animation-direction: reverse;" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M40 24a16 16 0 0 1-16 16" stroke="#E1F12E" stroke-width="4.5" stroke-linecap="round"></path>
                </svg>
                <span class="material-symbols-outlined text-lg text-forest-dark absolute">agriculture</span>
            </div>

            <!-- Loader Message -->
            <div class="space-y-1.5 relative z-10">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-celery-cream text-forest-dark text-[11px] font-extrabold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-forest-dark animate-ping"></span>
                    <span>Pendaftaran Poktan</span>
                </div>
                <h3 class="text-lg font-black text-obsidian tracking-tight font-urbanist">
                    Mendaftarkan Kelompok Tani...
                </h3>
                <p class="text-xs text-muted-green mt-1 leading-relaxed">
                    Menyimpan kredensial login dan profil kelembagaan ke sistem database Dinas Pertanian Subang.
                </p>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Scripts -->
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        // Multi-Step Form Submission Validation & Lightweight Spinner Loader
        const registerForm = document.getElementById('register-form');
        const loader = document.getElementById('register-3d-loader');

        window.addEventListener('pageshow', function() {
            if (loader) {
                loader.classList.remove('opacity-100', 'pointer-events-auto');
                loader.classList.add('opacity-0', 'pointer-events-none');
                loader.style.display = 'none';
            }
        });

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                // Periksa kelengkapan Step 1
                const name = document.getElementById('name');
                const email = document.getElementById('email');
                const pw = document.getElementById('password');
                const pwConf = document.getElementById('password_confirmation');

                if (!name.value.trim() || !email.value.trim() || !email.checkValidity() || !pw.value || pw.value.length < 8 || pw.value !== pwConf.value) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('change-step', { detail: 1 }));
                    setTimeout(() => {
                        if (!name.value.trim()) { name.focus(); }
                        else if (!email.value.trim() || !email.checkValidity()) { email.focus(); }
                        else if (!pw.value) { pw.focus(); }
                        else if (pw.value.length < 8) { pw.focus(); }
                        else if (pw.value !== pwConf.value) { pwConf.focus(); }
                    }, 100);
                    return false;
                }

                // Periksa kelengkapan Step 2
                const poktan = document.getElementById('nama_poktan');
                const ketua = document.getElementById('nama_ketua');
                const hp = document.getElementById('no_hp_ketua');

                if (!poktan.value.trim() || !ketua.value.trim() || !hp.value.trim()) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('change-step', { detail: 2 }));
                    setTimeout(() => {
                        if (!poktan.value.trim()) { poktan.focus(); }
                        else if (!ketua.value.trim()) { ketua.focus(); }
                        else if (!hp.value.trim()) { hp.focus(); }
                    }, 100);
                    return false;
                }

                // Jika semua valid, tampilkan spinner pemrosesan ringan
                if (loader) {
                    loader.style.display = 'flex';
                    requestAnimationFrame(() => {
                        loader.classList.remove('opacity-0', 'pointer-events-none');
                        loader.classList.add('opacity-100', 'pointer-events-auto');
                    });
                }
            });
        }
    </script>
</body>
</html>
