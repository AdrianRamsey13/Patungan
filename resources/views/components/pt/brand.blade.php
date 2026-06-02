@props(['size' => 'md'])

@php
    $text = match($size) { 'sm' => 'text-lg', 'lg' => 'text-2xl', default => 'text-xl' };
    $icon = match($size) { 'sm' => 28, 'lg' => 40, default => 34 };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
    {{-- Split-pie logo --}}
    <div style="width:{{ $icon }}px;height:{{ $icon }}px;border-radius:{{ round($icon*0.32) }}px;box-shadow:0 4px 12px rgba(255,107,74,.4)"
         class="bg-coral flex items-center justify-center flex-shrink-0">
        <svg width="{{ round($icon*0.56) }}" height="{{ round($icon*0.56) }}" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="9" fill="none" stroke="white" stroke-width="2.4"/>
            <path d="M12 12V3M12 12l7 5.5" stroke="white" stroke-width="2.4" stroke-linecap="round"/>
        </svg>
    </div>
    <span class="pt-num font-extrabold text-ink {{ $text }}" style="letter-spacing:-.01em">FunBill</span>
</div>
