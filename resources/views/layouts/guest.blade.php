<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'LMS Matematika') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
    <body class="font-sans text-gray-800 antialiased selection:bg-brand-red selection:text-white">
        <!-- Fixed Decorative Background -->
        <div class="fixed inset-0 z-0 bg-gradient-to-b from-rose-50 to-white overflow-hidden pointer-events-none">
            <!-- Decorative Blobs -->
            <div class="absolute top-0 -left-4 w-72 h-72 bg-rose-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40 animate-blob animation-delay-4000"></div>
            
            <!-- Abstract Dots Pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#ed1e28 2px, transparent 2px); background-size: 40px 40px;"></div>
        </div>

        <!-- Scrollable Content Layer -->
        <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-16 overflow-y-auto">
            <div class="relative w-full sm:max-w-md mt-6 px-10 py-10 bg-white/80 backdrop-blur-xl shadow-2xl overflow-hidden rounded-[2.5rem] border border-white/60">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-rose-100 to-transparent rounded-bl-full opacity-50"></div>
                <div class="flex justify-center mb-8 relative z-10">
                    <a href="/" class="group">
                        <x-application-logo class="w-20 h-20 fill-current text-brand-red drop-shadow-md transition-transform duration-300 group-hover:scale-105" />
                    </a>
                </div>
                <div class="relative z-10">
                    {{ $slot }}
                </div>
            </div>

            <p class="relative z-10 text-xs text-slate-400 font-medium mt-8 tracking-wide uppercase">LMS Matematika &copy; {{ date('Y') }} — Telkom University</p>
        </div>
    </body>
</html>
