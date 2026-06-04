<x-app-layout>
<div class="max-w-xl mx-auto px-4 py-6">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('dashboard') }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Buat Event Baru</span>
        <div class="w-10"></div>
    </div>

    <form method="POST" action="{{ route('events.store') }}"
          x-data="{
              cat: '{{ old('category', 'jalan') }}',
              selectedIds: {{ json_encode(old('members', [])) }},
              toggleMember(id) {
                  this.selectedIds.includes(id)
                      ? this.selectedIds = this.selectedIds.filter(i => i !== id)
                      : this.selectedIds.push(id);
              },
              // total semua peserta (kamu + selected users) — guest count di-track terpisah di child x-data
              totalUsers() { return this.selectedIds.length + 1; }
          }">
        @csrf

        <div class="bg-white rounded-[22px] p-6 flex flex-col gap-5"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Nama event --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Nama event</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="pt-input @error('name') ring-2 ring-red-400 @enderror"
                       placeholder="cth. Makan malam bareng" required autofocus>
                @error('name') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Kategori</label>
                <div class="flex gap-2 flex-wrap">
                    @php
                        $cats = [
                            ['key' => 'jalan',    'label' => 'Jalan',    'cat' => 'travel', 'accent' => 'sky',   'soft' => '#DDF0FD', 'ink' => '#1184D6', 'solid' => '#2BA8F4'],
                            ['key' => 'konsumsi', 'label' => 'Konsumsi', 'cat' => 'coffee', 'accent' => 'amber', 'soft' => '#FCEFD2', 'ink' => '#C77C05', 'solid' => '#F59E0B'],
                            ['key' => 'acara',    'label' => 'Acara',    'cat' => 'ball',   'accent' => 'coral', 'soft' => '#FFE7DF', 'ink' => '#E5512F', 'solid' => '#FF6B4A'],
                            ['key' => 'sewa',     'label' => 'Sewa',     'cat' => 'home',   'accent' => 'grape', 'soft' => '#ECE7FF', 'ink' => '#5E43E8', 'solid' => '#7B61FF'],
                            ['key' => 'kado',     'label' => 'Kado',     'cat' => 'gift',   'accent' => 'mint',  'soft' => '#DDF6EC', 'ink' => '#0B8A65', 'solid' => '#12B886'],
                        ];
                    @endphp
                    @foreach ($cats as $c)
                        <button type="button" @click="cat = '{{ $c['key'] }}'"
                                class="flex flex-col items-center gap-1 px-3 py-2.5 rounded-[14px] min-w-[64px] font-bold transition-all"
                                style="font-size:11.5px"
                                :style="cat === '{{ $c['key'] }}'
                                    ? 'background:{{ $c['soft'] }};color:{{ $c['ink'] }};box-shadow:inset 0 0 0 2px {{ $c['solid'] }}'
                                    : 'background:#FFF7EF;color:#6B6157;box-shadow:inset 0 0 0 1.5px #EADFCF'">
                            <x-pt.cat-icon :cat="$c['cat']" :accent="$c['accent']" :size="24" />
                            {{ $c['label'] }}
                        </button>
                        <input type="radio" name="category" value="{{ $c['key'] }}" class="hidden"
                               x-bind:checked="cat === '{{ $c['key'] }}'">
                    @endforeach
                </div>
                @error('category') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal (opsional) --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold text-ink mb-2">
                        Tanggal mulai <span class="font-normal text-muted">(opsional)</span>
                    </label>
                    <input type="date" name="date_start" value="{{ old('date_start') }}" class="pt-input text-sm">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-ink mb-2">
                        Tanggal selesai <span class="font-normal text-muted">(opsional)</span>
                    </label>
                    <input type="date" name="date_end" value="{{ old('date_end') }}" class="pt-input text-sm">
                </div>
            </div>

            {{-- Deskripsi (opsional) --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">
                    Deskripsi <span class="font-normal text-muted">(opsional)</span>
                </label>
                <textarea name="description" rows="2"
                          class="pt-input resize-none"
                          placeholder="Detail singkat tentang event ini…">{{ old('description') }}</textarea>
            </div>

            {{-- Peserta --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">
                    Peserta · <span x-text="selectedIds.length + 1"></span> orang
                </label>
                <div class="flex flex-wrap gap-2">
                    {{-- Kamu sendiri --}}
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-pill font-bold text-sm bg-coral text-white">
                        <x-pt.avatar :name="Auth::user()->name" size="xs" />
                        Kamu
                    </span>

                    @forelse ($users as $user)
                        <button type="button" @click="toggleMember({{ $user->id }})"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-pill font-bold text-sm transition-colors"
                                :style="selectedIds.includes({{ $user->id }})
                                    ? 'background:#241D16;color:#fff'
                                    : 'background:#FFF7EF;color:#6B6157;box-shadow:inset 0 0 0 1.5px #EADFCF'">
                            <x-pt.avatar :name="$user->name" size="xs" />
                            {{ explode(' ', $user->name)[0] }}
                        </button>
                        <input type="checkbox" name="members[]" value="{{ $user->id }}" class="hidden"
                               x-bind:checked="selectedIds.includes({{ $user->id }})">
                    @empty
                        <p class="text-muted text-sm font-semibold italic">
                            Belum ada user lain. Kamu bisa tambah anggota setelah event dibuat.
                        </p>
                    @endforelse
                </div>
                @error('members') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- ── Tambah orang lain (non-user) ── --}}
            <div class="border-t border-line pt-5"
                 x-data="{
                     guestCount: 0,
                     maxGuests: 30,
                     inc() { if (this.guestCount < this.maxGuests) this.guestCount++ },
                     dec() { if (this.guestCount > 0) this.guestCount-- },
                     setCount(v) {
                         const n = parseInt(v) || 0;
                         this.guestCount = Math.min(Math.max(n, 0), this.maxGuests);
                     }
                 }">

                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-xs font-extrabold text-ink">Tambah orang lain</div>
                        <div class="text-muted text-xs font-medium mt-0.5">
                            Peserta tanpa akun FunBill · maks. {{ 30 }} orang
                        </div>
                    </div>

                    {{-- Counter +/- --}}
                    <div class="flex items-center gap-1">
                        <button type="button" @click="dec()"
                                class="w-8 h-8 rounded-xl bg-cream border border-line flex items-center justify-center font-bold text-ink-soft hover:bg-white transition-colors"
                                :class="guestCount === 0 ? 'opacity-40 cursor-not-allowed' : ''">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                        </button>
                        <input type="number" min="0" max="30"
                               :value="guestCount"
                               @input="setCount($event.target.value)"
                               class="w-16 h-8 text-center font-extrabold text-ink text-sm rounded-xl border border-line bg-white focus:outline-none focus:ring-2 focus:ring-coral"
                               style="appearance:textfield;-moz-appearance:textfield"
                               onwheel="this.blur()">
                        <button type="button" @click="inc()"
                                class="w-8 h-8 rounded-xl bg-cream border border-line flex items-center justify-center font-bold text-coral hover:bg-white transition-colors"
                                :class="guestCount === maxGuests ? 'opacity-40 cursor-not-allowed' : ''">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Dynamic name fields --}}
                <div x-show="guestCount > 0" class="flex flex-col gap-2.5" style="display:none">
                    <template x-for="i in guestCount" :key="i">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-line flex items-center justify-center flex-shrink-0 text-muted text-xs font-extrabold"
                                 x-text="i"></div>
                            <input type="text"
                                   :name="'guests[' + (i-1) + '][name]'"
                                   :placeholder="'Nama tamu ' + i"
                                   class="pt-input text-sm flex-1"
                                   required>
                        </div>
                    </template>
                    <p class="text-muted text-xs font-semibold mt-1">
                        ⚠️ Nama wajib diisi — pembayaran tamu hanya bisa dikonfirmasi oleh creator event.
                    </p>
                </div>

                @error('guests') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
                @error('guests.*.name') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

        </div>

        {{-- Footer: total count + submit --}}
        <div class="mt-4 bg-white rounded-[18px] px-5 py-4"
             style="box-shadow:0 -4px 16px rgba(36,29,22,.06),0 2px 8px rgba(36,29,22,.04)">
            <p class="text-muted text-xs font-semibold mb-3">
                💡 Pengeluaran bisa ditambahkan setelah event dibuat oleh siapa saja yang ikut.
            </p>
            <button type="submit"
                    class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-4 text-base font-bold">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Buat Event
            </button>
        </div>

    </form>
</div>
</x-app-layout>
