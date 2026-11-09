<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="{{ route('guru.kelas.tampil', $bab->kelas_id) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-400 border border-slate-200 shadow-sm transition-all hover:text-brand-red hover:border-brand-red">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-xl text-gray-800 leading-tight">{{ $bab->title }}</h2>
                    <p class="text-sm text-gray-400 mt-0.5">Bab {{ $bab->order }} · {{ $bab->kelas->name }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('guru.teks.edit', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Edit Ringkasan
                </a>
                <a href="{{ route('guru.bab.edit', $bab) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                    Ubah Judul Bab
                </a>
                <form action="{{ route('guru.bab.hapus', $bab) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Yakin ingin menghapus bab ini beserta materi dan soal di dalamnya? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-5 text-sm font-bold text-red-600 transition hover:bg-red-100">
                        Hapus Bab
                    </button>
                </form>
                <a href="{{ route('guru.soal.buat', $bab) }}" class="btn-brand h-11 px-5">
                    + Tambah Soal Manual
                </a>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-ai-soal-modal'))" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-indigo-50 px-5 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100 shadow-sm border border-indigo-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    ✨ Generate AI
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-[1800px] mx-auto sm:px-6 lg:px-8 space-y-8">
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
                                        <a href="{{ route('siswa.materi.tampil', $modul->slug) }}" class="text-xs font-bold text-gray-600 hover:underline px-2 py-1 rounded-lg border border-gray-200">Pratinjau</a>
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
                                            <div class="text-sm font-bold text-gray-800">{!! app(\App\Services\EvaluasiSoalService::class)->formatHTML($soal->question_template) !!}</div>
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
                        
                        <!-- Modal AI Soal (diletakkan di dalam card soal) -->
                        <div x-data="{ 
                                open: false, 
                                topik: '', 
                                jumlah: 3, 
                                loading: false,
                                sisaKuota: null,
                                batasKuota: 5,
                                async init() {
                                    try {
                                        const res = await fetch('{{ route('guru.ai.sisa-kuota') }}');
                                        const d = await res.json();
                                        this.sisaKuota = d.sisa_generate;
                                        this.batasKuota = d.batas_generate;
                                    } catch(e) {}
                                },
                                async generate() {
                                    if(!this.topik) return alert('Topik harus diisi');
                                    if (this.sisaKuota !== null && this.sisaKuota <= 0) {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Kuota Habis',
                                            text: 'Kuota AI harian Anda telah habis (' + this.batasKuota + ' kali/hari). Silakan coba lagi besok.',
                                            confirmButtonColor: '#ed1e28'
                                        });
                                        return;
                                    }
                                    this.loading = true;
                                    try {
                                        const res = await fetch('{{ route('guru.ai.generate-soal', $bab->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                            },
                                            body: JSON.stringify({ topik: this.topik, jumlah: parseInt(this.jumlah) })
                                        });
                                        const data = await res.json();
                                        if (data.sisa !== undefined) this.sisaKuota = data.sisa;
                                        if (data.batas !== undefined) this.batasKuota = data.batas;
                                        if(res.status === 429) {
                                            this.sisaKuota = 0;
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Kuota Habis',
                                                text: data.pesan || 'Kuota AI harian Anda telah habis.',
                                                confirmButtonColor: '#ed1e28'
                                            });
                                            return;
                                        }
                                        if(data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: data.pesan + ' (Sisa kuota: ' + this.sisaKuota + '/' + this.batasKuota + ')',
                                                confirmButtonColor: '#4f46e5'
                                            }).then(() => {
                                                window.location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Peringatan AI',
                                                text: data.pesan || 'Gagal generate soal.',
                                                confirmButtonColor: '#ed1e28'
                                            });
                                        }
                                    } catch(e) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan jaringan atau server kepenuhan (Error 503).',
                                            confirmButtonColor: '#ed1e28'
                                        });
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }" 
                            @open-ai-soal-modal.window="open = true">
                            
                            <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="flex min-h-screen items-center justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                                    <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                        <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
                                    </div>
                                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                                    <div x-show="open" x-transition.scale class="inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                </div>
                                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                                    <h3 class="text-lg font-extrabold leading-6 text-gray-900">Generate Soal dengan AI</h3>
                                                    <div class="mt-2 space-y-4">
                                                        <p class="text-xs text-gray-500">AI akan membuatkan template soal matematika dinamis & kompleks sesuai topik dan kurikulum SMP.</p>
                                                        <div x-show="sisaKuota !== null" class="flex items-center gap-2">
                                                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold" :class="sisaKuota > 0 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-red-50 text-red-700 ring-1 ring-red-200'">
                                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                                Sisa kuota: <span x-text="sisaKuota"></span>/<span x-text="batasKuota"></span> hari ini
                                                            </span>
                                                        </div>

                                                        <!-- Preset Topik Cepat -->
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Preset Topik Cepat (Kompleks):</label>
                                                            <div class="flex flex-wrap gap-1.5">
                                                                <button type="button" @click="topik = 'SPLDV dua variabel soal cerita kontekstual dengan solusi bilangan bulat'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">SPLDV</button>
                                                                <button type="button" @click="topik = 'Persamaan Kuadrat menentukan akar dan jumlah/hasil kali akar'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Persamaan Kuadrat</button>
                                                                <button type="button" @click="topik = 'Teorema Pythagoras dan tripel pythagoras pada segitiga dan soal cerita'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Pythagoras</button>
                                                                <button type="button" @click="topik = 'Luas dan volume bangun ruang sisi lengkung (tabung/kerucut) dengan pi 22/7'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Bangun Ruang</button>
                                                                <button type="button" @click="topik = 'Statistika data tunggal mencari kuartil (Q1, Q3) dan jangkauan interkuartil'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Statistika & Kuartil</button>
                                                                <button type="button" @click="topik = 'Peluang pengambilan bola/kelereng dengan jawaban pecahan biasa'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Peluang (Pecahan)</button>
                                                                <button type="button" @click="topik = 'Barisan dan deret aritmetika menentukan suku ke-n dan jumlah n suku pertama'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Barisan Aritmetika</button>
                                                                <button type="button" @click="topik = 'Aritmetika sosial tentang untung rugi, persentase diskon dan pajak'" class="text-[11px] font-semibold bg-gray-100 hover:bg-indigo-50 hover:text-indigo-600 px-2.5 py-1 rounded-lg border border-gray-200 transition-colors">Aritmetika Sosial</button>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-700 mb-1">Topik / Deskripsi Khusus</label>
                                                            <textarea x-model="topik" rows="2" placeholder="Tuliskan topik atau tipe soal yang diinginkan..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" :disabled="sisaKuota !== null && sisaKuota <= 0"></textarea>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah Soal</label>
                                                            <input type="number" x-model="jumlah" min="1" max="10" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" :disabled="sisaKuota !== null && sisaKuota <= 0">
                                                            <p class="text-xs text-gray-400 mt-1">Maksimal 10 soal sekali generate.</p>
                                                        </div>
                                                        <p x-show="sisaKuota !== null && sisaKuota <= 0" class="text-xs text-red-600 font-bold">Kuota harian Anda sudah habis. Silakan coba lagi besok.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                            <button type="button" @click="generate()" :disabled="loading" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2 text-base font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors">
                                                <span x-show="!loading">Buat Soal</span>
                                                <span x-show="loading" class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                    Memproses...
                                                </span>
                                            </button>
                                            <button type="button" @click="open = false" :disabled="loading" class="mt-3 inline-flex w-full justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
