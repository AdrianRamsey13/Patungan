<x-guest-layout>

    {{-- Heading --}}
    <div class="mb-8">
        <h1 class="pt-num font-extrabold text-ink mb-1" style="font-size:1.9rem;letter-spacing:-.03em">
            Buat akun baru 🎉
        </h1>
        <p class="text-ink-soft font-medium text-sm">
            Gratis selamanya. Tidak perlu kartu kredit.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
        @csrf

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-xs font-extrabold text-ink mb-2">Nama lengkap</label>
            <input id="name" type="text" name="name"
                   value="{{ old('name') }}"
                   class="pt-input @error('name') ring-2 ring-red-400 @enderror"
                   placeholder="Nama kamu"
                   required autofocus autocomplete="name">
            @error('name')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-extrabold text-ink mb-2">Email</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   class="pt-input @error('email') ring-2 ring-red-400 @enderror"
                   placeholder="nama@email.com"
                   required autocomplete="username">
            @error('email')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-extrabold text-ink mb-2">Password</label>
            <input id="password" type="password" name="password"
                   class="pt-input @error('password') ring-2 ring-red-400 @enderror"
                   placeholder="Min. 8 karakter"
                   required autocomplete="new-password">
            @error('password')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-extrabold text-ink mb-2">
                Konfirmasi password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="pt-input @error('password_confirmation') ring-2 ring-red-400 @enderror"
                   placeholder="Ulangi password"
                   required autocomplete="new-password">
            @error('password_confirmation')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-4 text-base font-bold mt-1">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
            Buat Akun
        </button>
    </form>

    {{-- Login link --}}
    <div class="mt-6 text-center">
        <span class="text-ink-soft text-sm font-medium">Sudah punya akun?</span>
        <a href="{{ route('login') }}"
           class="ml-1.5 text-sm font-bold text-coral hover:underline">
            Masuk di sini
        </a>
    </div>

</x-guest-layout>
