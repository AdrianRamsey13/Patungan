<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Patungan') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- Alpine root: `open` = state mobile sidebar drawer --}}
<body class="font-sans antialiased bg-cream text-ink"
      x-data="{ open: false }" @keydown.escape.window="open = false">

    <div class="flex min-h-screen">

        {{-- ── BACKDROP (mobile) ─────────────────────────── --}}
        <div x-show="open"
             x-transition:enter="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 z-20 bg-ink/30 backdrop-blur-[2px] lg:hidden"
             style="display:none">
        </div>

        {{-- ── SIDEBAR ────────────────────────────────────── --}}
        <aside class="fixed inset-y-0 left-0 z-30 w-[220px] bg-white border-r border-line
                      transform transition-transform duration-200 ease-in-out
                      lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0 lg:translate-x-0"
               :class="open ? 'translate-x-0 shadow-pop' : '-translate-x-full lg:translate-x-0'">
            @include('layouts.sidebar')
        </aside>

        {{-- ── MAIN AREA ──────────────────────────────────── --}}
        <div class="flex flex-col flex-1 min-w-0">

            {{-- Mobile top bar --}}
            <header class="lg:hidden sticky top-0 z-10 flex items-center gap-3 px-4 h-14
                           bg-white/90 backdrop-blur-sm border-b border-line">
                {{-- Hamburger --}}
                <button @click="open = !open"
                        class="w-9 h-9 rounded-xl bg-cream flex items-center justify-center text-ink-soft
                               hover:bg-line transition-colors flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.2" stroke-linecap="round">
                        <path x-show="!open" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open"  d="M6 18L18 6M6 6l12 12" style="display:none"/>
                    </svg>
                </button>

                {{-- Logo tengah --}}
                <a href="{{ route('dashboard') }}" class="flex-1 flex justify-center">
                    <x-pt.brand size="sm" />
                </a>

                {{-- Notifikasi --}}
                <button class="relative w-9 h-9 rounded-xl bg-cream flex items-center justify-center
                               text-ink-soft hover:bg-line transition-colors flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                    </svg>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-coral"
                          style="box-shadow:0 0 0 2px #fff"></span>
                </button>
            </header>

            {{-- Page content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>

        </div>{{-- end main area --}}

    </div>{{-- end flex wrapper --}}

</body>
</html>
