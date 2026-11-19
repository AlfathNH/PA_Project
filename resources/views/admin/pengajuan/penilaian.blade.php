@extends('layouts.app')

@section('title', 'Penilaian Kriteria TOPSIS - ' . $pengajuan->nomor_pengajuan)

@section('content')
<div class="max-w-3xl mx-auto py-4 space-y-6">

    <!-- Header & Info Poktan (Harvesta Style) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-bone-slate/60 pb-3">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-extrabold bg-celery-cream text-forest-dark border border-bone-slate">
                    {{ $pengajuan->nomor_pengajuan }}
                </span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-obsidian tracking-tight font-urbanist mt-1.5">
                    Input Penilaian Kriteria: {{ $pengajuan->user?->kelompokTani?->nama_poktan }}
                </h1>
            </div>
            <a href="{{ route('admin.pengajuan.index') }}" 
               class="px-4 py-2 rounded-full bg-bone-canvas hover:bg-bone-slate text-xs font-bold text-forest-dark transition flex items-center border border-bone-slate">
                <span class="material-symbols-outlined text-sm mr-1">arrow_back</span> Kembali
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-bone-canvas/50 p-4 rounded-2xl border border-bone-slate">
            <div>
                <span class="text-muted-green block text-[10px] font-bold uppercase">Jenis Alat</span>
                <strong class="text-obsidian font-extrabold">{{ $pengajuan->jenisAlsintan?->nama_alsintan }}</strong>
            </div>
            <div>
                <span class="text-muted-green block text-[10px] font-bold uppercase">Wilayah</span>
                <strong class="text-obsidian font-extrabold">Kec. {{ $pengajuan->user?->kelompokTani?->kecamatan }}</strong>
            </div>
            <div>
                <span class="text-muted-green block text-[10px] font-bold uppercase">Luas Lahan</span>
                <strong class="text-obsidian font-extrabold tabular-nums">{{ $pengajuan->user?->kelompokTani?->luas_lahan_total }} Ha</strong>
            </div>
            <div>
                <span class="text-muted-green block text-[10px] font-bold uppercase">Jumlah Anggota</span>
                <strong class="text-obsidian font-extrabold tabular-nums">{{ $pengajuan->user?->kelompokTani?->jumlah_anggota }} Petani</strong>
            </div>
        </div>
    </div>

    <!-- Form Input Nilai Kriteria (x_ij) -->
    <div class="bg-white rounded-3xl border border-bone-slate p-6 sm:p-7 shadow-sm space-y-6">
        <div class="border-b border-bone-slate/60 pb-3">
            <h2 class="font-extrabold text-obsidian text-sm uppercase tracking-wider flex items-center font-urbanist">
                <span class="material-symbols-outlined text-forest-dark mr-1.5">calculate</span>
                Nilai Kriteria Matriks Keputusan (<i>x</i><sub>ij</sub>)
            </h2>
            <p class="text-xs text-muted-green mt-0.5">
                Isi skor riil atau skala penilaian untuk masing-masing kriteria. Data ini akan diproses pada Langkah 1 normalisasi TOPSIS.
            </p>
        </div>

        <form action="{{ route('admin.pengajuan.penilaian.simpan', $pengajuan->id) }}" method="POST" class="space-y-4">
            @csrf

            @foreach($kriterias as $kriteria)
                @php
                    $valDefault = $nilaiEksisting[$kriteria->id] ?? match($kriteria->kode_kriteria) {
                        'C1' => $pengajuan->user?->kelompokTani?->luas_lahan_total ?? 25.0,
                        'C2' => 6.0,
                        'C3' => $pengajuan->jumlah_alsintan_eksisting ?? 0,
                        'C4' => $pengajuan->user?->kelompokTani?->jumlah_anggota ?? 30,
                        'C5' => 4,
                        default => 0,
                    };

                    $inputConfig = match($kriteria->kode_kriteria) {
                        'C1' => [
                            'step' => '0.1', 
                            'min' => '0', 
                            'unit' => 'Hektar (Ha)', 
                            'helper' => 'Data riil luas hamparan Poktan. Boleh desimal (contoh: 38.5 Ha).'
                        ],
                        'C2' => [
                            'step' => '0.01', 
                            'min' => '0', 
                            'unit' => 'Ton / Ha', 
                            'helper' => 'Produktivitas panen rata-rata. Boleh desimal (contoh: 6.25 Ton/Ha).'
                        ],
                        'C3' => [
                            'step' => '1', 
                            'min' => '0', 
                            'unit' => 'Unit Mesin (Bulat)', 
                            'helper' => 'Wajib bilangan bulat utuh (contoh: 0, 1, 2). Terisi otomatis dari form usulan Poktan: ' . ($pengajuan->jumlah_alsintan_eksisting ?? 0) . ' unit.'
                        ],
                        'C4' => [
                            'step' => '1', 
                            'min' => '1', 
                            'unit' => 'Orang Petani (Bulat)', 
                            'helper' => 'Wajib bilangan bulat utuh (contoh: 30, 45 orang).'
                        ],
                        'C5' => [
                            'step' => '1', 
                            'min' => '1', 
                            'max' => '5', 
                            'unit' => 'Skor (1 - 5)', 
                            'helper' => 'Skala Likert kesiapan gudang & operator: 1 (Sangat Kurang), 2 (Kurang), 3 (Cukup), 4 (Baik/Siap), 5 (Sangat Siap).'
                        ],
                        default => [
                            'step' => '0.01', 
                            'min' => '0', 
                            'unit' => 'Nilai', 
                            'helper' => ''
                        ],
                    };
                @endphp
                <div class="p-4 sm:p-5 rounded-2xl border border-bone-slate bg-bone-canvas/40 space-y-2.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center space-x-2">
                            <span class="font-mono text-xs font-extrabold text-forest-dark bg-white px-2.5 py-1 rounded-lg border border-bone-slate">
                                {{ $kriteria->kode_kriteria }}
                            </span>
                            <span class="font-extrabold text-sm text-obsidian">{{ $kriteria->nama_kriteria }}</span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="text-muted-green">Bobot: <strong class="text-forest-dark">{{ number_format($kriteria->bobot * 100, 1) }}%</strong></span>
                            <span class="px-2.5 py-0.5 rounded-full font-extrabold text-[10px] {{ $kriteria->tipe === 'benefit' ? 'bg-lime-harvest/40 text-forest-dark border border-lime-harvest' : 'bg-amber-100 text-amber-900 border border-amber-harvest' }}">
                                {{ strtoupper($kriteria->tipe) }}
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-muted-green">{{ $kriteria->deskripsi }}</p>

                    <div class="space-y-1 pt-1">
                        <div class="flex items-center space-x-3">
                            <input type="number" 
                                   step="{{ $inputConfig['step'] }}" 
                                   min="{{ $inputConfig['min'] }}" 
                                   @if(isset($inputConfig['max'])) max="{{ $inputConfig['max'] }}" @endif
                                   name="nilai[{{ $kriteria->id }}]" 
                                   value="{{ old('nilai.' . $kriteria->id, $valDefault) }}" required
                                   class="no-spin tabular-nums w-full sm:w-52 px-4 py-2.5 bg-white border border-bone-slate rounded-xl text-sm font-mono font-extrabold text-obsidian focus:ring-2 focus:ring-forest-dark transition">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-celery-cream text-forest-dark border border-bone-slate">
                                {{ $inputConfig['unit'] }}
                            </span>
                        </div>
                        <p class="text-[11px] text-muted-green italic">{{ $inputConfig['helper'] }}</p>
                    </div>
                </div>
            @endforeach

            <div class="pt-4 border-t border-bone-slate flex items-center justify-between">
                <a href="{{ route('admin.pengajuan.index') }}" class="text-xs font-bold text-muted-green hover:text-obsidian">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-3 rounded-full bg-lime-harvest hover:bg-lime-harvest-hover text-obsidian font-extrabold text-xs transition shadow-sm border border-black/10 flex items-center space-x-2 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-sm">save</span>
                    <span>Simpan Skor Penilaian & Tandai Siap Dihitung</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
