@props(['type', 'amount', 'count' => 0, 'people' => 0])

@php
    // type: 'owe' | 'receive'
    $isOwe = $type === 'owe';
    $cfg = $isOwe
        ? ['bg' => '#FCEFD2', 'iconBg' => '#F59E0B', 'ink' => '#C77C05',
           'label' => 'Kamu harus bayar',
           'sub'   => $count . ' tagihan belum lunas',
           'arrow' => 'up']
        : ['bg' => '#DDF6EC', 'iconBg' => '#12B886', 'ink' => '#0B8A65',
           'label' => 'Kamu bakal terima',
           'sub'   => 'dari ' . $people . ' orang',
           'arrow' => 'down'];
@endphp

<div style="background:{{ $cfg['bg'] }};border-radius:22px;padding:18px 20px;position:relative;overflow:hidden">
    {{-- Icon + label --}}
    <div class="flex items-center gap-2.5 mb-3">
        <div style="width:34px;height:34px;border-radius:11px;background:{{ $cfg['iconBg'] }};color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            @if ($cfg['arrow'] === 'up')
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    <path d="M12 19V5M6 11l6-6 6 6"/>
                </svg>
            @else
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    <path d="M12 5v14M6 13l6 6 6-6"/>
                </svg>
            @endif
        </div>
        <span style="font-weight:800;font-size:13.5px;color:{{ $cfg['ink'] }};white-space:nowrap">{{ $cfg['label'] }}</span>
    </div>

    {{-- Nominal --}}
    <div class="pt-num" style="font-size:30px;font-weight:800;color:#241D16;line-height:1">
        Rp {{ number_format($amount, 0, ',', '.') }}
    </div>

    {{-- Sub-info --}}
    <div style="margin-top:7px;font-size:12.5px;color:#6B6157;font-weight:600">{{ $cfg['sub'] }}</div>
</div>
