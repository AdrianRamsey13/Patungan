<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-6">

    @php
        $isAdd     = $entryType === 'tambah';
        $isHutang  = $note->type === 'hutang';
        $title     = $isAdd ? $note->addLabel() : $note->payLabel();
        $accentBg  = $isAdd ? ($isHutang ? '#FCEFD2' : '#DDF6EC') : '#DDF6EC';
        $accentFg  = $isAdd ? ($isHutang ? '#C77C05' : '#0B8A65') : '#0B8A65';
        $accentSolid = $isAdd ? ($isHutang ? '#F59E0B' : '#12B886') : '#12B886';
    @endphp

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('notes.show', $note) }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">{{ $title }}</span>
        <div class="w-10"></div>
    </div>

    {{-- Note context --}}
    <div class="flex items-center gap-3 bg-white rounded-[14px] px-4 py-3 mb-5"
         style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
        <div class="w-9 h-9 rounded-[11px] flex items-center justify-center flex-shrink-0"
             style="background:{{ $accentBg }};color:{{ $accentFg }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div>
            <div class="font-extrabold text-ink text-sm">{{ $note->name }}</div>
            <div class="text-muted text-xs font-semibold">{{ $note->typeLabel() }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('notes.entries.store', $note) }}"
          x-data="{
              amount: '',
              formatInput() {
                  const d = this.amount.replace(/\D/g, '');
                  this.amount = d ? parseInt(d, 10).toLocaleString('id-ID') : '';
              },
              totalNum() { return parseInt(this.amount.replace(/\D/g, '') || '0', 10); }
          }">
        @csrf
        <input type="hidden" name="entry_type" value="{{ $entryType }}">

        <div class="bg-white rounded-[22px] p-6 flex flex-col gap-5"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Tanggal --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Tanggal</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                       class="pt-input @error('date') ring-2 ring-red-400 @enderror" required>
                @error('date') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Keterangan</label>
                <input type="text" name="description" value="{{ old('description') }}"
                       class="pt-input @error('description') ring-2 ring-red-400 @enderror"
                       placeholder="{{ $isAdd ? 'cth. Pinjam bensin, Pinjam makan siang…' : 'cth. Transfer BCA, Bayar tunai…' }}"
                       required autofocus>
                @error('description') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-extrabold text-muted text-sm">Rp</span>
                    <input type="text" x-model="amount" @input="formatInput()" @keydown.enter.prevent
                           inputmode="numeric" placeholder="0"
                           class="pt-input pl-11 pt-num font-bold text-lg">
                    <input type="hidden" name="amount" :value="totalNum()">
                </div>
                @error('amount') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="mt-4">
            <button type="submit"
                    :disabled="totalNum() <= 0"
                    class="pt-btn w-full justify-center rounded-[14px] py-4 text-base font-bold transition-all"
                    :class="totalNum() <= 0 ? 'opacity-40 cursor-not-allowed bg-ink text-white' : ''"
                    :style="totalNum() > 0 ? 'background:{{ $accentSolid }};color:#fff;box-shadow:0 6px 16px color-mix(in srgb, {{ $accentSolid }} 40%, transparent)' : ''">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                    @if ($isAdd)
                        <path d="M12 5v14M5 12h14"/>
                    @else
                        <path d="M5 12.5l5 5 9-11"/>
                    @endif
                </svg>
                Simpan {{ $title }}
            </button>
        </div>
    </form>
</div>
</x-app-layout>
