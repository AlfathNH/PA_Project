@if ($paginator->hasPages())
    @php
        $pageName = $paginator->getPageName();
        $queryParams = request()->except($pageName);
        $isLengthAware = method_exists($paginator, 'total') && method_exists($paginator, 'lastPage');

        if ($isLengthAware) {
            // Batasi rentang angka yang tampil di sisi kiri/kanan halaman aktif (maksimal 3 angka di sekitar halaman aktif)
            $window = \Illuminate\Pagination\UrlWindow::make($paginator->onEachSide(1));
            $elements = array_filter([
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ]);
        } else {
            $elements = [];
        }
    @endphp

    <nav role="navigation" aria-label="Navigasi Halaman" class="w-full min-w-0 overflow-hidden">
        {{-- ========================================================= --}}
        {{-- TAMPILAN MOBILE: 100% PAS DI LAYAR HP (ANTI-GESER)        --}}
        {{-- ========================================================= --}}
        <div class="sm:hidden space-y-2.5 w-full min-w-0">
            <div class="flex items-center justify-between gap-1.5 w-full min-w-0">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-bold text-muted-green/40 bg-bone-canvas/60 border border-bone-slate/70 rounded-xl cursor-not-allowed select-none min-h-[36px] shrink-0" aria-disabled="true">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                        <span class="text-[11px]">Seb</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-bold text-forest-dark bg-white border border-bone-slate rounded-xl hover:bg-bone-canvas active:bg-bone-slate transition shadow-2xs min-h-[36px] shrink-0" title="Halaman Sebelumnya">
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                        <span class="text-[11px]">Seb</span>
                    </a>
                @endif

                {{-- Keterangan Halaman Aktif (Fleksibel & Otomatis Pas di Layar HP) --}}
                <div class="flex-1 min-w-0 px-2 py-1 bg-celery-cream/80 text-forest-dark border border-bone-slate rounded-xl text-xs font-bold text-center truncate shadow-2xs">
                    Hal <span class="text-obsidian tabular-nums font-black">{{ $paginator->currentPage() }}</span>
                    @if ($isLengthAware)
                        / <span class="tabular-nums font-black">{{ $paginator->lastPage() }}</span>
                    @endif
                </div>

                {{-- Tombol Selanjutnya --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-bold text-forest-dark bg-white border border-bone-slate rounded-xl hover:bg-bone-canvas active:bg-bone-slate transition shadow-2xs min-h-[36px] shrink-0" title="Halaman Selanjutnya">
                        <span class="text-[11px]">Sel</span>
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 text-xs font-bold text-muted-green/40 bg-bone-canvas/60 border border-bone-slate/70 rounded-xl cursor-not-allowed select-none min-h-[36px] shrink-0" aria-disabled="true">
                        <span class="text-[11px]">Sel</span>
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </span>
                @endif
            </div>

            {{-- Fitur Jump to Page (Khusus Layar Mobile: Mungil & Tanpa Loader Blocking) --}}
            @if ($isLengthAware && $paginator->lastPage() > 1)
                <div class="flex items-center justify-center pt-2 border-t border-bone-slate/60 w-full min-w-0">
                    <form method="GET" action="{{ url()->current() }}" class="no-loader flex items-center justify-center gap-1.5 w-full max-w-xs">
                        @foreach ($queryParams as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $itemKey => $itemValue)
                                    <input type="hidden" name="{{ $key }}[{{ $itemKey }}]" value="{{ $itemValue }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <label for="jump-mob-{{ $pageName }}" class="text-[11px] font-semibold text-muted-green whitespace-nowrap">
                            Ke hal:
                        </label>
                        <input type="number" 
                               id="jump-mob-{{ $pageName }}"
                               name="{{ $pageName }}" 
                               min="1" 
                               max="{{ $paginator->lastPage() }}" 
                               value="{{ $paginator->currentPage() }}" 
                               class="w-14 px-1.5 py-1 text-xs font-bold text-center text-obsidian bg-white border border-bone-slate rounded-lg focus:outline-none focus:ring-2 focus:ring-forest-dark focus:border-forest-dark tabular-nums shadow-2xs [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                               aria-label="Nomor halaman tujuan"
                               required>
                        <button type="submit" 
                                class="px-2.5 py-1 min-h-[30px] text-xs font-black text-obsidian bg-lime-harvest hover:bg-lime-harvest-hover active:bg-lime-500 border border-black/10 rounded-lg shadow-2xs transition cursor-pointer shrink-0">
                            Pergi
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- ========================================================= --}}
        {{-- TAMPILAN DESKTOP: ELEGAN, RINGKAS & JUMP TO PAGE FORM      --}}
        {{-- ========================================================= --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4 w-full">
            {{-- Keterangan Total Data --}}
            <div class="text-xs text-muted-green font-medium whitespace-nowrap">
                @if ($isLengthAware)
                    Menampilkan <strong class="text-obsidian tabular-nums">{{ $paginator->firstItem() ?? 0 }}</strong>
                    - <strong class="text-obsidian tabular-nums">{{ $paginator->lastItem() ?? 0 }}</strong>
                    dari <strong class="text-obsidian tabular-nums">{{ $paginator->total() }}</strong> usulan
                @else
                    Halaman <strong class="text-obsidian tabular-nums">{{ $paginator->currentPage() }}</strong>
                @endif
            </div>

            {{-- Sisi Kanan: Deretan Angka Paginasi + Input Jump to Page --}}
            <div class="flex items-center gap-3 shrink-0">
                {{-- Deretan Tombol Angka Paginasi --}}
                <div class="inline-flex items-center -space-x-px rounded-xl border border-bone-slate bg-white shadow-2xs overflow-hidden">
                    {{-- Tombol Sebelumnya (Desktop) --}}
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center justify-center w-8 h-8 text-muted-green/40 bg-bone-canvas/40 cursor-not-allowed select-none border-r border-bone-slate" aria-disabled="true" title="Halaman Sebelumnya">
                            <span class="material-symbols-outlined text-base">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-8 h-8 text-forest-dark hover:bg-bone-canvas active:bg-bone-slate border-r border-bone-slate transition-colors" title="Halaman Sebelumnya">
                            <span class="material-symbols-outlined text-base">chevron_left</span>
                        </a>
                    @endif

                    {{-- Angka Halaman & Tiga Titik --}}
                    @foreach ($elements as $element)
                        {{-- Separator Tiga Titik --}}
                        @if (is_string($element))
                            <span class="inline-flex items-center justify-center px-2.5 h-8 text-xs font-black text-muted-green/60 bg-bone-canvas/40 border-r border-bone-slate select-none">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Nomor Halaman --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="inline-flex items-center justify-center min-w-8 px-2.5 h-8 text-xs font-black bg-forest-dark text-lime-harvest border-r border-forest-dark tabular-nums select-none shadow-inner" aria-current="page" title="Halaman Saat Ini ({{ $page }})">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-8 px-2.5 h-8 text-xs font-bold text-obsidian hover:bg-bone-canvas/80 border-r border-bone-slate transition-colors tabular-nums" title="Halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Tombol Selanjutnya (Desktop) --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-8 h-8 text-forest-dark hover:bg-bone-canvas active:bg-bone-slate transition-colors" title="Halaman Selanjutnya">
                            <span class="material-symbols-outlined text-base">chevron_right</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-8 h-8 text-muted-green/40 bg-bone-canvas/40 cursor-not-allowed select-none" aria-disabled="true" title="Halaman Selanjutnya">
                            <span class="material-symbols-outlined text-base">chevron_right</span>
                        </span>
                    @endif
                </div>

                {{-- Fitur Jump to Page (Khusus Layar Desktop) --}}
                @if ($isLengthAware && $paginator->lastPage() > 1)
                    <form method="GET" action="{{ url()->current() }}" class="no-loader flex items-center gap-1.5 shrink-0 pl-2.5 border-l border-bone-slate/80">
                        @foreach ($queryParams as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $itemKey => $itemValue)
                                    <input type="hidden" name="{{ $key }}[{{ $itemKey }}]" value="{{ $itemValue }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <label for="jump-desk-{{ $pageName }}" class="text-[11px] font-semibold text-muted-green whitespace-nowrap">
                            Ke Hal:
                        </label>
                        <input type="number" 
                               id="jump-desk-{{ $pageName }}"
                               name="{{ $pageName }}" 
                               min="1" 
                               max="{{ $paginator->lastPage() }}" 
                               placeholder="{{ $paginator->currentPage() }}" 
                               class="w-14 px-2 py-1 text-xs font-bold text-center text-obsidian bg-white border border-bone-slate rounded-lg focus:outline-none focus:ring-2 focus:ring-forest-dark focus:border-forest-dark tabular-nums shadow-2xs [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                               aria-label="Lompat ke halaman tujuan"
                               required>
                        <button type="submit" 
                                class="px-2.5 py-1 text-xs font-black text-obsidian bg-lime-harvest hover:bg-lime-harvest-hover active:bg-lime-500 border border-black/10 rounded-lg shadow-2xs transition cursor-pointer shrink-0">
                            Pergi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </nav>
@endif
