<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Soal;
use App\Services\EvaluasiSoalService;
use App\Services\MathExpressionProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiGuruController extends Controller
{
    private const BATAS_GENERATE_AI = 5;
    private const BATAS_JADIKAN_DINAMIS = 50;

    private function getApiKey()
    {
        return config('services.groq.key');
    }

    private function cacheKeyGenerate(): string
    {
        return 'ai_usage_generate_guru_' . auth()->id() . '_' . now()->toDateString();
    }

    private function cacheKeyDinamis(): string
    {
        return 'ai_usage_dinamis_guru_' . auth()->id() . '_' . now()->toDateString();
    }

    private function cekKuotaGenerate(): int
    {
        $terpakai = Cache::get($this->cacheKeyGenerate(), 0);
        return max(0, self::BATAS_GENERATE_AI - $terpakai);
    }

    private function cekKuotaDinamis(): int
    {
        $terpakai = Cache::get($this->cacheKeyDinamis(), 0);
        return max(0, self::BATAS_JADIKAN_DINAMIS - $terpakai);
    }

    private function catatPenggunaanGenerate(): void
    {
        $key = $this->cacheKeyGenerate();
        $terpakai = Cache::get($key, 0);
        $sisaDetik = (int) abs(now()->diffInSeconds(now()->endOfDay()));
        Cache::put($key, $terpakai + 1, $sisaDetik);
    }

    private function catatPenggunaanDinamis(): void
    {
        $key = $this->cacheKeyDinamis();
        $terpakai = Cache::get($key, 0);
        $sisaDetik = (int) abs(now()->diffInSeconds(now()->endOfDay()));
        Cache::put($key, $terpakai + 1, $sisaDetik);
    }

    public function sisaKuota()
    {
        return response()->json([
            'sisa_generate' => $this->cekKuotaGenerate(),
            'batas_generate' => self::BATAS_GENERATE_AI,
            'sisa_dinamis' => $this->cekKuotaDinamis(),
            'batas_dinamis' => self::BATAS_JADIKAN_DINAMIS,
            // Fallback
            'sisa' => $this->cekKuotaGenerate(),
            'batas' => self::BATAS_GENERATE_AI,
        ]);
    }

    /**
     * Blok persona + batas kurikulum, dipakai bersama oleh generateSoal() dan bantuTemplate()
     * supaya kedua fitur konsisten dalam mendefinisikan "SMP Kelas 7-9" ke AI.
     */
    private function personaDanKurikulumPrompt(): string
    {
        return "Kamu adalah Asisten Pembuat Soal Matematika SMP Profesional untuk Kurikulum Merdeka Kelas 7, 8, dan 9 di Indonesia.\n\n"
            . "BATAS KURIKULUM KETAT:\n"
            . "- DIIZINKAN: aritmetika & aritmetika sosial, aljabar dasar hingga SPLDV dan persamaan/fungsi kuadrat, geometri datar & bangun ruang, statistika dasar, peluang, transformasi geometri, trigonometri sudut istimewa (sin/cos/tan dalam derajat).\n"
            . "- DILARANG KERAS: kalkulus (limit, turunan, integral kecuali closed-form aljabar sederhana), matriks, vektor, trigonometri identitas/rumus jumlah-selisih sudut, statistika inferensial, bilangan kompleks, atau materi tingkat SMA/kuliah lainnya.\n"
            . "- Jika topik yang diminta guru berada di luar cakupan ini, tetap hasilkan versi SMP yang paling relevan/mendekati. JANGAN menolak permintaan.\n";
    }

    /**
     * Skema variable_rules dijelaskan sebagai struktur formal (bukan prosa), termasuk
     * dukungan eksplisit untuk tipe float/desimal dan pembulatan derived.
     */
    private function skemaVariableRulesPrompt(): string
    {
        return "SKEMA `variable_rules` (objek JSON, key = nama variabel):\n"
            . "- integer: {\"type\": \"integer\", \"min\": 1, \"max\": 20, \"kelipatan\": 5} (kelipatan opsional)\n"
            . "- float/desimal: {\"type\": \"float\", \"min\": 1.5, \"max\": 10.5, \"decimals\": 2} (decimals WAJIB diisi supaya hasil rapi)\n"
            . "- set (himpunan/data statistika): {\"type\": \"set\", \"count\": 5, \"min\": 1, \"max\": 20}\n"
            . "- derived (nilai dihitung dari variabel lain, WAJIB dipakai agar soal punya solusi rapi): {\"type\": \"derived\", \"formula\": \"2 * a + b\", \"bulatkan\": 2} (bulatkan opsional = jumlah digit desimal hasil)\n"
            . "- __constraints (opsional, array syarat boolean yang wajib benar): [\"a > b\", \"sisa_bagi(a, b) == 0\"]\n";
    }

    /**
     * Daftar fungsi matematika untuk prompt AI, diambil LANGSUNG dari MathExpressionProvider
     * (single source of truth) supaya tidak pernah drift dari fungsi yang benar-benar terdaftar.
     */
    private function buildFunctionReferenceText(): string
    {
        $daftar = (new MathExpressionProvider())->daftarUntukPrompt();

        $baris = [
            '=== FUNGSI MATEMATIKA YANG TERSEDIA DI ENGINE ===',
            'WAJIB dipakai persis seperti nama di bawah ini. DILARANG mengarang nama fungsi lain yang tidak tercantum.',
            'Operator yang didukung: +, -, *, /, ^ (pangkat). DILARANG perkalian implisit (2x), WAJIB eksplisit (2 * x).',
        ];

        foreach ($daftar as $kategori => $fungsi) {
            $baris[] = "- {$kategori}: " . implode(', ', $fungsi);
        }

        return implode("\n", $baris);
    }

    /**
     * Membersihkan & mem-parse JSON dari respons AI secara aman.
     * Melempar Exception dengan pesan ramah guru jika gagal, detail teknisnya dicatat ke log saja.
     */
    private function parseJsonAiResponse(string $raw): array
    {
        $jsonString = preg_replace('/```json\s*(.*?)\s*```/is', '$1', $raw);
        $jsonString = preg_replace('/```\s*(.*?)\s*```/is', '$1', $jsonString);

        $start = strpos($jsonString, '{');
        $end = strrpos($jsonString, '}');
        if ($start !== false && $end !== false) {
            $jsonString = substr($jsonString, $start, $end - $start + 1);
        }

        try {
            $data = json_decode(trim($jsonString), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            Log::warning('AI mengembalikan JSON tidak valid: ' . $e->getMessage() . ' | Raw: ' . substr($raw, 0, 1000));
            throw new \Exception('AI memberikan format respons yang tidak valid. Silakan coba generate ulang.');
        }

        if (!is_array($data)) {
            Log::warning('AI mengembalikan JSON yang bukan objek/array. Raw: ' . substr($raw, 0, 1000));
            throw new \Exception('AI memberikan format respons yang tidak valid. Silakan coba generate ulang.');
        }

        return $data;
    }

    /**
     * Validasi struktur satu butir soal SEBELUM dieksekusi ke EvaluasiSoalService, supaya
     * kesalahan tipe data (mis. variable_rules bukan objek) tertangkap dengan pesan jelas,
     * bukan meledak di lapisan eksekusi formula.
     */
    private function validasiStrukturSoal(array $soal): array
    {
        $errors = [];

        if (empty($soal['question_template']) || !is_string($soal['question_template'])) {
            $errors[] = 'question_template kosong atau bukan string';
        }

        if (!isset($soal['variable_rules']) || !is_array($soal['variable_rules'])) {
            $errors[] = 'variable_rules bukan objek/array';
            return $errors;
        }

        foreach ($soal['variable_rules'] as $nama => $rule) {
            if ($nama === '__constraints') {
                if (!is_array($rule)) {
                    $errors[] = '__constraints harus berupa array';
                }
                continue;
            }

            if (!is_array($rule)) {
                $errors[] = "variabel '{$nama}' harus berupa objek konfigurasi";
                continue;
            }

            $tipe = $rule['type'] ?? 'integer';
            if (!in_array($tipe, ['integer', 'float', 'set', 'derived', 'number'], true)) {
                $errors[] = "variabel '{$nama}' punya type tidak dikenal: {$tipe}";
                continue;
            }

            if ($tipe === 'derived') {
                if (empty($rule['formula']) || !is_string($rule['formula'])) {
                    $errors[] = "variabel derived '{$nama}' butuh formula string";
                }
            } elseif (!isset($rule['set_values'])) {
                if (!isset($rule['min']) || !is_numeric($rule['min'])) {
                    $errors[] = "variabel '{$nama}' butuh min numerik";
                }
                if (!isset($rule['max']) || !is_numeric($rule['max'])) {
                    $errors[] = "variabel '{$nama}' butuh max numerik";
                }
            }
        }

        if (empty($soal['answer_formula']) || !is_string($soal['answer_formula'])) {
            $errors[] = 'answer_formula kosong atau bukan string';
        }

        return $errors;
    }

    private function getAiResponse($prompt, $isJson = false)
    {
        $apiKey = $this->getApiKey();
        if (empty($apiKey)) {
            throw new \Exception('API Key Groq belum dikonfigurasi.');
        }

        $payload = [
            'model' => 'openai/gpt-oss-120b',
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

        // [SIDANG] Di sinilah aplikasi kita mengirim HTTP Request (stateless) ke server API Groq.
        // Model AI yang dipakai (Llama-3.3-70b) disisipkan token otentikasi di Header.
        try {
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.groq.com/openai/v1/chat/completions', $payload);
        } catch (ConnectionException $e) {
            Log::warning('Groq API tidak merespons: ' . $e->getMessage());
            throw new \Exception('Server AI tidak merespons saat ini. Silakan coba lagi beberapa saat lagi.');
        }

        if (!$response->successful()) {
            throw new \Exception('API Error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    public function generateMateri(Request $request)
    {
        // Cek kuota harian materi
        $sisa = $this->cekKuotaGenerate();
        if ($sisa <= 0) {
            return response()->json([
                'success' => false,
                'pesan' => 'Kuota AI harian Anda telah habis (maks. ' . self::BATAS_GENERATE_AI . ' kali/hari). Silakan coba lagi besok.',
                'kuota_habis' => true,
                'sisa' => 0,
                'batas' => self::BATAS_GENERATE_AI,
            ], 429);
        }

        $request->validate([
            'poin_inti' => 'required|string|max:2000'
        ]);

        $prompt = "Kamu adalah seorang asisten ahli pembuat materi pembelajaran matematika SMP (Kelas 7, 8, 9 Kurikulum Merdeka) untuk sebuah Learning Management System (LMS). "
                . "Tugasmu adalah mengembangkan poin-poin inti berikut menjadi paragraf materi pelajaran yang edukatif, profesional, dan mudah dipahami siswa SMP (bukan tingkat SMA/kuliah). "
                . "Format outputmu HARUS berupa tag HTML yang siap dirender di editor Trix (gunakan <h1>, <h2>, <p>, <ul>, <li>, <strong>, dll tanpa tambahan block markdown ```html). "
                . "PENTING: Jika materi melibatkan rumus matematika (termasuk Aljabar, Aritmatika, Geometri, dll), WAJIB gunakan format LaTeX dengan pembungkus \\\\( ... \\\\) untuk inline math, atau \$\$ ... \$\$ untuk block math. "
                . "\n\nPoin-poin inti dari guru:\n" . $request->poin_inti;

        try {
            $hasilHtml = $this->getAiResponse($prompt, false);
            // Bersihkan jika AI masih memberi markdown ```html
            $hasilHtml = preg_replace('/```html\s*(.*?)\s*```/is', '$1', $hasilHtml);
            $hasilHtml = preg_replace('/```\s*(.*?)\s*```/is', '$1', $hasilHtml);

            // Catat penggunaan setelah berhasil
            $this->catatPenggunaanGenerate();

            return response()->json([
                'success' => true,
                'html' => trim($hasilHtml),
                'sisa' => $this->cekKuotaGenerate(),
                'batas' => self::BATAS_GENERATE_AI,
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

        // Cek kuota harian soal
        $sisa = $this->cekKuotaGenerate();
        if ($sisa <= 0) {
            return response()->json([
                'success' => false,
                'pesan' => 'Kuota AI harian Anda telah habis (maks. ' . self::BATAS_GENERATE_AI . ' kali/hari). Silakan coba lagi besok.',
                'kuota_habis' => true,
                'sisa' => 0,
                'batas' => self::BATAS_GENERATE_AI,
            ], 429);
        }

        $request->validate([
            'topik' => 'required|string|max:500',
            'jumlah' => 'required|integer|min:1|max:10'
        ]);

        $prompt = $this->personaDanKurikulumPrompt()
                . "\nTugasmu adalah membuat {$request->jumlah} soal dinamis yang berkualitas tinggi, mendalam, dan bervariasi (mulai dari pemahaman konsep, cerita kontekstual, hingga penalaran/HOTS) tentang topik: '{$request->topik}'.\n\n"
                . "=== ARSITEKTUR MESIN SOAL DINAMIS KAMI ===\n"
                . "Soal BUKAN pilihan ganda statis, melainkan template parametrik dengan angka acak.\n"
                . "Setiap butir soal WAJIB berupa objek JSON dengan atribut berikut:\n"
                . "1. `question_template` (string): Teks soal dalam Bahasa Indonesia yang rapi dan kontekstual. Selipkan variabel dalam kurung kurawal, contoh: {a}, {b}, {x}, {r}. "
                . "Gunakan format LaTeX \\\\( ... \\\\) untuk ekspresi matematika yang indah (misal: \\\\(ax + b = c\\\\), \\\\(x^2\\\\), \\\\(\\sqrt{x}\\\\), \\\\(\\pi\\\\)). Berikan instruksi jelas ke siswa tentang format jawaban yang diminta (misal: jika pecahan 'Tulis dalam bentuk pecahan biasa a/b', jika urutan/koordinat 'Tulis dalam bentuk x, y').\n"
                . "2. `variable_rules` (objek JSON): lihat skema di bawah.\n"
                . "3. `answer_formula` (string): Rumus evaluasi jawaban benar. WAJIB menggunakan operator eksplisit (+, -, *, /, ^). DILARANG KERAS perkalian implisit seperti '2x' atau 'ab', WAJIB '2 * x' atau 'a * b'.\n"
                . "4. `bobot` (integer): Tentukan bobot atau skor soal berdasarkan tingkat kesulitannya (contoh: Mudah = 10, Sedang = 15, Sulit/HOTS = 20).\n\n"
                . $this->skemaVariableRulesPrompt() . "\n"
                . $this->buildFunctionReferenceText() . "\n\n"
                . "=== CONTOH POLA SOAL KOMPLEKS BERBAGAI TOPIK SMP ===\n"
                . "1. [Persamaan Kuadrat Berakar Bulat]: Tentukan x1 & x2 dulu, turunkan b dan c:\n"
                . "   variable_rules: {\"x1\": {\"type\": \"integer\", \"min\": 1, \"max\": 5}, \"x2\": {\"type\": \"integer\", \"min\": 2, \"max\": 6}, \"b\": {\"type\": \"derived\", \"formula\": \"-(x1 + x2)\"}, \"c\": {\"type\": \"derived\", \"formula\": \"x1 * x2\"}}\n"
                . "   question_template: \"Tentukan hasil kali akar-akar (x1 . x2) dari persamaan kuadrat \\\\(x^2 + ({b})x + {c} = 0\\\\)!\"\n"
                . "   answer_formula: \"x1 * x2\"\n"
                . "   bobot: 15\n\n"
                . "2. [SPLDV Dua Variabel]: Tentukan x dan y bulat, turunkan c1 dan c2:\n"
                . "   variable_rules: {\"x\": {\"type\": \"integer\", \"min\": 2, \"max\": 8}, \"y\": {\"type\": \"integer\", \"min\": 1, \"max\": 6}, \"a1\": {\"type\": \"integer\", \"min\": 2, \"max\": 4}, \"b1\": {\"type\": \"integer\", \"min\": 1, \"max\": 3}, \"c1\": {\"type\": \"derived\", \"formula\": \"a1 * x + b1 * y\"}, \"a2\": {\"type\": \"integer\", \"min\": 1, \"max\": 3}, \"b2\": {\"type\": \"integer\", \"min\": 2, \"max\": 5}, \"c2\": {\"type\": \"derived\", \"formula\": \"a2 * x + b2 * y\"}}\n"
                . "   question_template: \"Diketahui sistem persamaan: \\\\({a1}x + {b1}y = {c1}\\\\) dan \\\\({a2}x + {b2}y = {c2}\\\\). Berapakah nilai \\\\(x + y\\\\)?\"\n"
                . "   answer_formula: \"x + y\"\n"
                . "   bobot: 20\n\n"
                . "3. [Teorema Pythagoras / Tripel Pythagoras]:\n"
                . "   variable_rules: {\"m\": {\"type\": \"integer\", \"min\": 2, \"max\": 5}, \"n\": {\"type\": \"integer\", \"min\": 1, \"max\": 4}, \"__constraints\": [\"m > n\"], \"alas\": {\"type\": \"derived\", \"formula\": \"m^2 - n^2\"}, \"tinggi\": {\"type\": \"derived\", \"formula\": \"2 * m * n\"}, \"miring\": {\"type\": \"derived\", \"formula\": \"m^2 + n^2\"}}\n"
                . "   question_template: \"Sebuah segitiga siku-siku memiliki alas {alas} cm dan tinggi {tinggi} cm. Hitung panjang sisi miringnya!\"\n"
                . "   answer_formula: \"miring\"\n"
                . "   bobot: 10\n\n"
                . "4. [Lingkaran & Bangun Ruang]: Jari-jari kelipatan 7:\n"
                . "   variable_rules: {\"r\": {\"type\": \"integer\", \"min\": 7, \"max\": 28, \"kelipatan\": 7}, \"t\": {\"type\": \"integer\", \"min\": 5, \"max\": 15}}\n"
                . "   question_template: \"Sebuah tabung memiliki jari-jari alas {r} cm dan tinggi {t} cm. Hitunglah volume tabung tersebut! (Gunakan \\\\(\\pi = \\\\frac{22}{7}\\\\))\"\n"
                . "   answer_formula: \"(22 / 7) * (r ^ 2) * t\"\n"
                . "   bobot: 15\n\n"
                . "5. [Peluang dengan Jawaban Pecahan Pasti]:\n"
                . "   variable_rules: {\"merah\": {\"type\": \"integer\", \"min\": 3, \"max\": 8}, \"kuning\": {\"type\": \"integer\", \"min\": 2, \"max\": 6}, \"total\": {\"type\": \"derived\", \"formula\": \"merah + kuning\"}}\n"
                . "   question_template: \"Dalam sebuah kantong terdapat {merah} bola merah dan {kuning} bola kuning. Diambil 1 bola secara acak. Berapakah peluang terambilnya bola merah? (Tulis dalam pecahan biasa, contoh: 3/5)\"\n"
                . "   answer_formula: \"pecahan(merah, total)\"\n"
                . "   bobot: 10\n\n"
                . "6. [Statistika Data Acak]:\n"
                . "   variable_rules: {\"data\": {\"type\": \"set\", \"count\": 7, \"min\": 50, \"max\": 95}}\n"
                . "   question_template: \"Berikut adalah data nilai ulangan matematika siswa: {data}. Tentukan jangkauan interkuartil (IQR) dari data tersebut!\"\n"
                . "   answer_formula: \"jangkauan_interkuartil(data)\"\n"
                . "   bobot: 15\n\n"
                . "7. [Aritmetika Sosial dengan Variabel Desimal]: Suku bunga dalam bentuk desimal:\n"
                . "   variable_rules: {\"modal\": {\"type\": \"integer\", \"min\": 100000, \"max\": 500000, \"kelipatan\": 10000}, \"persen_bunga\": {\"type\": \"float\", \"min\": 2.5, \"max\": 7.5, \"decimals\": 1}, \"bulan\": {\"type\": \"integer\", \"min\": 6, \"max\": 18, \"kelipatan\": 6}}\n"
                . "   question_template: \"Seseorang menabung Rp{modal} dengan bunga tunggal {persen_bunga}% per tahun selama {bulan} bulan. Berapa besar bunga yang diperoleh?\"\n"
                . "   answer_formula: \"bunga_tunggal(modal, persen_bunga, bulan)\"\n"
                . "   bobot: 15\n\n"
                . "=== ATURAN FORMAT OUTPUT ===\n"
                . "- KEMBALIKAN OUTPUT HANYA BERUPA OBJEK JSON MURNI dengan key 'soal' (array of objects).\n"
                . "- DILARANG menambahkan markdown ```json atau teks pembuka/penutup apapun.\n"
                . "- Pastikan SEMUA tanda backslash LaTeX di-double escape (\\\\\\\\), contoh: \\\\\\\\frac{a}{b}, \\\\\\\\sqrt{a}, \\\\\\\\pi, \\\\\\\\( ... \\\\\\\\).\n"
                . "- Pastikan JSON valid dan dapat langsung di-decode oleh json_decode().";

        try {
            $jsonString = $this->getAiResponse($prompt, true);
            $dataSoal = $this->parseJsonAiResponse($jsonString);
            $daftarSoal = $dataSoal['soal'] ?? [];

            if (!is_array($daftarSoal)) {
                throw new \Exception('AI tidak mengembalikan daftar soal yang valid.');
            }

            $count = 0;
            $evaluasiService = app(EvaluasiSoalService::class);

            foreach ($daftarSoal as $soal) {
                if (!is_array($soal)) {
                    continue;
                }

                // Validasi struktur SEBELUM dieksekusi, supaya kesalahan tipe data (bukan cuma
                // rumus yang gagal) juga tertangkap dengan pesan yang jelas di log.
                $errorSkema = $this->validasiStrukturSoal($soal);
                if (!empty($errorSkema)) {
                    Log::warning('AI menghasilkan struktur soal tidak valid: ' . implode('; ', $errorSkema));
                    continue;
                }

                // Test rumus sebelum menyimpan untuk menghindari UnknownOperatorException di kemudian hari
                try {
                    $testVariabel = $evaluasiService->hasilkanVariabel($soal['variable_rules']);
                    $evaluasiService->hitungRumus($soal['answer_formula'], $testVariabel);
                } catch (\Exception $e) {
                    Log::warning("AI menghasilkan rumus tidak valid: " . $soal['answer_formula'] . " Error: " . $e->getMessage());
                    continue; // Skip soal jika rumusnya tidak bisa dieksekusi
                }

                Soal::create([
                    'bab_id' => $bab->id,
                    'question_template' => $soal['question_template'],
                    'variable_rules' => $soal['variable_rules'],
                    'answer_formula' => $soal['answer_formula'],
                    'bobot' => $soal['bobot'] ?? 10,
                ]);
                $count++;
            }

            if ($count === 0) {
                throw new \Exception('AI tidak menghasilkan soal yang valid. Rumus mungkin tidak dapat diproses (contoh perkalian implisit 2x). Silakan coba lagi.');
            }

            // Catat penggunaan setelah berhasil
            $this->catatPenggunaanGenerate();

            return response()->json([
                'success' => true,
                'pesan' => "Berhasil men-generate $count soal baru.",
                'sisa' => $this->cekKuotaGenerate(),
                'batas' => self::BATAS_GENERATE_AI,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'pesan' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membantu guru mengisi template soal satu per satu berdasarkan deskripsi natural.
     */
    public function bantuTemplate(Request $request)
    {
        $sisa = $this->cekKuotaDinamis();
        if ($sisa <= 0) {
            return response()->json([
                'success' => false,
                'pesan' => 'Kuota AI harian Anda telah habis. Silakan coba lagi besok.',
                'kuota_habis' => true,
            ], 429);
        }

        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        $prompt = $this->personaDanKurikulumPrompt()
            . "\nGuru menempelkan sebuah soal matematika statis (biasa) berikut:\n\"{$request->deskripsi}\"\n\n"
            . "Tugasmu adalah MENGANALISIS soal statis ini dan mengubahnya menjadi objek JSON dengan salah satu dari 2 mode berikut:\n\n"
            . "=== MODE 1: SOAL DINAMIS (Default untuk sebagian besar soal matematika, termasuk persamaan kompleks atau Integral JIKA kamu bisa menemukan rumus akhir/closed-form nya) ===\n"
            . "1. Ganti angka dengan variabel {a}, {b}, dsb. (Misal: batas integral, koefisien, pangkat). KHUSUS untuk variabel di dalam sintaks LaTeX yang butuh kurung kurawal (seperti `\int_{a}^{b}` atau `\frac{a}{b}`), kamu WAJIB menggunakan TIGA kurung kurawal, contoh: `\int_{{{a}}}^{{{b}}}` atau `\frac{{{a}}}{{{b}}}`. Jika tidak, kurung kurawal LaTeX-nya akan hilang di sistem kami!\n"
            . "2. Tentukan rentang (min, max) di `variable_rules`. Gunakan type \"float\" dengan \"decimals\" jika soal aslinya memang memakai angka desimal — JANGAN paksakan semua jadi bilangan bulat. PENTING: Pastikan rentang variabel menghasilkan angka yang 'cantik' (tidak menghasilkan desimal tak berujung atau error komputasi).\n"
            . "3. Tulis rumus matematika (closed-form) untuk mencari jawaban di `answer_formula`. Misalnya untuk integral substitusi, lakukan evaluasi matematis secara aljabar (contoh: JANGAN lupakan fungsi di dalamnya. Integral 2x(x^2+1)^3 dari a ke b adalah `(((b^2+1)^4)/4) - (((a^2+1)^4)/4)`). Tulis rumusnya di sini tanpa menggunakan simbol integral/kalkulus.\n"
            . "4. Jika jawaban berupa pecahan, kamu WAJIB menggunakan fungsi `pecahan(pembilang, penyebut)`. Contoh: `pecahan(a+1, b)`. JANGAN GUNAKAN operator bagi `/`!\n"
            . "5. KONSISTENSI SANGAT PENTING: Pastikan angka/koefisien di `question_template` SAMA PERSIS dengan rumus di `variable_rules`! Jangan sampai di teks soal tertulis '3 pensil' tetapi di rumus kamu memakai `13*y` (typo).\n\n"
            . "PENTING UNTUK SEMUA MODE: Jika guru menempelkan teks panjang yang berisi 'Pembahasan dan Langkah Penyelesaian', PISAHKAN teks tersebut! Bagian sebelum pembahasan masuk ke `question_template`, dan bagian pembahasannya masuk ke `pembahasan_template`. JANGAN menaruh seluruh teks di `question_template`!\n\n"
            . "=== MODE 2: SOAL STATIS (HANYA digunakan JIKA soal murni teori/hafalan, atau jika rumus matematikanya 100% MUSTAHIL dikerjakan secara dinamis) ===\n"
            . "1. Biarkan `question_template` PERSIS seperti soal asli (JANGAN MENGUBAH APAPUN). DILARANG MENYISIPKAN kurung kurawal `{}`!\n"
            . "2. KOSONGKAN `variable_rules` menjadi `{}` mutlak!\n"
            . "3. Ekstrak HANYA HASIL AKHIR MATEMATIKANYA SAJA dari teks (misal: \\frac{7}{12} atau 5) dan letakkan di `answer_formula`. JANGAN mengosongkan `answer_formula` jika soal punya jawaban akhir! Jika jawaban berupa pecahan, kamu WAJIB menggunakan fungsi `pecahan(pembilang, penyebut)` (misal `pecahan(7, 12)`). Jika jawaban mengandung bentuk akar, kamu WAJIB menggunakan fungsi `sederhanakan_akar(n)` (misal jika jawabannya 5 akar 2, tulis `sederhanakan_akar(50)`). JANGAN PERNAH MENULIS teks mentah seperti '5 akar 2'!\n"
            . "4. Set `jawaban_format` menjadi 'aljabar'.\n"
            . "5. PENTING: Untuk `pembahasan_template`, SALIN SELURUH teks pembahasan asli TANPA memotongnya, dan PERTAHANKAN jeda baris (gunakan \\n).\n\n"
            . $this->skemaVariableRulesPrompt() . "\n"
            . $this->buildFunctionReferenceText() . "\n\n"
            . "=== FORMAT OUTPUT ===\n"
            . "Kembalikan HANYA objek JSON murni (tanpa markdown) dengan format persis ini. PASTIKAN SEMUA BACKSLASH (\\) PADA RUMUS LATEX DI-ESCAPE (DOUBLE BACKSLASH, CONTOH: `\\\\frac`, BUKAN `\\frac`):\n"
            . "{\n"
            . "  \"langkah_analisis\": \"Uraikan dengan sangat detail (langkah demi langkah) bagaimana kamu menurunkan rumus matematika atau menyelesaikan integral/persamaan ini secara aljabar. Hitung dengan cermat sebelum menentukan variabel dan formula akhirnya!\",\n"
            . "  \"question_template\": \"Teks soal dengan {variabel}...\",\n"
            . "  \"variable_rules\": { \"a\": {\"type\": \"integer\", \"min\": 1, \"max\": 10}, \"b\": {\"type\": \"derived\", \"formula\": \"a * 2\"} },\n"
            . "  \"answer_formula\": \"a + b\",\n"
            . "  \"jawaban_format\": \"bulat\",\n"
            . "  \"pembahasan_template\": \"Langkah 1: ... = [[ a + b ]]\",\n"
            . "  \"bobot\": 10,\n"
            . "  \"penjelasan\": \"Penjelasan singkat 1-2 kalimat kepada guru mengenai variabel apa saja yang dibuat dari soal asli dan rentangnya.\"\n"
            . "}\n\n"
            . "Untuk `jawaban_format`, pilih salah satu yang paling tepat: 'bulat' (jika jawaban berupa bilangan bulat), 'desimal' (jika jawaban berupa bilangan desimal/koma), 'angka' (angka umum), 'pecahan' (pecahan a/b), 'koordinat' ((x,y)), 'himpunan' ({a,b,...}), 'aljabar', atau 'akar'.\n"
            . "PENTING: Jika soal meminta lebih dari satu jawaban (seperti 'akar-akar persamaan kuadrat'), kamu WAJIB menyatukan jawabannya menggunakan fungsi `himpunan(x1, x2)` di dalam `answer_formula` dan mengatur `jawaban_format` menjadi 'himpunan'.\n"
            . "Untuk `pembahasan_template`, gunakan `[[ rumus ]]` untuk operasi matematika (bukan `{{ }}`). Isi dengan langkah-langkah penyelesaian yang jelas.";

        try {
            $jsonString = $this->getAiResponse($prompt, true);
            $data = $this->parseJsonAiResponse($jsonString);

            if (!isset($data['question_template'])) {
                throw new \Exception('AI menghasilkan respons yang tidak valid. Silakan coba lagi.');
            }

            // Validasi rumus sebelum dikirim ke guru
            try {
                $evaluasiService = app(EvaluasiSoalService::class);
                $testVar = $evaluasiService->hasilkanVariabel($data['variable_rules'] ?? []);
                $evaluasiService->hitungRumus($data['answer_formula'], $testVar);
            } catch (\Exception $e) {
                // Tetap kembalikan, beri peringatan saja
                $data['peringatan'] = 'Peringatan: Rumus jawaban perlu diperiksa ulang (' . $e->getMessage() . ')';
            }

            $this->catatPenggunaanDinamis();

            return response()->json([
                'success' => true,
                'data' => $data,
                'sisa' => $this->cekKuotaDinamis(),
                'batas' => self::BATAS_JADIKAN_DINAMIS,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'pesan' => $e->getMessage(),
            ], 500);
        }
    }
}
