<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/beranda', function () {
    $pengguna = auth()->user();
    $data = [];

    if ($pengguna->role === 'siswa') {
        if ($pengguna->kelasYangDiikuti()->count() === 0) {
            return redirect()->route('siswa.gabung-kelas.index');
        }

        $sesiSelesai = \App\Models\SesiUjian::where('user_id', $pengguna->id)
            ->whereNotNull('finished_at')
            ->with(['bab.kelas', 'daftarJawaban'])
            ->latest('finished_at')
            ->get();

        $data['total_kelas'] = $pengguna->kelasYangDiikuti()->count();
        $data['total_ujian'] = $sesiSelesai->count();

        $totalSemuaBobot = 0;
        $totalBobotBenar = 0;
        $skorTertinggi = 0;

        foreach ($sesiSelesai as $sesi) {
            $total = $sesi->daftarJawaban->count();
            if ($total === 0) continue;

            $semuaBobot = 0;
            $bobotBenar = 0;
            foreach ($sesi->daftarJawaban as $jwb) {
                $bobot = $jwb->soal->bobot ?? 10;
                $semuaBobot += $bobot;
                if ($jwb->is_correct) {
                    $bobotBenar += $bobot;
                }
            }
            
            $totalSemuaBobot += $semuaBobot;
            $totalBobotBenar += $bobotBenar;
            
            $skorSesi = $semuaBobot > 0 ? ($bobotBenar / $semuaBobot) * 100 : 0;
            if ($skorSesi > $skorTertinggi) {
                $skorTertinggi = $skorSesi;
            }
        }

        $data['rata_skor'] = $totalSemuaBobot > 0 ? round(($totalBobotBenar / $totalSemuaBobot) * 100) : 0;
        $data['skor_tertinggi'] = round($skorTertinggi);

        $data['daftar_kelas'] = $pengguna->kelasYangDiikuti()->with('guru')->get();
        $data['aktivitas_terbaru'] = $sesiSelesai->take(5)->map(function ($sesi) {
            $semuaBobot = 0;
            $bobotBenar = 0;
            foreach ($sesi->daftarJawaban as $jwb) {
                $bobot = $jwb->soal->bobot ?? 10;
                $semuaBobot += $bobot;
                if ($jwb->is_correct) {
                    $bobotBenar += $bobot;
                }
            }
            $skor = $semuaBobot > 0 ? round(($bobotBenar / $semuaBobot) * 100) : 0;

            return (object) [
                'sesi_id' => $sesi->id,
                'judul_bab' => $sesi->bab->title,
                'nama_kelas' => $sesi->bab->kelas->name,
                'waktu' => $sesi->finished_at->diffForHumans(),
                'skor' => $skor
            ];
        });
    }

    if ($pengguna->role === 'guru') {
        if ($pengguna->kelasYangDiajar()->count() === 0) {
            return redirect()->route('guru.kelas.buat');
        }

        $kelas = $pengguna->kelasYangDiajar;
        $data['total_kelas'] = $kelas->count();
        $data['total_siswa'] = $kelas->sum(fn($k) => $k->siswa()->count());
        $data['daftar_kelas'] = $kelas;

        $babIds = \App\Models\Bab::whereIn('kelas_id', $kelas->pluck('id'))->pluck('id');
        $sesiSelesai = \App\Models\SesiUjian::whereIn('bab_id', $babIds)
            ->whereNotNull('finished_at')
            ->with(['user', 'bab.kelas', 'daftarJawaban'])
            ->latest('finished_at')
            ->get();

        $data['total_ujian'] = $sesiSelesai->count();

        $totalSemuaBobot = 0;
        $totalBobotBenar = 0;

        foreach ($sesiSelesai as $sesi) {
            $total = $sesi->daftarJawaban->count();
            if ($total === 0) continue;

            $semuaBobot = 0;
            $bobotBenar = 0;
            foreach ($sesi->daftarJawaban as $jwb) {
                $bobot = $jwb->soal->bobot ?? 10;
                $semuaBobot += $bobot;
                if ($jwb->is_correct) {
                    $bobotBenar += $bobot;
                }
            }
            
            $totalSemuaBobot += $semuaBobot;
            $totalBobotBenar += $bobotBenar;
        }

        $data['rata_skor'] = $totalSemuaBobot > 0 ? round(($totalBobotBenar / $totalSemuaBobot) * 100) : 0;
        $data['aktivitas_terbaru'] = $sesiSelesai->take(5)->map(function ($sesi) {
            $semuaBobot = 0;
            $bobotBenar = 0;
            foreach ($sesi->daftarJawaban as $jwb) {
                $bobot = $jwb->soal->bobot ?? 10;
                $semuaBobot += $bobot;
                if ($jwb->is_correct) {
                    $bobotBenar += $bobot;
                }
            }
            $skor = $semuaBobot > 0 ? round(($bobotBenar / $semuaBobot) * 100) : 0;

            return (object) [
                'sesi_id' => $sesi->id,
                'nama_siswa' => $sesi->user->name,
                'judul_bab' => $sesi->bab->title,
                'nama_kelas' => $sesi->bab->kelas->name,
                'waktu' => $sesi->finished_at->diffForHumans(),
                'skor' => $skor
            ];
        });
    }

    return view('dashboard', compact('data'));
})->middleware(['auth', 'verified'])->name('beranda');

