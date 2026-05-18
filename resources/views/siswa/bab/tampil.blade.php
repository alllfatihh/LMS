<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('siswa.ujian.kelas.tampil', $bab->kelas_id) }}" class="text-gray-400 hover:text-brand-red transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">{{ $bab->title }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Bab {{ $bab->order }}</p>
            </div>
        </div>
    </x-slot>

    <div
        class="py-10"
        x-data="{ tab: 'materi' }"
        @open-kuis-tab.window="tab = 'kuis'; $nextTick(() => document.getElementById('panel-kuis')?.scrollIntoView({ behavior: 'smooth', block: 'start' }))"
    >
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-start space-x-3">
                    <p class="font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <div class="flex space-x-4 border-b border-gray-200">
                <button @click="tab = 'materi'" :class="{ 'border-brand-red text-brand-red': tab === 'materi', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'materi' }" class="py-4 px-1 border-b-2 font-bold text-sm flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Materi Belajar</span>
                </button>
                <button @click="tab = 'kuis'" :class="{ 'border-brand-red text-brand-red': tab === 'kuis', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'kuis' }" class="py-4 px-1 border-b-2 font-bold text-sm flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Latihan Kuis</span>
                </button>
            </div>

            <!-- Tab: Materi -->
            <div x-show="tab === 'materi'" x-cloak class="space-y-6">
                {{-- Progress Bar --}}
                @if($totalMateri > 0)
                    <div class="premium-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Progres Belajar</p>
                            <p class="text-xs font-black text-brand-red">{{ $materiSelesaiCount }}/{{ $totalMateri }} Modul</p>
                        </div>
                        <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100 shadow-inner">
                            <div class="h-full bg-brand-red transition-all duration-1000 shadow-lg shadow-red-100" style="width: {{ ($materiSelesaiCount / $totalMateri) * 100 }}%"></div>
                        </div>
                    </div>
                @endif

                @if($bab->teksBab && filled($bab->teksBab->content))
                    <div class="premium-card p-8 border-l-4 border-brand-red">
                        <p class="text-[10px] font-black uppercase tracking-widest text-brand-red mb-4">Ringkasan Bab</p>
                        <div class="prose prose-slate prose-lg max-w-none prose-headings:text-brand-red prose-a:text-brand-red prose-headings:font-black">
                            {!! $bab->teksBab->content !!}
                        </div>
                    </div>
                @endif

                @if($materiBab->isNotEmpty())
                    <div class="space-y-4">
                        <p class="text-xs font-extrabold uppercase tracking-wider text-gray-500 px-1">Modul materi</p>
                        @foreach($materiBab as $modul)
                            @php $isSelesai = auth()->user()->sudahMembacaMateri($modul->id); @endphp
                            <div class="premium-card group p-6 sm:p-8">
                                <div class="flex items-start justify-between gap-6">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider {{ $isSelesai ? 'bg-green-100 text-green-700' : 'bg-brand-red/10 text-brand-red' }}">
                                                {{ $modul->labelTipe() }}
                                            </span>
                                        </div>
                                        <div class="flex items-start gap-3">
                                            <h3 class="text-xl font-black text-slate-900 leading-tight group-hover:text-brand-red transition-colors">
                                                {{ $modul->judul }}
                                            </h3>
                                            @if($isSelesai)
                                                <div class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-200">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        @if(filled($modul->ringkasan))
                                            <p class="text-sm text-slate-500 mt-2.5 line-clamp-2 leading-relaxed font-medium">{{ $modul->ringkasan }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('siswa.materi.tampil', $modul->slug) }}" class="shrink-0 inline-flex h-14 w-14 items-center justify-center rounded-2xl {{ $isSelesai ? 'bg-green-50 text-green-600' : 'bg-slate-50 text-slate-300 group-hover:bg-brand-red group-hover:text-white group-hover:shadow-lg group-hover:shadow-red-100' }} transition-all">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!$sudahDibaca)
                    <div class="mt-10 p-8 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-200 text-center">
                        @if($semuaMateriSelesai)
                            <div class="mb-8">
                                <div class="mx-auto w-20 h-20 bg-green-100 rounded-3xl flex items-center justify-center text-green-600 mb-4">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h4 class="text-2xl font-black text-slate-900">Semua Modul Selesai!</h4>
                                <p class="text-sm text-slate-500 mt-2">Selamat! Kamu telah mempelajari seluruh materi. Akses kuis kini terbuka.</p>
                            </div>
                            <form action="{{ route('siswa.ujian.bab.tandai-dibaca', $bab->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-brand w-full py-4 text-lg">
                                    <span>Konfirmasi & Buka Kuis</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        @else
                            <div class="py-4">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <h4 class="text-lg font-bold text-gray-400">Kuis Masih Terkunci</h4>
                                <p class="text-sm text-gray-400 mt-1">Selesaikan {{ $totalMateri - $materiSelesaiCount }} modul lagi untuk membuka kuis.</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-green-50 text-green-700 p-6 rounded-3xl font-bold flex items-center justify-center space-x-3 border-2 border-green-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm">Pembelajaran Bab Selesai!</p>
                            <p class="text-xs font-normal opacity-80">Kuis latihan sekarang sudah dapat kamu kerjakan.</p>
                        </div>
                        <button @click="window.dispatchEvent(new CustomEvent('open-kuis-tab'))" class="bg-white text-green-600 px-4 py-2 rounded-xl text-xs font-extrabold shadow-sm hover:shadow-md transition">Buka Kuis</button>
                    </div>
                @endif
            </div>

            <!-- Tab: Kuis -->
            <div id="panel-kuis" x-show="tab === 'kuis'" x-cloak class="space-y-6">
                <div class="premium-card p-12 text-center border-t-4 border-brand-red">
                    <div class="mx-auto w-24 h-24 mb-6 bg-brand-red-light rounded-3xl flex items-center justify-center">
                        <svg class="w-12 h-12 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 mb-2">Latihan Kuis: {{ $bab->title }}</h3>
                    <p class="text-slate-500 mb-10 max-w-sm mx-auto leading-relaxed">Uji pemahamanmu setelah mempelajari seluruh modul dalam bab ini.</p>

                    @if($ujianSelesai)
                        <div class="bg-slate-50 border border-slate-100 p-8 rounded-3xl">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Status Terakhir</p>
                            <p class="text-slate-900 font-black text-xl mb-8">Kamu sudah menyelesaikan kuis ini.</p>
                            <a href="{{ route('siswa.ujian.hasil', $ujianSelesai->id) }}" class="btn-brand px-10 py-4 text-base">
                                Lihat Hasil Ujian
                            </a>
                        </div>
                    @elseif($sudahDibaca)
                        <form action="{{ route('siswa.ujian.bab.mulai', $bab->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-brand px-12 py-5 text-lg mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Mulai Kuis Sekarang</span>
                            </button>
                        </form>
                    @else
                        <div class="bg-slate-50 border-2 border-dashed border-slate-200 p-12 rounded-3xl">
                            <div class="h-20 w-20 bg-white shadow-sm rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h4 class="text-xl font-black text-slate-400">Kuis Masih Terkunci</h4>
                            <p class="text-sm font-medium mt-2 text-slate-400 max-w-xs mx-auto">Selesaikan seluruh modul materi di tab sebelah untuk membuka kuis latihan ini.</p>
                            <button @click="tab = 'materi'" class="mt-8 text-brand-red font-black hover:underline">Pelajari Materi Sekarang</button>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
    
    <style>
        .anti-gravity-card {
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .anti-gravity-card:hover {
            transform: translateY(-5px);
        }
    </style>
</x-app-layout>
