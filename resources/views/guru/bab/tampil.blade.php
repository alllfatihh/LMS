<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-xl text-gray-800 leading-tight">{{ $bab->title }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">Bab {{ $bab->order }} · {{ $bab->kelas->name }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guru.teks.edit', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Edit Ringkasan
                </a>
                <a href="{{ route('guru.bab.edit', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Ubah Judul Bab
                </a>
                <a href="{{ route('guru.soal.buat', $bab) }}" class="btn-brand h-11 px-5">
                    + Tambah Soal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-start space-x-3">
                    <p class="font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-xl">
                    <p class="font-bold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if(!($materiTerhubungBab))
                <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-xl text-sm">
                    <p class="font-bold">Skema database belum lengkap</p>
                    <p class="mt-1">Jalankan migrasi di folder aplikasi ini: <code class="rounded bg-white/80 px-1.5 py-0.5 font-mono text-xs">php artisan migrate</code> (menambahkan kolom <code class="font-mono">bab_id</code> pada tabel <code class="font-mono">materi</code>).</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red">
                <div class="p-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-100">
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-800">Modul materi (per bab)</h3>
                        <p class="text-sm text-gray-500 mt-0.5">Teks, YouTube, atau PDF — siswa membuka dari halaman bab yang sama.</p>
                    </div>
                    @if($materiTerhubungBab)
                        <a href="{{ route('guru.materi.buat', $bab) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-brand-red to-brand-red-dark px-4 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-brand-glow transition whitespace-nowrap">
                            + Tambah materi
                        </a>
                    @endif
                </div>
                <div class="p-6">
                    @if($daftarMateriBab->isEmpty())
                        <p class="text-gray-400 italic text-sm text-center py-6">Belum ada modul materi. @if($materiTerhubungBab)Tambahkan lewat tombol di atas.@else Setelah migrasi selesai, tombol tambah akan tersedia.@endif</p>
                    @else
                        <ul class="divide-y divide-gray-100 rounded-xl border border-gray-100 overflow-hidden">
                            @foreach($daftarMateriBab as $modul)
                                <li class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-4 py-3 bg-white hover:bg-gray-50/80 transition">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-400">Urutan {{ $modul->order }} · {{ $modul->labelTipe() }}</p>
                                        <p class="font-bold text-gray-900 truncate">{{ $modul->judul }}</p>
                                        @if($modul->ringkasan)
                                            <p class="text-sm text-gray-500 line-clamp-2 mt-0.5">{{ $modul->ringkasan }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap gap-2 shrink-0">
                                        <a href="{{ route('guru.materi.edit', $modul) }}" class="text-xs font-bold text-brand-red hover:underline px-2 py-1 rounded-lg border border-brand-red/20">Edit</a>
                                        <a href="{{ route('siswa.materi.tampil', $modul->slug) }}" target="_blank" rel="noopener" class="text-xs font-bold text-gray-600 hover:underline px-2 py-1 rounded-lg border border-gray-200">Pratinjau</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-gray-800">
                        <div class="p-6">
                            <h3 class="text-lg font-extrabold text-gray-800 mb-4">Pratinjau ringkasan</h3>
                            @if($bab->teksBab)
                                <div class="prose prose-sm prose-slate max-w-none text-gray-700">
                                    {!! $bab->teksBab->content !!}
                                </div>
                            @else
                                <p class="text-gray-400 italic text-sm">Ringkasan belum diisi.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border-t-4 border-brand-red">
                        <div class="p-6">
                            <h3 class="text-lg font-extrabold text-gray-800 mb-4">Daftar soal latihan</h3>
                            @if($bab->daftarSoal->isEmpty())
                                <p class="text-gray-300 italic text-center py-6">Belum ada soal di bab ini.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($bab->daftarSoal as $soal)
                                        <div class="p-4 border border-gray-100 rounded-xl hover:shadow-md transition-shadow">
                                            <p class="text-sm font-bold text-gray-800">{{ $soal->question_template }}</p>
                                            <div class="mt-2 flex items-center justify-between">
                                                <div class="text-xs font-mono text-gray-500 bg-gray-50 px-2 py-1 rounded">
                                                    Rumus: {{ $soal->answer_formula }}
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('guru.soal.edit', $soal) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-brand-red hover:text-brand-red transition-all">
                                                        Ubah
                                                    </a>
                                                    <form action="{{ route('guru.soal.hapus', $soal) }}" method="POST" onsubmit="return confirm('Yakin hapus soal ini?')" class="m-0 p-0">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition-all">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
