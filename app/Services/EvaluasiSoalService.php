<?php

namespace App\Services;

use NXP\MathExecutor;

/**
 * Wrapper agar MathExecutor mengembalikan urutan jawaban yang pasti.
 */
class UrutanJawaban
{
    public function __construct(public array $items)
    {
    }

    public function __toString(): string
    {
        return implode(',', $this->items);
    }
}

/**
 * Wrapper agar jawaban wajib berbentuk pecahan strict (siswa tak bisa input desimal).
 */
class PecahanJawaban
{
    public string $pembilang;
    public string $penyebut;

    public function __construct($pembilang, $penyebut)
    {
        $this->pembilang = (string) $pembilang;
        $this->penyebut = (string) $penyebut;
    }

    public function __toString(): string
    {
        return "{$this->pembilang}/{$this->penyebut}";
    }
}

class EvaluasiSoalService
{
    private MathExecutor $expressionLanguage;

    /** Digit desimal minimum saat membandingkan jawaban siswa vs nilai benar. */
    public const SKALA_PERBANDINGAN_JAWABAN = 2;

    /** Batas atas digit desimal presisi (mencegah noise floating point membuat skala meledak). */
    public const MAKS_DESIMAL_PRESISI = 6;

    /** Batas percobaan pengacakan ulang untuk memenuhi __constraints. */
    public const MAKS_PERCOBAAN_CONSTRAINT = 300;

    public function __construct()
    {
        $this->expressionLanguage = new MathExecutor();

        // Mendaftarkan fungsi pecahan
        $this->expressionLanguage->addFunction('pecahan', function ($pembilang, $penyebut) {
            return new PecahanJawaban($pembilang, $penyebut);
        });

        // Mendaftarkan fungsi urutan (array dinamis tak terbatas)
        $this->expressionLanguage->addFunction('urutan', function (...$args) {
            return new UrutanJawaban($args);
        });

        // Mendaftarkan fungsi matematika dasar tambahan
        $this->expressionLanguage->addFunction('akar', function ($n) {
            return sqrt((float) $n);
        });


        // Pendaftaran fungsi matematika lanjutan dipindah ke class terpisah
        $mathProvider = new MathExpressionProvider();
        $mathProvider->registerFunctions($this->expressionLanguage);
    }

    private function setSemuaVar(array $variabel): void
    {
        // Set variabel individu
        foreach ($variabel as $kunci => $nilai) {
            if (!is_array($nilai)) {
                $this->expressionLanguage->setVar($kunci, $nilai);
            }
        }

        // Coba injeksi array keys jika dibutuhkan MathExecutor custom (optional)
        foreach ($variabel as $kunci => $nilai) {
            if (is_array($nilai)) {
                foreach ($nilai as $i => $v) {
                    $this->expressionLanguage->setVar($kunci . '_' . $i, $v);
                }
            }
        }
    }

