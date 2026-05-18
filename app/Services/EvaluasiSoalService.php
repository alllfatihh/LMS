<?php

namespace App\Services;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class EvaluasiSoalService
{
    private ExpressionLanguage $expressionLanguage;

    /** Digit desimal saat membandingkan jawaban siswa vs nilai benar. */
    public const SKALA_PERBANDINGAN_JAWABAN = 6;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage;
    }

    /**
     * @param  array<string, array{min?: int, max?: int}>  $aturan
     * @return array<string, int>
     */
    public function hasilkanVariabel(array $aturan): array
    {
        $variabel = [];
        foreach ($aturan as $nama => $rule) {
            $min = (int) ($rule['min'] ?? 1);
            $max = (int) ($rule['max'] ?? 10);
            if ($max < $min) {
                [$min, $max] = [$max, $min];
            }
            $variabel[$nama] = random_int($min, $max);
        }

        return $variabel;
    }

    /**
     * @param  array<string, int|float|string>  $variabel
     */
    public function uraiTemplatSoal(string $templat, array $variabel): string
    {
        $hasil = $templat;
        foreach ($variabel as $kunci => $nilai) {
            $hasil = str_replace('{{'.$kunci.'}}', (string) $nilai, $hasil);
            $hasil = str_replace('{'.$kunci.'}', (string) $nilai, $hasil);
        }

        return $hasil;
    }

    /**
     * @param  array<string, int|float|string>  $variabel
     */
    public function hitungRumus(string $rumus, array $variabel)
    {
        return $this->expressionLanguage->evaluate($rumus, $variabel);
    }

    public function jawabanNumerikSama(float|string|null $jawabanSiswa, mixed $jawabanBenar, ?int $skala = null): bool
    {
        if ($jawabanSiswa === null || $jawabanSiswa === '') {
            return false;
        }

        if (! is_numeric($jawabanSiswa) || ! is_numeric($jawabanBenar)) {
            return false;
        }

        $skala ??= self::SKALA_PERBANDINGAN_JAWABAN;

        $siswa = (float) $jawabanSiswa;
        $benar = (float) $jawabanBenar;

        if (extension_loaded('bcmath')) {
            $s = number_format($siswa, $skala, '.', '');
            $c = number_format($benar, $skala, '.', '');

            if (bccomp($s, $c, $skala) === 0) {
                return true;
            }
        }

        if (round($siswa, $skala) === round($benar, $skala)) {
            return true;
        }

        $toleransi = max(pow(10, -$skala) * 5, 1e-12 * max(1.0, abs($benar)));

        return abs($siswa - $benar) <= $toleransi;
    }
}
