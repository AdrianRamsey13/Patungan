<x-app-layout>
<div class="px-6 md:px-8 py-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-end justify-between mb-6">
        <div>
            <h1 class="font-extrabold text-ink text-2xl" style="letter-spacing:-.02em">Riwayat Transaksi</h1>
            <p class="text-ink-soft text-sm font-medium mt-1">Semua pembayaran yang sudah terkonfirmasi.</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="text-muted text-sm font-bold hover:text-ink transition-colors flex items-center gap-1">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
            Dashboard
        </a>
    </div>

    @if ($history->isEmpty())
        <div class="bg-white rounded-[22px] px-6 py-14 text-center"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
            <div class="text-2xl mb-3">📭</div>
            <div class="font-extrabold text-ink text-lg mb-2">Belum ada transaksi</div>
            <p class="text-ink-soft text-sm font-medium">Transaksi akan muncul di sini setelah ada pembayaran yang dikonfirmasi.</p>
        </div>
    @else
        <div class="bg-white rounded-[22px] overflow-hidden"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            @foreach ($history as $i => $split)
                <x-pt.history-row :split="$split" :divider="$i > 0" />
            @endforeach

        </div>

        {{-- Laravel pagination --}}
        @if ($history->hasPages())
            <div class="mt-5 flex items-center justify-between">

                {{-- Prev --}}
                @if ($history->onFirstPage())
                    <span class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold opacity-40 cursor-not-allowed">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
                        Prev
                    </span>
                @else
                    <a href="{{ $history->previousPageUrl() }}"
                       class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
                        Prev
                    </a>
                @endif

                {{-- Page info --}}
                <span class="text-ink-soft text-sm font-bold">
                    {{ $history->currentPage() }} / {{ $history->lastPage() }}
                    <span class="text-muted font-semibold ml-1">({{ $history->total() }} transaksi)</span>
                </span>

                {{-- Next --}}
                @if ($history->hasMorePages())
                    <a href="{{ $history->nextPageUrl() }}"
                       class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold">
                        Next
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @else
                    <span class="pt-btn pt-btn-ghost flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-bold opacity-40 cursor-not-allowed">
                        Next
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M9 6l6 6-6 6"/></svg>
                    </span>
                @endif

            </div>
        @endif

    @endif
</div>
</x-app-layout>
