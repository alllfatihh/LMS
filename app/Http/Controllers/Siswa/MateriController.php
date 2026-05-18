<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Materi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MateriController extends Controller
{
    public function tampil(string $slug)
    {
        if (! Schema::hasTable('materi')) {
            abort(503, 'Tabel materi belum ada. Minta admin jalankan: php artisan migrate');
        }

        $modul = Materi::query()
            ->where('slug', $slug)
            ->with(['kelas.modulMateri', 'bab'])
            ->firstOrFail();

        $kelas = $modul->kelas;

        if (Auth::user()->role === 'siswa') {
            if (! $kelas->siswa()->where('user_id', Auth::id())->exists()) {
                abort(403);
            }
        } elseif (Auth::user()->role === 'guru') {
            if ($kelas->guru_id !== Auth::id()) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $babKembali = $modul->bab;
        $daftarMateri = $kelas->modulMateri()->get();

        return view('siswa.materi.tampil', compact('modul', 'babKembali', 'daftarMateri'));
    }

    public function tandaiSudahDibaca(string $slug)
    {
        $modul = Materi::where('slug', $slug)->firstOrFail();
        
        Auth::user()->materiYangSudahDibaca()->syncWithoutDetaching([
            $modul->id => ['read_at' => now()]
        ]);

        return redirect()->back()->with('success', 'Modul "' . $modul->judul . '" sudah ditandai selesai!');
    }
}
