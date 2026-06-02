@props(['paid', 'total', 'accent' => 'mint', 'height' => 8])

@php
    $pct = $total > 0 ? round(($paid / $total) * 100) : 0;
    $accents = [
        'mint'  => ['solid' => '#12B886', 'ink' => '#0B8A65'],
        'coral' => ['solid' => '#FF6B4A', 'ink' => '#E5512F'],
        'sky'   => ['solid' => '#2BA8F4', 'ink' => '#1184D6'],
        'grape' => ['solid' => '#7B61FF', 'ink' => '#5E43E8'],
        'amber' => ['solid' => '#F59E0B', 'ink' => '#C77C05'],
    ];
    $a = $accents[$accent] ?? $accents['mint'];
@endphp

<div style="width:100%">
    <div style="height:{{ $height }}px;border-radius:99px;background:#F0E8DC;overflow:hidden">
        <div style="height:100%;width:{{ $pct }}%;border-radius:99px;background:linear-gradient(90deg,{{ $a['solid'] }},{{ $a['ink'] }});transition:width .3s ease"></div>
    </div>
</div>
