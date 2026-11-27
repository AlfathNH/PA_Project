<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur Ulang Kata Sandi - Dinas Pertanian Kabupaten Subang</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : time() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}">

    <!-- Local Self-Hosted Fonts & Icons -->
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ file_exists(public_path('css/fonts.css')) ? filemtime(public_path('css/fonts.css')) : time() }}">

    <!-- Google 3D Model Viewer Engine (v4.4.0 dengan Subresource Integrity SHA-384) -->
    <script type="module"
            src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.4.0/dist/model-viewer.min.js"
            integrity="sha384-wjsPzEO+lXOf7d9csIDfAQpPt2OKuUU3m0K9qsdKIuzX4N8czSF6hdh23eAbZa3p"
            crossorigin="anonymous"></script>

    <!-- Pre-compiled Tailwind CSS Engine (First-Party) -->
    <link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}?v={{ file_exists(public_path('css/tailwind.min.css')) ? filemtime(public_path('css/tailwind.min.css')) : time() }}">

    <style>
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
    </style>
</head>
<body class="bg-bone-canvas text-obsidian min-h-screen flex flex-col justify-between topographic-grid antialiased font-sans relative overflow-x-hidden selection:bg-lime-harvest selection:text-obsidian">

    <!-- 3D Living Background Canvas -->
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

    <!-- Top Navigation Header -->
    <header class="w-full max-w-6xl mx-auto px-4 sm:px-6 pt-5 sm:pt-6 flex items-center justify-between relative z-10">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 sm:gap-2 text-xs font-extrabold text-forest-dark hover:text-forest-deep transition group px-3 py-1.5 sm:px-3.5 rounded-full bg-white/80 backdrop-blur-md border border-bone-slate shadow-2xs">
            <span class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
            <span class="hidden sm:inline">Kembali ke Halaman Masuk</span>
            <span class="sm:hidden">Masuk</span>
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-white/85 backdrop-blur-md border border-bone-slate text-forest-dark shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
            <span class="hidden sm:inline">Layanan Pemulihan Sandi</span>
            <span class="sm:hidden">Pemulihan Sandi</span>
        </span>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-md mx-auto px-4 py-8 relative z-10 flex-1 flex flex-col justify-center">
        
        <!-- Flash Messages & Unified Verification Error -->
        @php
            $lockoutSec = max((int) session('lockout_seconds', 0), (int) ($lockoutRemaining ?? 0));
            $authErrorMsg = $errors->first('auth_error') ?: $errors->first('email') ?: $errors->first('kode_legalitas') ?: session('error');
        @endphp

        @if($authErrorMsg || $lockoutSec > 0)
            <div class="mb-4 p-4 rounded-2xl bg-red-50/95 backdrop-blur-md border border-red-200 text-red-900 flex items-start gap-2.5 shadow-sm text-xs font-semibold">
                <span class="material-symbols-outlined text-red-600 text-lg shrink-0">{{ $lockoutSec > 0 ? 'timer' : 'gpp_maybe' }}</span>
                <div class="space-y-1 flex-1">
                    <p>{{ $authErrorMsg ?: 'Akses pemulihan dibatasi sementara karena 5 kali percobaan gagal.' }}</p>
                    @if($lockoutSec > 0)
                        <p class="text-[11px] font-extrabold text-red-800 tabular-nums">
                            Sisa waktu tunggu: <span id="lockout-countdown" class="font-mono bg-white px-2 py-0.5 rounded-md border border-red-200">--:--</span>
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Card Auth (Solid White Card - Clean, High Contrast & Non-Transparent) -->
        <div class="bg-white rounded-3xl border border-bone-slate shadow-2xl p-7 sm:p-9 space-y-6 relative overflow-hidden">
            
            <!-- Dinas Subang Emblem Header -->
            <div class="text-center space-y-3">
                <div class="inline-block p-2.5 rounded-2xl bg-bone-canvas/70 border border-bone-slate shadow-2xs">
                    <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                         alt="Logo Pemkab Subang" 
                         class="h-16 w-auto mx-auto object-contain filter drop-shadow">
                </div>
                <div>
                    <span class="text-[11px] font-bold tracking-widest text-muted-green uppercase block">
                        Pemerintah Daerah Kabupaten Subang
                    </span>
                    <h1 class="text-2xl font-black text-obsidian tracking-tight font-urbanist mt-0.5">
                        Pemulihan Kata Sandi
                    </h1>
                    <p class="text-xs text-muted-green font-medium mt-1">
                        Masukkan email akun terdaftar Anda untuk mengatur ulang kata sandi baru secara mandiri.
                    </p>
                </div>
            </div>

            <!-- Form Reset Password (novalidate mencegah tooltip browser menunjuk field spesifik yang keliru) -->
            <form id="forgot-form" action="{{ route('password.update') }}" method="POST" novalidate data-lockout="{{ (int) ($lockoutSec ?? 0) }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1.5">
                        Alamat Email Akun Terdaftar <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                            <span class="material-symbols-outlined text-lg">mail</span>
                        </div>
                        <input type="email" inputmode="email" autocomplete="email" id="email" name="email" value="{{ old('email') }}" 
                               placeholder="contoh: poktan.srimukti@gmail.com"
                               class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                    </div>
                </div>

                <!-- Kunci Konfirmasi Legalitas Poktan (Security Token / Nomor SK) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="kode_legalitas" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider">
                            Kunci Legalitas / No. SK Poktan <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[10px] text-muted-green font-bold">Verifikasi Kepemilikan Sah</span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                            <span class="material-symbols-outlined text-lg">verified_user</span>
                        </div>
                        <input type="text" id="kode_legalitas" name="kode_legalitas" value="{{ old('kode_legalitas') }}" 
                               placeholder="Cth: 521.1/SK-102/DISTAN/2021 atau No. HP Ketua"
                               class="w-full pl-10 pr-3.5 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                    </div>
                    <p class="text-[11px] text-muted-green mt-1 leading-tight">
                        <span class="material-symbols-outlined text-xs align-middle mr-1 text-forest-dark">verified_user</span>Masukkan <strong>Nomor SK Badan Hukum Poktan</strong> atau <strong>Nomor HP Ketua</strong> terdaftar secara lengkap. <em>(Untuk pegawai dinas: gunakan PIN Otorisasi Subang)</em>.
                    </p>
                </div>

                <!-- Password Baru Input -->
                <div>
                    <label for="password" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1.5">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                            <span class="material-symbols-outlined text-lg">lock_reset</span>
                        </div>
                        <input type="password" id="password" name="password" required 
                               placeholder="Min. 8 karakter (huruf besar, kecil & angka)"
                               class="w-full pl-10 pr-10 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePasswordVisibility('password', 'pw-icon-1')" aria-label="Tampilkan atau sembunyikan kata sandi baru" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted-green hover:text-obsidian transition">
                            <span class="material-symbols-outlined text-lg" id="pw-icon-1">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password Baru Input -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-extrabold text-forest-dark uppercase tracking-wider mb-1.5">
                        Ulangi Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-muted-green">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                        </div>
                        <input type="password" id="password_confirmation" name="password_confirmation" required 
                               placeholder="Ulangi kata sandi baru persis sama"
                               class="w-full pl-10 pr-10 py-3 bg-bone-canvas/80 border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'pw-icon-2')" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted-green hover:text-obsidian transition">
                            <span class="material-symbols-outlined text-lg" id="pw-icon-2">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="forgot-submit-btn"
                        @if($lockoutSec > 0) disabled @endif
                        class="w-full py-3.5 px-6 rounded-full {{ $lockoutSec > 0 ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian active:scale-[0.99]' }} font-extrabold text-sm transition shadow-md flex items-center justify-between group border border-black/10 mt-2">
                    <span class="tracking-tight" id="forgot-submit-label">{{ $lockoutSec > 0 ? 'Akses Dibekukan Sementara' : 'Simpan Kata Sandi Baru' }}</span>
                    <span class="w-8 h-8 rounded-full bg-forest-dark text-lime-harvest flex items-center justify-center text-xs group-hover:rotate-45 transition-transform">
                        <span class="material-symbols-outlined text-base">{{ $lockoutSec > 0 ? 'lock_clock' : 'check' }}</span>
                    </span>
                </button>
            </form>

            <!-- Callout Bantuan Hotline Helpdesk Kedinasan -->
            <div class="p-4 rounded-2xl bg-celery-cream/60 border border-bone-slate space-y-2">
                <div class="flex items-center gap-2 text-forest-dark font-extrabold text-xs">
                    <span class="material-symbols-outlined text-base text-forest-dark">support_agent</span>
                    <span>Layanan Bantuan Akun Poktan (Helpdesk)</span>
                </div>
                <p class="text-[11px] text-muted-green leading-relaxed">
                    Bagi pengurus Kelompok Tani yang mengalami kendala teknis atau lupa alamat email akun, silakan hubungi petugas Administrator Dinas Pertanian Kabupaten Subang melalui WhatsApp resmi:
                </p>
                <div class="pt-1 flex items-center justify-between text-xs">
                    <span class="font-bold text-obsidian">Pelayanan Alsintan:</span>
                    <a href="https://wa.me/6281223344551?text=Halo%20Admin%20Dinas%20Pertanian%20Subang,%20saya%20pengurus%20Poktan%20membutuhkan%20bantuan%20reset%20kata%20sandi%20akun." 
                       target="_blank" 
                       class="inline-flex items-center gap-1 font-extrabold text-forest-dark hover:underline bg-white px-2.5 py-1 rounded-lg border border-bone-slate">
                        <span class="material-symbols-outlined text-sm text-emerald-600">chat</span>
                        <span>0812-2334-4551</span>
                    </a>
                </div>
            </div>

            <!-- Back to Login Link -->
            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-extrabold text-forest-dark hover:underline inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Sudah ingat kata sandi? Masuk di sini</span>
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

    <!-- Lightweight CSS/SVG Spinner Overlay -->
    <div id="login-3d-loader" 
         class="fixed inset-0 z-[9999] bg-forest-dark/65 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none"
         style="display: none;">
        
        <div class="bg-white border border-bone-slate rounded-3xl p-7 sm:p-8 max-w-sm w-full mx-4 shadow-2xl flex flex-col items-center text-center space-y-5 relative overflow-hidden">
            
            <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full bg-lime-harvest/25 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-32 h-32 rounded-full bg-forest-dark/10 blur-2xl pointer-events-none"></div>

            <!-- Dual-Ring Harvesta CSS/SVG Spinner -->
            <div class="relative w-16 h-16 flex items-center justify-center" role="status" aria-label="Menyimpan kata sandi">
                <svg class="w-16 h-16 animate-spin text-emerald-100" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="32" r="26" stroke="currentColor" stroke-width="6"></circle>
                    <path d="M58 32a26 26 0 0 0-26-26" stroke="#14532D" stroke-width="6" stroke-linecap="round"></path>
                </svg>
                <svg class="w-11 h-11 absolute animate-spin" style="animation-duration: 1.4s; animation-direction: reverse;" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                    <path d="M40 24a16 16 0 0 1-16 16" stroke="#E1F12E" stroke-width="4.5" stroke-linecap="round"></path>
                </svg>
                <span class="material-symbols-outlined text-lg text-forest-dark absolute">lock_reset</span>
            </div>

            <div class="space-y-1.5 w-full relative z-10">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-celery-cream text-forest-dark text-[11px] font-extrabold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-forest-dark animate-ping"></span>
                    <span>Memperbarui Sandi</span>
                </div>
                <h3 class="text-base font-extrabold text-obsidian font-urbanist">
                    Menyimpan Kata Sandi Baru...
                </h3>
                <p class="text-xs text-muted-green leading-snug px-2">
                    Mengenkripsi kata sandi dan memperbarui basis data pengguna.
                </p>
            </div>
        </div>
    </div>

    <script>
        function showResetLoader() {
            const loader = document.getElementById('login-3d-loader');
            if (!loader) return;
            loader.style.display = 'flex';
            requestAnimationFrame(() => {
                loader.classList.remove('opacity-0', 'pointer-events-none');
                loader.classList.add('opacity-100', 'pointer-events-auto');
            });
        }

        window.addEventListener('pageshow', function () {
            const loader = document.getElementById('login-3d-loader');
            if (!loader) return;
            loader.classList.remove('opacity-100', 'pointer-events-auto');
            loader.classList.add('opacity-0', 'pointer-events-none');
            loader.style.display = 'none';
        });

        document.querySelector('form')?.addEventListener('submit', function () {
            showResetLoader();
        });

        (function initLockoutTimer() {
            const formEl = document.getElementById('forgot-form');
            let remaining = parseInt(formEl?.dataset.lockout || '0', 10);
            if (remaining <= 0) return;

            const badge = document.getElementById('lockout-countdown');
            const btn = document.getElementById('forgot-submit-btn');
            const label = document.getElementById('forgot-submit-label');

            function render() {
                const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                const s = String(remaining % 60).padStart(2, '0');
                if (badge) badge.textContent = m + ':' + s;
            }
            render();

            const timer = setInterval(() => {
                remaining -= 1;
                if (remaining <= 0) {
                    clearInterval(timer);
                    if (badge) badge.textContent = '00:00 (Silakan coba lagi)';
                    if (btn) {
                        btn.disabled = false;
                        btn.className = 'w-full py-3.5 px-6 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian active:scale-[0.99] font-extrabold text-sm transition shadow-md flex items-center justify-between group border border-black/10 mt-2';
                    }
                    if (label) label.textContent = 'Simpan Kata Sandi Baru';
                    return;
                }
                render();
            }, 1000);
        })();

        function togglePasswordVisibility(fieldId, iconId) {
            const pwInput = document.getElementById(fieldId);
            const pwIcon = document.getElementById(iconId);
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                pwIcon.innerText = 'visibility_off';
            } else {
                pwInput.type = 'password';
                pwIcon.innerText = 'visibility';
            }
        }
    </script>
</body>
</html>

