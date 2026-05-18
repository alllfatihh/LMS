<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BabController extends Controller
{
    public function index(Kelas $kelas)
    {
        if (! Auth::user()->kelasYangDiikuti()->where('kelas.id', $kelas->id)->exists()) {
            abort(403);
        }

        $daftarBab = $kelas->bab()->orderBy('order')->get();

        return view('siswa.bab.index', compact('kelas', 'daftarBab'));
    }

    public function tampil(Bab $bab)
    {
        $kelas = $bab->kelas;
        if (! Auth::user()->kelasYangDiikuti()->where('kelas.id', $kelas->id)->exists()) {
            abort(403);
        }

        $sudahDibaca = Auth::user()->sudahMembacaBab($bab->id);

        $ujianSelesai = SesiUjian::where('user_id', Auth::id())
            ->where('bab_id', $bab->id)
            ->whereNotNull('finished_at')
            ->latest()
            ->first();

        $materiBab = collect();
        if (Schema::hasTable('materi') && Schema::hasColumn('materi', 'bab_id')) {
            $materiBab = $bab->materi()->orderBy('order')->orderBy('id')->get();
            if ($materiBab->isEmpty()) {
                $legacy = Materi::query()
                    ->where('kelas_id', $kelas->id)
                    ->where('order', $bab->order)
                    ->whereNull('bab_id')
                    ->first();
                if ($legacy) {
                    $legacy->update(['bab_id' => $bab->id]);
                    $materiBab = $bab->materi()->orderBy('order')->orderBy('id')->get();
                }
            }
        }

        $bab->loadMissing('teksBab');

        // Logika Kunci Soal: Cek apakah semua materi sudah dibaca
        $totalMateri = $materiBab->count();
        $materiSelesaiCount = 0;
        if (Auth::user()->role === 'siswa') {
            $materiSelesaiIds = Auth::user()->materiYangSudahDibaca()->whereIn('materi_id', $materiBab->pluck('id'))->pluck('materi_id')->toArray();
            $materiSelesaiCount = count($materiSelesaiIds);
        }
        $semuaMateriSelesai = $totalMateri > 0 && $materiSelesaiCount >= $totalMateri;

        return view('siswa.bab.tampil', compact('bab', 'sudahDibaca', 'ujianSelesai', 'materiBab', 'totalMateri', 'materiSelesaiCount', 'semuaMateriSelesai'));
    }

    public function tandaiSudahDibaca(Bab $bab)
    {
        $kelas = $bab->kelas;
        if (! Auth::user()->kelasYangDiikuti()->where('kelas.id', $kelas->id)->exists()) {
            abort(403);
        }

        Auth::user()->babYangSudahDibaca()->syncWithoutDetaching([$bab->id => ['read_at' => now()]]);

        return redirect()->route('siswa.ujian.bab.tampil', $bab->id)->with('success', 'Materi sudah kamu baca. Sekarang bisa mulai latihan soal, ya!');
    }
}
