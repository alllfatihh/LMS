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

        $semuaBobot = 0;
        $bobotBenar = 0;
        foreach ($daftarJawaban as $jwb) {
            $bobot = $jwb->soal->bobot ?? 10;
            $semuaBobot += $bobot;
            if ($jwb->is_correct) {
                $bobotBenar += $bobot;
            }
        }
        $nilai = $semuaBobot > 0 ? ($bobotBenar / $semuaBobot) * 100 : 0;

        // Kami perlu mengevaluasi / menyuntikkan variabel kembali ke template agar guru bisa melihat
        // soal persis seperti yang dilihat siswa.
        $auditList = $daftarJawaban->map(function ($jawaban) {
            $soal = $jawaban->soal;
            
            // [SIDANG] Di sinilah kehebatan arsitektur Audit. Karena kita menyimpan variabel acaknya di database,
            // kita bisa "merekonstruksi/membangun ulang" soal dan jawabannya menggunakan EvaluasiSoalService
            // sehingga guru melihat teks soal dan angka persis seperti yang dialami siswa ujian kemarin.
            // Reconstruct Text Soal
            $teksSoalTerurai = $this->evaluasi->uraiTemplatSoal(
                $soal->question_template,
                $jawaban->generated_variables ?? []
            );

            // Reconstruct Kunci Jawaban. hitungAtauUrai() mengeksekusi formula (mis. pemanggilan
            // fungsi seperti faktorisasi_kuadrat(p, q)) jika bisa, dan fallback ke substitusi teks
            // untuk template aljabar simbolik yang tak bisa dieksekusi — tidak pernah melempar
            // exception, jadi guru tidak pernah melihat pesan error PHP mentah di halaman audit.
            $kunciJawaban = null;
            if (!empty($soal->answer_formula)) {
                $kunciJawaban = $this->evaluasi->hitungAtauUrai(
                    $soal->answer_formula,
                    $jawaban->generated_variables ?? [],
                    $soal->jawaban_format
                );
            }

            return (object) [
                'soal_asli' => $soal->question_template,
                'soal' => $soal,
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
