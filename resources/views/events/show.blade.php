<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 text-red-600 font-semibold text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Detail Event</span>
        @if (Auth::id() === $event->created_by && $event->status === 'open')
            <a href="{{ route('events.edit', $event) }}"
               class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink-soft hover:bg-cream transition-colors"
               style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
        @else
            <div class="w-10"></div>
        @endif
    </div>

    {{-- ── EVENT HERO ──────────────────────────────────── --}}
    <div class="bg-white rounded-[22px] p-6 mb-4"
         style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
        <div class="flex gap-4 items-start">
            <x-pt.cat-icon :cat="$event->categoryIcon()" :accent="$event->accentColor()" :size="52" />
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
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3.5" y="5" width="17" height="16" rx="2.5"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/></svg>
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

        {{-- Anggota --}}
        <div class="mt-4 pt-4 border-t border-line flex items-center justify-between">
            <x-pt.avatar-stack :members="$event->members" :max="6" size="sm" />
            <span class="text-muted text-xs font-semibold">{{ $event->members->count() }} anggota</span>
        </div>
    </div>

    {{-- ── PENGELUARAN ─────────────────────────────────── --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-extrabold text-ink" style="font-size:15px">
                Pengeluaran
                @if ($event->expenses->count())
                    <span class="ml-1.5 text-muted font-semibold text-sm">
                        · Rp {{ number_format($event->expenses->sum('amount'), 0, ',', '.') }} total
                    </span>
                @endif
            </h2>
            @if ($event->status === 'open')
                <a href="{{ route('events.expenses.create', $event) }}"
                   class="inline-flex items-center gap-1.5 text-coral font-bold text-sm hover:underline">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah
                </a>
            @endif
        </div>

        @if ($event->expenses->isEmpty())
            <div class="bg-white rounded-[18px] px-5 py-8 text-center"
                 style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 8px 20px rgba(36,29,22,.06)">
                <div class="text-muted text-sm font-semibold mb-2">Belum ada pengeluaran.</div>
                @if ($event->status === 'open')
                    <a href="{{ route('events.expenses.create', $event) }}"
                       class="text-coral text-sm font-bold hover:underline">
                        Tambah pengeluaran pertama →
                    </a>
                @endif
            </div>
        @else
            <div class="flex flex-col gap-2">
                @foreach ($event->expenses as $expense)
                    @php
                        $splitCount  = $expense->splits->count();
                        $paidSplits  = $expense->splits->where('is_paid', true)->count();
                        $paidAmount  = $expense->splits->sum('amount_paid');
                    @endphp
                    <div class="bg-white rounded-[18px] px-4 py-3.5"
                         style="box-shadow:0 1px 3px rgba(36,29,22,.05),0 6px 16px rgba(36,29,22,.06)">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-ink text-sm truncate">{{ $expense->description }}</div>
                                <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                    <span class="text-muted text-xs font-semibold">
                                        Ditanggung {{ $expense->payer->id === Auth::id() ? 'kamu' : $expense->payer->name }}
                                    </span>
                                    <span class="text-line-2 text-xs">·</span>
                                    <span class="text-muted text-xs font-semibold">
                                        {{ $splitCount }} orang
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="pt-num font-extrabold text-ink text-base">
                                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                </div>
                                <div class="text-muted text-xs font-semibold">
                                    Rp {{ number_format($splitCount > 0 ? round($expense->amount / $splitCount) : 0, 0, ',', '.') }}/orang
                                </div>
                            </div>
                        </div>
                        {{-- Progress mini --}}
                        <div class="mt-3">
                            <x-pt.progress-bar :paid="$paidSplits" :total="$splitCount" accent="mint" :height="5" />
                            <div class="mt-1 text-xs font-bold text-ink-soft">
                                <b class="text-mint-ink">{{ $paidSplits }}</b>/{{ $splitCount }} lunas
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── SETTLEMENT PLAN ─────────────────────────────── --}}
    @if ($settlements->isNotEmpty())
    <div class="mb-4">
        <h2 class="font-extrabold text-ink mb-3" style="font-size:15px">Status Pembayaran</h2>

        {{-- Yang harus kamu bayar --}}
        @if ($myDebts->isNotEmpty())
            <div class="bg-amber-soft rounded-[18px] p-4 mb-3">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-[9px] bg-amber flex items-center justify-center flex-shrink-0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                    </div>
                    <span class="font-extrabold text-amber-ink text-sm">Kamu perlu bayar</span>
                </div>

                <div class="flex flex-col gap-2">
                    @foreach ($myDebts as $settlement)
                        @if ($settlement['remaining'] > 0)
                        @php $creditor = $settlement['creditor_member']; $debtor = $settlement['debtor_member']; @endphp
                        <div class="bg-white rounded-[14px] px-3.5 py-3 flex items-center gap-3"
                             style="box-shadow:0 1px 3px rgba(36,29,22,.06)">
                            <x-pt.avatar :name="$creditor->displayName()" size="sm" />
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-ink text-sm flex items-center gap-1.5">
                                    {{ $creditor->displayName() }}
                                    @if ($creditor->isGuest())
                                        <span class="text-[10px] font-bold bg-line text-muted px-1.5 py-0.5 rounded-pill">tamu</span>
                                    @endif
                                </div>
                                <div class="text-muted text-xs font-semibold mt-0.5">
                                    @foreach ($settlement['splits'] as $split)
                                        @if ($split->remaining() > 0)
                                            {{ $split->expense->description }}
                                            (Rp {{ number_format($split->remaining(), 0, ',', '.') }})
                                            @if (!$loop->last) · @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                <span class="pt-num font-extrabold text-ink text-base">
                                    Rp {{ number_format($settlement['remaining'], 0, ',', '.') }}
                                </span>
                                @if ($event->status === 'open')
                                    <form method="POST"
                                          action="{{ route('events.pay', [$event, $debtor, $creditor]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="pt-btn pt-btn-primary px-3 py-1.5 rounded-[10px] text-xs font-bold">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                                            Tandai Lunas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Total --}}
                <div class="mt-3 pt-3 border-t border-amber/30 flex items-center justify-between">
                    <span class="text-amber-ink text-sm font-bold">Total utangmu</span>
                    <span class="pt-num font-extrabold text-ink">
                        Rp {{ number_format($myDebts->sum('remaining'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Yang bakal kamu terima --}}
        @if ($myCredits->isNotEmpty())
            <div class="bg-mint-soft rounded-[18px] p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-[9px] bg-mint flex items-center justify-center flex-shrink-0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                    </div>
                    <span class="font-extrabold text-mint-ink text-sm">Kamu bakal terima</span>
                </div>

                <div class="flex flex-col gap-2">
                    @foreach ($myCredits as $settlement)
                        @php $debtorM = $settlement['debtor_member']; @endphp
                        <div class="bg-white rounded-[14px] px-3.5 py-3 flex items-center gap-3"
                             style="box-shadow:0 1px 3px rgba(36,29,22,.06)">
                            <x-pt.avatar :name="$debtorM->displayName()" size="sm" />
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-ink text-sm flex items-center gap-1.5">
                                    {{ $debtorM->displayName() }}
                                    @if ($debtorM->isGuest())
                                        <span class="text-[10px] font-bold bg-line text-muted px-1.5 py-0.5 rounded-pill">tamu</span>
                                    @endif
                                </div>
                                <div class="text-muted text-xs font-semibold mt-0.5">
                                    @foreach ($settlement['splits'] as $split)
                                        {{ $split->expense->description }}
                                        @if (!$loop->last) · @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                <span class="pt-num font-extrabold text-ink text-base">
                                    Rp {{ number_format($settlement['total_owed'], 0, ',', '.') }}
                                </span>
                                @if ($settlement['remaining'] > 0)
                                    <x-pt.status-pill state="belum" size="sm">
                                        Belum bayar
                                    </x-pt.status-pill>
                                @else
                                    <x-pt.status-pill state="lunas" size="sm" />
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 pt-3 border-t border-mint/30 flex items-center justify-between">
                    <span class="text-mint-ink text-sm font-bold">Total yang diterima</span>
                    <span class="pt-num font-extrabold text-ink">
                        Rp {{ number_format($myCredits->sum('total_owed'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Jika tidak punya utang maupun piutang di event ini --}}
        @if ($myDebts->isEmpty() && $myCredits->isEmpty())
            <div class="bg-white rounded-[18px] px-5 py-6 text-center"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.05)">
                <div class="text-2xl mb-2">✅</div>
                <div class="font-extrabold text-ink text-sm">Semua beres!</div>
                <div class="text-muted text-xs font-semibold mt-1">Tidak ada utang atau piutang untukmu di event ini.</div>
            </div>
        @endif

        {{-- Pembayaran tamu (hanya creator) --}}
        @if (Auth::id() === $event->created_by && $guestSettlements->isNotEmpty())
            <div class="bg-white rounded-[18px] p-4 mt-3"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.06),0 6px 16px rgba(36,29,22,.05)">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-[9px] bg-grape-soft text-grape flex items-center justify-center flex-shrink-0">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/>
                                <path d="M16 5.2a3.2 3.2 0 0 1 0 6M17.5 19a5.5 5.5 0 0 0-3-4.9"/>
                            </svg>
                        </div>
                        <span class="font-extrabold text-ink text-sm">Pembayaran Tamu</span>
                    </div>
                    {{-- Tandai semua tamu lunas --}}
                    @if ($event->status === 'open')
                        <form method="POST" action="{{ route('events.guests.mark-all-paid', $event) }}" id="mark-all-guests-form">
                            @csrf
                            <button type="button"
                                    @click="ptConfirm({
                                        title: 'Tandai semua tamu lunas?',
                                        message: 'Semua pembayaran tamu di event ini akan dikonfirmasi sekaligus.',
                                        confirmText: 'Ya, Konfirmasi',
                                        isDanger: false
                                    }).then(ok => ok && document.getElementById('mark-all-guests-form').submit())"
                                    class="text-coral text-xs font-bold hover:underline">
                                Tandai Semua Lunas
                            </button>
                        </form>
                    @endif
                </div>

                <div class="flex flex-col gap-2">
                    @foreach ($guestSettlements as $settlement)
                        @php
                            $gDebtor   = $settlement['debtor_member'];
                            $gCreditor = $settlement['creditor_member'];
                        @endphp
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-[12px] bg-cream">
                            <x-pt.avatar :name="$gDebtor->displayName()" size="sm" />
                            <div class="flex-1 min-w-0">
                                <div class="text-ink text-xs font-bold flex items-center gap-1.5">
                                    {{ $gDebtor->displayName() }}
                                    @if ($gDebtor->isGuest())
                                        <span class="text-[9px] bg-line text-muted px-1.5 py-0.5 rounded-pill">tamu</span>
                                    @endif
                                    <span class="text-muted font-semibold">→</span>
                                    {{ $gCreditor->displayName() }}
                                    @if ($gCreditor->isGuest())
                                        <span class="text-[9px] bg-line text-muted px-1.5 py-0.5 rounded-pill">tamu</span>
                                    @endif
                                </div>
                                <div class="text-muted text-[10px] font-semibold mt-0.5">
                                    @foreach ($settlement['splits'] as $split)
                                        {{ $split->expense->description }}@if (!$loop->last), @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                <span class="pt-num font-extrabold text-ink text-sm">
                                    Rp {{ number_format($settlement['remaining'], 0, ',', '.') }}
                                </span>
                                @if ($event->status === 'open')
                                    <form method="POST"
                                          action="{{ route('events.pay', [$event, $gDebtor, $gCreditor]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="text-coral text-[10px] font-bold hover:underline">
                                            Konfirmasi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- ── ANGGOTA ──────────────────────────────────────── --}}
    <div>
        <h2 class="font-extrabold text-ink mb-3" style="font-size:15px">Anggota Event</h2>
        <div class="bg-white rounded-[18px] overflow-hidden"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 8px 20px rgba(36,29,22,.06)">
            @foreach ($event->eventMembers as $member)
                @php
                    $isCreator        = !$member->isGuest() && $member->user_id === $event->created_by;
                    $isMe             = !$member->isGuest() && $member->user_id === Auth::id();
                    $memberDebts      = $settlements->filter(fn($s) => $s['debtor_member']->id === $member->id);
                    $hasDebts         = $memberDebts->isNotEmpty();
                    $allSettled       = $hasDebts && $memberDebts->every(fn($s) => $s['remaining'] === 0);
                    $hasPaidSomething = $hasDebts && $memberDebts->some(fn($s) => $s['total_paid'] > 0);
                    $hasUnpaid        = $hasDebts && $memberDebts->some(fn($s) => $s['remaining'] > 0);

                    $memberStatus = match(true) {
                        $isCreator || !$hasDebts        => 'lunas',
                        $allSettled                     => 'lunas',
                        $hasPaidSomething && $hasUnpaid => 'sebagian',
                        default                         => 'belum',
                    };
                @endphp
                <div class="flex items-center gap-3 px-4 py-3 {{ !$loop->first ? 'border-t border-line' : '' }}">
                    <x-pt.avatar :name="$member->displayName()" size="md"
                                 :ring="$isMe ? '#FFE7DF' : ($member->isGuest() ? '#F1E8DC' : '#fff')" />
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-ink text-sm flex items-center gap-2 flex-wrap">
                            {{ $isMe ? 'Kamu' : $member->displayName() }}
                            @if ($isCreator)
                                <span class="text-xs font-bold text-coral bg-coral-soft px-2 py-0.5 rounded-pill">nalangin</span>
                            @endif
                            @if ($member->isGuest())
                                <span class="text-xs font-bold text-muted bg-line px-2 py-0.5 rounded-pill">tamu</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <x-pt.status-pill :state="$memberStatus" size="sm" />

                        @if (!$isCreator && !$isMe && Auth::id() === $event->created_by && $event->status === 'open')
                            <form method="POST"
                                  action="{{ route('events.members.remove', [$event, $member]) }}"
                                  id="remove-member-{{ $member->id }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        @click="ptConfirm({
                                            title: 'Keluarkan {{ addslashes($member->displayName()) }}?',
                                            message: '{{ addslashes($member->displayName()) }} akan dikeluarkan dari event ini.',
                                            confirmText: 'Ya, Keluarkan',
                                            isDanger: true
                                        }).then(ok => ok && document.getElementById('remove-member-{{ $member->id }}').submit())"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-muted hover:bg-red-50 hover:text-red-400 transition-colors">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Tambah anggota (hanya creator) --}}
            @if (Auth::id() === $event->created_by && $event->status === 'open')
                <div class="border-t border-line px-4 py-3"
                     x-data="{ open: false, selectedIds: [] }">

                    <button @click="open = !open"
                            class="flex items-center gap-2 text-coral text-sm font-bold hover:underline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        <span x-text="open ? 'Batal' : 'Tambah anggota'"></span>
                    </button>

                    @if ($availableUsers->isEmpty())
                        <p x-show="open" class="text-muted text-xs font-semibold mt-2" style="display:none">
                            Semua user sudah ada di event ini.
                        </p>
                    @else
                        <form x-show="open" method="POST"
                              action="{{ route('events.members.add', $event) }}"
                              class="mt-3" style="display:none">
                            @csrf

                            {{-- Chip picker --}}
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach ($availableUsers as $u)
                                    <button type="button"
                                            @click="selectedIds.includes({{ $u->id }})
                                                ? selectedIds = selectedIds.filter(i => i !== {{ $u->id }})
                                                : selectedIds.push({{ $u->id }})"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-pill font-bold text-sm transition-colors"
                                            :style="selectedIds.includes({{ $u->id }})
                                                ? 'background:#241D16;color:#fff'
                                                : 'background:#FFF7EF;color:#6B6157;box-shadow:inset 0 0 0 1.5px #EADFCF'">
                                        <x-pt.avatar :name="$u->name" size="xs" />
                                        {{ explode(' ', $u->name)[0] }}
                                    </button>
                                    <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="hidden"
                                           x-bind:checked="selectedIds.includes({{ $u->id }})">
                                @endforeach
                            </div>

                            <button type="submit"
                                    :disabled="selectedIds.length === 0"
                                    class="pt-btn pt-btn-primary w-full justify-center rounded-xl py-2.5 text-sm font-bold"
                                    :class="selectedIds.length === 0 ? 'opacity-40 cursor-not-allowed' : ''">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                Tambah <span x-text="selectedIds.length > 0 ? selectedIds.length + ' orang' : ''"></span>
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── HAPUS EVENT (hanya creator) ── --}}
    @if (Auth::id() === $event->created_by)
        <div class="mt-6 pt-4 text-center">
            <form method="POST" action="{{ route('events.destroy', $event) }}" id="delete-event-form">
                @csrf @method('DELETE')
                <button type="button"
                        @click="ptConfirm({
                            title: 'Hapus Event?',
                            message: 'Event ini dan semua data pembayarannya akan hilang permanen.',
                            confirmText: 'Ya, Hapus',
                            isDanger: true
                        }).then(ok => ok && document.getElementById('delete-event-form').submit())"
                        class="inline-flex items-center gap-2 text-red-400 hover:text-red-600 text-sm font-bold transition-colors px-4 py-2 rounded-xl hover:bg-red-50">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                    </svg>
                    Hapus Event
                </button>
            </form>
        </div>
    @endif

</div>
</x-app-layout>
