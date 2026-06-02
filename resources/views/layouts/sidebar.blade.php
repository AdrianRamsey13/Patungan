@php
    $navItems = [
        ['route' => 'dashboard',    'activePattern' => 'dashboard',  'icon' => 'home',    'label' => 'Beranda'],
        ['route' => 'events.create','activePattern' => 'events.*',   'icon' => 'receipt', 'label' => 'Event'],
        ['route' => 'notes.index',  'activePattern' => 'notes.*',    'icon' => 'notes',   'label' => 'Notes'],
        ['route' => 'friends.index','activePattern' => 'friends.*',  'icon' => 'users',   'label' => 'Teman'],
        ['route' => 'bills.index',  'activePattern' => 'bills.*',    'icon' => 'wallet',  'label' => 'Tagihan'],
        ['route' => 'profile.edit', 'activePattern' => 'profile.*',  'icon' => 'user',    'label' => 'Profil'],
    ];

    $icons = [
        'home'    => '<path d="M3.5 11L12 4l8.5 7"/><path d="M5.5 9.7V20h13V9.7"/>',
        'receipt' => '<path d="M5 3.5h14V21l-2.3-1.5L14.4 21 12 19.5 9.6 21 7.3 19.5 5 21V3.5z"/><path d="M9 8h6M9 12h6"/>',
        'notes'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        'users'   => '<circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/><path d="M16 5.2a3.2 3.2 0 0 1 0 6M17.5 19a5.5 5.5 0 0 0-3-4.9"/>',
        'wallet'  => '<path d="M3 7h15a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path d="M3 7l2.5-3.5a1 1 0 0 1 .9-.5H16"/><circle cx="16.5" cy="13" r="1.3" fill="currentColor" stroke="none"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/>',
    ];
@endphp

<div class="flex flex-col h-full p-[18px]">

    {{-- Logo --}}
    <div class="px-1.5 pb-6">
        <a href="{{ route('dashboard') }}">
            <x-pt.brand />
        </a>
    </div>

    {{-- Nav items --}}
    <nav class="flex flex-col gap-1">
        @foreach ($navItems as $item)
            @php $isActive = request()->routeIs($item['activePattern']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3.5 py-[11px] rounded-[14px] font-bold transition-colors"
               style="font-size:14.5px;{{ $isActive ? 'background:#FFE7DF;color:#FF6B4A' : 'color:#6B6157' }}">
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
        <p class="mt-1 mb-3 text-ink-soft leading-relaxed font-medium" style="font-size:11.5px">
            FunBill makin seru kalau rame.
        </p>
        <button class="pt-btn pt-btn-ghost w-full justify-center rounded-[11px] text-xs font-bold"
                style="padding:9px">
            Bagikan link
        </button>
    </div>

    <div class="flex-1"></div>

    {{-- User profile — klik untuk ke halaman profil --}}
    <div class="mt-4 border-t border-line pt-3">
        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-2.5 px-1.5 py-2 rounded-[14px] transition-colors
                  {{ request()->routeIs('profile.*') ? 'bg-coral-soft' : 'hover:bg-cream' }}">
            <x-pt.avatar :name="Auth::user()->name" size="md" ring="#FFF7EF" />
            <div class="min-w-0 flex-1">
                <div class="font-extrabold text-ink truncate" style="font-size:13.5px">
                    {{ Auth::user()->name }}
                </div>
                <div class="text-muted font-semibold truncate" style="font-size:11.5px">
                    {{ Auth::user()->email }}
                </div>
            </div>
        </a>

        {{-- Logout terpisah --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-[14px]
                           text-ink-soft hover:bg-cream hover:text-ink transition-colors font-bold text-sm">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Keluar
            </button>
        </form>
    </div>

</div>
