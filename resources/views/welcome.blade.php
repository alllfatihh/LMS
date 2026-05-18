<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LMS Matematika — Revolusi Belajar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        'brand-red': '#ed1e28',
                        'brand-red-dark': '#c91a23',
                    },
                    boxShadow: {
                        'anti-gravity': '0 20px 40px -10px rgba(237, 30, 40, 0.1)',
                        'glow': '0 0 20px rgba(237, 30, 40, 0.4)',
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'pulse-glow': 'pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { opacity: 1, transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(237,30,40, 0.7)' },
                            '50%': { opacity: 1, transform: 'scale(1.02)', boxShadow: '0 0 20px 10px rgba(237,30,40, 0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased bg-white overflow-x-hidden selection:bg-brand-red selection:text-white">

    <!-- Navbar Glassmorphism -->
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('assets/logo-LMS.jpg') }}" alt="Logo LMS" class="h-10 w-auto rounded-xl shadow-sm transition-transform group-hover:scale-105" onerror="this.style.display='none'">
                    <span class="font-extrabold text-xl tracking-tight text-slate-900" id="logo-text">MATH<span class="text-brand-red">ACADEMY</span></span>
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-sm text-slate-600 hover:text-brand-red transition-colors hidden sm:block">Dashboard</a>
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-brand-red text-white font-bold text-sm shadow-glow hover:bg-brand-red-dark transition-all hover:-translate-y-0.5">Buka Kelas</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-sm text-slate-600 hover:text-brand-red transition-colors hidden sm:block">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-brand-red text-white font-bold text-sm shadow-glow hover:bg-brand-red-dark transition-all hover:-translate-y-0.5">Daftar Gratis</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Fixed Decorative Background -->
    <div class="fixed inset-0 z-0 bg-gradient-to-b from-rose-50 to-white overflow-hidden pointer-events-none">
        <!-- Decorative Blobs -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-rose-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        
        <!-- Abstract Dots Pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#ed1e28 2px, transparent 2px); background-size: 40px 40px;"></div>
    </div>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center mt-10 sm:mt-0">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/60 border border-red-200 text-brand-red font-bold text-xs uppercase tracking-widest mb-8 backdrop-blur-md shadow-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-red opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-red"></span>
                </span>
                Platform EdTech Masa Depan
            </div>
            
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Revolusi Pembelajaran <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-red to-rose-400">Matematika Interaktif</span>
            </h1>
            
            <p class="mt-4 text-lg md:text-2xl text-slate-600 max-w-3xl mx-auto mb-10 leading-relaxed font-medium">
                Sistem pembelajaran sekuensial yang memastikan pemahaman mendalam. Kuasai materi melalui tahap 
                <span class="font-bold text-brand-red bg-rose-100 px-2 py-1 rounded-md">Bab &rarr; Materi &rarr; Kuis</span> secara terstruktur.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-10 py-4 rounded-full bg-brand-red text-white font-extrabold text-lg animate-pulse-glow hover:bg-brand-red-dark transition-all shadow-glow">
                        Lanjutkan Belajar Sekarang
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 rounded-full bg-brand-red text-white font-extrabold text-lg animate-pulse-glow hover:bg-brand-red-dark transition-all shadow-glow">
                        Mulai Belajar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 rounded-full bg-white text-slate-700 font-bold text-lg border-2 border-slate-200 hover:border-brand-red hover:text-brand-red transition-all shadow-sm">
                        Masuk Akun
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Anti-Gravity Features Section -->
    <div class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-brand-red font-bold tracking-widest uppercase text-sm mb-3">Teknologi Pendidikan</h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900">Dirancang untuk Hasil Terbaik</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group bg-white p-10 rounded-[2.5rem] shadow-anti-gravity hover:-translate-y-4 transition-all duration-500 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-rose-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
                    <div class="w-16 h-16 rounded-2xl bg-rose-100 text-brand-red flex items-center justify-center mb-8 transform group-hover:rotate-6 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-2xl font-extrabold text-slate-900 mb-4 relative z-10">Materi Multi-Modul</h4>
                    <p class="text-slate-600 leading-relaxed relative z-10 font-medium">Pengalaman belajar kaya dengan integrasi mulus antara Video YouTube interaktif, dokumen PDF, dan teks ringkasan dalam satu portal.</p>
                </div>

                <!-- Feature 2 (Center - Highlight) -->
                <div class="group bg-gradient-to-br from-brand-red to-[#c91a23] p-10 rounded-[2.5rem] shadow-glow hover:-translate-y-4 transition-all duration-500 relative overflow-hidden text-white mt-0 md:-mt-8 mb-0 md:mb-8">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-bl-full transition-transform group-hover:scale-110"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center mb-8 transform group-hover:-rotate-6 transition-transform border border-white/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-extrabold mb-4 relative z-10">Urutan Belajar Disiplin</h4>
                    <p class="text-red-100 leading-relaxed relative z-10 font-medium">Sistem pintar memblokir kuis sampai seluruh materi selesai dibaca, memastikan siswa benar-benar memahami teori sebelum berlatih soal.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group bg-white p-10 rounded-[2.5rem] shadow-anti-gravity hover:-translate-y-4 transition-all duration-500 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-rose-100 to-transparent rounded-bl-full opacity-50 transition-transform group-hover:scale-110"></div>
                    <div class="w-16 h-16 rounded-2xl bg-rose-100 text-brand-red flex items-center justify-center mb-8 transform group-hover:rotate-6 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <h4 class="text-2xl font-extrabold text-slate-900 mb-4 relative z-10">Analitik Real-Time</h4>
                    <p class="text-slate-600 leading-relaxed relative z-10 font-medium">Guru dilengkapi dasbor canggih untuk memantau progres baca dan skor ujian siswa secara instan tanpa perlu memuat ulang halaman.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-50 border-t border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/logo-LMS.jpg') }}" alt="Logo LMS" class="h-8 w-auto rounded-lg shadow-sm" onerror="this.style.display='none'">
            </div>
            <p class="text-slate-500 font-medium text-sm">
                &copy; {{ date('Y') }} LMS Matematika — Telkom University. All rights reserved.
            </p>
        </div>
    </footer>

    <script>
        // Glassmorphism Scroll Effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-sm');
                nav.style.background = 'rgba(255, 255, 255, 0.9)';
            } else {
                nav.classList.remove('shadow-sm');
                nav.style.background = 'rgba(255, 255, 255, 0.6)';
            }
        });

        // Hide fallback text if image loads
        document.querySelector('img').onload = function() {
            const fallbackText = document.getElementById('logo-text');
            if(fallbackText) fallbackText.style.display = 'none';
        }
    </script>
</body>
</html>