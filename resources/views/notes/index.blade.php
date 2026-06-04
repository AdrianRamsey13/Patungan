<x-app-layout>
<div class="px-6 md:px-8 py-6 max-w-4xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">{{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-end justify-between mb-6">
        <div>
            <h1 class="font-extrabold text-ink text-2xl" style="letter-spacing:-.02em">Notes</h1>
            <p class="text-ink-soft text-sm font-medium mt-1">Catatan hutang & piutang pribadi kamu.</p>
        </div>
        <a href="{{ route('notes.create') }}"
           class="pt-btn pt-btn-primary px-4 py-2.5 rounded-[13px] text-sm font-bold">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Buat Note
        </a>
    </div>

    @if ($notes->isEmpty())
        <div class="bg-white rounded-[22px] px-6 py-14 text-center"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
            <div class="w-14 h-14 rounded-[18px] bg-grape-soft flex items-center justify-center mx-auto mb-4">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#7B61FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div class="font-extrabold text-ink text-lg mb-2">Belum ada notes</div>
            <p class="text-ink-soft text-sm font-medium max-w-xs mx-auto mb-5 leading-relaxed">
                Catat hutang & piutang kamu di sini tanpa perlu buka spreadsheet.
            </p>
            <a href="{{ route('notes.create') }}"
               class="pt-btn pt-btn-primary px-5 py-3 rounded-[13px] text-sm font-bold inline-flex">
                Buat Note Pertama
            </a>
        </div>

    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ($notes as $note)
                @php
                    $balance   = (int)($note->total_added ?? 0) - (int)($note->total_paid ?? 0);
                    $isHutang  = $note->type === 'hutang';
                    $typeColor = $isHutang ? ['bg' => '#FCEFD2', 'fg' => '#C77C05'] : ['bg' => '#DDF6EC', 'fg' => '#0B8A65'];
                    $balanceColor = $balance > 0 ? ($isHutang ? 'text-amber-ink' : 'text-mint-ink') : 'text-muted';
                @endphp
                <a href="{{ route('notes.show', $note) }}"
                   class="pt-lift bg-white rounded-[18px] px-5 py-4 flex items-center gap-4 no-underline"
                   style="box-shadow:0 1px 3px rgba(36,29,22,.05),0 6px 16px rgba(36,29,22,.06)">

                    {{-- Icon --}}
                    <div class="w-11 h-11 rounded-[14px] flex items-center justify-center flex-shrink-0"
                         style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['fg'] }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-extrabold text-ink text-sm">{{ $note->name }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-pill"
                                  style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['fg'] }}">
                                {{ $note->typeLabel() }}
                            </span>
                        </div>
                        @if ($note->description)
                            <div class="text-muted text-xs font-semibold mt-0.5 truncate">{{ $note->description }}</div>
                        @endif
                        <div class="text-muted text-xs font-semibold mt-1">{{ $note->entries_count }} transaksi</div>
                    </div>

                    {{-- Balance --}}
                    <div class="text-right flex-shrink-0">
                        <div class="text-muted text-xs font-semibold mb-0.5">Sisa</div>
                        <div class="pt-num font-extrabold {{ $balanceColor }} text-base">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </div>
                        @if ($balance === 0)
                            <div class="text-xs font-bold text-mint-ink mt-0.5">Lunas ✓</div>
                        @endif
                    </div>

                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A39A8F" stroke-width="2" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    @endif

</div>
</x-app-layout>
