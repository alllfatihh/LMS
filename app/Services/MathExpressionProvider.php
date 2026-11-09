<?php

namespace App\Services;

use NXP\MathExecutor;

class MathExpressionProvider
{
        /**
         * Sumber kebenaran tunggal daftar fungsi untuk dikonsumsi prompt AI (AiGuruController).
         * Wajib diperbarui setiap kali ada fungsi baru ditambah/diubah di registerFunctions(),
         * supaya AI tidak pernah merekomendasikan fungsi yang sudah tidak ada atau tidak tahu fungsi baru.
         */
        public function daftarUntukPrompt(): array
        {
                return [
                        'Dasar' => [
                                'akar(x)', 'akar_kubik(x)', 'akar_pangkat(x, k)', 'abs(x)', 'mutlak(x)',
                                'faktorial(n)', 'pangkat(a, n)', 'log(x)', 'ln(x)', 'logaritma(n, basis)',
                                'floor_val(x)', 'ceil_val(x)', 'pembulatan(x, n)', 'sisa_bagi(a, b)', 'persen(a, b)',
                        ],
                        'Trigonometri (sudut istimewa saja, dalam derajat)' => [
                                'sin(derajat)', 'cos(derajat)', 'tan(derajat)',
                        ],
                        'Teori Bilangan & Peluang' => [
                                'fpb(a, b)', 'kpk(a, b)', 'kombinasi(n, r)', 'permutasi(n, r)',
                                'peluang(kejadian, total)', 'frekuensi_harapan(peluang, n)',
                        ],
                        'Aritmetika Sosial & Rasio' => [
                                'diskon(harga, persen)', 'bunga_tunggal(modal, persen, bulan)', 'rasio(a, b)',
                                'skala(jarak_peta, skala)', 'perbandingan_senilai(a1, b1, a2)', 'perbandingan_berbalik(a1, b1, a2)',
                        ],
                        'Statistika' => [
                                'rata_rata(a, b, ...)', 'median(a, b, ...)', 'modus(a, b, ...)', 'jumlah(a, b, ...)',
                                'nilai_maks(a, b, ...)', 'nilai_min(a, b, ...)', 'jangkauan(a, b, ...)',
                                'kuartil(a, b, ..., k)', 'jangkauan_interkuartil(a, b, ...)',
                                'variansi(a, b, ...)', 'simpangan_baku(a, b, ...)', 'simpangan_rata_rata(a, b, ...)',
                        ],
                        'Himpunan' => [
                                'himpunan(a, b, ...)', 'jumlah_anggota(a, b, ...)', 'irisan(A, B)', 'gabungan(A, B)', 'selisih(A, B)',
                        ],
                        'Koordinat, Garis & Barisan' => [
                                'jarak(x1, y1, x2, y2)', 'titik_tengah(x1, y1, x2, y2)', 'gradien(x1, y1, x2, y2)',
                                'gradien_dari_persamaan(a, b)', 'gradien_tegak_lurus(m)', 'gradien_sejajar(m)', 'persamaan_garis(m, c)',
                                'suku_n_aritmetika(a, b, n)', 'jumlah_n_aritmetika(a, b, n)',
                                'suku_n_geometri(a, r, n)', 'jumlah_n_geometri(a, r, n)',
                        ],
                        'Geometri Datar (Kelas 7)' => [
                                'sudut_pelurus(a)', 'sudut_penyiku(a)', 'sudut_bertolak(a)', 'sudut_segitiga(a, b)',
                                'keliling_persegi(s)', 'luas_persegi(s)', 'keliling_persegi_panjang(p, l)', 'luas_persegi_panjang(p, l)',
                                'luas_segitiga(alas, tinggi)', 'luas_trapesium(a, b, tinggi)', 'luas_jajar_genjang(a, t)',
                                'luas_belah_ketupat(d1, d2)', 'luas_layang_layang(d1, d2)',
                                'luas_lingkaran(r)', 'keliling_lingkaran(r)', 'panjang_busur(r, sudut)', 'luas_juring(r, sudut)',
                                'hipotenusa(a, b)', 'sisi_siku_siku(c, a)',
                        ],
                        'Bilangan Berpangkat & Bentuk Akar (Kelas 8)' => [
                                'bentuk_baku(a, n)', 'sederhanakan_akar(n)',
                        ],
                        'Aljabar & Persamaan (Kelas 8-9)' => [
                                'substitusi_linear(a, x, b)', 'fungsi_linear(a, x, b)', 'plsv(a, b)',
                                'diskriminan(a, b, c)', 'sumbu_simetri(a, b)', 'akar_kuadrat_1(a, b, c)', 'akar_kuadrat_2(a, b, c)',
                                'jumlah_akar(a, b)', 'hasil_kali_akar(a, c)', 'nilai_fungsi_kuadrat(a, x, b, c)', 'titik_puncak(a, b, c)',
                                'faktorisasi_kuadrat(p, q)', 'sisi_sebangun(a, b, c)',
                        ],
                        'Transformasi Geometri (Kelas 9)' => [
                                'translasi(x, y, a, b)', 'dilatasi(x, y, k)',
                                'refleksi_x(x, y)', 'refleksi_y(x, y)', 'refleksi_y_sama_dengan_x(x, y)', 'refleksi_titik_asal(x, y)',
                                'rotasi_90(x, y)', 'rotasi_180(x, y)', 'rotasi_270(x, y)', 'format_titik(x, y)',
                        ],
                        'Bangun Ruang' => [
                                'volume_kubus(s)', 'luas_permukaan_kubus(s)', 'volume_balok(p, l, t)', 'luas_permukaan_balok(p, l, t)',
                                'volume_prisma(luas_alas, t)', 'luas_permukaan_prisma(luas_alas, keliling_alas, t)',
                                'volume_limas(luas_alas, t)', 'luas_permukaan_limas(luas_alas, keliling_alas, tinggi_miring)',
                                'volume_tabung(r, t)', 'luas_permukaan_tabung(r, t)', 'volume_kerucut(r, t)',
                                'luas_permukaan_kerucut(r, s)', 'luas_selimut_kerucut(r, s)', 'garis_pelukis_kerucut(r, t)',
                                'volume_bola(r)', 'luas_permukaan_bola(r)', 'volume_setengah_bola(r)',
                        ],
                        'Format Jawaban Khusus' => [
                                'pecahan(pembilang, penyebut)', 'urutan(a, b, ...)', 'urutkan_naik(a, b, ...)', 'urutkan_turun(a, b, ...)',
                        ],
                ];
        }

