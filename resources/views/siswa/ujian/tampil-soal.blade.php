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

        /* Micro-animations */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up {
            animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        /* Premium Card Improvements */
        .premium-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05), 0 0 0 1px rgba(0,0,0,0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .premium-card:hover {
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);
        }
        
        /* Navigation Button Hover Effect */
        .nav-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="exam-anti-gravity-bg min-h-[calc(100vh-0px)] pb-16 font-sans">
        {{-- Top bar (Not sticky) --}}
        <div class="px-4 sm:px-6 pt-6">
            <div class="max-w-6xl mx-auto rounded-3xl bg-white/70 backdrop-blur-2xl border border-white/50 shadow-lg shadow-slate-200/50 px-6 py-4 flex justify-between items-center transition-all">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-gradient-to-br from-brand-red to-brand-red-dark text-white rounded-2xl flex items-center justify-center font-black shadow-lg shadow-brand-red/30 text-lg">
                        {{ $sesiUjian->bab->order }}
                    </div>
                    <div>
                        <p class="font-black text-slate-900 text-sm md:text-base leading-tight">Sesi Latihan Ujian</p>
                        <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Bab {{ $sesiUjian->bab->title }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-slate-900 text-white px-5 py-3 shadow-xl ring-4 ring-slate-900/10" id="timer-wrapper">
                    <svg class="w-5 h-5 text-brand-red animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-mono font-black text-base tracking-widest" id="exam-timer">00:00:00</span>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Kiri: Area Soal --}}
                <div class="lg:col-span-8 animate-slide-up" style="animation-delay: 0.1s;">
                    {{-- Meta row (floating strip) --}}
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 px-1">
                        <div class="flex gap-3">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 border border-slate-200 shadow-sm font-bold">
                                <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Soal <strong class="text-slate-900 text-sm">{{ $nomorSoalAktif }}</strong> dari {{ $totalSoal }}</span>
                            </span>
                            @if($sisaSoal > 0)
                                <span class="inline-flex items-center rounded-full bg-amber-50 text-amber-700 px-4 py-2 border border-amber-200 font-semibold shadow-sm">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2 animate-pulse"></span> Sisa {{ $sisaSoal }} belum dijawab
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 px-4 py-2 border border-emerald-200 font-semibold shadow-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Semua soal terjawab
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Main floating card --}}
                    <div class="premium-card relative overflow-hidden">
                        {{-- Top color accent --}}
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-brand-red to-orange-400"></div>

                        <div class="px-8 pt-10 pb-6 border-b border-slate-50">
                            <div class="flex items-start gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center shrink-0 border border-red-100 shadow-inner">
                                    <svg class="w-7 h-7 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="pt-1">
                                    <p class="text-[11px] font-black uppercase tracking-widest text-brand-red mb-1">Pertanyaan Analitik</p>
                                    <h2 class="text-slate-900 font-black text-2xl leading-tight">Kerjakan Soal Berikut</h2>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 pb-10 pt-8 bg-slate-50/30">
                            <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-8 mb-10 transition-colors hover:border-brand-red/30">
                                @if($jawaban->soal->image_path)
                                    <div class="mb-6 flex justify-center">
                                        <img src="{{ asset('storage/' . $jawaban->soal->image_path) }}" alt="Visual Soal" class="max-w-full h-auto max-h-80 rounded-2xl border border-slate-200 shadow-sm">
                                    </div>
                                @endif

                                @if(!empty($grafikTerurai))
                                    <div class="mb-6 rounded-2xl overflow-hidden border-2 border-slate-200 shadow-sm">
                                        <div id="desmos-calculator" style="width: 100%; height: 350px;"></div>
                                    </div>
                                @endif
                                <div class="prose prose-slate prose-lg max-w-none text-slate-800 font-medium leading-relaxed">
                                    {!! app(\App\Services\EvaluasiSoalService::class)->formatHTML($teksSoalTerurai) !!}
                                </div>
                                
                                {{-- Visualisasi Titik Koordinat (Jika format jawaban koordinat) --}}
                                <x-coordinate-visualizer :soal="$jawaban->soal" :teks="$teksSoalTerurai" />
                            </div>

                            <form action="{{ route('siswa.ujian.jawaban.kirim', $jawaban) }}" method="POST">
                                @csrf

                                <div class="mb-8">
                                    <label for="math_field_input" class="flex items-center gap-2 text-sm font-black uppercase tracking-widest text-slate-500 mb-4 px-2">
                                        <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Area Jawaban Anda (Keyboard Matematika)
                                    </label>
                                    <div class="relative group">
                                        <input type="hidden" name="student_answer" id="student_answer" value="{{ $jawaban->student_answer ?? '' }}" required>
                                        
                                        <math-field 
                                            id="math_field_input" 
                                            class="block w-full border-2 border-slate-200 focus-within:border-brand-red focus-within:ring-4 focus-within:ring-brand-red/20 rounded-2xl py-6 px-8 text-4xl font-black text-slate-900 text-center transition-all bg-white shadow-inner"
                                            math-virtual-keyboard-policy="manual"
                                        >
                                            {{ $jawaban->student_answer ?? '' }}
                                        </math-field>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 mt-4 text-center hidden" id="preview_numeric">
                                        Nilai desimal terdeteksi: <strong class="text-brand-red text-sm bg-red-50 px-2 py-0.5 rounded border border-red-100" id="preview_value"></strong>
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-amber-50 border border-amber-200 px-6 py-5 flex gap-4 text-sm font-semibold text-amber-800 mb-10 shadow-sm">
                                    <svg class="w-6 h-6 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="leading-relaxed">Gunakan ikon keyboard <svg class="w-4 h-4 inline-block -mt-1 mx-1 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> untuk menampilkan papan ketik matematika. Anda dapat mengetikkan <strong>pecahan</strong> maupun <strong>akar kuadrat</strong>.</p>
                                </div>

                                <button
                                    type="submit"
                                    id="submit-btn"
                                    class="w-full py-5 px-8 text-lg font-bold text-white bg-gradient-to-r from-brand-red to-brand-red-dark hover:from-brand-red-dark hover:to-red-800 rounded-2xl shadow-xl shadow-brand-red/30 transition-all transform hover:-translate-y-1 focus:ring-4 focus:ring-brand-red/50 flex items-center justify-center gap-3"
                                    onclick="
                                        const btn = this;
                                        const form = btn.closest('form');
                                        if (form.checkValidity()) {
                                            btn.style.pointerEvents = 'none';
                                            btn.innerHTML = '<svg class=\'animate-spin w-6 h-6 text-white\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z\'></path></svg><span>Menyimpan...</span>';
                                            setTimeout(function() { btn.style.pointerEvents = 'auto'; btn.innerHTML = '<svg class=\'w-6 h-6\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2.5\' d=\'M14 5l7 7m0 0l-7 7m7-7H3\'/></svg><span>Simpan &amp; Lanjut</span>'; }, 8000);
                                            form.submit();
                                        } else {
                                            form.reportValidity();
                                        }
                                    "
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span>Simpan & Lanjut</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                {{-- End Kiri --}}
                
                {{-- Kanan: Panel Navigasi --}}
                <div class="lg:col-span-4 animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="premium-card overflow-hidden sticky top-32">
                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                </div>
                                <h3 class="font-black text-slate-900 text-sm uppercase tracking-widest">Peta Navigasi</h3>
                            </div>
                            
                            <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-4 gap-3 mb-8">
                                @foreach($semuaJawaban as $index => $jwb)
                                    @php 
                                        $no = $index + 1;
                                        $isActive = $jawaban->id === $jwb->id;
                                        $isAnswered = !is_null($jwb->student_answer);
                                        
                                        if ($isActive) {
                                            $btnClass = "bg-brand-red text-white shadow-lg shadow-brand-red/40 ring-2 ring-brand-red ring-offset-2";
                                        } elseif ($isAnswered) {
                                            $btnClass = "bg-emerald-50 text-emerald-600 border-2 border-emerald-200 hover:bg-emerald-100";
                                        } else {
                                            $btnClass = "bg-white text-slate-500 border-2 border-slate-200 hover:border-slate-400 hover:text-slate-700";
                                        }
                                    @endphp
                                    <a href="{{ route('siswa.ujian.tampil-soal', ['sesi_ujian' => $sesiUjian->id, 'n' => $no]) }}" 
                                       class="nav-btn aspect-square flex items-center justify-center rounded-xl text-base font-black {{ $btnClass }}">
                                        {{ $no }}
                                    </a>
                                @endforeach
                            </div>
                            
                            <div class="pt-8 border-t border-slate-200">
                                <form action="{{ route('siswa.ujian.selesaikan', $sesiUjian->id) }}" method="POST" id="form-akhiri" onsubmit="
                                    let currentMathVal = document.getElementById('student_answer').value;
                                    let savedMathVal = '{{ $jawaban->student_answer ?? '' }}';
                                    if (currentMathVal !== savedMathVal && currentMathVal.trim() !== '') {
                                        alert('Kamu sudah mengetik jawaban di soal ini tapi belum menyimpannya. Silakan klik tombol \'Simpan & Lanjut\' terlebih dahulu sebelum mengakhiri ujian!');
                                        return false;
                                    }
                                    return confirm('Peringatan: Kamu tidak bisa mengubah jawaban lagi setelah ini.\n\nApakah kamu sudah yakin 100% untuk mengakhiri sesi ujian ini?');
                                ">
                                    @csrf
                                    <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-slate-900 text-white font-bold text-sm flex justify-center items-center gap-3 hover:bg-slate-800 transition-all transform hover:-translate-y-1 shadow-xl shadow-slate-900/20 focus:ring-4 focus:ring-slate-900/30">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Akhiri & Kumpulkan Ujian
                                    </button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
                {{-- End Kanan --}}

            </div>


            <p class="text-center text-xs font-semibold text-slate-400 mt-12 flex items-center justify-center gap-2 opacity-80">
                <svg class="w-4 h-4 text-brand-red/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Setelah ujian selesai, kamu akan langsung diarahkan ke halaman hasil rekapitulasi.</span>
            </p>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://unpkg.com/@cortex-js/compute-engine"></script>
    <script src="https://www.desmos.com/api/v1.8/calculator.js?apiKey=dcb31709b452b1cf9dc26972add0fda6"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(!empty($grafikTerurai))
            const elt = document.getElementById('desmos-calculator');
            const calculator = Desmos.GraphingCalculator(elt, {
                keypad: false,
                expressions: false,
                settingsMenu: false,
                zoomButtons: true,
                lockViewport: false
            });
            calculator.setExpression({ id: 'graph1', latex: '{{ addslashes($grafikTerurai) }}' });
            @endif

            const startTime = new Date("{{ $sesiUjian->started_at->toIso8601String() }}").getTime();
            const durasiMenit = {{ $sesiUjian->bab->durasi ?? 10 }};
            const endTime = startTime + (durasiMenit * 60 * 1000);
            
            const timerElement = document.getElementById('exam-timer');
            const timerWrapper = document.getElementById('timer-wrapper');
            if (!timerElement) return;

            function submitSelesai() {
                const formSelesai = document.querySelector('form[action="{{ route('siswa.ujian.selesaikan', $sesiUjian->id) }}"]');
                if (formSelesai) {
                    formSelesai.onsubmit = null; // hapus prompt konfirmasi
                    formSelesai.submit();
                }
            }

            function updateTimer() {
                const now = Date.now();
                const distance = endTime - now;
                
                if (distance <= 0) {
                    timerElement.innerText = "00:00:00";
                    clearInterval(timerInterval);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Waktu Habis!',
                            text: 'Jawaban akan dikumpulkan secara otomatis.',
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => submitSelesai());
                    } else {
                        alert("Waktu Habis! Kuis akan dikumpulkan otomatis.");
                        submitSelesai();
                    }
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                timerElement.innerText =
                    (hours < 10 ? "0" : "") + hours + ":" +
                    (minutes < 10 ? "0" : "") + minutes + ":" +
                    (seconds < 10 ? "0" : "") + seconds;
                    
                if (distance <= 60000 && timerWrapper) { // < 1 Menit peringatan warna merah
                    if (!timerWrapper.classList.contains('animate-pulse')) {
                        timerWrapper.classList.remove('bg-slate-900', 'ring-slate-900/10');
                        timerWrapper.classList.add('bg-brand-red', 'ring-brand-red/30', 'animate-pulse');
                        timerElement.classList.add('text-white');
                        const svgIcon = timerWrapper.querySelector('svg');
                        if(svgIcon) svgIcon.classList.replace('text-brand-red', 'text-white');
                    }
                }
            }
            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer();

            // MathLive & Compute Engine Integration
            const mf = document.getElementById('math_field_input');
            const hiddenInput = document.getElementById('student_answer');
            const previewText = document.getElementById('preview_numeric');
            const previewVal = document.getElementById('preview_value');
            
            if (mf && typeof ComputeEngine !== 'undefined') {
                const ce = new ComputeEngine.ComputeEngine();
                
                // Konfigurasi layout keyboard virtual
                mathVirtualKeyboard.layouts = ['numeric', 'symbols'];
                
                mf.addEventListener('input', (ev) => {
                    // Selalu kirim teks mentah LaTeX ke backend agar dievaluasi penuh
                    hiddenInput.value = mf.value;

                    try {
                        const expr = ce.parse(mf.value);
                        const numericResult = expr.N().valueOf();
                        
                        // Validasi apakah hasilnya berupa number asli untuk ditampilkan di preview (opsional)
                        if (typeof numericResult === 'number' && !isNaN(numericResult)) {
                            // Format 6 angka di belakang koma untuk akurasi
                            const roundedStr = String(Math.round(numericResult * 1000000) / 1000000);
                            previewVal.innerText = roundedStr;
                            previewText.classList.remove('hidden');
                        } else {
                            previewText.classList.add('hidden');
                        }
                    } catch (e) {
                        previewText.classList.add('hidden');
                    }
                });
                
                // Trigger evaluasi awal jika field sudah ada nilainya (misal saat navigasi kembali ke soal yang sudah dijawab)
                setTimeout(() => {
                    if (mf.value) {
                        mf.dispatchEvent(new Event('input'));
                    }
                }, 500);
            }
        });
    </script>
    @endpush
</x-app-layout>
