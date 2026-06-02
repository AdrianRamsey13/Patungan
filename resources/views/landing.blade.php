<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patungan — Split bill bareng teman, gampang banget</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-cream text-ink">

{{-- ── NAVBAR ─────────────────────────────────────── --}}
<header class="flex items-center justify-between px-8 md:px-16 h-[68px] bg-cream/80 backdrop-blur-sm sticky top-0 z-30">
    <x-pt.brand />

    <nav class="flex items-center gap-3">
        <a href="{{ route('login') }}"
           class="px-5 py-2.5 rounded-pill font-bold text-sm text-ink hover:bg-white transition-colors"
           style="box-shadow:inset 0 0 0 1.5px #EADFCF">
            Masuk
        </a>
        <a href="{{ route('register') }}"
           class="pt-btn pt-btn-primary px-5 py-2.5 rounded-pill text-sm font-bold">
            Mulai gratis
        </a>
    </nav>
</header>

{{-- ── HERO ────────────────────────────────────────── --}}
<section class="px-6 md:px-16 pt-20 pb-24 text-center max-w-3xl mx-auto">

    {{-- Badge --}}
    <div class="inline-flex items-center gap-2 bg-coral-soft text-coral px-4 py-1.5 rounded-pill text-sm font-bold mb-8">
        <span class="w-2 h-2 rounded-full bg-coral"></span>
        Patungan makin gampang
    </div>

    {{-- Headline --}}
    <h1 class="pt-num font-extrabold text-ink leading-[1.1] mb-6"
        style="font-size:clamp(2.4rem,6vw,3.8rem);letter-spacing:-.03em">
        Split bill bareng teman,<br>
        <span style="color:#FF6B4A">tanpa drama</span>.
    </h1>

    {{-- Subheadline --}}
    <p class="text-ink-soft font-medium text-lg leading-relaxed mb-10 max-w-xl mx-auto">
        Buat event, tambah peserta, input total biaya — Patungan hitung otomatis siapa harus bayar berapa ke siapa.
    </p>

    {{-- CTAs --}}
    <div class="flex items-center justify-center gap-3 flex-wrap">
        <a href="{{ route('register') }}"
           class="pt-btn pt-btn-primary px-7 py-4 rounded-[16px] text-base font-bold">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Buat akun gratis
        </a>
        <a href="{{ route('login') }}"
           class="pt-btn pt-btn-ghost px-7 py-4 rounded-[16px] text-base font-bold">
            Sudah punya akun
        </a>
    </div>

    {{-- Social proof mini --}}
    <p class="text-muted text-sm font-semibold mt-8">
        Gratis selamanya · Tidak perlu kartu kredit
    </p>
</section>

