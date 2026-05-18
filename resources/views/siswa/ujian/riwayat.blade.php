<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-gray-900 sm:text-3xl">Riwayat Belajar</h2>
                <p class="mt-1 text-sm font-medium text-gray-500">Pantau progres kuis dan pencapaian skormu.</p>
            </div>
            <div class="flex shrink-0 gap-3">
                <a href="{{ route('siswa.ujian.index') }}" class="btn-brand px-6 py-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Kuis Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-gray-50/50 pb-20 pt-8">
        {{-- Decorative Background Elements --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -left-20 top-0 h-64 w-64 rounded-full bg-brand-red/5 blur-3xl"></div>
            <div class="absolute right-0 top-40 h-96 w-96 rounded-full bg-brand-red/5 blur-3xl"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            
            @if($daftarSesi->count() > 0)
                @php
                    $selesai = $daftarSesi->filter(fn ($s) => $s->finished_at !== null);
                    $avgScore = $selesai->avg('nilai') ?? 0;
                    $bestScore = $selesai->max('nilai') ?? 0;
                    $totalLatihan = $daftarSesi->count();
                @endphp

                {{-- Stats Grid --}}
                <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="premium-card p-8 group">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Sesi</p>
                            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:text-brand-red group-hover:bg-brand-red-light transition-all">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black text-slate-900 leading-none">{{ $totalLatihan }}</h3>
                        <p class="mt-2 text-xs font-bold text-slate-400">Sesi Ujian</p>
                    </div>

                    <div class="premium-card p-8 group border-brand-red/20">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-brand-red/60">Rata-rata Skor</p>
                            <div class="h-12 w-12 rounded-2xl bg-brand-red-light flex items-center justify-center text-brand-red group-hover:scale-110 transition-all">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black text-brand-red leading-none">{{ number_format($avgScore, 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-brand-red/60">Poin Rata-rata</p>
                    </div>

                    <div class="premium-card p-8 group border-amber-200/50">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600/60">Skor Tertinggi</p>
                            <div class="h-12 w-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-100 transition-all">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-4xl font-black text-amber-600 leading-none">{{ number_format($bestScore, 0) }}</h3>
                        <p class="mt-2 text-xs font-bold text-amber-600/60">Pencapaian Terbaik</p>
                    </div>
                </div>
            @endif

            {{-- Main List --}}
            @if($daftarSesi->isEmpty())
                <div class="flex flex-col items-center justify-center rounded-[3rem] bg-white p-12 text-center shadow-xl shadow-gray-200/50">
                    <div class="relative mb-8">
                        <div class="absolute -inset-4 rounded-full bg-brand-red/5 blur-xl animate-pulse"></div>
                        <div class="relative flex h-24 w-24 items-center justify-center rounded-3xl bg-brand-red-light text-brand-red shadow-inner">
                            <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900">Belum ada aktivitas</h3>
                    <p class="mx-auto mt-2 max-w-xs text-gray-500 font-medium">Kamu belum mengerjakan kuis apapun. Ayo mulai belajar hari ini!</p>
                    <a href="{{ route('siswa.ujian.index') }}" class="mt-10 inline-flex items-center gap-3 rounded-2xl bg-gray-900 px-10 py-4 text-base font-extrabold text-white transition-all hover:bg-brand-red hover:shadow-brand-glow">
                        Cari Kuis Pertama
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($daftarSesi as $sesi)
                        @php
                            $isFinished = $sesi->finished_at !== null;
                            $score = $sesi->nilai ?? 0;
                            $isPerfect = $score >= 100;
                            $isGood = $score >= 70;
                        @endphp
                        <div class="premium-card group p-6 sm:p-8">
                            <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                                {{-- Score Circle --}}
                                <div class="flex shrink-0 items-center justify-center">
                                    <div class="relative flex h-24 w-24 items-center justify-center rounded-3xl {{ !$isFinished ? 'bg-slate-100 text-slate-300' : ($isGood ? 'bg-green-50 text-green-600' : 'bg-brand-red-light text-brand-red') }} shadow-inner transition-transform group-hover:scale-105">
                                        @if(!$isFinished)
                                            <svg class="h-10 w-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <div class="flex flex-col items-center">
                                                <span class="text-4xl font-black">{{ number_format($score, 0) }}</span>
                                                <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Poin</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="rounded-lg bg-slate-50 border border-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $sesi->bab->kelas->name }}</span>
                                        @if($isFinished)
                                            <span class="inline-flex items-center gap-1.5 rounded-lg {{ $isGood ? 'bg-green-100 text-green-700' : 'bg-brand-red-light text-brand-red' }} px-3 py-1 text-[10px] font-black uppercase tracking-widest">
                                                <div class="h-1.5 w-1.5 rounded-full {{ $isGood ? 'bg-green-600' : 'bg-brand-red' }} animate-pulse"></div>
                                                {{ $isGood ? 'Lulus' : 'Remedi' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-600 border border-amber-100">
                                                <svg class="h-3.5 w-3.5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3"/></svg>
                                                Dalam Proses
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="mt-3 text-2xl font-black text-slate-900 leading-tight group-hover:text-brand-red transition-colors">{{ $sesi->bab->title }}</h4>
                                    <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs font-bold text-slate-400">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $sesi->started_at->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        @if($isFinished)
                                            <div class="flex items-center gap-2">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $sesi->started_at->diffInMinutes($sesi->finished_at) }} Menit
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action --}}
                                <div class="flex shrink-0 items-center justify-end">
                                    @if($isFinished)
                                        <a href="{{ route('siswa.ujian.hasil', $sesi->id) }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-8 text-sm font-black text-slate-600 transition-all hover:border-brand-red hover:text-brand-red hover:shadow-sm">
                                            Hasil
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('siswa.ujian.tampil-soal', $sesi->id) }}" class="btn-brand h-14 px-8">
                                            Lanjutkan
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