        public function registerFunctions(MathExecutor $el): void
        {
                // Format pasangan koordinat "(x, y)" sekaligus menormalkan negatif-nol (-0 -> 0),
                // supaya perbandingan string jawaban koordinat tidak gagal hanya karena tanda "-0".
                $fmtTitik = function ($x, $y) {
                        $x = (float) $x + 0.0;
                        $y = (float) $y + 0.0;
                        return '(' . $x . ', ' . $y . ')';
                };
                // ==========================================
                // EKSPANSI FUNGSI MATEMATIKA LANJUTAN (BARU)
                // ==========================================
                $el->addFunction('sin', function ($n) {
                        return sin(deg2rad((float) $n));
                });
                $el->addFunction('cos', function ($n) {
                        return cos(deg2rad((float) $n));
                });
                $el->addFunction('tan', function ($n) {
                        return tan(deg2rad((float) $n));
                });
                $el->addFunction('log', function ($n) {
                        return log10((float) $n);
                });
                $el->addFunction('ln', function ($n) {
                        return log((float) $n);
                });
                $el->addFunction('abs', function ($n) {
                        return abs((float) $n);
                });
                $el->addFunction('mutlak', function ($n) {
                        return abs((float) $n);
                });
                $el->addFunction('faktorial', function ($n) {
                        $n = (int) $n;
                        if ($n < 0) {
                                return 0;
                        }
                        if ($n == 0) {
                                return 1;
                        }
                        $res = 1;
                        for ($i = 1; $i <= $n; $i++) {
                                $res *= $i;
                        }

                        return $res;
                });
                // --- Aljabar & Lanjutan ---
                $el->addFunction('akar_pangkat', function ($n, $k) {
                        $n = (float) $n;
                        $k = (float) $k;
                        if ($n < 0) {
                                // Akar pangkat ganjil dari bilangan negatif valid secara real (mis. akar pangkat 3 dari -8 = -2).
                                // Akar pangkat genap dari bilangan negatif tidak real, kembalikan 0 (konsisten dgn fungsi lain di file ini).
                                $kBulat = (int) round($k);
                                if (abs($k - $kBulat) < 1e-9 && $kBulat % 2 !== 0) {
                                        return -pow(abs($n), 1 / $k);
                                }
                                return 0;
                        }
                        return pow($n, 1 / $k);
                });
                $el->addFunction('akar_kubik', function ($n) {
                        $n = (float) $n;
                        return $n < 0 ? -pow(-$n, 1 / 3) : pow($n, 1 / 3);
                });
                $el->addFunction('logaritma', function ($n, $basis) {
                        $n = (float) $n;
                        $basis = (float) $basis;
                        if ($n <= 0 || $basis <= 0 || $basis == 1) {
                                return 0;
                        }
                        return log($n) / log($basis);
                });
                // --- Peluang & Bilangan ---
                $el->addFunction('kombinasi', function ($n, $r) {
                        $n = (int) $n;
                        $r = (int) $r;
                        if ($r > $n || $n < 0 || $r < 0) {
                                return 0;
                        }
                        $res = 1;
                        for ($i = 1; $i <= $r; $i++) {
                                $res = $res * ($n - $i + 1) / $i;
                        }

                        return $res;
                });
                $el->addFunction('permutasi', function ($n, $r) {
                        $n = (int) $n;
                        $r = (int) $r;
                        if ($r > $n || $n < 0 || $r < 0) {
                                return 0;
                        }
                        $res = 1;
                        for ($i = $n - $r + 1; $i <= $n; $i++) {
                                $res *= $i;
                        }

                        return $res;
                });
                $el->addFunction('peluang', function ($k, $t) {
                        return (float) $k / (float) $t;
                });
                $el->addFunction('frekuensi_harapan', function ($p, $n) {
                        return (float) $p * (float) $n;
                });
                $el->addFunction('fpb', function ($a, $b) {
                        $a = abs((int) $a);
                        $b = abs((int) $b);
                        while ($b != 0) {
                                $t = $b;
                                $b = $a % $b;
                                $a = $t;
                        }

                        return $a;
                });
                $el->addFunction('kpk', function ($a, $b) {
                        $a = abs((int) $a);
                        $b = abs((int) $b);
                        if ($a == 0 || $b == 0) {
                                return 0;
                        }
                        $fpb = function ($x, $y) {
                                while ($y != 0) {
                                        $t = $y;
                                        $y = $x % $y;
                                        $x = $t;
                                }

                                return $x;
                        };

                        return ($a * $b) / $fpb($a, $b);
                });
                $el->addFunction('diskon', function ($harga, $persen) {
                        return (float) $harga - ((float) $harga * ((float) $persen / 100));
                });
                $el->addFunction('bunga_tunggal', function ($modal, $persen_bunga, $bulan) {
                        return (float) $modal * ((float) $persen_bunga / 100) * ((float) $bulan / 12);
                });
                $el->addFunction('hipotenusa', function ($a, $b) {
                        return sqrt(pow((float) $a, 2) + pow((float) $b, 2));
                });
                $el->addFunction('sisi_siku_siku', function ($c, $a) {
                        $val = pow((float) $c, 2) - pow((float) $a, 2);

                        return $val >= 0 ? sqrt($val) : 0;
                });
                $el->addFunction('sisa_bagi', function ($a, $b) {
                        return (int) $a % (int) $b;
                });
                $el->addFunction('persen', function ($a, $b) {
                        return ((float) $a / (float) $b) * 100;
                });
                // --- Statistika ---
                $el->addFunction('rata_rata', function (...$args) {
                        if (empty($args)) {
                                return 0;
                        }

                        return array_sum($args) / count($args);
                });
                $el->addFunction('median', function (...$args) {
                        if (empty($args)) {
                                return 0;
                        }
                        sort($args);
                        $c = count($args);
                        $m = floor($c / 2);

                        return ($c % 2 == 0) ? ($args[$m - 1] + $args[$m]) / 2 : $args[$m];
                });
                $el->addFunction('modus', function (...$args) {
                        if (empty($args)) {
                                return 0;
                        }
                        $strArgs = array_map('strval', $args);
                        $v = array_count_values($strArgs);
                        arsort($v);

                        return (float) array_key_first($v);
                });
                $el->addFunction('jumlah', function (...$args) {
                        return array_sum($args);
                });
                $el->addFunction('nilai_maks', function (...$args) {
                        return empty($args) ? 0 : max($args);
                });
                $el->addFunction('nilai_min', function (...$args) {
                        return empty($args) ? 0 : min($args);
                });
                $el->addFunction('jangkauan', function (...$args) {
                        if (empty($args)) {
                                return 0;
                        }

                        return max($args) - min($args);
                });
                $el->addFunction('variansi', function (...$args) {
                        $c = count($args);
                        if ($c <= 1) {
                                return 0;
                        }
                        $mean = array_sum($args) / $c;
                        $sum = 0;
                        foreach ($args as $a) {
                                $sum += pow($a - $mean, 2);
                        }

                        return $sum / ($c - 1);
                });
                $el->addFunction('simpangan_rata_rata', function (...$args) {
                        $c = count($args);
                        if ($c === 0) {
                                return 0;
                        }
                        $mean = array_sum($args) / $c;
                        $sum = 0;
                        foreach ($args as $a) {
                                $sum += abs($a - $mean);
                        }

                        return $sum / $c;
                });
                $el->addFunction('simpangan_baku', function (...$args) {
                        $c = count($args);
                        if ($c <= 1) {
                                return 0;
                        }
                        $mean = array_sum($args) / $c;
                        $sum = 0;
                        foreach ($args as $a) {
                                $sum += pow($a - $mean, 2);
                        }

                        return sqrt($sum / ($c - 1));
                });
                $calcKuartil = function ($data, $k) {
                        sort($data);
                        $c = count($data);
                        if ($c == 0) {
                                return 0;
                        }
                        if ($c == 1) {
                                return $data[0];
                        }
                        $median = function ($arr) {
                                $n = count($arr);
                                if ($n == 0) {
                                        return 0;
                                }
                                $m = floor($n / 2);

                                return ($n % 2 == 0) ? ($arr[$m - 1] + $arr[$m]) / 2 : $arr[$m];
                        };
                        if ($k == 2) {
                                return $median($data);
                        }
                        $mid = (int) floor($c / 2);
                        $lower = array_slice($data, 0, $mid);
                        $upper = array_slice($data, $c % 2 == 0 ? $mid : $mid + 1);
                        if ($k == 1) {
                                return $median($lower);
                        }
                        if ($k == 3) {
                                return $median($upper);
                        }

                        return 0;
                };
                $el->addFunction('kuartil', function (...$args) use ($calcKuartil) {
                        $k = (int) array_pop($args);

                        return $calcKuartil($args, $k);
                });
                $el->addFunction('jangkauan_interkuartil', function (...$args) use ($calcKuartil) {
                        $q1 = $calcKuartil($args, 1);
                        $q3 = $calcKuartil($args, 3);

                        return $q3 - $q1;
                });
                // --- Himpunan ---
                $el->addFunction('himpunan', function (...$args) {
                        return '{' . implode(', ', $args) . '}';
                });
                $el->addFunction('jumlah_anggota', function (...$args) {
                        return count(array_unique($args));
                });
                $parseSet = function ($str) {
                        if (!is_string($str)) {
                                return [$str];
                        }
                        $str = trim($str, '{}');
                        $arr = explode(',', $str);

                        return array_map('trim', $arr);
                };
                $el->addFunction('irisan', function ($A, $B) use ($parseSet) {
                        $arrA = $parseSet($A);
                        $arrB = $parseSet($B);
                        $res = array_intersect($arrA, $arrB);
                        sort($res);

                        return '{' . implode(', ', $res) . '}';
                });
                $el->addFunction('gabungan', function ($A, $B) use ($parseSet) {
                        $arrA = $parseSet($A);
                        $arrB = $parseSet($B);
                        $res = array_unique(array_merge($arrA, $arrB));
                        sort($res);

                        return '{' . implode(', ', $res) . '}';
                });
                $el->addFunction('selisih', function ($A, $B) use ($parseSet) {
                        $arrA = $parseSet($A);
                        $arrB = $parseSet($B);
                        $res = array_diff($arrA, $arrB);
                        sort($res);

                        return '{' . implode(', ', $res) . '}';
                });
                // --- Koordinat & Garis ---
                $el->addFunction('jarak', function ($x1, $y1, $x2, $y2) {
                        return sqrt(pow((float) $x2 - (float) $x1, 2) + pow((float) $y2 - (float) $y1, 2));
                });
                $el->addFunction('titik_tengah', function ($x1, $y1, $x2, $y2) {
                        return '(' . (((float) $x1 + (float) $x2) / 2) . ', ' . (((float) $y1 + (float) $y2) / 2) . ')';
                });
                $el->addFunction('gradien', function ($x1, $y1, $x2, $y2) {
                        $dx = (float) $x2 - (float) $x1;

                        return $dx == 0 ? 0 : ((float) $y2 - (float) $y1) / $dx;
                });
                $el->addFunction('suku_n_aritmetika', function ($a, $b, $n) {
                        return (float) $a + ((float) $n - 1) * (float) $b;
                });
                $el->addFunction('jumlah_n_aritmetika', function ($a, $b, $n) {
                        return ((float) $n / 2) * (2 * (float) $a + ((float) $n - 1) * (float) $b);
                });
                $el->addFunction('suku_n_geometri', function ($a, $r, $n) {
                        return (float) $a * pow((float) $r, (float) $n - 1);
                });
                $el->addFunction('jumlah_n_geometri', function ($a, $r, $n) {
                        $r = (float) $r;
                        if ($r == 1) {
                                return (float) $a * (float) $n;
                        }

                        return (float) $a * (pow($r, (float) $n) - 1) / ($r - 1);
                });
                $el->addFunction('luas_lingkaran', function ($r) {
                        return pi() * pow((float) $r, 2);
                });
                $el->addFunction('keliling_lingkaran', function ($r) {
                        return 2 * pi() * (float) $r;
                });
                $el->addFunction('luas_segitiga', function ($alas, $tinggi) {
                        return 0.5 * (float) $alas * (float) $tinggi;
                });
                $el->addFunction('luas_trapesium', function ($a, $b, $tinggi) {
                        return 0.5 * ((float) $a + (float) $b) * (float) $tinggi;
                });
                $el->addFunction('volume_tabung', function ($r, $t) {
                        return pi() * pow((float) $r, 2) * (float) $t;
                });
                $el->addFunction('luas_permukaan_tabung', function ($r, $t) {
                        return 2 * pi() * (float) $r * ((float) $r + (float) $t);
                });
                $el->addFunction('volume_kerucut', function ($r, $t) {
                        return (1 / 3) * pi() * pow((float) $r, 2) * (float) $t;
                });
                $el->addFunction('volume_bola', function ($r) {
                        return (4 / 3) * pi() * pow((float) $r, 3);
                });
                // --- Transformasi & Kuadrat ---
                $el->addFunction('diskriminan', function ($a, $b, $c) {
                        return pow((float) $b, 2) - 4 * (float) $a * (float) $c;
                });
                $el->addFunction('sumbu_simetri', function ($a, $b) {
                        return (float) $a == 0 ? 0 : -(float) $b / (2 * (float) $a);
                });
                $el->addFunction('translasi', function ($x, $y, $a, $b) use ($fmtTitik) {
                        return $fmtTitik((float) $x + (float) $a, (float) $y + (float) $b);
                });
                $el->addFunction('rotasi_90', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik(-(float) $y, (float) $x);
                });
                $el->addFunction('dilatasi', function ($x, $y, $k) use ($fmtTitik) {
                        return $fmtTitik((float) $x * (float) $k, (float) $y * (float) $k);
                });
                $el->addFunction('refleksi_x', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik((float) $x, -(float) $y);
                });
                $el->addFunction('refleksi_y', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik(-(float) $x, (float) $y);
                });
                $el->addFunction('refleksi_y_sama_dengan_x', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik((float) $y, (float) $x);
                });
                // --- Format Jawaban ---
                $el->addFunction('format_titik', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik($x, $y);
                });
                $el->addFunction('faktorisasi_kuadrat', function ($p, $q) {
                        $signP = $p >= 0 ? '+' : '';
                        $signQ = $q >= 0 ? '+' : '';

                        return "(x{$signP}{$p})(x{$signQ}{$q})";
                });
                // --- Urutan Jawaban ---
                $el->addFunction('urutkan_naik', function (...$args) {
                        sort($args);

                        return implode(', ', $args);
                });
                $el->addFunction('urutkan_turun', function (...$args) {
                        rsort($args);

                        return implode(', ', $args);
                });
                // =============================================================
                // KELAS 7: Bilangan, Rasio, Perbandingan
                // =============================================================
                $el->addFunction('pangkat', function ($a, $n) {
                        return pow((float) $a, (float) $n);
                });
                $el->addFunction('floor_val', function ($x) {
                        return floor((float) $x);
                });
                $el->addFunction('ceil_val', function ($x) {
                        return ceil((float) $x);
                });
                $el->addFunction('pembulatan', function ($x, $n) {
                        return round((float) $x, (int) $n);
                });
                $el->addFunction('rasio', function ($a, $b) {
                        $a = (int) $a;
                        $b = (int) $b;
                        if ($b == 0) {
                                return "{$a}:0";
                        }
                        $fpb_fn = function ($x, $y) {
                                while ($y != 0) {
                                        $t = $y;
                                        $y = $x % $y;
                                        $x = $t;
                                }

                                return $x;
                        };
                        $f = $fpb_fn(abs($a), abs($b));

                        return ($a / $f) . ':' . ($b / $f);
                });
                $el->addFunction('skala', function ($jarak_peta, $skala) {
                        return (float) $jarak_peta * (float) $skala;
                });
                $el->addFunction('perbandingan_senilai', function ($a1, $b1, $a2) {
                        return (float) $a1 == 0 ? 0 : ((float) $b1 * (float) $a2) / (float) $a1;
                });
                $el->addFunction('perbandingan_berbalik', function ($a1, $b1, $a2) {
                        return (float) $a2 == 0 ? 0 : ((float) $a1 * (float) $b1) / (float) $a2;
                });
                // --- Kelas 7: Aljabar Dasar ---
                $el->addFunction('substitusi_linear', function ($a, $x, $b) {
                        return (float) $a * (float) $x + (float) $b;
                });
                // =============================================================
                // KELAS 7: Kesebangunan & Geometri Datar (Sudut & Bangun)
                // =============================================================
                $el->addFunction('sudut_pelurus', function ($a) {
                        return 180 - (float) $a;
                });
                $el->addFunction('sudut_penyiku', function ($a) {
                        return 90 - (float) $a;
                });
                $el->addFunction('sudut_bertolak', function ($a) {
                        return (float) $a;
                });
                $el->addFunction('sudut_segitiga', function ($a, $b) {
                        return 180 - ((float) $a + (float) $b);
                });
                $el->addFunction('keliling_persegi', function ($s) {
                        return 4 * (float) $s;
                });
                $el->addFunction('luas_persegi', function ($s) {
                        return pow((float) $s, 2);
                });
                $el->addFunction('keliling_persegi_panjang', function ($p, $l) {
                        return 2 * ((float) $p + (float) $l);
                });
                $el->addFunction('luas_persegi_panjang', function ($p, $l) {
                        return (float) $p * (float) $l;
                });
                $el->addFunction('luas_jajar_genjang', function ($a, $t) {
                        return (float) $a * (float) $t;
                });
                $el->addFunction('luas_belah_ketupat', function ($d1, $d2) {
                        return 0.5 * (float) $d1 * (float) $d2;
                });
                $el->addFunction('luas_layang_layang', function ($d1, $d2) {
                        return 0.5 * (float) $d1 * (float) $d2;
                });
                $el->addFunction('panjang_busur', function ($r, $sudut) {
                        return ((float) $sudut / 360) * 2 * M_PI * (float) $r;
                });
                $el->addFunction('luas_juring', function ($r, $sudut) {
                        return ((float) $sudut / 360) * M_PI * pow((float) $r, 2);
                });
                // =============================================================
                // KELAS 8: Bilangan Berpangkat & Bentuk Akar
                // =============================================================
                $el->addFunction('bentuk_baku', function ($a, $n) {
                        return (float) $a * pow(10, (int) $n);
                });
                $el->addFunction('sederhanakan_akar', function ($n) {
                        $n = (int) $n;
                        if ($n <= 0) {
                                return '0';
                        }
                        $koef = 1;
                        $sisa = $n;
                        for ($i = 2; $i * $i <= $sisa; $i++) {
                                while ($sisa % ($i * $i) == 0) {
                                        $koef *= $i;
                                        $sisa /= ($i * $i);
                                }
                        }
                        if ($sisa == 1) {
                                return (string) $koef;
                        }
                        if ($koef == 1) {
                                return "\\sqrt{{$sisa}}";
                        }

                        return "{$koef}\\sqrt{{$sisa}}";
                });
                // --- Kelas 8: PLSV ---
                $el->addFunction('plsv', function ($a, $b) {
                        // ax + b = 0 → x = -b/a
                        return (float) $a == 0 ? 0 : -(float) $b / (float) $a;
                });
                // --- Kelas 8: Persamaan Garis Lurus ---
                $el->addFunction('persamaan_garis', function ($m, $c) {
                        $m = (float) $m;
                        $c = (float) $c;
                        $mStr = ($m == 1) ? '' : (($m == -1) ? '-' : $m);
                        if ($c == 0) {
                                return "y = {$mStr}x";
                        }
                        $sign = $c > 0 ? '+' : '-';
                        $absC = abs($c);

                        return "y = {$mStr}x {$sign} {$absC}";
                });
                $el->addFunction('gradien_dari_persamaan', function ($a, $b) {
                        // ax + by = c → m = -a/b
                        return (float) $b == 0 ? 0 : -(float) $a / (float) $b;
                });
                $el->addFunction('gradien_tegak_lurus', function ($m) {
                        return (float) $m == 0 ? 0 : -1 / (float) $m;
                });
                $el->addFunction('gradien_sejajar', function ($m) {
                        return (float) $m;
                });
                // --- Kelas 8: Relasi & Fungsi ---
                $el->addFunction('fungsi_linear', function ($a, $x, $b) {
                        return (float) $a * (float) $x + (float) $b;
                });
                // =============================================================
                // KELAS 9: Persamaan & Fungsi Kuadrat
                // =============================================================
                $el->addFunction('akar_kuadrat_1', function ($a, $b, $c) {
                        $a = (float) $a;
                        $b = (float) $b;
                        $c = (float) $c;
                        $D = $b * $b - 4 * $a * $c;
                        if ($D < 0 || $a == 0) {
                                return 0;
                        }

                        return (-$b + sqrt($D)) / (2 * $a);
                });
                $el->addFunction('akar_kuadrat_2', function ($a, $b, $c) {
                        $a = (float) $a;
                        $b = (float) $b;
                        $c = (float) $c;
                        $D = $b * $b - 4 * $a * $c;
                        if ($D < 0 || $a == 0) {
                                return 0;
                        }

                        return (-$b - sqrt($D)) / (2 * $a);
                });
                $el->addFunction('jumlah_akar', function ($a, $b) {
                        // Vieta: x1 + x2 = -b/a
                        return (float) $a == 0 ? 0 : -(float) $b / (float) $a;
                });
                $el->addFunction('hasil_kali_akar', function ($a, $c) {
                        // Vieta: x1 * x2 = c/a
                        return (float) $a == 0 ? 0 : (float) $c / (float) $a;
                });
                $el->addFunction('nilai_fungsi_kuadrat', function ($a, $x, $b, $c) {
                        // f(x) = ax² + bx + c
                        return (float) $a * pow((float) $x, 2) + (float) $b * (float) $x + (float) $c;
                });
                $el->addFunction('titik_puncak', function ($a, $b, $c) use ($fmtTitik) {
                        $a = (float) $a;
                        $b = (float) $b;
                        $c = (float) $c;
                        if ($a == 0) {
                                return '(0, 0)';
                        }
                        $xp = -$b / (2 * $a);
                        $yp = $c - ($b * $b) / (4 * $a);

                        return $fmtTitik($xp, $yp);
                });
                // =============================================================
                // KELAS 9: Transformasi (Tambahan)
                // =============================================================
                $el->addFunction('rotasi_180', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik(-(float) $x, -(float) $y);
                });
                $el->addFunction('rotasi_270', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik((float) $y, -(float) $x);
                });
                $el->addFunction('refleksi_titik_asal', function ($x, $y) use ($fmtTitik) {
                        return $fmtTitik(-(float) $x, -(float) $y);
                });
                // =============================================================
                // KELAS 9: Kekongruenan & Kesebangunan
                // =============================================================
                $el->addFunction('sisi_sebangun', function ($a, $b, $c) {
                        // a/b = x/c → x = (a*c)/b
                        return (float) $b == 0 ? 0 : ((float) $a * (float) $c) / (float) $b;
                });
                // =============================================================
                // KELAS 9: Bangun Ruang Sisi Lengkung (Tambahan)
                // =============================================================
                $el->addFunction('luas_permukaan_kerucut', function ($r, $s) {
                        return M_PI * (float) $r * ((float) $r + (float) $s);
                });
                $el->addFunction('luas_permukaan_bola', function ($r) {
                        return 4 * M_PI * pow((float) $r, 2);
                });
                $el->addFunction('garis_pelukis_kerucut', function ($r, $t) {
                        return sqrt(pow((float) $r, 2) + pow((float) $t, 2));
                });
                $el->addFunction('luas_selimut_kerucut', function ($r, $s) {
                        return M_PI * (float) $r * (float) $s;
                });
                $el->addFunction('volume_setengah_bola', function ($r) {
                        return (2.0 / 3.0) * M_PI * pow((float) $r, 3);
                });
                // =============================================================
                // Bangun Ruang Sisi Datar
                // =============================================================
                $el->addFunction('volume_kubus', function ($s) {
                        return pow((float) $s, 3);
                });
                $el->addFunction('luas_permukaan_kubus', function ($s) {
                        return 6 * pow((float) $s, 2);
                });
                $el->addFunction('volume_balok', function ($p, $l, $t) {
                        return (float) $p * (float) $l * (float) $t;
                });
                $el->addFunction('luas_permukaan_balok', function ($p, $l, $t) {
                        $p = (float) $p;
                        $l = (float) $l;
                        $t = (float) $t;

                        return 2 * ($p * $l + $p * $t + $l * $t);
                });
                $el->addFunction('volume_prisma', function ($luas_alas, $t) {
                        return (float) $luas_alas * (float) $t;
                });
                $el->addFunction('luas_permukaan_prisma', function ($luas_alas, $keliling_alas, $t) {
                        return (2 * (float) $luas_alas) + ((float) $keliling_alas * (float) $t);
                });
                $el->addFunction('volume_limas', function ($luas_alas, $t) {
                        return (1.0 / 3.0) * (float) $luas_alas * (float) $t;
                });
                $el->addFunction('luas_permukaan_limas', function ($luas_alas, $keliling_alas, $tinggi_miring) {
                        return (float) $luas_alas + (0.5 * (float) $keliling_alas * (float) $tinggi_miring);
                });
                // Konstanta Lanjutan
                $el->setVar('pi', M_PI);
                $el->setVar('phi', M_PI);
        }
}
