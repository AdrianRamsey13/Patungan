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
<body class="font-sans antialiased text-ink">

<div class="min-h-screen flex">

    {{-- ── PANEL KIRI (desktop only) ─────────────────── --}}
    <div class="hidden lg:flex lg:w-[420px] xl:w-[480px] flex-shrink-0 flex-col relative overflow-hidden"
         style="background:linear-gradient(160deg,#2E2620,#241D16)">

        {{-- Dekorasi lingkaran --}}
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full"
             style="background:rgba(255,107,74,.15)"></div>
        <div class="absolute bottom-20 -left-16 w-56 h-56 rounded-full"
             style="background:rgba(255,107,74,.08)"></div>

        {{-- Konten atas --}}
        <div class="relative z-10 flex flex-col flex-1 p-10">

            {{-- Brand --}}
            <a href="{{ route('landing') }}">
                <x-pt.brand size="md" />
            </a>

            {{-- Tagline --}}
            <div class="mt-12">
                <h2 class="pt-num font-extrabold text-white leading-tight mb-3"
                    style="font-size:2.1rem;letter-spacing:-.03em">
                    Split bill bareng<br>teman, gampang<br>
                    <span style="color:#FF6B4A">banget.</span>
                </h2>
                <p class="text-white/60 font-medium text-sm leading-relaxed">
                    Buat event, pilih peserta, input biaya — FunBill hitung otomatis sisanya.
                </p>
            </div>

            {{-- Feature bullets --}}
            <div class="mt-8 flex flex-col gap-3">
                @foreach ([
                    ['✅', 'Split rata otomatis, bisa custom per orang'],
                    ['🔔', 'Ingatkan teman yang belum bayar'],
                    ['📊', 'Lacak semua event & riwayat transaksi'],
                ] as [$icon, $text])
                    <div class="flex items-center gap-3">
                        <span class="text-base flex-shrink-0">{{ $icon }}</span>
                        <span class="text-white/70 font-medium text-sm">{{ $text }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex-1"></div>

            {{-- Karakter 3D di pojok bawah --}}
            <div class="relative flex justify-end items-end h-[220px] -mr-4 -mb-2">
                <div class="absolute bottom-0 right-8 w-36 h-10 rounded-full"
                     style="background:rgba(255,107,74,.2);filter:blur(16px)"></div>
                <img src="{{ asset('images/ramsey.png') }}" alt="Developer"
                     class="relative h-[210px] object-contain object-bottom select-none"
                     style="filter:drop-shadow(0 10px 30px rgba(0,0,0,.4))"
                     draggable="false">
            </div>
        </div>
    </div>

    {{-- ── PANEL KANAN (form) ──────────────────────────── --}}
    <div class="flex-1 flex flex-col items-center justify-center min-h-screen bg-cream px-6 py-10">

        {{-- Brand mobile (hanya muncul di mobile) --}}
        <div class="lg:hidden mb-8">
            <a href="{{ route('landing') }}">
                <x-pt.brand />
            </a>
        </div>

        {{-- Form card --}}
        <div class="w-full max-w-[400px]">
            {{ $slot }}
        </div>

        {{-- Back to landing --}}
        <a href="{{ route('landing') }}"
           class="mt-8 text-muted text-sm font-semibold hover:text-ink transition-colors flex items-center gap-1.5">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
            Kembali ke beranda
        </a>
    </div>

</div>

</body>
</html>
