<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-6">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('notes.index') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Buat Note Baru</span>
        <div class="w-10"></div>
    </div>

    <form method="POST" action="{{ route('notes.store') }}"
          x-data="{ type: '{{ old('type', 'piutang') }}' }">
        @csrf

        <div class="bg-white rounded-[22px] p-6 flex flex-col gap-5"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Nama note</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="pt-input @error('name') ring-2 ring-red-400 @enderror"
                       placeholder="cth. Piutang ke Budi, Hutang ke Andi…"
                       required autofocus>
                @error('name') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Tipe: Hutang atau Piutang --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Tipe</label>
                <div class="grid grid-cols-2 gap-3">

                    {{-- Piutang --}}
                    <label @click="type = 'piutang'"
                           class="flex flex-col gap-2 p-4 rounded-[16px] cursor-pointer border-2 transition-all"
                           :class="type === 'piutang' ? 'border-mint bg-mint-soft' : 'border-line bg-cream'">
                        <input type="radio" name="type" value="piutang" class="hidden" :checked="type === 'piutang'">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-[10px] flex items-center justify-center flex-shrink-0"
                                 :class="type === 'piutang' ? 'bg-mint text-white' : 'bg-line text-muted'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                            </div>
                            <span class="font-extrabold text-sm" :class="type === 'piutang' ? 'text-mint-ink' : 'text-ink-soft'">Piutang</span>
                        </div>
                        <p class="text-xs font-medium leading-relaxed" :class="type === 'piutang' ? 'text-mint-ink' : 'text-muted'">
                            Orang lain yang minjem ke kamu — kamu yang nunggu diterima.
                        </p>
                    </label>

                    {{-- Hutang --}}
                    <label @click="type = 'hutang'"
                           class="flex flex-col gap-2 p-4 rounded-[16px] cursor-pointer border-2 transition-all"
                           :class="type === 'hutang' ? 'border-amber bg-amber-soft' : 'border-line bg-cream'">
                        <input type="radio" name="type" value="hutang" class="hidden" :checked="type === 'hutang'">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-[10px] flex items-center justify-center flex-shrink-0"
                                 :class="type === 'hutang' ? 'bg-amber text-white' : 'bg-line text-muted'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
                            </div>
                            <span class="font-extrabold text-sm" :class="type === 'hutang' ? 'text-amber-ink' : 'text-ink-soft'">Hutang</span>
                        </div>
                        <p class="text-xs font-medium leading-relaxed" :class="type === 'hutang' ? 'text-amber-ink' : 'text-muted'">
                            Kamu yang minjem ke orang lain — kamu yang punya kewajiban bayar.
                        </p>
                    </label>

                </div>
                @error('type') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi (opsional) --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">
                    Deskripsi <span class="font-normal text-muted">(opsional)</span>
                </label>
                <textarea name="description" rows="2" class="pt-input resize-none"
                          placeholder="Catatan tambahan tentang note ini…">{{ old('description') }}</textarea>
            </div>

        </div>

        <div class="mt-4">
            <button type="submit"
                    class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-4 text-base font-bold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Buat Note
            </button>
        </div>
    </form>
</div>
</x-app-layout>
