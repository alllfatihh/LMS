<x-app-layout>
    <x-slot name="header">
        <span class="sr-only">Ujian berlangsung</span>
    </x-slot>

    <style>
        body > div.min-h-screen > nav { display: none !important; }
        body > div.min-h-screen > header { display: none !important; }
        .exam-anti-gravity-bg {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(237, 30, 40, 0.08), transparent 55%),
                radial-gradient(900px 500px at 100% 20%, rgba(237, 30, 40, 0.06), transparent 50%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }
    </style>

    <div class="exam-anti-gravity-bg min-h-[calc(100vh-0px)] pb-16">
        {{-- Floating top bar --}}
        <div class="sticky top-0 z-50 px-4 sm:px-6 pt-4">
            <div class="max-w-2xl mx-auto rounded-3xl bg-white/80 backdrop-blur-xl border border-white shadow-premium px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-brand-red text-white rounded-2xl flex items-center justify-center font-black shadow-brand-glow text-base">
                        {{ $sesiUjian->bab->order }}
                    </div>
                    <div>
                        <p class="font-black text-slate-900 text-sm leading-tight">Sesi Ujian</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bab {{ $sesiUjian->bab->title }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 rounded-2xl bg-slate-900 text-white px-4 py-2.5 shadow-xl" id="timer-wrapper">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-mono font-black text-sm tracking-widest" id="exam-timer">00:00:00</span>
                </div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            {{-- Meta row (floating strip) --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500 px-1">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/80 px-3 py-1.5 border border-gray-100 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Bab <strong class="text-gray-800">{{ $sesiUjian->bab->title }}</strong></span>
                </span>
                <span class="font-mono text-gray-400 rounded-full bg-white/70 px-3 py-1.5 border border-gray-100">Sesi #{{ $sesiUjian->id }}</span>
            </div>

            {{-- Main floating card --}}
            <div class="premium-card overflow-hidden">
                <div class="h-2 w-full bg-brand-red"></div>

                <div class="px-8 pt-10 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-red-light flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-brand-red">Latihan Kuis</p>
                            <h2 class="text-gray-900 font-black text-2xl leading-tight">Kerjakan Soal</h2>
                        </div>
                    </div>
                </div>

                <div class="px-8 pb-10 pt-4">
                    <div class="rounded-3xl bg-slate-50 border border-slate-100 p-8 mb-8">
                        <div class="prose prose-slate prose-lg max-w-none text-slate-900 font-bold leading-relaxed">
                            {!! nl2br(e($teksSoalTerurai)) !!}
                        </div>
                    </div>

                        <div class="mb-8">
                            <label for="student_answer" class="flex items-center gap-2 text-xs font-black uppercase tracking-widest text-slate-400 mb-4 px-2">
                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Masukkan Jawaban
                            </label>
                            <input
                                type="number"
                                step="any"
                                name="student_answer"
                                id="student_answer"
                                class="block w-full border-2 border-slate-100 focus:border-brand-red focus:ring-4 focus:ring-brand-red/10 rounded-2xl py-6 px-8 text-3xl font-black text-slate-900 text-center transition-all bg-slate-50/50"
                                placeholder="0"
                                required
                                autofocus
                            >
                        </div>

                        <div class="rounded-2xl bg-amber-50 border border-amber-100 px-6 py-4 flex gap-4 text-xs font-bold text-amber-700 mb-8">
                            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>Gunakan titik <strong>(.)</strong> untuk desimal. Contoh: <code class="bg-amber-100/50 px-2 py-0.5 rounded-lg font-black">3.5</code></p>
                        </div>

                        <button
                            type="submit"
                            id="submit-btn"
                            class="btn-brand w-full py-5 text-lg"
                            onclick="this.disabled=true; this.innerHTML='<svg class=\'animate-spin w-6 h-6 mr-3 text-white\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg><span>Memeriksa...</span>'; this.closest('form').submit();"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            <span>Jawab & Lanjut</span>
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-[11px] text-gray-400 mt-8 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-brand-red/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Setelah ujian selesai, tombol kembali browser akan mengarahkan ke hasil (bukan soal).</span>
            </p>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const startTime = new Date("{{ $sesiUjian->started_at->toIso8601String() }}").getTime();
            const timerElement = document.getElementById('exam-timer');
            const timerWrapper = document.getElementById('timer-wrapper');
            if (!timerElement) return;

            function updateTimer() {
                const now = Date.now();
                const distance = now - startTime;
                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                timerElement.innerText =
                    (hours < 10 ? "0" : "") + hours + ":" +
                    (minutes < 10 ? "0" : "") + minutes + ":" +
                    (seconds < 10 ? "0" : "") + seconds;
                if (distance > 600000 && timerWrapper) {
                    timerWrapper.classList.add('animate-pulse');
                }
            }
            setInterval(updateTimer, 1000);
            updateTimer();
        });
    </script>
    @endpush
</x-app-layout>
