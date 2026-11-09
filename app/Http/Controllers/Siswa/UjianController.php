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

        if ($bab->daftarSoal()->count() === 0) {
            return redirect()->back()
                ->with('error', 'Bab ini belum memiliki soal latihan. Silakan hubungi gurumu.');
        }

        $semuaSesiSelesai = SesiUjian::where('user_id', $pengguna->id)
            ->where('bab_id', $bab->id)
            ->whereNotNull('finished_at')
            ->get();

        $sudahLulus = false;
        foreach ($semuaSesiSelesai as $sesiLama) {
            $semuaBobot = 0;
            $bobotBenar = 0;
            foreach ($sesiLama->daftarJawaban as $jwb) {
                $bobot = $jwb->soal->bobot ?? 10;
                $semuaBobot += $bobot;
                if ($jwb->is_correct) {
                    $bobotBenar += $bobot;
                }
            }
            $nilaiSesi = $semuaBobot > 0 ? ($bobotBenar / $semuaBobot) * 100 : 0;
            if ($nilaiSesi >= 80) {
                $sudahLulus = true;
                break;
            }
        }

        if ($sudahLulus) {
            return redirect()->route('siswa.ujian.riwayat')
                ->with('info', 'Kamu sudah pernah menyelesaikan latihan di bab "'.$bab->title.'" dengan nilai yang memuaskan.');
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

    public function tampilSoal(Request $permintaan, SesiUjian $sesiUjian)
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

        $semuaJawaban = $sesiUjian->daftarJawaban()->orderBy('id')->get();

        if ($semuaJawaban->isEmpty()) {
            $sesiUjian->delete();
            return redirect()->route('siswa.ujian.index')
                ->with('error', 'Sesi ujian tidak valid karena tidak memiliki soal.');
        }

        $jawaban = null;

        if ($permintaan->has('n') && is_numeric($permintaan->n)) {
            $n = (int) $permintaan->n;
            if ($n >= 1 && $n <= $semuaJawaban->count()) {
                $jawaban = $semuaJawaban->values()->get($n - 1);
            }
        }

        if (! $jawaban) {
            $jawaban = $semuaJawaban->whereNull('student_answer')->first() ?? $semuaJawaban->first();
        }

        $nomorSoalAktif = $semuaJawaban->search(fn($j) => $j->id === $jawaban->id) + 1;
        $totalSoal = $semuaJawaban->count();
        $sisaSoal = $semuaJawaban->whereNull('student_answer')->count();

        $soal = $jawaban->soal;

        $teksSoalTerurai = $this->evaluasi->uraiTemplatSoal(
            $soal->question_template,
            $jawaban->generated_variables
        );

        $grafikTerurai = null;
        if (isset($soal->variable_rules['graph_formula'])) {
            $grafikTerurai = $this->evaluasi->uraiTemplatSoal(
                $soal->variable_rules['graph_formula'],
                $jawaban->generated_variables
            );
        }

        return response()
            ->view('siswa.ujian.tampil-soal', compact('sesiUjian', 'jawaban', 'teksSoalTerurai', 'grafikTerurai', 'semuaJawaban', 'nomorSoalAktif', 'totalSoal', 'sisaSoal'))
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

        $format = $jawaban->soal->jawaban_format ?? 'angka';

        $rules = ['required', 'string'];
        $messages = [
            'student_answer.required' => 'Isi jawaban dulu, ya.',
        ];

        if ($format === 'angka') {
            $rules[] = 'numeric';
            $messages['student_answer.numeric'] = 'Jawaban harus berupa angka.';
        }

        $permintaan->validate([
            'student_answer' => $rules,
        ], $messages);

        $jawabanBenar = $this->evaluasi->hitungAtauUrai(
            $jawaban->soal->answer_formula,
            $jawaban->generated_variables,
            $format
        );

        $benar = $this->evaluasi->cekJawaban(
            $format,
            $permintaan->student_answer,
            $jawabanBenar
        );

        $jawaban->update([
            'student_answer' => $permintaan->student_answer,
            'is_correct' => $benar,
        ]);

        $semuaJawaban = $sesi->daftarJawaban()->orderBy('id')->get();
        $nextUnanswered = $semuaJawaban->whereNull('student_answer')->first();

        if ($nextUnanswered) {
            $nextNomor = $semuaJawaban->search(fn($j) => $j->id === $nextUnanswered->id) + 1;
            return redirect()->route('siswa.ujian.tampil-soal', ['sesi_ujian' => $sesi->id, 'n' => $nextNomor]);
        }

        $currentNomor = $semuaJawaban->search(fn($j) => $j->id === $jawaban->id) + 1;
        $nextNomor = $currentNomor < $semuaJawaban->count() ? $currentNomor + 1 : 1;

        return redirect()->route('siswa.ujian.tampil-soal', ['sesi_ujian' => $sesi->id, 'n' => $nextNomor])
            ->with('info', 'Semua soal telah dijawab. Silakan periksa kembali jawabanmu lalu klik Selesaikan Ujian.');
    }

    public function selesaikan(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $sesiUjian->finished_at) {
            $sesiUjian->update(['finished_at' => now()]);
        }

        return redirect()->route('siswa.ujian.hasil', $sesiUjian->id)
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate');
    }

    public function hasil(SesiUjian $sesiUjian)
    {
        if ($sesiUjian->user_id !== Auth::id()) {
            abort(403);
        }

        $sesiUjian->loadMissing(['daftarJawaban.soal', 'bab.kelas']);

        $jumlahSoal = $sesiUjian->daftarJawaban()->count();
        $jumlahBenar = $sesiUjian->daftarJawaban()->where('is_correct', true)->count();
        $semuaBobot = 0;
        $bobotBenar = 0;
        foreach ($sesiUjian->daftarJawaban as $jwb) {
            $bobot = $jwb->soal->bobot ?? 10;
            $semuaBobot += $bobot;
            if ($jwb->is_correct) {
                $bobotBenar += $bobot;
            }
        }
        $nilai = $semuaBobot > 0 ? ($bobotBenar / $semuaBobot) * 100 : 0;

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
                    $semuaBobot = 0;
                    $bobotBenar = 0;
                    foreach ($sesi->daftarJawaban as $jwb) {
                        $bobot = $jwb->soal->bobot ?? 10;
                        $semuaBobot += $bobot;
                        if ($jwb->is_correct) {
                            $bobotBenar += $bobot;
                        }
                    }
                    $sesi->nilai = $semuaBobot > 0 ? ($bobotBenar / $semuaBobot) * 100 : 0;
                } else {
                    $sesi->nilai = null;
                }

                return $sesi;
            });

        return view('siswa.ujian.riwayat', ['daftarSesi' => $daftarSesi]);
    }
}
