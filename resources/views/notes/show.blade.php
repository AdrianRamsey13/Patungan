<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-6">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">{{ session('success') }}</div>
    @endif

    @php
        $isHutang  = $note->type === 'hutang';
        $typeColor = $isHutang
            ? ['bg' => '#FCEFD2', 'fg' => '#C77C05', 'solid' => '#F59E0B', 'soft' => 'amber']
            : ['bg' => '#DDF6EC', 'fg' => '#0B8A65', 'solid' => '#12B886', 'soft' => 'mint'];
    @endphp

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('notes.index') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Detail Note</span>
        {{-- Hapus note --}}
        <form method="POST" action="{{ route('notes.destroy', $note) }}" id="delete-note-form">
            @csrf @method('DELETE')
            <button type="button"
                    @click="ptConfirm({
                        title: 'Hapus Note?',
                        message: 'Semua data catatan dan pembayaran akan hilang permanen.',
                        confirmText: 'Ya, Hapus',
                        isDanger: true
                    }).then(ok => ok && document.getElementById('delete-note-form').submit())"
                    class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-red-300 hover:bg-red-50 hover:text-red-400 transition-colors"
                    style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Note header --}}
    <div class="bg-white rounded-[22px] p-5 mb-4"
         style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">
        <div class="flex items-start gap-3">
            <div class="w-11 h-11 rounded-[14px] flex items-center justify-center flex-shrink-0"
                 style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['fg'] }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-extrabold text-ink text-lg leading-tight">{{ $note->name }}</h1>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-pill"
                          style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['fg'] }}">
                        {{ $note->typeLabel() }}
                    </span>
                </div>
                @if ($note->description)
                    <p class="text-ink-soft text-sm mt-1 leading-relaxed">{{ $note->description }}</p>
                @endif
            </div>
        </div>

        {{-- Summary bar --}}
        <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-line">
            <div class="text-center">
                <div class="text-muted text-xs font-semibold mb-1">
                    Total {{ $isHutang ? 'hutang' : 'piutang' }}
                </div>
                <div class="pt-num font-extrabold text-ink text-base">
                    Rp {{ number_format($totalAdded, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-center border-x border-line">
                <div class="text-muted text-xs font-semibold mb-1">Dibayar</div>
                <div class="pt-num font-extrabold text-mint-ink text-base">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-center">
                <div class="text-muted text-xs font-semibold mb-1">Sisa</div>
                <div class="pt-num font-extrabold text-base {{ $balance > 0 ? ($isHutang ? 'text-amber-ink' : 'text-coral') : 'text-mint-ink' }}">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if ($balance === 0 && $totalAdded > 0)
            <div class="mt-3 flex items-center justify-center gap-2 bg-mint-soft rounded-[12px] px-4 py-2">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B8A65" stroke-width="2.6" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
                <span class="text-mint-ink text-sm font-bold">Lunas! 🎉</span>
            </div>
        @endif
    </div>

    {{-- Action buttons --}}
    <div class="flex gap-3 mb-6">
        <a href="{{ route('notes.entries.create', [$note, 'tambah']) }}"
           class="pt-btn pt-btn-primary flex-1 justify-center rounded-[13px] py-3 text-sm font-bold">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            {{ $note->addLabel() }}
        </a>
        <a href="{{ route('notes.entries.create', [$note, 'bayar']) }}"
           class="pt-btn pt-btn-ghost flex-1 justify-center rounded-[13px] py-3 text-sm font-bold">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12.5l5 5 9-11"/></svg>
            {{ $note->payLabel() }}
        </a>
    </div>

    {{-- ── TABEL CATATAN ────────────────────────────────── --}}
    <div class="mb-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-extrabold text-ink" style="font-size:14px">
                {{ $isHutang ? '📋 Catatan Hutang' : '📋 Catatan Piutang' }}
            </h2>
            <span class="text-muted text-xs font-semibold">{{ $addEntries->count() }} entri</span>
        </div>

        @if ($addEntries->isEmpty())
            <div class="bg-white rounded-[16px] px-5 py-6 text-center text-muted text-sm font-semibold"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.05)">
                Belum ada catatan. Klik "{{ $note->addLabel() }}" untuk mulai.
            </div>
        @else
            <div class="bg-white rounded-[16px] overflow-hidden"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.05),0 6px 16px rgba(36,29,22,.05)">
                {{-- Header tabel --}}
                <div class="grid px-4 py-2.5 text-xs font-extrabold text-muted border-b border-line"
                     style="grid-template-columns:100px 1fr 110px 36px">
                    <span>Tanggal</span>
                    <span>Keterangan</span>
                    <span class="text-right">Jumlah</span>
                    <span></span>
                </div>
                {{-- Rows --}}
                @foreach ($addEntries as $entry)
                    <div class="grid items-center px-4 py-3 {{ !$loop->first ? 'border-t border-line' : '' }}"
                         style="grid-template-columns:100px 1fr 110px 36px">
                        <span class="text-ink-soft text-xs font-semibold">
                            {{ $entry->date->format('d M Y') }}
                        </span>
                        <span class="text-ink text-sm font-semibold truncate pr-2">{{ $entry->description }}</span>
                        <span class="pt-num font-extrabold text-right"
                              style="color:{{ $typeColor['fg'] }};font-size:13px">
                            +Rp {{ number_format($entry->amount, 0, ',', '.') }}
                        </span>
                        <form method="POST"
                              action="{{ route('notes.entries.destroy', [$note, $entry]) }}"
                              id="del-add-{{ $entry->id }}">
                            @csrf @method('DELETE')
                            <button type="button"
                                    @click="ptConfirm({ title: 'Hapus entri?', message: '{{ addslashes($entry->description) }}', confirmText: 'Hapus', isDanger: true }).then(ok => ok && document.getElementById('del-add-{{ $entry->id }}').submit())"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-muted hover:bg-red-50 hover:text-red-400 transition-colors ml-auto">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
                {{-- Total catatan --}}
                <div class="flex items-center justify-between px-4 py-3 border-t-2 border-line bg-cream">
                    <span class="font-extrabold text-ink text-sm">Total {{ $isHutang ? 'hutang' : 'piutang' }}</span>
                    <span class="pt-num font-extrabold" style="color:{{ $typeColor['fg'] }};font-size:15px">
                        Rp {{ number_format($totalAdded, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- ── TABEL PEMBAYARAN ─────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-extrabold text-ink" style="font-size:14px">
                {{ $isHutang ? '💸 Pelunasan yang Sudah Dibayar' : '💰 Pembayaran yang Diterima' }}
            </h2>
            <span class="text-muted text-xs font-semibold">{{ $payEntries->count() }} entri</span>
        </div>

        @if ($payEntries->isEmpty())
            <div class="bg-white rounded-[16px] px-5 py-6 text-center text-muted text-sm font-semibold"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.05)">
                Belum ada pembayaran tercatat.
            </div>
        @else
            <div class="bg-white rounded-[16px] overflow-hidden"
                 style="box-shadow:0 1px 3px rgba(36,29,22,.05),0 6px 16px rgba(36,29,22,.05)">
                <div class="grid px-4 py-2.5 text-xs font-extrabold text-muted border-b border-line"
                     style="grid-template-columns:100px 1fr 110px 36px">
                    <span>Tanggal</span>
                    <span>Keterangan</span>
                    <span class="text-right">Jumlah</span>
                    <span></span>
                </div>
                @foreach ($payEntries as $entry)
                    <div class="grid items-center px-4 py-3 {{ !$loop->first ? 'border-t border-line' : '' }}"
                         style="grid-template-columns:100px 1fr 110px 36px">
                        <span class="text-ink-soft text-xs font-semibold">{{ $entry->date->format('d M Y') }}</span>
                        <span class="text-ink text-sm font-semibold truncate pr-2">{{ $entry->description }}</span>
                        <span class="pt-num font-extrabold text-mint-ink text-right" style="font-size:13px">
                            −Rp {{ number_format($entry->amount, 0, ',', '.') }}
                        </span>
                        <form method="POST"
                              action="{{ route('notes.entries.destroy', [$note, $entry]) }}"
                              id="del-pay-{{ $entry->id }}">
                            @csrf @method('DELETE')
                            <button type="button"
                                    @click="ptConfirm({ title: 'Hapus pembayaran?', message: '{{ addslashes($entry->description) }}', confirmText: 'Hapus', isDanger: true }).then(ok => ok && document.getElementById('del-pay-{{ $entry->id }}').submit())"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-muted hover:bg-red-50 hover:text-red-400 transition-colors ml-auto">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
                <div class="flex items-center justify-between px-4 py-3 border-t-2 border-line bg-mint-soft">
                    <span class="font-extrabold text-mint-ink text-sm">Total dibayar</span>
                    <span class="pt-num font-extrabold text-mint-ink" style="font-size:15px">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif
    </div>

</div>
</x-app-layout>
