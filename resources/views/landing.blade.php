<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FunBill — Split bill bareng teman, gampang banget</title>

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
        FunBill makin seru
    </div>

    {{-- Headline --}}
    <h1 class="pt-num font-extrabold text-ink leading-[1.1] mb-6"
        style="font-size:clamp(2.4rem,6vw,3.8rem);letter-spacing:-.03em">
        Split bill bareng teman,<br>
        <span style="color:#FF6B4A">tanpa drama</span>.
    </h1>

    {{-- Subheadline --}}
    <p class="text-ink-soft font-medium text-lg leading-relaxed mb-10 max-w-xl mx-auto">
        Buat event, tambah peserta, input total biaya — FunBill hitung otomatis siapa harus bayar berapa ke siapa.
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

{{-- ── WALL OF EXCUSES + FAKE WHATSAPP ────────────────── --}}
<section class="px-6 md:px-16 pb-28 max-w-4xl mx-auto">

    {{-- Section header --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-white px-4 py-1.5 rounded-pill text-sm font-bold text-ink-soft mb-5"
             style="box-shadow:0 2px 8px rgba(36,29,22,.08)">
            😅 Terlalu Familiar
        </div>
        <h2 class="pt-num font-extrabold text-ink mb-3"
            style="font-size:clamp(1.6rem,4vw,2.2rem);letter-spacing:-.025em">
            Kalimat yang udah kamu hafal<br>di luar kepala
        </h2>
        <p class="text-ink-soft font-medium text-base max-w-md mx-auto">
            Setiap minggu, di setiap grup, selalu ada satu orang yang kirim ini.
        </p>
    </div>

    {{-- ── WALL OF EXCUSES ── --}}
    @php
        $excuses = [
            ['text' => '"Transfernya nanti ya."',           'sent' => false],
            ['text' => '"Aku lagi di jalan."',              'sent' => true],
            ['text' => '"QRIS-nya mana?"',                  'sent' => false],
            ['text' => '"Eh aku kira udah."',               'sent' => true],
            ['text' => '"Nanti malem ya."',                 'sent' => false],
            ['text' => '"Besok gajian nih."',               'sent' => true],
            ['text' => '"Sinyal jelek sorry 😅"',           'sent' => false],
            ['text' => '"HP aku mati seharian."',           'sent' => true],
            ['text' => '"Remind aku besok ya."',            'sent' => false],
            ['text' => '"Udah aku suruh adek transfer."',   'sent' => true],
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6"
         x-data="{
             active: -1,
             init() {
                 setTimeout(() => {
                     this.active = 0;
                     setInterval(() => {
                         this.active = Math.floor(Math.random() * {{ count($excuses) }});
                     }, 1700);
                 }, 600);
             }
         }">
        @foreach ($excuses as $i => $ex)
            <div class="rounded-[16px] px-4 py-3 text-sm font-bold transition-all duration-300 cursor-default select-none"
                 :class="{{ $i }} === active
                     ? 'scale-[1.04] -rotate-1 shadow-pop ring-2 ring-coral z-10'
                     : 'scale-100 rotate-0'"
                 style="{{ $ex['sent']
                     ? 'background:#DFF7D5;color:#1A7341;border:1.5px solid #B8EDCA'
                     : 'background:#FFFFFF;color:#241D16;border:1.5px solid #F1E8DC;box-shadow:0 2px 8px rgba(36,29,22,.06)' }}">
                <div class="flex items-start gap-2">
                    <span class="text-base flex-shrink-0 mt-0.5">{{ $ex['sent'] ? '↗' : '↙' }}</span>
                    <span class="leading-snug">{{ $ex['text'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bottom tagline excuses --}}
    <div class="text-center mb-16">
        <p class="text-ink-soft font-semibold text-sm">
            Dan grup WA kamu masih penuh <span class="text-coral font-bold">notifikasi belum dibaca.</span>
        </p>
    </div>

    {{-- ── FAKE WHATSAPP BEFORE/AFTER ── --}}
    <div class="text-center mb-10">
        <p class="text-muted font-bold text-sm tracking-wide uppercase" style="letter-spacing:.08em">Contoh nyatanya</p>
        <h3 class="pt-num font-extrabold text-ink mt-2" style="font-size:1.5rem;letter-spacing:-.02em">
            Sebelum vs Sesudah FunBill
        </h3>
    </div>

    <div class="grid md:grid-cols-2 gap-6 items-start md:items-start">

        {{-- ── SEBELUM: WA Chaos ── --}}
        <div>
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="bg-red-100 text-red-500 text-xs font-bold px-3 py-1 rounded-pill">😵 Sebelum FunBill</span>
            </div>

            {{-- Phone frame --}}
            <div class="mx-auto max-w-[280px] rounded-[36px] overflow-hidden"
                 style="border:3px solid #241D16;box-shadow:0 20px 48px rgba(36,29,22,.20)">

                {{-- WA Header --}}
                <div class="flex items-center gap-3 px-4 py-3"
                     style="background:linear-gradient(135deg,#075E54,#128C7E)">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-sm flex-shrink-0">🌴</div>
                    <div class="min-w-0 flex-1">
                        <div class="font-bold text-white text-sm truncate">Patungan Liburan Bali</div>
                        <div class="text-white/70 text-xs">Ramsey, Andi, Sinta, Budi + 2</div>
                    </div>
                    <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">7</div>
                </div>

                {{-- Chat messages — fixed height + scrollable, auto-scroll ke bawah --}}
                <div class="px-3 py-3 flex flex-col gap-2"
                     style="background:#ECE5DD;height:340px;overflow-y:auto;scroll-behavior:smooth"
                     x-data x-init="$el.scrollTop = $el.scrollHeight">
                    @php
                        $waMessages = [
                            ['name' => 'Ramsey', 'color' => '#FF6B4A', 'msg' => 'guys total tagihan makan malem brp?', 'time' => '20.14'],
                            ['name' => 'Andi',   'color' => '#2BA8F4', 'msg' => 'bentar aku hitung dulu', 'time' => '20.15'],
                            ['name' => 'Sinta',  'color' => '#7B61FF', 'msg' => 'aku bayar 150 tadi kan?', 'time' => '20.15'],
                            ['name' => 'Budi',   'color' => '#F59E0B', 'msg' => 'eh aku lupa pesen apa tadi 😅', 'time' => '20.16'],
                            ['name' => 'Ramsey', 'color' => '#FF6B4A', 'msg' => 'Andi gimana?? udah dihitung belum', 'time' => '20.18'],
                            ['name' => 'Sinta',  'color' => '#7B61FF', 'msg' => 'aku udah transfer loh', 'time' => '20.19'],
                            ['name' => 'Budi',   'color' => '#F59E0B', 'msg' => 'transfer ke siapa?', 'time' => '20.19'],
                            ['name' => 'Sinta',  'color' => '#7B61FF', 'msg' => 'ke Ramsey?', 'time' => '20.20'],
                            ['name' => 'Ramsey', 'color' => '#FF6B4A', 'msg' => 'belum masuk nih 😐', 'time' => '20.22'],
                        ];
                    @endphp

                    @foreach ($waMessages as $m)
                        <div class="max-w-[80%] {{ $loop->index % 3 === 0 ? 'self-end' : 'self-start' }}">
                            <div class="rounded-[12px] px-3 py-2 text-xs shadow-sm"
                                 style="{{ $loop->index % 3 === 0
                                     ? 'background:#DCF8C6;border-radius:12px 0 12px 12px'
                                     : 'background:#fff;border-radius:0 12px 12px 12px' }}">
                                @if ($loop->index % 3 !== 0)
                                    <div class="font-bold text-[11px] mb-0.5" style="color:{{ $m['color'] }}">
                                        {{ $m['name'] }}
                                    </div>
                                @endif
                                <div class="text-gray-800 leading-snug">{{ $m['msg'] }}</div>
                                <div class="text-right text-gray-400 text-[10px] mt-0.5">{{ $m['time'] }}</div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Typing indicator --}}
                    <div class="self-start">
                        <div class="bg-white rounded-[12px] px-3 py-2 shadow-sm inline-flex items-center gap-1.5">
                            <span class="text-[11px] text-gray-500">Andi mengetik</span>
                            <div class="flex gap-0.5">
                                @foreach([0, 1, 2] as $d)
                                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay:{{ $d * 0.15 }}s"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- WA Input bar --}}
                <div class="flex items-center gap-2 px-3 py-2" style="background:#F0F0F0">
                    <div class="flex-1 bg-white rounded-full px-3 py-1.5 text-xs text-gray-400">Ketik pesan</div>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center"
                         style="background:linear-gradient(135deg,#075E54,#128C7E)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SESUDAH: FunBill Clean ── --}}
        <div>
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="bg-mint-soft text-mint-ink text-xs font-bold px-3 py-1 rounded-pill">✅ Sesudah FunBill</span>
            </div>

            {{-- Phone frame --}}
            <div class="mx-auto max-w-[280px] rounded-[36px] overflow-hidden"
                 style="border:3px solid #241D16;box-shadow:0 20px 48px rgba(36,29,22,.20);background:#FFF7EF">

                {{-- FunBill Header --}}
                <div class="flex items-center gap-3 px-4 py-3 bg-white border-b border-line">
                    <div class="w-9 h-9 rounded-[12px] bg-sky-soft flex items-center justify-center flex-shrink-0">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2BA8F4" stroke-width="2" stroke-linecap="round"><path d="M21.5 3.5L11 14M21.5 3.5l-6.6 17.5-3.9-7.5-7.5-3.9L21.5 3.5z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-extrabold text-ink text-sm truncate">Patungan Liburan Bali</div>
                        <div class="text-muted text-xs font-semibold">6 anggota · Rp 850.000</div>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-coral flex items-center justify-center">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    </div>
                </div>

                {{-- FunBill content --}}
                <div class="px-3 py-3 flex flex-col gap-2.5" style="height:340px;overflow-y:auto">

                    {{-- Summary card --}}
                    <div class="rounded-[14px] px-3 py-2.5" style="background:linear-gradient(135deg,#2BA8F4,#1184D6)">
                        <div class="text-white/80 text-[10px] font-bold">Per orang</div>
                        <div class="text-white font-extrabold text-xl pt-num">Rp 141.667</div>
                        <div class="text-white/70 text-[10px] font-semibold mt-0.5">Dibagi rata 6 orang</div>
                    </div>

                    {{-- Members --}}
                    <div class="bg-white rounded-[14px] overflow-hidden" style="box-shadow:0 1px 4px rgba(36,29,22,.08)">
                        @php
                            $funMembers = [
                                ['name' => 'Ramsey', 'color' => '#FF6B4A', 'paid' => true,  'note' => 'nalangin'],
                                ['name' => 'Andi',   'color' => '#2BA8F4', 'paid' => true,  'note' => null],
                                ['name' => 'Sinta',  'color' => '#7B61FF', 'paid' => true,  'note' => null],
                                ['name' => 'Budi',   'color' => '#F59E0B', 'paid' => false, 'note' => null],
                                ['name' => 'Dina',   'color' => '#12B886', 'paid' => true,  'note' => null],
                                ['name' => 'Kiki',   'color' => '#EC5F9E', 'paid' => false, 'note' => null],
                            ];
                        @endphp
                        @foreach ($funMembers as $m)
                            <div class="flex items-center gap-2 px-3 py-2 {{ !$loop->first ? 'border-t border-line' : '' }}">
                                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-white text-[9px] font-extrabold"
                                     style="background:{{ $m['color'] }}">
                                    {{ strtoupper(substr($m['name'], 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-ink text-xs font-bold flex items-center gap-1.5">
                                        {{ $m['name'] }}
                                        @if ($m['note'])
                                            <span class="text-[9px] bg-coral-soft text-coral px-1.5 py-0 rounded-pill font-bold">{{ $m['note'] }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($m['paid'])
                                    <span class="flex items-center gap-1 bg-mint-soft text-mint-ink rounded-pill px-2 py-0.5 text-[10px] font-bold flex-shrink-0">
                                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                                        Lunas
                                    </span>
                                @else
                                    <span class="bg-amber-soft text-amber-ink rounded-pill px-2 py-0.5 text-[10px] font-bold flex-shrink-0">
                                        Belum
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Progress --}}
                    <div class="bg-white rounded-[14px] px-3 py-2.5" style="box-shadow:0 1px 4px rgba(36,29,22,.06)">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-ink text-xs font-bold">Progress</span>
                            <span class="text-xs font-bold"><b class="text-mint-ink">4</b>/6 lunas</span>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden" style="background:#F0E8DC">
                            <div class="h-full rounded-full" style="width:67%;background:linear-gradient(90deg,#12B886,#0B8A65)"></div>
                        </div>
                    </div>
                </div>

                {{-- Bottom bar --}}
                <div class="px-4 py-3 bg-white border-t border-line">
                    <div class="bg-coral text-white rounded-[12px] py-2 text-center text-xs font-bold">
                        ✓ Tandai Lunas
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- CTA line --}}
    <div class="text-center mt-12">
        <p class="text-ink-soft font-medium mb-4">Cukup dramanya. Beres-beresin dengan FunBill.</p>
        <a href="{{ route('register') }}"
           class="pt-btn pt-btn-primary inline-flex items-center gap-2 px-7 py-3.5 rounded-[16px] text-sm font-bold">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Coba FunBill Gratis
        </a>
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
            ['icon' => 'M12 5v14M5 12h14',             'color' => '#FF6B4A', 'bg' => '#FFE7DF', 'title' => 'Buat event dalam hitungan detik',     'desc' => 'Nama, kategori, total biaya, pilih teman — selesai. FunBill langsung hitung per orang.'],
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
            ['n' => '2', 'title' => 'Pilih peserta',      'desc' => 'Tambahkan teman-teman yang ikut split bill.'],
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

{{-- ── DEVELOPER SECTION ───────────────────────────────── --}}
<section class="px-6 md:px-16 pb-24 max-w-4xl mx-auto"
         x-data="{
             visible: false,
             floatDone: false,
             init() {
                 const io = new IntersectionObserver(([e]) => {
                     if (e.isIntersecting) {
                         this.visible = true;
                         setTimeout(() => this.floatDone = true, 750);
                         io.disconnect();
                     }
                 }, { threshold: 0.25 });
                 io.observe(this.$el);
             }
         }">

    {{-- Section label --}}
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-grape-soft text-grape px-4 py-1.5 rounded-pill text-sm font-bold mb-4">
            <span>👨‍💻</span> Tentang Developernya
        </div>
        <h2 class="pt-num font-extrabold text-ink" style="font-size:2rem;letter-spacing:-.02em">
            Dibuat dengan sepenuh hati
        </h2>
    </div>

    <div class="bg-white rounded-[28px] overflow-hidden"
         style="box-shadow:0 10px 24px rgba(36,29,22,.08),0 40px 80px rgba(36,29,22,.10)">
        <div class="grid md:grid-cols-[1fr_300px] items-end">

            {{-- Kiri: Info developer --}}
            <div class="p-8 md:p-10">

                {{-- Nama + title --}}
                <div class="mb-6" :class="visible ? 'dev-info-1' : 'opacity-0'">
                    <div class="text-muted font-bold text-sm mb-1 tracking-wide">DEVELOPER</div>
                    <h3 class="pt-num font-extrabold text-ink" style="font-size:2.2rem;letter-spacing:-.03em;line-height:1.1">
                        Ramsey Adrian
                    </h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-2 h-2 rounded-full bg-mint"></div>
                        <span class="text-ink-soft font-semibold text-sm">Full-stack Developer</span>
                    </div>
                </div>

                {{-- Bio --}}
                <p class="text-ink-soft font-medium text-sm leading-relaxed mb-6"
                   :class="visible ? 'dev-info-2' : 'opacity-0'">
                    Suka bikin produk yang beneran dipake orang. FunBill lahir dari frustrasi pribadi — capek chat sana-sini ngitung siapa utang berapa. Jadi ya, bikin sendiri aja.
                </p>

                {{-- Stack / skills chips --}}
                <div class="flex flex-wrap gap-2 mb-8" :class="visible ? '' : 'opacity-0'">
                    @foreach ([
                        ['label' => 'Laravel',    'bg' => '#FFE7DF', 'fg' => '#E5512F', 'n' => 1],
                        ['label' => 'Alpine.js',  'bg' => '#DDF0FD', 'fg' => '#1184D6', 'n' => 2],
                        ['label' => 'Tailwind',   'bg' => '#DDF6EC', 'fg' => '#0B8A65', 'n' => 3],
                        ['label' => 'MySQL',      'bg' => '#ECE7FF', 'fg' => '#5E43E8', 'n' => 4],
                        ['label' => 'Blade',      'bg' => '#FCEFD2', 'fg' => '#C77C05', 'n' => 5],
                    ] as $tag)
                        <span class="dev-tag-{{ $tag['n'] }} px-3 py-1.5 rounded-pill font-bold text-xs"
                              style="background:{{ $tag['bg'] }};color:{{ $tag['fg'] }}">
                            {{ $tag['label'] }}
                        </span>
                    @endforeach
                </div>

                {{-- Fun facts --}}
                <div class="grid grid-cols-1 gap-2.5" :class="visible ? 'dev-info-3' : 'opacity-0'">
                    @foreach ([
                        ['emoji' => '☕', 'text' => 'Bukan manusia sebelum kopi pagi'],
                        ['emoji' => '🎧', 'text' => 'Coding paling produktif pas lo-fi beats'],
                        ['emoji' => '🌙', 'text' => 'Bug paling banyak ketemu jam 2 malem'],
                    ] as $fact)
                        <div class="flex items-center gap-3 bg-cream rounded-[12px] px-4 py-2.5">
                            <span class="text-lg flex-shrink-0">{{ $fact['emoji'] }}</span>
                            <span class="text-ink-soft font-semibold text-sm">{{ $fact['text'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Contact --}}
                <div class="mt-6" :class="visible ? 'dev-info-4' : 'opacity-0'">
                    <a href="mailto:adriansey13@gmail.com"
                       class="pt-btn pt-btn-ghost inline-flex items-center gap-2 px-5 py-2.5 rounded-pill text-sm font-bold">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        Hubungi Saya
                    </a>
                </div>

            </div>

            {{-- Kanan: Karakter 3D + Easter Egg --}}
            <div class="relative flex items-end justify-center h-[340px] md:h-[420px] overflow-visible"
                 x-data="{
                     angry: false,
                     shaking: false,
                     bubbleState: 'hidden',   {{-- hidden | in | idle | out --}}
                     timer: null,
                     poke() {
                         if (this.angry) return;

                         // 1. shake + swap ke marah
                         this.shaking = true;
                         this.angry = true;

                         // 2. speech bubble muncul setelah gambar ganti
                         setTimeout(() => {
                             this.shaking = false;
                             this.bubbleState = 'in';
                         }, 120);

                         // 3. bubble masuk animasi idle setelah pop-in
                         setTimeout(() => { this.bubbleState = 'idle'; }, 480);

                         // 4. bubble keluar setelah 2.6 detik
                         clearTimeout(this.timer);
                         this.timer = setTimeout(() => {
                             this.bubbleState = 'out';
                             // 5. revert ke normal
                             setTimeout(() => {
                                 this.angry = false;
                                 this.bubbleState = 'hidden';
                             }, 280);
                         }, 2600);
                     }
                 }">

                {{-- Background blob dekoratif --}}
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-56 h-56 rounded-full"
                     style="background:radial-gradient(circle,#FFE7DF 0%,rgba(255,231,223,0) 70%)"></div>

                {{-- Speech bubble --}}
                <div class="absolute z-20 pointer-events-none"
                     style="top: 24px; left: 50%; transform: translateX(-130px);"
                     x-show="bubbleState !== 'hidden'"
                     :class="{
                         'bubble-in':  bubbleState === 'in',
                         'bubble-idle': bubbleState === 'idle',
                         'bubble-out': bubbleState === 'out',
                     }"
                     style="display:none">
                    <div class="relative bg-white rounded-[18px] px-4 py-3 text-center"
                         style="box-shadow:0 8px 24px rgba(36,29,22,.14);border:2px solid #F1E8DC;max-width:160px">
                        <span class="font-extrabold text-ink text-sm leading-tight block"
                              style="font-family:'Baloo 2',cursive">
                            ngapain pegang2 ??!!
                        </span>
                        {{-- Bubble tail (panah ke bawah-kanan ke arah karakter) --}}
                        <div class="absolute -bottom-[10px] right-8 w-4 h-4 bg-white rotate-45"
                             style="border-right:2px solid #F1E8DC;border-bottom:2px solid #F1E8DC;clip-path:polygon(0 0,100% 0,100% 100%)"></div>
                    </div>
                </div>

                {{-- Karakter (clickable!) --}}
                <img :src="angry ? '{{ asset('images/ramsey-angry.png') }}' : '{{ asset('images/ramsey.png') }}'"
                     alt="Ramsey Adrian"
                     class="relative z-10 select-none transition-[filter] duration-150"
                     style="height:380px;max-height:95%;object-fit:contain;object-position:bottom;
                            filter:drop-shadow(0 20px 40px rgba(36,29,22,.18));cursor:pointer"
                     :class="[
                         visible ? (floatDone && !angry ? 'dev-char-float' : (visible ? 'dev-char-enter' : '')) : 'opacity-0 translate-y-16',
                         shaking ? 'char-shake' : ''
                     ]"
                     @click="poke()"
                     draggable="false"
                     title="Coba klik...">

                {{-- Shadow bawah karakter --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-28 h-4 rounded-full"
                     style="background:rgba(36,29,22,.12);filter:blur(10px)"></div>
            </div>

        </div>
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
                Cobain FunBill sekarang
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
    <p class="text-muted text-sm font-semibold">© {{ date('Y') }} FunBill. Dibuat dengan ☕</p>
</footer>

</body>
</html>
