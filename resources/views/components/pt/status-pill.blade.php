@props(['state', 'size' => 'md'])

@php
    $map = [
        'lunas'    => ['bg' => '#DDF6EC', 'fg' => '#0B8A65', 'label' => 'Lunas',         'dot' => '#12B886'],
        'sebagian' => ['bg' => '#DDF0FD', 'fg' => '#1184D6', 'label' => 'Belum lunas',   'dot' => '#2BA8F4'],
        'belum'    => ['bg' => '#FCEFD2', 'fg' => '#C77C05', 'label' => 'Belum bayar',   'dot' => '#F59E0B'],
        'payer'    => ['bg' => '#FFE7DF', 'fg' => '#FF6B4A', 'label' => 'Kamu nalangin', 'dot' => null],
        'selesai'  => ['bg' => '#EDEAE4', 'fg' => '#7A7165', 'label' => 'Selesai',       'dot' => null],
    ];
    $s = $map[$state] ?? $map['belum'];
    $pad   = $size === 'sm' ? '3px 9px'  : '5px 12px';
    $fs    = $size === 'sm' ? '11.5px'   : '13px';
@endphp

<span {{ $attributes }}
      style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};padding:{{ $pad }};border-radius:9999px;font-weight:700;font-size:{{ $fs }};display:inline-flex;align-items:center;gap:5px;white-space:nowrap;line-height:1.1">
    @if ($s['dot'])
        <span style="width:6px;height:6px;border-radius:50%;background:{{ $s['dot'] }};display:inline-block;flex-shrink:0"></span>
    @endif
    {{ $slot->isEmpty() ? $s['label'] : $slot }}
</span>
