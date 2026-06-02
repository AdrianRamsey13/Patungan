{{--
    Sidebar Patungan — referensi: Variasi C (Web Bold Visual)
    Dipakai di app.blade.php, dirender dua kali:
      1. Desktop: sticky left sidebar
      2. Mobile: sliding drawer (Alpine.js)
--}}

@php
    $navItems = [
        ['route' => 'dashboard',      'icon' => 'home',    'label' => 'Beranda'],
        ['route' => 'events.create',  'icon' => 'receipt', 'label' => 'Event'],
        ['route' => 'profile.edit',   'icon' => 'users',   'label' => 'Teman'],
        ['route' => 'profile.edit',   'icon' => 'wallet',  'label' => 'Tagihan'],
    ];

    $icons = [
        'home'    => '<path d="M3.5 11L12 4l8.5 7"/><path d="M5.5 9.7V20h13V9.7"/>',
        'receipt' => '<path d="M5 3.5h14V21l-2.3-1.5L14.4 21 12 19.5 9.6 21 7.3 19.5 5 21V3.5z"/><path d="M9 8h6M9 12h6"/>',
        'users'   => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16 5.2a3.2 3.2 0 0 1 0 6M17.5 19a5.5 5.5 0 0 0-3-4.9"/>',
        'wallet'  => '<path d="M3 7h15a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path d="M3 7l2.5-3.5a1 1 0 0 1 .9-.5H16"/><circle cx="16.5" cy="13" r="1.3" fill="currentColor" stroke="none"/>',
    ];
@endphp

<div class="flex flex-col h-full p-[18px] gap-0">

    {{-- Logo --}}
    <div class="px-1.5 pb-6">
        <a href="{{ route('dashboard') }}">
            <x-pt.brand />
        </a>
    </div>

    {{-- Nav items --}}
    <nav class="flex flex-col gap-1">
        @foreach ($navItems as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3.5 py-[11px] rounded-[14px] font-bold transition-colors"
               style="font-size:14.5px;{{ $isActive
                   ? 'background:#FFE7DF;color:#FF6B4A'
                   : 'color:#6B6157' }}">
                <svg width="20" height="20" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="{{ $isActive ? '2.3' : '2' }}"
                     stroke-linecap="round" stroke-linejoin="round">
                    {!! $icons[$item['icon']] !!}
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    {{-- "Ajak teman" card --}}
    <div class="mt-5 bg-cream rounded-[18px] p-4">
        <div class="font-extrabold text-ink text-sm">Ajak teman 🎈</div>
        <p class="mt-1 mb-3 text-ink-soft leading-relaxed font-medium"
           style="font-size:11.5px">
            Patungan makin gampang kalau rame.
        </p>
        <button class="pt-btn pt-btn-ghost w-full justify-center rounded-[11px] text-xs font-bold"
                style="padding:9px">
            Bagikan link
        </button>
    </div>

    {{-- Spacer --}}
    <div class="flex-1"></div>

    {{-- User profile --}}
    <div class="flex items-center gap-2.5 px-1.5 py-2 mt-4">
        <x-pt.avatar :name="Auth::user()->name" size="md" ring="#FFF7EF" />
        <div class="min-w-0 flex-1">
            <div class="font-extrabold text-ink truncate" style="font-size:13.5px">
                {{ Auth::user()->name }}
            </div>
            <div class="text-muted font-semibold truncate" style="font-size:11.5px">
                {{ Auth::user()->email }}
            </div>
        </div>
        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
            @csrf
            <button type="submit" title="Keluar"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-muted hover:bg-line hover:text-ink transition-colors">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </form>
    </div>

</div>
