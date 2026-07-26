<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AiGuruController extends Controller
{
    /** Batas maksimal prompt AI per guru per hari */
    private const BATAS_HARIAN = 5;

    private function getApiKey()
    {
        return config('services.groq.key');
    }

    /**
     * Mendapatkan cache key untuk penggunaan AI harian guru.
     */
    private function cacheKeyHarian(): string
    {
        return 'ai_usage_guru_' . auth()->id() . '_' . now()->toDateString();
    }

    /**
     * Cek apakah guru masih memiliki kuota AI hari ini.
     * Mengembalikan sisa kuota, atau false jika sudah habis.
     */
    private function cekKuota(): int
    {
        $terpakai = Cache::get($this->cacheKeyHarian(), 0);
        return max(0, self::BATAS_HARIAN - $terpakai);
    }

    /**
     * Catat penggunaan 1 prompt AI.
     */
    private function catatPenggunaan(): void
    {
        $key = $this->cacheKeyHarian();
        $terpakai = Cache::get($key, 0);
        // Simpan sampai akhir hari (sisa detik hingga tengah malam)
        $sisaDetik = now()->endOfDay()->diffInSeconds(now());
        Cache::put($key, $terpakai + 1, $sisaDetik);
    }

    /**
     * API endpoint untuk mengambil sisa kuota AI (dipakai frontend).
     */
    public function sisaKuota()
    {
        return response()->json([
            'sisa' => $this->cekKuota(),
            'batas' => self::BATAS_HARIAN,
        ]);
    }

    private function getAiResponse($prompt, $isJson = false)
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            throw new \Exception('API Key Groq belum dikonfigurasi.');
        }

        $payload = [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];

        if ($isJson) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withToken($apiKey)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://api.groq.com/openai/v1/chat/completions', $payload);

        if (!$response->successful()) {
            throw new \Exception('API Error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    public function generateMateri(Request $request)
    {
        // Cek kuota harian
        $sisa = $this->cekKuota();
        if ($sisa <= 0) {
            return response()->json([
                'success' => false,
                'pesan' => 'Kuota AI harian Anda telah habis (maks. ' . self::BATAS_HARIAN . ' kali/hari). Silakan coba lagi besok.',
                'kuota_habis' => true,
                'sisa' => 0,
                'batas' => self::BATAS_HARIAN,
            ], 429);
        }

        $request->validate([
            'poin_inti' => 'required|string|max:2000'
        ]);

        $prompt = "Kamu adalah seorang asisten ahli pembuat materi pembelajaran untuk sebuah Learning Management System (LMS). "
                . "Tugasmu adalah mengembangkan poin-poin inti berikut menjadi paragraf materi pelajaran yang edukatif, profesional, dan mudah dipahami siswa. "
                . "Format outputmu HARUS berupa tag HTML yang siap dirender di editor Trix (gunakan <h1>, <h2>, <p>, <ul>, <li>, <strong>, dll tanpa tambahan block markdown ```html). "
                . "\n\nPoin-poin inti dari guru:\n" . $request->poin_inti;

        try {
            $hasilHtml = $this->getAiResponse($prompt, false);
            // Bersihkan jika AI masih memberi markdown ```html
            $hasilHtml = preg_replace('/```html\s*(.*?)\s*```/is', '$1', $hasilHtml);
            $hasilHtml = preg_replace('/```\s*(.*?)\s*```/is', '$1', $hasilHtml);

            // Catat penggunaan setelah berhasil
            $this->catatPenggunaan();

            return response()->json([
                'success' => true,
                'html' => trim($hasilHtml),
                'sisa' => $this->cekKuota(),
                'batas' => self::BATAS_HARIAN,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'pesan' => $e->getMessage()
            ], 500);
        }
    }

    public function generateSoal(Request $request, Bab $bab)
    {
        if ($bab->kelas->guru_id !== auth()->id()) {
            abort(403);
        }

        // Cek kuota harian
        $sisa = $this->cekKuota();
        if ($sisa <= 0) {
            return response()->json([
                'success' => false,
                'pesan' => 'Kuota AI harian Anda telah habis (maks. ' . self::BATAS_HARIAN . ' kali/hari). Silakan coba lagi besok.',
                'kuota_habis' => true,
                'sisa' => 0,
                'batas' => self::BATAS_HARIAN,
            ], 429);
        }

        $request->validate([
            'topik' => 'required|string|max:500',
            'jumlah' => 'required|integer|min:1|max:10'
        ]);

        $prompt = "Kamu adalah asisten ahli pembuat soal ujian matematika dinamis. "
                . "Buatkan {$request->jumlah} soal tentang topik: '{$request->topik}'.\n"
                . "Sistem kami MENGHARUSKAN soal berupa soal matematika dinamis (BUKAN pilihan ganda teks). "
                . "Setiap soal harus memiliki:\n"
                . "1. `question_template`: string yang mengandung variabel dalam kurung kurawal, misal: \"Berapa hasil dari {x} + {y}?\"\n"
                . "2. `variable_rules`: objek konfigurasi variabel, misal: {\"x\": {\"min\": 1, \"max\": 10}, \"y\": {\"min\": 1, \"max\": 10}}\n"
                . "3. `answer_formula`: string rumus matematika valid (mengandung variabel tersebut) yang akan dievaluasi dengan eval(), misal: \"x + y\"\n\n"
                . "KEMBALIKAN OUTPUT SEBAGAI OBJEK JSON MURNI DENGAN KEY 'soal' TANPA BLOCK MARKDOWN ```json. Contoh struktur JSON:\n"
                . "{\n"
                . "  \"soal\": [\n"
                . "    {\n"
                . "      \"question_template\": \"Hitunglah nilai dari {a} dikali {b}.\",\n"
                . "      \"variable_rules\": {\"a\": {\"min\": 2, \"max\": 5}, \"b\": {\"min\": 2, \"max\": 5}},\n"
                . "      \"answer_formula\": \"a * b\"\n"
                . "    }\n"
                . "  ]\n"
                . "}\n"
                . "PENTING: Pastikan string JSON valid dan format variabel konsisten.";

        try {
            $jsonString = $this->getAiResponse($prompt, true);
            $jsonString = preg_replace('/```json\s*(.*?)\s*```/is', '$1', $jsonString);
            $jsonString = preg_replace('/```\s*(.*?)\s*```/is', '$1', $jsonString);
            
            $dataSoal = json_decode(trim($jsonString), true);
            $daftarSoal = $dataSoal['soal'] ?? [];

            if (!is_array($daftarSoal)) {
                throw new \Exception('Gagal mem-parsing response JSON dari AI.');
            }

            $count = 0;
            foreach ($daftarSoal as $soal) {
                if (!isset($soal['question_template']) || !isset($soal['variable_rules']) || !isset($soal['answer_formula'])) {
                    continue; // Skip invalid format
                }

                Soal::create([
                    'bab_id' => $bab->id,
                    'question_template' => $soal['question_template'],
                    'variable_rules' => $soal['variable_rules'],
                    'answer_formula' => $soal['answer_formula'],
                ]);
                $count++;
            }

            // Catat penggunaan setelah berhasil
            $this->catatPenggunaan();

            return response()->json([
                'success' => true,
                'pesan' => "Berhasil men-generate $count soal baru.",
                'sisa' => $this->cekKuota(),
                'batas' => self::BATAS_HARIAN,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'pesan' => $e->getMessage()
            ], 500);
        }
    }
}
