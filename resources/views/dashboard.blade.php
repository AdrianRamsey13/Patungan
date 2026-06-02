<x-app-layout>
<div class="px-6 md:px-8 py-6 max-w-[1100px]">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-mint-soft text-mint-ink font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Greeting + CTA --}}
    <div class="flex items-end justify-between mb-5">
        <div>
            <h1 class="text-ink font-extrabold text-2xl tracking-tight" style="letter-spacing:-.02em">
                Halo, {{ explode(' ', Auth::user()->name)[0] }} 👋
            </h1>
            <p class="text-ink-soft text-sm font-medium mt-1">
                Kamu ikut <b class="text-ink">{{ $events->count() }} event</b> split bill yang masih aktif.
            </p>
        </div>
        <a href="{{ route('events.create') }}"
           class="pt-btn pt-btn-primary inline-flex items-center gap-2 px-5 py-3.5 rounded-[14px] text-sm font-bold">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Buat Event
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <x-pt.summary-card type="owe"     :amount="$totalOwe"     :count="$oweCount" />
        <x-pt.summary-card type="receive" :amount="$totalReceive" :people="$receivePeople" />
    </div>

    {{-- Main 2-column layout --}}
    <div class="grid gap-6" style="grid-template-columns:1fr 340px;align-items:start">

        {{-- Kiri: Event list --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-extrabold text-ink" style="font-size:16.5px;letter-spacing:-.01em">Event Split Bill</h2>

                {{-- Tab filter --}}
                <div class="flex gap-1 bg-white p-1 rounded-pill" style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
                    @foreach (['aktif' => 'Aktif', 'selesai' => 'Selesai', 'semua' => 'Semua'] as $key => $label)
                        <a href="{{ route('dashboard', ['tab' => $key]) }}"
                           class="px-4 py-1.5 rounded-pill text-xs font-bold transition-colors"
                           style="{{ $tab === $key ? 'background:#241D16;color:#fff' : 'color:#6B6157' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($events->isEmpty())
                <div class="bg-white rounded-lg px-6 py-12 text-center" style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
                    <div class="text-muted font-semibold text-sm">Belum ada event {{ $tab === 'aktif' ? 'aktif' : ($tab === 'selesai' ? 'yang selesai' : '') }}.</div>
                    <a href="{{ route('events.create') }}" class="inline-block mt-3 text-coral font-bold text-sm hover:underline">Buat event pertama →</a>
                </div>
            @else
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($events as $event)
                        <x-pt.event-card :event="$event" :userId="Auth::id()" />
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Kanan: Riwayat Transaksi --}}
        <section>
            <h2 class="font-extrabold text-ink mb-4" style="font-size:16.5px;letter-spacing:-.01em">Riwayat Transaksi</h2>

            <div class="bg-white rounded-[20px]" style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
                @if ($history->isEmpty())
                    <div class="px-5 py-8 text-center text-muted text-sm font-semibold">
                        Belum ada transaksi.
                    </div>
                @else
                    @foreach ($history as $i => $split)
                        <x-pt.history-row :split="$split" :divider="$i > 0" />
                    @endforeach
                @endif
            </div>
        </section>

    </div>
</div>
</x-app-layout>
