<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-xl font-extrabold leading-tight text-gray-800 sm:text-2xl">{{ $kelas->name }}</h2>
                <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500">
                    <span class="inline-flex items-center gap-1">
                        <svg class="h-4 w-4 text-[#ed1e28]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        Kode: <span class="font-mono font-bold text-[#ed1e28]">{{ $kelas->class_code }}</span>
                    </span>
                    @if($kelas->tahun)
                        <span class="rounded-lg bg-brand-red-light px-2 py-0.5 text-xs font-bold text-[#ed1e28]">TA {{ $kelas->tahun }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guru.kelas.index') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-6 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Daftar Kelas
                </a>
                <a href="{{ route('guru.kelas.edit', $kelas) }}" class="btn-brand h-12 px-6">
                    Edit Kelas
                </a>
                <button type="button" id="tombol-hapus-kelas" class="inline-flex h-12 items-center justify-center rounded-xl border-2 border-brand-red/10 bg-white px-6 text-sm font-black text-brand-red transition hover:bg-brand-red-light">
                    Hapus
                </button>
            </div>
        </div>
    </x-slot>

    <form id="form-hapus-kelas" action="{{ route('guru.kelas.hapus', $kelas) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <div class="py-10" x-data="{ tab: 'materi' }">
        <div class="mx-auto max-w-7xl space-y-10 px-4 sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-bold">{{ session('error') }}</p>
                </div>
            @endif
            {{-- Kartu detail (floating) --}}
            <div class="premium-card p-8 sm:p-10 group">
                <div class="grid gap-10 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ringkasan Kelas</p>
                        <h3 class="mt-2 text-3xl font-black text-slate-900 group-hover:text-brand-red transition-colors">{{ $kelas->name }}</h3>
                        @if($kelas->description)
                            <p class="mt-4 text-sm leading-relaxed text-slate-500 font-medium">{{ $kelas->description }}</p>
                        @else
                            <p class="mt-4 text-sm italic text-slate-300">Belum ada deskripsi kelas.</p>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center gap-5 rounded-3xl bg-slate-50 p-8 border border-slate-100">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kode Akses</p>
                            <p class="mt-1 font-mono text-4xl font-black tracking-[0.2em] text-brand-red">{{ $kelas->class_code }}</p>
                        </div>
                        <div class="flex justify-between border-t border-slate-200/60 pt-5 text-sm">
                            <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Siswa Bergabung</span>
                            <span class="text-xl font-black text-slate-900">{{ $daftarSiswa->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Modul Materi</span>
                            <span class="text-xl font-black text-slate-900">{{ $daftarMateri->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Materi / Siswa --}}
            <div class="premium-card overflow-hidden">
                <div class="flex border-b border-slate-100 bg-slate-50/50 px-2 pt-2 sm:px-6">
                    <button type="button" @click="tab = 'materi'" :class="tab === 'materi' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-slate-400 hover:text-slate-600'" class="-mb-px flex-1 rounded-t-2xl border-b-4 px-6 py-4 text-center text-xs font-black uppercase tracking-widest transition sm:flex-none sm:px-10">
                        Daftar Materi
                    </button>
                    <button type="button" @click="tab = 'siswa'" :class="tab === 'siswa' ? 'border-brand-red text-brand-red bg-white' : 'border-transparent text-slate-400 hover:text-slate-600'" class="-mb-px flex-1 rounded-t-2xl border-b-4 px-6 py-4 text-center text-xs font-black uppercase tracking-widest transition sm:flex-none sm:px-10">
                        Daftar Siswa
                    </button>
                </div>

                <div class="p-5 sm:p-8" x-show="tab === 'materi'" x-cloak>
                    @if(!($materiTerhubungBab ?? true))
                        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            <p class="font-bold">Skema database belum lengkap</p>
                            <p class="mt-1">Jalankan <code class="rounded bg-white/80 px-1 font-mono text-xs">php artisan migrate</code> agar modul materi terhubung ke bab (kolom <code class="font-mono">bab_id</code>).</p>
                        </div>
                    @endif
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600">Modul ditambahkan per bab. Buka sebuah bab, lalu pilih <strong class="text-gray-800">Tambah materi</strong>.</p>
                    </div>
                    @if($daftarMateri->isEmpty())
                        <p class="py-12 text-center text-sm italic text-gray-400">Belum ada modul materi untuk kelas ini.</p>
                    @else
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($daftarMateri as $modul)
                                @php
                                    $pathIkon = match ($modul->tipe) {
                                        \App\Models\Materi::TIPE_VIDEO => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
                                        \App\Models\Materi::TIPE_PDF => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                                        default => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                    };
                                @endphp
                                <div class="premium-card group p-6 border-slate-100 bg-slate-50/30 hover:bg-white transition-all flex flex-col h-full relative">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-brand-red shadow-sm ring-1 ring-slate-100 group-hover:bg-brand-red group-hover:text-white transition-all">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $pathIkon }}"/></svg>
                                            </div>
                                            <div class="min-w-0 flex-1 pr-12">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                                                    Modul {{ $modul->order }}
                                                </p>
                                                <h4 class="text-lg font-black text-slate-900 group-hover:text-brand-red transition-colors leading-tight">{{ $modul->judul }}</h4>
                                            </div>
                                            <div class="absolute right-6 top-6 rounded-lg bg-brand-red-light px-2 py-1 text-[9px] font-black uppercase tracking-widest text-brand-red shadow-sm">
                                                {{ $modul->labelTipe() }}
                                            </div>
                                        </div>
                                        @if($modul->ringkasan)
                                            <p class="mt-4 line-clamp-2 text-sm text-slate-500 font-medium leading-relaxed">{{ $modul->ringkasan }}</p>
                                        @endif
                                    </div>
                                    <div class="mt-6 flex flex-wrap gap-2 border-t border-slate-100 pt-5 mt-auto">
                                        @if(($materiTerhubungBab ?? true) && $modul->bab)
                                            <a href="{{ route('guru.bab.tampil', $modul->bab) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-600 hover:border-brand-red hover:text-brand-red transition-all">Bab</a>
                                        @endif
                                        <a href="{{ route('guru.materi.edit', $modul) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-600 hover:border-brand-red hover:text-brand-red transition-all">Edit</a>
                                        <a href="{{ route('siswa.materi.tampil', $modul->slug) }}" target="_blank" rel="noopener" class="btn-brand flex-1 py-2.5 text-xs">Pratinjau</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-5 sm:p-8" x-show="tab === 'siswa'" x-cloak>
                    @if($daftarSiswa->isEmpty())
                        <p class="py-12 text-center text-sm italic text-gray-400">Belum ada siswa yang bergabung.</p>
                    @else
                        <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="py-4 pl-6 pr-4 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Siswa</th>
                                            <th class="py-4 px-6 text-left text-[10px] font-black uppercase tracking-widest text-slate-400">Progres Baca Bab</th>
                                            <th class="py-4 px-6 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50 bg-white">
                                        @foreach($daftarSiswa as $siswa)
                                            <tr class="transition hover:bg-slate-50/50">
                                                <td class="py-5 pl-6 pr-4">
                                                    <span class="text-sm font-bold text-slate-900">{{ $siswa->name }}</span>
                                                </td>
                                                <td class="py-5 px-6">
                                                    <div class="flex items-center gap-4">
                                                        <div class="h-2 w-full max-w-[12rem] overflow-hidden rounded-full bg-slate-100 shadow-inner">
                                                            <div class="h-full rounded-full bg-brand-red shadow-[0_0_8px_rgba(237,30,40,0.3)] transition-all" style="width: {{ min(100, $siswa->persen_kemajuan) }}%"></div>
                                                        </div>
                                                        <span class="text-xs font-black text-brand-red">{{ round($siswa->persen_kemajuan) }}%</span>
                                                    </div>
                                                </td>
                                                <td class="py-5 px-6 text-center">
                                                    <button type="button" @click="confirmKeluarkanSiswa(@js($siswa->name), @js(route('guru.kelas.keluarkan-siswa', [$kelas, $siswa])))" class="rounded-lg px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-brand-red hover:bg-brand-red-light transition-all">
                                                        Keluarkan
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form id="form-keluarkan-siswa" action="" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>

            {{-- Bab & ujian (di luar tab, tetap mudah diakses) --}}
            <div class="rounded-3xl bg-white p-6 shadow-[0_20px_50px_-20px_rgba(15,23,42,0.12)] ring-1 ring-gray-100 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl sm:p-8">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-gray-900">Bab &amp; ujian</h3>
                        <p class="mt-1 text-sm text-gray-500">Kelola ringkasan bab, soal ujian, dan urutan pembelajaran.</p>
                    </div>
                    <form action="{{ route('guru.bab.simpan', $kelas) }}" method="POST" class="flex flex-wrap items-end gap-2">
                        @csrf
                        <div>
                            <label for="title-bab" class="sr-only">Judul bab</label>
                            <input type="text" name="title" id="title-bab" placeholder="Judul bab baru" required class="min-w-[12rem] rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        </div>
                        <div>
                            <label for="order-bab" class="sr-only">Urutan</label>
                            <input type="number" name="order" id="order-bab" value="{{ $daftarBab->count() + 1 }}" required class="w-20 rounded-xl border-gray-200 text-sm shadow-sm focus:border-[#ed1e28] focus:ring-[#ed1e28]">
                        </div>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-[#ed1e28] px-4 text-sm font-bold text-white shadow-md transition hover:bg-[#c91a23]">
                            + Tambah bab
                        </button>
                        <a href="{{ route('guru.bab.buat', $kelas) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-gray-200 px-3 text-xs font-bold text-gray-600 hover:bg-gray-50">Form lengkap</a>
                    </form>
                </div>
                @if($daftarBab->isEmpty())
                    <p class="py-10 text-center text-sm italic text-gray-400">Belum ada bab di kelas ini.</p>
                @else
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($daftarBab as $bab)
                            <div class="premium-card group p-8 relative overflow-hidden flex flex-col h-full">
                                <div class="absolute left-0 top-0 h-full w-2 bg-brand-red opacity-10 group-hover:opacity-100 transition-opacity"></div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4 mb-4">
                                        <div class="h-10 w-10 rounded-xl bg-brand-red-light flex items-center justify-center text-brand-red font-black text-xs">
                                            {{ $bab->order }}
                                        </div>
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <span class="rounded-lg px-2.5 py-1 text-[9px] font-black uppercase tracking-widest {{ $bab->teksBab ? 'bg-green-50 text-green-600' : 'bg-slate-50 text-slate-400' }}">
                                                Ringkasan: {{ $bab->teksBab ? 'Ada' : 'Kosong' }}
                                            </span>
                                            <span class="rounded-lg bg-brand-red-light px-2.5 py-1 text-[9px] font-black uppercase tracking-widest text-brand-red">
                                                Soal: {{ $bab->daftar_soal_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-900 group-hover:text-brand-red transition-colors leading-tight mb-8">{{ $bab->title }}</h4>
                                </div>
                                <a href="{{ route('guru.bab.tampil', $bab) }}" class="btn-brand w-full py-3.5 text-xs mt-auto">
                                    Kelola Bab & Soal
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.confirmKeluarkanSiswa = function (namaSiswa, url) {
                const form = document.getElementById('form-keluarkan-siswa');
                const jalankanKeluarkan = () => {
                    if (form) {
                        form.action = url;
                        form.submit();
                    }
                };

                if (typeof window.Swal === 'undefined') {
                    if (confirm('Keluarkan ' + namaSiswa + ' dari kelas ini?')) {
                        jalankanKeluarkan();
                    }
                    return;
                }

                window.Swal.fire({
                    title: 'Keluarkan siswa?',
                    text: 'Siswa ' + namaSiswa + ' akan dikeluarkan dari kelas ini. Data progres belajarnya di kelas ini tetap ada di sistem, namun ia tidak lagi memiliki akses ke kelas ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, keluarkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    confirmButtonColor: '#ed1e28',
                    cancelButtonColor: '#6b7280',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        jalankanKeluarkan();
                    }
                });
            };

            window.addEventListener('load', function () {
                document.getElementById('tombol-hapus-kelas')?.addEventListener('click', function () {
                    const form = document.getElementById('form-hapus-kelas');
                    const jalankanHapus = () => form && form.submit();

                    if (typeof window.Swal === 'undefined') {
                        if (confirm('Hapus kelas ini secara permanen? Data bab, materi, dan tautan siswa ikut terhapus.')) {
                            jalankanHapus();
                        }
                        return;
                    }

                    window.Swal.fire({
                        title: 'Hapus kelas?',
                        text: 'Kelas ' + @json($kelas->name) + ' akan dihapus. Relasi siswa dan data bab/materi ikut terhapus (cascade database). Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true,
                        confirmButtonColor: '#ed1e28',
                        cancelButtonColor: '#6b7280',
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            jalankanHapus();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
