<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanSiswaBeradaDiKelas
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === 'siswa') {
            if ($user->kelasYangDiikuti()->count() === 0) {
                if (! $request->routeIs('siswa.gabung-kelas.index')
                    && ! $request->routeIs('siswa.gabung-kelas.simpan')
                    && ! $request->routeIs('logout')
                    && ! $request->routeIs('login')) {

                    return redirect()->route('siswa.gabung-kelas.index')
                        ->with('error', 'Kamu perlu gabung ke kelas dulu, baru bisa buka materi dan ujian, ya!');
                }
            }
        }

        return $next($request);
    }
}
