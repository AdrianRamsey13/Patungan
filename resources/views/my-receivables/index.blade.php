<x-app-layout>
<div class="px-4 md:px-8 py-6 max-w-4xl mx-auto">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">{{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-extrabold text-ink text-2xl" style="letter-spacing:-.02em">Piutang Saya</h1>
            <p class="text-ink-soft text-sm font-medium mt-1">Semua yang orang lain belum bayar ke kamu.</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink-soft hover:bg-cream transition-colors flex-shrink-0"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
    </div>

    @if ($tableRows->isEmpty())
        <div class="bg-white rounded-[22px] px-6 py-14 text-center"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
            <div class="text-5xl mb-4">✅</div>
            <div class="font-extrabold text-ink text-xl mb-2">Semua sudah lunas!</div>
            <p class="text-ink-soft text-sm font-medium max-w-xs mx-auto">Tidak ada yang masih berhutang ke kamu saat ini.</p>
        </div>
    @else

        {{-- Total summary --}}
        <div class="bg-mint-soft rounded-[18px] px-5 py-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[12px] bg-mint flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-mint-ink">Total yang belum kamu terima</div>
                    <div class="text-muted text-xs font-semibold">{{ $tableRows->count() }} tagihan · {{ $events->count() }} event</div>
                </div>
            </div>
            <div class="pt-num font-extrabold text-ink text-xl">
                Rp {{ number_format($totalReceive, 0, ',', '.') }}
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
        <h2 class="font-extrabold text-ink mb-3" style="font-size:15px">Rincian Piutang</h2>
        <div class="bg-white rounded-[18px] overflow-hidden"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Table header (desktop) --}}
            <div class="hidden md:grid px-5 py-3 text-xs font-extrabold text-muted border-b border-line"
                 style="grid-template-columns:1fr 1fr 1fr 120px 110px">
                <span>Event</span>
                <span>Dari siapa</span>
                <span>Untuk</span>
                <span class="text-right">Jumlah</span>
                <span class="text-right">Status</span>
            </div>

            @foreach ($tableRows as $i => $row)
                <div class="{{ $i > 0 ? 'border-t border-line' : '' }} px-5 py-4">

                    {{-- Mobile --}}
                    <div class="md:hidden flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-ink text-sm truncate">{{ $row['event']->name }}</div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <x-pt.avatar :name="$row['debtor_name']" size="xs" />
                                <span class="text-muted text-xs font-semibold truncate">
                                    {{ $row['debtor_name'] }}
                                    @if ($row['is_guest'])
                                        <span class="text-[10px] bg-line text-muted px-1.5 rounded-pill">tamu</span>
                                    @endif
                                    · {{ $row['expense']->description }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="pt-num font-extrabold text-mint-ink text-base">
                                Rp {{ number_format($row['amount'], 0, ',', '.') }}
                            </span>
                            <x-pt.status-pill state="belum" size="sm">Belum bayar</x-pt.status-pill>
                        </div>
                    </div>

                    {{-- Desktop --}}
                    <div class="hidden md:grid items-center gap-3"
                         style="grid-template-columns:1fr 1fr 1fr 120px 110px">
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
                            <x-pt.avatar :name="$row['debtor_name']" size="xs" />
                            <span class="font-semibold text-ink text-sm truncate">
                                {{ $row['debtor_name'] }}
                                @if ($row['is_guest'])
                                    <span class="text-[10px] bg-line text-muted px-1.5 py-0.5 rounded-pill ml-1">tamu</span>
                                @endif
                            </span>
                        </div>
                        <div class="text-ink-soft text-sm font-semibold truncate">
                            {{ $row['expense']->description }}
                        </div>
                        <div class="pt-num font-extrabold text-mint-ink text-right">
                            Rp {{ number_format($row['amount'], 0, ',', '.') }}
                        </div>
                        <div class="flex justify-end">
                            @if ($row['debtor_member'] && $row['creditor_member'] && $row['event']->status === 'open' && $row['is_guest'])
                                {{-- Guest debtor: creator confirm --}}
                                <form method="POST"
                                      action="{{ route('events.pay', [$row['event'], $row['debtor_member'], $row['creditor_member']]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-coral text-xs font-bold hover:underline">
                                        Konfirmasi
                                    </button>
                                </form>
                            @else
                                <x-pt.status-pill state="belum" size="sm">Belum bayar</x-pt.status-pill>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach

            {{-- Total row --}}
            <div class="border-t-2 border-line px-5 py-4 flex items-center justify-between bg-mint-soft">
                <span class="font-extrabold text-mint-ink text-sm">Total piutang</span>
                <span class="pt-num font-extrabold text-ink text-lg">
                    Rp {{ number_format($totalReceive, 0, ',', '.') }}
                </span>
            </div>
        </div>

    @endif
</div>
</x-app-layout>
