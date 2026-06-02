@props(['cat', 'accent' => 'coral', 'size' => 46])

@php
    $accents = [
        'sky'   => ['soft' => '#DDF0FD', 'ink' => '#1184D6'],
        'coral' => ['soft' => '#FFE7DF', 'ink' => '#E5512F'],
        'amber' => ['soft' => '#FCEFD2', 'ink' => '#C77C05'],
        'grape' => ['soft' => '#ECE7FF', 'ink' => '#5E43E8'],
        'mint'  => ['soft' => '#DDF6EC', 'ink' => '#0B8A65'],
    ];
    $a = $accents[$accent] ?? $accents['coral'];
    $iconSize = round($size * 0.5);
    $radius = round($size * 0.30);
@endphp

<div style="width:{{ $size }}px;height:{{ $size }}px;border-radius:{{ $radius }}px;background:{{ $a['soft'] }};color:{{ $a['ink'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <svg width="{{ $iconSize }}" height="{{ $iconSize }}" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        @switch($cat)
            @case('travel')
                <path d="M21.5 3.5L11 14M21.5 3.5l-6.6 17.5-3.9-7.5-7.5-3.9L21.5 3.5z"/>
                @break
            @case('coffee')
                <path d="M5 8.5h12v4.5a4.5 4.5 0 0 1-4.5 4.5H9.5A4.5 4.5 0 0 1 5 13V8.5z"/>
                <path d="M17 9.5h1.8a2.2 2.2 0 0 1 0 4.4H17"/>
                <path d="M8.5 3.2v2M12 3v2M15.5 3.2v2"/>
                @break
            @case('ball')
                <rect x="3" y="5.5" width="18" height="12.5" rx="2.4"/>
                <path d="M10 9.5l4.5 2.6L10 14.7z" fill="currentColor" stroke="none"/>
                <path d="M8.5 21h7"/>
                @break
            @case('home')
                <path d="M3.5 11L12 4l8.5 7"/>
                <path d="M5.5 9.7V20h13V9.7"/>
                <path d="M10 20v-5h4v5"/>
                @break
            @case('gift')
                <rect x="4" y="9.5" width="16" height="4" rx="1"/>
                <path d="M5.5 13.5V20h13v-6.5"/>
                <path d="M12 9.5V20"/>
                <path d="M12 9.5C12 7 10.5 5 8.5 5S6 8 9 9.5M12 9.5C12 7 13.5 5 15.5 5S18 8 15 9.5"/>
                @break
            @default
                <circle cx="12" cy="12" r="8"/>
        @endswitch
    </svg>
</div>
