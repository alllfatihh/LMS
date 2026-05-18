<x-guest-layout>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-rose-50 text-brand-red mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Lupa Password?</h2>
        <p class="text-sm text-slate-400 mt-2 leading-relaxed px-4">
            Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 px-4 py-3 rounded-2xl bg-green-50 border border-green-100 text-sm text-green-600 font-bold" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email Terdaftar')" class="font-bold text-slate-700 text-sm mb-1.5" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                </span>
                <x-text-input id="email" class="block w-full pl-12 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium text-slate-900 transition" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="space-y-4">
            <button type="submit" class="w-full bg-gradient-to-r from-brand-red to-brand-red-dark text-white font-extrabold py-4 px-4 rounded-2xl shadow-md hover:shadow-[0_8px_24px_rgba(237,30,40,0.35)] hover:-translate-y-0.5 transition-all duration-200 text-base tracking-wide">
                Kirim Tautan Atur Ulang
            </button>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-brand-red transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke halaman Login
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
