<header class="bg-white border-b border-line flex items-center gap-4 px-7 h-[66px] sticky top-0 z-30">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}">
        <x-pt.brand />
    </a>

    <div class="flex-1"></div>

    {{-- Search (dekoratif untuk sekarang) --}}
    <div class="hidden md:flex items-center gap-2 bg-cream rounded-pill px-4 py-2 w-56 text-muted select-none cursor-text">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>
        </svg>
        <span class="text-sm font-semibold">Cari event…</span>
    </div>

    {{-- Notifikasi --}}
    <button class="relative w-10 h-10 rounded-[13px] bg-cream text-ink-soft flex items-center justify-center hover:bg-white transition-colors">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>
        </svg>
        {{-- Dot merah notifikasi --}}
        <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-coral" style="box-shadow:0 0 0 2px #fff"></span>
    </button>

    {{-- Avatar + nama + dropdown --}}
    <div x-data="{ open: false }" class="relative flex items-center gap-2.5">
        <button @click="open = !open" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity">
            <x-pt.avatar :name="Auth::user()->name" size="md" ring="#FFF7EF" />
            <div class="hidden md:block text-left leading-tight">
                <div class="font-extrabold text-sm text-ink">{{ Str::limit(Auth::user()->name, 14) }}</div>
            </div>
        </button>

        {{-- Dropdown --}}
        <div x-show="open" @click.outside="open = false" x-transition
             class="absolute right-0 top-full mt-2 w-44 bg-white rounded-lg py-1"
             style="box-shadow:0 10px 24px rgba(36,29,22,.12);border:1px solid #F1E8DC">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-ink hover:bg-cream transition-colors">
                Profil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-ink hover:bg-cream transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </div>

</header>
