@extends('layouts.app')

@section('title', 'Survei Kepuasan Masyarakat (IKM) - SPK Bantuan Alsintan Subang')

@section('content')
<div class="space-y-6 pb-20">

    <!-- Header Halaman IKM -->
    <div class="bg-forest-dark text-white rounded-3xl p-6 sm:p-8 shadow-sm relative overflow-hidden border border-forest-deep">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -bottom-20 w-64 h-64 rounded-full bg-lime-harvest/10 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-lime-harvest text-obsidian shadow-sm">
                        <span class="material-symbols-outlined text-sm">hotel_class</span>
                        SURVEI KEPUASAN MASYARAKAT (IKM)
                    </span>
                    <span class="text-xs text-celery-cream font-medium bg-white/10 px-3 py-1 rounded-full border border-white/10">
                        Permenpan-RB No. 14 / 2017
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white font-urbanist">
                    Indeks Kepuasan &amp; Kurasi Ulasan Beranda
                </h1>
                <p class="text-xs sm:text-sm text-celery-cream/90 max-w-2xl leading-relaxed">
                    Evaluasi mutu transparansi pelayanan bantuan alsintan berdasarkan penilaian riil dari Kelompok Tani, serta tentukan ulasan yang disetujui untuk ditampilkan pada Landing Page publik.
                </p>
            </div>

            <!-- Skor IKM Mini Banner -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/20 text-center shrink-0 w-full lg:w-auto shadow-sm">
                <span class="text-[11px] font-bold text-celery-cream uppercase tracking-wider block">Indeks Kepuasan Layanan</span>
                <div class="flex items-baseline justify-center gap-1 mt-1">
                    <span class="text-3xl sm:text-4xl font-black text-lime-harvest font-urbanist tabular-nums">{{ number_format($statsUlasan['avg'], 2) }}</span>
                    <span class="text-xs text-celery-cream font-bold">/ 5.0</span>
                </div>
                <div class="flex items-center justify-center gap-0.5 text-amber-300 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' {{ $i <= round($statsUlasan['avg']) ? 1 : 0 }};">star</span>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- 4 Monumental Stats IKM -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-bone-slate shadow-2xs">
            <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Rerata Skor IKM</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl sm:text-3xl font-black text-forest-dark font-urbanist tabular-nums">{{ number_format($statsUlasan['avg'], 2) }}</span>
                <span class="text-xs text-muted-green font-bold">/ 5.0</span>
            </div>
            <div class="flex items-center gap-0.5 text-amber-500 mt-1">
                @for($i = 1; $i <= 5; $i++)
                    <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' {{ $i <= round($statsUlasan['avg']) ? 1 : 0 }};">star</span>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-bone-slate shadow-2xs">
            <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Total Responden Poktan</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl sm:text-3xl font-black text-obsidian font-urbanist tabular-nums">{{ $statsUlasan['total'] }}</span>
                <span class="text-xs text-muted-green font-bold">Poktan</span>
            </div>
            <span class="text-[10px] text-muted-green mt-1 block">Survei bersifat sukarela/opsional</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-bone-slate shadow-2xs">
            <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Tayang di Beranda Publik</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl sm:text-3xl font-black text-emerald-700 font-urbanist tabular-nums">{{ $statsUlasan['tampil_landing'] }}</span>
                <span class="text-xs text-muted-green font-bold">Ulasan Disetujui</span>
            </div>
            <span class="text-[10px] text-emerald-700 font-medium mt-1 block">Maksimal 3 ulasan teratas tampil di carousel</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-bone-slate shadow-2xs">
            <span class="text-[11px] font-bold text-muted-green uppercase tracking-wider block">Menunggu / Internal Saja</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-2xl sm:text-3xl font-black text-amber-700 font-urbanist tabular-nums">{{ $statsUlasan['pending'] }}</span>
                <span class="text-xs text-muted-green font-bold">Ulasan</span>
            </div>
            <span class="text-[10px] text-muted-green mt-1 block">Tersimpan untuk arsip mutu dinas</span>
        </div>
    </div>

    <!-- Kartu Daftar Ulasan & Aksi Kurasi -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-bone-slate pb-4 gap-3">
            <div>
                <h2 class="font-extrabold text-obsidian text-sm sm:text-base uppercase tracking-wider flex items-center font-urbanist">
                    <span class="material-symbols-outlined text-forest-dark mr-1.5">rate_review</span>
                    Daftar Ulasan &amp; Masukan Kelompok Tani
                </h2>
                <p class="text-xs text-muted-green mt-0.5">
                    Klik tombol kurasi untuk menyetujui ulasan yang layak ditampilkan pada carousel beranda utama publik.
                </p>
            </div>
            <div class="text-xs font-bold text-muted-green bg-bone-canvas px-3 py-1.5 rounded-full border border-bone-slate shrink-0">
                Total Masuk: {{ $statsUlasan['total'] }} Ulasan
            </div>
        </div>

        @if($ulasanList->isEmpty())
            <div class="py-16 text-center text-muted-green space-y-2 bg-bone-canvas/40 rounded-3xl border border-dashed border-bone-slate">
                <span class="material-symbols-outlined text-4xl text-bone-slate">reviews</span>
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
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 transition shadow-2xs active:scale-95">
                                                <span class="material-symbols-outlined text-xs">hide_source</span>
                                                <span>Sembunyikan</span>
                                            </button>
                                        @else
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-forest-dark hover:bg-forest-hover text-lime-harvest transition shadow-2xs active:scale-95">
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

</div>
@endsection
