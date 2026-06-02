<x-guest-layout>

    {{-- Heading --}}
    <div class="mb-8">
        <h1 class="pt-num font-extrabold text-ink mb-1" style="font-size:1.9rem;letter-spacing:-.03em">
            Selamat datang! 👋
        </h1>
        <p class="text-ink-soft font-medium text-sm">
            Masuk ke akun FunBill kamu.
        </p>
    </div>

    {{-- Session status --}}
    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-mint-soft text-mint-ink font-semibold text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-extrabold text-ink mb-2">
                Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   class="pt-input @error('email') ring-2 ring-red-400 @enderror"
                   placeholder="nama@email.com"
                   required autofocus autocomplete="username">
            @error('email')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="text-xs font-extrabold text-ink">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs font-bold text-coral hover:underline">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   class="pt-input @error('password') ring-2 ring-red-400 @enderror"
                   placeholder="••••••••"
                   required autocomplete="current-password">
            @error('password')
                <p class="mt-1.5 text-red-500 text-xs font-semibold">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="flex items-center gap-2.5 cursor-pointer select-none">
            <input type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-line text-coral focus:ring-coral focus:ring-offset-0">
            <span class="text-sm font-semibold text-ink-soft">Ingat saya</span>
        </label>

        {{-- Submit --}}
        <button type="submit"
                class="pt-btn pt-btn-primary w-full justify-center rounded-[14px] py-4 text-base font-bold mt-1">
            Masuk
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    {{-- Divider + register link --}}
    <div class="mt-6 text-center">
        <span class="text-ink-soft text-sm font-medium">Belum punya akun?</span>
        <a href="{{ route('register') }}"
           class="ml-1.5 text-sm font-bold text-coral hover:underline">
            Daftar gratis
        </a>
    </div>

</x-guest-layout>
