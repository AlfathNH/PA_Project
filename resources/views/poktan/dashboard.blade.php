@extends('layouts.app')

@section('title', 'Dashboard Kelompok Tani - SPK Bantuan Alsintan Subang')

@section('content')
@php
    $initialMobileTab = request('tab')
        ?: ($errors->hasAny(['nama_poktan', 'nomor_badan_hukum', 'nama_ketua', 'no_hp_ketua', 'luas_lahan_total', 'jumlah_anggota', 'desa', 'kecamatan', 'alamat_lengkap'])
            ? 'profil'
            : ($errors->hasAny(['jenis_alsintan_id', 'tahun_pengajuan', 'jumlah_diminta', 'jumlah_alsintan_eksisting', 'alasan_pengajuan', 'berkas_proposal'])
                ? 'form'
                : 'status'));
@endphp
<div x-data="{ 
        mobileTab: '{{ $initialMobileTab }}', 
        editProfileModal: false,
        ratingModal: {{ session('show_rating_popup') ? 'true' : 'false' }},
        ratingScore: 5,
        ratingHover: 5,
        getRatingLabel() {
            const labels = {
                1: 'Sangat Kurang Memuaskan ⭐',
                2: 'Kurang Memuaskan ⭐⭐',
                3: 'Cukup Baik & Terbantu ⭐⭐⭐',
                4: 'Puas & Pelayanan Ramah ⭐⭐⭐⭐',
                5: 'Sangat Puas & Transparan ⭐⭐⭐⭐⭐'
            };
            return labels[this.ratingHover || this.ratingScore] || '';
        }
     }"
     @open-poktan-profile-modal.window="editProfileModal = true"
     @open-rating-modal.window="ratingModal = true"
     class="space-y-4 sm:space-y-6 pb-36 sm:pb-40 lg:pb-8 relative">

    <!-- Ringkasan Identitas Poktan Ringkas (Tampil di Mobile saat tab Status / Ajukan aktif) -->
    <div x-show="mobileTab !== 'profil'"
         class="lg:hidden bg-forest-dark text-white rounded-2xl px-4 py-3 shadow-sm border border-forest-deep flex items-center justify-between gap-3">
        <div class="min-w-0">
            <span class="text-[10px] font-bold uppercase tracking-wider text-lime-harvest block">Kelompok Tani Aktif</span>
            <p class="text-sm font-extrabold text-white truncate font-urbanist">{{ $poktan?->nama_poktan ?? 'Kelompok Tani' }}</p>
            <p class="text-[11px] text-celery-cream/90 tabular-nums truncate">
                {{ number_format($poktan?->luas_lahan_total ?? 0, 1) }} Ha &bull; {{ $poktan?->jumlah_anggota ?? 0 }} Petani &bull; Kec. {{ $poktan?->kecamatan ?? '-' }}
            </p>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button"
                    @click="ratingModal = true"
                    title="Beri Ulasan Layanan (IKM)"
                    class="min-h-[44px] px-2.5 py-2 rounded-xl bg-lime-harvest text-obsidian text-xs font-black inline-flex items-center gap-1 transition shadow-xs active:scale-95">
                <span class="material-symbols-outlined text-base">hotel_class</span>
                <span class="hidden sm:inline">Ulasan</span>
            </button>
            <button type="button"
                    @click="editProfileModal = false; mobileTab = 'profil'; window.scrollTo({ top: 0, behavior: 'smooth' })"
                    class="min-h-[44px] px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-lime-harvest text-xs font-bold border border-white/15 inline-flex items-center gap-1 transition">
                <span class="material-symbols-outlined text-base">manage_accounts</span>
                <span>Profil</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. HEADER PROFIL POKTAN (Penuh di Desktop, & Aktif di Tab Profil Mobile)  -->
    <!-- ========================================================================= -->
    <div :class="mobileTab === 'profil' ? 'block' : 'hidden lg:block'"
         class="bg-forest-dark text-white rounded-3xl p-6 sm:p-8 shadow-md relative overflow-hidden border border-forest-deep">
        <!-- Ambient Glowing Background Circles -->
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-emerald-500/10 pointer-events-none blur-3xl"></div>
        <div class="absolute right-40 -bottom-20 w-64 h-64 rounded-full bg-lime-harvest/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-black bg-lime-harvest text-obsidian border border-lime-harvest-hover shadow-sm">
                        <span class="material-symbols-outlined text-sm mr-1.5 font-bold">verified</span>
                        TERDAFTAR RESMI DI DINAS PERTANIAN
                    </span>
                    <span class="text-xs text-celery-cream font-medium bg-white/10 px-3 py-1 rounded-full border border-white/10">
                        🌾 Simluhtan Aktif TA 2026
                    </span>
                </div>
                
                <div>
                    <span class="text-xs sm:text-sm text-lime-harvest font-bold block mb-0.5">
                        Wilujeng Sumping / Selamat Datang,
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white font-urbanist">
                        {{ $poktan?->nama_poktan ?? 'Kelompok Tani' }}
                    </h1>
                </div>
                
                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 pt-1 text-xs sm:text-sm text-celery-cream/95">
                    <div class="flex items-center gap-1.5 bg-forest-deep/60 px-3 py-1.5 rounded-xl border border-white/10">
                        <span class="material-symbols-outlined text-base text-lime-harvest">badge</span>
                        <span>No. SK Badan Hukum: <strong class="text-white font-mono">{{ $poktan?->nomor_badan_hukum ?? '-' }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-forest-deep/60 px-3 py-1.5 rounded-xl border border-white/10">
                        <span class="material-symbols-outlined text-base text-lime-harvest">location_on</span>
                        <span>Wilayah: <strong>Desa {{ $poktan?->desa }}, Kec. {{ $poktan?->kecamatan }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-forest-deep/60 px-3 py-1.5 rounded-xl border border-white/10">
                        <span class="material-symbols-outlined text-base text-lime-harvest">person</span>
                        <span>Ketua Kelompok: <strong>{{ $poktan?->nama_ketua }}</strong> ({{ $poktan?->no_hp_ketua }})</span>
                    </div>
                </div>

                <!-- Tombol Edit Data Poktan & Tombol Survei Kepuasan (IKM) -->
                <div class="hidden lg:flex items-center gap-3 pt-2">
                    <button type="button" 
                            @click="editProfileModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] rounded-full text-xs font-black bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10 transition shadow-sm active:scale-95">
                        <span class="material-symbols-outlined text-base">edit_note</span>
                        <span>Perbarui Data Hamparan & Anggota Poktan</span>
                    </button>
                    <button type="button" 
                            @click="ratingModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 min-h-[44px] rounded-full text-xs font-black bg-white/10 hover:bg-white/20 text-lime-harvest border border-white/20 transition shadow-sm active:scale-95">
                        <span class="material-symbols-outlined text-base">hotel_class</span>
                        <span>Survei Kepuasan (IKM)</span>
                    </button>
                </div>
            </div>

            <!-- Stats Ringkas Hamparan Lahan & Anggota (Ukuran Font Besar Nyaman Dibaca & Mobile Friendly) -->
            <div class="grid grid-cols-2 gap-2.5 sm:flex sm:flex-nowrap sm:items-center sm:gap-3 w-full lg:w-auto pt-2 lg:pt-0">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl px-4 py-3 sm:px-5 sm:py-3.5 border border-white/20 text-center shadow-sm">
                    <span class="text-[11px] sm:text-xs font-bold text-celery-cream uppercase tracking-wider block">Luas Hamparan</span>
                    <span class="text-xl sm:text-2xl font-black text-white font-mono">{{ number_format($poktan?->luas_lahan_total ?? 0, 1) }} <span class="text-xs sm:text-sm font-sans font-bold text-lime-harvest">Ha</span></span>
                </div>
                
                <div class="bg-white/10 backdrop-blur-md rounded-2xl px-4 py-3 sm:px-5 sm:py-3.5 border border-white/20 text-center shadow-sm">
                    <span class="text-[11px] sm:text-xs font-bold text-celery-cream uppercase tracking-wider block">Anggota Aktif</span>
                    <span class="text-xl sm:text-2xl font-black text-white font-mono">{{ $poktan?->jumlah_anggota ?? 0 }} <span class="text-xs sm:text-sm font-sans font-bold text-lime-harvest">Petani</span></span>
                </div>

                <div class="hidden xl:block pl-3 border-l border-white/15">
                    <img src="{{ asset('images/logo-subang-emblem.png') }}?v={{ file_exists(public_path('images/logo-subang-emblem.png')) ? filemtime(public_path('images/logo-subang-emblem.png')) : time() }}" 
                         alt="Logo Pemkab Subang" 
                         class="h-16 w-auto object-contain filter drop-shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. GRID UTAMA: FORM PENGAJUAN (KIRI) & RIWAYAT USULAN (KANAN)              -->
    <!-- Di desktop berdampingan (Golden Ratio); di mobile dikontrol via Segmented / Floating Bar -->
    <!-- ========================================================================= -->
    <div class="space-y-4">

        <!-- Mobile Segmented Tab Switcher (< lg) - Selaras 3 Menu Pill Navbar -->
        <div class="lg:hidden p-1.5 bg-white rounded-2xl border border-bone-slate shadow-xs grid grid-cols-3 gap-1">
            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'status'"
                    :class="mobileTab === 'status' ? 'bg-forest-dark text-lime-harvest shadow-sm font-black' : 'text-obsidian hover:bg-bone-canvas font-bold'"
                    class="min-h-[44px] py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-base">track_changes</span>
                <span>Status</span>
                <span :class="mobileTab === 'status' ? 'bg-lime-harvest text-obsidian' : 'bg-bone-canvas text-forest-dark border border-bone-slate'"
                      class="px-1.5 py-0.5 rounded-full text-[10px] font-mono tabular-nums font-black">
                    {{ method_exists($pengajuans, 'total') ? $pengajuans->total() : $pengajuans->count() }}
                </span>
            </button>

            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'form'"
                    :class="mobileTab === 'form' ? 'bg-forest-dark text-lime-harvest shadow-sm font-black' : 'text-obsidian hover:bg-bone-canvas font-bold'"
                    class="min-h-[44px] py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-base">post_add</span>
                <span>Ajukan</span>
            </button>

            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'profil'"
                    :class="mobileTab === 'profil' ? 'bg-forest-dark text-lime-harvest shadow-sm font-black' : 'text-obsidian hover:bg-bone-canvas font-bold'"
                    class="min-h-[44px] py-2 px-2 rounded-xl text-xs flex items-center justify-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-base">manage_accounts</span>
                <span>Profil</span>
            </button>
        </div>

        <!-- Grid Responsif: Berdampingan di Desktop (lg), Tab-Switch di Mobile (< lg) -->
        <div class="grid-cols-1 lg:grid-cols-12 gap-6 items-start"
             :class="{ 'hidden lg:grid': mobileTab === 'profil', 'grid': mobileTab !== 'profil' }">

            <!-- ===================================================================== -->
            <!-- SISI KIRI (5 Kolom di Desktop): FORMULIR PENGAJUAN                    -->
            <!-- ===================================================================== -->
            <div id="poktan-form-section"
                 class="lg:col-span-5 scroll-mt-24 bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-6"
                 :class="{ 'hidden lg:block': mobileTab !== 'form', 'block': mobileTab === 'form' }">
                
                <!-- Judul Formulir -->
                <div class="border-b border-bone-slate pb-4">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="font-extrabold text-obsidian text-lg sm:text-xl flex items-center font-urbanist min-w-0">
                            <span class="w-8 h-8 sm:w-9 sm:h-9 shrink-0 rounded-2xl bg-forest-dark text-lime-harvest flex items-center justify-center mr-2 sm:mr-2.5 shadow-2xs">
                                <span class="material-symbols-outlined text-lg sm:text-xl">post_add</span>
                            </span>
                            <span class="leading-snug truncate">Formulir Permohonan Alat</span>
                        </h2>
                        <span class="shrink-0 whitespace-nowrap text-[10px] sm:text-[11px] bg-celery-cream text-forest-dark font-black px-2.5 sm:px-3 py-1 rounded-full border border-bone-slate shadow-2xs">
                            Buka 24 Jam
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-muted-green mt-1">
                        Silakan isi 3 langkah mudah di bawah ini untuk mengajukan mesin pertanian ke Dinas.
                    </p>
                </div>

            <!-- Panduan 3 Langkah Ramah Lansia / Boomer -->
            <div class="p-4 rounded-2xl bg-celery-cream/50 border border-bone-slate space-y-2">
                <div class="text-xs font-black text-forest-dark uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-forest-dark">help</span>
                    Petunjuk Mudah Mengajukan Bantuan:
                </div>
                <div class="grid grid-cols-3 gap-2 text-center pt-1">
                    <div class="bg-white p-2.5 rounded-xl border border-bone-slate shadow-2xs">
                        <div class="w-6 h-6 rounded-full bg-forest-dark text-lime-harvest font-black text-xs mx-auto flex items-center justify-center mb-1">1</div>
                        <div class="text-[11px] font-bold text-obsidian">Pilih Mesin</div>
                        <div class="text-[10px] text-muted-green leading-tight">Alat yang dibutuhkan</div>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-bone-slate shadow-2xs">
                        <div class="w-6 h-6 rounded-full bg-forest-dark text-lime-harvest font-black text-xs mx-auto flex items-center justify-center mb-1">2</div>
                        <div class="text-[11px] font-bold text-obsidian">Isi Kebutuhan</div>
                        <div class="text-[10px] text-muted-green leading-tight">Jumlah unit & lahan</div>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-bone-slate shadow-2xs">
                        <div class="w-6 h-6 rounded-full bg-forest-dark text-lime-harvest font-black text-xs mx-auto flex items-center justify-center mb-1">3</div>
                        <div class="text-[11px] font-bold text-obsidian">Kirim Usulan</div>
                        <div class="text-[10px] text-muted-green leading-tight">Dinas memeriksa</div>
                    </div>
                </div>
            </div>

            <!-- Formulir Pengajuan Bantuan Alsintan (Bebas Tooltip Native, Validasi Notif Card) -->
            <form action="{{ route('poktan.pengajuan.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  novalidate
                  x-data="{
                      formError: '',
                      alsintanId: '{{ old('jenis_alsintan_id', '') }}',
                      alasan: '{{ addslashes(old('alasan_pengajuan', '')) }}',
                      hasFile: false,
                      fileName: '',
                      onFileChange(e) {
                          const file = e.target.files && e.target.files[0];
                          if (file) {
                              this.hasFile = true;
                              this.fileName = file.name;
                          } else {
                              this.hasFile = false;
                              this.fileName = '';
                          }
                      },
                      resetFile() {
                          const input = document.getElementById('berkas_proposal');
                          if (input) input.value = '';
                          this.hasFile = false;
                          this.fileName = '';
                      },
                      validatePengajuan(e) {
                          this.formError = '';
                          if (!this.alsintanId) {
                              e.preventDefault();
                              this.formError = 'Harap pilih jenis mesin pertanian terlebih dahulu pada Langkah 1.';
                              const el = document.getElementById('jenis_alsintan_id');
                              if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); el.focus(); }
                              return false;
                          }
                          if (!this.alasan || !this.alasan.trim()) {
                              e.preventDefault();
                              this.formError = 'Harap isi alasan dan cerita kebutuhan alat di sawah pada Langkah 3.';
                              const el = document.getElementById('alasan_pengajuan');
                              if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'center' }); el.focus(); }
                              return false;
                          }
                      }
                  }"
                  @submit="validatePengajuan($event)"
                  class="space-y-5">
                @csrf

                <!-- Notifikasi Peringatan Validasi Interaktif (Animasi Halus & Kontras Tinggi) -->
                <div x-cloak 
                     x-show="formError" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
                     class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex items-start gap-3 shadow-sm">
                    <div class="w-8 h-8 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-lg">warning</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs sm:text-sm font-black text-amber-950" x-text="formError"></p>
                        <p class="text-[11px] text-amber-800/90 mt-0.5 font-medium">Lengkapi kolom bertanda bintang sebelum mengirim usulan ke dinas.</p>
                    </div>
                    <button type="button" @click="formError = ''" class="text-amber-800 hover:text-amber-950 text-xs font-bold p-1 rounded-lg hover:bg-amber-100 transition shrink-0" aria-label="Tutup notifikasi">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>

                <!-- Langkah 1: Kategori Alat / Alsintan -->
                <div>
                    <label for="jenis_alsintan_id" class="block text-xs sm:text-sm font-black text-forest-dark uppercase tracking-wider mb-1.5">
                        Langkah 1: Pilih Jenis Mesin Pertanian <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_alsintan_id" id="jenis_alsintan_id" x-model="alsintanId"
                            class="w-full px-4 py-3.5 bg-bone-canvas/70 border-2 border-bone-slate rounded-2xl text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark focus:bg-white transition @error('jenis_alsintan_id') border-red-500 @enderror">
                        <option value="">-- Sentuh / Klik untuk Memilih Mesin Pertanian --</option>
                        @foreach($jenisAlsintans as $alsintan)
                            <option value="{{ $alsintan->id }}" {{ old('jenis_alsintan_id') == $alsintan->id ? 'selected' : '' }}>
                                [{{ $alsintan->kode_alsintan }}] {{ $alsintan->nama_alsintan }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-muted-green mt-1">Pilihlah alat yang paling mendesak bagi kelancaran panen/tanam kelompok Anda.</p>
                    @error('jenis_alsintan_id') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Langkah 2: Grid Tahun, Jumlah Diminta, & Alat Eksisting -->
                <div class="space-y-3">
                    <label class="block text-xs sm:text-sm font-black text-forest-dark uppercase tracking-wider">
                        Langkah 2: Rincian Jumlah Kebutuhan Alat
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Tahun Anggaran -->
                        <div>
                            <label for="tahun_pengajuan" class="block text-[11px] font-bold text-obsidian mb-1">
                                Tahun Usulan
                            </label>
                            <input type="number" id="tahun_pengajuan" name="tahun_pengajuan" value="{{ old('tahun_pengajuan', date('Y')) }}" min="2024" max="2030"
                                   class="no-spin tabular-nums w-full px-3.5 py-3 bg-bone-canvas/70 border-2 border-bone-slate rounded-xl text-sm font-black text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('tahun_pengajuan') border-red-500 @enderror">
                            @error('tahun_pengajuan') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Jumlah Diminta -->
                        <div>
                            <label for="jumlah_diminta" class="block text-[11px] font-bold text-obsidian mb-1">
                                Jumlah Diminta <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="jumlah_diminta" name="jumlah_diminta" value="{{ old('jumlah_diminta', 1) }}" min="1" max="10" step="1"
                                   class="no-spin tabular-nums w-full px-3.5 py-3 bg-bone-canvas/70 border-2 border-bone-slate rounded-xl text-sm font-black text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('jumlah_diminta') border-red-500 @enderror">
                            <span class="text-[10px] text-muted-green block mt-0.5 font-semibold">Satuan: Unit</span>
                            @error('jumlah_diminta') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <!-- Alat Eksisting -->
                        <div>
                            <label for="jumlah_alsintan_eksisting" class="block text-[11px] font-bold text-obsidian mb-1" title="Jumlah alat sejenis yang sudah dimiliki kelompok saat ini">
                                Sudah Punya? (Unit)
                            </label>
                            <input type="number" id="jumlah_alsintan_eksisting" name="jumlah_alsintan_eksisting" value="{{ old('jumlah_alsintan_eksisting', 0) }}" min="0" max="50" step="1"
                                   class="no-spin tabular-nums w-full px-3.5 py-3 bg-bone-canvas/70 border-2 border-bone-slate rounded-xl text-sm font-black text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('jumlah_alsintan_eksisting') border-red-500 @enderror">
                            <span class="text-[10px] text-muted-green block mt-0.5 font-semibold">Ketik 0 jika belum punya</span>
                            @error('jumlah_alsintan_eksisting') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Langkah 3: Urgensi & Alasan Kebutuhan -->
                <div>
                    <label for="alasan_pengajuan" class="block text-xs sm:text-sm font-black text-forest-dark uppercase tracking-wider mb-1.5">
                        Langkah 3: Alasan & Cerita Kebutuhan di Sawah <span class="text-red-500">*</span>
                    </label>
                    <textarea id="alasan_pengajuan" name="alasan_pengajuan" rows="3" x-model="alasan"
                              placeholder="Contoh: Kami sangat membutuhkan Combine Harvester karena hamparan sawah 38.5 Ha di Desa Gunungsari sering kekurangan tenaga panen manual saat musim hujan tiba..."
                              class="w-full px-4 py-3 bg-bone-canvas/70 border-2 border-bone-slate rounded-2xl text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition @error('alasan_pengajuan') border-red-500 @enderror">{{ old('alasan_pengajuan') }}</textarea>
                    <p class="text-[11px] text-muted-green mt-1">
                        Tuliskan kendala nyata yang dihadapi kelompok tani agar dinas dapat memberikan penilaian yang tepat.
                    </p>
                    @error('alasan_pengajuan') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Langkah 4: Lampiran Berkas Proposal Resmi (PDF) - Terproteksi Anti-Malware -->
                <div>
                    <div class="flex flex-wrap items-center justify-between gap-1.5 mb-1.5">
                        <label for="berkas_proposal" class="text-xs sm:text-sm font-black text-forest-dark uppercase tracking-wider">
                            Langkah 4: Lampiran Proposal (PDF)
                        </label>
                        <div class="flex items-center gap-2">
                            <!-- Tombol Reset / Batalkan File Jika Sudah Dipilih -->
                            <button type="button" 
                                    x-cloak
                                    x-show="hasFile"
                                    @click="resetFile()"
                                    class="text-[11px] font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-2.5 py-1 rounded-full flex items-center gap-1 transition shadow-2xs">
                                <span class="material-symbols-outlined text-xs">close</span>
                                <span>Batal Pilih / Kosongkan File</span>
                            </button>
                            <span class="shrink-0 whitespace-nowrap text-[10px] font-bold text-muted-green normal-case bg-bone-canvas px-2.5 py-1 rounded-full border border-bone-slate">Opsional (Format .PDF)</span>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="file" id="berkas_proposal" name="berkas_proposal" accept="application/pdf"
                               @change="onFileChange($event)"
                               class="w-full px-3.5 py-2.5 bg-bone-canvas/70 border-2 border-bone-slate rounded-2xl text-xs font-bold text-obsidian file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-forest-dark file:text-lime-harvest hover:file:bg-forest-accent cursor-pointer transition focus:ring-2 focus:ring-forest-dark focus:bg-white @error('berkas_proposal') border-red-500 @enderror">
                    </div>
                    <p class="text-[10px] text-muted-green mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs text-forest-dark">lock</span>
                        Standar Keamanan: Dokumen tersimpan di brankas digital privat (enkripsi UUID), maksimal 5 MB.
                    </p>
                    @error('berkas_proposal') <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Informasi Masa Berlaku Berkas (Bahasa Santun & Menenangkan) -->
                <div class="p-4 bg-emerald-50/80 border border-emerald-200 rounded-2xl text-xs text-emerald-950 space-y-1">
                    <div class="font-extrabold flex items-center text-forest-dark text-xs">
                        <span class="material-symbols-outlined text-forest-dark text-base mr-1.5 font-bold">shield</span>
                        Jaminan Pelayanan Dinas Pertanian Subang
                    </div>
                    <p class="text-[11px] leading-relaxed text-emerald-900/90">
                        Setiap usulan bantuan yang Bapak/Ibu kirimkan akan <strong>sah dan aktif dinilai selama 90 hari</strong>. Pastikan nomor kontak pengurus aktif agar petugas penyuluh lapangan (PPL) mudah berkoordinasi saat verifikasi fisik ke sawah.
                    </p>
                </div>

                <!-- Tombol Submit Ramah Sentuhan (Besar & Kontras Sangat Tinggi) -->
                <div class="pt-2 pb-6 sm:pb-0">
                    <button type="submit" 
                            class="w-full py-4 px-5 sm:px-6 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-black text-sm sm:text-base transition shadow-md flex items-center justify-between group active:scale-[0.99] border-2 border-black/10">
                        <span class="tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-xl">send</span>
                            <span>Kirimkan Permohonan Bantuan ke Dinas</span>
                        </span>
                        <span class="w-9 h-9 rounded-full bg-forest-dark text-lime-harvest flex items-center justify-center text-sm group-hover:translate-x-1 transition-transform shrink-0">
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ===================================================================== -->
        <!-- SISI KANAN (7 Kolom di Desktop): RIWAYAT USULAN BANTUAN               -->
        <!-- ===================================================================== -->
        <div id="poktan-status-section"
             class="lg:col-span-7 scroll-mt-24 bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5"
             :class="{ 'hidden lg:block': mobileTab !== 'status', 'block': mobileTab === 'status' }">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-bone-slate pb-4 gap-2">
                <div>
                    <h2 class="font-extrabold text-obsidian text-xl font-urbanist flex items-center gap-2">
                        <span class="material-symbols-outlined text-forest-dark text-2xl">track_changes</span>
                        Pantau Status Permohonan Anda
                    </h2>
                    <p class="text-xs sm:text-sm text-muted-green mt-0.5">
                        Lihat sudah sampai mana berkas usulan kelompok tani diproses oleh Dinas.
                    </p>
                </div>
                <div>
                    <span class="text-xs font-black bg-celery-cream text-forest-dark px-3.5 py-1.5 rounded-full border border-bone-slate inline-flex items-center gap-1 shadow-2xs shrink-0 whitespace-nowrap">
                        <span class="material-symbols-outlined text-xs">folder_open</span>
                        Total: {{ method_exists($pengajuans, 'total') ? $pengajuans->total() : $pengajuans->count() }} Usulan
                    </span>
                </div>
            </div>

            <!-- Petunjuk Ringkas Membaca Tahapan (Visual Stepper Guide) -->
            <div class="p-3.5 rounded-2xl bg-bone-canvas/60 border border-bone-slate text-xs flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-forest-dark flex items-center gap-1 text-[11px]">
                    <span class="material-symbols-outlined text-sm text-forest-dark">timeline</span>
                    Tahapan Pengajuan:
                </span>
                <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold text-muted-green">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-forest-dark"></span> 1. Usulan Masuk</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-forest-dark"></span> 2. Cek Berkas Dinas</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-forest-dark"></span> 3. Penilaian Skor</span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-lime-harvest-hover"></span> 4. Keputusan ACC</span>
                </div>
            </div>

            @if($pengajuans->isEmpty())
                <div class="py-16 text-center text-muted-green space-y-3 bg-bone-canvas/30 rounded-3xl border-2 border-dashed border-bone-slate">
                    <div class="w-16 h-16 rounded-full bg-celery-cream text-forest-dark mx-auto flex items-center justify-center mb-1">
                        <span class="material-symbols-outlined text-3xl">inbox</span>
                    </div>
                    <h3 class="text-base font-extrabold text-obsidian">Belum Ada Permohonan yang Diajukan</h3>
                    <p class="text-xs text-muted-green max-w-sm mx-auto leading-relaxed">
                        Kelompok tani Anda belum pernah mengirimkan usulan alat. Silakan gunakan formulir di sebelah kiri untuk mengajukan alat perdana!
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pengajuans as $pengajuan)
                        @php
                            // Tentukan posisi langkah (1 - 4) dalam alur usulan
                            $step = match($pengajuan->status) {
                                'draft'           => 1,
                                'diajukan'        => 1,
                                'diverifikasi'    => 2,
                                'dinilai'         => 2,
                                'dirangking'      => 3,
                                'disetujui_kabid' => 4,
                                'ditolak_admin'   => -1,
                                'ditolak_kabid'   => -1,
                                default           => 1,
                            };

                            $isApproved = ($pengajuan->status === 'disetujui_kabid');
                            $isRejected = in_array($pengajuan->status, ['ditolak_admin', 'ditolak_kabid']);
                            $isRanked   = ($pengajuan->status === 'dirangking');
                            $isChecking = in_array($pengajuan->status, ['diajukan', 'diverifikasi', 'dinilai']);

                            // Tanggal kedaluwarsa berkas
                            $tglExpired = $pengajuan->expired_at ? $pengajuan->expired_at->translatedFormat('d F Y') : '-';
                            $masihBerlaku = $pengajuan->expired_at ? $pengajuan->expired_at->isFuture() : true;
                            $tglDibuat = $pengajuan->created_at ? $pengajuan->created_at->translatedFormat('d F Y') : date('d F Y');
                        @endphp

                        <div class="p-5 sm:p-6 rounded-3xl border-2 transition bg-white space-y-4 shadow-sm {{ $isApproved ? 'border-lime-harvest bg-lime-50/20' : ($isRejected ? 'border-red-200 bg-red-50/20' : 'border-bone-slate hover:border-forest-dark/40') }}">
                            
                            <!-- Header Kartu Usulan: Nomor Tiket & Tanggal -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-bone-slate/60 pb-3">
                                <div class="flex flex-wrap items-center gap-2 min-w-0">
                                    <span class="font-mono text-xs font-black text-forest-dark bg-celery-cream px-2.5 py-1 rounded-xl border border-bone-slate shrink-0 whitespace-nowrap shadow-2xs">
                                        {{ $pengajuan->nomor_pengajuan }}
                                    </span>
                                    <span class="text-[11px] sm:text-xs text-muted-green font-medium flex items-center gap-1 shrink-0 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-xs">calendar_today</span>
                                        <span>Diajukan: {{ $tglDibuat }}</span>
                                    </span>
                                </div>

                                <!-- Badge Status Bahasa Indonesia Lugas -->
                                <div class="shrink-0 self-start sm:self-auto">
                                    @if($isApproved)
                                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-lime-harvest text-obsidian border border-lime-harvest-hover flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-sm font-bold">check_circle</span>
                                            DISETUJUI (ACC DINAS)
                                        </span>
                                    @elseif($isRanked)
                                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-950 border border-amber-harvest flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-sm font-bold text-amber-harvest">hourglass_top</span>
                                            MENUNGGU PENETAPAN PIMPINAN
                                        </span>
                                    @elseif($isRejected)
                                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-red-100 text-red-900 border border-red-300 flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-sm font-bold">cancel</span>
                                            BELUM LOLOS SELEKSI
                                        </span>
                                    @elseif($pengajuan->status === 'diverifikasi' || $pengajuan->status === 'dinilai')
                                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-900 border border-blue-300 flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-sm font-bold">verified</span>
                                            BERKAS LOLOS VERIFIKASI
                                        </span>
                                    @else
                                        <span class="px-3.5 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-900 border border-amber-harvest/60 flex items-center gap-1 shadow-2xs whitespace-nowrap">
                                            <span class="material-symbols-outlined text-sm font-bold">pending</span>
                                            DALAM ANTREAN PEMERIKSAAN
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Judul Alat & Alasan Permohonan -->
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-muted-green block">Mesin yang Diminta:</span>
                                        <h3 class="font-black text-base sm:text-lg text-obsidian font-urbanist mt-0.5 leading-snug">
                                            {{ $pengajuan->jenisAlsintan?->nama_alsintan }}
                                        </h3>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-muted-green block whitespace-nowrap">Jumlah Unit:</span>
                                        <span class="font-mono text-xs sm:text-base font-black text-forest-dark bg-celery-cream/70 px-2.5 sm:px-3 py-1 rounded-xl border border-bone-slate inline-flex items-center justify-center mt-0.5 shrink-0 whitespace-nowrap shadow-2xs tabular-nums">
                                            {{ $pengajuan->jumlah_diminta }} Unit
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2.5 p-3 rounded-2xl bg-bone-canvas/50 border border-bone-slate/80 text-xs text-obsidian">
                                    <span class="text-[11px] font-bold text-muted-green block mb-0.5">Catatan Permohonan Kelompok:</span>
                                    <p class="leading-relaxed italic text-obsidian/90">"{{ $pengajuan->alasan_pengajuan }}"</p>
                                </div>

                                @if($pengajuan->berkasPengajuans && $pengajuan->berkasPengajuans->isNotEmpty())
                                    <div class="mt-2.5 pt-2 border-t border-bone-slate/50 flex flex-wrap items-center gap-2">
                                        <span class="text-[11px] font-bold text-muted-green flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs text-forest-dark">attach_file</span>
                                            Dokumen Terlampir:
                                        </span>
                                        @foreach($pengajuan->berkasPengajuans as $berkas)
                                            <a href="{{ route('berkas.unduh', [$pengajuan->id, $berkas->id]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-celery-cream/80 hover:bg-lime-harvest text-forest-dark hover:text-obsidian rounded-xl text-xs font-bold transition border border-bone-slate shadow-2xs group"
                                               title="Unduh berkas proposal resmi terverifikasi">
                                                <span class="material-symbols-outlined text-xs text-red-600 group-hover:scale-110 transition-transform">picture_as_pdf</span>
                                                <span>{{ $berkas->nama_berkas }} ({{ $berkas->file_size_human }})</span>
                                                <span class="material-symbols-outlined text-xs">download</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- ========================================================= -->
                            <!-- VISUAL STEPPER TRACKER (4 Tahapan Ramah Lansia & Boomer)  -->
                            <!-- ========================================================= -->
                            <div class="pt-2">
                                <div class="text-[11px] font-black text-forest-dark uppercase tracking-wider mb-2.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">flag</span>
                                    Status Perkembangan Permohonan di Dinas:
                                </div>

                                @if(!$isRejected)
                                    <!-- 4-Step Stepper Bar -->
                                    <div class="relative flex items-center justify-between w-full">
                                        <!-- Garis Penghubung Background -->
                                        <div class="absolute left-4 right-4 top-1/2 -translate-y-1/2 h-1 bg-bone-slate rounded-full -z-0"></div>
                                        
                                        <!-- Garis Penghubung Progress Aktif -->
                                        @php
                                            $lineWidth = match($step) {
                                                1 => 'w-[15%]',
                                                2 => 'w-[45%]',
                                                3 => 'w-[75%]',
                                                4 => 'w-[100%]',
                                                default => 'w-[0%]',
                                            };
                                        @endphp
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 h-1.5 bg-forest-dark rounded-full -z-0 transition-all duration-700 {{ $lineWidth }}"></div>

                                        <!-- Step 1: Usulan Terkirim -->
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shadow-sm {{ $step >= 1 ? 'bg-forest-dark text-lime-harvest ring-4 ring-white' : 'bg-bone-canvas text-muted-green border border-bone-slate' }}">
                                                @if($step > 1)
                                                    <span class="material-symbols-outlined text-base">check</span>
                                                @else
                                                    1
                                                @endif
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] font-bold text-obsidian mt-1.5 block max-w-[70px] leading-tight">1. Usulan Diterima</span>
                                        </div>

                                        <!-- Step 2: Verifikasi Dinas -->
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shadow-sm {{ $step >= 2 ? 'bg-forest-dark text-lime-harvest ring-4 ring-white' : ($step === 1 ? 'bg-amber-100 text-amber-900 border-2 border-amber-harvest animate-pulse ring-4 ring-white' : 'bg-bone-canvas text-muted-green border border-bone-slate') }}">
                                                @if($step > 2)
                                                    <span class="material-symbols-outlined text-base">check</span>
                                                @elseif($step === 2)
                                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                                @else
                                                    2
                                                @endif
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] font-bold {{ $step >= 2 ? 'text-obsidian' : 'text-muted-green' }} mt-1.5 block max-w-[70px] leading-tight">2. Cek Berkas</span>
                                        </div>

                                        <!-- Step 3: Penilaian SPK TOPSIS -->
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shadow-sm {{ $step >= 3 ? 'bg-forest-dark text-lime-harvest ring-4 ring-white' : ($step === 2 ? 'bg-amber-100 text-amber-900 border-2 border-amber-harvest ring-4 ring-white' : 'bg-bone-canvas text-muted-green border border-bone-slate') }}">
                                                @if($step > 3)
                                                    <span class="material-symbols-outlined text-base">check</span>
                                                @elseif($step === 3)
                                                    <span class="material-symbols-outlined text-sm">insights</span>
                                                @else
                                                    3
                                                @endif
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] font-bold {{ $step >= 3 ? 'text-obsidian' : 'text-muted-green' }} mt-1.5 block max-w-[70px] leading-tight">3. Penilaian Skor</span>
                                        </div>

                                        <!-- Step 4: Keputusan Pimpinan (ACC) -->
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shadow-sm {{ $step === 4 ? 'bg-lime-harvest text-obsidian ring-4 ring-white font-black' : 'bg-bone-canvas text-muted-green border border-bone-slate' }}">
                                                @if($step === 4)
                                                    <span class="material-symbols-outlined text-base">task_alt</span>
                                                @else
                                                    4
                                                @endif
                                            </div>
                                            <span class="text-[10px] sm:text-[11px] font-bold {{ $step === 4 ? 'text-forest-dark font-black' : 'text-muted-green' }} mt-1.5 block max-w-[70px] leading-tight">4. Keputusan ACC</span>
                                        </div>
                                    </div>
                                @else
                                    <!-- Jika Ditolak -->
                                    <div class="p-3.5 bg-red-50 border border-red-200 rounded-2xl flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-xl">info</span>
                                        </div>
                                        <div class="text-xs text-red-900">
                                            <div class="font-bold">Permohonan Belum Memperoleh Alokasi Pada Periode Ini</div>
                                            <div class="text-[11px] mt-0.5 text-red-800/90 leading-relaxed">
                                                {{ $pengajuan->catatan_admin ?? $pengajuan->catatan_kabid ?? 'Alokasi kuota mesin pertanian untuk periode anggaran ini telah dialokasikan ke kelompok yang lebih mendesak. Bapak/Ibu dapat mengajukan usulan baru pada pembukaan periode berikutnya.' }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- ========================================================= -->
                            <!-- PENJELASAN RAMAH BAHASA MANUSIA (Status Terkini)           -->
                            <!-- ========================================================= -->
                            <div class="pt-1">
                                @if($isApproved)
                                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-1.5">
                                        <div class="flex items-center text-forest-dark font-black text-sm">
                                            <span class="material-symbols-outlined text-base mr-1.5 text-emerald-600">celebration</span>
                                            Alhamdulillah! Bantuan Resmi Disetujui (ACC Pimpinan)
                                        </div>
                                        <p class="text-emerald-950 text-xs leading-relaxed">
                                            Selamat kepada Kelompok Tani <strong>{{ $poktan?->nama_poktan }}</strong>! Permohonan bantuan alat ini telah disahkan oleh Kepala Bidang Dinas Pertanian. Silakan berkoordinasi dengan Penyuluh Pertanian Lapangan (PPL) desa Anda untuk jadwal serah terima mesin dan penandatanganan Berita Acara (BAST).
                                        </p>
                                    </div>
                                @elseif($isRanked)
                                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-harvest/50 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center text-amber-950 font-black text-sm">
                                                <span class="material-symbols-outlined text-base mr-1.5 text-amber-harvest">stars</span>
                                                Hasil Penilaian: Rekomendasi Prioritas Ke-{{ $pengajuan->hasilTopsis?->ranking ?? 1 }}
                                            </div>
                                            @if($pengajuan->hasilTopsis)
                                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-950 border border-amber-harvest font-mono tabular-nums text-[11px] font-black">
                                                    Skor Kelayakan: {{ number_format($pengajuan->hasilTopsis->nilai_preferensi, 4) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-amber-900 text-xs leading-relaxed">
                                            Kondisi sawah dan kebutuhan kelompok tani Anda telah dinilai oleh sistem ilmiah Dinas Pertanian dan menempati <strong>peringkat prioritas tinggi</strong>. Saat ini berkas sedang berada di meja Kepala Bidang untuk penerbitan persetujuan (ACC).
                                        </p>
                                    </div>
                                @elseif($pengajuan->status === 'diverifikasi' || $pengajuan->status === 'dinilai')
                                    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs space-y-1">
                                        <div class="flex items-center text-blue-950 font-bold text-xs">
                                            <span class="material-symbols-outlined text-base mr-1.5 text-blue-600">assignment_turned_in</span>
                                            Tahap 2 Selesai: Berkas Administrasi Lolos Pemeriksaan
                                        </div>
                                        <p class="text-blue-900 text-[11px] leading-relaxed">
                                            Petugas Dinas Pertanian telah memeriksa keabsahan surat dan luas hamparan lahan. Berkas Anda selanjutnya akan masuk ke tahap penilaian ilmiah untuk menentukan urutan prioritas penerima.
                                        </p>
                                    </div>
                                @elseif($pengajuan->status === 'diajukan')
                                    <div class="p-4 rounded-2xl bg-bone-canvas border border-bone-slate text-xs space-y-1">
                                        <div class="flex items-center text-forest-dark font-bold text-xs">
                                            <span class="material-symbols-outlined text-base mr-1.5 text-forest-dark">inbox</span>
                                            Tahap 1: Permohonan Sudah Masuk ke Komputer Dinas
                                        </div>
                                        <p class="text-muted-green text-[11px] leading-relaxed">
                                            Berkas usulan kelompok Anda sudah tersimpan dengan aman di server Dinas Pertanian Kabupaten Subang. Petugas sedang memverifikasi antrean berkas secara berurutan.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer Kartu: Masa Berlaku Berkas yang Menentramkan (Bukan Kata "Expired") -->
                            <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-bone-slate/60 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $masihBerlaku ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-gray-100 text-gray-700 border-gray-300' }} font-bold text-[11px]">
                                        <span class="material-symbols-outlined text-sm {{ $masihBerlaku ? 'text-emerald-600' : 'text-gray-500' }}">event_available</span>
                                        Masa Aktif Berkas: Berlaku Sah s.d. {{ $tglExpired }}
                                    </span>
                                </div>

                                <div class="text-[11px] text-muted-green font-medium">
                                    Kabupaten Subang &bull; Dinas Pertanian
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Footer Pagination Khusus Poktan (Maksimal 5 Usulan Per Halaman) -->
                @if(method_exists($pengajuans, 'hasPages') && $pengajuans->hasPages())
                    <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                        {{ $pengajuans->links('vendor.pagination.custom-tailwind') }}
                    </div>
                @endif
            @endif

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- PROFIL KELOMPOK TANI: INLINE DI TAB PROFIL MOBILE & MODAL DI DESKTOP      -->
    <!-- ========================================================================= -->
    <div x-show="editProfileModal || mobileTab === 'profil'" 
         x-cloak
         @keydown.escape.window="if (editProfileModal) editProfileModal = false"
         :class="editProfileModal ? 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-obsidian/60 backdrop-blur-xs' : 'lg:hidden block'">
        
        <div id="poktan-profil-form-card"
             @click.away="if (editProfileModal) editProfileModal = false"
             :class="editProfileModal ? 'max-w-xl w-full p-6 sm:p-7 shadow-2xl max-h-[90vh] overflow-y-auto' : 'w-full p-5 sm:p-6 shadow-sm'"
             class="bg-white rounded-3xl border border-bone-slate space-y-5">
            
            <div class="flex items-center justify-between border-b border-bone-slate pb-3">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-forest-dark text-lime-harvest flex items-center justify-center text-sm font-bold shadow-2xs">
                        <span class="material-symbols-outlined text-xl">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-obsidian text-lg font-urbanist leading-tight">
                            Perbarui Data Profil Poktan
                        </h3>
                        <p class="text-xs text-muted-green">Sesuaikan perubahan luas lahan hamparan & data keanggotaan</p>
                    </div>
                </div>
                <button type="button" x-show="editProfileModal" @click="editProfileModal = false" aria-label="Tutup jendela ubah profil Poktan" class="w-11 h-11 min-w-[44px] min-h-[44px] flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas rounded-full transition">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <form action="{{ route('poktan.profil.update') }}" method="POST" novalidate class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="edit_nama_poktan" class="block text-xs font-bold text-obsidian mb-1">Nama Kelompok Tani *</label>
                        <input type="text" id="edit_nama_poktan" name="nama_poktan" value="{{ old('nama_poktan', $poktan?->nama_poktan) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">
                    </div>
                    <div>
                        <label for="edit_no_sk" class="block text-xs font-bold text-obsidian mb-1">No. SK Badan Hukum</label>
                        <input type="text" id="edit_no_sk" name="nomor_badan_hukum" value="{{ old('nomor_badan_hukum', $poktan?->nomor_badan_hukum) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="edit_nama_ketua" class="block text-xs font-bold text-obsidian mb-1">Nama Ketua Poktan *</label>
                        <input type="text" id="edit_nama_ketua" name="nama_ketua" value="{{ old('nama_ketua', $poktan?->nama_ketua) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">
                    </div>
                    <div>
                        <label for="edit_no_hp" class="block text-xs font-bold text-obsidian mb-1">Nomor HP / WhatsApp *</label>
                        <input type="tel" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               id="edit_no_hp" 
                               name="no_hp_ketua" 
                               value="{{ old('no_hp_ketua', $poktan?->no_hp_ketua) }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               onkeypress="if (!/[0-9]/.test(event.key)) event.preventDefault()"
                               placeholder="Contoh: 081234567890"
                               maxlength="15"
                               class="no-spin tabular-nums w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">
                    </div>
                </div>

                <!-- Luas Lahan & Jumlah Anggota (Sesuai Kebutuhan Tanah Bertambah / Anggota Berkurang) -->
                <div class="p-3.5 rounded-2xl bg-lime-harvest/20 border border-lime-harvest/60 space-y-3">
                    <div class="text-xs font-black text-forest-dark uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">agriculture</span>
                        Kondisi Riil Hamparan & Anggota Saat Ini:
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_luas_lahan" class="block text-xs font-bold text-obsidian mb-1">
                                Luas Hamparan Total (Hektar) *
                            </label>
                            <div class="relative">
                                <input type="number" step="0.1" min="0.1" max="10000" id="edit_luas_lahan" name="luas_lahan_total" 
                                       value="{{ old('luas_lahan_total', $poktan?->luas_lahan_total) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-white text-xs sm:text-sm font-mono tabular-nums font-black text-obsidian focus:ring-2 focus:ring-forest-dark no-spin pr-12">
                                <span class="absolute right-3 top-2.5 text-xs font-bold text-forest-dark">Ha</span>
                            </div>
                            <span class="text-[10px] text-muted-green block mt-0.5">Ubah jika tanah sawah bertambah / berkurang</span>
                        </div>

                        <div>
                            <label for="edit_jumlah_anggota" class="block text-xs font-bold text-obsidian mb-1">
                                Jumlah Anggota Aktif (Petani) *
                            </label>
                            <div class="relative">
                                <input type="number" min="1" max="1000" id="edit_jumlah_anggota" name="jumlah_anggota" 
                                       value="{{ old('jumlah_anggota', $poktan?->jumlah_anggota) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-white text-xs sm:text-sm font-mono tabular-nums font-black text-obsidian focus:ring-2 focus:ring-forest-dark no-spin pr-14">
                                <span class="absolute right-3 top-2.5 text-xs font-bold text-forest-dark">Orang</span>
                            </div>
                            <span class="text-[10px] text-muted-green block mt-0.5">Ubah jika anggota kelompok bertambah / berkurang</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="edit_desa" class="block text-xs font-bold text-obsidian mb-1">Desa *</label>
                        <input type="text" id="edit_desa" name="desa" value="{{ old('desa', $poktan?->desa) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">
                    </div>
                    <div>
                        <label for="edit_kecamatan" class="block text-xs font-bold text-obsidian mb-1">Kecamatan di Kab. Subang *</label>
                        @php
                            $subangKecamatans = [
                                'Binong', 'Blanakan', 'Ciasem', 'Ciater', 'Cibogo', 'Cijambe', 'Cikaum', 
                                'Cipeundeuy', 'Cipunagara', 'Cisalak', 'Compreng', 'Dawuan', 'Jalancagak', 
                                'Kalijati', 'Kasomalang', 'Legonkulon', 'Pabuaran', 'Pagaden', 'Pagaden Barat', 
                                'Pamanukan', 'Patokbeusi', 'Purwadadi', 'Pusakajaya', 'Pusakanagara', 
                                'Sagalaherang', 'Serangpanjang', 'Subang', 'Sukasari', 'Tambakdahan', 'Tanjungsiang'
                            ];
                            $currentKec = old('kecamatan', $poktan?->kecamatan);
                        @endphp
                        <select id="edit_kecamatan" name="kecamatan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition">
                            <option value="">-- Pilih Kecamatan di Subang --</option>
                            @foreach($subangKecamatans as $kec)
                                <option value="{{ $kec }}" {{ $currentKec == $kec ? 'selected' : '' }}>
                                    {{ $kec }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit_alamat" class="block text-xs font-bold text-obsidian mb-1">Alamat Lengkap / Keterangan Hamparan</label>
                    <textarea id="edit_alamat" name="alamat_lengkap" rows="2" 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-bone-slate bg-bone-canvas/60 text-xs sm:text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white">{{ old('alamat_lengkap', $poktan?->alamat_lengkap) }}</textarea>
                </div>

                <div class="pt-3 border-t border-bone-slate flex items-center justify-end space-x-2">
                    <button type="button" x-show="editProfileModal" @click="editProfileModal = false" 
                            class="px-4 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 min-h-[44px] rounded-full text-xs font-black bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 active:scale-95">
                        <span class="material-symbols-outlined text-sm">save</span>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POP-UP: SURVEI KEPUASAN & RATING LAYANAN (IKM - TABEL KE-10)       -->
    <!-- Animasi Halus & Interaktif, Bersifat Opsional                             -->
    <!-- ========================================================================= -->
    <div x-cloak
         x-show="ratingModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-obsidian/70 backdrop-blur-sm flex items-center justify-center p-4">

        <div @click.away="ratingModal = false"
             x-show="ratingModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full border border-forest-dark/20 shadow-2xl relative space-y-5">

            <!-- Tombol Tutup Silang di Kanan Atas -->
            <button type="button"
                    @click="ratingModal = false"
                    class="absolute top-4 right-4 text-muted-green hover:text-obsidian p-1 rounded-full hover:bg-bone-canvas transition">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>

            <!-- Header Modal -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-lime-harvest/30 border border-lime-harvest text-forest-dark flex items-center justify-center shadow-inner">
                    <span class="material-symbols-outlined text-3xl text-forest-dark">hotel_class</span>
                </div>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 inline-block mb-1">
                        Survei Kepuasan Layanan (IKM)
                    </span>
                    <h3 class="text-lg sm:text-xl font-black text-forest-dark font-urbanist">
                        Bagaimana Pengalaman Anda?
                    </h3>
                    <p class="text-xs text-muted-green leading-relaxed mt-1">
                        Bantu Dinas Pertanian Kabupaten Subang meningkatkan mutu pelayanan hibah alsintan melalui penilaian Anda (opsional).
                    </p>
                </div>
            </div>

            <!-- Form Penilaian -->
            <form method="POST" action="{{ route('poktan.ulasan.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="pengajuan_bantuan_id" value="{{ session('baru_diajukan_id', $latestPengajuan?->id) }}">
                <input type="hidden" name="rating_bintang" :value="ratingScore">

                <!-- Interaktif 5 Bintang -->
                <div class="bg-bone-canvas/80 p-4 rounded-2xl border border-bone-slate text-center space-y-2">
                    <span class="text-xs font-bold text-obsidian block">Pilih Rating Bintang:</span>
                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                            <button type="button"
                                    @click="ratingScore = star; ratingHover = star"
                                    @mouseenter="ratingHover = star"
                                    @mouseleave="ratingHover = ratingScore"
                                    class="p-1 rounded-lg transition transform hover:scale-125 focus:outline-hidden cursor-pointer"
                                    :title="star + ' Bintang'">
                                <span class="material-symbols-outlined text-3xl sm:text-4xl transition-colors"
                                      :class="(ratingHover || ratingScore) >= star ? 'text-amber-500' : 'text-gray-300'"
                                      :style="(ratingHover || ratingScore) >= star ? 'font-variation-settings: \'FILL\' 1;' : ''">
                                    star
                                </span>
                            </button>
                        </template>
                    </div>
                    <!-- Label Dinamis Rating -->
                    <div class="text-xs font-extrabold text-forest-dark min-h-[18px]" x-text="getRatingLabel()"></div>
                </div>

                <!-- Input Komentar / Masukan -->
                <div>
                    <label for="komentar_ulasan" class="block text-xs font-bold text-obsidian mb-1">
                        Ulasan atau Saran Perbaikan <span class="text-[10px] text-muted-green font-normal">(Opsional)</span>
                    </label>
                    <textarea id="komentar_ulasan" 
                              name="komentar" 
                              rows="3" 
                              maxlength="1000"
                              placeholder="Tuliskan pengalaman atau kendala yang dihadapi selama proses pengajuan..."
                              class="w-full px-3.5 py-2.5 rounded-2xl border border-bone-slate bg-bone-canvas/60 text-xs text-obsidian placeholder:text-muted-green/60 focus:ring-2 focus:ring-forest-dark focus:bg-white transition resize-none"></textarea>
                </div>

                <!-- Aksi: Kirim & Lewati -->
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" 
                            @click="ratingModal = false" 
                            class="px-4 py-2.5 rounded-xl border border-bone-slate bg-white hover:bg-bone-canvas text-xs font-bold text-muted-green transition">
                        Nanti Saja / Lewati
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-forest-dark hover:bg-forest-hover text-lime-harvest font-black text-xs shadow-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-base">send</span>
                        <span>Kirim Penilaian</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- FLOATING MOBILE BOTTOM NAVIGATION BAR (Compact Curved Scoop 3 Menu)       -->
    <!-- ========================================================================= -->
    <div class="lg:hidden fixed bottom-3 inset-x-0 z-40 flex justify-center px-4 pointer-events-none">
        <nav class="pointer-events-auto bg-forest-dark/95 backdrop-blur-md text-white rounded-full px-3 py-1.5 shadow-2xl border-2 border-forest-deep flex items-center justify-around w-full max-w-[260px] transition-all" aria-label="Navigasi Mobile Kelompok Tani">
            
            <!-- Tab 1: Pantau Status Usulan -->
            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'status'; window.scrollTo({ top: 0, behavior: 'smooth' })" 
                    class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all group">
                <div :class="mobileTab === 'status' ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white'"
                     class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                    <span class="material-symbols-outlined text-[19px] font-bold">track_changes</span>
                </div>
                <span :class="mobileTab === 'status' ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold'"
                      class="text-[10px] leading-tight tracking-tight block mt-0.5 transition-colors">Status</span>
            </button>

            <!-- Tab 2: Ajukan Alat Baru -->
            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'form'; window.scrollTo({ top: 0, behavior: 'smooth' })" 
                    class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all group">
                <div :class="mobileTab === 'form' ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white'"
                     class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                    <span class="material-symbols-outlined text-[19px] font-bold">post_add</span>
                </div>
                <span :class="mobileTab === 'form' ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold'"
                      class="text-[10px] leading-tight tracking-tight block mt-0.5 transition-colors">Ajukan</span>
            </button>

            <!-- Tab 3: Profil Lahan & Anggota Poktan (Tampilan Tab In-Page Tanpa Pop-up) -->
            <button type="button" 
                    @click="editProfileModal = false; mobileTab = 'profil'; window.scrollTo({ top: 0, behavior: 'smooth' })" 
                    class="relative flex-1 flex flex-col items-center justify-center min-w-[44px] min-h-[44px] py-0.5 rounded-full transition-all group">
                <div :class="mobileTab === 'profil' ? 'bg-lime-harvest text-obsidian shadow-lg -translate-y-2 scale-105 ring-[3px] ring-forest-dark' : 'text-celery-cream/75 hover:text-white'"
                     class="w-9 h-9 rounded-full flex items-center justify-center transition-all duration-300 ease-out">
                    <span class="material-symbols-outlined text-[19px] font-bold">manage_accounts</span>
                </div>
                <span :class="mobileTab === 'profil' ? 'text-lime-harvest font-black' : 'text-celery-cream/70 font-semibold'"
                      class="text-[10px] leading-tight tracking-tight block mt-0.5 transition-colors">Profil</span>
            </button>

        </nav>
    </div>

</div>
@endsection
