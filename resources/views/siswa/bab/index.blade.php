<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('siswa.ujian.index') }}" class="text-gray-400 hover:text-brand-red transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">{{ $kelas->name }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Daftar Bab Pembelajaran</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($daftarBab as $bab)
                    <a href="{{ route('siswa.ujian.bab.tampil', $bab->id) }}" class="block group relative bg-white border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-brand-glow transition-all duration-300 border-l-4 border-l-brand-red hover:-translate-y-1">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-gray-800 text-lg group-hover:text-brand-red transition-colors leading-tight">{{ $bab->title }}</h3>
                            @if(auth()->user()->sudahMembacaBab($bab->id))
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded shrink-0 ml-2">Selesai Dibaca</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-400 mt-3">Bab {{ $bab->order }}</p>
                    </a>
                @empty
                    <div class="col-span-full bg-white p-8 rounded-2xl shadow-sm text-center border-t-4 border-brand-red">
                        <p class="text-gray-500">Belum ada bab pembelajaran di kelas ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
