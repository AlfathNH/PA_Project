@extends('layouts.app')

@section('title', 'Riwayat & Rekapitulasi Keputusan Bantuan Alsintan - Dinas Pertanian Kab. Subang')

@section('content')
<div class="space-y-6 w-full max-w-full min-w-0">

    <!-- Header Panel Riwayat (Harvesta Style) -->
    <div class="bg-forest-dark text-white rounded-3xl p-6 sm:p-8 shadow-md flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden border border-forest-deep">
        <!-- Ambient Glow -->
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-emerald-500/10 pointer-events-none blur-3xl"></div>
        <div class="absolute right-40 -bottom-20 w-64 h-64 rounded-full bg-lime-harvest/10 pointer-events-none blur-2xl"></div>

        <div class="space-y-1.5 relative z-10">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold bg-lime-harvest text-obsidian border border-lime-harvest-hover">
                    <span class="material-symbols-outlined text-sm mr-1">history_edu</span>
                    Rekapitulasi & Arsip Tahunan
                </span>
                <span class="text-xs text-celery-cream font-mono">• Tahun Anggaran {{ $filterTahun }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white font-urbanist">
                Riwayat & Rekap Penetapan Bantuan Alsintan
            </h1>
            <p class="text-xs sm:text-sm text-celery-cream/90">
                Arsip rekapitulasi keputusan pemeringkatan SPK TOPSIS untuk pelaporan LPJ bulanan dan tahunan dinas
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10">
            <!-- Tombol Cetak / Unduh Rekap Berita Acara -->
            <a href="{{ route('kepbid.rekap.cetak', request()->query()) }}" target="_blank"
               class="px-5 py-3 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-extrabold text-xs transition shadow-sm border border-black/10 flex items-center space-x-2 active:scale-[0.98]">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Cetak / Unduh Berita Acara</span>
            </a>

            <!-- Tombol Simulasi Auto-Pruning Berkas (Khusus Demo Sidang) -->
            <form action="{{ route('kepbid.pruning.run') }}" method="POST" class="inline" onsubmit="return confirm('Jalankan Engine Auto-Pruning untuk mengecek dan membersihkan file PDF yang masa retensinya sudah kedaluwarsa?');">
                @csrf
                <button type="submit" 
                        class="px-4 py-3 rounded-full bg-forest-deep hover:bg-forest-deep/80 text-celery-cream border border-white/20 font-bold text-xs transition flex items-center space-x-1.5"
                        title="Simulasi pemeriksaan berkas kedaluwarsa">
                    <span class="material-symbols-outlined text-sm text-amber-harvest">cleaning_services</span>
                    <span>Cek Auto-Pruning</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 4 KPI Card Rekapitulasi (Harvesta Card Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Total Telah Diputuskan</span>
                <div class="w-10 h-10 rounded-2xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">fact_check</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $rekapStats['total_diputuskan'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Usulan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-forest-dark"></span>
                    <span class="text-xs font-semibold text-forest-dark">Berdasarkan filter aktif</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-forest-dark h-full w-full rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Disetujui (ACC)</span>
                <div class="w-10 h-10 rounded-2xl bg-lime-harvest/30 text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl text-forest-dark">check_circle</span>
                </div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-extrabold text-forest-dark tracking-tight font-urbanist">
                    {{ $rekapStats['total_disetujui'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Poktan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-lime-harvest"></span>
                    <span class="text-xs font-extrabold text-forest-dark">Total: {{ $rekapStats['total_unit_acc'] }} Unit Mesin</span>
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
                    {{ $rekapStats['total_ditolak'] }}
                    <span class="text-sm font-sans font-normal text-muted-green">Poktan</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span>
                    <span class="text-xs font-semibold text-red-800">Tidak memenuhi kuota/syarat</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-red-400 h-full w-1/4 rounded-full"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-bone-slate shadow-sm space-y-3 relative overflow-hidden group hover:border-forest-dark/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-muted-green uppercase tracking-wider">Status Berkas Fisik</span>
                <div class="w-10 h-10 rounded-2xl bg-celery-cream text-forest-dark flex items-center justify-center text-sm shadow-2xs">
                    <span class="material-symbols-outlined text-xl">storage</span>
                </div>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight font-urbanist">
                    {{ $rekapStats['berkas_aktif'] }} <span class="text-xs font-sans font-normal text-muted-green">Aktif</span> / {{ $rekapStats['berkas_dipruning'] }} <span class="text-xs font-sans font-normal text-muted-green">Pruned</span>
                </div>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-harvest"></span>
                    <span class="text-xs font-semibold text-muted-green">Masa retensi 13 bln / 90 hr</span>
                </div>
            </div>
            <div class="w-full bg-bone-canvas h-1.5 rounded-full overflow-hidden flex gap-1">
                <div class="bg-amber-harvest h-full w-2/3 rounded-full"></div>
                <div class="bg-bone-slate h-full w-1/3 rounded-full"></div>
            </div>
        </div>

    </div>

    <!-- Filter Multi-Kriteria (Tahun, Bulan, Jenis Alat, Status) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 shadow-sm">
        <form action="{{ route('kepbid.riwayat') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            
            <!-- Filter Tahun Anggaran -->
            <div>
                <label for="tahun" class="block text-xs font-bold text-forest-dark uppercase tracking-wider mb-1.5">Tahun Anggaran</label>
                <select name="tahun" id="tahun" class="w-full px-3.5 py-2.5 bg-bone-canvas/60 border border-bone-slate rounded-2xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    @foreach(range(date('Y') + 1, date('Y') - 3) as $yr)
                        <option value="{{ $yr }}" {{ $filterTahun == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Bulan -->
            <div>
                <label for="bulan" class="block text-xs font-bold text-forest-dark uppercase tracking-wider mb-1.5">Bulan Penetapan</label>
                <select name="bulan" id="bulan" class="w-full px-3.5 py-2.5 bg-bone-canvas/60 border border-bone-slate rounded-2xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    <option value="">-- Semua Bulan (Rekap 1 Tahun) --</option>
                    @php
                        $daftarBulan = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" {{ $filterBulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Jenis Alsintan -->
            <div>
                <label for="jenis_id" class="block text-xs font-bold text-forest-dark uppercase tracking-wider mb-1.5">Kategori Alsintan</label>
                <select name="jenis_id" id="jenis_id" class="w-full px-3.5 py-2.5 bg-bone-canvas/60 border border-bone-slate rounded-2xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    <option value="">-- Semua Jenis Alat --</option>
                    @foreach($jenisAlsintans as $j)
                        <option value="{{ $j->id }}" {{ $filterJenis == $j->id ? 'selected' : '' }}>
                            [{{ $j->kode_alsintan }}] {{ $j->nama_alsintan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Keputusan -->
            <div>
                <label for="status" class="block text-xs font-bold text-forest-dark uppercase tracking-wider mb-1.5">Status Keputusan</label>
                <select name="status" id="status" class="w-full px-3.5 py-2.5 bg-bone-canvas/60 border border-bone-slate rounded-2xl text-xs font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    <option value="semua" {{ $filterStatus === 'semua' ? 'selected' : '' }}>-- Semua Keputusan --</option>
                    <option value="disetujui_kabid" {{ $filterStatus === 'disetujui_kabid' ? 'selected' : '' }}>Hanya Disetujui (ACC)</option>
                    <option value="ditolak_kabid" {{ $filterStatus === 'ditolak_kabid' ? 'selected' : '' }}>Hanya Ditolak</option>
                </select>
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="flex items-center space-x-2">
                <button type="submit" 
                        class="w-full py-2.5 px-4 bg-forest-dark hover:bg-forest-deep text-white font-bold text-xs rounded-full transition shadow flex items-center justify-center space-x-1.5">
                    <span class="material-symbols-outlined text-sm">filter_alt</span>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('kepbid.riwayat') }}" 
                   class="p-2.5 bg-bone-canvas hover:bg-bone-slate text-forest-dark rounded-full text-xs transition border border-bone-slate flex items-center justify-center" 
                   title="Reset Filter">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                </a>
            </div>

        </form>
    </div>

    <!-- Tabel Data Rekapitulasi Riwayat -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-bone-slate pb-4">
            <div>
                <h2 class="font-extrabold text-obsidian text-lg font-urbanist">Daftar Arsip Penetapan Bantuan Alsintan</h2>
                <p class="text-xs text-muted-green mt-0.5">Data teks rekapitulasi tersimpan permanen di database untuk audit, terlepas dari berkas fisik yang dipruning.</p>
            </div>
            <span class="text-xs text-forest-dark font-mono font-bold bg-celery-cream px-3 py-1 rounded-full border border-bone-slate">
                Total: {{ $riwayatList->count() }} Data
            </span>
        </div>

        @if($riwayatList->isEmpty())
            <div class="py-16 text-center text-muted-green space-y-3 bg-bone-canvas/40 rounded-3xl border border-dashed border-bone-slate">
                <span class="material-symbols-outlined text-4xl text-forest-dark/40">folder_open</span>
                <h3 class="font-extrabold text-obsidian text-sm font-urbanist">Tidak ada data riwayat yang cocok dengan filter.</h3>
                <p class="text-xs text-muted-green">Silakan ubah opsi filter tahun, bulan, atau jenis alat di atas.</p>
            </div>
        @else
            <!-- TAMPILAN KARTU MOBILE (< md): Bebas Geser Samping -->
            <div class="space-y-3 md:hidden">
                @foreach($riwayatList as $item)
                    @php
                        $isAcc = $item->status === 'disetujui_kabid';
                        $sisaHari = $item->sisaHariRetensi();
                    @endphp
                    <div class="p-4 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-2.5 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-mono font-bold text-[11px] text-forest-dark block">{{ $item->nomor_pengajuan }}</span>
                                <div class="font-extrabold text-sm text-obsidian mt-0.5">{{ $item->user?->kelompokTani?->nama_poktan }}</div>
                                <div class="text-[11px] text-muted-green">
                                    Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}
                                </div>
                            </div>
                            @if($isAcc)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest shrink-0">DISETUJUI</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-900 border border-red-300 shrink-0">DITOLAK</span>
                            @endif
                        </div>

                        <div class="p-2.5 rounded-xl bg-white border border-bone-slate/70 flex items-center justify-between gap-2 text-xs">
                            <div>
                                <div class="font-bold text-obsidian">[{{ $item->jenisAlsintan?->kode_alsintan }}] {{ $item->jenisAlsintan?->nama_alsintan }}</div>
                                <div class="text-[11px] text-forest-dark font-mono font-bold tabular-nums">{{ $item->jumlah_diminta }} Unit (TA {{ $item->tahun_pengajuan }})</div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-mono tabular-nums font-bold text-xs text-forest-dark bg-celery-cream px-2 py-0.5 rounded-lg border border-bone-slate block">
                                    {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 4) }}
                                </span>
                                <span class="text-[10px] text-muted-green tabular-nums">Peringkat #{{ $item->hasilTopsis?->ranking ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted-green tabular-nums">
                            <span>{{ $item->approved_at?->translatedFormat('d M Y, H:i') ?? '-' }} WIB</span>
                            @if($item->isFilesPruned())
                                <span class="font-semibold">Berkas PDF Dipruning</span>
                            @else
                                <span class="font-bold text-forest-dark">{{ $isAcc ? 'Retensi 13 Bulan' : 'Retensi 90 Hari' }} &bull; s.d. {{ $item->expired_at?->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- TAMPILAN TABEL DESKTOP (>= md) DENGAN PEMBUNGKUS OVERFLOW AMAN -->
            <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/40 font-bold whitespace-nowrap">
                            <th class="py-3.5 px-4 rounded-l-xl whitespace-nowrap">No. Usulan & Waktu ACC</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Kelompok Tani (Poktan)</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Kategori Alat & Alokasi</th>
                            <th class="py-3.5 px-4 text-right whitespace-nowrap">Skor TOPSIS (Vᵢ)</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Keputusan</th>
                            <th class="py-3.5 px-4 rounded-r-xl whitespace-nowrap">Masa Retensi Berkas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bone-slate text-obsidian font-semibold">
                        @foreach($riwayatList as $item)
                            @php
                                $isAcc = $item->status === 'disetujui_kabid';
                                $sisaHari = $item->sisaHariRetensi();
                            @endphp
                            <tr class="hover:bg-bone-canvas/30 transition">
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-xs text-forest-dark block">{{ $item->nomor_pengajuan }}</span>
                                    <span class="text-[11px] text-muted-green">
                                        {{ $item->approved_at?->translatedFormat('d M Y, H:i') ?? '-' }} WIB
                                    </span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap max-w-sm">
                                    <div class="font-extrabold text-sm text-obsidian truncate max-w-xs" title="{{ $item->user?->kelompokTani?->nama_poktan }}">{{ $item->user?->kelompokTani?->nama_poktan }}</div>
                                    <div class="text-[11px] text-muted-green truncate max-w-xs" title="Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; Ketua: {{ $item->user?->kelompokTani?->nama_ketua }}">
                                        Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }} &bull; Ketua: {{ $item->user?->kelompokTani?->nama_ketua }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-bold text-obsidian block">
                                        [{{ $item->jenisAlsintan?->kode_alsintan }}] {{ $item->jenisAlsintan?->nama_alsintan }}
                                    </span>
                                    <span class="text-[11px] text-forest-dark font-extrabold font-mono">
                                        Alokasi: {{ $item->jumlah_diminta }} Unit (TA {{ $item->tahun_pengajuan }})
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <span class="font-mono tabular-nums font-bold text-xs text-forest-dark bg-celery-cream px-2.5 py-1 rounded-lg border border-bone-slate">
                                        {{ number_format($item->hasilTopsis?->nilai_preferensi ?? 0, 6) }}
                                    </span>
                                    <span class="block text-[10px] text-muted-green mt-0.5 tabular-nums">Peringkat #{{ $item->hasilTopsis?->ranking ?? '-' }}</span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($isAcc)
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-lime-harvest/40 text-forest-dark border border-lime-harvest inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm text-forest-dark">task_alt</span>
                                            DISETUJUI
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-900 border border-red-300 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm text-red-600">cancel</span>
                                            DITOLAK
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($item->isFilesPruned())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-bone-canvas text-muted-green border border-bone-slate">
                                            <span class="material-symbols-outlined text-xs mr-1">inventory_2</span>
                                            Berkas PDF Dipruning
                                        </span>
                                        <span class="block text-[9px] text-muted-green mt-0.5 font-mono">Pruned: {{ $item->files_pruned_at?->format('d/m/Y') }}</span>
                                    @else
                                        <div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $isAcc ? 'bg-celery-cream text-forest-dark border border-bone-slate' : 'bg-amber-100 text-amber-900 border border-amber-harvest' }}">
                                                <span class="material-symbols-outlined text-xs mr-1">security</span>
                                                {{ $isAcc ? 'Retensi 13 Bulan Audit' : 'Retensi 90 Hari' }}
                                            </span>
                                            <span class="block text-[11px] text-muted-green mt-0.5 font-mono">
                                                Kedaluwarsa: {{ $item->expired_at?->format('d/m/Y') }}
                                                @if($sisaHari !== null && $sisaHari > 0)
                                                    <span class="text-forest-dark font-extrabold">({{ $sisaHari }} hr lagi)</span>
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

            <!-- Footer Pagination Riwayat Keputusan -->
            <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                {{ $riwayatList->links('vendor.pagination.custom-tailwind') }}
            </div>
        @endif
    </div>

</div>
@endsection
