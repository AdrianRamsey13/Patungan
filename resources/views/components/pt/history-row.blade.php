@props(['split', 'divider' => false])

@php
    // $split: ExpenseSplit model dengan relasi expense.payer, expense.event, user
    $expense  = $split->expense;
    $isPayer  = $expense->paid_by === auth()->id();  // uang masuk ke kita
    $who      = $isPayer ? $split->user->name : $expense->payer->name;
    $label    = $isPayer
        ? "{$who} bayar ke kamu"
        : "Kamu bayar ke {$who}";
    $amountFmt = 'Rp ' . number_format($split->amount_owed, 0, ',', '.');
    $when      = $split->paid_at?->diffForHumans() ?? '-';
@endphp

<div class="flex items-center gap-3 {{ $divider ? 'border-t border-line' : '' }}"
     style="padding:11px 12px">

    {{-- Avatar + directional badge --}}
    <div class="relative flex-shrink-0">
        <x-pt.avatar :name="$who" size="sm" />
        <div style="position:absolute;right:-2px;bottom:-2px;width:16px;height:16px;border-radius:50%;background:{{ $isPayer ? '#12B886' : '#F59E0B' }};color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 2px #fff">
            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                @if ($isPayer)
                    <path d="M12 5v14M6 13l6 6 6-6"/>
                @else
                    <path d="M12 19V5M6 11l6-6 6 6"/>
                @endif
            </svg>
        </div>
    </div>

    {{-- Text --}}
    <div class="flex-1 min-w-0">
        <div class="font-bold text-ink truncate" style="font-size:13px">{{ $label }}</div>
        <div class="text-muted font-semibold truncate" style="font-size:11.5px">
            {{ $expense->event->name }} · {{ $when }}
        </div>
    </div>

    {{-- Amount --}}
    <div class="pt-num font-extrabold flex-shrink-0" style="font-size:13.5px;color:{{ $isPayer ? '#0B8A65' : '#241D16' }}">
        {{ $isPayer ? '+' : '–' }}{{ $amountFmt }}
    </div>
</div>
