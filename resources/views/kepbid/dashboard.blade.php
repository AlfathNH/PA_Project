@extends('layouts.app')

@section('title', 'Persetujuan Pimpinan (ACC) - Kepala Bidang Dinas Pertanian')

@section('content')
<div class="space-y-6 w-full max-w-full min-w-0"
     x-data="{
        confirmModalOpen: false,
        confirmActionUrl: '',
        confirmKeputusan: '',
        confirmCatatan: '',
        confirmTitle: '',
        confirmDesc: '',
        confirmBtnText: '',
        confirmBtnClass: '',
        confirmIcon: '',
        openConfirmModal(url, keputusan, catatan, title, desc, btnText, btnClass, icon) {
            this.confirmActionUrl = url;
            this.confirmKeputusan = keputusan;
            this.confirmCatatan = catatan;
            this.confirmTitle = title;
            this.confirmDesc = desc;
            this.confirmBtnText = btnText;
            this.confirmBtnClass = btnClass;
            this.confirmIcon = icon;
            this.confirmModalOpen = true;
        }
     }">

    <!-- Header Panel Kepala Bidang (Harvesta Executive Style) -->
    <div class="bg-forest-dark text-white rounded-3xl p-6 sm:p-8 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden border border-forest-deep">
        <!-- Ambient Glow -->
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-emerald-500/10 pointer-events-none blur-3xl"></div>
        <div class="absolute right-40 -bottom-20 w-64 h-64 rounded-full bg-lime-harvest/10 pointer-events-none blur-2xl"></div>

        <div class="space-y-1.5 relative z-10">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold bg-lime-harvest text-obsidian border border-lime-harvest-hover">
                    <span class="material-symbols-outlined text-sm mr-1">approval</span>
                    Portal Pimpinan & Approver Final
                </span>
                <span class="text-xs text-celery-cream/80 hidden sm:inline">• Bidang Sarana & Prasarana</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white font-urbanist">
                Keputusan & Pengesahan Bantuan Alsintan
            </h1>
            <p class="text-xs sm:text-sm text-celery-cream/90">
                Penetapan persetujuan (ACC) penerima bantuan berdasarkan urutan rekomendasi ilmiah SPK TOPSIS
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-center gap-3 relative z-10 w-full md:w-auto">
            <!-- Filter Kategori Alat -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2 border border-white/15 w-full sm:w-auto min-w-0">
                <label for="filter_jenis" class="block text-[10px] text-celery-cream uppercase font-bold mb-1 px-1">Kategori Alat</label>
                <select id="filter_jenis" onchange="window.location.href='{{ route('kepbid.dashboard') }}?jenis_id=' + this.value + '&tab={{ $tab }}'"
                        class="w-full max-w-full px-3 py-2 bg-forest-deep text-white border border-white/20 rounded-xl text-xs font-bold focus:ring-2 focus:ring-lime-harvest">
                    @foreach($jenisAlsintans as $j)
                        <option value="{{ $j->id }}" {{ $selectedId == $j->id ? 'selected' : '' }}>
                            [{{ $j->kode_alsintan }}] {{ $j->nama_alsintan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol ke Halaman Rekap Lengkap & Cetak -->
            <a href="{{ route('kepbid.riwayat', ['jenis_id' => $selectedId]) }}" 
               class="px-5 py-3 rounded-full bg-celery-cream hover:bg-white text-forest-dark font-extrabold text-xs transition shadow-sm border border-bone-slate flex items-center space-x-2">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Rekap & Unduh Laporan</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Card Keputusan (Harvesta Card Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Total Rekomendasi</span>
                <div class="w-10 h-10 rounded-2xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">format_list_numbered</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $stats['total_rekomendasi'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Usulan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-forest-dark"></span>
                    <span class="text-xs font-semibold text-forest-dark">Hasil kalkulasi algoritma TOPSIS</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-forest-dark h-full w-full rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Menunggu Keputusan</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-900 flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl text-amber-harvest">hourglass_top</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-amber-950 tracking-tight font-urbanist">
                    {{ $stats['menunggu_acc'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Poktan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-harvest animate-pulse"></span>
                    <span class="text-xs font-semibold text-amber-900">Perlu penetapan persetujuan (ACC)</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-amber-harvest h-full w-2/3 rounded-full"></div>
                <div class="bg-celery-cream h-full w-1/3 rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Disetujui (ACC)</span>
                <div class="w-10 h-10 rounded-2xl bg-lime-harvest/30 text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl text-forest-dark">task_alt</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-forest-dark tracking-tight font-urbanist">
                    {{ $stats['disetujui'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Telah ACC</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-lime-harvest"></span>
                    <span class="text-xs font-semibold text-forest-dark">Retensi berkas 13 bulan audit</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-lime-harvest h-full w-full rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Ditolak</span>
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-700 flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl text-red-600">cancel</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-red-900 tracking-tight font-urbanist">
                    {{ $stats['ditolak'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Ditolak</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                    <span class="text-xs font-semibold text-red-800">Auto-pruning 90 hari</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-red-400 h-full w-1/4 rounded-full"></div>
            </div>
        </div>

    </div>

    <!-- Status Kuota & Alokasi Kategori Terpilih -->
    @if($selectedAlsintan)
        @php
            $sisaJatah = $selectedAlsintan->sisa_kuota;
            $isHabis = ($sisaJatah <= 0);
        @endphp
        <div class="p-6 rounded-3xl border {{ $isHabis ? 'bg-red-50/90 border-red-200' : 'bg-white border-bone-slate' }} shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5 transition-all">
            <div class="space-y-1.5 max-w-xl">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg {{ $isHabis ? 'bg-red-200 text-red-900 border-red-300' : 'bg-celery-cream text-forest-dark border-bone-slate' }} border">
                        {{ $selectedAlsintan->kode_alsintan }}
                    </span>
                    <span class="text-xs font-bold text-muted-green uppercase tracking-wider">
                        Alokasi Kuota Kategori Terpilih
                    </span>
                    @if($isHabis)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-red-600 text-white animate-pulse">
                            KUOTA TELAH HABIS
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-lime-harvest/50 text-forest-dark border border-lime-harvest">
                            KUOTA TERSEDIA
                        </span>
                    @endif
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $selectedAlsintan->nama_alsintan }}
                </h2>
                <p class="text-xs {{ $isHabis ? 'text-red-700 font-medium' : 'text-muted-green' }} leading-relaxed">
                    @if($isHabis)
                        <span class="material-symbols-outlined text-xs align-middle mr-1">warning</span><strong>Perhatian:</strong> Seluruh kuota bantuan ({{ $selectedAlsintan->kuota_unit }} unit) untuk kategori ini telah habis disetujui (ACC). Jika Pemerintah Pusat (Kementan) atau APBD Perubahan memberikan tambahan unit, minta Admin Dinas memperbarui kuota di Dashboard Admin agar slot ACC terbuka kembali.
                    @else
                        Saat ini tersisa <strong>{{ $sisaJatah }} unit</strong> siap dialokasikan kepada Poktan prioritas tertinggi berdasarkan peringkat rekomendasi SPK TOPSIS.
                    @endif
                </p>
            </div>

            <!-- Mini Metrics Badges & Progress -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="px-4 py-3 rounded-2xl bg-bone-canvas border border-bone-slate text-center min-w-[85px]">
                        <div class="text-[10px] text-muted-green font-bold uppercase">Total Kuota</div>
                        <div class="text-lg font-black font-mono tabular-nums text-obsidian">{{ $selectedAlsintan->kuota_unit }}</div>
                        <div class="text-[10px] text-muted-green">Unit</div>
                    </div>
                    <div class="px-4 py-3 rounded-2xl bg-lime-harvest/30 border border-lime-harvest text-center min-w-[85px]">
                        <div class="text-[10px] text-forest-dark font-bold uppercase">Telah ACC</div>
                        <div class="text-lg font-black font-mono tabular-nums text-forest-dark">{{ $selectedAlsintan->tersalurkan }}</div>
                        <div class="text-[10px] text-forest-dark">Unit</div>
                    </div>
                    <div class="px-4 py-3 rounded-2xl {{ $isHabis ? 'bg-red-100 text-red-900 border-red-300' : 'bg-celery-cream text-forest-dark border-bone-slate' }} border text-center min-w-[85px]">
                        <div class="text-[10px] font-bold uppercase">Sisa Kuota</div>
                        <div class="text-lg font-black font-mono tabular-nums">{{ $sisaJatah }}</div>
                        <div class="text-[10px]">Unit</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Panel Utama dengan Tab Switcher: Antrean Aktif vs Riwayat Keputusan -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
        
        <!-- Tab Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-bone-slate pb-4 gap-3">
            <div class="flex flex-wrap sm:flex-nowrap gap-2">
                <a href="{{ route('kepbid.dashboard', ['jenis_id' => $selectedId, 'tab' => 'antrean']) }}" 
                   class="px-3.5 sm:px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-1.5 sm:space-x-2 {{ $tab === 'antrean' || ($tab !== 'riwayat' && $tab !== 'ulasan') ? 'bg-forest-dark text-white shadow-sm' : 'bg-bone-canvas text-forest-dark hover:bg-bone-slate' }}">
                    <span class="material-symbols-outlined text-base">inbox</span>
                    <span>Antrean <span class="hidden sm:inline">Menunggu Keputusan</span></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'antrean' || ($tab !== 'riwayat' && $tab !== 'ulasan') ? 'bg-lime-harvest text-obsidian' : 'bg-bone-slate text-forest-dark' }}">
                        {{ $stats['menunggu_acc'] }}
                    </span>
                </a>

                <a href="{{ route('kepbid.dashboard', ['jenis_id' => $selectedId, 'tab' => 'riwayat']) }}" 
                   class="px-3.5 sm:px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-1.5 sm:space-x-2 {{ $tab === 'riwayat' ? 'bg-forest-dark text-white shadow-sm' : 'bg-bone-canvas text-forest-dark hover:bg-bone-slate' }}">
                    <span class="material-symbols-outlined text-base">history</span>
                    <span>Riwayat <span class="hidden sm:inline">Keputusan Selesai</span></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'riwayat' ? 'bg-lime-harvest text-obsidian' : 'bg-bone-slate text-forest-dark' }}">
                        {{ $stats['disetujui'] + $stats['ditolak'] }}
                    </span>
                </a>

                <a href="{{ route('kepbid.dashboard', ['jenis_id' => $selectedId, 'tab' => 'ulasan']) }}" 
                   class="px-3.5 sm:px-5 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-1.5 sm:space-x-2 {{ $tab === 'ulasan' ? 'bg-forest-dark text-white shadow-sm' : 'bg-bone-canvas text-forest-dark hover:bg-bone-slate' }}">
                    <span class="material-symbols-outlined text-base">hotel_class</span>
                    <span>Survei IKM <span class="hidden sm:inline">&amp; Ulasan</span></span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $tab === 'ulasan' ? 'bg-lime-harvest text-obsidian' : 'bg-bone-slate text-forest-dark' }}">
                        {{ $statsUlasan['total'] }}
                    </span>
                </a>
            </div>

            <a href="{{ route('kepbid.rekap.cetak', ['jenis_id' => $selectedId]) }}" target="_blank"
               class="text-xs text-forest-dark hover:text-forest-deep font-bold flex items-center justify-center space-x-1.5 bg-celery-cream px-4 py-2 rounded-full border border-bone-slate transition shadow-2xs w-full sm:w-auto">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Cetak Rekap Kategori Ini</span>
            </a>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 1: ANTREAN MENUNGGU KEPUTUSAN (Hanya yang berstatus 'dirangking')     --}}
        {{-- ========================================================================= --}}
        @if($tab === 'antrean' || ($tab !== 'riwayat' && $tab !== 'ulasan'))
            <div>
                <div class="mb-4">
                    <h2 class="font-extrabold text-obsidian text-sm uppercase tracking-wider flex items-center font-urbanist">
                        <span class="material-symbols-outlined text-amber-harvest mr-1.5">bolt</span>
                        Usulan Menunggu Keputusan Pimpinan
                    </h2>
                    <p class="text-xs text-muted-green mt-0.5">
                        Usulan di bawah disusun berdasarkan urutan nilai preferensi TOPSIS tertinggi. Begitu Anda klik <strong>ACC</strong> atau <strong>Tolak</strong>, usulan akan langsung berpindah ke Riwayat dan diberi batas retensi kedaluwarsa.
                    </p>
                </div>

                @if($antrean->isEmpty())
                    <div class="py-16 text-center text-muted-green space-y-3 bg-bone-canvas/40 rounded-3xl border border-dashed border-bone-slate">
                        <div class="w-16 h-16 mx-auto rounded-full bg-celery-cream text-forest-dark flex items-center justify-center text-3xl">
                            <span class="material-symbols-outlined">check_circle</span>
                        </div>
                        <h3 class="font-extrabold text-obsidian text-base font-urbanist">Antrean Keputusan Bersih!</h3>
                        <p class="text-xs text-muted-green max-w-md mx-auto leading-relaxed">
                            Tidak ada usulan yang menunggu persetujuan untuk kategori alat ini. Semua usulan yang dihitung sudah Anda putuskan (ACC/Tolak) atau belum ada pengajuan baru yang dinilai.
                        </p>
                        <a href="{{ route('kepbid.dashboard', ['jenis_id' => $selectedId, 'tab' => 'riwayat']) }}" 
                           class="inline-flex items-center px-5 py-2.5 rounded-full text-xs font-bold bg-celery-cream text-forest-dark hover:bg-white border border-bone-slate transition">
                            <span class="material-symbols-outlined text-sm mr-1.5">history</span>
                            <span>Lihat Arsip Riwayat Keputusan</span>
                        </a>
                    </div>
                @else
                    <!-- TAMPILAN KARTU MOBILE (< md): Bebas Geser Samping -->
                    <div class="space-y-3.5 md:hidden">
                        @foreach($antrean as $item)
                            @php
                                $isTop = $item->hasilTopsis?->ranking === 1;
                                $sisaJatahAlat = $selectedAlsintan?->sisa_kuota ?? 0;
                                $bisaDiAcc = ($sisaJatahAlat >= $item->jumlah_diminta);
                            @endphp
                            <div class="p-4 rounded-2xl border {{ $isTop ? 'border-lime-harvest bg-lime-harvest/10' : 'border-bone-slate bg-bone-canvas/40' }} space-y-3 shadow-2xs">
                                <div class="flex items-start justify-between gap-2.5">
                                    <div class="flex items-start gap-2.5 min-w-0">
                                        <span class="w-9 h-9 rounded-full font-black flex items-center justify-center text-xs shrink-0 {{ $isTop ? 'bg-lime-harvest text-obsidian shadow-sm border border-black/10' : 'bg-white text-forest-dark border border-bone-slate' }}">
                                            #{{ $item->hasilTopsis?->ranking ?? '-' }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-sm text-obsidian leading-snug">{{ $item->user?->kelompokTani?->nama_poktan }}</div>
                                            <div class="text-[11px] text-muted-green mt-0.5">
                                                Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}
                                            </div>
                                            <div class="text-[11px] text-muted-green font-mono tabular-nums mt-0.5">
                                                Lahan: {{ $item->user?->kelompokTani?->luas_lahan_total }} Ha &bull; {{ $item->user?->kelompokTani?->jumlah_anggota }} Petani
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 space-y-1">
                                        <span class="block font-mono tabular-nums font-extrabold text-xs text-forest-dark bg-celery-cream px-2.5 py-1 rounded-xl border border-bone-slate">
                                            <i>V</i><sub>i</sub>: {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 4) }}
                                        </span>
                                        <span class="inline-block font-bold text-[11px] text-obsidian font-mono tabular-nums bg-white px-2 py-0.5 rounded-lg border border-bone-slate">
                                            {{ $item->jumlah_diminta }} Unit
                                        </span>
                                    </div>
                                </div>

                                @if(!empty($item->alasan_pengajuan))
                                    <p class="text-[11px] text-muted-green italic bg-white/80 p-2.5 rounded-xl border border-bone-slate/60">
                                        "{{ $item->alasan_pengajuan }}"
                                    </p>
                                @endif

                                @if($item->berkasPengajuans->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($item->berkasPengajuans as $berkas)
                                            <a href="{{ route('berkas.unduh', [$item->id, $berkas->id]) }}"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold text-forest-dark bg-celery-cream hover:bg-lime-harvest px-3 py-1.5 rounded-xl border border-bone-slate transition">
                                                <span class="material-symbols-outlined text-xs text-red-600">picture_as_pdf</span>
                                                <span>Unduh Proposal</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="pt-2 border-t border-bone-slate/60 grid grid-cols-2 gap-2">
                                    @if($bisaDiAcc)
                                        <button type="button"
                                                @click="openConfirmModal(
                                                    '{{ route('kepbid.putuskan', $item->id) }}',
                                                    'setuju',
                                                    'Disetujui berdasarkan rekomendasi peringkat TOPSIS.',
                                                    'Persetujuan Usulan Bantuan Alsintan',
                                                    'Apakah Anda yakin ingin MENYETUJUI usulan {{ $item->nomor_pengajuan }} ({{ $item->user?->kelompokTani?->nama_poktan }})? Alokasi kuota alat akan berkurang {{ $item->jumlah_diminta }} unit.',
                                                    'Ya, Setujui Usulan',
                                                    'bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10',
                                                    'check_circle'
                                                )"
                                                class="w-full px-3 py-2.5 min-h-[44px] rounded-full text-xs font-extrabold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center justify-center gap-1 active:scale-95">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                            <span>Setujui (ACC)</span>
                                        </button>
                                    @else
                                        <div class="w-full inline-flex items-center justify-center px-3 py-2.5 min-h-[44px] rounded-full text-[11px] font-bold bg-gray-100 text-gray-500 border border-gray-200 cursor-not-allowed">
                                            <span class="material-symbols-outlined text-xs mr-1">lock</span>
                                            <span>Kuota Habis</span>
                                        </div>
                                    @endif

                                    <button type="button"
                                            @click="openConfirmModal(
                                                '{{ route('kepbid.putuskan', $item->id) }}',
                                                'tolak',
                                                'Ditolak karena keterbatasan kuota alokasi bantuan tahun ini.',
                                                'Penolakan Usulan Bantuan Alsintan',
                                                'Apakah Anda yakin ingin MENOLAK usulan {{ $item->nomor_pengajuan }} ({{ $item->user?->kelompokTani?->nama_poktan }})? Usulan akan dipindahkan ke Riwayat dengan masa retensi berkas 90 hari.',
                                                'Ya, Tolak Usulan',
                                                'bg-red-600 hover:bg-red-700 text-white',
                                                'cancel'
                                            )"
                                            class="w-full px-3 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-white hover:bg-red-50 text-obsidian transition border border-bone-slate flex items-center justify-center gap-1 active:scale-95">
                                        <span class="material-symbols-outlined text-sm text-red-600">close</span>
                                        <span>Tolak</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TAMPILAN TABEL DESKTOP (>= md) DENGAN PEMBUNGKUS OVERFLOW AMAN -->
                    <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/40 font-bold whitespace-nowrap">
                                    <th class="py-3.5 px-4 rounded-l-xl text-center whitespace-nowrap">Peringkat</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Kelompok Tani (Poktan)</th>
                                    <th class="py-3.5 px-4 text-right whitespace-nowrap">Nilai Preferensi (<i>V</i><sub>i</sub>)</th>
                                    <th class="py-3.5 px-4 text-center whitespace-nowrap">Jumlah Unit</th>
                                    <th class="py-3.5 px-4 rounded-r-xl text-center whitespace-nowrap">Aksi Pimpinan (1-Klik)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate text-obsidian font-semibold">
                                @foreach($antrean as $item)
                                    @php
                                        $isTop = $item->hasilTopsis?->ranking === 1;
                                    @endphp
                                    <tr class="hover:bg-bone-canvas/30 transition">
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            <span class="w-8 h-8 mx-auto rounded-full font-black flex items-center justify-center text-xs {{ $isTop ? 'bg-lime-harvest text-obsidian shadow-sm border border-black/10' : 'bg-bone-canvas text-forest-dark border border-bone-slate' }}">
                                                #{{ $item->hasilTopsis?->ranking ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap max-w-sm">
                                            <div class="font-extrabold text-sm sm:text-base text-obsidian truncate max-w-xs" title="{{ $item->user?->kelompokTani?->nama_poktan }}">{{ $item->user?->kelompokTani?->nama_poktan }}</div>
                                            <div class="text-[11px] text-muted-green mt-0.5 truncate max-w-sm" title="Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; Lahan: {{ $item->user?->kelompokTani?->luas_lahan_total }} Ha &bull; {{ $item->user?->kelompokTani?->jumlah_anggota }} Petani">
                                                Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; Lahan: {{ $item->user?->kelompokTani?->luas_lahan_total }} Ha &bull; {{ $item->user?->kelompokTani?->jumlah_anggota }} Petani
                                            </div>
                                            <div class="text-[11px] text-muted-green/80 mt-1 italic truncate max-w-sm" title="{{ $item->alasan_pengajuan }}">
                                                "{{ $item->alasan_pengajuan }}"
                                            </div>
                                            @if($item->berkasPengajuans->isNotEmpty())
                                                <div class="mt-1.5 flex flex-wrap gap-1">
                                                    @foreach($item->berkasPengajuans as $berkas)
                                                        <a href="{{ route('berkas.unduh', [$item->id, $berkas->id]) }}" 
                                                           class="inline-flex items-center gap-1 text-[10px] font-bold text-forest-dark bg-celery-cream hover:bg-lime-harvest px-2 py-0.5 rounded-lg border border-bone-slate transition"
                                                           title="Unduh Berkas Proposal PDF Resmi">
                                                            <span class="material-symbols-outlined text-[11px] text-red-600">picture_as_pdf</span>
                                                            <span>Lampiran Proposal</span>
                                                            <span class="material-symbols-outlined text-[10px]">download</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-right whitespace-nowrap">
                                            <span class="font-mono tabular-nums font-extrabold text-sm text-forest-dark bg-celery-cream px-3 py-1 rounded-xl border border-bone-slate">
                                                {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 6) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            <span class="font-bold text-obsidian font-mono tabular-nums">{{ $item->jumlah_diminta }} Unit</span>
                                        </td>
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center space-x-2">
                                                @php
                                                    $sisaJatahAlat = $selectedAlsintan?->sisa_kuota ?? 0;
                                                    $bisaDiAcc = ($sisaJatahAlat >= $item->jumlah_diminta);
                                                @endphp

                                                @if($bisaDiAcc)
                                                    <button type="button" 
                                                            @click="openConfirmModal(
                                                                '{{ route('kepbid.putuskan', $item->id) }}',
                                                                'setuju',
                                                                'Disetujui berdasarkan rekomendasi peringkat TOPSIS.',
                                                                'Persetujuan Usulan Bantuan Alsintan',
                                                                'Apakah Anda yakin ingin MENYETUJUI usulan {{ $item->nomor_pengajuan }} ({{ $item->user?->kelompokTani?->nama_poktan }})? Alokasi kuota alat akan berkurang {{ $item->jumlah_diminta }} unit.',
                                                                'Ya, Setujui Usulan',
                                                                'bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10',
                                                                'check_circle'
                                                            )"
                                                            class="px-4 py-2.5 min-h-[44px] rounded-full text-xs font-extrabold bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian transition shadow-sm border border-black/10 flex items-center space-x-1.5 active:scale-[0.98]">
                                                        <span class="material-symbols-outlined text-sm">check</span>
                                                        <span>Setujui (ACC)</span>
                                                    </button>
                                                @else
                                                    <!-- Kuota Habis: Tombol Terkunci Aman -->
                                                    <div class="inline-flex items-center px-3.5 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200 cursor-not-allowed select-none" 
                                                         title="Sisa kuota unit tidak mencukupi permintaan ({{ $item->jumlah_diminta }} unit). Admin Dinas perlu menambah kuota terlebih dahulu di Dashboard Admin.">
                                                        <span class="material-symbols-outlined text-xs mr-1 text-gray-500">lock</span>
                                                        <span>Kuota Habis (0 Unit)</span>
                                                    </div>
                                                @endif

                                                <button type="button" 
                                                        @click="openConfirmModal(
                                                            '{{ route('kepbid.putuskan', $item->id) }}',
                                                            'tolak',
                                                            'Ditolak karena keterbatasan kuota alokasi bantuan tahun ini.',
                                                            'Penolakan Usulan Bantuan Alsintan',
                                                            'Apakah Anda yakin ingin MENOLAK usulan {{ $item->nomor_pengajuan }} ({{ $item->user?->kelompokTani?->nama_poktan }})? Usulan akan dipindahkan ke Riwayat dengan masa retensi berkas 90 hari.',
                                                            'Ya, Tolak Usulan',
                                                            'bg-red-600 hover:bg-red-700 text-white',
                                                            'cancel'
                                                        )"
                                                        class="px-3.5 py-2.5 min-h-[44px] rounded-full text-xs font-bold bg-bone-canvas hover:bg-red-50 text-obsidian transition border border-bone-slate flex items-center space-x-1 active:scale-[0.98]">
                                                    <span class="material-symbols-outlined text-sm text-red-600">close</span>
                                                    <span>Tolak</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($antrean->hasPages())
                        <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                            {{ $antrean->links('vendor.pagination.custom-tailwind') }}
                        </div>
                    @endif
                @endif
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 2: RIWAYAT KEPUTUSAN (Telah di-ACC atau Ditolak)                      --}}
        {{-- ========================================================================= --}}
        @elseif($tab === 'riwayat')
            <div>
                <div class="mb-4">
                    <h2 class="font-extrabold text-obsidian text-sm uppercase tracking-wider flex items-center font-urbanist">
                        <span class="material-symbols-outlined text-forest-dark mr-1.5">history_edu</span>
                        Riwayat Keputusan Kategori: {{ $jenisAlsintans->firstWhere('id', $selectedId)?->nama_alsintan }}
                    </h2>
                    <p class="text-xs text-muted-green mt-0.5">
                        Daftar usulan yang telah selesai diputuskan. Berkas bantuan yang disetujui (ACC) disimpan selama <strong>13 bulan</strong> (1 tahun anggaran + 1 bulan rekapitulasi audit LPJ) sebelum berkas fisik dipruning.
                    </p>
                </div>

                @if($riwayat->isEmpty())
                    <div class="py-16 text-center text-muted-green space-y-2 bg-bone-canvas/40 rounded-3xl border border-dashed border-bone-slate">
                        <span class="material-symbols-outlined text-4xl text-forest-dark/40">folder_open</span>
                        <p class="text-sm font-bold text-obsidian">Belum ada riwayat keputusan untuk kategori alat ini.</p>
                        <p class="text-xs text-muted-green">Silakan proses persetujuan usulan pada tab Antrean Menunggu Keputusan.</p>
                    </div>
                @else
                    <!-- TAMPILAN KARTU RIWAYAT MOBILE (< md) -->
                    <div class="space-y-3 md:hidden">
                        @foreach($riwayat as $item)
                            @php
                                $isAcc = $item->status === 'disetujui_kabid';
                                $sisaHari = $item->sisaHariRetensi();
                            @endphp
                            <div class="p-4 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-2.5 shadow-2xs">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white border border-bone-slate text-forest-dark">#{{ $item->hasilTopsis?->ranking ?? '-' }}</span>
                                            <span class="font-extrabold text-sm text-obsidian">{{ $item->user?->kelompokTani?->nama_poktan }}</span>
                                        </div>
                                        <div class="text-[11px] text-muted-green mt-0.5">
                                            Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; {{ $item->jumlah_diminta }} Unit
                                        </div>
                                    </div>
                                    @if($isAcc)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest shrink-0">DISETUJUI</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-900 border border-red-300 shrink-0">DITOLAK</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-bone-slate/60 text-[11px] tabular-nums">
                                    <span class="font-mono font-bold text-forest-dark bg-celery-cream px-2 py-0.5 rounded-lg border border-bone-slate">
                                        <i>V</i><sub>i</sub>: {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 6) }}
                                    </span>
                                    <span class="text-muted-green">
                                        {{ $item->approved_at?->translatedFormat('d M Y') ?? '-' }}
                                    </span>
                                    <span class="text-[10px] font-bold {{ $item->isFilesPruned() ? 'text-muted-green' : ($isAcc ? 'text-forest-dark' : 'text-amber-800') }}">
                                        {{ $item->isFilesPruned() ? 'PDF Dipruning' : ($isAcc ? 'Retensi 13 Bln' : 'Retensi 90 Hr') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TAMPILAN TABEL DESKTOP (>= md) DENGAN PEMBUNGKUS OVERFLOW AMAN -->
                    <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/40 font-bold whitespace-nowrap">
                                    <th class="py-3.5 px-4 rounded-l-xl text-center whitespace-nowrap">Rank</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Kelompok Tani</th>
                                    <th class="py-3.5 px-4 text-right whitespace-nowrap">Skor (Vᵢ)</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Keputusan Pimpinan</th>
                                    <th class="py-3.5 px-4 whitespace-nowrap">Tanggal ACC</th>
                                    <th class="py-3.5 px-4 rounded-r-xl whitespace-nowrap">Masa Retensi Berkas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate text-obsidian font-semibold">
                                @foreach($riwayat as $item)
                                    @php
                                        $isAcc = $item->status === 'disetujui_kabid';
                                        $sisaHari = $item->sisaHariRetensi();
                                    @endphp
                                    <tr class="hover:bg-bone-canvas/30 transition">
                                        <td class="py-4 px-4 text-center whitespace-nowrap">
                                            <span class="w-8 h-8 mx-auto rounded-full font-bold flex items-center justify-center text-xs bg-bone-canvas text-forest-dark border border-bone-slate">
                                                #{{ $item->hasilTopsis?->ranking ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap max-w-sm">
                                            <div class="font-extrabold text-sm text-obsidian truncate max-w-xs" title="{{ $item->user?->kelompokTani?->nama_poktan }}">{{ $item->user?->kelompokTani?->nama_poktan }}</div>
                                            <div class="text-[11px] text-muted-green truncate max-w-xs" title="Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; {{ $item->jumlah_diminta }} Unit">
                                                Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; {{ $item->jumlah_diminta }} Unit
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right whitespace-nowrap">
                                            <span class="font-mono tabular-nums font-bold text-xs text-forest-dark bg-celery-cream px-2.5 py-1 rounded-lg border border-bone-slate">
                                                {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 6) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($isAcc)
                                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm text-forest-dark">task_alt</span>
                                                    DISETUJUI (ACC)
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-900 border border-red-300 inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm text-red-600">cancel</span>
                                                    DITOLAK
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-obsidian whitespace-nowrap">
                                            <span class="block font-bold">{{ $item->approved_at?->translatedFormat('d M Y') ?? '-' }}</span>
                                            <span class="text-[10px] text-muted-green font-mono">{{ $item->approved_at?->format('H:i') }} WIB</span>
                                        </td>
                                        <td class="py-4 px-4 whitespace-nowrap">
                                            @if($item->isFilesPruned())
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-bone-canvas text-muted-green border border-bone-slate">
                                                    <span class="material-symbols-outlined text-xs mr-1">inventory_2</span>
                                                    Berkas Fisik Dipruning
                                                </span>
                                            @else
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $isAcc ? 'bg-celery-cream text-forest-dark border border-bone-slate' : 'bg-amber-100 text-amber-900 border border-amber-harvest' }}">
                                                        <span class="material-symbols-outlined text-xs mr-1">security</span>
                                                        {{ $isAcc ? 'Retensi 13 Bulan Audit' : 'Retensi 90 Hari' }}
                                                    </span>
                                                    <span class="block text-[11px] text-muted-green mt-0.5 font-mono">
                                                        Kedaluwarsa: {{ $item->expired_at?->format('d/m/Y') }} 
                                                        @if($sisaHari !== null && $sisaHari > 0)
                                                             ({{ $sisaHari }} hr lagi)
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($riwayat->hasPages())
                        <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                            {{ $riwayat->links('vendor.pagination.custom-tailwind') }}
                        </div>
                    @endif
                @endif
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: SURVEI KEPUASAN MASYARAKAT (IKM) & KURASI ULASAN BERANDA          --}}
        {{-- ========================================================================= --}}
        @else
            <div class="space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-obsidian text-sm sm:text-base uppercase tracking-wider flex items-center font-urbanist">
                            <span class="material-symbols-outlined text-forest-dark mr-1.5">hotel_class</span>
                            Survei Kepuasan Masyarakat (IKM) &amp; Kurasi Ulasan Beranda
                        </h2>
                        <p class="text-xs text-muted-green mt-0.5">
                            Pantau tingkat kepuasan Kelompok Tani secara transparan dan tentukan ulasan yang disetujui untuk ditampilkan pada Landing Page publik.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-2xl shrink-0">
                        <span class="material-symbols-outlined text-emerald-700 text-lg">verified</span>
                        <span class="text-xs font-bold text-emerald-900">Permenpan-RB No. 14 / 2017</span>
                    </div>
                </div>

                <!-- 4 Statistik Ringkas IKM -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div class="bg-bone-canvas/70 rounded-2xl p-4 border border-bone-slate">
                        <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Indeks Kepuasan (IKM)</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl sm:text-3xl font-black text-forest-dark font-urbanist tabular-nums">{{ number_format($statsUlasan['avg'], 2) }}</span>
                            <span class="text-xs text-muted-green font-bold">/ 5.0</span>
                        </div>
                        <div class="flex items-center gap-0.5 text-amber-500 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' {{ $i <= round($statsUlasan['avg']) ? 1 : 0 }};">star</span>
                            @endfor
                        </div>
                    </div>

                    <div class="bg-bone-canvas/70 rounded-2xl p-4 border border-bone-slate">
                        <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Total Responden Poktan</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl sm:text-3xl font-black text-obsidian font-urbanist tabular-nums">{{ $statsUlasan['total'] }}</span>
                            <span class="text-xs text-muted-green font-bold">Poktan</span>
                        </div>
                        <span class="text-[10px] text-muted-green mt-1 block">Survei bersifat sukarela/opsional</span>
                    </div>

                    <div class="bg-bone-canvas/70 rounded-2xl p-4 border border-bone-slate">
                        <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Tayang di Beranda Publik</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl sm:text-3xl font-black text-emerald-700 font-urbanist tabular-nums">{{ $statsUlasan['tampil_landing'] }}</span>
                            <span class="text-xs text-muted-green font-bold">Ulasan Disetujui</span>
                        </div>
                        <span class="text-[10px] text-emerald-700 font-medium mt-1 block">Telah dikurasi oleh Kepala Bidang</span>
                    </div>

                    <div class="bg-bone-canvas/70 rounded-2xl p-4 border border-bone-slate">
                        <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Menunggu / Internal Saja</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-2xl sm:text-3xl font-black text-amber-700 font-urbanist tabular-nums">{{ $statsUlasan['pending'] }}</span>
                            <span class="text-xs text-muted-green font-bold">Ulasan</span>
                        </div>
                        <span class="text-[10px] text-muted-green mt-1 block">Tersimpan untuk arsip dinas</span>
                    </div>
                </div>

                <!-- Tabel Kurasi Ulasan -->
                @if($ulasanList->isEmpty())
                    <div class="py-16 text-center text-muted-green space-y-2 bg-bone-canvas/40 rounded-3xl border border-dashed border-bone-slate">
                        <span class="material-symbols-outlined text-4xl text-bone-slate">rate_review</span>
                        <p class="text-sm font-bold text-obsidian">Belum ada ulasan atau survei kepuasan yang dikirimkan oleh Kelompok Tani.</p>
                        <p class="text-xs text-muted-green">Ulasan akan muncul otomatis setelah poktan mengisi survei kepuasan di dashboard mereka.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-bone-slate">
                        <table class="w-full text-left text-xs text-obsidian">
                            <thead class="bg-bone-canvas/90 text-forest-dark uppercase tracking-wider text-[11px] font-black border-b border-bone-slate">
                                <tr>
                                    <th class="py-3.5 px-4">Kelompok Tani (Responden)</th>
                                    <th class="py-3.5 px-4 text-center">Rating</th>
                                    <th class="py-3.5 px-4">Ulasan / Masukan</th>
                                    <th class="py-3.5 px-4">Usulan Terkait</th>
                                    <th class="py-3.5 px-4 text-center">Status Beranda</th>
                                    <th class="py-3.5 px-4 text-right">Aksi Kurasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate/60 bg-white">
                                @foreach($ulasanList as $u)
                                    <tr class="hover:bg-bone-canvas/40 transition">
                                        <td class="py-3 px-4">
                                            <div class="font-extrabold text-forest-dark text-xs sm:text-sm">
                                                {{ $u->kelompokTani?->nama_poktan ?? 'Kelompok Tani' }}
                                            </div>
                                            <div class="text-[11px] text-muted-green">
                                                Kec. {{ $u->kelompokTani?->kecamatan ?? '-' }}, Desa {{ $u->kelompokTani?->desa ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-muted-green/80 mt-0.5">
                                                Ketua: {{ $u->kelompokTani?->nama_ketua ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center shrink-0">
                                            <div class="inline-flex items-center gap-0.5 text-amber-500 font-bold">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' {{ $s <= $u->rating_bintang ? 1 : 0 }};">star</span>
                                                @endfor
                                            </div>
                                            <div class="text-[10px] font-bold text-obsidian tabular-nums mt-0.5">
                                                {{ $u->rating_bintang }} / 5
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 max-w-xs sm:max-w-md">
                                            @if($u->komentar)
                                                <p class="text-xs text-obsidian italic leading-relaxed">
                                                    &ldquo;{{ $u->komentar }}&rdquo;
                                                </p>
                                            @else
                                                <span class="text-muted-green/60 italic text-[11px]">Hanya memberikan rating bintang</span>
                                            @endif
                                            <span class="text-[10px] text-muted-green block mt-1">
                                                Dikirim pada: {{ $u->created_at->format('d/m/Y H:i') }} WIB
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-[11px]">
                                            @if($u->pengajuanBantuan)
                                                <div class="font-bold text-forest-dark">{{ $u->pengajuanBantuan->jenisAlsintan?->nama_alsintan ?? 'Mesin Alsintan' }}</div>
                                                <div class="text-muted-green font-mono">{{ $u->pengajuanBantuan->nomor_pengajuan }}</div>
                                            @else
                                                <span class="text-muted-green">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if($u->is_tampil_landing)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                                    <span class="material-symbols-outlined text-xs">visibility</span>
                                                    <span>Tampil di Beranda</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                    <span class="material-symbols-outlined text-xs">visibility_off</span>
                                                    <span>Internal Saja</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <form method="POST" action="{{ route('kepbid.ulasan.toggle_landing', $u->id) }}">
                                                @csrf
                                                @if($u->is_tampil_landing)
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition shadow-2xs">
                                                        <span class="material-symbols-outlined text-xs">hide_source</span>
                                                        <span>Sembunyikan</span>
                                                    </button>
                                                @else
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-forest-dark hover:bg-forest-hover text-lime-harvest transition shadow-2xs">
                                                        <span class="material-symbols-outlined text-xs">publish</span>
                                                        <span>Tampilkan di Beranda</span>
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($ulasanList->hasPages())
                        <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                            {{ $ulasanList->links('vendor.pagination.custom-tailwind') }}
                        </div>
                    @endif
                @endif
            </div>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI KEPUTUSAN KEPBID (POP-UP ANIMASI HALUS & KONTRAST TINGGI) -->
    <!-- ========================================================================= -->
    <div x-cloak 
         x-show="confirmModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         @keydown.escape.window="confirmModalOpen = false">
        
        <!-- Backdrop Blur Transparan -->
        <div x-show="confirmModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-obsidian/60 backdrop-blur-xs transition-opacity"
             @click="confirmModalOpen = false"></div>

        <!-- Wadah Kotak Dialog Modal -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="confirmModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-bone-slate p-6 sm:p-7">
                
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                         :class="confirmKeputusan === 'setuju' ? 'bg-emerald-100 text-forest-dark' : 'bg-red-100 text-red-700'">
                        <span class="material-symbols-outlined text-2xl" x-text="confirmIcon"></span>
                    </div>
                    <div class="mt-0.5 text-left flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-black font-urbanist text-obsidian tracking-tight" id="modal-title" x-text="confirmTitle"></h3>
                        <p class="text-xs sm:text-sm text-obsidian/80 mt-1.5 leading-relaxed font-medium" x-text="confirmDesc"></p>
                    </div>
                </div>

                <!-- Form Submit Keputusan -->
                <form :action="confirmActionUrl" method="POST" class="mt-6 flex flex-col sm:flex-row items-center justify-end gap-2.5">
                    @csrf
                    <input type="hidden" name="keputusan" :value="confirmKeputusan">
                    <input type="hidden" name="catatan_kepbid" :value="confirmCatatan">
                    
                    <button type="button" 
                            @click="confirmModalOpen = false"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-full border border-bone-slate bg-bone-canvas hover:bg-bone-canvas/80 text-obsidian font-bold text-xs sm:text-sm transition order-2 sm:order-1">
                        Batal
                    </button>
                    <button type="submit"
                            :class="confirmBtnClass"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-full font-black text-xs sm:text-sm transition shadow-md flex items-center justify-center gap-1.5 active:scale-95 order-1 sm:order-2">
                        <span class="material-symbols-outlined text-base" x-text="confirmIcon"></span>
                        <span x-text="confirmBtnText"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
