@php
    $t = strtolower(trim((string)$modul->tipe));
    $jumlahKata = 0;
    $waktuDetikDinamis = 10;
    if($t === 'teks') {
        $jumlahKata = str_word_count(strip_tags($modul->konten_teks));
        $waktuDetikDinamis = max(10, ceil(($jumlahKata / 250) * 60));
    }
@endphp
@push('styles')
    <style>
        /* Custom styles for tables within prose to make them look premium */
        .prose table { width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; margin-top: 1.5rem; margin-bottom: 1.5rem; }
        .prose th, .prose td { padding: 12px; border: 1px solid #e2e8f0; }
        .prose th { background-color: #fff1f2; font-weight: 800; color: #ed1e28; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; }
        .prose tr:nth-child(even) { background-color: #fcfcfc; }
    </style>
@endpush

@push('scripts')
    {{-- MathJax Configuration --}}
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true
            },
            options: {
                ignoreHtmlClass: 'no-mathjax',
                processHtmlClass: 'mathjax-full'
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSelesai = document.getElementById('btn-selesai');
            const textSelesai = document.getElementById('text-selesai');
            const formSelesai = document.getElementById('form-selesai');
            
            const enabledClasses = ['bg-brand-red', 'shadow-lg', 'shadow-red-200', 'hover:bg-brand-red-dark', 'transform', 'hover:scale-105', 'cursor-pointer'];
            const disabledClasses = ['bg-gray-400', 'shadow-md', 'cursor-not-allowed'];
            
            if (!btnSelesai) return;
            
            function enableButton(defaultText) {
                btnSelesai.disabled = false;
                disabledClasses.forEach(cls => btnSelesai.classList.remove(cls));
                enabledClasses.forEach(cls => btnSelesai.classList.add(cls));
                textSelesai.textContent = defaultText;
            }

            function updateButtonText(text) {
                textSelesai.textContent = text;
            }
            
            function updateHeaderTimer(seconds) {
                const headerTimer = document.getElementById('header-timer');
                if (headerTimer) {
                    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const s = (seconds % 60).toString().padStart(2, '0');
                    headerTimer.textContent = `${m}:${s}`;
                }
            }

            const materiTipe = '{{ strtolower(trim((string)$modul->tipe)) }}';
            
            if (materiTipe === 'teks') {
                let timeElapsed = false;
                let bottomReached = false;
                let timeLeft = {{ $waktuDetikDinamis }};
                
                const timer = setInterval(() => {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        timeElapsed = true;
                        clearInterval(timer);
                        updateHeaderTimer(0);
                        checkTextCompletion();
                    } else {
                        updateHeaderTimer(timeLeft);
                        if (!bottomReached) {
                            updateButtonText(`Tunggu ${timeLeft} detik & gulir ke bawah`);
                        } else {
                            updateButtonText(`Tunggu ${timeLeft} detik`);
                        }
                    }
                }, 1000);

                const marker = document.getElementById('article-bottom-marker');
                if (marker) {
                    const observer = new IntersectionObserver((entries) => {
                        if (entries[0].isIntersecting) {
                            bottomReached = true;
                            if (timeLeft > 0) {
                                updateButtonText(`Tunggu ${timeLeft} detik`);
                            }
                            checkTextCompletion();
                        }
                    });
                    observer.observe(marker);
                } else {
                    bottomReached = true;
                }

                function checkTextCompletion() {
                    if (timeElapsed && bottomReached) {
                        enableButton('Sudah Selesai Membaca');
                    } else if (timeElapsed && !bottomReached) {
                        updateButtonText('Tolong gulir (scroll) hingga ke bawah artikel');
                    }
                }
            } 
            else if (materiTipe === 'pdf') {
                let timeLeft = 60;
                const timer = setInterval(() => {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        clearInterval(timer);
                        updateHeaderTimer(0);
                        enableButton('Sudah Selesai Membaca');
                    } else {
                        updateHeaderTimer(timeLeft);
                        updateButtonText(`Tunggu ${timeLeft} detik`);
                    }
                }, 1000);
            }
        });
    </script>
    
    @if(strtolower(trim((string)$modul->tipe)) === 'video')
    <script>
        let youtubePlayer;
        let isVideoCompleted = false;
        
        function onYouTubeIframeAPIReady() {
            youtubePlayer = new YT.Player('youtube-player', {
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }
        
        let videoCheckInterval;
        function onPlayerStateChange(event) {
            if (isVideoCompleted) return;
            
            if (event.data == YT.PlayerState.PLAYING) {
                videoCheckInterval = setInterval(checkVideoProgress, 1000);
            } else {
                clearInterval(videoCheckInterval);
            }
        }
        
        function checkVideoProgress() {
            if (isVideoCompleted) return;
            
            const btnSelesai = document.getElementById('btn-selesai');
            if (!btnSelesai) return;

            const duration = youtubePlayer.getDuration();
            const currentTime = youtubePlayer.getCurrentTime();
            
            if (duration > 0) {
                const percent = (currentTime / duration) * 100;
                const textSelesai = document.getElementById('text-selesai');
                
                if (percent >= 90) {
                    isVideoCompleted = true;
                    clearInterval(videoCheckInterval);
                    
                    btnSelesai.disabled = false;
                    ['bg-gray-400', 'shadow-md', 'cursor-not-allowed'].forEach(cls => btnSelesai.classList.remove(cls));
                    ['bg-brand-red', 'shadow-lg', 'shadow-red-200', 'hover:bg-brand-red-dark', 'transform', 'hover:scale-105', 'cursor-pointer'].forEach(cls => btnSelesai.classList.add(cls));
                    textSelesai.textContent = 'Sudah Selesai Menonton';
                } else {
                    textSelesai.textContent = `Tonton video (Mencapai ${Math.floor(percent)}%)`;
                }
            }
        }
    </script>
    <script src="https://www.youtube.com/iframe_api"></script>
    @endif
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('aiTutorWidget', (endpointUrl) => ({
                isOpen: false,
                isLoading: false,
                inputText: '',
                messages: [],
                
                toggleChat() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        setTimeout(() => this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight, 100);
                    }
                },
                
                async sendMessage() {
                    if (this.inputText.trim() === '' || this.isLoading) return;
                    
                    const question = this.inputText.trim();
                    this.inputText = '';
                    
                    // Add user message to UI
                    this.messages.push({ role: 'user', text: question });
                    this.scrollToBottom();
                    
                    this.isLoading = true;
                    
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        
                        const response = await fetch(endpointUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ pesan: question })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Format basic markdown (bold, newlines)
                            let formattedHtml = data.pesan
                                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                .replace(/\n/g, '<br>');
                            
                            this.messages.push({ role: 'ai', text: formattedHtml });
                        } else {
                            const errorMsg = data.pesan || data.message || JSON.stringify(data);
                            this.messages.push({ role: 'ai', text: '<span class="text-red-500">Error: ' + errorMsg + '</span>' });
                        }
                    } catch (error) {
                        this.messages.push({ role: 'ai', text: '<span class="text-red-500">Maaf, terjadi kesalahan koneksi. Coba lagi nanti.</span>' });
                    } finally {
                        this.isLoading = false;
                        this.scrollToBottom();
                    }
                },
                
                scrollToBottom() {
                    setTimeout(() => {
                        this.$refs.chatBox.scrollTop = this.$refs.chatBox.scrollHeight;
                    }, 50);
                }
            }));
        });
    </script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 flex-1 items-start gap-3">
                @if($babKembali ?? null)
                    @php
                        $routeKembali = auth()->user()->role === 'guru' 
                            ? route('guru.bab.tampil', $babKembali->id)
                            : route('siswa.ujian.bab.tampil', $babKembali->id);
                    @endphp
                    <a href="{{ $routeKembali }}" class="mt-0.5 shrink-0 text-gray-400 transition-colors hover:text-brand-red">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-red">{{ $modul->kelas->name }}</p>
                    <h1 class="text-xl font-extrabold leading-tight text-gray-900">{{ $modul->judul }}</h1>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                @if(auth()->user()->role === 'guru')
                    <a href="{{ route('guru.materi.edit', $modul) }}" class="rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-bold text-brand-red">Edit (Guru)</a>
                @endif
                
                @if(in_array($t, ['teks', 'pdf']) && auth()->user()->role === 'siswa')
                    @php
                        $initTime = $t === 'teks' ? $waktuDetikDinamis : 60;
                    @endphp
                    <span class="flex items-center gap-1 rounded-full bg-red-50 border border-red-100 px-3 py-1 text-xs font-bold text-brand-red" title="Sisa Waktu Baca">
                        <svg class="h-3.5 w-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="header-timer">{{ sprintf('%02d:%02d', floor($initTime/60), $initTime%60) }}</span>
                    </span>
                @endif
                
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-500">{{ $modul->labelTipe() }}</span>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-[65vh] bg-gray-50 pb-16 pt-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-[16rem_1fr] lg:gap-10">
                {{-- Sidebar --}}
                <aside class="mb-8 self-start lg:sticky lg:top-24 lg:mb-0">
                    <div class="premium-card overflow-hidden">
                        <div class="border-b border-gray-50 bg-gray-50/50 px-5 py-4">
                            <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400">Daftar Modul</p>
                        </div>
                        @php
                            $sortedMateri = collect($daftarMateri ?? [])->sortBy(function($item) {
                                return sprintf('%04d-%04d', $item->bab ? $item->bab->order : 9999, $item->order);
                            });
                            $materiPerBab = $sortedMateri->groupBy(function($item) {
                                return $item->bab ? 'Bab ' . $item->bab->order . ': ' . $item->bab->title : 'Materi Tambahan';
                            });
                        @endphp
                        
                        <div class="max-h-[70vh] overflow-y-auto custom-scrollbar p-3 space-y-6">
                            @foreach($materiPerBab as $namaBab => $materiGrup)
                                <div>
                                    <h4 class="px-2 text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $namaBab }}</h4>
                                    <nav class="space-y-1">
                                        @foreach($materiGrup as $item)
                                            @php
                                                $tipeItem = strtolower(trim((string)$item->tipe));
                                                $isAktif = $item->slug === $modul->slug;
                                                
                                                $ikon = match($tipeItem) {
                                                    'video' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                                                    'pdf'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>',
                                                    default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                                                };
                                            @endphp
                                            <a href="{{ route('siswa.materi.tampil', $item->slug) }}" 
                                               class="group flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-bold transition-all duration-200 {{ $isAktif ? 'bg-brand-red text-white shadow-md shadow-red-100' : 'text-gray-600 hover:bg-gray-50 hover:text-brand-red' }}">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl transition-colors {{ $isAktif ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-brand-red/10' }}">
                                                    <svg class="h-5 w-5 {{ $isAktif ? 'text-white' : 'text-gray-400 group-hover:text-brand-red' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        {!! $ikon !!}
                                                    </svg>
                                                </div>
                                                <span class="truncate">{{ $item->judul }}</span>
                                            </a>
                                        @endforeach
                                    </nav>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>

                {{-- Konten Utama --}}
                <div class="min-w-0 space-y-6">
                    @php
                        $t = strtolower(trim((string)$modul->tipe));
                    @endphp

                    @if($t == 'teks')
                        <div class="premium-card p-6 sm:p-10">
                            <article class="prose prose-slate prose-lg max-w-none prose-headings:text-brand-red prose-a:text-brand-red prose-strong:text-slate-900 prose-headings:font-black">
                                {!! $modul->konten_teks !!}
                            </article>
                            <div id="article-bottom-marker" class="h-1"></div>
                            
                            @if(auth()->user()->role === 'siswa')
                                <div class="mt-10 pt-8 border-t border-gray-50 flex justify-center">
                                    @if(auth()->user()->sudahMembacaMateri($modul->id))
                                        <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-6 py-2.5 text-sm font-bold text-green-600 ring-1 ring-green-100">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Modul sudah diselesaikan
                                        </span>
                                    @else
                                        <form action="{{ route('siswa.materi.tandai-dibaca', $modul->slug) }}" method="POST" id="form-selesai">
                                            @csrf
                                            <button type="submit" id="btn-selesai" disabled class="inline-flex items-center gap-2 rounded-full bg-gray-400 px-8 py-3 text-sm font-extrabold text-white shadow-md cursor-not-allowed transition-all duration-300">
                                                <span id="text-selesai">Tunggu {{ $waktuDetikDinamis }} detik & gulir ke bawah</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>

                    @elseif($t == 'video')
                        @php
                            $vId = \App\Support\Youtube::extractVideoId($modul->link_video);
                            $embed = $vId ? 'https://www.youtube.com/embed/' . $vId : null;
                        @endphp
                        <div class="premium-card overflow-hidden">
                            <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500">Video Pembelajaran</h3>
                            </div>
                            <div class="p-4 sm:p-8">
                                @if($embed)
                                    <div class="relative aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-2xl">
                                        <iframe id="youtube-player" src="{{ $embed }}?rel=0&modestbranding=1&enablejsapi=1" 
                                                class="absolute inset-0 h-full w-full border-0" 
                                                allowfullscreen 
                                                loading="lazy"></iframe>
                                    </div>
                                    <div class="mt-6 text-center">
                                        <a href="https://www.youtube.com/watch?v={{ $vId }}" target="_blank" rel="noopener" 
                                           class="inline-flex items-center gap-2 text-sm font-bold text-brand-red hover:underline">
                                            <span>Tonton di YouTube</span>
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                    
                                    @if(auth()->user()->role === 'siswa')
                                        <div class="mt-10 pt-8 border-t border-gray-50 flex justify-center">
                                            @if(auth()->user()->sudahMembacaMateri($modul->id))
                                                <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-6 py-2.5 text-sm font-bold text-green-600 ring-1 ring-green-100">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Video sudah diselesaikan
                                                </span>
                                            @else
                                                <form action="{{ route('siswa.materi.tandai-dibaca', $modul->slug) }}" method="POST" id="form-selesai">
                                                    @csrf
                                                    <button type="submit" id="btn-selesai" disabled class="inline-flex items-center gap-2 rounded-full bg-gray-400 px-8 py-3 text-sm font-extrabold text-white shadow-md cursor-not-allowed transition-all duration-300">
                                                        <span id="text-selesai">Tonton video hingga 90%...</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="rounded-2xl bg-amber-50 p-10 text-center ring-1 ring-amber-100">
                                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        <p class="font-bold text-amber-900">Link video tidak ditemukan</p>
                                        <p class="mt-1 text-sm text-amber-700">Guru belum mengatur link video dengan benar untuk materi ini.</p>
                                        @if(auth()->user()->role === 'guru')
                                            <p class="mt-4 text-xs font-mono text-amber-600">Nilai saat ini: "{{ $modul->link_video }}"</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                    @elseif($t == 'pdf')
                        <div class="premium-card overflow-hidden">
                            <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-6 py-4">
                                <h3 class="text-sm font-bold uppercase tracking-widest text-gray-500">Dokumen PDF</h3>
                                <a href="{{ $modul->urlPublikPdf() }}" target="_blank" class="text-xs font-bold text-brand-red hover:underline">Buka Penuh</a>
                            </div>
                            <div class="p-4 sm:p-8">
                                @if(filled($modul->file_path))
                                    <div class="aspect-[3/4] w-full rounded-2xl bg-gray-100 sm:aspect-[4/5] lg:aspect-[3/4]" style="height: 80vh;">
                                        <iframe src="{{ $modul->urlPublikPdf() }}#view=FitH" class="h-full w-full border-0 rounded-2xl"></iframe>
                                    </div>
                                    
                                    @if(auth()->user()->role === 'siswa')
                                        <div class="mt-10 pt-8 border-t border-gray-50 flex justify-center">
                                            @if(auth()->user()->sudahMembacaMateri($modul->id))
                                                <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-6 py-2.5 text-sm font-bold text-green-600 ring-1 ring-green-100">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Dokumen sudah dipelajari
                                                </span>
                                            @else
                                                <form action="{{ route('siswa.materi.tandai-dibaca', $modul->slug) }}" method="POST" id="form-selesai">
                                                    @csrf
                                                    <button type="submit" id="btn-selesai" disabled class="inline-flex items-center gap-2 rounded-full bg-gray-400 px-8 py-3 text-sm font-extrabold text-white shadow-md cursor-not-allowed transition-all duration-300">
                                                        <span id="text-selesai">Tunggu 60 detik...</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <p class="text-center text-gray-500 italic py-10">File PDF belum diunggah.</p>
                                @endif
                            </div>
                        </div>

                    @else
                        <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500 shadow-sm">
                            <p class="font-bold">Materi tidak dapat ditampilkan</p>
                            <p class="text-sm">Tipe materi "{{ $modul->tipe }}" tidak dikenali.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
