@extends('layouts.app')

@section('title', 'Perhitungan SPK TOPSIS - Dinas Pertanian Kab. Subang')

@section('content')
@php
    $safeExtract = function($p) {
        if (!$p) {
            return ['nama' => 'Poktan', 'nomor' => '-', 'desa' => '-', 'kec' => '-'];
        }
        $nomor = is_array($p) ? ($p['nomor_pengajuan'] ?? '-') : ($p->nomor_pengajuan ?? '-');

        $u = is_array($p) ? ($p['user'] ?? null) : ($p->user ?? null);
        $userName = is_array($u) ? ($u['name'] ?? 'Poktan') : ($u?->name ?? 'Poktan');

        $kt = is_array($u) 
            ? ($u['kelompok_tani'] ?? $u['kelompokTani'] ?? null) 
            : ($u?->kelompokTani ?? null);

        $namaPoktan = is_array($kt) 
            ? ($kt['nama_poktan'] ?? $userName) 
            : ($kt?->nama_poktan ?? $userName);

        $desa = is_array($kt) ? ($kt['desa'] ?? '-') : ($kt?->desa ?? '-');
        $kec  = is_array($kt) ? ($kt['kecamatan'] ?? '-') : ($kt?->kecamatan ?? '-');

        return [
            'nama'  => $namaPoktan,
            'nomor' => $nomor,
            'desa'  => $desa,
            'kec'   => $kec,
        ];
    };

    $getPoktanName = function($id) use ($kandidatsById, $hasilKalkulasi, $safeExtract) {
        $cand = $kandidatsById[$id] ?? ($hasilKalkulasi['pengajuans'][$id] ?? null);
        return $safeExtract($cand)['nama'];
    };
@endphp

