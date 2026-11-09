<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermintaanSimpanSoal;
use App\Models\Bab;
use App\Models\Soal;
use App\Services\EvaluasiSoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $aturan = $this->prosesAturanVariabel($permintaan->variables);
        if ($permintaan->filled('graph_formula')) {
            $aturan['graph_formula'] = $permintaan->graph_formula;
        }

        $imagePath = null;
        if ($permintaan->hasFile('image')) {
            $imagePath = $permintaan->file('image')->store('soal_images', 'public');
        }

        Soal::create([
            'bab_id'              => $bab->id,
            'question_template'   => $permintaan->question_template,
            'variable_rules'      => $aturan,
            'answer_formula'      => $permintaan->answer_formula,
            'jawaban_format'      => $permintaan->jawaban_format ?? 'angka',
            'pembahasan_template' => $permintaan->pembahasan_template,
            'image_path'          => $imagePath,
            'bobot'               => $permintaan->bobot,
        ]);

        return redirect()->route('guru.bab.tampil', $bab)->with('sukses', 'Soal berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $editorInitial = [
            'template'   => $soal->question_template,
            'formula'    => $soal->answer_formula,
            'graphFormula' => $soal->variable_rules['graph_formula'] ?? '',
            'format'     => $soal->jawaban_format ?? 'angka',
            'pembahasan' => $soal->pembahasan_template,
            'imageUrl'   => $soal->image_path ? Storage::url($soal->image_path) : null,
        ];

        $vars = [];
        $rules = $soal->variable_rules;
        unset($rules['graph_formula']);
        foreach ($rules as $name => $konfig) {
            $vars[] = [
                'name'      => $name,
                'type'      => $konfig['type'] ?? 'integer',
                'min'       => $konfig['min'] ?? 1,
                'max'       => $konfig['max'] ?? 10,
                'kelipatan' => $konfig['kelipatan'] ?? '',
                'decimals'  => $konfig['decimals'] ?? (($konfig['type'] ?? '') === 'float' ? 2 : ''),
                'formula'   => $konfig['formula'] ?? '',
                'count'     => $konfig['count'] ?? '',
            ];
        }
        $editorInitial['variables'] = $vars;
        
        $bab = $soal->bab;

        return view('guru.soal.edit', compact('soal', 'editorInitial', 'bab'));
    }

    public function perbarui(PermintaanSimpanSoal $permintaan, Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $aturan = $this->prosesAturanVariabel($permintaan->variables);
        if ($permintaan->filled('graph_formula')) {
            $aturan['graph_formula'] = $permintaan->graph_formula;
        }

        $data = [
            'question_template'   => $permintaan->question_template,
            'variable_rules'      => $aturan,
            'answer_formula'      => $permintaan->answer_formula,
            'jawaban_format'      => $permintaan->jawaban_format ?? 'angka',
            'pembahasan_template' => $permintaan->pembahasan_template,
            'bobot'               => $permintaan->bobot,
        ];

        if ($permintaan->has('remove_image') && $permintaan->remove_image == '1') {
            if ($soal->image_path) {
                Storage::disk('public')->delete($soal->image_path);
            }
            $data['image_path'] = null;
        }

        if ($permintaan->hasFile('image')) {
            if ($soal->image_path) {
                Storage::disk('public')->delete($soal->image_path);
            }
            $data['image_path'] = $permintaan->file('image')->store('soal_images', 'public');
        }

        $soal->update($data);

        return redirect()->route('guru.bab.tampil', $soal->bab_id)->with('success', 'Soal berhasil diperbarui.');
    }

    public function hapus(Soal $soal)
    {
        if ($soal->bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        $idBab = $soal->bab_id;
        
        if ($soal->image_path) {
            Storage::disk('public')->delete($soal->image_path);
        }
        
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
                if (empty($nama)) continue;
                $tipe = $konfig['type'] ?? 'integer';
                if ($tipe === 'derived') {
                    $aturan[$nama] = [
                        'type' => 'derived',
                        'formula' => $konfig['formula'] ?? '',
                    ];
                } else {
                    $entry = [
                        'type' => ($tipe === 'number') ? 'integer' : $tipe,
                        'min'  => isset($konfig['min']) ? ($tipe === 'float' ? (float) $konfig['min'] : (int) $konfig['min']) : 1,
                        'max'  => isset($konfig['max']) ? ($tipe === 'float' ? (float) $konfig['max'] : (int) $konfig['max']) : 10,
                    ];
                    if (!empty($konfig['kelipatan'])) {
                        $entry['kelipatan'] = (float) $konfig['kelipatan'];
                    }
                    if (isset($konfig['decimals']) && $konfig['decimals'] !== '') {
                        $entry['decimals'] = (int) $konfig['decimals'];
                    } elseif ($tipe === 'float') {
                        $entry['decimals'] = 2;
                    }
                    if (!empty($konfig['count'])) {
                        $entry['count'] = (int) $konfig['count'];
                    }
                    $aturan[$nama] = $entry;
                }
            }

            $variabel = $this->evaluasi->hasilkanVariabel($aturan);
            $teksSoal = $this->evaluasi->formatHTML($this->evaluasi->uraiTemplatSoal($templat, $variabel));
            $grafikTerurai = null;
            if ($permintaan->filled('graph_formula')) {
                $grafikTerurai = $this->evaluasi->uraiTemplatSoal($permintaan->graph_formula, $variabel);
            }
            $format = $permintaan->format ?? 'angka';
            $jawaban = null;
            if (! empty($rumus)) {
                $jawaban = $this->evaluasi->hitungAtauUrai($rumus, $variabel, $format);
            }

            return response()->json(['success' => true, 'variables' => $variabel, 'parsed_template' => $teksSoal, 'parsed_graph' => $grafikTerurai, 'answer' => $jawaban]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 422);
        }
    }

    private function prosesAturanVariabel(array $variabelInput): array
    {
        $aturan = [];
        foreach ($variabelInput as $konfig) {
            $nama = trim($konfig['name'] ?? '');
            if (empty($nama)) continue;
            unset($konfig['name']);

            $tipe = $konfig['type'] ?? 'integer';
            if ($tipe === 'derived') {
                $aturan[$nama] = [
                    'type' => 'derived',
                    'formula' => $konfig['formula'] ?? '',
                ];
            } else {
                $entry = [
                    'type' => ($tipe === 'number') ? 'integer' : $tipe,
                    'min'  => isset($konfig['min']) ? ($tipe === 'float' ? (float) $konfig['min'] : (int) $konfig['min']) : 1,
                    'max'  => isset($konfig['max']) ? ($tipe === 'float' ? (float) $konfig['max'] : (int) $konfig['max']) : 10,
                ];
                if (!empty($konfig['kelipatan'])) {
                    $entry['kelipatan'] = (float) $konfig['kelipatan'];
                }
                if (isset($konfig['decimals']) && $konfig['decimals'] !== '') {
                    $entry['decimals'] = (int) $konfig['decimals'];
                } elseif ($tipe === 'float') {
                    $entry['decimals'] = 2;
                }
                if (!empty($konfig['count'])) {
                    $entry['count'] = (int) $konfig['count'];
                }
                $aturan[$nama] = $entry;
            }
        }
        return $aturan;
    }
}
