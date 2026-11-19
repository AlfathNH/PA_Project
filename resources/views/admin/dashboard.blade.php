@extends('layouts.app')

@section('title', 'Dashboard Admin Verifikator - SPK Bantuan Alsintan Subang')

@section('content')
<div class="space-y-6 w-full max-w-full min-w-0">

    <!-- Top Banner & Quick Actions (Harvesta Executive Layout) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold bg-celery-cream text-forest-dark border border-bone-slate">
                    <span class="material-symbols-outlined text-sm mr-1 text-forest-dark">admin_panel_settings</span>
                    Panel Administrator & Operator SPK
                </span>
                <span class="text-xs text-muted-green hidden sm:inline">• Dinas Pertanian Subang</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight font-urbanist">
                Verifikasi & Pengolahan Usulan Alsintan
            </h1>
            <p class="text-xs sm:text-sm text-muted-green">
                Kelola kriteria, verifikasi berkas CPCL lapangan, dan eksekusi komputasi multi-kriteria TOPSIS
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.pengajuan.index') }}" 
               class="px-5 py-3 rounded-full bg-bone-canvas hover:bg-bone-slate text-forest-dark font-bold text-xs transition border border-bone-slate flex items-center space-x-2">
                <span class="material-symbols-outlined text-base">checklist</span>
                <span>Verifikasi & Nilai Usulan</span>
            </a>
            <a href="{{ route('admin.topsis.index') }}" 
               class="px-5 py-3 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-extrabold text-xs transition shadow-sm border border-black/10 flex items-center space-x-2 active:scale-[0.98]">
                <span class="material-symbols-outlined text-base">calculate</span>
                <span>Jalankan SPK TOPSIS</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Stat Cards (Modeled after Harvesta Field Metrics in Reference Image) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Card 1: Kelompok Tani -->
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Database Poktan</span>
                <div class="w-10 h-10 rounded-2xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">groups</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $stats['total_poktan'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Poktan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-forest-dark"></span>
                    <span class="text-xs font-semibold text-forest-dark">Terdaftar di 30 Kecamatan Subang</span>
                </div>
            </div>
            <!-- Segmented Progress Bar Accent -->
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-forest-dark h-full w-3/4 rounded-full"></div>
                <div class="bg-lime-harvest h-full w-1/4 rounded-full"></div>
            </div>
        </div>

        <!-- Card 2: Total Usulan Masuk -->
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Total Usulan Masuk</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $stats['total_pengajuan'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Proposal</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span class="text-xs font-semibold text-blue-800">Semua kategori mesin Alsintan</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-blue-600 h-full w-2/3 rounded-full"></div>
                <div class="bg-celery-cream h-full w-1/3 rounded-full"></div>
            </div>
        </div>

        <!-- Card 3: Siap Dihitung TOPSIS -->
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Siap Dihitung TOPSIS</span>
                <div class="w-10 h-10 rounded-2xl bg-lime-harvest/30 text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">insights</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $stats['siap_dihitung'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Kandidat</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-lime-harvest"></span>
                    <span class="text-xs font-semibold text-forest-dark">Skor <i>x</i><sub>ij</sub> telah diverifikasi</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-lime-harvest h-full w-4/5 rounded-full"></div>
                <div class="bg-forest-dark h-full w-1/5 rounded-full"></div>
            </div>
        </div>

        <!-- Card 4: Disetujui Pimpinan -->
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Disetujui Pimpinan</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-900 flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl text-amber-harvest">approval</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $stats['disetujui'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Disetujui</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-harvest"></span>
                    <span class="text-xs font-semibold text-amber-950">ACC Kepala Bidang / Kadis</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-amber-harvest h-full w-full rounded-full"></div>
            </div>
        </div>

    </div>

    <!-- Status Alokasi & CRUD Master Data Alsintan TA 2026 (Stok Dinas Pertanian) -->
    <div id="master-alsintan"
         x-data="{
            alsintanView: 'grid',
            createModalOpen: false,
            editModalOpen: false,
            deleteModalOpen: false,

            // State Edit Modal
            editId: null,
            editKode: '',
            editNama: '',
            editSatuan: 'Unit',
            editKuota: 0,
            editDeskripsi: '',
            editTersalurkan: 0,
            editIsActive: true,

            // State Delete Modal
            deleteId: null,
            deleteKode: '',
            deleteNama: '',
            deleteRelasiCount: 0,

            openEdit(id, kode, nama, satuan, kuota, deskripsi, tersalurkan, isActive) {
                this.editId = id;
                this.editKode = kode;
                this.editNama = nama;
                this.editSatuan = satuan || 'Unit';
                this.editKuota = kuota;
                this.editDeskripsi = deskripsi || '';
                this.editTersalurkan = tersalurkan || 0;
                this.editIsActive = Boolean(isActive);
                this.editModalOpen = true;
            },

            openDelete(id, kode, nama, relasiCount) {
                this.deleteId = id;
                this.deleteKode = kode;
                this.deleteNama = nama;
                this.deleteRelasiCount = relasiCount || 0;
                this.deleteModalOpen = true;
            }
         }" 
         class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
        
        <!-- Header Panel Manajemen Master Alsintan -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-bone-slate/60 pb-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest">
                        <span class="material-symbols-outlined text-xs mr-1 text-forest-dark">inventory_2</span>
                        Master Data Alsintan (CRUD)
                    </span>
                    <span class="text-xs text-muted-green font-semibold">Tahun Anggaran 2026</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-obsidian tracking-tight font-urbanist mt-1">
                    Status Alokasi &amp; Kuota Unit Alsintan
                </h2>
                <p class="text-xs text-muted-green mt-0.5">
                    Kelola jenis mesin pertanian dinas dengan paginasi 6 item per halaman, pencarian cepat, dan pilihan tampilan kartu atau tabel ringkas.
                </p>
            </div>
            
            <div class="flex items-center gap-2.5 flex-wrap shrink-0">
                <span class="px-3 py-2 rounded-xl bg-celery-cream text-forest-dark font-extrabold text-xs border border-bone-slate tabular-nums">
                    Total {{ $alsintanSummary['total'] ?? $jenisAlsintans->total() }} Jenis
                    <span class="text-muted-green font-semibold">({{ $alsintanSummary['aktif'] ?? 0 }} Aktif)</span>
                </span>
                <button type="button" 
                        @click="createModalOpen = true"
                        class="px-4 py-2.5 min-h-[44px] rounded-xl text-xs font-black bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10 transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-base">add_circle</span>
                    <span>Tambah Jenis Alsintan</span>
                </button>
            </div>
        </div>

        <!-- Bar Kontrol Master Alsintan: Cari, Filter Status, Urutkan, & Mode Tampilan -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 bg-bone-canvas/60 p-3.5 rounded-2xl border border-bone-slate">
            <form action="{{ route('admin.dashboard') }}#master-alsintan" method="GET" class="no-loader flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="relative flex-1 min-w-[200px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-muted-green text-base">search</span>
                    <input type="text"
                           name="alsintan_search"
                           value="{{ $alsintanSearch ?? request('alsintan_search') }}"
                           placeholder="Cari kode (TR-2) atau nama barang alsintan..."
                           aria-label="Cari kode atau nama barang alsintan"
                           class="w-full pl-9 pr-3 py-2 min-h-[44px] bg-white border border-bone-slate rounded-xl text-xs font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark transition">
                </div>

                <select name="alsintan_status"
                        onchange="this.form.submit()"
                        aria-label="Filter status keaktifan barang alsintan"
                        class="px-3 py-2 min-h-[44px] bg-white border border-bone-slate rounded-xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ ($alsintanStatus ?? '') === 'aktif' ? 'selected' : '' }}>Aktif Saja</option>
                    <option value="nonaktif" {{ ($alsintanStatus ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <select name="alsintan_sort"
                        onchange="this.form.submit()"
                        aria-label="Urutkan daftar barang alsintan"
                        class="px-3 py-2 min-h-[44px] bg-white border border-bone-slate rounded-xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    <option value="default" {{ ($alsintanSort ?? 'default') === 'default' ? 'selected' : '' }}>Urutan: Utama (Aktif)</option>
                    <option value="terbaru" {{ ($alsintanSort ?? '') === 'terbaru' ? 'selected' : '' }}>Terbaru Ditambah</option>
                    <option value="kuota_asc" {{ ($alsintanSort ?? '') === 'kuota_asc' ? 'selected' : '' }}>Kuota Terkecil</option>
                </select>

                <div class="flex items-center gap-2">
                    <button type="submit"
                            class="px-4 py-2 min-h-[44px] rounded-xl bg-forest-dark hover:bg-forest-deep text-lime-harvest font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                        <span class="material-symbols-outlined text-sm">filter_list</span>
                        <span>Terapkan</span>
                    </button>

                    @if(!empty($alsintanSearch) || !empty($alsintanStatus) || ($alsintanSort ?? 'default') !== 'default')
                        <a href="{{ route('admin.dashboard', array_filter(['search' => request('search')])) }}#master-alsintan"
                           class="px-3 py-2 min-h-[44px] rounded-xl bg-white hover:bg-bone-slate text-forest-dark font-bold text-xs border border-bone-slate transition inline-flex items-center justify-center"
                           title="Reset filter barang alsintan"
                           aria-label="Reset filter barang alsintan">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Toggle Mode Tampilan: Kartu vs Tabel Ringkas -->
            <div class="inline-flex items-center p-1 rounded-xl bg-white border border-bone-slate self-start xl:self-auto shrink-0" role="group" aria-label="Pilih mode tampilan barang alsintan">
                <button type="button"
                        @click="alsintanView = 'grid'"
                        :class="alsintanView === 'grid' ? 'bg-forest-dark text-lime-harvest shadow-2xs' : 'text-muted-green hover:text-obsidian'"
                        class="px-3 py-2 min-h-[38px] rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-sm">grid_view</span>
                    <span>Kartu</span>
                </button>
                <button type="button"
                        @click="alsintanView = 'table'"
                        :class="alsintanView === 'table' ? 'bg-forest-dark text-lime-harvest shadow-2xs' : 'text-muted-green hover:text-obsidian'"
                        class="px-3 py-2 min-h-[38px] rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-sm">table_rows</span>
                    <span>Tabel</span>
                </button>
            </div>
        </div>

        @if($jenisAlsintans->isEmpty())
            <div class="py-10 px-4 text-center bg-bone-canvas/40 rounded-2xl border border-dashed border-bone-slate space-y-2.5">
                <span class="material-symbols-outlined text-3xl text-muted-green block">inventory_2</span>
                <p class="text-sm font-bold text-obsidian">Tidak ditemukan mesin Alsintan yang sesuai filter.</p>
                <a href="{{ route('admin.dashboard', array_filter(['search' => request('search')])) }}#master-alsintan"
                   class="inline-flex items-center px-4 py-2 min-h-[44px] rounded-full bg-white hover:bg-bone-slate text-forest-dark font-bold text-xs border border-bone-slate transition">
                    Tampilkan Semua Alsintan
                </a>
            </div>
        @else
            <!-- MODE 1: GRID KARTU LONGGAR (Maks 6 Item per Halaman, Jarak 20px-24px) -->
            <div x-show="alsintanView === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                @foreach($jenisAlsintans as $alsintan)
                    @php
                        $sisa = $alsintan->sisa_kuota;
                        $persen = $alsintan->persentase_tersalurkan;
                        $relasiCount = $alsintan->pengajuanBantuans->count();
                        $isActive = (bool) $alsintan->is_active;

                        $statusColor = match(true) {
                            !$isActive => 'text-slate-600 bg-slate-100 border-slate-300',
                            $sisa === 0 => 'text-red-700 bg-red-50 border-red-200',
                            $sisa <= 2 => 'text-amber-800 bg-amber-50 border-amber-harvest',
                            default => 'text-forest-dark bg-celery-cream border-bone-slate'
                        };
                        $icon = match($alsintan->kode_alsintan) {
                            'TR-2', 'TR-4' => 'agriculture',
                            'PMP' => 'water_drop',
                            'SPR' => 'sanitizer',
                            'RCT' => 'grass',
                            'CBH' => 'precision_manufacturing',
                            default => 'hardware'
                        };
                    @endphp
                    <div class="p-5 sm:p-6 rounded-2xl border-2 border-bone-slate hover:border-forest-dark/40 shadow-2xs transition-all flex flex-col justify-between space-y-4 {{ !$isActive ? 'opacity-75 bg-slate-50' : 'bg-white' }}">
                        <div>
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center shadow-2xs shrink-0">
                                        <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-bone-canvas border border-bone-slate text-forest-dark">
                                                {{ $alsintan->kode_alsintan }}
                                            </span>
                                            @if(!$isActive)
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="font-bold text-obsidian text-sm mt-1 truncate" title="{{ $alsintan->nama_alsintan }}">
                                            {{ $alsintan->nama_alsintan }}
                                        </h3>
                                    </div>
                                </div>
                                <span class="text-[11px] font-extrabold px-2.5 py-1 rounded-full border shrink-0 tabular-nums {{ $statusColor }}">
                                    @if(!$isActive)
                                        Nonaktif
                                    @else
                                        Sisa: {{ $sisa }} {{ $alsintan->satuan ?? 'Unit' }}
                                    @endif
                                </span>
                            </div>

                            <!-- Progress Bar & Unit Count -->
                            <div class="mt-4 space-y-1.5">
                                <div class="flex justify-between text-xs tabular-nums">
                                    <span class="text-muted-green">Tersalurkan: <strong class="text-obsidian font-mono">{{ $alsintan->tersalurkan }}</strong> / {{ $alsintan->kuota_unit }} {{ $alsintan->satuan ?? 'Unit' }}</span>
                                    <span class="font-mono font-bold text-forest-dark">{{ $persen }}%</span>
                                </div>
                                <div class="w-full bg-bone-slate h-2 rounded-full overflow-hidden">
                                    <div class="bg-forest-dark h-full rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                </div>
                            </div>

                            @if(!empty($alsintan->deskripsi))
                                <p class="text-[11px] text-muted-green mt-3 line-clamp-2 leading-relaxed">
                                    {{ $alsintan->deskripsi }}
                                </p>
                            @endif
                        </div>

                        <!-- Action Bar: Ubah Data (Edit) & Hapus/Nonaktifkan (Delete) -->
                        <div class="pt-3 border-t border-bone-slate/60 flex items-center justify-between gap-2">
                            <span class="text-[11px] text-muted-green truncate">
                                Satuan: <strong class="text-obsidian">{{ $alsintan->satuan ?? 'Unit' }}</strong>
                            </span>
                            
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="openEdit({{ $alsintan->id }}, '{{ $alsintan->kode_alsintan }}', '{{ addslashes($alsintan->nama_alsintan) }}', '{{ addslashes($alsintan->satuan ?? 'Unit') }}', {{ $alsintan->kuota_unit }}, '{{ addslashes($alsintan->deskripsi ?? '') }}', {{ $alsintan->tersalurkan }}, {{ $isActive ? 1 : 0 }})"
                                        class="px-3.5 py-2 min-h-[44px] rounded-xl text-xs font-bold bg-bone-canvas/60 hover:bg-celery-cream text-forest-dark border border-bone-slate transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                                        title="Ubah spesifikasi nama, kode, satuan, atau kuota alat">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                    <span>Ubah</span>
                                </button>

                                <button type="button" 
                                        @click="openDelete({{ $alsintan->id }}, '{{ $alsintan->kode_alsintan }}', '{{ addslashes($alsintan->nama_alsintan) }}', {{ $relasiCount }})"
                                        class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-xl bg-white hover:bg-red-50 text-slate-500 hover:text-red-700 border border-bone-slate hover:border-red-200 transition flex items-center justify-center shadow-2xs cursor-pointer"
                                        aria-label="Hapus atau nonaktifkan {{ $alsintan->nama_alsintan }}"
                                        title="Hapus atau nonaktifkan jenis alsintan ini">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- MODE 2: TABEL RINGKAS (Nyaman untuk Memindai Banyak Jenis Barang) -->
            <div x-show="alsintanView === 'table'" x-cloak class="border border-bone-slate rounded-2xl overflow-hidden shadow-2xs">
                <div class="w-full overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/70 font-black whitespace-nowrap">
                                <th class="py-3.5 px-4 whitespace-nowrap">Kode</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Jenis Alsintan</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Serapan / Kuota</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Sisa Kuota</th>
                                <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bone-slate/70 text-obsidian bg-white">
                            @foreach($jenisAlsintans as $alsintan)
                                @php
                                    $sisa = $alsintan->sisa_kuota;
                                    $persen = $alsintan->persentase_tersalurkan;
                                    $relasiCount = $alsintan->pengajuanBantuans->count();
                                    $isActive = (bool) $alsintan->is_active;
                                @endphp
                                <tr class="hover:bg-bone-canvas/40 transition">
                                    <td class="py-3.5 px-4 font-mono font-extrabold text-forest-dark whitespace-nowrap">
                                        {{ $alsintan->kode_alsintan }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-obsidian">{{ $alsintan->nama_alsintan }}</div>
                                        @if(!empty($alsintan->deskripsi))
                                            <div class="text-[11px] text-muted-green line-clamp-1 max-w-md">{{ $alsintan->deskripsi }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono tabular-nums whitespace-nowrap">
                                        <strong>{{ $alsintan->tersalurkan }}</strong> / {{ $alsintan->kuota_unit }} {{ $alsintan->satuan ?? 'Unit' }}
                                        <span class="text-[10px] text-muted-green ml-1">({{ $persen }}%)</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold border tabular-nums {{ !$isActive ? 'bg-slate-100 text-slate-600 border-slate-300' : ($sisa === 0 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-celery-cream text-forest-dark border-bone-slate') }}">
                                            {{ $sisa }} {{ $alsintan->satuan ?? 'Unit' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border {{ $isActive ? 'bg-lime-harvest/40 text-forest-dark border-lime-harvest' : 'bg-slate-100 text-slate-700 border-slate-300' }}">
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button"
                                                    @click="openEdit({{ $alsintan->id }}, '{{ $alsintan->kode_alsintan }}', '{{ addslashes($alsintan->nama_alsintan) }}', '{{ addslashes($alsintan->satuan ?? 'Unit') }}', {{ $alsintan->kuota_unit }}, '{{ addslashes($alsintan->deskripsi ?? '') }}', {{ $alsintan->tersalurkan }}, {{ $isActive ? 1 : 0 }})"
                                                    class="px-3 py-2 min-h-[44px] rounded-xl text-xs font-bold bg-bone-canvas/60 hover:bg-celery-cream text-forest-dark border border-bone-slate transition inline-flex items-center gap-1 cursor-pointer">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                                <span>Ubah</span>
                                            </button>
                                            <button type="button"
                                                    @click="openDelete({{ $alsintan->id }}, '{{ $alsintan->kode_alsintan }}', '{{ addslashes($alsintan->nama_alsintan) }}', {{ $relasiCount }})"
                                                    class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-xl bg-white hover:bg-red-50 text-slate-500 hover:text-red-700 border border-bone-slate hover:border-red-200 transition inline-flex items-center justify-center cursor-pointer"
                                                    aria-label="Hapus atau nonaktifkan {{ $alsintan->nama_alsintan }}">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bar Info & Paginasi Master Alsintan -->
            <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                {{ $jenisAlsintans->links('vendor.pagination.custom-tailwind') }}
            </div>
        @endif

        <!-- ========================================================= -->
        <!-- MODAL 1: TAMBAH MASTER JENIS ALSINTAN BARU (CREATE)       -->
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
                        <div class="w-8 h-8 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                                Tambah Jenis Mesin Alsintan Baru
                            </h3>
                            <p class="text-[11px] text-muted-green">Formulir master data kategori Alsintan dinas</p>
                        </div>
                    </div>
                    <button type="button" @click="createModalOpen = false" aria-label="Tutup formulir tambah alsintan" class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas transition">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <form action="{{ route('admin.alsintan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="create_kode" class="block text-xs font-bold text-obsidian mb-1">
                                Kode Alsintan <span class="text-red-500">*</span>:
                            </label>
                            <input type="text" 
                                   id="create_kode" 
                                   name="kode_alsintan" 
                                   required 
                                   maxlength="10" 
                                   placeholder="Cth: TR-4, CLT, DRN"
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-mono font-bold text-sm text-obsidian uppercase focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                        </div>
                        <div>
                            <label for="create_satuan" class="block text-xs font-bold text-obsidian mb-1">
                                Satuan Unit <span class="text-red-500">*</span>:
                            </label>
                            <input type="text" 
                                   id="create_satuan" 
                                   name="satuan" 
                                   value="Unit" 
                                   required 
                                   maxlength="50" 
                                   placeholder="Cth: Unit, Paket, Set"
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label for="create_nama" class="block text-xs font-bold text-obsidian mb-1">
                            Nama Jenis Mesin Pertanian <span class="text-red-500">*</span>:
                        </label>
                        <input type="text" 
                               id="create_nama" 
                               name="nama_alsintan" 
                               required 
                               maxlength="255" 
                               placeholder="Cth: Traktor Roda 4 (Four-Wheel Tractor)"
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                    </div>

                    <div>
                        <label for="create_kuota" class="block text-xs font-bold text-obsidian mb-1">
                            Alokasi Kuota Bantuan TA 2026 <span class="text-red-500">*</span>:
                        </label>
                        <div class="relative flex items-center">
                            <input type="number" 
                                   id="create_kuota" 
                                   name="kuota_unit" 
                                   value="5" 
                                   min="0" 
                                   max="1000" 
                                   required
                                   class="w-full pl-4 pr-16 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-mono font-black text-base text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark no-spin transition shadow-2xs">
                            <div class="absolute right-3 flex items-center pointer-events-none">
                                <span class="text-xs font-black text-forest-dark uppercase bg-celery-cream px-2 py-0.5 rounded-md border border-bone-slate">Unit</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-muted-green mt-1">
                            Jumlah alokasi unit yang disediakan Dinas Pertanian untuk disalurkan ke Poktan.
                        </p>
                    </div>

                    <div>
                        <label for="create_deskripsi" class="block text-xs font-bold text-obsidian mb-1">
                            Deskripsi / Spesifikasi Teknis:
                        </label>
                        <textarea id="create_deskripsi" 
                                  name="deskripsi" 
                                  rows="2" 
                                  placeholder="Spesifikasi mesin, kapasitas, atau peruntukan lahan sawah..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white text-xs text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs"></textarea>
                    </div>

                    <div class="p-3 rounded-2xl bg-celery-cream/50 border border-bone-slate text-[11px] text-forest-dark flex items-start gap-2">
                        <span class="material-symbols-outlined text-base shrink-0 mt-0.5">info</span>
                        <span>Setelah disimpan, jenis mesin ini langsung dapat dipilih oleh Kelompok Tani pada formulir permohonan bantuan dan siap dinilai dalam SPK TOPSIS.</span>
                    </div>

                    <div class="pt-2 flex items-center justify-end space-x-2">
                        <button type="button" 
                                @click="createModalOpen = false" 
                                class="px-4 py-2 rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-full text-xs font-extrabold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-sm">check</span>
                            <span>Simpan Alsintan Baru</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL 2: UBAH DATA & KUOTA MASTER ALSINTAN (UPDATE)       -->
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
                        <div class="w-8 h-8 rounded-xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                                Ubah Data & Kuota Alsintan
                            </h3>
                            <p class="text-[11px] text-muted-green">Perbarui spesifikasi nama, kode, kuota, atau status</p>
                        </div>
                    </div>
                    <button type="button" @click="editModalOpen = false" aria-label="Tutup formulir ubah data alsintan" class="w-11 h-11 min-w-[44px] min-h-[44px] rounded-full flex items-center justify-center text-muted-green hover:text-obsidian hover:bg-bone-canvas transition">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <form :action="'{{ url('/admin/alsintan') }}/' + editId" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="edit_kode" class="block text-xs font-bold text-obsidian mb-1">
                                Kode Alsintan <span class="text-red-500">*</span>:
                            </label>
                            <input type="text" 
                                   id="edit_kode" 
                                   name="kode_alsintan" 
                                   x-model="editKode" 
                                   required 
                                   maxlength="10" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-mono font-bold text-sm text-obsidian uppercase focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                        </div>
                        <div>
                            <label for="edit_satuan" class="block text-xs font-bold text-obsidian mb-1">
                                Satuan Unit <span class="text-red-500">*</span>:
                            </label>
                            <input type="text" 
                                   id="edit_satuan" 
                                   name="satuan" 
                                   x-model="editSatuan" 
                                   required 
                                   maxlength="50" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label for="edit_nama" class="block text-xs font-bold text-obsidian mb-1">
                            Nama Jenis Mesin Pertanian <span class="text-red-500">*</span>:
                        </label>
                        <input type="text" 
                               id="edit_nama" 
                               name="nama_alsintan" 
                               x-model="editNama" 
                               required 
                               maxlength="255" 
                               class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-bold text-sm text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="edit_kuota" class="block text-xs font-bold text-obsidian">
                                Alokasi Kuota Unit TA 2026 <span class="text-red-500">*</span>:
                            </label>
                            <span class="text-[11px] text-muted-green">
                                Tersalurkan: <strong class="text-obsidian" x-text="editTersalurkan"></strong> Unit
                            </span>
                        </div>
                        <div class="relative flex items-center">
                            <input type="number" 
                                   id="edit_kuota" 
                                   name="kuota_unit" 
                                   x-model="editKuota" 
                                   :min="editTersalurkan" 
                                   max="1000" 
                                   required
                                   class="w-full pl-4 pr-16 py-2.5 rounded-xl border-2 border-bone-slate bg-white font-mono font-black text-base text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark no-spin transition shadow-2xs">
                            <div class="absolute right-3 flex items-center pointer-events-none">
                                <span class="text-xs font-black text-forest-dark uppercase bg-celery-cream px-2 py-0.5 rounded-md border border-bone-slate" x-text="editSatuan">Unit</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-muted-green mt-1">
                            Kuota tidak boleh lebih kecil dari unit yang sudah disalurkan/ACC (<span x-text="editTersalurkan"></span> unit).
                        </p>
                    </div>

                    <div>
                        <label for="edit_deskripsi" class="block text-xs font-bold text-obsidian mb-1">
                            Deskripsi / Spesifikasi:
                        </label>
                        <textarea id="edit_deskripsi" 
                                  name="deskripsi" 
                                  x-model="editDeskripsi" 
                                  rows="2" 
                                  class="w-full px-3.5 py-2.5 rounded-xl border-2 border-bone-slate bg-white text-xs text-obsidian focus:ring-2 focus:ring-forest-dark focus:border-forest-dark transition shadow-2xs"></textarea>
                    </div>

                    <!-- Status Keaktifan Alsintan (Hidden input memastikan nilai 0 tetap terkirim saat tidak dicentang) -->
                    <div class="flex items-center justify-between gap-2 p-3 rounded-2xl bg-bone-canvas/70 border border-bone-slate">
                        <div class="flex items-center space-x-2.5">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   id="edit_is_active" 
                                   name="is_active" 
                                   value="1" 
                                   x-model="editIsActive" 
                                   class="w-4 h-4 rounded text-forest-dark focus:ring-forest-dark border-bone-slate cursor-pointer">
                            <label for="edit_is_active" class="text-xs font-bold text-obsidian cursor-pointer select-none">
                                Aktifkan alsintan ini (Tampilkan di opsi formulir usulan Poktan)
                            </label>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border shrink-0"
                              :class="editIsActive ? 'bg-lime-harvest/40 text-forest-dark border-lime-harvest' : 'bg-slate-200 text-slate-700 border-slate-300'"
                              x-text="editIsActive ? 'Aktif' : 'Nonaktif'">
                        </span>
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
        <!-- MODAL 3: KONFIRMASI HAPUS / NONAKTIFKAN (DELETE)           -->
        <!-- ========================================================= -->
        <div x-show="deleteModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-obsidian/60 backdrop-blur-xs"
             style="display: none;">
            
            <div @click.away="deleteModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 border border-bone-slate shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center space-x-3 text-red-600 border-b border-bone-slate pb-3">
                    <div class="w-10 h-10 rounded-2xl bg-red-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl text-red-600">warning</span>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-obsidian text-base font-urbanist">
                            Hapus / Nonaktifkan Alsintan
                        </h3>
                        <p class="text-[11px] text-muted-green">Konfirmasi penghapusan master inventaris</p>
                    </div>
                </div>

                <form :action="'{{ url('/admin/alsintan') }}/' + deleteId" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    
                    <div class="p-3.5 rounded-2xl bg-red-50/70 border border-red-200 space-y-2">
                        <div class="text-xs text-red-800 font-bold">
                            Jenis Alsintan: <span class="font-mono text-obsidian" x-text="'[' + deleteKode + '] ' + deleteNama"></span>
                        </div>

                        <!-- Keterangan Cerdas: Terhubung ke Pengajuan vs Belum Ada Pengajuan -->
                        <template x-if="deleteRelasiCount > 0">
                            <p class="text-[11px] text-red-900 leading-relaxed">
                                <span class="material-symbols-outlined text-xs align-middle mr-0.5 text-red-700">warning</span>Mesin ini terhubung dengan <strong x-text="deleteRelasiCount"></strong> berkas usulan Poktan. Sistem akan menonaktifkan data mesin agar riwayat peringkat SPK TOPSIS dan arsip BAST tetap aman.
                            </p>
                        </template>

                        <template x-if="deleteRelasiCount === 0">
                            <p class="text-[11px] text-red-900 leading-relaxed">
                                <span class="material-symbols-outlined text-xs align-middle mr-0.5 text-red-700">info</span>Jenis mesin ini belum memiliki usulan terkait dari Poktan mana pun. Mesin akan <strong>dihapus permanen</strong> dari basis data.
                            </p>
                        </template>
                    </div>

                    <div class="pt-2 flex items-center justify-end space-x-2">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="px-4 py-2 rounded-full text-xs font-bold bg-bone-canvas hover:bg-bone-slate text-forest-dark transition border border-bone-slate">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-full text-xs font-extrabold bg-red-600 hover:bg-red-700 text-white transition shadow-sm flex items-center space-x-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-sm">delete_forever</span>
                            <span>Ya, Proses Hapus</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Main Grid: Tabel Pengajuan (61.8%) & Panel Intelijen/Kriteria (38.2%) - Golden Ratio -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Tabel Pengajuan (61.8% Golden Ratio Column) -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-bone-slate/60 pb-4 gap-2">
                <div>
                    <h2 class="font-extrabold text-obsidian text-lg font-urbanist">Daftar Usulan Bantuan Alsintan (Semua Poktan)</h2>
                    <p class="text-xs text-muted-green mt-0.5">Permohonan bantuan masuk dari seluruh wilayah pertanian Subang</p>
                </div>
                <div class="text-xs font-bold bg-celery-cream text-forest-dark px-3 py-1 rounded-full border border-bone-slate self-start sm:self-auto">
                    Total: {{ $pengajuans->total() }} Usulan
                </div>
            </div>

            <!-- Bar Pencarian Cepat (Cari Nama Poktan, Desa, No. Usulan, atau Alat) -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="no-loader flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                @if(request('alsintan_search')) <input type="hidden" name="alsintan_search" value="{{ request('alsintan_search') }}"> @endif
                @if(request('alsintan_status')) <input type="hidden" name="alsintan_status" value="{{ request('alsintan_status') }}"> @endif
                @if(request('alsintan_sort')) <input type="hidden" name="alsintan_sort" value="{{ request('alsintan_sort') }}"> @endif
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted-green text-lg">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama Poktan, Ketua, Desa, No. Usulan, atau Jenis Mesin..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-bone-canvas/70 border-2 border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark focus:bg-white transition shadow-2xs">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-2xl bg-forest-dark hover:bg-forest-deep text-lime-harvest font-black text-xs transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                        <span class="material-symbols-outlined text-sm">search</span>
                        <span>Cari Data</span>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-4 py-2.5 rounded-2xl bg-bone-canvas hover:bg-bone-slate text-forest-dark font-bold text-xs transition border border-bone-slate flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-sm">close</span>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            @if(request('search'))
                <div class="p-3 rounded-2xl bg-celery-cream/70 border border-bone-slate text-xs flex items-center justify-between">
                    <div class="text-forest-dark font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-forest-dark">filter_alt</span>
                        <span>Hasil pencarian untuk kata kunci: <strong class="text-obsidian">"{{ request('search') }}"</strong></span>
                    </div>
                    <span class="font-bold text-muted-green">Ditemukan: {{ $pengajuans->total() }} data</span>
                </div>
            @endif

            <!-- TAMPILAN KARTU MOBILE (< md): Daftar Usulan Tanpa Geser Samping -->
            <div class="space-y-3 md:hidden">
                @forelse($pengajuans as $item)
                    @php
                        $badgeColor = match($item->status) {
                            'draft'           => 'bg-gray-100 text-gray-700 border-gray-300',
                            'diajukan'        => 'bg-amber-100 text-amber-950 border-amber-harvest',
                            'diverifikasi'    => 'bg-blue-100 text-blue-900 border-blue-300',
                            'dinilai'         => 'bg-teal-100 text-teal-900 border-teal-300',
                            'dirangking'      => 'bg-forest-dark/10 text-forest-dark border-forest-dark/30',
                            'disetujui_kabid' => 'bg-lime-harvest/50 text-forest-dark border-lime-harvest font-black',
                            'ditolak_admin', 'ditolak_kabid' => 'bg-red-100 text-red-900 border-red-300',
                            default           => 'bg-gray-100 text-gray-700',
                        };
                        $statusLabel = match($item->status) {
                            'draft'           => 'Draf',
                            'diajukan'        => 'Menunggu Verifikasi',
                            'diverifikasi'    => 'Berkas Diverifikasi',
                            'dinilai'         => 'Siap Dihitung TOPSIS',
                            'dirangking'      => 'Selesai Diperingkat',
                            'disetujui_kabid' => 'Disetujui Pimpinan',
                            'ditolak_admin'   => 'Ditolak Verifikator',
                            'ditolak_kabid'   => 'Ditolak Pimpinan',
                            default           => $item->status,
                        };
                    @endphp
                    <div class="p-4 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-2.5 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-mono text-xs font-black text-forest-dark">{{ $item->nomor_pengajuan }}</span>
                                <div class="font-extrabold text-sm text-obsidian mt-0.5">{{ $item->user?->kelompokTani?->nama_poktan ?? '-' }}</div>
                                <div class="text-[11px] text-muted-green">Kec. {{ $item->user?->kelompokTani?->kecamatan ?? '-' }}</div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $badgeColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-bone-slate/70 flex items-center justify-between gap-2 text-xs">
                            <div>
                                <div class="font-bold text-obsidian">{{ $item->jenisAlsintan?->nama_alsintan }}</div>
                                <div class="text-[11px] text-muted-green tabular-nums">{{ $item->jumlah_diminta }} Unit (TA {{ $item->tahun_pengajuan }})</div>
                            </div>
                            <div class="text-right shrink-0">
                                @if($item->hasilTopsis && in_array($item->status, ['dirangking', 'disetujui_kabid', 'ditolak_kabid']))
                                    <span class="font-black text-forest-dark bg-lime-harvest/40 px-2.5 py-1 rounded-xl border border-lime-harvest inline-block text-[11px] tabular-nums">
                                        #{{ $item->hasilTopsis->ranking }} &bull; <i>V</i><sub>i</sub> {{ number_format($item->hasilTopsis->nilai_preferensi, 4) }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-muted-green">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-muted-green bg-bone-canvas/30 rounded-2xl border border-dashed border-bone-slate">
                        <span class="material-symbols-outlined text-2xl text-forest-dark/40 block mb-1">inbox</span>
                        <p class="font-bold text-obsidian text-xs">Belum ada usulan bantuan yang masuk.</p>
                    </div>
                @endforelse
            </div>

            <!-- Container Tabel Desktop (>= md) dengan Border Halus & Rounded -->
            <div class="hidden md:block border border-bone-slate rounded-2xl overflow-hidden shadow-2xs">
                <div class="w-full overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/70 font-black whitespace-nowrap">
                                <th class="py-3.5 px-2.5 sm:px-3 whitespace-nowrap">No. Usulan</th>
                                <th class="py-3.5 px-2.5 sm:px-3 whitespace-nowrap">Kelompok Tani</th>
                                <th class="py-3.5 px-2.5 sm:px-3 whitespace-nowrap">Jenis Alsintan</th>
                                <th class="py-3.5 px-2.5 sm:px-3 text-center whitespace-nowrap">Status</th>
                                <th class="py-3.5 px-2.5 sm:px-3 text-right whitespace-nowrap">Hasil SPK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bone-slate/70 text-obsidian">
                            @forelse($pengajuans as $item)
                                @php
                                    $badgeColor = match($item->status) {
                                        'draft'           => 'bg-gray-100 text-gray-700 border-gray-300',
                                        'diajukan'        => 'bg-amber-100 text-amber-950 border-amber-harvest',
                                        'diverifikasi'    => 'bg-blue-100 text-blue-900 border-blue-300',
                                        'dinilai'         => 'bg-teal-100 text-teal-900 border-teal-300',
                                        'dirangking'      => 'bg-forest-dark/10 text-forest-dark border-forest-dark/30',
                                        'disetujui_kabid' => 'bg-lime-harvest/50 text-forest-dark border-lime-harvest font-black',
                                        'ditolak_admin', 'ditolak_kabid' => 'bg-red-100 text-red-900 border-red-300',
                                        default           => 'bg-gray-100 text-gray-700',
                                    };
                                    $statusLabel = match($item->status) {
                                        'draft'           => 'Draf',
                                        'diajukan'        => 'Menunggu Verifikasi',
                                        'diverifikasi'    => 'Berkas Diverifikasi',
                                        'dinilai'         => 'Siap Dihitung TOPSIS',
                                        'dirangking'      => 'Selesai Diperingkat',
                                        'disetujui_kabid' => 'Disetujui Pimpinan',
                                        'ditolak_admin'   => 'Ditolak Verifikator',
                                        'ditolak_kabid'   => 'Ditolak Pimpinan',
                                        default           => $item->status,
                                    };
                                @endphp
                                <tr class="hover:bg-celery-cream/20 transition-colors odd:bg-white even:bg-bone-canvas/20">
                                    <td class="py-3.5 px-2.5 sm:px-3 align-middle whitespace-nowrap">
                                        <span class="font-mono text-xs font-black text-forest-dark bg-bone-canvas px-2 py-1 rounded-lg border border-bone-slate shadow-2xs inline-block">
                                            {{ $item->nomor_pengajuan }}
                                        </span>
                                        <div class="text-[11px] text-muted-green font-medium mt-1 flex items-center gap-1 tabular-nums">
                                            <span class="material-symbols-outlined text-xs">calendar_today</span>
                                            {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2.5 sm:px-3 align-middle">
                                        <div class="font-black text-sm text-obsidian">{{ $item->user?->kelompokTani?->nama_poktan ?? '-' }}</div>
                                        <div class="text-[11px] text-muted-green font-medium flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-xs text-lime-harvest">location_on</span>
                                            Kec. {{ $item->user?->kelompokTani?->kecamatan ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2.5 sm:px-3 align-middle">
                                        <div class="font-bold text-obsidian">{{ $item->jenisAlsintan?->nama_alsintan }}</div>
                                        <div class="text-[11px] text-muted-green mt-0.5 flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md bg-celery-cream text-forest-dark font-mono font-bold text-[10px] border border-bone-slate tabular-nums">
                                                {{ $item->jumlah_diminta }} Unit
                                            </span>
                                            <span class="tabular-nums">TA {{ $item->tahun_pengajuan }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-2.5 sm:px-3 align-middle text-center whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badgeColor }} inline-block shadow-2xs">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-2.5 sm:px-3 align-middle text-right whitespace-nowrap">
                                        @if($item->hasilTopsis && in_array($item->status, ['dirangking', 'disetujui_kabid', 'ditolak_kabid']))
                                            <div class="font-black text-forest-dark bg-lime-harvest/40 px-2.5 py-1 rounded-xl border border-lime-harvest inline-flex flex-col items-end shadow-2xs tabular-nums">
                                                <span class="inline-flex items-center gap-1 text-xs">
                                                    <span class="material-symbols-outlined text-sm text-amber-harvest">emoji_events</span>
                                                    <span>Peringkat #{{ $item->hasilTopsis->ranking }}</span>
                                                </span>
                                                <span class="text-[10px] font-mono text-muted-green"><i>V</i><sub>i</sub>: {{ number_format($item->hasilTopsis->nilai_preferensi, 4) }}</span>
                                            </div>
                                        @elseif($item->status === 'diajukan')
                                            <span class="text-amber-950 bg-amber-50 px-3 py-1 rounded-full border border-amber-harvest/50 text-[11px] font-bold inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-harvest animate-pulse"></span>
                                                Perlu Verifikasi
                                            </span>
                                        @elseif($item->status === 'diverifikasi')
                                            <span class="text-blue-900 bg-blue-50 px-3 py-1 rounded-full border border-blue-200 text-[11px] font-bold inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Belum Dinilai
                                            </span>
                                        @elseif($item->status === 'dinilai')
                                            <span class="text-forest-dark bg-celery-cream px-3 py-1 rounded-full border border-bone-slate text-[11px] font-black inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-lime-harvest"></span>
                                                Siap TOPSIS
                                            </span>
                                        @else
                                            <span class="text-muted-green text-[11px] italic">Belum dihitung</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-muted-green bg-white">
                                        <div class="w-12 h-12 rounded-full bg-bone-canvas mx-auto flex items-center justify-center text-forest-dark/40 mb-2">
                                            <span class="material-symbols-outlined text-2xl">inbox</span>
                                        </div>
                                        <p class="font-bold text-obsidian">Belum ada usulan bantuan yang masuk.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Pagination dengan Keterangan & Navigasi -->
            <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                {{ $pengajuans->links('vendor.pagination.custom-tailwind') }}
            </div>
        </div>

        <!-- Panel Kriteria & Retensi Pruning (38.2% Golden Ratio Column) -->
        <div class="lg:col-span-5 space-y-5">
            
            <!-- Widget 1: Auto-Pruning & Storage Optimizer (Retensi 13-Bulan Audit BPKP) -->
            <div class="bg-white rounded-3xl border border-bone-slate p-6 shadow-sm space-y-4 border-t-4 border-t-forest-dark">
                <div class="flex items-center justify-between border-b border-bone-slate/60 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-2xl bg-celery-cream text-forest-dark flex items-center justify-center text-base shadow-2xs">
                            <span class="material-symbols-outlined">schedule</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-obsidian text-sm font-urbanist">Auto-Pruning & Retensi Berkas</h3>
                            <p class="text-[11px] text-muted-green">Siklus 13-Bulan Tutup Buku LPJ Dinas</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest">
                        AKTIF 01:00 WIB
                    </span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="p-3.5 rounded-2xl bg-bone-canvas/60 border border-bone-slate space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-obsidian">
                            <span class="font-semibold text-muted-green">Retensi Usulan ACC:</span>
                            <strong class="text-forest-dark font-extrabold">13 Bulan (395 Hari)</strong>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-obsidian">
                            <span class="font-semibold text-muted-green">Retensi Usulan Ditolak:</span>
                            <strong class="text-amber-900 font-bold">90 Hari</strong>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-obsidian">
                            <span class="font-semibold text-muted-green">Proteksi Database:</span>
                            <strong class="text-forest-dark font-mono font-bold">Teks Rekapitulasi Utuh</strong>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between text-[11px] text-muted-green border-t border-bone-slate/80">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs text-forest-dark">lock</span>
                            Otoritas Rekap BAST: Kepala Bidang
                        </span>
                        <span class="font-semibold text-forest-dark">Standar ANRI</span>
                    </div>
                </div>
            </div>

            <!-- Widget 2: Panel Kriteria TOPSIS -->
            <div class="bg-white rounded-3xl border border-bone-slate p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-bone-slate/60 pb-3">
                    <div>
                        <h3 class="font-extrabold text-obsidian text-sm font-urbanist">5 Kriteria TOPSIS Dinamis</h3>
                        <p class="text-[11px] text-muted-green">Bobot & atribut perhitungan matematis</p>
                    </div>
                    <span class="text-xs font-mono font-extrabold px-2.5 py-0.5 rounded-full {{ abs($totalBobot - 1.0) < 0.001 ? 'bg-celery-cream text-forest-dark border border-bone-slate' : 'bg-red-100 text-red-800' }}">
                        Total: {{ number_format($totalBobot * 100, 1) }}%
                    </span>
                </div>

                <div class="space-y-2.5">
                    @foreach($kriterias as $kriteria)
                        <div class="p-3 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-extrabold text-forest-dark bg-white px-2 py-0.5 rounded-lg border border-bone-slate">
                                    {{ $kriteria->kode_kriteria }}
                                </span>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $kriteria->tipe === 'benefit' ? 'bg-lime-harvest/40 text-forest-dark border border-lime-harvest' : 'bg-amber-100 text-amber-900 border border-amber-harvest' }}">
                                    {{ strtoupper($kriteria->tipe) }}
                                </span>
                            </div>
                            <div class="text-xs font-bold text-obsidian">{{ $kriteria->nama_kriteria }}</div>
                            <div class="flex items-center justify-between text-[11px] text-muted-green pt-0.5">
                                <span>Bobot (W): <strong class="text-forest-dark">{{ number_format($kriteria->bobot * 100, 1) }}%</strong></span>
                                <span>Urutan: {{ $kriteria->urutan }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-3.5 rounded-2xl bg-celery-cream/50 border border-bone-slate text-xs text-forest-dark space-y-1">
                    <div class="font-bold flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1.5 text-forest-dark">menu_book</span>
                        Kontrak Matematis SPK
                    </div>
                    <p class="text-[11px] leading-relaxed text-muted-green">
                        Setiap rumus yang dihitung oleh sistem telah disinkronkan 100% dengan file <code>TOPSIS_FORMULA_SPEC.md</code> untuk laporan Bab 3 & 4 TA.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
