<x-guest-layout>
    <div class="text-center mb-5">
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Buat Akun Baru 👋</h2>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-2.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-600 font-semibold">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name & Email Group -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-slate-700 text-xs mb-1 ml-1" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <x-text-input id="name" class="block w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium text-slate-900 transition" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
                </div>
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" class="font-bold text-slate-700 text-xs mb-1 ml-1" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </span>
                    <x-text-input id="email" class="block w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium text-slate-900 transition" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
                </div>
            </div>
        </div>

        <!-- Role Selector -->
        <div class="mb-4">
            <x-input-label for="role" :value="__('Mendaftar Sebagai')" class="font-bold text-slate-700 text-xs mb-1 ml-1" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <select id="role" name="role" class="block w-full pl-12 pr-10 py-3 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-bold text-slate-700 appearance-none transition cursor-pointer">
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa (Belajar Matematika)</option>
                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru (Mengajar & Kelola)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <!-- Password Group -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <x-input-label for="password" :value="__('Password')" class="font-bold text-slate-700 text-xs mb-1 ml-1" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <x-text-input id="password" class="block w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium transition" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi')" class="font-bold text-slate-700 text-xs mb-1 ml-1" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <x-text-input id="password_confirmation" class="block w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-brand-red focus:border-brand-red shadow-sm text-sm font-medium transition" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-gradient-to-r from-brand-red to-brand-red-dark text-white font-extrabold py-4 px-4 rounded-2xl shadow-md hover:shadow-brand-glow hover:-translate-y-0.5 transition-all duration-200 text-sm tracking-widest uppercase">
            Daftar Sekarang
        </button>

        <div class="text-center mt-5">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-brand-red transition-all duration-200 uppercase tracking-tight">
                Sudah punya akun? <span class="text-brand-red">Masuk Disini</span>
            </a>
        </div>
    </form>
</x-guest-layout>