{{-- ── MOCK UI CARD ─────────────────────────────────── --}}
<section class="px-6 md:px-16 pb-24 max-w-3xl mx-auto">
    <div class="bg-white rounded-[28px] p-6 md:p-8"
         style="box-shadow:0 10px 24px rgba(36,29,22,.08),0 40px 80px rgba(36,29,22,.12)">

        {{-- Summary bar --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="rounded-[18px] p-4" style="background:#FCEFD2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-[9px] bg-amber flex items-center justify-center">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                    </div>
                    <span class="text-xs font-bold text-amber-ink">Harus bayar</span>
                </div>
                <div class="pt-num font-extrabold text-ink" style="font-size:22px">Rp 100.000</div>
                <div class="text-ink-soft text-xs font-semibold mt-1">2 tagihan belum lunas</div>
            </div>
            <div class="rounded-[18px] p-4" style="background:#DDF6EC">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-7 h-7 rounded-[9px] bg-mint flex items-center justify-center">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                    </div>
                    <span class="text-xs font-bold text-mint-ink">Bakal terima</span>
                </div>
                <div class="pt-num font-extrabold text-ink" style="font-size:22px">Rp 2.300.000</div>
                <div class="text-ink-soft text-xs font-semibold mt-1">dari 4 orang</div>
            </div>
        </div>

        {{-- Event cards mock --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach ([
                ['title' => 'Liburan ke Bali',     'cat' => 'travel', 'accent' => 'sky',   'date' => '12 Jun', 'n' => 6,  'share' => 800000,  'paid' => 4, 'state' => 'payer'],
                ['title' => 'Galon & Kopi Kantor',  'cat' => 'coffee', 'accent' => 'amber', 'date' => '29 Mei', 'n' => 8,  'share' => 30000,   'paid' => 5, 'state' => 'belum'],
                ['title' => 'Nobar Final Liga',     'cat' => 'ball',   'accent' => 'coral', 'date' => '27 Mei', 'n' => 5,  'share' => 70000,   'paid' => 2, 'state' => 'belum'],
                ['title' => 'Sewa Villa Puncak',    'cat' => 'home',   'accent' => 'grape', 'date' => '20 Jun', 'n' => 10, 'share' => 350000,  'paid' => 8, 'state' => 'payer'],
            ] as $card)
            @php
                $stateLabel = $card['state'] === 'payer' ? 'Nalangin' : 'Utang';
                $accents = [
                    'sky'   => ['soft' => '#DDF0FD', 'ink' => '#1184D6'],
                    'amber' => ['soft' => '#FCEFD2', 'ink' => '#C77C05'],
                    'coral' => ['soft' => '#FFE7DF', 'ink' => '#E5512F'],
                    'grape' => ['soft' => '#ECE7FF', 'ink' => '#5E43E8'],
                ];
                $a = $accents[$card['accent']];
                $pct = round($card['paid'] / $card['n'] * 100);
            @endphp
            <div class="rounded-[18px] p-4" style="background:#FFF7EF">
                <div class="flex items-start gap-3 mb-3">
                    <x-pt.cat-icon :cat="$card['cat']" :accent="$card['accent']" :size="42" />
                    <div class="flex-1 min-w-0">
                        <div class="font-extrabold text-ink text-sm truncate">{{ $card['title'] }}</div>
                        <div class="text-muted font-semibold text-xs mt-0.5">{{ $card['date'] }} · {{ $card['n'] }} orang</div>
                    </div>
                    <x-pt.status-pill :state="$card['state']" size="sm">{{ $stateLabel }}</x-pt.status-pill>
                </div>
                <div class="pt-num font-extrabold text-ink mb-2" style="font-size:18px">
                    Rp {{ number_format($card['share'], 0, ',', '.') }}
                    <span class="text-muted font-semibold text-xs">/orang</span>
                </div>
                <div class="h-1.5 rounded-full bg-line overflow-hidden">
                    <div class="h-full rounded-full bg-mint" style="width:{{ $pct }}%"></div>
                </div>
                <div class="text-xs font-bold text-ink-soft mt-1">
                    <b class="text-mint-ink">{{ $card['paid'] }}</b>/{{ $card['n'] }} sudah bayar
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── FEATURES ─────────────────────────────────────── --}}
<section class="px-6 md:px-16 pb-24 max-w-4xl mx-auto">
    <h2 class="pt-num font-extrabold text-ink text-center mb-12"
        style="font-size:2rem;letter-spacing:-.02em">
        Semua yang kamu butuhkan
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach ([
            ['icon' => 'M12 5v14M5 12h14',             'color' => '#FF6B4A', 'bg' => '#FFE7DF', 'title' => 'Buat event dalam hitungan detik',     'desc' => 'Nama, kategori, total biaya, pilih teman — selesai. Patungan langsung hitung per orang.'],
            ['icon' => 'M5 12.5l5 5 9-11',              'color' => '#12B886', 'bg' => '#DDF6EC', 'title' => 'Tandai lunas kapan saja',              'desc' => 'Semua anggota event bisa konfirmasi pembayaran sendiri. Kamu selalu tahu siapa yang sudah dan belum.'],
            ['icon' => 'M12 19V5M6 11l6-6 6 6',        'color' => '#2BA8F4', 'bg' => '#DDF0FD', 'title' => 'Lacak semua di satu tempat',            'desc' => 'Dashboard ringkas menampilkan total yang harus kamu terima dan total yang perlu kamu bayar, real-time.'],
        ] as $f)
        <div class="bg-white rounded-[22px] p-6" style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
            <div class="w-12 h-12 rounded-[15px] flex items-center justify-center mb-4"
                 style="background:{{ $f['bg'] }};color:{{ $f['color'] }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $f['icon'] }}"/>
                </svg>
            </div>
            <h3 class="font-extrabold text-ink mb-2" style="font-size:15px;letter-spacing:-.01em">{{ $f['title'] }}</h3>
            <p class="text-ink-soft text-sm leading-relaxed font-medium">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ── HOW IT WORKS ─────────────────────────────────── --}}