<div class="space-y-6 w-full max-w-full min-w-0">

    <!-- Header Section (Harvesta Executive Style) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-bold bg-celery-cream text-forest-dark border border-bone-slate">
                    <span class="material-symbols-outlined text-sm mr-1 text-forest-dark">functions</span>
                    Engine SPK TOPSIS (6 Langkah Matematis)
                </span>
                <span class="text-xs text-muted-green hidden sm:inline">• Standar Keputusan Ilmiah</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-obsidian tracking-tight font-urbanist">
                Perangkingan Kelayakan Penerima Bantuan Alsintan
            </h1>
            <p class="text-xs sm:text-sm text-muted-green">
                Perhitungan objektif multi-kriteria berbasis jarak Euclidean terhadap solusi ideal positif (<i>A</i><sup>+</sup>) dan negatif (<i>A</i><sup>&minus;</sup>)
            </p>
        </div>

        <!-- Filter Kategori, Tombol Eksekusi, & Reset Simulasi -->
        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('admin.topsis.hitung') }}" method="POST" class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                @csrf
                <select name="jenis_alsintan_id" id="filter_jenis" onchange="window.location.href='{{ route('admin.topsis.index') }}?jenis_id=' + this.value"
                        class="px-4 py-3 bg-bone-canvas/60 border border-bone-slate rounded-2xl text-xs sm:text-sm font-bold text-obsidian focus:ring-2 focus:ring-forest-dark">
                    @foreach($jenisAlsintans as $j)
                        <option value="{{ $j->id }}" {{ $selectedId == $j->id ? 'selected' : '' }}>
                            [{{ $j->kode_alsintan }}] {{ $j->nama_alsintan }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" 
                        class="px-5 py-3 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-extrabold text-xs transition shadow-sm border border-black/10 flex items-center space-x-2 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-base">calculate</span>
                    <span>Hitung TOPSIS Sekarang</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
            </form>

            <form action="{{ route('admin.topsis.reset_simulasi') }}" method="POST" class="inline" onsubmit="return confirm('Reset status semua pengajuan kategori ini ke status Siap Dihitung? Berguna untuk demonstrasi sidang ulang.');">
                @csrf
                <input type="hidden" name="jenis_alsintan_id" value="{{ $selectedId }}">
                <button type="submit" 
                        class="px-3.5 py-3 rounded-full bg-bone-canvas hover:bg-bone-slate text-forest-dark font-bold text-xs transition border border-bone-slate flex items-center space-x-1" 
                        title="Reset status usulan ke Siap Dihitung">
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    <span class="hidden sm:inline">Reset Simulasi</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Info Bobot & Kriteria Acuan (Celery Cream Grid) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-bone-slate/60 pb-3">
            <h3 class="font-extrabold text-xs uppercase tracking-wider text-forest-dark flex items-center font-urbanist">
                <span class="material-symbols-outlined text-base mr-1.5 text-forest-dark">tune</span>
                Kriteria & Bobot yang Digunakan pada Sesi Ini
            </h3>
            <span class="text-xs font-extrabold font-mono px-3 py-1 rounded-full bg-celery-cream text-forest-dark border border-bone-slate">
                Total Bobot: {{ number_format($totalBobot * 100, 1) }}% (Valid)
            </span>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            @foreach($kriterias as $c)
                <div class="p-3.5 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-1">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-mono font-extrabold text-forest-dark bg-white px-2 py-0.5 rounded-lg border border-bone-slate text-[11px]">
                            [{{ $c->kode_kriteria }}]
                        </span>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full {{ $c->tipe === 'benefit' ? 'bg-lime-harvest/40 text-forest-dark border border-lime-harvest' : 'bg-amber-100 text-amber-900 border border-amber-harvest' }}">
                            {{ $c->tipe }}
                        </span>
                    </div>
                    <div class="font-bold text-obsidian text-xs truncate" title="{{ $c->nama_kriteria }}">{{ $c->nama_kriteria }}</div>
                    <div class="text-muted-green text-[11px] pt-0.5">Bobot: <strong class="text-forest-dark">{{ number_format($c->bobot, 4) }}</strong></div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tab / Detail 6 Langkah Kalkulasi TOPSIS -->
    @if($hasilKalkulasi)
        <div class="space-y-6 w-full max-w-full min-w-0">

            <!-- HASIL UTAMA: Tabel Ranking Prioritas (Langkah 6) -->
            <div class="bg-white rounded-3xl border-2 border-forest-dark/30 p-6 sm:p-7 shadow-md space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-bone-slate pb-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-forest-dark flex items-center">
                            <span class="material-symbols-outlined text-amber-harvest text-base mr-1.5">emoji_events</span>
                            Hasil Akhir Langkah 6 (Nilai Preferensi <i>V</i><sub>i</sub>)
                        </span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-obsidian tracking-tight font-urbanist mt-0.5">
                            Tabel Peringkat Prioritas Penerima Bantuan Alsintan
                        </h2>
                    </div>
                    <span class="text-xs text-muted-green font-mono bg-bone-canvas px-3 py-1 rounded-full border border-bone-slate">
                        Batch: {{ substr($hasilKalkulasi['batch_id'] ?? 'auto-gen', 0, 8) }}...
                    </span>
                </div>

                <!-- TAMPILAN KARTU PERINGKAT MOBILE (< md): Bebas Geser Samping -->
                @php
                    $rankingRows = isset($paginatedRanking) ? $paginatedRanking : ($hasilKalkulasi['ranking'] ?? []);
                @endphp
                <div class="space-y-3 md:hidden">
                    @foreach($rankingRows as $row)
                        @php
                            $pId = $row['pengajuan_id'];
                            $p = $kandidatsById[$pId] ?? ($hasilKalkulasi['pengajuans'][$pId] ?? null);
                            $info = $safeExtract($p);
                            $namaPoktan     = $info['nama'];
                            $nomorPengajuan = $info['nomor'];
                            $desa           = $info['desa'];
                            $kecamatan      = $info['kec'];
                            $isTop = $row['ranking'] === 1;
                        @endphp
                        <div class="p-4 rounded-2xl border {{ $isTop ? 'border-lime-harvest bg-lime-harvest/15' : 'border-bone-slate bg-bone-canvas/40' }} space-y-2.5 shadow-2xs">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-full font-black flex items-center justify-center text-xs shrink-0 {{ $isTop ? 'bg-lime-harvest text-obsidian border border-black/10' : 'bg-white text-forest-dark border border-bone-slate' }}">
                                        #{{ $row['ranking'] }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-extrabold text-sm text-obsidian">{{ $namaPoktan }}</div>
                                        <div class="text-[11px] font-mono text-muted-green">{{ $nomorPengajuan }}</div>
                                        <div class="text-[11px] text-muted-green">Desa {{ $desa }}, Kec. {{ $kecamatan }}</div>
                                    </div>
                                </div>
                                @if($isTop)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-lime-harvest text-obsidian border border-lime-harvest-hover shrink-0">PRIORITAS #1</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-forest-dark bg-celery-cream border border-bone-slate shrink-0">Kandidat #{{ $row['ranking'] }}</span>
                                @endif
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-bone-slate/60 text-center tabular-nums">
                                <div class="bg-white p-2 rounded-xl border border-bone-slate">
                                    <div class="text-[10px] text-muted-green"><i>D</i><sup>+</sup></div>
                                    <div class="font-mono text-xs font-bold text-obsidian">{{ number_format($row['jarak_positif'], 4) }}</div>
                                </div>
                                <div class="bg-white p-2 rounded-xl border border-bone-slate">
                                    <div class="text-[10px] text-muted-green"><i>D</i><sup>&minus;</sup></div>
                                    <div class="font-mono text-xs font-bold text-obsidian">{{ number_format($row['jarak_negatif'], 4) }}</div>
                                </div>
                                <div class="{{ $isTop ? 'bg-forest-dark text-lime-harvest' : 'bg-white text-forest-dark border border-bone-slate' }} p-2 rounded-xl">
                                    <div class="text-[10px] opacity-80"><i>V</i><sub>i</sub></div>
                                    <div class="font-mono text-xs font-black">{{ number_format($row['nilai_preferensi'], 4) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- TAMPILAN TABEL DESKTOP (>= md) DENGAN PEMBUNGKUS OVERFLOW AMAN -->
                <div class="hidden md:block w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-bone-slate text-forest-dark uppercase tracking-wider bg-celery-cream/50 font-bold whitespace-nowrap">
                                <th class="py-3.5 px-4 rounded-l-xl text-center whitespace-nowrap">Peringkat</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Kelompok Tani</th>
                                <th class="py-3.5 px-4 whitespace-nowrap">Desa / Kecamatan</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Jarak Ideal Positif (<i>D</i><sup>+</sup>)</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Jarak Ideal Negatif (<i>D</i><sup>&minus;</sup>)</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Nilai Preferensi (<i>V</i><sub>i</sub>)</th>
                                <th class="py-3.5 px-4 rounded-r-xl text-center whitespace-nowrap">Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bone-slate text-obsidian font-semibold">
                            @foreach($rankingRows as $row)
                                @php
                                    $pId = $row['pengajuan_id'];
                                    $p = $kandidatsById[$pId] ?? ($hasilKalkulasi['pengajuans'][$pId] ?? null);
                                    $info = $safeExtract($p);
                                    $namaPoktan     = $info['nama'];
                                    $nomorPengajuan = $info['nomor'];
                                    $desa           = $info['desa'];
                                    $kecamatan      = $info['kec'];
                                    $isTop = $row['ranking'] === 1;
                                @endphp
                                <tr class="{{ $isTop ? 'bg-lime-harvest/15 font-bold' : 'hover:bg-bone-canvas/40' }} transition">
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if($isTop)
                                            <span class="w-8 h-8 mx-auto rounded-full bg-lime-harvest text-obsidian font-black flex items-center justify-center text-xs shadow-sm border border-black/10">
                                                1
                                            </span>
                                        @elseif($row['ranking'] === 2)
                                            <span class="w-8 h-8 mx-auto rounded-full bg-celery-cream text-forest-dark font-black flex items-center justify-center text-xs border border-bone-slate">
                                                2
                                            </span>
                                        @elseif($row['ranking'] === 3)
                                            <span class="w-8 h-8 mx-auto rounded-full bg-amber-100 text-amber-900 font-black flex items-center justify-center text-xs border border-amber-harvest/40">
                                                3
                                            </span>
                                        @else
                                            <span class="text-muted-green font-bold">#{{ $row['ranking'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <div class="text-obsidian font-extrabold text-sm truncate max-w-xs" title="{{ $namaPoktan }}">{{ $namaPoktan }}</div>
                                        <div class="text-[11px] text-muted-green font-mono">{{ $nomorPengajuan }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-muted-green whitespace-nowrap">
                                        <div class="truncate max-w-[200px]" title="Desa {{ $desa }}, Kec. {{ $kecamatan }}">Desa {{ $desa }}, Kec. {{ $kecamatan }}</div>
                                    </td>
                                    <td class="py-4 px-4 text-right font-mono tabular-nums text-muted-green whitespace-nowrap">
                                        {{ number_format($row['jarak_positif'], 6) }}
                                    </td>
                                    <td class="py-4 px-4 text-right font-mono tabular-nums text-muted-green whitespace-nowrap">
                                        {{ number_format($row['jarak_negatif'], 6) }}
                                    </td>
                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        <span class="px-3 py-1.5 rounded-xl font-mono tabular-nums font-extrabold text-sm {{ $isTop ? 'bg-forest-dark text-lime-harvest shadow-sm' : 'bg-bone-canvas text-forest-dark border border-bone-slate' }}">
                                            {{ number_format($row['nilai_preferensi'], 6) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if($isTop)
                                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-lime-harvest text-obsidian border border-lime-harvest-hover shadow-2xs">
                                                PRIORITAS UTAMA
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold text-forest-dark bg-celery-cream border border-bone-slate">
                                                Kandidat #{{ $row['ranking'] }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(isset($paginatedRanking) && $paginatedRanking->hasPages())
                    <div class="mt-4 px-3 sm:px-6 py-3 border-t border-bone-slate bg-white rounded-2xl w-full min-w-0 overflow-hidden shadow-2xs">
                        {{ $paginatedRanking->links('vendor.pagination.custom-tailwind') }}
                    </div>
                @endif
            </div>

            <!-- Detail Perhitungan Matematis (Langkah 1 s/d 5) -->
            @php
                $totalMatriksAlt = count($hasilKalkulasi['matriks']['keputusan'] ?? []);
                $matriksKeputusanPreview = array_slice($hasilKalkulasi['matriks']['keputusan'] ?? [], 0, 50, true);
                $matriksNormalPreview    = array_slice($hasilKalkulasi['matriks']['normal'] ?? [], 0, 50, true);
                $matriksTerbobotPreview  = array_slice($hasilKalkulasi['matriks']['terbobot'] ?? [], 0, 50, true);
            @endphp
            <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-obsidian flex items-center font-urbanist">
                        <span class="material-symbols-outlined text-forest-dark mr-2">menu_book</span>
                        Rincian Matematis 6 Langkah Algoritma TOPSIS
                    </h3>
                    <p class="text-xs text-muted-green mt-0.5">Transparansi rumus langkah demi langkah sesuai dengan pembobotan kriteria dan jarak Euclidean</p>
                    <p class="text-[11px] text-forest-dark font-semibold mt-1 md:hidden">Geser tabel matriks ke samping untuk melihat seluruh kolom C1&ndash;C5.</p>
                </div>

                <!-- 1. Matriks Keputusan (X) -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-forest-dark flex items-center justify-between flex-wrap gap-1">
                        <span>Langkah 1: Matriks Keputusan Asli (X) - [<i>x</i><sub>ij</sub>]</span>
                        @if($totalMatriksAlt > 50)
                            <span class="text-[11px] font-medium text-muted-green lowercase font-sans">(menampilkan 50 teratas dari {{ $totalMatriksAlt }} poktan)</span>
                        @endif
                    </h4>
                    <div class="w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-celery-cream/40 text-forest-dark font-bold border-b border-bone-slate whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Alternatif (Poktan)</th>
                                    @foreach($kriterias as $c)
                                        <th class="py-3 px-3.5 text-right font-mono whitespace-nowrap">{{ $c->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate font-mono text-obsidian">
                                @foreach($matriksKeputusanPreview as $pId => $cols)
                                    <tr class="hover:bg-bone-canvas/30">
                                        <td class="py-2.5 px-3.5 font-sans font-bold text-obsidian whitespace-nowrap">
                                            <div class="truncate max-w-xs" title="{{ $getPoktanName($pId) }}">{{ $getPoktanName($pId) }}</div>
                                        </td>
                                        @foreach($kriterias as $c)
                                            <td class="py-2.5 px-3.5 text-right tabular-nums whitespace-nowrap">{{ number_format($cols[$c->id] ?? 0, 2) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Matriks Ternormalisasi (R) -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-forest-dark flex items-center justify-between flex-wrap gap-1">
                        <span>Langkah 2: Matriks Ternormalisasi Euclidean (R) - [<i>r</i><sub>ij</sub> = <i>x</i><sub>ij</sub> / ||<i>x</i><sub>j</sub>||]</span>
                        @if($totalMatriksAlt > 50)
                            <span class="text-[11px] font-medium text-muted-green lowercase font-sans">(menampilkan 50 teratas dari {{ $totalMatriksAlt }} poktan)</span>
                        @endif
                    </h4>
                    <div class="w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-celery-cream/40 text-forest-dark font-bold border-b border-bone-slate whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Alternatif (Poktan)</th>
                                    @foreach($kriterias as $c)
                                        <th class="py-3 px-3.5 text-right font-mono whitespace-nowrap">{{ $c->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate font-mono text-obsidian">
                                @foreach($matriksNormalPreview as $pId => $cols)
                                    <tr class="hover:bg-bone-canvas/30">
                                        <td class="py-2.5 px-3.5 font-sans font-bold text-obsidian whitespace-nowrap">
                                            <div class="truncate max-w-xs" title="{{ $getPoktanName($pId) }}">{{ $getPoktanName($pId) }}</div>
                                        </td>
                                        @foreach($kriterias as $c)
                                            <td class="py-2.5 px-3.5 text-right tabular-nums whitespace-nowrap">{{ number_format($cols[$c->id] ?? 0, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Matriks Ternormalisasi Terbobot (Y) -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-forest-dark flex items-center justify-between flex-wrap gap-1">
                        <span>Langkah 3: Matriks Ternormalisasi Terbobot (Y) - [<i>y</i><sub>ij</sub> = <i>w</i><sub>j</sub> &times; <i>r</i><sub>ij</sub>]</span>
                        @if($totalMatriksAlt > 50)
                            <span class="text-[11px] font-medium text-muted-green lowercase font-sans">(menampilkan 50 teratas dari {{ $totalMatriksAlt }} poktan)</span>
                        @endif
                    </h4>
                    <div class="w-full overflow-x-auto overflow-y-hidden border border-bone-slate rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-celery-cream/40 text-forest-dark font-bold border-b border-bone-slate whitespace-nowrap">
                                <tr>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Alternatif (Poktan)</th>
                                    @foreach($kriterias as $c)
                                        <th class="py-3 px-3.5 text-right font-mono whitespace-nowrap">{{ $c->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bone-slate font-mono text-obsidian">
                                @foreach($matriksTerbobotPreview as $pId => $cols)
                                    <tr class="hover:bg-bone-canvas/30">
                                        <td class="py-2.5 px-3.5 font-sans font-bold text-obsidian whitespace-nowrap">
                                            <div class="truncate max-w-xs" title="{{ $getPoktanName($pId) }}">{{ $getPoktanName($pId) }}</div>
                                        </td>
                                        @foreach($kriterias as $c)
                                            <td class="py-2.5 px-3.5 text-right tabular-nums whitespace-nowrap">{{ number_format($cols[$c->id] ?? 0, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Solusi Ideal Positif (A+) & Negatif (A-) -->
                <div class="space-y-2.5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-forest-dark">
                        Langkah 4: Solusi Ideal Positif (<i>A</i><sup>+</sup>) dan Negatif (<i>A</i><sup>&minus;</sup>)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl border border-bone-slate bg-celery-cream/30 space-y-2">
                            <span class="font-bold text-xs text-forest-dark block flex items-center">
                                <span class="material-symbols-outlined text-forest-dark text-base mr-1.5">add_circle</span>
                                Solusi Ideal Positif (<i>A</i><sup>+</sup>)
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs tabular-nums">
                                @foreach($kriterias as $c)
                                    <div class="bg-white p-2 rounded-xl border border-bone-slate shadow-2xs">
                                        <div class="text-[10px] text-muted-green font-mono">{{ $c->kode_kriteria }}</div>
                                        <div class="font-mono font-extrabold text-forest-dark text-[11px] mt-0.5 tabular-nums">
                                            {{ number_format($hasilKalkulasi['ideal_positif'][$c->id] ?? 0, 4) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-red-200 bg-red-50/50 space-y-2">
                            <span class="font-bold text-xs text-red-900 block flex items-center">
                                <span class="material-symbols-outlined text-red-700 text-base mr-1.5">remove_circle</span>
                                Solusi Ideal Negatif (<i>A</i><sup>&minus;</sup>)
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs tabular-nums">
                                @foreach($kriterias as $c)
                                    <div class="bg-white p-2 rounded-xl border border-red-200 shadow-2xs">
                                        <div class="text-[10px] text-red-700 font-bold font-mono">{{ $c->kode_kriteria }}</div>
                                        <div class="font-mono font-extrabold text-red-900 text-[11px] mt-0.5 tabular-nums">
                                            {{ number_format($hasilKalkulasi['ideal_negatif'][$c->id] ?? 0, 4) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @else
        <!-- State Awal Sebelum Kalkulasi -->
        <div class="bg-white rounded-3xl border border-bone-slate p-12 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-celery-cream text-forest-dark flex items-center justify-center text-2xl shadow-sm">
                <span class="material-symbols-outlined text-3xl">calculate</span>
            </div>
            <div class="max-w-md mx-auto space-y-2">
                <h3 class="text-lg font-extrabold text-obsidian font-urbanist">Belum Cukup Alternatif untuk Dihitung</h3>
                <p class="text-xs text-muted-green leading-relaxed">
                    Terdapat <strong>{{ $kandidats->count() }} kelompok tani</strong> yang sudah dinilai untuk kategori alat ini. Metode TOPSIS memerlukan minimal 2 alternatif untuk membandingkan jarak terhadap solusi ideal.
                </p>
                <div class="pt-3 flex items-center justify-center gap-2">
                    <a href="{{ route('admin.pengajuan.index') }}" 
                       class="px-5 py-2.5 rounded-full bg-forest-dark text-white text-xs font-bold hover:bg-forest-deep transition flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">checklist</span>
                        <span>Input Nilai Poktan Lain</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
