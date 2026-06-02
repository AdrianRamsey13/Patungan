<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-mint-soft text-mint-ink font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 5l-7 7 7 7"/>
            </svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Detail Event</span>
        <div class="w-10"></div>{{-- spacer --}}
    </div>

    <div class="bg-white rounded-[22px] overflow-hidden" style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

        {{-- Hero: icon + nama + tanggal --}}
        <div class="flex gap-4 items-start p-6 pb-5">
            <x-pt.cat-icon :cat="$event->categoryIcon()" :accent="$event->accentColor()" :size="56" />
            <div class="flex-1 min-w-0">
                <h1 class="font-extrabold text-ink text-xl leading-tight" style="letter-spacing:-.01em">
                    {{ $event->name }}
                </h1>
                @if ($event->description)
                    <p class="text-ink-soft text-sm mt-1 leading-relaxed">{{ $event->description }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    @if ($event->date_start)
                        <span class="flex items-center gap-1.5 text-muted text-xs font-semibold">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/>
                            </svg>
                            {{ $event->date_start->translatedFormat('d M Y') }}
                            @if ($event->date_end && !$event->date_start->eq($event->date_end))
                                – {{ $event->date_end->translatedFormat('d M Y') }}
                            @endif
                        </span>
                    @endif
                    @if ($event->status === 'closed')
                        <x-pt.status-pill state="selesai" size="sm" />
                    @endif
                </div>
            </div>
        </div>

        <div class="px-6 pb-6 flex flex-col gap-4">

            {{-- Split summary card --}}
            @php
                $accents = [
                    'sky'   => ['soft' => '#DDF0FD', 'ink' => '#1184D6'],
                    'coral' => ['soft' => '#FFE7DF', 'ink' => '#E5512F'],
                    'amber' => ['soft' => '#FCEFD2', 'ink' => '#C77C05'],
                    'grape' => ['soft' => '#ECE7FF', 'ink' => '#5E43E8'],
                    'mint'  => ['soft' => '#DDF6EC', 'ink' => '#0B8A65'],
                ];
                $a = $accents[$event->accentColor()] ?? $accents['coral'];
                $totalAmount = $event->expenses->sum('amount');
                $shareAmount = $memberCount > 0 ? (int) round($totalAmount / $memberCount) : 0;
            @endphp

            <div class="flex items-center justify-between gap-4 rounded-[18px] px-5 py-4"
                 style="background:{{ $a['soft'] }}">
                <div>
                    <div class="text-xs font-bold mb-1" style="color:{{ $a['ink'] }};opacity:.8">Total biaya</div>
                    <div class="pt-num font-bold text-ink text-xl">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                    <div class="flex items-center gap-1.5 mt-1 text-ink-soft text-xs font-semibold">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16 5.2a3.2 3.2 0 0 1 0 6M17.5 19a5.5 5.5 0 0 0-3-4.9"/>
                        </svg>
                        Dibagi rata {{ $memberCount }} orang
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold mb-1" style="color:{{ $a['ink'] }};opacity:.8">Per orang</div>
                    <div class="pt-num font-extrabold text-2xl" style="color:{{ $a['ink'] }};line-height:1.05">
                        Rp {{ number_format($shareAmount, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Status band: nalangin / utang / lunas --}}
            @if ($iAmPayer)
                {{-- Nalangin: bakal terima --}}
                <div class="flex items-center justify-between gap-3 rounded-[16px] px-4 py-3 bg-mint-soft">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-mint text-white flex items-center justify-center flex-shrink-0">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-mint-ink">Kamu yang nalangin — bakal terima</div>
                            <div class="pt-num font-extrabold text-ink text-lg">Rp {{ number_format($toReceive, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <button class="pt-btn pt-btn-ghost px-4 py-2.5 rounded-xl text-sm font-bold">Ingatkan</button>
                </div>
            @elseif ($mySplit && $mySplit->is_paid)
                {{-- Sudah lunas --}}
                <div class="flex items-center gap-3 rounded-[16px] px-4 py-3 bg-mint-soft">
                    <div class="w-10 h-10 rounded-xl bg-mint text-white flex items-center justify-center flex-shrink-0">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                    </div>
                    <div>
                        <div class="font-extrabold text-mint-ink text-sm">Kamu sudah bayar</div>
                        <div class="text-ink-soft text-xs font-semibold">
                            Rp {{ number_format($mySplit->amount_owed, 0, ',', '.') }} ke {{ $event->creator->name }}
                        </div>
                    </div>
                </div>
            @elseif ($iOwe > 0)
                {{-- Masih utang --}}
                <div class="flex items-center justify-between gap-3 rounded-[16px] px-4 py-3 bg-amber-soft">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber text-white flex items-center justify-center flex-shrink-0">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-amber-ink">Kamu perlu bayar ke {{ $event->creator->name }}</div>
                            <div class="pt-num font-extrabold text-ink text-lg">Rp {{ number_format($iOwe, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @if ($mySplit)
                        <form method="POST" action="{{ route('expense-splits.pay', $mySplit) }}">
                            @csrf
                            <button type="submit"
                                    class="pt-btn pt-btn-primary flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                                Tandai Lunas
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            {{-- Progress --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-extrabold text-ink text-sm">Status pembayaran</span>
                    <span class="text-ink-soft text-xs font-bold">
                        <b class="text-mint-ink">{{ $paidCount }}</b> dari {{ $memberCount }} lunas
                    </span>
                </div>
                <x-pt.progress-bar :paid="$paidCount" :total="$memberCount" accent="mint" :height="9" />
            </div>

            {{-- Member list --}}
            <div class="flex flex-col">
                @foreach ($event->members as $member)
                    @php
                        $isCreator = $member->id === $event->created_by;
                        $memberSplit = $event->expenses->flatMap->splits->firstWhere('user_id', $member->id);
                        $paid = $isCreator ? true : ($memberSplit?->is_paid ?? false);
                    @endphp
                    <div class="flex items-center gap-3 py-2.5 {{ !$loop->first ? 'border-t border-line' : '' }}">
                        <x-pt.avatar :name="$member->name" size="md"
                                     :ring="$member->id === Auth::id() ? '#FFE7DF' : '#fff'" />
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-ink text-sm">
                                {{ $member->id === Auth::id() ? 'Kamu' : $member->name }}
                                @if ($isCreator)
                                    <span class="ml-1.5 text-xs font-bold text-coral bg-coral-soft px-2 py-0.5 rounded-pill">nalangin</span>
                                @endif
                            </div>
                            @if ($memberSplit)
                                <div class="pt-num text-muted text-xs font-semibold">
                                    Rp {{ number_format($memberSplit->amount_owed, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        @if (!$isCreator)
                            <x-pt.status-pill :state="$paid ? 'lunas' : 'belum'" size="sm" />
                        @else
                            <span class="text-ink-soft text-sm font-bold">—</span>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>{{-- end card body --}}
    </div>{{-- end card --}}

    {{-- Tambah expense (hanya creator) --}}
    @if (Auth::id() === $event->created_by && $event->status === 'open')
        <div class="mt-4 text-center">
            <a href="{{ route('events.expenses.create', $event) }}"
               class="pt-btn pt-btn-ghost inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-bold">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Pengeluaran
            </a>
        </div>
    @endif

</div>
</x-app-layout>