<section class="px-6 md:px-16 pb-28 max-w-3xl mx-auto text-center">
    <h2 class="pt-num font-extrabold text-ink mb-12"
        style="font-size:2rem;letter-spacing:-.02em">
        Cara pakainya simpel
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-left">
        @foreach ([
            ['n' => '1', 'title' => 'Buat event',        'desc' => 'Kasih nama, pilih kategori, input total biaya event.'],
            ['n' => '2', 'title' => 'Pilih peserta',      'desc' => 'Tambahkan teman-teman yang ikut patungan.'],
            ['n' => '3', 'title' => 'Split otomatis',     'desc' => 'Sistem langsung hitung bagian masing-masing orang.'],
            ['n' => '4', 'title' => 'Tandai lunas',       'desc' => 'Setelah bayar, konfirmasi — semua langsung tau.'],
        ] as $step)
        <div class="flex flex-col gap-3">
            <div class="w-10 h-10 rounded-[13px] bg-coral text-white flex items-center justify-center pt-num font-extrabold text-lg flex-shrink-0"
                 style="box-shadow:0 6px 16px rgba(255,107,74,.34)">
                {{ $step['n'] }}
            </div>
            <div>
                <div class="font-extrabold text-ink text-sm mb-1">{{ $step['title'] }}</div>
                <div class="text-ink-soft text-sm leading-relaxed font-medium">{{ $step['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ── CTA BOTTOM ───────────────────────────────────── --}}
<section class="px-6 md:px-16 pb-24">
    <div class="max-w-2xl mx-auto text-center bg-ink rounded-[28px] px-8 py-14"
         style="background:linear-gradient(150deg,#2E2620,#241D16);position:relative;overflow:hidden">
        {{-- Decorative circle --}}
        <div style="position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,107,74,.18)"></div>
        <div style="position:absolute;left:-40px;bottom:-60px;width:160px;height:160px;border-radius:50%;background:rgba(255,107,74,.10)"></div>

        <div class="relative">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-[20px] bg-coral mb-6"
                 style="box-shadow:0 8px 24px rgba(255,107,74,.45)">
                <svg width="32" height="32" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" fill="none" stroke="white" stroke-width="2"/>
                    <path d="M12 12V3M12 12l7 5.5" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="pt-num font-extrabold text-white mb-4"
                style="font-size:1.9rem;letter-spacing:-.02em">
                Cobain Patungan sekarang
            </h2>
            <p class="text-white/60 font-medium mb-8 leading-relaxed">
                Gratis untuk dipakai. Tidak ada biaya tersembunyi. <br>Langsung bisa buat event pertama kamu.
            </p>
            <a href="{{ route('register') }}"
               class="pt-btn pt-btn-primary inline-flex items-center gap-2 px-8 py-4 rounded-[16px] text-base font-bold">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Buat akun gratis
            </a>
        </div>
    </div>
</section>

{{-- ── FOOTER ───────────────────────────────────────── --}}
<footer class="border-t border-line px-8 md:px-16 py-8 flex items-center justify-between flex-wrap gap-4">
    <x-pt.brand size="sm" />
    <p class="text-muted text-sm font-semibold">© {{ date('Y') }} Patungan. Dibuat dengan ☕</p>
</footer>

</body>
</html>
