<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900 leading-tight">Hasil ujian</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $sesiUjian->bab->kelas->name ?? 'Kelas' }} — {{ $sesiUjian->bab->title }}</p>
            </div>
            <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center justify-center gap-2 text-brand-red text-sm font-semibold border-2 border-brand-red px-4 py-2.5 rounded-xl hover:bg-brand-red-light transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke kelas</span>
            </a>
        </div>
    </x-slot>

    <style>
        .result-anti-gravity-bg {
            background:
                radial-gradient(1000px 480px at 0% 0%, rgba(237, 30, 40, 0.07), transparent 50%),
                radial-gradient(800px 400px at 100% 10%, rgba(237, 30, 40, 0.05), transparent 45%),
                linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
        }
    </style>

    @php
        $isPassed = $nilai >= 80;
        $duration = $sesiUjian->finished_at && $sesiUjian->started_at
            ? $sesiUjian->finished_at->diffInMinutes($sesiUjian->started_at)
            : null;
    @endphp

    <div class="result-anti-gravity-bg py-10 min-h-[60vh]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 px-4 space-y-8">

            {{-- Hero score card --}}
            <div class="rounded-2xl bg-white shadow-[0_24px_48px_-12px_rgba(15,23,42,0.15)] border border-white ring-1 ring-black/[0.04] overflow-hidden -translate-y-0.5">
                <div class="h-1.5 w-full {{ $isPassed ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-brand-red to-brand-red-dark' }}"></div>
                <div class="px-6 sm:px-10 py-8 sm:py-10 text-center bg-gradient-to-b from-white to-gray-50/80">
                    @if($isPassed)
                        <div class="text-5xl mb-2" aria-hidden="true"></div>
                        <h3 class="text-gray-900 font-black text-2xl sm:text-3xl tracking-tight">Selamat, kamu lulus</h3>
                        <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">Nilai memenuhi ambang kelulusan (Minimal 80). Terus latihan untuk mempertajam pemahaman.</p>
                    @else
                        <div class="text-5xl mb-2" aria-hidden="true"></div>
                        <h3 class="text-gray-900 font-black text-2xl sm:text-3xl tracking-tight">Tetap semangat</h3>
                        <p class="text-gray-500 text-sm mt-2 max-w-md mx-auto">Nilai kamu di bawah 80. Pelajari pembahasan di bawah lalu ulangi kuis ini untuk memperbaiki nilaimu.</p>
                    @endif
                </div>

                <div class="px-6 sm:px-10 pb-10 grid sm:grid-cols-2 gap-8 items-center border-t border-gray-100 bg-white/90">
                    <div class="flex justify-center">
                        <div class="relative w-40 h-40">
                            <svg class="w-40 h-40 -rotate-90 drop-shadow-sm" viewBox="0 0 36 36" aria-hidden="true">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="2.8"/>
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                    stroke="{{ $isPassed ? '#10b981' : '#ed1e28' }}"
                                    stroke-width="2.8"
                                    stroke-dasharray="{{ min(100, max(0, $nilai)) }}, 100"
                                    stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-4xl font-black tabular-nums {{ $isPassed ? 'text-emerald-600' : 'text-brand-red' }}">{{ number_format($nilai, 0) }}</span>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">dari 100</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3 flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 ring-1 ring-emerald-100">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Benar</p>
                                <p class="font-extrabold text-lg text-gray-900">{{ $jumlahBenar }} soal</p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-brand-red-light border border-red-100 px-4 py-3 flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center shrink-0 ring-1 ring-brand-red/15">
                                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Salah</p>
                                <p class="font-extrabold text-lg text-gray-900">{{ $jumlahSoal - $jumlahBenar }} soal</p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3 flex items-center gap-3 shadow-sm">
                            <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Total</p>
                                <p class="font-extrabold text-lg text-gray-900">{{ $jumlahSoal }} soal</p>
                            </div>
                        </div>
                        @if($duration !== null)
                        <div class="rounded-2xl bg-brand-red-light/60 border border-red-100 px-4 py-3 flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center shrink-0 ring-1 ring-brand-red/10">
                                <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Durasi</p>
                                <p class="font-extrabold text-lg text-gray-900">{{ $duration }} menit</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Discussion floating card --}}
            <div class="rounded-2xl bg-white shadow-[0_16px_40px_-12px_rgba(15,23,42,0.12)] border border-white ring-1 ring-black/[0.04] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3 bg-gradient-to-r from-brand-red-light to-white">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm ring-1 ring-brand-red/10">
                        <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-gray-900">Pembahasan</h4>
                        <p class="text-xs text-gray-500">Soal, jawabanmu, dan kunci (dibulatkan untuk tampilan)</p>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($sesiUjian->daftarJawaban as $index => $ans)
                        @php
                            $correctAnswer = null;
                            try {
                                $evaluator = app(\App\Services\EvaluasiSoalService::class);
                                $correctAnswer = $evaluator->hitungRumus(
                                    $ans->soal->answer_formula,
                                    $ans->generated_variables
                                );
                            } catch (\Exception $e) {
                                $correctAnswer = null;
                            }
                            $parsedQ = '';
                            try {
                                $evaluator2 = app(\App\Services\EvaluasiSoalService::class);
                                $parsedQ = $evaluator2->uraiTemplatSoal(
                                    $ans->soal->question_template,
                                    $ans->generated_variables
                                );
                            } catch (\Exception $e) {
                                $parsedQ = $ans->soal->question_template;
                            }
                            $fmtCorrect = is_numeric($correctAnswer)
                                ? rtrim(rtrim(number_format((float) $correctAnswer, 6, '.', ''), '0'), '.') ?: '0'
                                : ($correctAnswer ?? 'N/A');
                        @endphp
                        <div class="p-6 sm:p-7 {{ $ans->is_correct ? 'bg-white' : 'bg-brand-red-light/40' }}">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-black shrink-0
                                        {{ $ans->is_correct ? 'bg-emerald-100 text-emerald-800' : 'bg-white text-brand-red ring-2 ring-brand-red/20' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Soal {{ $index + 1 }}</span>
                                </div>
                                @if($ans->is_correct)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold px-3 py-1 border border-emerald-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Benar
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white text-brand-red text-xs font-bold px-3 py-1 border border-red-200 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Salah
                                    </span>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3 mb-4 ml-0 sm:ml-12">
                                <p class="text-gray-800 text-sm leading-relaxed">{{ $parsedQ }}</p>
                            </div>

                            <div class="ml-0 sm:ml-12 grid sm:grid-cols-2 gap-3">
                                <div class="rounded-2xl border px-4 py-3 {{ $ans->is_correct ? 'border-emerald-200 bg-emerald-50/40' : 'border-red-200 bg-white' }}">
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Jawabanmu</p>
                                    <p class="font-mono font-extrabold text-lg {{ $ans->is_correct ? 'text-emerald-700' : 'text-brand-red' }}">
                                        {{ $ans->student_answer ?? '—' }}
                                    </p>
                                </div>
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 px-4 py-3">
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1">Kunci</p>
                                    <p class="font-mono font-extrabold text-lg text-emerald-800">{{ $fmtCorrect }}</p>
                                </div>
                            </div>

                            @if(!$ans->is_correct)
                            <div class="mt-4 ml-0 sm:ml-12 rounded-2xl bg-amber-50 border border-amber-100 px-4 py-3 text-xs text-amber-900">
                                <p class="font-bold mb-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                    Tips
                                </p>
                                <p>
                                    Rumus <code class="font-mono bg-amber-100/80 px-1 rounded font-bold">{{ $ans->soal->answer_formula }}</code>
                                    @foreach($ans->generated_variables as $var => $val)
                                        <span class="inline-block mt-1 mr-1"><code class="font-mono bg-amber-100/80 px-1 rounded">{{ $var }} = {{ $val }}</code></span>
                                    @endforeach
                                </p>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pb-4">
                <a href="{{ route('siswa.ujian.index') }}"
                   class="flex-1 text-center rounded-2xl bg-white border-2 border-slate-200 text-slate-700 hover:bg-slate-50 font-extrabold py-4 px-6 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Daftar Kelas</span>
                </a>
                <a href="{{ route('siswa.ujian.riwayat') }}"
                   class="flex-1 text-center rounded-2xl bg-white border-2 border-brand-red text-brand-red hover:bg-brand-red-light font-extrabold py-4 px-6 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Ujian</span>
                </a>
                @if(!$isPassed)
                <form action="{{ route('siswa.ujian.bab.mulai', $sesiUjian->bab->id) }}" method="POST" class="flex-[1.5]">
                    @csrf
                    <button type="submit" class="w-full h-full rounded-2xl bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-red-800 text-white font-extrabold py-4 px-6 shadow-lg shadow-brand-red/30 hover:shadow-brand-glow transform transition hover:-translate-y-1 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Coba Ulangi Kuis</span>
                    </button>
                </form>
                @endif
            </div>
</x-app-layout>
