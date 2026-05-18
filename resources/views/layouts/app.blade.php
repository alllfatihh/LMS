<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="guru-soal-pratinjau-url" content="{{ route('guru.soal.pratinjau') }}">
        <title>{{ config('app.name', 'LMS Matematika') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        @stack('styles')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-gray-50/50">
        <div class="min-h-screen">
            <div class="glass-header">
                @include('layouts.navigation')
            </div>

            @isset($header)
                <header class="bg-white border-b border-slate-100">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="max-w-7xl mx-auto">
                @if(session('success') || session('info') || session('error'))
                    <div
                        class="pointer-events-none fixed inset-x-0 top-0 z-[100] flex justify-end px-4 pt-20 sm:pt-24"
                        aria-live="polite"
                    >
                        <div
                            x-data="{ show: true }"
                            x-init="setTimeout(() => show = false, 6000)"
                            x-show="show"
                            x-transition:enter="transform transition ease-out duration-300"
                            x-transition:enter-start="translate-x-6 translate-y-2 opacity-0"
                            x-transition:enter-end="translate-x-0 translate-y-0 opacity-100"
                            x-transition:leave="transform transition ease-in duration-200"
                            x-transition:leave-start="translate-x-0 opacity-100"
                            x-transition:leave-end="translate-x-4 opacity-0"
                            class="pointer-events-auto w-full max-w-sm space-y-2"
                            @keydown.escape.window="show = false"
                        >
                            @if(session('success'))
                                <div class="flex gap-3 rounded-2xl border border-green-200/80 bg-white p-4 shadow-lg shadow-green-900/5 ring-1 ring-green-100" role="status">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1 pt-0.5">
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-green-700">Berhasil</p>
                                        <p class="mt-0.5 text-sm font-medium leading-snug text-gray-800">{{ session('success') }}</p>
                                    </div>
                                    <button type="button" class="shrink-0 rounded-lg p-1 text-gray-400 hover:bg-gray-50 hover:text-gray-600" @click="show = false" aria-label="Tutup">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endif
                            @if(session('info'))
                                <div class="flex gap-3 rounded-2xl border border-red-100 bg-white p-4 shadow-lg shadow-red-900/5 ring-1 ring-[#ed1e28]/10" role="status">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-red-light text-[#ed1e28]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1 pt-0.5">
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-[#ed1e28]">Informasi</p>
                                        <p class="mt-0.5 text-sm font-medium leading-snug text-gray-800">{{ session('info') }}</p>
                                    </div>
                                    <button type="button" class="shrink-0 rounded-lg p-1 text-gray-400 hover:bg-gray-50 hover:text-gray-600" @click="show = false" aria-label="Tutup">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="flex gap-3 rounded-2xl border border-red-200/80 bg-white p-4 shadow-lg shadow-red-900/5 ring-1 ring-red-100" role="alert">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-[#ed1e28]">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1 pt-0.5">
                                        <p class="text-xs font-extrabold uppercase tracking-wide text-[#ed1e28]">Gagal</p>
                                        <p class="mt-0.5 text-sm font-medium leading-snug text-gray-800">{{ session('error') }}</p>
                                    </div>
                                    <button type="button" class="shrink-0 rounded-lg p-1 text-gray-400 hover:bg-gray-50 hover:text-gray-600" @click="show = false" aria-label="Tutup">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
