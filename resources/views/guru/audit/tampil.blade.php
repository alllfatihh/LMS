<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Ujian Siswa: ') }} {{ $sesiUjian->user->name }}
            </h2>
            <a href="{{ route('guru.kelas.tampil', $sesiUjian->bab->kelas_id) }}" class="inline-flex items-center text-sm text-brand-red font-medium hover:text-brand-red-dark">
                &larr; Kembali ke Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Info Header --}}
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">{{ $sesiUjian->bab->title }}</h3>
                    <p class="text-slate-500 font-medium">Kelas: {{ $sesiUjian->bab->kelas->name }}</p>
                    <div class="mt-4 flex gap-4 text-sm font-medium text-slate-600">
                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Mulai: {{ $sesiUjian->started_at->format('d M Y, H:i') }}</span>
                        <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Selesai: {{ $sesiUjian->finished_at ? $sesiUjian->finished_at->format('d M Y, H:i') : 'Belum Selesai' }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Skor Akhir</div>
                    <div class="text-4xl font-black {{ $nilai >= 70 ? 'text-emerald-600' : ($nilai >= 40 ? 'text-amber-500' : 'text-brand-red') }}">
                        {{ round($nilai) }}%
                    </div>
                    <p class="text-sm text-slate-500 font-medium mt-1">{{ $jumlahBenar }} benar dari {{ $jumlahSoal }} soal</p>
                </div>
            </div>

            {{-- Audit List --}}
            <div class="space-y-6">
                @foreach($auditList as $index => $audit)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                            <h4 class="font-bold text-slate-800">Soal #{{ $index + 1 }}</h4>
                            @if($audit->is_correct)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Benar
                                </span>
                            @elseif(is_null($audit->is_correct) && is_null($audit->jawaban_siswa))
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg> Kosong
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-red-light/20 text-brand-red text-xs font-bold uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Salah
                                </span>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- Kiri: Detail Soal --}}
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Teks Soal Yang Diterima Siswa (Seed Teraplikasi)</div>
                                        <div class="p-4 bg-slate-50 rounded-xl text-slate-800 font-medium text-lg leading-relaxed border border-slate-100">
                                            {!! nl2br(e($audit->soal_terurai)) !!}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Variabel Acak (Seed)</div>
                                        <div class="bg-slate-800 text-emerald-400 font-mono text-sm p-4 rounded-xl overflow-x-auto shadow-inner">
                                            @if($audit->variabel_seed && count($audit->variabel_seed) > 0)
                                                <ul class="space-y-1">
                                                    @foreach($audit->variabel_seed as $key => $val)
                                                        <li><span class="text-slate-400">let</span> {{ $key }} <span class="text-slate-400">=</span> <span class="text-yellow-400">{{ $val }}</span>;</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-slate-500">Tidak ada variabel ter-generate.</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Kanan: Detail Evaluasi --}}
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Rumus Kunci Jawaban</div>
                                        <div class="bg-blue-50 text-blue-800 font-mono text-sm p-4 rounded-xl border border-blue-100">
                                            {{ $audit->rumus_jawaban ?: '-' }}
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 rounded-xl border {{ $audit->is_correct === false ? 'bg-brand-red-light/10 border-brand-red/20' : 'bg-slate-50 border-slate-100' }}">
                                            <div class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Jawaban Siswa</div>
                                            <div class="text-xl font-bold {{ $audit->is_correct === false ? 'text-brand-red' : 'text-slate-800' }}">
                                                {{ $audit->jawaban_siswa ?? '(Kosong)' }}
                                            </div>
                                        </div>
                                        <div class="p-4 rounded-xl border bg-emerald-50 border-emerald-100">
                                            <div class="text-xs font-black text-emerald-600 uppercase tracking-wider mb-2">Jawaban Sistem</div>
                                            <div class="text-xl font-bold text-emerald-700">
                                                {{ $audit->kunci_jawaban ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
