<x-app-layout>
<div class="max-w-xl mx-auto px-4 py-6">

    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 text-red-600 font-semibold text-sm">{{ session('error') }}</div>
    @endif

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('events.show', $event) }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Edit Event</span>
        <div class="w-10"></div>
    </div>

    {{-- ── DETAIL EVENT ─────────────────────────────── --}}
    <form method="POST" action="{{ route('events.update', $event) }}"
          x-data="{ cat: '{{ $event->category }}' }">
        @csrf @method('PUT')

        <div class="bg-white rounded-[22px] p-6 flex flex-col gap-5 mb-4"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            <div class="font-extrabold text-ink text-sm mb-1">Detail Event</div>

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Nama event</label>
                <input type="text" name="name" value="{{ old('name', $event->name) }}"
                       class="pt-input @error('name') ring-2 ring-red-400 @enderror" required>
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
            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-extrabold text-ink mb-2">Tanggal mulai</label>
                    <input type="date" name="date_start"
                           value="{{ old('date_start', $event->date_start?->format('Y-m-d')) }}"
                           class="pt-input text-sm">
                </div>
                <div>
                    <label class="block text-xs font-extrabold text-ink mb-2">Tanggal selesai</label>
                    <input type="date" name="date_end"
                           value="{{ old('date_end', $event->date_end?->format('Y-m-d')) }}"
                           class="pt-input text-sm">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Deskripsi</label>
                <textarea name="description" rows="2" class="pt-input resize-none"
                          placeholder="Detail singkat…">{{ old('description', $event->description) }}</textarea>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Status Event</label>
                <div class="flex gap-2">
                    @foreach (['open' => 'Aktif', 'closed' => 'Selesai'] as $val => $label)
                        <label class="flex-1 flex items-center gap-2.5 px-4 py-3 rounded-[14px] cursor-pointer border-2 transition-colors
                                      {{ old('status', $event->status) === $val ? 'border-coral bg-coral-soft' : 'border-line bg-cream' }}">
                            <input type="radio" name="status" value="{{ $val }}"
                                   {{ old('status', $event->status) === $val ? 'checked' : '' }}
                                   class="accent-coral">
                            <span class="font-bold text-sm text-ink">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        <button type="submit"
                class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-3.5 text-sm font-bold mb-6">
            Simpan Perubahan
        </button>
    </form>

    {{-- ── KELOLA ANGGOTA ───────────────────────────── --}}
    <div class="bg-white rounded-[22px] overflow-hidden"
         style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

        <div class="px-5 py-4 border-b border-line">
            <div class="font-extrabold text-ink text-sm">Anggota Event</div>
            <div class="text-muted text-xs font-semibold mt-0.5">{{ $event->members->count() }} orang</div>
        </div>

        {{-- Daftar anggota saat ini --}}
        @foreach ($event->members as $member)
            @php $isCreator = $member->id === $event->created_by; @endphp
            <div class="flex items-center gap-3 px-5 py-3 {{ !$loop->first ? 'border-t border-line' : '' }}">
                <x-pt.avatar :name="$member->name" size="md" />
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-ink text-sm flex items-center gap-2">
                        {{ $member->id === Auth::id() ? 'Kamu' : $member->name }}
                        @if ($isCreator)
                            <span class="text-xs font-bold text-coral bg-coral-soft px-2 py-0.5 rounded-pill">creator</span>
                        @endif
                    </div>
                    <div class="text-muted text-xs font-semibold">{{ $member->email }}</div>
                </div>
                @if (!$isCreator)
                    <form method="POST"
                          action="{{ route('events.members.remove', [$event, $member]) }}"
                          id="remove-member-edit-{{ $member->id }}">
                        @csrf @method('DELETE')
                        <button type="button"
                                @click="ptConfirm({
                                    title: 'Keluarkan Anggota?',
                                    message: '{{ addslashes($member->name) }} akan dikeluarkan dari event ini.',
                                    confirmText: 'Ya, Keluarkan',
                                    isDanger: true
                                }).then(ok => ok && document.getElementById('remove-member-edit-{{ $member->id }}').submit())"
                                class="px-3 py-1.5 rounded-[10px] text-xs font-bold text-red-400 bg-red-50 hover:bg-red-100 transition-colors">
                            Keluarkan
                        </button>
                    </form>
                @endif
            </div>
        @endforeach

        {{-- Tambah anggota baru --}}
        @if ($event->status === 'open')
            <div class="border-t border-line px-5 py-4"
                 x-data="{ open: false, selectedIds: [] }">

                <button @click="open = !open"
                        class="flex items-center gap-2 text-coral text-sm font-bold">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                    <span x-text="open ? 'Batal' : 'Tambah anggota baru'"></span>
                </button>

                @if ($addable->isEmpty())
                    <p x-show="open" class="mt-2 text-muted text-xs font-semibold" style="display:none">
                        Tidak ada user lain yang bisa ditambahkan.
                    </p>
                @else
                    <form x-show="open" method="POST"
                          action="{{ route('events.members.add', $event) }}"
                          class="mt-4" style="display:none">
                        @csrf

                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($addable as $u)
                                <button type="button"
                                        @click="selectedIds.includes({{ $u->id }})
                                            ? selectedIds = selectedIds.filter(i => i !== {{ $u->id }})
                                            : selectedIds.push({{ $u->id }})"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-pill font-bold text-sm transition-colors"
                                        :style="selectedIds.includes({{ $u->id }})
                                            ? 'background:#241D16;color:#fff'
                                            : 'background:#FFF7EF;color:#6B6157;box-shadow:inset 0 0 0 1.5px #EADFCF'">
                                    <x-pt.avatar :name="$u->name" size="xs" />
                                    {{ explode(' ', $u->name)[0] }}
                                    <span class="text-xs opacity-60">{{ $u->email }}</span>
                                </button>
                                <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="hidden"
                                       x-bind:checked="selectedIds.includes({{ $u->id }})">
                            @endforeach
                        </div>

                        <button type="submit"
                                :disabled="selectedIds.length === 0"
                                class="pt-btn pt-btn-primary w-full justify-center rounded-xl py-3 text-sm font-bold"
                                :class="selectedIds.length === 0 ? 'opacity-40 cursor-not-allowed' : ''">
                            Tambah <span x-text="selectedIds.length > 0 ? selectedIds.length + ' anggota' : ''"></span>
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Hapus event --}}
    <div class="mt-6 text-center">
        <form method="POST" action="{{ route('events.destroy', $event) }}" id="delete-event-edit-form">
            @csrf @method('DELETE')
            <button type="button"
                    @click="ptConfirm({
                        title: 'Hapus Event?',
                        message: 'Event ini dan semua data pembayarannya akan hilang permanen.',
                        confirmText: 'Ya, Hapus',
                        isDanger: true
                    }).then(ok => ok && document.getElementById('delete-event-edit-form').submit())"
                    class="text-red-400 text-sm font-bold hover:text-red-600 transition-colors">
                Hapus event ini
            </button>
        </form>
    </div>

</div>
</x-app-layout>
