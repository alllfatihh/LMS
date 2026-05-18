<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermintaanSimpanSoal;
use App\Models\Bab;
use App\Models\Soal;
use App\Services\EvaluasiSoalService;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    public function __construct(
        private EvaluasiSoalService $evaluasi
    ) {}

    public function buat(Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        return view('guru.soal.buat', compact('bab'));
    }

    public function simpan(PermintaanSimpanSoal $permintaan, Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $aturan = [];
        foreach ($permintaan->variable_rules as $nama => $konfig) {
            $aturan[$nama] = ['min' => (int) $konfig['min'], 'max' => (int) $konfig['max']];
        }

        Soal::create([
            'bab_id' => $bab->id,
            'question_template' => $permintaan->question_template,
            'variable_rules' => $aturan,
            'answer_formula' => $permintaan->answer_formula,
        ]);

        return redirect()->route('guru.bab.tampil', $bab->id)->with('success', 'Soal baru berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $bab = $soal->bab;

        return view('guru.soal.edit', compact('bab', 'soal'));
    }

    public function perbarui(PermintaanSimpanSoal $permintaan, Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $aturan = [];
        foreach ($permintaan->variable_rules as $nama => $konfig) {
            $aturan[$nama] = ['min' => (int) $konfig['min'], 'max' => (int) $konfig['max']];
        }

        $soal->update([
            'question_template' => $permintaan->question_template,
            'variable_rules' => $aturan,
            'answer_formula' => $permintaan->answer_formula,
        ]);

        return redirect()->route('guru.bab.tampil', $soal->bab_id)->with('success', 'Soal berhasil diperbarui.');
    }

    public function hapus(Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $idBab = $soal->bab_id;
        $soal->delete();

        return redirect()->route('guru.bab.tampil', $idBab)->with('success', 'Soal sudah dihapus.');
    }

    public function pratinjau(Request $permintaan)
    {
        try {
            $aturanMentah = $permintaan->variable_rules ?? [];
            $templat = $permintaan->question_template ?? '';
            $rumus = $permintaan->answer_formula ?? '';

            $aturan = [];
            foreach ($aturanMentah as $nama => $konfig) {
                if (! empty($nama)) {
                    $aturan[$nama] = [
                        'min' => (int) ($konfig['min'] ?? 1),
                        'max' => (int) ($konfig['max'] ?? 10),
                    ];
                }
            }

            $variabel = $this->evaluasi->hasilkanVariabel($aturan);
            $teksSoal = $this->evaluasi->uraiTemplatSoal($templat, $variabel);
            $jawaban = null;
            if (! empty($rumus)) {
                $jawaban = $this->evaluasi->hitungRumus($rumus, $variabel);
            }

            return response()->json(['success' => true, 'variables' => $variabel, 'parsed_template' => $teksSoal, 'answer' => $jawaban]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 422);
        }
    }
}