/** Nama route `dashboard` dipertahankan agar kompatibel dengan layout Breeze */
Route::redirect('/dashboard', '/beranda')->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'hapus'])->name('profile.destroy');

    // Halaman detail materi yang bisa diakses oleh siswa dan dipratinjau oleh guru kelas
    Route::get('/siswa/materi/{slug}', [\App\Http\Controllers\Siswa\MateriController::class, 'tampil'])
        ->middleware('siswaBeradaDiKelas')
        ->name('siswa.materi.tampil');



    // [SIDANG] Route Group ini dibungkus dengan middleware 'role:siswa'.
    // Artinya, jika ada Guru atau orang luar yang iseng mengetik URL /siswa/... di browser,
    // Laravel akan langsung memblokir dan melemparkannya (karena rolenya tidak cocok).
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/gabung-kelas', [\App\Http\Controllers\Siswa\GabungKelasController::class, 'index'])->name('gabung-kelas.index');
        Route::post('/gabung-kelas', [\App\Http\Controllers\Siswa\GabungKelasController::class, 'simpan'])->name('gabung-kelas.simpan');
        Route::delete('/kelas/{kelas}/keluar', [\App\Http\Controllers\Siswa\GabungKelasController::class, 'keluar'])->name('gabung-kelas.keluar');

        Route::middleware('siswaBeradaDiKelas')->group(function () {
            Route::post('/materi/{slug}/baca', [\App\Http\Controllers\Siswa\MateriController::class, 'tandaiSudahDibaca'])->name('materi.tandai-dibaca');
        });

        Route::middleware('siswaBeradaDiKelas')->prefix('ujian')->name('ujian.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Siswa\UjianController::class, 'index'])->name('index');
            Route::get('/riwayat', [\App\Http\Controllers\Siswa\UjianController::class, 'riwayat'])->name('riwayat');

            Route::get('/kelas/{kelas}', [\App\Http\Controllers\Siswa\BabController::class, 'index'])->name('kelas.tampil');
            Route::get('/bab/{bab}', [\App\Http\Controllers\Siswa\BabController::class, 'tampil'])->name('bab.tampil');
            Route::post('/bab/{bab}/baca', [\App\Http\Controllers\Siswa\BabController::class, 'tandaiSudahDibaca'])->name('bab.tandai-dibaca');

            Route::post('/bab/{bab}/mulai', [\App\Http\Controllers\Siswa\UjianController::class, 'mulai'])->name('bab.mulai');
            Route::get('/sesi/{sesi_ujian}', [\App\Http\Controllers\Siswa\UjianController::class, 'tampilSoal'])->name('tampil-soal');
            Route::post('/jawaban/{jawaban}/kirim', [\App\Http\Controllers\Siswa\UjianController::class, 'kirimJawaban'])->name('jawaban.kirim');
            Route::post('/sesi/{sesi_ujian}/selesaikan', [\App\Http\Controllers\Siswa\UjianController::class, 'selesaikan'])->name('selesaikan');
            Route::get('/sesi/{sesi_ujian}/hasil', [\App\Http\Controllers\Siswa\UjianController::class, 'hasil'])->name('hasil');
        });
    });

    // [SIDANG] Sebaliknya, URL yang berawalan /guru/... diamankan secara massal oleh middleware 'role:guru'.
    // Siswa nakal yang mencoba meretas masuk ke URL guru akan langsung diblokir.
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        // Fitur AI Guru
        Route::get('/ai/sisa-kuota', [\App\Http\Controllers\Guru\AiGuruController::class, 'sisaKuota'])->name('ai.sisa-kuota');
        Route::post('/ai/generate-materi', [\App\Http\Controllers\Guru\AiGuruController::class, 'generateMateri'])->name('ai.generate-materi');
        Route::post('/ai/generate-soal/{bab}', [\App\Http\Controllers\Guru\AiGuruController::class, 'generateSoal'])->name('ai.generate-soal');
        Route::post('/ai/bantu-template', [\App\Http\Controllers\Guru\AiGuruController::class, 'bantuTemplate'])->name('ai.bantu-template');

        Route::get('kelas', [\App\Http\Controllers\Guru\KelasController::class, 'index'])->name('kelas.index');
        Route::get('kelas/buat', [\App\Http\Controllers\Guru\KelasController::class, 'buat'])->name('kelas.buat');
        Route::post('kelas', [\App\Http\Controllers\Guru\KelasController::class, 'simpan'])->name('kelas.simpan');
        Route::get('kelas/{kelas}', [\App\Http\Controllers\Guru\KelasController::class, 'tampil'])->name('kelas.tampil');
        Route::get('kelas/{kelas}/edit', [\App\Http\Controllers\Guru\KelasController::class, 'edit'])->name('kelas.edit');
        Route::put('kelas/{kelas}', [\App\Http\Controllers\Guru\KelasController::class, 'perbarui'])->name('kelas.perbarui');
        Route::delete('kelas/{kelas}', [\App\Http\Controllers\Guru\KelasController::class, 'hapus'])->name('kelas.hapus');
        Route::delete('kelas/{kelas}/siswa/{siswa}', [\App\Http\Controllers\Guru\KelasController::class, 'keluarkanSiswa'])->name('kelas.keluarkan-siswa');

        Route::get('materi/{materi}/edit', [\App\Http\Controllers\Guru\MateriController::class, 'edit'])->name('materi.edit');
        Route::put('materi/{materi}', [\App\Http\Controllers\Guru\MateriController::class, 'perbarui'])->name('materi.perbarui');
        Route::delete('materi/{materi}', [\App\Http\Controllers\Guru\MateriController::class, 'hapus'])->name('materi.hapus');

        Route::get('kelas/{kelas}/bab/baru', [\App\Http\Controllers\Guru\BabController::class, 'buat'])->name('bab.buat');
        Route::post('kelas/{kelas}/bab', [\App\Http\Controllers\Guru\BabController::class, 'simpan'])->name('bab.simpan');
        Route::get('bab/{bab}/materi/buat', [\App\Http\Controllers\Guru\MateriController::class, 'buat'])->name('materi.buat');
        Route::post('bab/{bab}/materi', [\App\Http\Controllers\Guru\MateriController::class, 'simpan'])->name('materi.simpan');
        Route::get('bab/{bab}', [\App\Http\Controllers\Guru\BabController::class, 'tampil'])->name('bab.tampil');
        Route::get('bab/{bab}/edit', [\App\Http\Controllers\Guru\BabController::class, 'edit'])->name('bab.edit');
        Route::put('bab/{bab}', [\App\Http\Controllers\Guru\BabController::class, 'perbarui'])->name('bab.perbarui');
        Route::delete('bab/{bab}', [\App\Http\Controllers\Guru\BabController::class, 'hapus'])->name('bab.hapus');

        Route::get('bab/{bab}/teks/edit', [\App\Http\Controllers\Guru\TeksBabController::class, 'edit'])->name('teks.edit');
        Route::put('bab/{bab}/teks', [\App\Http\Controllers\Guru\TeksBabController::class, 'perbarui'])->name('teks.perbarui');

        Route::post('soal/pratinjau', [\App\Http\Controllers\Guru\SoalController::class, 'pratinjau'])->name('soal.pratinjau');
        Route::get('bab/{bab}/soal/baru', [\App\Http\Controllers\Guru\SoalController::class, 'buat'])->name('soal.buat');
        Route::post('bab/{bab}/soal', [\App\Http\Controllers\Guru\SoalController::class, 'simpan'])->name('soal.simpan');
        Route::get('soal/{soal}/edit', [\App\Http\Controllers\Guru\SoalController::class, 'edit'])->name('soal.edit');
        Route::put('soal/{soal}', [\App\Http\Controllers\Guru\SoalController::class, 'perbarui'])->name('soal.perbarui');
        Route::delete('soal/{soal}', [\App\Http\Controllers\Guru\SoalController::class, 'hapus'])->name('soal.hapus');

        // Audit Ujian
        Route::get('sesi-ujian/{sesi_ujian}/audit', [\App\Http\Controllers\Guru\AuditUjianController::class, 'tampil'])->name('audit.tampil');
    });
});

require __DIR__ . '/auth.php';
