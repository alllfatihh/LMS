<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-gray-800 sm:text-2xl">Manajemen Kelas</h2>
                <p class="mt-0.5 text-sm text-gray-500">Kelola kelas dan pantau siswa yang bergabung</p>
            </div>
            <a href="{{ route('guru.kelas.buat') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-red to-brand-red-dark px-5 py-2.5 text-sm font-bold text-white shadow-md hover:from-brand-red hover:to-brand-red-dark sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Kelas Baru
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-8">
            @if(! $punyaKelas)
                <div class="premium-card px-8 py-20 text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-brand-red-light text-brand-red">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Anda belum memiliki kelas</h3>
                    <p class="text-slate-500 mb-10 max-w-sm mx-auto">Buat kelas pertama Anda dan mulai kelola pembelajaran digital hari ini.</p>
                    <a href="{{ route('guru.kelas.buat') }}" class="btn-brand px-10 py-4 text-base">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Kelas Baru
                    </a>
                </div>
            @else
                @php
                    $kataKunci = trim((string) request('search', ''));
                    $sedangMencari = $kataKunci !== '';
                    $filterTahunAktif = $tahunFilter !== null;
                    $memakaiFilter = $sedangMencari || $filterTahunAktif;
                @endphp

                <div class="mb-10 premium-card p-6 sm:p-8">
                    <div class="mb-6 flex flex-wrap items-baseline justify-between gap-2">
                        <p class="text-[0.7rem] font-black uppercase tracking-[0.2em] text-slate-400">Pencarian & Filter</p>
                        @if($memakaiFilter)
                            <span class="text-xs font-bold text-brand-red bg-brand-red-light px-3 py-1 rounded-lg">{{ $daftarKelas->count() }} hasil ditemukan</span>
                        @endif
                    </div>
                    <form method="get" action="{{ route('guru.kelas.index') }}" class="flex flex-col gap-4 md:flex-row md:items-stretch" role="search">
                        <div class="relative min-w-0 flex-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input
                                type="search"
                                name="search"
                                id="cari-kelas"
                                value="{{ request('search') }}"
                                placeholder="Cari nama kelas..."
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm font-bold text-slate-900 transition focus:border-brand-red focus:bg-white focus:ring-4 focus:ring-brand-red/10"
                            >
                        </div>
                        <div class="flex shrink-0 flex-col gap-4 sm:flex-row sm:items-stretch">
                            <div class="min-w-0 sm:min-w-[12rem]">
                                <select
                                    name="tahun"
                                    id="filter-tahun"
                                    class="block h-full w-full cursor-pointer rounded-xl border-slate-200 bg-slate-50 py-3.5 pl-4 pr-10 text-sm font-bold text-slate-900 transition focus:border-brand-red focus:bg-white focus:ring-4 focus:ring-brand-red/10"
                                >
                                    <option value="" @selected($tahunFilter === null)>Semua Tahun</option>
                                    @foreach($tahunTersedia as $t)
                                        <option value="{{ $t }}" @selected($tahunFilter === (int) $t)>TA {{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="btn-brand px-8">
                                    Terapkan
                                </button>
                                @if($memakaiFilter)
                                    <a href="{{ route('guru.kelas.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                @if($daftarKelas->isEmpty())
                    <div class="premium-card px-8 py-20 text-center border-dashed border-2">
                        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-300">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Tidak ada hasil</h3>
                        <p class="mt-2 text-sm text-slate-500">Coba gunakan kata kunci lain atau ubah filter tahun ajaran.</p>
                        <a href="{{ route('guru.kelas.index') }}" class="mt-8 inline-flex font-bold text-brand-red hover:underline">Tampilkan Semua Kelas</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($daftarKelas as $kelas)
                            <div class="premium-card group flex flex-col overflow-hidden rounded-[2rem] bg-white border border-slate-100 shadow-sm transition-all duration-500 hover:-translate-y-3 hover:shadow-[0_20px_40px_-15px_rgba(237,30,40,0.2)]" x-data="{ copied: false }">
                                <div class="h-3 bg-gradient-to-r from-brand-red to-rose-400 transition-all duration-500 group-hover:h-4"></div>
                                <div class="flex flex-1 flex-col p-6 sm:p-8 relative">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-rose-50 to-transparent rounded-bl-full opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                                    <div class="flex-1 relative z-10">
                                        <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                                            <h3 class="min-w-0 flex-1 text-xl font-black text-slate-900 group-hover:text-brand-red transition-colors leading-tight">{{ $kelas->name }}</h3>
                                            @if($kelas->tahun !== null)
                                                <span class="shrink-0 rounded-lg bg-rose-100 px-3 py-1.5 text-[10px] font-black text-brand-red uppercase tracking-widest">TA {{ $kelas->tahun }}</span>
                                            @endif
                                        </div>
                                        @if($kelas->description)
                                            <p class="mb-6 line-clamp-2 text-sm text-slate-500 font-medium leading-relaxed">{{ $kelas->description }}</p>
                                        @else
                                            <p class="mb-6 text-sm italic text-slate-300">Tidak ada deskripsi.</p>
                                        @endif
                                        
                                        <div class="mb-6 rounded-[1.5rem] bg-gradient-to-br from-rose-50 to-slate-50 p-6 text-center border border-rose-100/50 relative overflow-hidden transition-transform duration-500 group-hover:scale-[1.02]">
                                            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-40 rounded-bl-full"></div>
                                            <span class="mb-2 block text-[10px] font-black uppercase tracking-[0.2em] text-brand-red/70 relative z-10">Kode Akses Kelas</span>
                                            <div class="flex items-center justify-center gap-3 relative z-10">
                                                <span class="font-mono text-2xl sm:text-3xl font-black tracking-[0.2em] text-brand-red">{{ $kelas->class_code }}</span>
                                                <button
                                                    type="button"
                                                    class="rounded-xl p-2.5 text-slate-300 hover:bg-white hover:text-brand-red hover:shadow-sm transition-all"
                                                    title="Salin kode"
                                                    @click="navigator.clipboard.writeText('{{ $kelas->class_code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                </button>
                                            </div>
                                            <p x-show="copied" x-cloak x-transition class="mt-3 text-center text-[10px] font-black uppercase tracking-widest text-green-600">Berhasil Disalin</p>
                                        </div>
    
                                        <div class="mb-8 flex items-center justify-between rounded-xl border border-slate-100 bg-white p-4">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-brand-red-light flex items-center justify-center text-brand-red">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                </div>
                                                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Siswa Bergabung</span>
                                            </div>
                                            <strong class="text-xl font-black text-slate-900">{{ $kelas->siswa_count }}</strong>
                                        </div>
                                    </div>

                                    <a href="{{ route('guru.kelas.tampil', $kelas) }}" class="btn-brand w-full py-4 text-base mt-auto">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        Kelola Kelas
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
