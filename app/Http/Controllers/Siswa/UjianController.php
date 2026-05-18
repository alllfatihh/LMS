<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Jawaban;
use App\Models\SesiUjian;
use App\Services\EvaluasiSoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    public function __construct(
        private EvaluasiSoalService $evaluasi
    ) {}

    public function index()
    {
        $daftarKelas = auth()->user()->kelasYangDiikuti()->with('guru')->withCount('daftarSoal')->get();

        return view('siswa.ujian.index', compact('daftarKelas'));
    }

    public function mulai(Bab $bab)
    {
        $pengguna = Auth::user();

        $sesiSelesai = SesiUjian::where('user_id', $pengguna->id)
            ->where('bab_id', $bab->id)
            ->whereNotNull('finished_at')
            ->latest()
            ->first();

        if ($sesiSelesai) {
            return redirect()->route('siswa.ujian.riwayat')
                ->with('info', 'Kamu sudah pernah menyelesaikan latihan di bab "'.$bab->title.'".');
        }

        $sesiAktif = SesiUjian::where('user_id', $pengguna->id)
            ->where('bab_id', $bab->id)
            ->whereNull('finished_at')
            ->latest()
            ->first();

        if ($sesiAktif) {
            return redirect()->route('siswa.ujian.tampil-soal', $sesiAktif->id)
                ->with('info', 'Kamu masih punya sesi yang belum selesai. Yuk lanjutkan!');
        }

        $sesi = SesiUjian::create([
            'user_id' => $pengguna->id,
            'bab_id' => $bab->id,
            'started_at' => now(),
        ]);

        foreach ($bab->daftarSoal as $soal) {
            $variabel = $this->evaluasi->hasilkanVariabel($soal->variable_rules);

            Jawaban::create([
                'sesi_ujian_id' => $sesi->id,
                'soal_id' => $soal->id,
                'generated_variables' => $variabel,
                'student_answer' => null,
                'is_correct' => null,
            ]);
        }

        return redirect()->route('siswa.ujian.tampil-soal', $sesi->id);
    }

    public function tampilSoal(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->user_id !== Auth::id()) {
            abort(403);
        }

        if ($sesiUjian->finished_at) {
            return redirect()
                ->route('siswa.ujian.hasil', $sesiUjian->id)
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        $jawaban = $sesiUjian->daftarJawaban()->whereNull('student_answer')->first();

        if (! $jawaban) {
            if (! $sesiUjian->finished_at) {
                $sesiUjian->update(['finished_at' => now()]);
            }

            return redirect()
                ->route('siswa.ujian.hasil', $sesiUjian->id)
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        }

        $soal = $jawaban->soal;

        $teksSoalTerurai = $this->evaluasi->uraiTemplatSoal(
            $soal->question_template,
            $jawaban->generated_variables
        );

        return response()
            ->view('siswa.ujian.tampil-soal', compact('sesiUjian', 'jawaban', 'teksSoalTerurai'))
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function kirimJawaban(Request $permintaan, Jawaban $jawaban)
    {
        if ($jawaban->sesiUjian->user_id !== Auth::id()) {
            abort(403);
        }

        $sesi = $jawaban->sesiUjian;

        if ($sesi->finished_at) {
            return redirect()
                ->route('siswa.ujian.hasil', $sesi->id)
                ->with('info', 'Latihan ini sudah selesai. Halaman soal tidak bisa dibuka lagi.')
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
        }

        if ($jawaban->student_answer !== null) {
            return redirect()->route('siswa.ujian.tampil-soal', $jawaban->sesi_ujian_id);
        }

        $permintaan->validate([
            'student_answer' => ['required', 'numeric'],
        ], [
            'student_answer.required' => 'Isi jawaban angka dulu, ya.',
            'student_answer.numeric' => 'Jawaban harus berupa angka.',
        ]);

        $jawabanBenar = $this->evaluasi->hitungRumus(
            $jawaban->soal->answer_formula,
            $jawaban->generated_variables
        );

        $benar = $this->evaluasi->jawabanNumerikSama(
            $permintaan->student_answer,
            $jawabanBenar
        );

        $jawaban->update([
            'student_answer' => $permintaan->student_answer,
            'is_correct' => $benar,
        ]);

        return redirect()->route('siswa.ujian.tampil-soal', $jawaban->sesi_ujian_id);
    }

    public function hasil(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->user_id !== Auth::id()) {
            abort(403);
        }

        $sesiUjian->loadMissing(['daftarJawaban.soal', 'bab.kelas']);

        $jumlahSoal = $sesiUjian->daftarJawaban()->count();
        $jumlahBenar = $sesiUjian->daftarJawaban()->where('is_correct', true)->count();
        $nilai = $jumlahSoal > 0 ? ($jumlahBenar / $jumlahSoal) * 100 : 0;

        return response()
            ->view('siswa.ujian.hasil', compact('sesiUjian', 'jumlahSoal', 'jumlahBenar', 'nilai'))
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function riwayat()
    {
        $daftarSesi = SesiUjian::where('user_id', Auth::id())
            ->with('bab.kelas')
            ->withCount([
                'daftarJawaban as jumlah_jawaban',
                'daftarJawaban as jumlah_benar' => fn ($q) => $q->where('is_correct', true),
            ])
            ->latest('started_at')
            ->get()
            ->map(function ($sesi) {
                if ($sesi->finished_at && $sesi->jumlah_jawaban > 0) {
                    $sesi->nilai = ($sesi->jumlah_benar / $sesi->jumlah_jawaban) * 100;
                } else {
                    $sesi->nilai = null;
                }

                return $sesi;
            });

        return view('siswa.ujian.riwayat', ['daftarSesi' => $daftarSesi]);
    }
}
