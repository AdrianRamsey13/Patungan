<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-6">

    {{-- Top bar --}}
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('events.show', $event) }}"
           class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-ink hover:bg-cream transition-colors"
           style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <span class="font-extrabold text-ink" style="font-size:16px">Tambah Pengeluaran</span>
        <div class="w-10"></div>
    </div>

    {{-- Event context chip --}}
    <div class="flex items-center gap-2.5 bg-white rounded-[14px] px-4 py-3 mb-5"
         style="box-shadow:0 1px 2px rgba(36,29,22,.05),0 4px 12px rgba(36,29,22,.05)">
        <x-pt.cat-icon :cat="$event->categoryIcon()" :accent="$event->accentColor()" :size="36" />
        <div>
            <div class="font-extrabold text-ink text-sm">{{ $event->name }}</div>
            <div class="text-muted text-xs font-semibold">{{ $event->eventMembers()->count() }} peserta</div>
        </div>
    </div>

    @php
        $allMembers    = $event->eventMembers()->with('user')->get();
        $totalMembers  = $allMembers->count();
        // Default payer: EventMember milik auth user
        $myMember      = $allMembers->firstWhere('user_id', Auth::id());
        $defaultPayerId = $myMember?->id;
    @endphp

    <form method="POST" action="{{ route('events.expenses.store', $event) }}"
          x-data="{
              total: '',
              payerMemberId: {{ $defaultPayerId ?? 'null' }},
              totalNum() { return parseInt(this.total.replace(/\D/g, '') || '0', 10); },
              formatInput() {
                  const d = this.total.replace(/\D/g, '');
                  this.total = d ? parseInt(d, 10).toLocaleString('id-ID') : '';
              },
              memberCount: {{ $totalMembers }},
              sharePerPerson() {
                  return this.memberCount > 0 && this.totalNum() > 0
                      ? Math.round(this.totalNum() / this.memberCount)
                      : 0;
              },
              formatRp(n) { return n > 0 ? 'Rp ' + n.toLocaleString('id-ID') : 'Rp 0'; }
          }">
        @csrf
        {{-- Hidden: kirim payer_member_id ke controller --}}
        <input type="hidden" name="payer_member_id" :value="payerMemberId">

        <div class="bg-white rounded-[22px] p-6 flex flex-col gap-5"
             style="box-shadow:0 2px 5px rgba(36,29,22,.04),0 14px 30px rgba(36,29,22,.07)">

            {{-- Siapa yang nalangin --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Siapa yang nalangin?</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($allMembers as $member)
                        @php
                            $isMe     = !$member->isGuest() && $member->user_id === Auth::id();
                            $label    = $isMe ? 'Kamu' : $member->displayName();
                        @endphp
                        <button type="button"
                                @click="payerMemberId = {{ $member->id }}"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-pill font-bold text-sm transition-colors"
                                :style="payerMemberId === {{ $member->id }}
                                    ? 'background:#FF6B4A;color:#fff;box-shadow:0 4px 12px rgba(255,107,74,.3)'
                                    : 'background:#FFF7EF;color:#6B6157;box-shadow:inset 0 0 0 1.5px #EADFCF'">
                            @if ($member->isGuest())
                                <span class="w-5 h-5 rounded-full bg-line text-muted flex items-center justify-center text-[9px] font-extrabold flex-shrink-0">
                                    {{ strtoupper(substr($member->guest_name, 0, 1)) }}
                                </span>
                            @else
                                <x-pt.avatar :name="$member->displayName()" size="xs" />
                            @endif
                            {{ $label }}
                            @if ($member->isGuest())
                                <span class="text-[9px] opacity-70 font-semibold">(tamu)</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                @error('payer_member_id') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Untuk apa?</label>
                <input type="text" name="description" value="{{ old('description') }}"
                       class="pt-input @error('description') ring-2 ring-red-400 @enderror"
                       placeholder="cth. Barang belanjaan, Makan siang, Bensin…"
                       required autofocus>
                @error('description') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Nominal --}}
            <div>
                <label class="block text-xs font-extrabold text-ink mb-2">Jumlah yang ditalangin</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-extrabold text-muted text-sm">Rp</span>
                    <input type="text" x-model="total" @input="formatInput()" @keydown.enter.prevent
                           inputmode="numeric" placeholder="0"
                           class="pt-input pl-11 pt-num font-bold text-lg">
                    <input type="hidden" name="amount" :value="totalNum()">
                </div>
                @error('amount') <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p> @enderror
            </div>

            {{-- Split preview --}}
            <div class="rounded-[14px] bg-cream px-4 py-3">
                <div class="flex items-center justify-between">
                    <span class="text-ink-soft text-sm font-semibold">
                        Split rata ke {{ $totalMembers }} peserta
                    </span>
                    <span class="pt-num font-extrabold text-ink" x-text="formatRp(sharePerPerson())"></span>
                </div>
                <div class="mt-3 flex items-center gap-2 flex-wrap">
                    @foreach ($allMembers as $member)
                        <div class="flex items-center gap-1.5 bg-white rounded-pill px-2 py-1"
                             style="box-shadow:0 1px 3px rgba(36,29,22,.07)">
                            @if ($member->isGuest())
                                <span class="w-4 h-4 rounded-full bg-line text-muted flex items-center justify-center text-[8px] font-extrabold flex-shrink-0">
                                    {{ strtoupper(substr($member->guest_name, 0, 1)) }}
                                </span>
                            @else
                                <x-pt.avatar :name="$member->displayName()" size="xs" />
                            @endif
                            <span class="text-ink text-xs font-bold">
                                {{ !$member->isGuest() && $member->user_id === Auth::id() ? 'Kamu' : $member->displayName() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="mt-4">
            <button type="submit"
                    :disabled="totalNum() <= 0 || !payerMemberId"
                    class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-4 text-base font-bold"
                    :class="totalNum() <= 0 || !payerMemberId ? 'opacity-50 cursor-not-allowed' : ''">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Pengeluaran
            </button>
        </div>
    </form>
</div>
</x-app-layout>
