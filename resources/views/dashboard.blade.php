<x-app-layout>
<div class="px-4 md:px-8 py-6 max-w-[1100px] mx-auto"
     x-data="{
         eventPage: 1,
         historyPage: 1,
         eventPerPage: 6,
         historyPerPage: 10,
         init() {
             this.eventPerPage  = window.innerWidth >= 1024 ? 9999 : 6;
             this.historyPerPage = window.innerWidth >= 1024 ? 9999 : 10;
         },
         totalEventPages()   { return Math.max(1, Math.ceil({{ $events->count() }} / this.eventPerPage)); },
         totalHistoryPages() { return Math.max(1, Math.ceil({{ $history->count() }} / this.historyPerPage)); },
         showEvent(i)   { return i >= (this.eventPage   - 1) * this.eventPerPage   && i < this.eventPage   * this.eventPerPage; },
         showHistory(i) { return i >= (this.historyPage - 1) * this.historyPerPage && i < this.historyPage * this.historyPerPage; },
     }">

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Greeting + CTA --}}
    <div class="flex items-start justify-between mb-5 gap-3">
        <div class="min-w-0">
            <h1 class="text-ink font-extrabold text-2xl tracking-tight" style="letter-spacing:-.02em">
                Halo, {{ explode(' ', Auth::user()->name)[0] }} 👋
            </h1>
            <p class="text-ink-soft text-sm font-medium mt-1">
                Kamu ikut <b class="text-ink">{{ $events->count() }} event</b> split bill yang masih aktif.
            </p>
        </div>
        <a href="{{ route('events.create') }}"
           class="pt-btn pt-btn-primary flex-shrink-0 inline-flex items-center gap-2 px-4 py-3 rounded-[14px] text-sm font-bold">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            <span class="hidden sm:inline">Buat Event</span>
            <span class="sm:hidden">Buat</span>
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 md:gap-4 mb-6">
        <x-pt.summary-card type="owe"     :amount="$totalOwe"     :count="$oweCount" />
        <x-pt.summary-card type="receive" :amount="$totalReceive" :people="$receivePeople" />
    </div>

    {{-- ── MAIN LAYOUT ─────────────────────────────────────
         Mobile:  flex-col  (events → history, stacked)
         Desktop: flex-row  (events left, history sidebar right)
    ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-6 lg:flex-row lg:items-start">

        {{-- ── EVENTS SECTION ──────────────────────────── --}}
        <section class="w-full lg:flex-1 lg:min-w-0">

            {{-- Heading + Tab filter
                 Mobile:  centered column
                 Desktop: flex-row space-between  --}}
            <div class="flex flex-col items-center gap-3 mb-4 lg:flex-row lg:justify-between lg:items-center">
                <h2 class="font-extrabold text-ink text-center lg:text-left"
                    style="font-size:16.5px;letter-spacing:-.01em">
                    Event Split Bill
                </h2>
                <div class="flex gap-1 bg-white p-1 rounded-pill flex-shrink-0"
                     style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
                    @foreach (['aktif' => 'Aktif', 'selesai' => 'Selesai', 'semua' => 'Semua'] as $key => $label)
                        <a href="{{ route('dashboard', ['tab' => $key]) }}"
                           @click="eventPage = 1"
                           class="px-3 py-1.5 rounded-pill text-xs font-bold transition-colors"
                           style="{{ $tab === $key ? 'background:#241D16;color:#fff' : 'color:#6B6157' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($events->isEmpty())
                <div class="bg-white rounded-lg px-6 py-12 text-center"
                     style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
                    <div class="text-muted font-semibold text-sm">
                        Belum ada event {{ $tab === 'aktif' ? 'aktif' : ($tab === 'selesai' ? 'yang selesai' : '') }}.
                    </div>
                    <a href="{{ route('events.create') }}"
                       class="inline-block mt-3 text-coral font-bold text-sm hover:underline">
                        Buat event pertama →
                    </a>
                </div>
            @else
                {{-- Event grid: 2 col selalu --}}
                <div class="grid grid-cols-2 gap-3 md:gap-4">
                    @foreach ($events as $i => $event)
                        <div x-show="showEvent({{ $i }})" style="display:none">
                            <x-pt.event-card :event="$event" :userId="Auth::id()" />
                        </div>
                    @endforeach
                </div>

                {{-- Pagination events — hanya mobile, hanya kalau > 1 halaman --}}
                <div class="lg:hidden mt-4 flex items-center justify-between gap-2"
                     x-show="totalEventPages() > 1" style="display:none">
                    <button @click="eventPage = Math.max(1, eventPage - 1)"
                            :disabled="eventPage === 1"
                            class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-bold"
                            :class="eventPage === 1 ? 'opacity-40 cursor-not-allowed' : ''">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
                        Prev
                    </button>
                    <span class="text-ink-soft text-sm font-bold"
                          x-text="`${eventPage} / ${totalEventPages()}`"></span>
                    <button @click="eventPage = Math.min(totalEventPages(), eventPage + 1)"
                            :disabled="eventPage >= totalEventPages()"
                            class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-bold"
                            :class="eventPage >= totalEventPages() ? 'opacity-40 cursor-not-allowed' : ''">
                        Next
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                    </button>
                </div>
            @endif
        </section>

        {{-- ── HISTORY SECTION ─────────────────────────── --}}
        <aside class="w-full lg:w-[340px] lg:flex-shrink-0">
            <h2 class="font-extrabold text-ink mb-4"
                style="font-size:16.5px;letter-spacing:-.01em">
                Riwayat Transaksi
            </h2>

            <div class="bg-white rounded-[20px]"
                 style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
                @if ($history->isEmpty())
                    <div class="px-5 py-8 text-center text-muted text-sm font-semibold">
                        Belum ada transaksi.
                    </div>
                @else
                    @foreach ($history as $i => $split)
                        <div x-show="showHistory({{ $i }})" style="display:none">
                            <x-pt.history-row :split="$split" :divider="$i > 0" />
                        </div>
                    @endforeach

                    {{-- Pagination history — hanya mobile --}}
                    <div class="lg:hidden border-t border-line"
                         x-show="totalHistoryPages() > 1" style="display:none">
                        <div class="flex items-center justify-between px-4 py-3">
                            <button @click="historyPage = Math.max(1, historyPage - 1)"
                                    :disabled="historyPage === 1"
                                    class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-bold"
                                    :class="historyPage === 1 ? 'opacity-40 cursor-not-allowed' : ''">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
                                Prev
                            </button>
                            <span class="text-ink-soft text-sm font-bold"
                                  x-text="`${historyPage} / ${totalHistoryPages()}`"></span>
                            <button @click="historyPage = Math.min(totalHistoryPages(), historyPage + 1)"
                                    :disabled="historyPage >= totalHistoryPages()"
                                    class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-bold"
                                    :class="historyPage >= totalHistoryPages() ? 'opacity-40 cursor-not-allowed' : ''">
                                Next
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </aside>

    </div>
</div>
</x-app-layout>
