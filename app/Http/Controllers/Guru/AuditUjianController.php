<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SesiUjian;
use App\Services\EvaluasiSoalService;
use Illuminate\Http\Request;

class AuditUjianController extends Controller
{
    public function __construct(
        private EvaluasiSoalService $evaluasi
    ) {}

    public function tampil(Request $permintaan, SesiUjian $sesiUjian)
    {
        // Pastikan hanya guru kelas yang mengajar bab ini yang bisa melihat
        $sesiUjian->loadMissing(['bab.kelas', 'user', 'daftarJawaban.soal']);

        if ($sesiUjian->bab->kelas->guru_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak melihat audit sesi ujian kelas lain.');
        }

        $daftarJawaban = $sesiUjian->daftarJawaban;
        $jumlahSoal = $daftarJawaban->count();
        $jumlahBenar = $daftarJawaban->where('is_correct', true)->count();
        $nilai = $jumlahSoal > 0 ? ($jumlahBenar / $jumlahSoal) * 100 : 0;

        // Kami perlu mengevaluasi / menyuntikkan variabel kembali ke template agar guru bisa melihat
        // soal persis seperti yang dilihat siswa.
        $auditList = $daftarJawaban->map(function ($jawaban) {
            $soal = $jawaban->soal;
            
            // Reconstruct Text Soal
            $teksSoalTerurai = $this->evaluasi->uraiTemplatSoal(
                $soal->question_template,
                $jawaban->generated_variables ?? []
            );

            // Reconstruct Kunci Jawaban
            $kunciJawaban = null;
            if (!empty($soal->answer_formula)) {
                try {
                    $kunciJawaban = $this->evaluasi->hitungRumus(
                        $soal->answer_formula,
                        $jawaban->generated_variables ?? []
                    );
                } catch (\Exception $e) {
                    $kunciJawaban = 'Error: ' . $e->getMessage();
                }
            }

            return (object) [
                'soal_asli' => $soal->question_template,
                'soal_terurai' => $teksSoalTerurai,
                'variabel_seed' => $jawaban->generated_variables,
                'rumus_jawaban' => $soal->answer_formula,
                'kunci_jawaban' => $kunciJawaban,
                'jawaban_siswa' => $jawaban->student_answer,
                'is_correct' => $jawaban->is_correct,
            ];
        });

        return view('guru.audit.tampil', compact('sesiUjian', 'auditList', 'jumlahSoal', 'jumlahBenar', 'nilai'));
    }
}
