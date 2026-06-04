@props(['event', 'userId'])

@php
    $accents = [
        'sky'   => ['solid' => '#2BA8F4', 'dark' => '#1184D6'],
        'coral' => ['solid' => '#FF6B4A', 'dark' => '#E5512F'],
        'amber' => ['solid' => '#F59E0B', 'dark' => '#C77C05'],
        'grape' => ['solid' => '#7B61FF', 'dark' => '#5E43E8'],
        'mint'  => ['solid' => '#12B886', 'dark' => '#0B8A65'],
    ];
    $a = $accents[$event->accentColor()] ?? $accents['coral'];

    $iAmPayer = $event->created_by === $userId;
    $mySplit  = null;
    foreach ($event->expenses as $exp) {
        $split = $exp->splits->where('user_id', $userId)->first();
        if ($split) { $mySplit = $split; break; }
    }
    $iOwe = $mySplit && !$mySplit->is_paid && !$iAmPayer;

    $memberCount    = $event->eventMembers->count();
    $paidCount      = 0;
    foreach ($event->expenses as $exp) {
        $paid = $exp->splits->where('is_paid', true)->pluck('user_id')->unique()->count()
              + $exp->splits->whereNull('user_id')->where('is_paid', true)->count();
        $paidCount = max($paidCount, $paid);
    }
    $totalAmount    = $event->expenses->sum('amount');
    $sharePerPerson = $memberCount > 0 && $totalAmount > 0
        ? (int) round($totalAmount / $memberCount) : 0;
    $pct = $memberCount > 0 ? round($paidCount / $memberCount * 100) : 0;

    $statusLabel = $iOwe ? 'Utang' : ($iAmPayer ? 'Nalangin' : 'Lunas');

    $catIcons = [
        'travel' => '<path d="M21.5 3.5L11 14M21.5 3.5l-6.6 17.5-3.9-7.5-7.5-3.9L21.5 3.5z"/>',
        'coffee' => '<path d="M5 8.5h12v4.5a4.5 4.5 0 0 1-4.5 4.5H9.5A4.5 4.5 0 0 1 5 13V8.5z"/><path d="M17 9.5h1.8a2.2 2.2 0 0 1 0 4.4H17"/><path d="M8.5 3.2v2M12 3v2M15.5 3.2v2"/>',
        'ball'   => '<rect x="3" y="5.5" width="18" height="12.5" rx="2.4"/><path d="M10 9.5l4.5 2.6L10 14.7z" fill="white" stroke="none"/><path d="M8.5 21h7"/>',
        'home'   => '<path d="M3.5 11L12 4l8.5 7"/><path d="M5.5 9.7V20h13V9.7"/><path d="M10 20v-5h4v5"/>',
        'gift'   => '<rect x="4" y="9.5" width="16" height="4" rx="1"/><path d="M5.5 13.5V20h13v-6.5"/><path d="M12 9.5V20"/><path d="M12 9.5C12 7 10.5 5 8.5 5S6 8 9 9.5M12 9.5C12 7 13.5 5 15.5 5S18 8 15 9.5"/>',
    ];
    $iconPaths = $catIcons[$event->categoryIcon()] ?? '<circle cx="12" cy="12" r="8"/>';
@endphp

<a href="{{ route('events.show', $event) }}"
   class="pt-lift block rounded-[22px] overflow-hidden no-underline relative"
   style="background:linear-gradient(155deg,{{ $a['solid'] }},{{ $a['dark'] }});color:#fff;padding:18px">

    {{-- Decorative circle --}}
    <div class="absolute pointer-events-none"
         style="right:-24px;top:-24px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.12)"></div>

    {{-- Header: cat icon + status pill --}}
    <div class="flex items-center justify-between mb-4 relative">
        <div style="width:42px;height:42px;border-radius:13px;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $iconPaths !!}
            </svg>
        </div>
        <span style="background:rgba(255,255,255,.22);color:#fff;padding:4px 11px;border-radius:9999px;font-size:11.5px;font-weight:800;white-space:nowrap">
            {{ $statusLabel }}
        </span>
    </div>

    {{-- Event name --}}
    <div class="font-extrabold truncate" style="font-size:16px;margin-bottom:2px">
        {{ $event->name }}
    </div>
    <div style="font-size:11.5px;color:rgba(255,255,255,.8);font-weight:600;margin-bottom:14px">
        @if ($event->date_start)
            {{ $event->date_start->translatedFormat('d M') }} ·
        @endif
        {{ $memberCount }} orang
    </div>

    {{-- Per orang --}}
    <div style="margin-bottom:12px">
        <div style="font-size:11px;color:rgba(255,255,255,.8);font-weight:700">Per orang</div>
        <div class="pt-num font-extrabold" style="font-size:22px;line-height:1.1">
            Rp {{ number_format($sharePerPerson, 0, ',', '.') }}
        </div>
    </div>

    {{-- Progress bar --}}
    <div style="height:7px;border-radius:99px;background:rgba(255,255,255,.25);overflow:hidden;margin-bottom:6px">
        <div style="height:100%;width:{{ $pct }}%;background:#fff;border-radius:99px;transition:width .3s ease"></div>
    </div>
    <div style="font-size:11.5px;font-weight:700;color:rgba(255,255,255,.92)">
        <b>{{ $paidCount }}</b>/{{ $memberCount }} sudah bayar
    </div>

</a>
