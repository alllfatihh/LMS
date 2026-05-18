<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Selamat Datang! </h2>
        <p class="text-sm text-slate-400 mt-1.5 font-medium">Masuk ke akun LMS Matematika Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-2xl bg-rose-50 border border-rose-200 text-sm text-rose-600 font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <!-- Email -->
        <div class="mb-5">
            <x-input-label for="email" :value="__('Email')" class="font-bold text-slate-700 text-sm mb-1.5" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </span>
                <x-text-input id="email"
                    class="block w-full pl-12 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium text-slate-900 transition"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="nama@email.com" />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-5">
            <x-input-label for="password" :value="__('Password')" class="font-bold text-slate-700 text-sm mb-1.5" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>
                <x-text-input id="password"
                    class="block w-full pl-12 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium transition"
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
        </div>

        <!-- Remember & Forgot -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-300 text-brand-red shadow-sm focus:ring-brand-red" name="remember">
                <span class="text-sm text-slate-500 font-medium">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-brand-red hover:text-brand-red-dark transition"
                    href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit"
            class="w-full bg-gradient-to-r from-brand-red to-brand-red-dark text-white font-extrabold py-4 px-4 rounded-2xl shadow-md hover:shadow-[0_8px_24px_rgba(237,30,40,0.35)] hover:-translate-y-0.5 transition-all duration-200 text-base tracking-wide">
            Masuk ke Platform
        </button>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-slate-100"></div>
            <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">atau</span>
            <div class="flex-1 h-px bg-slate-100"></div>
        </div>

        <!-- Register Link -->
        <a href="{{ route('register') }}"
            class="flex items-center justify-center gap-2 w-full py-3.5 px-4 rounded-2xl border-2 border-slate-200 font-bold text-sm text-slate-600 hover:border-brand-red hover:text-brand-red transition-all duration-200">
            Belum punya akun? <span class="text-brand-red">Daftar Sekarang</span>
        </a>
    </form>
</x-guest-layout>