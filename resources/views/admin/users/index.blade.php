@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna - Dinas Pertanian Kab. Subang')

@section('content')
<div x-data="{
        createModalOpen: {{ ($errors->any() && old('role') !== null) ? 'true' : 'false' }},
        showCreatePw: false,
        showCreatePwConfirm: false,
        resetModalOpen: false,
        editModalOpen: false,

        // Data Reset Password
        resetUserId: null,
        resetUserName: '',
        resetUserEmail: '',

        // Data Edit User
        editUserId: null,
        editName: '',
        editEmail: '',
        editIsActive: true,
        editIsPoktan: false,
        editKetua: '',
        editNoHp: '',
        editDesa: '',
        editKecamatan: '',
        editLuasLahan: 0,
        editAnggota: 0,

        openReset(id, name, email) {
            this.resetUserId = id;
            this.resetUserName = name;
            this.resetUserEmail = email;
            this.resetModalOpen = true;
        },

        openEdit(user, poktan) {
            this.editUserId = user.id;
            this.editName = user.name;
            this.editEmail = user.email;
            this.editIsActive = Boolean(user.is_active);
            this.editIsPoktan = (user.role === 'kelompok_tani');

            if (poktan) {
                this.editKetua = poktan.nama_ketua || '';
                this.editNoHp = poktan.no_hp_ketua || '';
                this.editDesa = poktan.desa || '';
                this.editKecamatan = poktan.kecamatan || '';
                this.editLuasLahan = poktan.luas_lahan_total || 0;
                this.editAnggota = poktan.jumlah_anggota || 0;
            } else {
                this.editKetua = '';
                this.editNoHp = '';
                this.editDesa = '';
                this.editKecamatan = '';
                this.editLuasLahan = 0;
                this.editAnggota = 0;
            }

            this.editModalOpen = true;
        }
     }" 
     class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">

    <!-- Top Banner / Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-3xl p-6 sm:p-7 border border-bone-slate shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-lime-harvest/50 text-forest-dark border border-lime-harvest">
                    <span class="material-symbols-outlined text-xs mr-1 text-forest-dark">manage_accounts</span>
                    Pusat Kendali Pengguna & Hak Akses
                </span>
                <span class="text-xs text-muted-green font-semibold">Standar Kepegawaian & Simluhtan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight font-urbanist">
                Manajemen Akun Pengguna Portal Alsintan
            </h1>
            <p class="text-xs sm:text-sm text-muted-green mt-1 max-w-3xl">
                Kelola akun Kelompok Tani (Poktan), verifikasi keabsahan data registrasi mandiri, bekukan akun bermasalah, dan bantu reset kata sandi petani senior yang lupa.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <button type="button"
                    @click="createModalOpen = true"
                    class="px-4 py-2.5 min-h-[44px] rounded-full text-xs font-black bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10 transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                <span class="material-symbols-outlined text-base">person_add</span>
                <span>Tambah Pegawai Dinas</span>
            </button>
            <a href="{{ route('admin.dashboard') }}" 
               class="px-4 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark border border-bone-slate transition flex items-center gap-1.5 shadow-2xs">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards Ringkasan Akun -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Akun -->
        <div class="p-5 rounded-2xl bg-white border border-bone-slate shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green">Total Seluruh Akun</span>
                <div class="w-8 h-8 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">people</span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-obsidian font-urbanist">
                {{ $stats['total_users'] }}
            </div>
            <span class="text-[11px] text-muted-green block">Terdaftar resmi di database</span>
        </div>

        <!-- Kelompok Tani -->
        <div class="p-5 rounded-2xl bg-white border border-bone-slate shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green">Kelompok Tani (Poktan)</span>
                <div class="w-8 h-8 rounded-xl bg-lime-harvest/40 text-forest-dark flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">agriculture</span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-forest-dark font-urbanist">
                {{ $stats['total_poktan'] }}
            </div>
            <span class="text-[11px] text-muted-green block">Pemohon bantuan Alsintan</span>
        </div>

        <!-- Pegawai Kedinasan -->
        <div class="p-5 rounded-2xl bg-white border border-bone-slate shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green">Pejabat & Operator</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">badge</span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-blue-900 font-urbanist">
                {{ $stats['total_dinas'] }}
            </div>
            <span class="text-[11px] text-muted-green block">Admin & Kepala Bidang</span>
        </div>

        <!-- Akun Nonaktif / Dibekukan -->
        <div class="p-5 rounded-2xl bg-white border border-bone-slate shadow-2xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green">Status Dibekukan</span>
                <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-base">block</span>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-red-700 font-urbanist">
                {{ $stats['total_nonaktif'] }}
            </div>
            <span class="text-[11px] text-muted-green block">Akses masuk dinonaktifkan</span>
        </div>
    </div>

    <!-- Main Container: Tabel Akun & Bar Pencarian / Filter -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
        
        <!-- Header Tabel & Search / Filter -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-bone-slate/60 pb-5">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    Daftar Pengguna Portal Bantuan Alsintan
                </h2>
                <p class="text-xs text-muted-green mt-0.5">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari total {{ $users->total() }} akun
                </p>
            </div>

            <!-- Form Pencarian & Filter Cepat -->
            <form action="{{ route('admin.users.index') }}" method="GET" class="no-loader grid grid-cols-1 sm:flex sm:flex-wrap items-stretch sm:items-center gap-2">
                <!-- Search Input -->
                <div class="relative sm:min-w-[230px] flex-1 sm:flex-initial">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-muted-green text-base">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, email, poktan, desa..." 
                           class="w-full pl-9 pr-3.5 py-2.5 min-h-[44px] bg-bone-canvas/70 border border-bone-slate rounded-xl text-xs font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition shadow-2xs">
                </div>

                <div class="grid grid-cols-2 sm:flex items-center gap-2">
                    <!-- Role Filter -->
                    <select name="role" class="w-full sm:w-auto px-3 py-2.5 min-h-[44px] bg-bone-canvas/70 border border-bone-slate rounded-xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark transition shadow-2xs">
                        <option value="">Semua Peran</option>
                        <option value="kelompok_tani" {{ request('role') == 'kelompok_tani' ? 'selected' : '' }}>Kelompok Tani</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin Verifikator</option>
                        <option value="kepala_bidang" {{ request('role') == 'kepala_bidang' ? 'selected' : '' }}>Kepala Bidang</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="w-full sm:w-auto px-3 py-2.5 min-h-[44px] bg-bone-canvas/70 border border-bone-slate rounded-xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark transition shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Dibekukan</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial justify-center px-4 py-2.5 min-h-[44px] rounded-xl text-xs font-bold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10 transition shadow-2xs flex items-center gap-1 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">filter_alt</span>
                        <span>Terapkan</span>
                    </button>

                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-3.5 py-2.5 min-h-[44px] rounded-xl text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-muted-green border border-bone-slate transition inline-flex items-center justify-center"
                           aria-label="Reset filter pengguna"
                           title="Reset Filter">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- TAMPILAN KARTU MOBILE (< md): Bebas Geser Samping -->
        <div class="space-y-3 md:hidden">
            @forelse($users as $user)
                @php
                    $poktan = $user->kelompokTani;
                    $roleLabel = match($user->role) {
                        'admin'         => 'Admin Verifikator',
                        'kepala_bidang' => 'Kepala Bidang',
                        'kelompok_tani' => 'Kelompok Tani',
                        default         => $user->role,
                    };
                    $roleBadgeColor = match($user->role) {
                        'admin'         => 'bg-blue-50 text-blue-800 border-blue-200',
                        'kepala_bidang' => 'bg-amber-50 text-amber-900 border-amber-300',
                        'kelompok_tani' => 'bg-celery-cream text-forest-dark border-bone-slate',
                        default         => 'bg-slate-100 text-slate-800',
                    };
                @endphp
                <div class="p-4 rounded-2xl border border-bone-slate shadow-2xs space-y-3 {{ !$user->is_active ? 'bg-red-50/30' : 'bg-bone-canvas/30' }}">
                    <div class="flex items-start justify-between gap-2.5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center font-extrabold text-xs shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-sm text-obsidian flex items-center gap-1.5 flex-wrap">
                                    <span class="truncate">{{ $user->name }}</span>
                                    @if($user->id === Auth::id())
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-forest-dark text-lime-harvest">Anda</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-muted-green truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        @if($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1"></span>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-red-50 text-red-800 border border-red-200 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1"></span>Dibekukan
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $roleBadgeColor }}">
                            {{ $roleLabel }}
                        </span>
                        @if($user->isPoktan() && $poktan)
                            <span class="text-[11px] text-obsidian font-bold">{{ $poktan->nama_poktan }}</span>
                        @else
                            <span class="text-[11px] text-muted-green">Pegawai Dinas Pertanian Kab. Subang</span>
                        @endif
                    </div>

                    @if($user->isPoktan() && $poktan)
                        <div class="p-2.5 rounded-xl bg-white border border-bone-slate/80 text-[11px] text-muted-green space-y-0.5">
                            <div>Desa {{ $poktan->desa }}, Kec. {{ $poktan->kecamatan }}</div>
                            <div class="font-mono tabular-nums text-[10px]">
                                Ketua: {{ $poktan->nama_ketua }} ({{ $poktan->no_hp_ketua }}) &bull; {{ $poktan->luas_lahan_total }} Ha
                            </div>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-bone-slate/60 flex flex-wrap items-center gap-2">
                        <button type="button"
                                @click="openReset({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                                class="flex-1 min-h-[44px] px-3 py-2 rounded-xl text-xs font-bold bg-white hover:bg-amber-50 text-amber-900 border border-bone-slate transition flex items-center justify-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-sm text-amber-700">lock_reset</span>
                            <span>Reset</span>
                        </button>

                        <button type="button"
                                @click="openEdit({{ json_encode($user) }}, {{ json_encode($poktan) }})"
                                class="flex-1 min-h-[44px] px-3 py-2 rounded-xl text-xs font-bold bg-white hover:bg-celery-cream text-forest-dark border border-bone-slate transition flex items-center justify-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            <span>Ubah</span>
                        </button>

                        @if($user->id !== Auth::id())
                            <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @if($user->is_active)
                                    <button type="submit"
                                            onclick="return confirm('Apakah Anda yakin ingin menonaktifkan / membekukan akun ini? Pengguna tidak akan dapat masuk ke sistem.')"
                                            class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-xl text-xs font-bold bg-white hover:bg-red-50 text-red-700 border border-bone-slate hover:border-red-200 transition flex items-center justify-center cursor-pointer"
                                            aria-label="Bekukan akun {{ $user->name }}"
                                            title="Bekukan akun">
                                        <span class="material-symbols-outlined text-base">block</span>
                                    </button>
                                @else
                                    <button type="submit"
                                            onclick="return confirm('Aktifkan kembali akun ini agar dapat mengakses portal?')"
                                            class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition flex items-center justify-center cursor-pointer"
                                            aria-label="Aktifkan kembali akun {{ $user->name }}"
                                            title="Aktifkan kembali akun">
                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-muted-green text-xs bg-bone-canvas/40 rounded-2xl border border-dashed border-bone-slate">
                    <span class="material-symbols-outlined text-3xl mb-1 text-slate-400 block">search_off</span>
                    Tidak ada akun pengguna yang sesuai dengan kriteria pencarian atau filter.
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN TABEL DESKTOP (>= md) -->
        <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden rounded-2xl border border-bone-slate/80 shadow-2xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-bone-canvas/70 border-b border-bone-slate text-[11px] font-extrabold text-muted-green uppercase tracking-wider">
                        <th class="py-3 px-4 whitespace-nowrap">Pengguna</th>
                        <th class="py-3 px-4 whitespace-nowrap">Peran / Otoritas</th>
                        <th class="py-3 px-4 whitespace-nowrap">Profil & Wilayah Poktan</th>
                        <th class="py-3 px-4 text-center whitespace-nowrap">Status Akun</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bone-slate/60 text-xs">
                    @forelse($users as $user)
                        @php
                            $poktan = $user->kelompokTani;
                            $roleLabel = match($user->role) {
                                'admin'         => 'Admin Verifikator',
                                'kepala_bidang' => 'Kepala Bidang',
                                'kelompok_tani' => 'Kelompok Tani',
                                default         => $user->role,
                            };
                            $roleBadgeColor = match($user->role) {
                                'admin'         => 'bg-blue-50 text-blue-800 border-blue-200',
                                'kepala_bidang' => 'bg-amber-50 text-amber-900 border-amber-300',
                                'kelompok_tani' => 'bg-celery-cream text-forest-dark border-bone-slate',
                                default         => 'bg-slate-100 text-slate-800',
                            };
                        @endphp
                        <tr class="hover:bg-bone-canvas/40 transition-colors {{ !$user->is_active ? 'bg-red-50/20' : '' }}">
                            
                            <!-- Pengguna Info -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center font-extrabold text-xs shadow-2xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-obsidian text-sm flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === Auth::id())
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded bg-forest-dark text-lime-harvest">
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-muted-green font-medium">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $roleBadgeColor }}">
                                    @if($user->isAdmin())
                                        <span class="material-symbols-outlined text-xs mr-1 text-blue-700">admin_panel_settings</span>
                                    @elseif($user->isKepBid())
                                        <span class="material-symbols-outlined text-xs mr-1 text-amber-700">workspace_premium</span>
                                    @else
                                        <span class="material-symbols-outlined text-xs mr-1 text-forest-dark">groups</span>
                                    @endif
                                    {{ $roleLabel }}
                                </span>
                            </td>

                            <!-- Wilayah / Profil Poktan -->
                            <td class="py-3.5 px-4">
                                @if($user->isPoktan() && $poktan)
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-obsidian text-xs">
                                            {{ $poktan->nama_poktan }}
                                        </div>
                                        <div class="text-[11px] text-muted-green">
                                            Desa {{ $poktan->desa }}, Kec. {{ $poktan->kecamatan }}
                                        </div>
                                        <div class="text-[10px] text-muted-green/80 font-mono">
                                            Ketua: {{ $poktan->nama_ketua }} ({{ $poktan->no_hp_ketua }}) &bull; Lahan: {{ $poktan->luas_lahan_total }} Ha
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-muted-green italic">
                                        Pegawai Dinas Pertanian Kab. Subang
                                    </span>
                                @endif
                            </td>

                            <!-- Status Keaktifan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-red-50 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-1.5"></span>
                                        Dibekukan
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    
                                    <!-- Tombol Reset Password Petani -->
                                    <button type="button" 
                                            @click="openReset({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}')"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-white hover:bg-amber-50 text-amber-900 border border-bone-slate hover:border-amber-300 transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                            title="Bantu reset kata sandi petani">
                                        <span class="material-symbols-outlined text-sm text-amber-700">lock_reset</span>
                                        <span>Reset Sandi</span>
                                    </button>

                                    <!-- Tombol Edit Akun -->
                                    <button type="button" 
                                            @click="openEdit({{ json_encode($user) }}, {{ json_encode($poktan) }})"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-white hover:bg-celery-cream text-forest-dark border border-bone-slate transition flex items-center gap-1 shadow-2xs cursor-pointer"
                                            title="Ubah profil akun">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Ubah</span>
                                    </button>

                                    <!-- Tombol Toggle Status (Bekukan / Aktifkan) -->
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.toggle_status', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @if($user->is_active)
                                                <button type="submit" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menonaktifkan / membekukan akun ini? Pengguna tidak akan dapat masuk ke sistem.')"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-white hover:bg-red-50 text-red-700 border border-bone-slate hover:border-red-200 transition shadow-2xs cursor-pointer"
                                                        title="Bekukan akun">
                                                    <span class="material-symbols-outlined text-sm">block</span>
                                                </button>
                                            @else
                                                <button type="submit" 
                                                        onclick="return confirm('Aktifkan kembali akun ini agar dapat mengakses portal?')"
                                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-2xs cursor-pointer"
                                                        title="Aktifkan kembali akun">
                                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                                </button>
                                            @endif
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-muted-green text-xs">
                                <span class="material-symbols-outlined text-3xl mb-1 text-slate-400 block">search_off</span>
                                Tidak ada akun pengguna yang sesuai dengan kriteria pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
            {{ $users->links('vendor.pagination.custom-tailwind') }}
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MODAL 1: RESET KATA SANDI OLEH ADMIN                      -->
    <!-- ========================================================= -->
    <div x-show="resetModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-obsidian/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="resetModalOpen = false" 
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 border border-bone-slate shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-bone-slate pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-lg">lock_reset</span>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                            Reset Kata Sandi Akun
                        </h3>
                        <p class="text-[11px] text-muted-green">Bantuan atur ulang sandi pengguna</p>
                    </div>
                </div>
                <button type="button" @click="resetModalOpen = false" aria-label="Tutup jendela reset kata sandi" class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas transition">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + resetUserId + '/reset-password'" method="POST" class="space-y-4">
                @csrf
                
                <div class="p-3.5 rounded-2xl bg-bone-canvas/60 border border-bone-slate space-y-1">
                    <div class="text-xs text-muted-green font-semibold">Akun yang Direset:</div>
                    <div class="font-extrabold text-sm text-obsidian" x-text="resetUserName"></div>
                    <div class="text-xs text-muted-green font-mono" x-text="resetUserEmail"></div>
                </div>

                <div>
                    <label for="reset_pw" class="block text-xs font-bold text-obsidian mb-1">
                        Kata Sandi Baru:
                    </label>
                    <input type="text" 
                           id="reset_pw" 
                           name="password" 
                           value="Subang2026!" 
                           required 
                           minlength="8"
                           class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-mono font-bold text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                    <p class="text-[11px] text-muted-green mt-1">
                        <span class="material-symbols-outlined text-xs align-middle mr-0.5 text-amber-700">info</span> <em>Kata sandi bawaan:</em> <strong>Subang2026!</strong> (min. 8 karakter, kombinasi huruf besar, kecil, dan angka). Pengguna dapat mengubahnya mandiri setelah masuk kembali.
                    </p>
                </div>

                <div class="pt-2 flex items-center justify-end space-x-2">
                    <button type="button" 
                            @click="resetModalOpen = false" 
                            class="px-4 py-2 rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-full text-xs font-extrabold bg-amber-500 hover:bg-amber-600 text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">lock_reset</span>
                        <span>Konfirmasi Reset Sandi</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 2: UBAH PROFIL AKUN PENGGUNA                        -->
    <!-- ========================================================= -->
    <div x-show="editModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-obsidian/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="editModalOpen = false" 
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 border border-bone-slate shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-bone-slate pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-lg">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                            Perbarui Data Profil Akun
                        </h3>
                        <p class="text-[11px] text-muted-green">Koreksi identitas akun atau nomor kontak</p>
                    </div>
                </div>
                <button type="button" @click="editModalOpen = false" aria-label="Tutup jendela ubah profil pengguna" class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas transition">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + editUserId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="edit_u_name" class="block text-xs font-bold text-obsidian mb-1">
                            Nama Akun / Kontak:
                        </label>
                        <input type="text" 
                               id="edit_u_name" 
                               name="name" 
                               x-model="editName" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-xs text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                    </div>
                    <div>
                        <label for="edit_u_email" class="block text-xs font-bold text-obsidian mb-1">
                            Alamat Email:
                        </label>
                        <input type="email" 
                               id="edit_u_email" 
                               name="email" 
                               x-model="editEmail" 
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-xs text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                    </div>
                </div>

                <!-- Fields Khusus Kelompok Tani -->
                <template x-if="editIsPoktan">
                    <div class="space-y-3 pt-2 border-t border-bone-slate/60">
                        <span class="text-[11px] font-extrabold uppercase text-forest-dark tracking-wider block">
                            Identitas Kelompok Tani Binaan:
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">Nama Ketua Poktan:</label>
                                <input type="text" name="nama_ketua" x-model="editKetua" class="w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">No. WhatsApp / HP:</label>
                                <input type="text" name="no_hp_ketua" x-model="editNoHp" class="w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">Desa:</label>
                                <input type="text" name="desa" x-model="editDesa" class="w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">Kecamatan:</label>
                                <input type="text" name="kecamatan" x-model="editKecamatan" class="w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">Luas Lahan (Ha):</label>
                                <input type="number" step="0.01" min="0.01" max="10000" name="luas_lahan_total" x-model="editLuasLahan" class="no-spin tabular-nums w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-obsidian mb-1">Jumlah Anggota:</label>
                                <input type="number" min="1" max="5000" step="1" name="jumlah_anggota" x-model="editAnggota" class="no-spin tabular-nums w-full px-3 py-2 rounded-xl border border-bone-slate text-xs font-semibold text-obsidian font-mono">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Checkbox Status Aktif (Hidden input memastikan nilai 0 tetap terkirim saat tidak dicentang) -->
                <div class="p-3 rounded-2xl bg-bone-canvas/70 border border-bone-slate space-y-1.5">
                    <div class="flex items-center justify-between gap-2 min-h-[28px]">
                        <div class="flex items-center space-x-2.5">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   id="edit_user_active" 
                                   name="is_active" 
                                   value="1" 
                                   x-model="editIsActive" 
                                   class="w-4 h-4 rounded text-forest-dark focus:ring-forest-dark border-bone-slate cursor-pointer">
                            <label for="edit_user_active" class="text-xs font-bold text-obsidian cursor-pointer select-none">
                                Status Akun Aktif (Dapat masuk ke portal)
                            </label>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border shrink-0"
                              :class="editIsActive ? 'bg-lime-harvest/40 text-forest-dark border-lime-harvest' : 'bg-red-50 text-red-800 border-red-200'"
                              x-text="editIsActive ? 'Aktif' : 'Dibekukan'">
                        </span>
                    </div>
                    <p x-show="editUserId === {{ Auth::id() }}" x-cloak class="text-[11px] text-amber-800 font-semibold">
                        Catatan: Akun Anda sendiri tidak dapat dinonaktifkan selama sesi aktif berlangsung.
                    </p>
                </div>

                <div class="pt-2 flex items-center justify-end space-x-2">
                    <button type="button" 
                            @click="editModalOpen = false" 
                            class="px-4 py-2 rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-full text-xs font-extrabold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">check</span>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL 3: TAMBAH AKUN PEGAWAI DINAS (ADMIN / KEPBID)       -->
    <!-- ========================================================= -->
    <div x-show="createModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-obsidian/60 backdrop-blur-xs"
         style="display: none;">
        
        <div @click.away="createModalOpen = false" 
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 border border-bone-slate shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-bone-slate pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-lime-harvest/40 text-forest-dark flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                            Tambah Akun Pegawai Dinas Baru
                        </h3>
                        <p class="text-[11px] text-muted-green">Khusus peran internal (Admin Verifikator &amp; Kepala Bidang)</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" aria-label="Tutup formulir tambah akun pegawai" class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas transition">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <div class="p-3 rounded-2xl bg-celery-cream/50 border border-bone-slate text-[11px] text-forest-dark leading-relaxed">
                <span class="material-symbols-outlined text-xs align-middle mr-1">verified_user</span>
                Demi keamanan hak akses (RBAC), registrasi depan hanya berlaku bagi Kelompok Tani. Penambahan akun <strong>Admin Verifikator</strong> maupun <strong>Kepala Bidang</strong> hanya dapat dilakukan oleh Admin Dinas melalui formulir ini.
            </div>

            @if($errors->any() && old('role') !== null)
                <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-900 space-y-1 text-xs">
                    <div class="font-extrabold flex items-center gap-1.5 text-red-800">
                        <span class="material-symbols-outlined text-base text-red-600">error</span>
                        <span>Gagal membuat akun pegawai:</span>
                    </div>
                    <ul class="list-disc list-inside pl-1 space-y-0.5 text-red-700 font-semibold">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="create_role" class="block text-xs font-bold text-obsidian mb-1">
                        Peran Otoritas Pegawai <span class="text-red-600">*</span>
                    </label>
                    <select id="create_role" name="role" required
                            class="w-full px-3.5 py-2.5 min-h-[44px] rounded-xl border-2 @error('role') border-red-500 @else border-bone-slate @enderror bg-white text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin Verifikator (Verifikasi Berkas, Penilaian &amp; TOPSIS)</option>
                        <option value="kepala_bidang" {{ old('role') === 'kepala_bidang' ? 'selected' : '' }}>Kepala Bidang (Penetapan Persetujuan ACC &amp; Berita Acara)</option>
                    </select>
                    @error('role') <p class="text-[11px] text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="create_name" class="block text-xs font-bold text-obsidian mb-1">
                        Nama Lengkap &amp; Gelar <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="create_name" name="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Ir. Budi Santoso, M.P."
                           class="w-full px-3.5 py-2.5 min-h-[44px] rounded-xl border-2 @error('name') border-red-500 @else border-bone-slate @enderror bg-white text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark">
                    @error('name') <p class="text-[11px] text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="create_email" class="block text-xs font-bold text-obsidian mb-1">
                        Alamat Email Aktif <span class="text-red-600">*</span>
                    </label>
                    <input type="email" id="create_email" name="email" value="{{ old('email') }}" required
                           placeholder="Contoh: pegawai2@gmail.com (belum pernah dipakai akun lain)"
                           class="w-full px-3.5 py-2.5 min-h-[44px] rounded-xl border-2 @error('email') border-red-500 @else border-bone-slate @enderror bg-white text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark">
                    @error('email') <p class="text-[11px] text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="create_password" class="block text-xs font-bold text-obsidian mb-1">
                            Kata Sandi Awal <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showCreatePw ? 'text' : 'password'" id="create_password" name="password" required minlength="8"
                                   placeholder="Contoh: Subang2026"
                                   class="w-full pl-3.5 pr-10 py-2.5 min-h-[44px] rounded-xl border-2 @error('password') border-red-500 @else border-bone-slate @enderror bg-white text-xs font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark">
                            <button type="button" @click="showCreatePw = !showCreatePw" aria-label="Tampilkan atau sembunyikan kata sandi awal"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-green hover:text-obsidian">
                                <span class="material-symbols-outlined text-base" x-text="showCreatePw ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        @error('password') <p class="text-[11px] text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="create_password_confirmation" class="block text-xs font-bold text-obsidian mb-1">
                            Ulangi Kata Sandi <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showCreatePwConfirm ? 'text' : 'password'" id="create_password_confirmation" name="password_confirmation" required minlength="8"
                                   placeholder="Ulangi kata sandi"
                                   class="w-full pl-3.5 pr-10 py-2.5 min-h-[44px] rounded-xl border-2 border-bone-slate bg-white text-xs font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark">
                            <button type="button" @click="showCreatePwConfirm = !showCreatePwConfirm" aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi"
                                    class="absolute inset-y-0 right-0 px-3 flex items-center text-muted-green hover:text-obsidian">
                                <span class="material-symbols-outlined text-base" x-text="showCreatePwConfirm ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] text-muted-green">
                    Wajib minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, dan angka.
                </p>

                <div class="pt-2 flex items-center justify-end space-x-2">
                    <button type="button"
                            @click="createModalOpen = false"
                            class="px-4 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 min-h-[44px] rounded-full text-xs font-extrabold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">person_add</span>
                        <span>Buat Akun Pegawai</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

