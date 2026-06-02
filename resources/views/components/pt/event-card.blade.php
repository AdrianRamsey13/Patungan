@props(['event', 'userId'])

@php
    // Cek status user terhadap event ini
    $myExpense = $event->expenses->where('paid_by', $userId)->first();
    $mySplit   = null;
    foreach ($event->expenses as $exp) {
        $split = $exp->splits->where('user_id', $userId)->first();
        if ($split) { $mySplit = $split; break; }
    }

    $iAmPayer  = $event->created_by === $userId;
    $iOwe      = $mySplit && !$mySplit->is_paid && !$iAmPayer;

    $paidCount = 0;
    $memberCount = $event->members->count();
    foreach ($event->expenses as $exp) {
        $paidCount = max($paidCount, $exp->splits->where('is_paid', true)->count());
    }

    $sharePerPerson = $memberCount > 0 && $event->expenses->first()
        ? (int) round($event->expenses->sum('amount') / $memberCount)
        : 0;

    $accent = $event->accentColor();
    $cat    = $event->categoryIcon();
@endphp

<a href="{{ route('events.show', $event) }}"
   class="pt-lift block text-left w-full rounded-lg bg-white p-4 no-underline"
   style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

    {{-- Header: icon + nama + status pill --}}
    <div class="flex items-start gap-3 mb-3.5">
        <x-pt.cat-icon :cat="$cat" :accent="$accent" :size="46" />
        <div class="flex-1 min-w-0">
            <div class="font-extrabold text-ink truncate" style="font-size:15px">{{ $event->name }}</div>
            <div class="text-muted font-semibold mt-0.5" style="font-size:12px">
                {{ $event->date_start?->translatedFormat('d M') }} · {{ $memberCount }} orang
            </div>
        </div>
        @if ($iOwe)
            <x-pt.status-pill state="belum" size="sm">Utang</x-pt.status-pill>
        @elseif ($iAmPayer)
            <x-pt.status-pill state="payer" size="sm" />
        @else
            <x-pt.status-pill state="lunas" size="sm" />
        @endif
    </div>

    {{-- Per orang + avatar stack --}}
    <div class="flex items-end justify-between mb-3">
        <div>
            <div class="text-muted font-bold" style="font-size:11.5px">Per orang</div>
            <div class="pt-num font-extrabold text-ink" style="font-size:21px">
                Rp {{ number_format($sharePerPerson, 0, ',', '.') }}
            </div>
        </div>
        <x-pt.avatar-stack :members="$event->members" :max="4" size="sm" />
    </div>

    {{-- Progress bar --}}
    <x-pt.progress-bar :paid="$paidCount" :total="$memberCount" accent="mint" :height="7" />
    <div class="mt-1.5 font-bold text-ink-soft" style="font-size:11.5px">
        <b class="text-mint-ink">{{ $paidCount }}</b>/{{ $memberCount }} sudah bayar
    </div>
</a>
