<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FunBill') }}</title>

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
        {{-- -translate-x-full ada di static class supaya sidebar mulai hidden sebelum Alpine init
             transition sengaja di-delay via x-init supaya tidak ada slide animation saat page load --}}
        <aside class="-translate-x-full lg:translate-x-0
                      fixed inset-y-0 left-0 z-30 w-[220px] bg-white border-r border-line
                      lg:sticky lg:top-0 lg:h-screen lg:flex-shrink-0"
               x-init="$nextTick(() => $el.classList.add('transition-transform', 'duration-200', 'ease-in-out'))"
               :class="open ? '!translate-x-0 shadow-pop' : ''">
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

                {{-- Profile avatar --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex-shrink-0 rounded-full ring-2 transition-all
                          {{ request()->routeIs('profile.*') ? 'ring-coral' : 'ring-transparent hover:ring-line' }}">
                    <x-pt.avatar :name="Auth::user()->name" size="sm" />
                </a>
            </header>

            {{-- Page content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>

        </div>{{-- end main area --}}

    </div>{{-- end flex wrapper --}}

    {{-- ── GLOBAL CONFIRMATION DIALOG ─────────────────────── --}}
    <div x-data
         x-show="$store.dialog.open"
         @keydown.escape.window="$store.dialog.cancel()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-ink/40 backdrop-blur-[3px]"
             x-transition:enter="transition-opacity duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.dialog.cancel()">
        </div>

        {{-- Modal card --}}
        <div class="relative w-full max-w-sm bg-white rounded-[24px] p-6 dialog-enter"
             style="box-shadow:0 20px 48px rgba(36,29,22,.18),0 4px 12px rgba(36,29,22,.08)">

            {{-- Icon --}}
            <div class="w-12 h-12 rounded-[16px] flex items-center justify-center mx-auto mb-4"
                 :class="$store.dialog.isDanger ? 'bg-red-50' : 'bg-coral-soft'">
                <template x-if="$store.dialog.isDanger">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#EF4444"
                         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                </template>
                <template x-if="!$store.dialog.isDanger">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FF6B4A"
                         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </template>
            </div>

            {{-- Title --}}
            <h3 class="pt-num font-extrabold text-ink text-center mb-2"
                style="font-size:1.2rem;letter-spacing:-.02em"
                x-text="$store.dialog.title">
            </h3>

            {{-- Message --}}
            <p class="text-ink-soft text-sm font-medium text-center leading-relaxed mb-6"
               x-text="$store.dialog.message">
            </p>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button @click="$store.dialog.cancel()"
                        class="pt-btn pt-btn-ghost flex-1 justify-center rounded-[14px] py-3 text-sm font-bold"
                        x-text="$store.dialog.cancelText">
                </button>
                <button @click="$store.dialog.confirm()"
                        class="flex-1 justify-center rounded-[14px] py-3 text-sm font-bold inline-flex items-center gap-2 transition-all"
                        :class="$store.dialog.isDanger
                            ? 'bg-red-500 text-white hover:bg-red-600 shadow-[0_6px_16px_rgba(239,68,68,.35)]'
                            : 'pt-btn-primary bg-coral text-white'"
                        x-text="$store.dialog.confirmText">
                </button>
            </div>
        </div>
    </div>

</body>
</html>
