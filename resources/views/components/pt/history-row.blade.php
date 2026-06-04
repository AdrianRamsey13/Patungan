@props(['split', 'divider' => false])

@php
    $expense = $split->expense;

    // isPayer: expense.paid_by === auth user (uang masuk ke kita)
    // Handles null paid_by (guest payer) safely
    $isPayer = $expense->paid_by !== null && $expense->paid_by === auth()->id();

    // Nama orang yang terlibat — handle null untuk guest split & guest payer
    if ($isPayer) {
        // Kita yang nalangin → "X bayar ke kamu" — X = debtor
        $who = $split->user?->name ?? $split->guest_name ?? 'Tamu';
    } else {
        // Kita yang bayar → "Kamu bayar ke X" — X = payer
        $who = $expense->payer?->name ?? $expense->guest_payer_name ?? 'Tamu';
    }

    $label     = $isPayer ? "{$who} bayar ke kamu" : "Kamu bayar ke {$who}";
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
            {{ $expense->event->name ?? '—' }} · {{ $when }}
        </div>
    </div>

    {{-- Amount --}}
    <div class="pt-num font-extrabold flex-shrink-0"
         style="font-size:13.5px;color:{{ $isPayer ? '#0B8A65' : '#241D16' }}">
        {{ $isPayer ? '+' : '–' }}{{ $amountFmt }}
    </div>
</div>