    public function hasilkanVariabel(array $aturan): array
    {
        $variabel = [];
        $percobaan = 0;

        while ($percobaan < self::MAKS_PERCOBAAN_CONSTRAINT) {
            $percobaan++;
            $variabel = [];

            foreach ($aturan as $nama => $rule) {
                if ($nama === '__constraints')
                    continue;

                // Skip non-array entries (e.g. graph_formula is a string, not a variable rule)
                if (!is_array($rule))
                    continue;

                $tipe = $rule['type'] ?? 'integer';

                if ($tipe === 'set') {
                    if (isset($rule['set_values'])) {
                        $set = explode(',', $rule['set_values']);
                        $variabel[$nama] = trim($set[array_rand($set)]);
                    } else {
                        // Generate a random set of unique integers (for himpunan)
                        $count = (int) ($rule['count'] ?? 3);
                        $min = (int) ($rule['min'] ?? 1);
                        $max = (int) ($rule['max'] ?? 10);
                        $pool = range($min, $max);
                        shuffle($pool);
                        $items = array_slice($pool, 0, min($count, count($pool)));
                        sort($items);
                        $variabel[$nama] = '{' . implode(', ', $items) . '}';
                    }
                } elseif ($tipe === 'derived') {
                    $hasil = $this->hitungRumus($rule['formula'], $variabel);
                    if (isset($rule['bulatkan']) && is_numeric($rule['bulatkan']) && is_numeric($hasil)) {
                        $hasil = round((float) $hasil, (int) $rule['bulatkan']);
                    }
                    $variabel[$nama] = $hasil;
                } elseif ($tipe === 'number') {
                    $count = (int) ($rule['count'] ?? 1);
                    $arr = [];
                    for ($i = 0; $i < $count; $i++) {
                        $arr[] = $this->generateSingleNumber($rule);
                    }
                    $variabel[$nama] = $count > 1 ? $arr : $arr[0];
                } else {
                    $variabel[$nama] = $this->generateSingleNumber($rule);
                }
            }

            // Cek constraint jika ada
            $semuaTerpenuhi = true;
            foreach ($aturan as $nama => $rule) {
                if ($nama !== '__constraints')
                    continue;
                try {
                    $this->setSemuaVar($variabel);
                    $kondisi = (bool) $this->expressionLanguage->execute($rule['formula']);
                    if (!$kondisi) {
                        $semuaTerpenuhi = false;
                        break;
                    }
                } catch (\Exception $e) {
                    $semuaTerpenuhi = false;
                    break;
                }
            }

            if ($semuaTerpenuhi) {
                return $variabel;
            }
        }
        return $variabel;
    }

    private function generateSingleNumber(array $rule)
    {
        $min = (float) ($rule['min'] ?? 1);
        $max = (float) ($rule['max'] ?? 10);
        if ($max < $min)
            [$min, $max] = [$max, $min];

        $kelipatan = (float) ($rule['kelipatan'] ?? 0);
        $tipe = $rule['type'] ?? 'integer';

        $decimals = 0;
        if (isset($rule['decimals']) && is_numeric($rule['decimals']) && (int) $rule['decimals'] > 0) {
            $decimals = (int) $rule['decimals'];
        } elseif ($tipe === 'float') {
            $minStr = (string) ($rule['min'] ?? '');
            $maxStr = (string) ($rule['max'] ?? '');
            $minDec = str_contains($minStr, '.') ? strlen(substr(strrchr($minStr, '.'), 1)) : 0;
            $maxDec = str_contains($maxStr, '.') ? strlen(substr(strrchr($maxStr, '.'), 1)) : 0;
            $decimals = max(2, $minDec, $maxDec);
        }

        if ($kelipatan > 0) {
            $rangeCount = floor(($max - $min) / $kelipatan);
            $steps = random_int(0, (int) $rangeCount);
            $val = $min + ($steps * $kelipatan);
            return $decimals > 0 ? (float) number_format($val, $decimals, '.', '') : (int) round($val);
        }

        if ($decimals > 0) {
            $scale = pow(10, $decimals);
            $val = random_int((int) round($min * $scale), (int) round($max * $scale)) / $scale;
            return (float) number_format($val, $decimals, '.', '');
        }

        return random_int((int) round($min), (int) round($max));
    }

    public function uraiTemplatSoal(string $templat, array $variabel): string
    {
        $hasil = $templat;

        // 1. Substitusi string sederhana untuk variabel tunggal terlebih dahulu
        foreach ($variabel as $kunci => $nilai) {
            $stringNilai = is_array($nilai) ? '{' . implode(', ', $nilai) . '}' : (string) $nilai;
            $hasil = str_replace('{{' . $kunci . '}}', $stringNilai, $hasil);
            $hasil = str_replace('{' . $kunci . '}', $stringNilai, $hasil);
        }

        // 2. Evaluasi ekspresi matematika di dalam tanda kurung kurawal ganda {{ ... }} atau kurung siku ganda [[ ... ]]
        $hasil = preg_replace_callback('/\{\{(.+?)\}\}|\[\[(.+?)\]\]/', function ($matches) use ($variabel) {
            // $matches[1] = dalam {{ ... }}, $matches[2] = dalam [[ ... ]]
            $expr = !empty($matches[1]) ? $matches[1] : $matches[2];
            try {
                return $this->hitungRumus($expr, $variabel);
            } catch (\Exception $e) {
                // Jika gagal dievaluasi secara matematis, kembalikan seperti aslinya
                return $matches[0];
            }
        }, $hasil);

        return $hasil;
    }

