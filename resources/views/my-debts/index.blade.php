<x-app-layout>
<div class="px-4 md:px-8 py-6 max-w-4xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">{{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-extrabold text-ink text-2xl" style="letter-spacing:-.02em">Tagihan Saya</h1>
            <p class="text-ink-soft text-sm font-medium mt-1">Semua yang perlu kamu bayar ke orang lain.</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink-soft hover:bg-cream transition-colors flex-shrink-0"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
    </div>

    @if ($tableRows->isEmpty())
        {{-- Empty state --}}
        <div class="bg-white rounded-[22px] px-6 py-14 text-center"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
            <div class="text-5xl mb-4">🎉</div>
            <div class="font-extrabold text-ink text-xl mb-2">Tidak ada tagihan!</div>
            <p class="text-ink-soft text-sm font-medium max-w-xs mx-auto">Kamu tidak punya utang ke siapapun saat ini. Mantap!</p>
        </div>
    @else

        {{-- Total summary --}}
        <div class="bg-amber-soft rounded-[18px] px-5 py-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[12px] bg-amber flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.4" stroke-linecap="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-amber-ink">Total yang harus kamu bayar</div>
                    <div class="text-muted text-xs font-semibold">{{ $tableRows->count() }} tagihan · {{ $events->count() }} event</div>
                </div>
            </div>
            <div class="pt-num font-extrabold text-ink text-xl">
                Rp {{ number_format($totalOwe, 0, ',', '.') }}
            </div>
        </div>

        {{-- Event cards --}}
        <h2 class="font-extrabold text-ink mb-3" style="font-size:15px">Event Terkait</h2>
        <div class="grid grid-cols-2 gap-3 md:gap-4 mb-8">
            @foreach ($events as $event)
                <x-pt.event-card :event="$event" :userId="Auth::id()" />
            @endforeach
        </div>

        {{-- Detail table --}}
        <h2 class="font-extrabold text-ink mb-3" style="font-size:15px">Rincian Tagihan</h2>
        <div class="bg-white rounded-[18px] overflow-hidden"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Table header --}}
            <div class="hidden md:grid px-5 py-3 text-xs font-extrabold text-muted border-b border-line"
                 style="grid-template-columns:1fr 1fr 1fr 120px 120px">
                <span>Event</span>
                <span>Bayar ke</span>
                <span>Untuk</span>
                <span class="text-right">Jumlah</span>
                <span class="text-right">Aksi</span>
            </div>

            @foreach ($tableRows as $i => $row)
                <div class="{{ $i > 0 ? 'border-t border-line' : '' }} px-5 py-4">

                    {{-- Mobile layout: stacked --}}
                    <div class="md:hidden flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-bold text-ink text-sm truncate">{{ $row['event']->name }}</div>
                                <div class="text-muted text-xs font-semibold mt-0.5">
                                    {{ $row['expense']->description }} · ke {{ $row['creditor_name'] }}
                                </div>
                            </div>
                            <div class="pt-num font-extrabold text-amber-ink text-base flex-shrink-0">
                                Rp {{ number_format($row['amount'], 0, ',', '.') }}
                            </div>
                        </div>
                        @if ($row['debtor_member'] && $row['creditor_member'] && $row['event']->status === 'open')
                            <form method="POST"
                                  action="{{ route('events.pay', [$row['event'], $row['debtor_member'], $row['creditor_member']]) }}">
                                @csrf
                                <button type="submit"
                                        class="pt-btn pt-btn-primary w-full justify-center rounded-[11px] py-2.5 text-sm font-bold">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                                    Tandai Lunas
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Desktop layout: grid --}}
                    <div class="hidden md:grid items-center gap-3"
                         style="grid-template-columns:1fr 1fr 1fr 120px 120px">
                        <div class="min-w-0">
                            <a href="{{ route('events.show', $row['event']) }}"
                               class="font-bold text-ink text-sm hover:text-coral transition-colors truncate block">
                                {{ $row['event']->name }}
                            </a>
                            @if ($row['event']->date_start)
                                <div class="text-muted text-xs font-semibold">{{ $row['event']->date_start->format('d M Y') }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 min-w-0">
                            <x-pt.avatar :name="$row['creditor_name']" size="xs" />
                            <span class="font-semibold text-ink text-sm truncate">{{ $row['creditor_name'] }}</span>
                        </div>
                        <div class="text-ink-soft text-sm font-semibold truncate">
                            {{ $row['expense']->description }}
                        </div>
                        <div class="pt-num font-extrabold text-amber-ink text-right">
                            Rp {{ number_format($row['amount'], 0, ',', '.') }}
                        </div>
                        <div class="flex justify-end">
                            @if ($row['debtor_member'] && $row['creditor_member'] && $row['event']->status === 'open')
                                <form method="POST"
                                      action="{{ route('events.pay', [$row['event'], $row['debtor_member'], $row['creditor_member']]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="pt-btn pt-btn-primary px-3 py-1.5 rounded-[10px] text-xs font-bold">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                                        Tandai Lunas
                                    </button>
                                </form>
                            @else
                                <x-pt.status-pill state="lunas" size="sm" />
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach

            {{-- Total row --}}
            <div class="border-t-2 border-line px-5 py-4 flex items-center justify-between bg-amber-soft">
                <span class="font-extrabold text-amber-ink text-sm">Total tagihan</span>
                <span class="pt-num font-extrabold text-ink text-lg">
                    Rp {{ number_format($totalOwe, 0, ',', '.') }}
                </span>
            </div>
        </div>

    @endif
</div>
</x-app-layout>
