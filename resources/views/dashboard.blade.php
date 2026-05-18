<x-app-layout>
    <x-slot name="header">
        @if(auth()->user()->role === 'guru')
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Utama') }}
            </h2>
        @endif
    </x-slot>

    {{-- Subtle animated background glow for the whole page --}}
    <div class="absolute top-0 inset-x-0 h-[500px] overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] rounded-full bg-brand-red/5 blur-[100px]"></div>
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-blue-500/5 blur-[100px]"></div>
    </div>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            {{-- Masterpiece Welcome Banner --}}
            <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-10 sm:p-16 shadow-2xl text-white">
                <!-- Immersive Background Gradients & Glassmorphism -->
                <div class="absolute inset-0 bg-gradient-to-r from-brand-red-dark/80 via-slate-900 to-slate-900"></div>
                <div class="absolute top-0 right-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10 mix-blend-overlay"></div>
                
                <!-- Floating Orbs -->
                <div class="absolute -right-20 top-0 h-96 w-96 rounded-full bg-brand-red mix-blend-screen opacity-40 blur-[80px] animate-pulse" style="animation-duration: 4s;"></div>
                <div class="absolute -bottom-32 right-32 h-64 w-64 rounded-full bg-blue-500 mix-blend-screen opacity-20 blur-[60px] animate-pulse" style="animation-duration: 6s;"></div>
                <div class="absolute top-10 left-10 h-32 w-32 rounded-full bg-purple-500 mix-blend-screen opacity-20 blur-[40px]"></div>
                
                <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-10">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/10 text-[10px] font-black uppercase tracking-[0.2em] text-white/90 mb-6 backdrop-blur-md shadow-xl">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.8)]"></span>
                            </span>
                            Sistem Utama Aktif
                        </div>
                        <h1 class="text-4xl sm:text-6xl font-black mb-4 tracking-tighter text-transparent bg-clip-text bg-gradient-to-br from-white via-white to-white/60">
                            Halo, {{ explode(' ', auth()->user()->name)[0] }}!
                        </h1>
                        <p class="text-slate-300 font-medium text-lg sm:text-xl leading-relaxed">
                            {{ auth()->user()->role === 'guru' ? 'Pusat kendali ruang kelas Anda. Analisis performa siswa dan kelola materi belajar dengan pengalaman luar biasa.' : 'Petualangan belajarmu berlanjut. Pecahkan setiap kuis dan capai rekor terbarumu hari ini.' }}
                        </p>
                    </div>
                    <div class="shrink-0 w-full lg:w-auto">
                        @if(auth()->user()->role === 'guru')
                            <a href="{{ route('guru.kelas.index') }}" class="group relative flex items-center justify-center gap-3 rounded-2xl bg-white px-8 py-5 text-sm font-black text-slate-900 shadow-[0_0_40px_rgba(255,255,255,0.3)] hover:shadow-[0_0_60px_rgba(255,255,255,0.5)] hover:-translate-y-2 transition-all duration-300 w-full sm:w-auto overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-slate-100 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                <svg class="h-6 w-6 text-brand-red transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Buka Ruang Kelas
                            </a>
                        @else
                            <a href="{{ route('siswa.ujian.index') }}" class="group relative flex items-center justify-center gap-3 rounded-2xl bg-white px-8 py-5 text-sm font-black text-slate-900 shadow-[0_0_40px_rgba(255,255,255,0.3)] hover:shadow-[0_0_60px_rgba(255,255,255,0.5)] hover:-translate-y-2 transition-all duration-300 w-full sm:w-auto overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-slate-100 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                <svg class="h-6 w-6 text-brand-red transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Kelas Saya
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dynamic Bento-Style Stats Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @if(auth()->user()->role === 'guru')
                    {{-- Guru Stats --}}
                    @php
                        $stats = [
                            ['label' => 'Total Kelas', 'value' => $data['total_kelas'] ?? 0, 'color' => 'brand-red', 'bg' => 'bg-brand-red/5', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                            ['label' => 'Total Siswa', 'value' => $data['total_siswa'] ?? 0, 'color' => 'blue-600', 'bg' => 'bg-blue-50', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['label' => 'Ujian Selesai', 'value' => $data['total_ujian'] ?? 0, 'color' => 'purple-600', 'bg' => 'bg-purple-50', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['label' => 'Rata-rata Skor', 'value' => ($data['rata_skor'] ?? 0) . '%', 'color' => 'emerald-600', 'bg' => 'bg-emerald-50', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6']
                        ];
                    @endphp
                @else
                    {{-- Siswa Stats --}}
                    @php
                        $stats = [
                            ['label' => 'Kelas Diikuti', 'value' => $data['total_kelas'] ?? 0, 'color' => 'brand-red', 'bg' => 'bg-brand-red/5', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                            ['label' => 'Ujian Selesai', 'value' => $data['total_ujian'] ?? 0, 'color' => 'blue-600', 'bg' => 'bg-blue-50', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                            ['label' => 'Rata-rata Skor', 'value' => ($data['rata_skor'] ?? 0) . '%', 'color' => 'purple-600', 'bg' => 'bg-purple-50', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
                            ['label' => 'Skor Tertinggi', 'value' => ($data['skor_tertinggi'] ?? 0) . '%', 'color' => 'emerald-600', 'bg' => 'bg-emerald-50', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z']
                        ];
                    @endphp
                @endif

                @foreach($stats as $stat)
                    <div class="relative overflow-hidden rounded-[2rem] bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-300 group cursor-default border border-slate-100">
                        {{-- Watermark Icon --}}
                        <svg class="absolute -right-6 -bottom-6 h-40 w-40 text-slate-50 opacity-50 group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}"/></svg>
                        
                        <div class="relative z-10">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $stat['bg'] }} text-{{ $stat['color'] }} shadow-inner mb-6 group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                            </div>
                            <span class="block text-[11px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1">{{ $stat['label'] }}</span>
                            <span class="block text-4xl font-black text-slate-900">{{ $stat['value'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Ultra-Premium Content Grid for Siswa --}}
            @if(auth()->user()->role === 'siswa')
                <div id="kelas-saya" class="space-y-8 scroll-mt-10">
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Kelas Saya</h3>
                    </div>

                    {{-- Classes Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($data['daftar_kelas'] as $kls)
                            <div class="group relative bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                                {{-- Link Overlay for the whole card --}}
                                <a href="{{ route('siswa.ujian.kelas.tampil', $kls->id) }}" class="absolute inset-0 z-0"></a>
                                
                                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-red/5 rounded-bl-full -mr-10 -mt-10 group-hover:scale-110 transition-transform duration-500"></div>
                                
                                <div class="relative z-10 flex-1">
                                    <div class="h-12 w-12 rounded-xl bg-brand-red text-white flex items-center justify-center mb-6 shadow-lg shadow-brand-red/20 group-hover:rotate-6 transition-transform">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-900 mb-2">{{ $kls->name }}</h4>
                                    <p class="text-sm text-slate-500 font-medium mb-4">Pengajar: <span class="text-slate-900">{{ $kls->guru->name }}</span></p>
                                </div>

                                <div class="relative z-20 mt-4 flex items-center justify-between border-t border-slate-50 pt-4">
                                    <a href="{{ route('siswa.ujian.kelas.tampil', $kls->id) }}" class="relative z-30 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-brand-red text-white text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-brand-red-dark hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                                        Buka Materi
                                        <svg class="h-3 w-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    </a>
                                    <button 
                                        type="button"
                                        onclick="event.stopPropagation(); event.preventDefault(); confirmKeluarKelas(@js($kls->name), @js(route('siswa.gabung-kelas.keluar', $kls)))"
                                        class="relative z-30 px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest text-slate-300 hover:text-brand-red hover:bg-brand-red-light transition-all"
                                    >
                                        Keluar Kelas
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Recent Progress Feed for Student --}}
                <div class="relative bg-white rounded-[2.5rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                    <div class="p-8 sm:px-10 sm:pt-10 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 border-b border-slate-100/60 pb-6 bg-slate-50/30">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[10px] font-black uppercase tracking-widest text-blue-600 mb-3">
                                Progress Belajar
                            </div>
                            <h3 class="text-2xl font-black text-slate-900">Riwayat Latihanmu</h3>
                            <p class="text-sm text-slate-500 mt-1 font-medium">Lihat sejauh mana perkembangan belajarmu minggu ini.</p>
                        </div>
                        <a href="{{ route('siswa.ujian.riwayat') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-xs font-black text-slate-700 shadow-sm border border-slate-200 hover:border-slate-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                            Lihat Semua Riwayat &rarr;
                        </a>
                    </div>
                    
                    @if(isset($data['aktivitas_terbaru']) && count($data['aktivitas_terbaru']) > 0)
                        <div class="p-6 sm:p-10 space-y-4">
                            @foreach($data['aktivitas_terbaru'] as $akt)
                                <div class="group relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 rounded-3xl p-6 bg-white border border-slate-100 shadow-sm hover:shadow-[0_10px_40px_rgb(0,0,0,0.06)] hover:border-slate-200 transition-all duration-300">
                                    <div class="flex items-center gap-5">
                                        <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center text-brand-red group-hover:scale-110 transition-transform">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-base mb-1">{{ $akt->judul_bab }}</h4>
                                            <div class="flex items-center gap-3 text-xs font-medium text-slate-500">
                                                <span>{{ $akt->nama_kelas }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span>{{ $akt->waktu }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full sm:w-auto shrink-0 flex items-center justify-between sm:justify-end gap-6 sm:min-w-[200px]">
                                        <div class="w-full sm:w-32 hidden sm:block">
                                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $akt->skor >= 70 ? 'bg-emerald-500' : ($akt->skor >= 40 ? 'bg-amber-500' : 'bg-brand-red') }}" style="width: {{ $akt->skor }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-400 mb-0.5">Skor</span>
                                            <span class="text-2xl font-black {{ $akt->skor >= 70 ? 'text-emerald-600' : ($akt->skor >= 40 ? 'text-amber-600' : 'text-brand-red') }}">
                                                {{ $akt->skor }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-16 text-center">
                            <h4 class="text-xl font-black text-slate-900 mb-2">Belum Ada Riwayat Ujian</h4>
                            <p class="text-base text-slate-500 font-medium max-w-sm mx-auto">Selesaikan kuis pertamamu untuk mulai melihat progres di sini.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Ultra-Premium Activity Feed (Guru Only) --}}
            @if(auth()->user()->role === 'guru')
                <div class="relative bg-white rounded-[2.5rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                    <div class="p-8 sm:px-10 sm:pt-10 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 border-b border-slate-100/60 pb-6 bg-slate-50/30">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-3">
                                Live Feed
                            </div>
                            <h3 class="text-2xl font-black text-slate-900">Aktivitas Ujian Siswa</h3>
                            <p class="text-sm text-slate-500 mt-1 font-medium">Pantau hasil evaluasi dan pergerakan siswa secara langsung.</p>
                        </div>
                        <a href="{{ route('guru.kelas.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-xs font-black text-brand-red shadow-sm border border-slate-200 hover:border-brand-red hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                            Lihat Rekap Kelas &rarr;
                        </a>
                    </div>
                    
                    @if(isset($data['aktivitas_terbaru']) && count($data['aktivitas_terbaru']) > 0)
                        <div class="p-6 sm:p-10 space-y-4">
                            @foreach($data['aktivitas_terbaru'] as $akt)
                                <div class="group relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 rounded-3xl p-6 bg-white border border-slate-100 shadow-sm hover:shadow-[0_10px_40px_rgb(0,0,0,0.06)] hover:border-slate-200 transition-all duration-300">
                                    {{-- Left Side: Avatar + Details --}}
                                    <div class="flex items-center gap-5">
                                        <div class="relative">
                                            <div class="absolute inset-0 rounded-full bg-brand-red opacity-0 group-hover:opacity-20 blur-md transition-opacity duration-300"></div>
                                            <div class="h-14 w-14 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 text-slate-700 flex items-center justify-center font-black text-lg border-2 border-white shadow-md relative z-10 group-hover:scale-110 transition-transform duration-300">
                                                {{ substr($akt->nama_siswa, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-base mb-1">{{ $akt->nama_siswa }}</h4>
                                            <div class="flex flex-wrap items-center gap-3 text-xs font-medium text-slate-500">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    {{ $akt->judul_bab }}
                                                </span>
                                                <span class="flex items-center gap-1 text-slate-400">
                                                    &bull; {{ $akt->nama_kelas }}
                                                </span>
                                                <span class="flex items-center gap-1 text-slate-400">
                                                    &bull; {{ $akt->waktu }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right Side: Score with Progress Bar --}}
                                    <div class="w-full sm:w-auto shrink-0 flex items-center justify-between sm:justify-end gap-6 sm:min-w-[200px]">
                                        <div class="w-full sm:w-32 hidden sm:block">
                                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $akt->skor >= 70 ? 'bg-emerald-500' : ($akt->skor >= 40 ? 'bg-amber-500' : 'bg-brand-red') }}" style="width: {{ $akt->skor }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-xs font-black uppercase tracking-widest text-slate-400 mb-0.5">Skor Akhir</span>
                                            <span class="text-2xl font-black {{ $akt->skor >= 70 ? 'text-emerald-600' : ($akt->skor >= 40 ? 'text-amber-600' : 'text-brand-red') }}">
                                                {{ $akt->skor }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-16 text-center">
                            <div class="mx-auto h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 shadow-inner relative overflow-hidden">
                                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                                <svg class="h-10 w-10 text-slate-300 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h4 class="text-xl font-black text-slate-900 mb-2">Belum Ada Aktivitas Siswa</h4>
                            <p class="text-base text-slate-500 font-medium max-w-sm mx-auto">Ketika siswa Anda menyelesaikan ujian, hasilnya akan langsung muncul secara *real-time* di sini.</p>
                        </div>
                    @endif
                </div>
            @endif



            <form id="form-keluar-kelas" action="" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <script>
                window.confirmKeluarKelas = function(namaKelas, url) {
                    const form = document.getElementById('form-keluar-kelas');
                    const jalankanKeluar = () => {
                        if (form) {
                            form.action = url;
                            form.submit();
                        }
                    };

                    if (typeof window.Swal === 'undefined') {
                        if (confirm('Keluar dari kelas ' + namaKelas + '?')) {
                            jalankanKeluar();
                        }
                        return;
                    }

                    window.Swal.fire({
                        title: 'Keluar dari kelas?',
                        text: 'Kamu akan keluar dari kelas ' + namaKelas + '. Seluruh progres belajarmu di kelas ini tidak akan hilang, namun kamu harus bergabung kembali menggunakan kode kelas jika ingin masuk lagi.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, keluar',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        confirmButtonColor: '#ed1e28',
                        cancelButtonColor: '#6b7280',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            jalankanKeluar();
                        }
                    });
                };
            </script>
        </div>
    </div>
</x-app-layout>