    private function bersihkanRumus(string $rumus): string
    {
        $rumus = str_replace(['{', '}'], '', $rumus);
        $rumus = preg_replace('/\b([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s+\(/', '$1(', $rumus);
        $rumus = str_replace('**', '^', $rumus);
        return $rumus;
    }

    public function hitungRumus(string $rumus, array $variabel)
    {
        $rumus = $this->bersihkanRumus($rumus);
        $this->setSemuaVar($variabel);
        try {
            return $this->expressionLanguage->execute($rumus);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'empty datastructure')) {
                throw new \Exception("Sintaks rumus tidak valid atau fungsi tidak dikenali.");
            }
            throw $e;
        }
    }

    /**
     * Mencoba menghitung rumus dengan MathExecutor.
     * Jika gagal (misal untuk format aljabar murni seperti '2x + {a}'),
     * akan fallback ke uraiTemplatSoal.
     */
    public function hitungAtauUrai(string $rumus, array $variabel, ?string $format = null): string
    {
        try {
            $jawaban = $this->hitungRumus($rumus, $variabel);
            if ($jawaban instanceof UrutanJawaban) {
                return implode(', ', $jawaban->items);
            } elseif ($jawaban instanceof PecahanJawaban) {
                return (string) $jawaban;
            } elseif (is_numeric($jawaban)) {
                $floatVal = (float) $jawaban;
                if ($format === 'bulat') {
                    return (string) (int) round($floatVal);
                }
                if (floor($floatVal) == $floatVal) {
                    return (string) (int) $floatVal;
                }
                return (string) rtrim(rtrim(number_format($floatVal, self::MAKS_DESIMAL_PRESISI, '.', ''), '0'), '.');
            }
            return (string) $jawaban;
        } catch (\Exception $e) {
            return $this->uraiTemplatSoal($rumus, $variabel);
        }
    }

    public function normalizeLatex(string $latex): string
    {
        $str = str_replace(['\\ ', '\\quad', '\\qquad', ' '], '', $latex);
        $str = str_replace('\\times', '*', $str);
        $str = str_replace('\\div', '/', $str);
        $str = str_replace(['\\cdot', '\\bullet'], '*', $str);

        // Pecahan: \frac{a}{b} -> (a)/(b)
        $str = preg_replace('/\\\\frac\{([^}]+)\}\{([^}]+)\}/', '($1)/($2)', $str);

        // Akar: \sqrt{x} -> akar(x)
        $str = preg_replace('/\\\\sqrt\{([^}]+)\}/', 'akar($1)', $str);

        // Deteksi implicit multiplication untuk variabel: 2x -> 2*x, 5akar(2) -> 5*akar(2)
        $str = preg_replace('/(\d)([a-zA-Z])/', '$1*$2', $str);

        // Hapus sisa backslash agar mudah dievaluasi numerik
        $str = str_replace('\\', '', $str);

        return $str;
    }

    /**
     * Menghitung jumlah digit desimal signifikan dari sebuah nilai numerik.
     * Dipakai untuk menentukan presisi pembanding jawaban secara dinamis.
     */
    private function hitungJumlahDesimal(mixed $nilai): int
    {
        if (!is_numeric($nilai)) {
            return 0;
        }

        $str = rtrim(rtrim(number_format((float) $nilai, self::MAKS_DESIMAL_PRESISI, '.', ''), '0'), '.');
        $posTitik = strpos($str, '.');

        return $posTitik === false ? 0 : strlen($str) - $posTitik - 1;
    }

    public function jawabanNumerikSama(float|string|null $jawabanSiswa, mixed $jawabanBenar, ?int $skala = null): bool
    {
        if ($jawabanSiswa === null || $jawabanSiswa === '') {
            return false;
        }

        if (is_string($jawabanSiswa)) {
            $jawabanSiswa = $this->normalizeLatex($jawabanSiswa);

            // Hapus titik sebagai pemisah ribuan (format Indonesia)
            // Contoh: 80.000 -> 80000, 1.234.567 -> 1234567
            $jawabanSiswa = preg_replace('/(?<=\d)\.(?=\d{3}(?:\D|$))/', '', trim($jawabanSiswa));

            // Ganti koma dengan titik untuk desimal (format Indonesia -> format Internasional)
            // Contoh: 80,5 -> 80.5
            if (preg_match('/^-?\d+,\d+$/', $jawabanSiswa)) {
                $jawabanSiswa = str_replace(',', '.', $jawabanSiswa);
            }
        }

        if ($jawabanBenar instanceof PecahanJawaban) {
            $siswaStr = str_replace(' ', '', (string) $jawabanSiswa);
            $benarStr = (string) $jawabanBenar;
            return $siswaStr === $benarStr;
        }

        $isUrutan = $jawabanBenar instanceof UrutanJawaban;
        if ($isUrutan) {
            $jawabanBenar = $jawabanBenar->items;
        }

        if (is_array($jawabanBenar)) {
            if (!is_string($jawabanSiswa)) {
                return false;
            }
            $siswaStr = str_replace(['{', '}', '[', ']', ' '], '', $jawabanSiswa);
            $arrSiswa = array_filter(explode(',', $siswaStr), 'is_numeric');
            $arrSiswa = array_map('floatval', $arrSiswa);
            $arrBenar = array_map('floatval', $jawabanBenar);
            if (!$isUrutan) {
                sort($arrSiswa);
                sort($arrBenar);
            }
            return $arrSiswa === $arrBenar;
        }

        // Presisi mengikuti jumlah digit desimal jawaban benar itu sendiri (minimal
        // SKALA_PERBANDINGAN_JAWABAN), supaya soal dengan variabel/hasil desimal > 2 digit
        // tidak kehilangan presisi saat dinilai, tanpa mengubah perilaku soal-soal lama.
        $skala ??= max(self::SKALA_PERBANDINGAN_JAWABAN, $this->hitungJumlahDesimal($jawabanBenar));

        try {
            $siswa = is_numeric($jawabanSiswa) ? (float) $jawabanSiswa : $this->expressionLanguage->execute($jawabanSiswa);
        } catch (\Exception $e) {
            return false;
        }

        try {
            $benar = is_numeric($jawabanBenar) ? (float) $jawabanBenar : $this->expressionLanguage->execute($this->normalizeLatex((string) $jawabanBenar));
        } catch (\Exception $e) {
            $benar = (float) $jawabanBenar;
        }

        // 1. Perbandingan standar pembulatan 2 digit desimal
        if (round($siswa, self::SKALA_PERBANDINGAN_JAWABAN) === round($benar, self::SKALA_PERBANDINGAN_JAWABAN)) {
            return true;
        }

        // 2. Perbandingan standar (dengan skala desimal)
        if (round($siswa, $skala) === round($benar, $skala)) {
            return true;
        }

        // Toleransi desimal standar
        $toleransi = max(pow(10, -$skala) * 5, 1e-12 * max(1.0, abs($benar)));
        if (abs($siswa - $benar) <= $toleransi) {
            return true;
        }

        // ============================================================
        // TOLERANSI PEMBULATAN BILANGAN BULAT
        // Jika siswa menjawab bilangan bulat (contoh: 36),
        // dan jawaban benar adalah desimal (contoh: 36.14...),
        // cocokkan jika jawaban siswa = round(jawaban benar).
        // Ini penting untuk soal yang meminta "Tulis bilangan bulatnya saja".
        // ============================================================
        $siswaAdalahBulatSempurna = (floor($siswa) == $siswa);
        $benarBukanBulat = (floor($benar) != $benar);

        if ($siswaAdalahBulatSempurna && $benarBukanBulat) {
            if ((int) $siswa === (int) round($benar)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluasi khusus untuk format bilangan bulat (integer murni).
     * Siswa WAJIB memasukkan bilangan bulat (tanpa desimal/pecahan bukan bulat).
     */
    public function jawabanBulatSama(float|string|null $jawabanSiswa, mixed $jawabanBenar): bool
    {
        if ($jawabanSiswa === null || $jawabanSiswa === '') {
            return false;
        }

        $siswaStr = trim((string) $jawabanSiswa);
        $siswaStr = $this->normalizeLatex($siswaStr);

        // Hapus pemisah ribuan titik gaya Indonesia (misal 10.000 -> 10000)
        $siswaStr = preg_replace('/(?<=\d)\.(?=\d{3}(?:\D|$))/', '', $siswaStr);
        // Ganti koma desimal Indonesia menjadi titik
        $siswaStr = str_replace(',', '.', $siswaStr);

        // Jika terdapat titik desimal yang diikuti angka bukan 0 (misal 15.2 atau 15.5), tolak sebagai bilangan bulat
        if (preg_match('/\.\d*[1-9]/', $siswaStr)) {
            return false;
        }

        try {
            $siswa = is_numeric($siswaStr) ? (float) $siswaStr : (float) $this->expressionLanguage->execute($siswaStr);
        } catch (\Exception $e) {
            return false;
        }

        if (floor($siswa) != $siswa) {
            return false;
        }

        try {
            $benar = is_numeric($jawabanBenar) ? (float) $jawabanBenar : (float) $this->expressionLanguage->execute($this->normalizeLatex((string) $jawabanBenar));
        } catch (\Exception $e) {
            $benar = (float) $jawabanBenar;
        }

        return (int) round($siswa) === (int) round($benar);
    }

    /**
     * Evaluasi khusus untuk format bilangan desimal.
     * Menggunakan presisi desimal tanpa auto-rounding ke bilangan bulat.
     */
    public function jawabanDesimalSama(float|string|null $jawabanSiswa, mixed $jawabanBenar, ?int $skala = null): bool
    {
        if ($jawabanSiswa === null || $jawabanSiswa === '') {
            return false;
        }

        if (is_string($jawabanSiswa)) {
            $jawabanSiswa = $this->normalizeLatex($jawabanSiswa);
            $jawabanSiswa = preg_replace('/(?<=\d)\.(?=\d{3}(?:\D|$))/', '', trim($jawabanSiswa));
            if (preg_match('/^-?\d+,\d+$/', $jawabanSiswa)) {
                $jawabanSiswa = str_replace(',', '.', $jawabanSiswa);
            }
        }

        $skala ??= max(self::SKALA_PERBANDINGAN_JAWABAN, $this->hitungJumlahDesimal($jawabanBenar));

        try {
            $siswa = is_numeric($jawabanSiswa) ? (float) $jawabanSiswa : (float) $this->expressionLanguage->execute($jawabanSiswa);
        } catch (\Exception $e) {
            return false;
        }

        try {
            $benar = is_numeric($jawabanBenar) ? (float) $jawabanBenar : (float) $this->expressionLanguage->execute($this->normalizeLatex((string) $jawabanBenar));
        } catch (\Exception $e) {
            $benar = (float) $jawabanBenar;
        }

        // 1. Perbandingan standar pembulatan 2 digit desimal (konvensi standar matematika sekolah)
        // Contoh: kunci = 3.666887, siswa menjawab 3.67 (atau 3,67) -> BENAR
        if (round($siswa, self::SKALA_PERBANDINGAN_JAWABAN) === round($benar, self::SKALA_PERBANDINGAN_JAWABAN)) {
            return true;
        }

        // 2. Perbandingan presisi tinggi (jika kunci > 2 desimal dan siswa menulis lebih dari 2 desimal)
        if (round($siswa, $skala) === round($benar, $skala)) {
            return true;
        }

        $toleransi = max(pow(10, -($skala + 1)) * 5, 1e-12 * max(1.0, abs($benar)));
        if (abs($siswa - $benar) <= $toleransi) {
            return true;
        }

        return false;
    }

    // ==========================================
    // PENGECEKAN ALJABAR SIMBOLIK (BARU)
    // ==========================================
    public function jawabanAljabarSama(string $jawabanSiswa, string $jawabanBenar): bool
    {
        if (empty(trim($jawabanSiswa)))
            return false;

        $siswa = $this->normalizeLatex($jawabanSiswa);
        $benar = $this->normalizeLatex($jawabanBenar);

        // Ekstrak semua variabel karakter yang ada di kedua ekspresi
        preg_match_all('/[a-zA-Z]/', $siswa . $benar, $matches);
        $vars = array_unique($matches[0]);
        // Kecualikan nama fungsi built-in jika ada (s,i,n,c,o,t,l,g,p,e) - sangat sederhana
        $vars = array_diff($vars, ['s', 'i', 'n', 'c', 'o', 't', 'a', 'l', 'g', 'e', 'p']);

        $engine = new MathExecutor();
        // Daftarkan fungsi matematika kustom (akar, dsb.) pada engine lokal ini,
        // jika tidak, ekspresi hasil normalizeLatex seperti "5*akar(2)" akan gagal dievaluasi
        // sehingga jawaban bentuk akar/aljabar selalu dianggap salah.
        $engine->addFunction('akar', function ($n) {
            return sqrt((float) $n);
        });
        (new MathExpressionProvider())->registerFunctions($engine);
        // Substitusi Monte Carlo dengan angka prima untuk meminimalkan false positive
        $primes = [7, 11, 13, 17, 19, 23, 29, 31, 37, 41];

        // Uji di 5 titik acak (naik dari 3) supaya kemiripan aljabar palsu lebih sulit lolos
        for ($i = 0; $i < 5; $i++) {
            foreach ($vars as $index => $var) {
                $primeIndex = ($i + $index) % count($primes);
                $engine->setVar($var, $primes[$primeIndex]);
            }

            try {
                $hasilSiswa = (float) $engine->execute($siswa);
                $hasilBenar = (float) $engine->execute($benar);

                // Jika selisih terlalu besar di titik ini, berarti persamaannya berbeda
                if (abs($hasilSiswa - $hasilBenar) > 1e-9) {
                    return false;
                }
            } catch (\Exception $e) {
                // Sintaks error pada input siswa (misal kurang kurung)
                return false;
            }
        }

        return true;
    }

    public function cekJawaban(?string $format, mixed $jawabanSiswa, mixed $jawabanBenar): bool
    {
        // 1. Fast-path exact text match (ignoring whitespace and case)
        $siswaStr = strtolower(str_replace(' ', '', (string) $jawabanSiswa));
        $benarStr = strtolower(str_replace(' ', '', (string) $jawabanBenar));

        if ($siswaStr === $benarStr) {
            return true;
        }

        if ($format === 'bulat' || $format === 'bilangan_bulat') {
            return $this->jawabanBulatSama($jawabanSiswa, $jawabanBenar);
        }

        if ($format === 'desimal') {
            return $this->jawabanDesimalSama($jawabanSiswa, $jawabanBenar);
        }

        if ($format === 'aljabar' || $format === 'akar') {
            return $this->jawabanAljabarSama((string) $jawabanSiswa, (string) $jawabanBenar);
        }

        if ($format === 'koordinat') {
            $siswa = str_replace(' ', '', (string) $jawabanSiswa);
            $benar = str_replace(' ', '', (string) $jawabanBenar);
            return $siswa === $benar;
        }

        if ($format === 'himpunan') {
            $siswa = trim(str_replace(' ', '', (string) $jawabanSiswa), '{}');
            $benar = trim(str_replace(' ', '', (string) $jawabanBenar), '{}');
            $arrSiswa = array_filter(explode(',', $siswa), 'strlen');
            $arrBenar = array_filter(explode(',', $benar), 'strlen');

            if (count($arrSiswa) !== count($arrBenar)) {
                return false;
            }

            $matched = [];
            foreach ($arrBenar as $b) {
                $found = false;
                foreach ($arrSiswa as $idx => $s) {
                    if (isset($matched[$idx]))
                        continue;
                    if ($this->jawabanNumerikSama($s, $b)) {
                        $matched[$idx] = true;
                        $found = true;
                        break;
                    }
                }
                if (!$found)
                    return false;
            }
            return true;
        }

        return $this->jawabanNumerikSama($jawabanSiswa, $jawabanBenar);
    }

    public function renderPembahasan(?string $template, array $variabel): string
    {
        if (!$template)
            return '';

        // 1. Substitusi variabel langsung (contoh: {x})
        foreach ($variabel as $nama => $nilai) {
            $valStr = is_array($nilai) ? implode(', ', $nilai) : (string) $nilai;
            $template = str_replace('{' . $nama . '}', $valStr, $template);
        }

        // 2. Evaluasi fungsi matematika (contoh: {{ x + y }} atau [[ x + y ]])
        $template = preg_replace_callback('/\{\{(.+?)\}\}|\[\[(.+?)\]\]/', function ($matches) use ($variabel) {
            $expr = !empty($matches[1]) ? $matches[1] : $matches[2];
            try {
                $hasil = $this->hitungRumus($expr, $variabel);
                return (string) $hasil;
            } catch (\Exception $e) {
                return $matches[0];
            }
        }, $template);

        return $template;
    }

    /**
     * Mengkonversi nilai desimal menjadi string pecahan yang disederhanakan.
     * Contoh: 0.25 -> 1/4, 0.333333 -> 1/3
     */
    public function decimalToFraction(float $decimal): string
    {
        if (floor($decimal) == $decimal) {
            return (string) (int) $decimal;
        }

        $sign = $decimal < 0 ? '-' : '';
        $decimal = abs($decimal);

        $tolerance = 1.e-5;
        $h1 = 1;
        $h2 = 0;
        $k1 = 0;
        $k2 = 1;
        $b = $decimal;

        do {
            $a = floor($b);
            $aux = $h1;
            $h1 = $a * $h1 + $h2;
            $h2 = $aux;
            $aux = $k1;
            $k1 = $a * $k1 + $k2;
            $k2 = $aux;
            $b = $b - $a;
            if ($b < $tolerance)
                break; // hindari division by zero
            $b = 1 / $b;
        } while (abs($decimal - $h1 / $k1) > $decimal * $tolerance);

        return $sign . "$h1/$k1";
    }

    /**
     * Memformat teks soal untuk ditampilkan di HTML.
     * Mencegah <br> masuk ke dalam tag <svg> yang bisa merusak render.
     */
    public function formatHTML(?string $teks): string
    {
        if (empty($teks)) {
            return '';
        }

        // 1. Amankan teks dengan htmlspecialchars (opsional, jika ingin mencegah XSS, tapi kita butuh tag HTML valid seperti <b>, <i>, <svg>)
        // Karena sistem ini memperbolehkan guru memasukkan <svg>, kita biarkan tag HTML.
        // Jika butuh keamanan, gunakan Purifier. Untuk sekarang, kita trust input guru.

        // 2. Hilangkan enter/newline di DALAM tag <svg> agar tidak jadi <br>
        $teks = preg_replace_callback('/<svg[\s\S]*?<\/svg>/i', function ($matches) {
            return str_replace(["\r", "\n"], '', $matches[0]);
        }, $teks);

        // 3. Konversi newline sisanya menjadi <br>
        return nl2br($teks);
    }
}
