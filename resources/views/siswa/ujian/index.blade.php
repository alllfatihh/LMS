<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">Kelas Saya</h2>
                <p class="text-sm text-gray-400 mt-0.5">Pilih kelas untuk mulai belajar</p>
            </div>
            <a href="{{ route('siswa.gabung-kelas.index') }}" class="bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-brand-red-darker text-white font-bold py-2.5 px-5 rounded-lg shadow-md hover:shadow-brand-glow transition-all duration-200 flex items-center space-x-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Join Kelas Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 mb-8 rounded-2xl flex items-start space-x-3 shadow-sm" role="alert">
                    <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if($daftarKelas->isEmpty())
                <div class="premium-card text-center py-20 px-8">
                    <div class="mx-auto w-20 h-20 bg-brand-red-light rounded-3xl flex items-center justify-center mb-6 text-brand-red">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Belum ada kelas</h3>
                    <p class="text-slate-500 mb-8 max-w-sm mx-auto">Kamu belum bergabung di kelas manapun. Yuk, join kelas sekarang!</p>
                    <a href="{{ route('siswa.gabung-kelas.index') }}" class="btn-brand px-10 py-4 text-base">
                        <span>Cari Kelas Sekarang</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($daftarKelas as $class)
                        <div class="premium-card group overflow-hidden">
                            <div class="h-3 bg-brand-red transition-all duration-500 group-hover:h-4"></div>
                            <div class="p-8">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-black text-slate-900 leading-tight group-hover:text-brand-red transition-colors">{{ $class->name }}</h3>
                                    <span class="bg-slate-50 text-slate-400 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-widest border border-slate-100 font-mono">{{ $class->class_code }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-500 mb-8">
                                    <div class="h-10 w-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 border border-slate-100 group-hover:border-brand-red/20 group-hover:text-brand-red transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Guru Pengajar</p>
                                        <p class="text-sm font-extrabold text-slate-700 leading-none">{{ $class->guru->name }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('siswa.ujian.kelas.tampil', $class->id) }}" class="btn-brand w-full py-4 text-base shadow-red-50">
                                    <span>Buka Kelas</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
