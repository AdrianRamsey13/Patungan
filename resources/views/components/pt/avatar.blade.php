@props(['name', 'size' => 'md', 'ring' => null])

@php
    // Warna deterministik berdasarkan nama
    $palette = ['#FF6B4A','#7B61FF','#2BA8F4','#12B886','#F59E0B','#EC5F9E','#0FB5AE','#8B6F47','#6366F1','#E8590C'];
    $bg = $palette[abs(crc32($name)) % count($palette)];

    // Inisial
    $words = explode(' ', trim($name));
    $initials = count($words) >= 2
        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
        : strtoupper(substr($name, 0, 2));

    $sizes = [
        'xs' => ['px' => 24, 'text' => '8.5px'],
        'sm' => ['px' => 28, 'text' => '10px'],
        'md' => ['px' => 36, 'text' => '13px'],
        'lg' => ['px' => 44, 'text' => '15px'],
        'xl' => ['px' => 52, 'text' => '18px'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $boxShadow = $ring ? "0 0 0 2px {$ring}" : 'none';
@endphp

<div {{ $attributes }}
     style="width:{{ $s['px'] }}px;height:{{ $s['px'] }}px;border-radius:50%;background:{{ $bg }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:{{ $s['text'] }};flex-shrink:0;box-shadow:{{ $boxShadow }};letter-spacing:.01em">
    {{ $initials }}
</div>
