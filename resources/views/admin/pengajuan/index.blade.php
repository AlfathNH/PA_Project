@extends('layouts.app')

@section('title', 'Kelola Usulan Bantuan Alsintan - Admin Dinas Pertanian')

@section('content')
<div class="space-y-6 w-full max-w-full min-w-0">

    <!-- Header Section (Harvesta Executive Style) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold bg-celery-cream text-forest-dark border border-bone-slate">
                    <span class="material-symbols-outlined text-sm mr-1">checklist</span>
                    Alur Kerja Seleksi Bantuan
                </span>
                <span class="text-xs text-muted-green hidden sm:inline">• Dinas Pertanian Subang</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight font-urbanist">
                Verifikasi Berkas &amp; Input Nilai Kriteria (<i>x</i><sub>ij</sub>)
            </h1>
            <p class="text-xs sm:text-sm text-muted-green">
                Tahapan alur: Usulan Masuk &rarr; Verifikasi Berkas CPCL &rarr; Input Nilai Kriteria (<i>x</i><sub>ij</sub>) &rarr; Hitung SPK TOPSIS
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.topsis.index') }}" 
               class="px-5 py-3 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-extrabold text-xs transition shadow-sm border border-black/10 flex items-center space-x-2 active:scale-[0.98]">
                <span class="material-symbols-outlined text-base">calculate</span>
                <span>Menuju Engine TOPSIS</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Bar Pencarian Cepat & Filter Status -->
    <div class="space-y-3">
        <form action="{{ route('admin.pengajuan.index') }}" method="GET" class="no-loader flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            @if($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-muted-green text-lg">search</span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari Poktan, Ketua, Desa, No. Pengajuan, atau Jenis Alsintan..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-bone-slate rounded-2xl text-xs sm:text-sm font-semibold text-obsidian focus:ring-2 focus:ring-forest-dark transition shadow-2xs">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="px-5 py-2.5 rounded-2xl bg-forest-dark hover:bg-forest-deep text-lime-harvest font-black text-xs transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95">
                    <span class="material-symbols-outlined text-sm">search</span>
                    <span>Cari</span>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.pengajuan.index', array_filter(['status' => $status])) }}" 
                       class="px-4 py-2.5 rounded-2xl bg-white hover:bg-bone-canvas text-forest-dark font-bold text-xs transition border border-bone-slate flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-sm">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>

        <!-- Filter Status Bar (Harvesta Rounded Pill Filters) -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.pengajuan.index', array_filter(['search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ !$status ? 'bg-forest-dark text-white shadow-sm' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.pengajuan.index', array_filter(['status' => 'diajukan', 'search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ $status == 'diajukan' ? 'bg-amber-harvest text-obsidian shadow-sm' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Menunggu Verifikasi
            </a>
            <a href="{{ route('admin.pengajuan.index', array_filter(['status' => 'diverifikasi', 'search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ $status == 'diverifikasi' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Lolos Verifikasi
            </a>
            <a href="{{ route('admin.pengajuan.index', array_filter(['status' => 'dinilai', 'search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ $status == 'dinilai' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Siap TOPSIS
            </a>
            <a href="{{ route('admin.pengajuan.index', array_filter(['status' => 'dirangking', 'search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ $status == 'dirangking' ? 'bg-forest-dark text-white shadow-sm' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Selesai Diperingkat
            </a>
            <a href="{{ route('admin.pengajuan.index', array_filter(['status' => 'disetujui_kabid', 'search' => request('search')])) }}" 
               class="px-4 py-2 rounded-full font-bold transition {{ $status == 'disetujui_kabid' ? 'bg-lime-harvest text-obsidian shadow-sm border border-black/10' : 'bg-white text-muted-green border border-bone-slate hover:bg-bone-canvas' }}">
                Disetujui Pimpinan (ACC)
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Pengajuan -->
    <div class="bg-white rounded-3xl border border-bone-slate p-5 sm:p-7 shadow-sm space-y-5">
        <!-- TAMPILAN KARTU MOBILE (< md): Bebas Geser Samping -->
        <div class="space-y-3.5 md:hidden">
            @forelse($pengajuans as $item)
                @php
                    $badgeColor = match($item->status) {
                        'draft'           => 'bg-gray-100 text-gray-700 border-gray-300',
                        'diajukan'        => 'bg-amber-100 text-amber-900 border-amber-harvest',
                        'diverifikasi'    => 'bg-blue-100 text-blue-900 border-blue-300',
                        'dinilai'         => 'bg-teal-100 text-teal-900 border-teal-300',
                        'dirangking'      => 'bg-forest-dark/10 text-forest-dark border-forest-dark/30',
                        'disetujui_kabid' => 'bg-lime-harvest/40 text-forest-dark border-lime-harvest font-extrabold',
                        'ditolak_admin', 'ditolak_kabid' => 'bg-red-100 text-red-900 border-red-300',
                        default           => 'bg-gray-100 text-gray-700',
                    };
                    $statusTeks = match($item->status) {
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'diverifikasi'        => 'Diverifikasi',
                        'dinilai'             => 'Siap Dihitung TOPSIS',
                        'dirangking'          => 'Selesai Diperingkat',
                        'disetujui_kabid'     => 'Disetujui Pimpinan',
                        'ditolak_kabid'       => 'Ditolak Pimpinan',
                        'diterima_poktan'     => 'Diterima Poktan',
                        default               => ucwords(str_replace('_', ' ', $item->status)),
                    };
                    $jumlahNilai = $item->penilaianTopsis->count();
                @endphp
                <div class="p-4 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-3 shadow-2xs">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="font-mono font-bold text-xs text-forest-dark">{{ $item->nomor_pengajuan }}</span>
                            <span class="text-[10px] text-muted-green ml-1.5 tabular-nums">{{ $item->created_at->format('d/m/Y') }}</span>
                            <div class="font-extrabold text-sm text-obsidian mt-0.5">{{ $item->user?->kelompokTani?->nama_poktan ?? '-' }}</div>
                            <div class="text-[11px] text-muted-green">
                                Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 {{ $badgeColor }}">
                            {{ $statusTeks }}
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-white border border-bone-slate/80 flex items-center justify-between gap-2 text-xs">
                        <div>
                            <div class="font-bold text-obsidian">{{ $item->jenisAlsintan?->nama_alsintan }}</div>
                            <div class="text-[11px] text-muted-green tabular-nums">{{ $item->jumlah_diminta }} Unit (TA {{ $item->tahun_pengajuan }})</div>
                        </div>
                        <div class="text-right shrink-0">
                            @if($jumlahNilai >= 5)
                                <span class="inline-flex items-center text-forest-dark bg-celery-cream px-2 py-0.5 rounded-full text-[10px] font-extrabold border border-bone-slate">
                                    5/5 Kriteria
                                </span>
                            @elseif($jumlahNilai > 0)
                                <span class="inline-flex items-center text-amber-900 bg-amber-50 px-2 py-0.5 rounded-full text-[10px] font-bold border border-amber-harvest/40">
                                    {{ $jumlahNilai }}/5 Kriteria
                                </span>
                            @else
                                <span class="text-muted-green text-[10px] italic">Belum dinilai</span>
                            @endif
                        </div>
                    </div>

                    @if($item->berkasPengajuans->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($item->berkasPengajuans as $berkas)
                                <a href="{{ route('berkas.unduh', [$item->id, $berkas->id]) }}"
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-forest-dark bg-celery-cream hover:bg-lime-harvest px-2.5 py-1.5 rounded-xl border border-bone-slate transition">
                                    <span class="material-symbols-outlined text-xs text-red-600">picture_as_pdf</span>
                                    <span>Proposal PDF</span>
                                    <span class="material-symbols-outlined text-xs">download</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2 border-t border-bone-slate/60 flex items-center gap-2">
                        @if($item->status === 'diajukan')
                            <form action="{{ route('admin.pengajuan.verifikasi', $item->id) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="diverifikasi">
                                <input type="hidden" name="catatan_admin" value="Berkas persyaratan dinyatakan lengkap dan sah.">
                                <button type="submit"
                                        class="w-full px-3 py-2.5 min-h-[44px] rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center justify-center gap-1 shadow-sm">
                                    <span class="material-symbols-outlined text-xs">verified</span>
                                    <span>Verifikasi</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.pengajuan.penilaian', $item->id) }}"
                           class="flex-1 px-3.5 py-2.5 min-h-[44px] rounded-full {{ $jumlahNilai > 0 ? 'bg-white hover:bg-bone-slate text-forest-dark border border-bone-slate' : 'bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10' }} font-bold text-xs transition flex items-center justify-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined text-xs">edit_note</span>
                            <span>{{ $jumlahNilai > 0 ? 'Edit Nilai' : 'Input Nilai' }}</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 px-4 text-center text-muted-green space-y-2 bg-bone-canvas/40 rounded-2xl border border-dashed border-bone-slate">
                    @if(request()->filled('status') || request()->filled('search'))
                        <div class="font-bold text-obsidian text-xs">Tidak ditemukan usulan yang sesuai dengan filter atau kata kunci pencarian.</div>
                        <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center px-4 py-2 min-h-[44px] rounded-full bg-white hover:bg-bone-slate text-forest-dark font-bold text-xs border border-bone-slate transition">
                            Tampilkan Semua Usulan
                        </a>
                    @else
                        <div class="text-xs">Belum ada usulan permohonan bantuan yang masuk.</div>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- TAMPILAN TABEL DESKTOP (>= md) DENGAN PEMBUNGKUS OVERFLOW AMAN -->
        <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/40 font-bold whitespace-nowrap">
                        <th class="py-3.5 px-3.5 rounded-l-xl whitespace-nowrap">No. Pengajuan</th>
                        <th class="py-3.5 px-3.5 whitespace-nowrap">Kelompok Tani (Poktan)</th>
                        <th class="py-3.5 px-3.5 whitespace-nowrap">Alat Diminta</th>
                        <th class="py-3.5 px-3.5 whitespace-nowrap">Status Saat Ini</th>
                        <th class="py-3.5 px-3.5 whitespace-nowrap">Status Penilaian</th>
                        <th class="py-3.5 px-3.5 rounded-r-xl text-center whitespace-nowrap">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bone-slate text-obsidian font-semibold">
                    @forelse($pengajuans as $item)
                        @php
                            $badgeColor = match($item->status) {
                                'draft'           => 'bg-gray-100 text-gray-700 border-gray-300',
                                'diajukan'        => 'bg-amber-100 text-amber-900 border-amber-harvest',
                                'diverifikasi'    => 'bg-blue-100 text-blue-900 border-blue-300',
                                'dinilai'         => 'bg-teal-100 text-teal-900 border-teal-300',
                                'dirangking'      => 'bg-forest-dark/10 text-forest-dark border-forest-dark/30',
                                'disetujui_kabid' => 'bg-lime-harvest/40 text-forest-dark border-lime-harvest font-extrabold',
                                'ditolak_admin', 'ditolak_kabid' => 'bg-red-100 text-red-900 border-red-300',
                                default           => 'bg-gray-100 text-gray-700',
                            };
                            $jumlahNilai = $item->penilaianTopsis->count();
                        @endphp
                        <tr class="hover:bg-bone-canvas/30 transition">
                            <td class="py-4 px-3.5 font-mono font-bold text-forest-dark whitespace-nowrap">
                                {{ $item->nomor_pengajuan }}
                                <div class="text-[10px] text-muted-green font-sans font-normal">{{ $item->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-4 px-3.5 whitespace-nowrap max-w-xs">
                                <div class="font-extrabold text-sm text-obsidian truncate max-w-xs" title="{{ $item->user?->kelompokTani?->nama_poktan ?? '-' }}">{{ $item->user?->kelompokTani?->nama_poktan ?? '-' }}</div>
                                <div class="text-[11px] text-muted-green truncate max-w-xs" title="Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}">
                                    Desa {{ $item->user?->kelompokTani?->desa }}, Kec. {{ $item->user?->kelompokTani?->kecamatan }}
                                </div>
                            </td>
                            <td class="py-4 px-3.5 whitespace-nowrap">
                                <span class="font-bold text-obsidian">{{ $item->jenisAlsintan?->nama_alsintan }}</span>
                                <div class="text-[11px] text-muted-green tabular-nums">{{ $item->jumlah_diminta }} Unit (TA {{ $item->tahun_pengajuan }})</div>
                                @if($item->berkasPengajuans->isNotEmpty())
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach($item->berkasPengajuans as $berkas)
                                            <a href="{{ route('berkas.unduh', [$item->id, $berkas->id]) }}" 
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-forest-dark bg-celery-cream hover:bg-lime-harvest px-2 py-0.5 rounded-lg border border-bone-slate transition"
                                               title="Unduh Berkas Proposal PDF Resmi">
                                                <span class="material-symbols-outlined text-[11px] text-red-600">picture_as_pdf</span>
                                                <span>Proposal PDF</span>
                                                <span class="material-symbols-outlined text-[10px]">download</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeColor }}">
                                    {{ match($item->status) {
                                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                        'diverifikasi'        => 'Diverifikasi',
                                        'dinilai'             => 'Siap Dihitung TOPSIS',
                                        'dirangking'          => 'Selesai Diperingkat',
                                        'disetujui_kabid'     => 'Disetujui Pimpinan',
                                        'ditolak_kabid'       => 'Ditolak Pimpinan',
                                        'diterima_poktan'     => 'Diterima Poktan',
                                        default               => ucwords(str_replace('_', ' ', $item->status)),
                                    } }}
                                </span>
                            </td>
                            <td class="py-4 px-3.5">
                                @if($jumlahNilai >= 5)
                                    <span class="inline-flex items-center text-forest-dark bg-celery-cream px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border border-bone-slate">
                                        <span class="material-symbols-outlined text-xs mr-1 text-forest-dark">task_alt</span> 5 Kriteria Terisi
                                    </span>
                                @elseif($jumlahNilai > 0)
                                    <span class="inline-flex items-center text-amber-900 bg-amber-50 px-2.5 py-0.5 rounded-full text-[11px] font-bold border border-amber-harvest/40">
                                        <span class="material-symbols-outlined text-xs mr-1 text-amber-harvest">warning</span> {{ $jumlahNilai }}/5 Kriteria
                                    </span>
                                @else
                                    <span class="text-muted-green text-[11px] italic">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="py-4 px-3.5 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Jika Status 'diajukan': Tombol Verifikasi Berkas -->
                                    @if($item->status === 'diajukan')
                                        <form action="{{ route('admin.pengajuan.verifikasi', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="diverifikasi">
                                            <input type="hidden" name="catatan_admin" value="Berkas persyaratan dinyatakan lengkap dan sah.">
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-full bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center space-x-1 shadow-sm">
                                                <span class="material-symbols-outlined text-xs">verified</span>
                                                <span>Verifikasi</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Tombol Input / Edit Nilai Kriteria TOPSIS -->
                                    <a href="{{ route('admin.pengajuan.penilaian', $item->id) }}" 
                                       class="px-3.5 py-1.5 rounded-full {{ $jumlahNilai > 0 ? 'bg-bone-canvas hover:bg-bone-slate text-forest-dark border border-bone-slate' : 'bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian border border-black/10' }} font-bold text-xs transition flex items-center space-x-1 shadow-sm">
                                        <span class="material-symbols-outlined text-xs">edit_note</span>
                                        <span>{{ $jumlahNilai > 0 ? 'Edit Nilai' : 'Input Nilai' }}</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center text-muted-green space-y-2">
                                @if(request()->filled('status') || request()->filled('search'))
                                    <div class="font-bold text-obsidian">Tidak ditemukan usulan yang sesuai dengan filter atau kata kunci pencarian.</div>
                                    <div>
                                        <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex items-center px-4 py-2 rounded-full bg-bone-canvas hover:bg-bone-slate text-forest-dark font-bold text-xs border border-bone-slate transition">
                                            Tampilkan Semua Usulan
                                        </a>
                                    </div>
                                @else
                                    <div>Belum ada usulan permohonan bantuan yang masuk.</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
            {{ $pengajuans->links('vendor.pagination.custom-tailwind') }}
        </div>
    </div>

</div>
@endsection
