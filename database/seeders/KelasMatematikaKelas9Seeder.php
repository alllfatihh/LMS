<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Materi;
use App\Models\Soal;
use Illuminate\Support\Str;

class KelasMatematikaKelas9Seeder extends Seeder
{
    /**
     * Run the database seeds for Kelas 9 Matematika.
     */
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first() ?? User::where('email', 'guru@example.com')->first();
        $siswa = User::where('role', 'siswa')->first() ?? User::where('email', 'siswa@example.com')->first();

        if (!$guru) {
            return;
        }

        // Hapus kelas lama jika ada
        Kelas::where('name', 'Matematika SMP Kelas 9 (Kurikulum Merdeka)')->delete();

        // Buat Kelas 9
        $kodeKelas = Str::upper(Str::random(6));
        $kelas = Kelas::create([
            'name' => 'Matematika SMP Kelas 9 (Kurikulum Merdeka)',
            'description' => 'Materi lengkap dan bank soal dinamis tingkat mahir untuk SMP Kelas 9: Perpangkatan & Bentuk Akar, Persamaan Kuadrat, Transformasi Geometri, Kesebangunan, dan Bangun Ruang Sisi Lengkung.',
            'tahun' => (int) date('Y'),
            'class_code' => $kodeKelas,
            'guru_id' => $guru->id,
        ]);

        if ($siswa) {
            $kelas->siswa()->syncWithoutDetaching([$siswa->id]);
        }

        $chaptersData = [
            // =====================================================
            // BAB 1: PERPANGKATAN DAN BENTUK AKAR
            // =====================================================
            [
                'title' => 'Bab 1: Perpangkatan dan Bentuk Akar',
                'order' => 1,
                'materials' => [
                    [
                        'judul' => 'A. Bilangan Berpangkat dan Sifat-sifatnya',
                        'ringkasan' => 'Memahami eksponen dan operasi aljabar bilangan berpangkat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Sifat Perpangkatan</h3><ul><li>\\( a^m \\times a^n = a^{m+n} \\)</li><li>\\( \\frac{a^m}{a^n} = a^{m-n} \\)</li><li>\\( (a^m)^n = a^{m \\times n} \\)</li><li>\\( a^0 = 1 \\) (untuk \\( a \\neq 0 \\))</li><li>\\( a^{-n} = \\frac{1}{a^n} \\)</li></ul>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Perkalian dan Pembagian pada Perpangkatan',
                        'ringkasan' => 'Menerapkan sifat perkalian dan pembagian pada bilangan berpangkat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Perkalian & Pembagian Perpangkatan</h3><p>Pada perkalian: \\( a^m \\times a^n = a^{m+n} \\)</p><p>Pada pembagian: \\( a^m \\div a^n = a^{m-n} \\)</p><p>Perpangkatan berlapis: \\( (a^m)^n = a^{mn} \\)</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Notasi Ilmiah (Bentuk Baku)',
                        'ringkasan' => 'Menulis bilangan sangat besar atau sangat kecil dalam bentuk baku.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Notasi Ilmiah</h3><p>Bentuk baku: \\( a \\times 10^n \\) dimana \\( 1 \\le a < 10 \\).</p><p>Contoh: Jarak bumi ke matahari ≈ \\( 1,496 \\times 10^8 \\) km.</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Hitunglah nilai dari \\( {a}^{p} \\times {a}^{q} \\div {a}^{r} \\). Tuliskan eksponen akhirnya!',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'p' => ['min' => 4, 'max' => 8],
                            'q' => ['min' => 3, 'max' => 6],
                            'r' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => 'p + q - r',
                    ],
                    [
                        'question_template' => 'Hitunglah hasil dari \\( \\sqrt{ {kuadrat} } \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 12, 'max' => 30],
                            'kuadrat' => ['type' => 'derived', 'formula' => 'x * x'],
                        ],
                        'answer_formula' => 'sqrt(kuadrat)',
                    ],
                    [
                        'question_template' => 'Hitunglah hasil dari \\( ({a}^2)^3 \\div {a}^4 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => 'a * a',
                    ],
                    [
                        'question_template' => 'Berapakah nilai dari \\( {a}^3 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 9],
                        ],
                        'answer_formula' => 'a * a * a',
                    ],
                    [
                        'question_template' => 'Sederhanakan \\( \\frac{ {a}^{m} }{ {a}^{n} } \\). Tuliskan eksponen hasilnya!',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 6],
                            'm' => ['min' => 8, 'max' => 15],
                            'n' => ['min' => 3, 'max' => 7],
                        ],
                        'answer_formula' => 'm - n',
                    ],
                    [
                        'question_template' => 'Hitunglah \\( ({a}^{p})^{q} \\). Tuliskan eksponen hasilnya!',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 3],
                            'p' => ['min' => 2, 'max' => 4],
                            'q' => ['min' => 2, 'max' => 3],
                        ],
                        'answer_formula' => 'p * q',
                    ],
                    [
                        'question_template' => 'Sebuah populasi bakteri berlipat 3 setiap jam. Jika mula-mula ada {n} bakteri, berapa banyak bakteri setelah {t} jam?',
                        'variable_rules' => [
                            'n' => ['min' => 2, 'max' => 5],
                            't' => ['min' => 3, 'max' => 4],
                        ],
                        'answer_formula' => 'n * (3^t)',
                    ],
                    [
                        'question_template' => 'Jika \\( 2^x = {val} \\), berapakah nilai \\( x \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 4, 'max' => 8],
                            'val' => ['type' => 'derived', 'formula' => '2^x'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari \\( {a}^2 + {b}^2 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 5, 'max' => 12],
                            'b' => ['min' => 3, 'max' => 10],
                        ],
                        'answer_formula' => '(a * a) + (b * b)',
                    ],
                    [
                        'question_template' => 'Berapakah nilai dari \\( {a}^4 \\div {a}^2 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 7],
                        ],
                        'answer_formula' => 'a * a',
                    ],
                ]
            ],
            // =====================================================
            // BAB 2: PERSAMAAN DAN FUNGSI KUADRAT
            // =====================================================
            [
                'title' => 'Bab 2: Persamaan dan Fungsi Kuadrat',
                'order' => 2,
                'materials' => [
                    [
                        'judul' => 'A. Persamaan Kuadrat dan Akar-akarnya',
                        'ringkasan' => 'Memahami bentuk umum persamaan kuadrat ax² + bx + c = 0 dan menentukan akar-akarnya.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Persamaan Kuadrat</h3><p>Bentuk umum: \\( ax^2 + bx + c = 0 \\) dengan \\( a \\neq 0 \\).</p><p>Rumus abc (kuadratik): \\( x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a} \\)</p><p>Hubungan akar: \\( x_1 + x_2 = -\\frac{b}{a} \\) dan \\( x_1 \\cdot x_2 = \\frac{c}{a} \\)</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Grafik dan Sumbu Simetri Fungsi Kuadrat',
                        'ringkasan' => 'Menentukan sumbu simetri, titik puncak, dan nilai optimum fungsi kuadrat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Grafik Fungsi Kuadrat</h3><p>Grafik \\( f(x) = ax^2 + bx + c \\) berbentuk parabola.</p><ul><li>Sumbu simetri: \\( x = -\\frac{b}{2a} \\)</li><li>Titik puncak: \\( \\left(-\\frac{b}{2a}, -\\frac{D}{4a}\\right) \\) dengan \\( D = b^2 - 4ac \\)</li></ul>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Aplikasi Fungsi Kuadrat',
                        'ringkasan' => 'Menerapkan persamaan kuadrat pada permasalahan kontekstual.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Aplikasi</h3><p>Fungsi kuadrat banyak digunakan dalam masalah optimasi, seperti menentukan luas maksimum, keuntungan maksimum, atau tinggi maksimum benda yang dilempar.</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Diberikan persamaan kuadrat \\( x^2 + {b}x + {c} = 0 \\) memiliki akar-akar \\( x_1 = {x1} \\) dan \\( x_2 = {x2} \\). Hitunglah nilai diskriminan (\\( D = b^2 - 4ac \\))?',
                        'variable_rules' => [
                            'x1' => ['min' => 3, 'max' => 10],
                            'x2' => ['min' => 12, 'max' => 20],
                            'b' => ['type' => 'derived', 'formula' => '-(x1 + x2)'],
                            'c' => ['type' => 'derived', 'formula' => 'x1 * x2'],
                            'graph_formula' => 'y = x^2 + {b}x + {c}',
                        ],
                        'answer_formula' => '(b * b) - (4 * c)',
                    ],
                    [
                        'question_template' => 'Tentukan koordinat titik puncak dari fungsi kuadrat \\( f(x) = x^2 + {b}x + {c} \\). (Petunjuk: Anda dapat menggunakan grafik interaktif di bawah untuk melihat titik baliknya)',
                        'variable_rules' => [
                            'b' => ['min' => 4, 'max' => 10, 'kelipatan' => 2],
                            'c' => ['min' => 2, 'max' => 15],
                            'graph_formula' => 'y = x^2 + {b}x + {c}',
                        ],
                        'jawaban_format' => 'koordinat',
                        'answer_formula' => 'titik_puncak(1, b, c)',
                        'pembahasan_template' => "Koordinat titik puncak = (-b/2a, -D/4a)\nx_p = -{b}/2 = [[-b/2]]\ny_p = {c} - (-{b}/2)^2 = [[c - (b*b/4)]]\nJadi koordinatnya adalah [[titik_puncak(1, b, c)]]",
                    ],
                    [
                        'question_template' => 'Persamaan kuadrat \\( x^2 - {s}x + {p} = 0 \\) memiliki akar \\( x_1 \\) dan \\( x_2 \\). Hitunglah jumlah akar-akarnya (\\( x_1 + x_2 \\))?',
                        'variable_rules' => [
                            'x1' => ['min' => 2, 'max' => 8],
                            'x2' => ['min' => 3, 'max' => 10],
                            's' => ['type' => 'derived', 'formula' => 'x1 + x2'],
                            'p' => ['type' => 'derived', 'formula' => 'x1 * x2'],
                            'graph_formula' => 'y = x^2 - {s}x + {p}',
                        ],
                        'answer_formula' => 's',
                    ],
                    [
                        'question_template' => 'Persamaan kuadrat \\( x^2 - {s}x + {p} = 0 \\) memiliki akar \\( x_1 \\) dan \\( x_2 \\). Hitunglah hasil kali akar-akarnya (\\( x_1 \\cdot x_2 \\))?',
                        'variable_rules' => [
                            'x1' => ['min' => 2, 'max' => 7],
                            'x2' => ['min' => 3, 'max' => 9],
                            's' => ['type' => 'derived', 'formula' => 'x1 + x2'],
                            'p' => ['type' => 'derived', 'formula' => 'x1 * x2'],
                            'graph_formula' => 'y = x^2 - {s}x + {p}',
                        ],
                        'answer_formula' => 'p',
                    ],
                    [
                        'question_template' => 'Diketahui \\( f(x) = x^2 - {c} \\). Hitunglah nilai \\( f({a}) \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 5, 'max' => 12],
                            'c' => ['min' => 10, 'max' => 30],
                        ],
                        'answer_formula' => '(a * a) - c',
                    ],
                    [
                        'question_template' => 'Hitunglah nilai \\( f({a}) \\) jika \\( f(x) = {k}x^2 + {b} \\)?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 4],
                            'a' => ['min' => 3, 'max' => 7],
                            'b' => ['min' => 5, 'max' => 15],
                        ],
                        'answer_formula' => '(k * a * a) + b',
                    ],
                    [
                        'question_template' => 'Sebuah bola dilempar vertikal ke atas. Tingginya setelah \\( t \\) detik diberikan oleh \\( h = {v}t - 5t^2 \\). Berapakah tinggi bola setelah {t} detik?',
                        'variable_rules' => [
                            'v' => ['min' => 30, 'max' => 50],
                            't' => ['min' => 2, 'max' => 4],
                        ],
                        'answer_formula' => '(v * t) - (5 * t * t)',
                    ],
                    [
                        'question_template' => 'Suatu persegi panjang memiliki panjang \\( (x + {a}) \\) dan lebar \\( (x + {b}) \\). Jika \\( x = {c} \\), berapakah luasnya?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 8],
                            'b' => ['min' => 1, 'max' => 5],
                            'c' => ['min' => 4, 'max' => 10],
                        ],
                        'answer_formula' => '(c + a) * (c + b)',
                    ],
                    [
                        'question_template' => 'Akar-akar persamaan kuadrat \\( x^2 - {s}x + {p} = 0 \\) adalah \\( x_1 \\) dan \\( x_2 \\). Jika akar-akarnya berupa bilangan bulat positif, tentukan himpunan penyelesaian dari persamaan tersebut!',
                        'variable_rules' => [
                            'x1' => ['min' => 2, 'max' => 5],
                            'x2' => ['min' => 6, 'max' => 9],
                            's' => ['type' => 'derived', 'formula' => 'x1 + x2'],
                            'p' => ['type' => 'derived', 'formula' => 'x1 * x2'],
                            'graph_formula' => 'y = x^2 - {s}x + {p}',
                        ],
                        'jawaban_format' => 'himpunan',
                        'answer_formula' => 'himpunan(x1, x2)',
                        'pembahasan_template' => "Berdasarkan pemfaktoran:\n(x - {x1})(x - {x2}) = 0\nHimpunan penyelesaiannya adalah [[himpunan(x1, x2)]]",
                    ],
                    [
                        'question_template' => 'Faktorkanlah bentuk kuadrat \\( x^2 + {b}x + {c} \\) menjadi perkalian dua binomial dalam bentuk aljabar \\( (x+p)(x+q) \\)!',
                        'variable_rules' => [
                            'p' => ['min' => 1, 'max' => 5],
                            'q' => ['min' => 2, 'max' => 6],
                            'b' => ['type' => 'derived', 'formula' => 'p + q'],
                            'c' => ['type' => 'derived', 'formula' => 'p * q'],
                            'graph_formula' => 'y = x^2 + {b}x + {c}',
                        ],
                        'jawaban_format' => 'aljabar',
                        'answer_formula' => 'faktorisasi_kuadrat(p, q)',
                    ],
                ]
            ],
            // =====================================================
            // BAB 3: TRANSFORMASI GEOMETRI
            // =====================================================
            [
                'title' => 'Bab 3: Transformasi Geometri',
                'order' => 3,
                'materials' => [
                    [
                        'judul' => 'A. Pencerminan (Refleksi)',
                        'ringkasan' => 'Pencerminan titik terhadap sumbu-X, sumbu-Y, titik asal, dan garis y = x.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pencerminan</h3><ul><li>Refleksi thd sumbu-X: \\( (x, y) \\to (x, -y) \\)</li><li>Refleksi thd sumbu-Y: \\( (x, y) \\to (-x, y) \\)</li><li>Refleksi thd titik O: \\( (x, y) \\to (-x, -y) \\)</li><li>Refleksi thd garis y = x: \\( (x, y) \\to (y, x) \\)</li></ul>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Pergeseran (Translasi)',
                        'ringkasan' => 'Menggeser titik koordinat sebesar vektor translasi T(a,b).',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Translasi</h3><p>Translasi \\( T(a, b) \\): titik \\( (x, y) \\to (x + a, y + b) \\)</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Perputaran (Rotasi)',
                        'ringkasan' => 'Memutar titik terhadap pusat rotasi dengan sudut tertentu.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Rotasi</h3><p>Rotasi 90° berlawanan arah jarum jam terhadap O: \\( (x, y) \\to (-y, x) \\)</p><p>Rotasi 180° terhadap O: \\( (x, y) \\to (-x, -y) \\)</p><p>Rotasi 270° berlawanan arah jarum jam terhadap O: \\( (x, y) \\to (y, -x) \\)</p>',
                        'order' => 3,
                    ],
                    [
                        'judul' => 'D. Dilatasi',
                        'ringkasan' => 'Memperbesar atau memperkecil bangun dengan faktor skala k terhadap pusat dilatasi.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Dilatasi</h3><p>Dilatasi dengan pusat O dan faktor skala k: \\( (x, y) \\to (kx, ky) \\)</p>',
                        'order' => 4,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Titik \\( A({x}, {y}) \\) ditranslasikan oleh vektor translasi \\( T({tx}, {ty}) \\). Tentukan koordinat titik bayangan \\( A\' \\)! Anda dapat melihat representasi titik A di dalam grafik.',
                        'variable_rules' => [
                            'x' => ['min' => 2, 'max' => 8],
                            'y' => ['min' => 1, 'max' => 6],
                            'tx' => ['min' => 3, 'max' => 7],
                            'ty' => ['min' => -5, 'max' => -1],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'jawaban_format' => 'koordinat',
                        'answer_formula' => 'translasi(x, y, tx, ty)',
                        'pembahasan_template' => "Titik A({x}, {y}) ditranslasi T({tx}, {ty})\nA' = ({x} + {tx}, {y} + ({ty}))\nA' = [[translasi(x, y, tx, ty)]]"
                    ],
                    [
                        'question_template' => 'Titik \\( A({x}, {y}) \\) ditranslasikan oleh \\( T = ({a}, {b}) \\). Hitunglah ordinat bayangan \\( A\'(x\', y\') \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 15],
                            'y' => ['min' => 5, 'max' => 20],
                            'a' => ['min' => 2, 'max' => 12],
                            'b' => ['min' => -10, 'max' => 10],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => 'y + b',
                    ],
                    [
                        'question_template' => 'Titik \\( B({x}, {y}) \\) dicerminkan terhadap sumbu-X. Berapakah ordinat bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 15],
                            'y' => ['min' => 2, 'max' => 20],
                            'graph_formula' => '({x}, {y}), y = 0',
                        ],
                        'answer_formula' => '-y',
                    ],
                    [
                        'question_template' => 'Titik \\( C({x}, {y}) \\) dicerminkan terhadap sumbu-Y. Berapakah absis bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 18],
                            'y' => ['min' => 2, 'max' => 15],
                            'graph_formula' => '({x}, {y}), x = 0',
                        ],
                        'answer_formula' => '-x',
                    ],
                    [
                        'question_template' => 'Titik \\( D({x}, {y}) \\) dicerminkan terhadap titik asal O(0,0). Berapakah absis bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 4, 'max' => 20],
                            'y' => ['min' => 3, 'max' => 15],
                            'graph_formula' => '({x}, {y}), (0, 0)',
                        ],
                        'answer_formula' => '-x',
                    ],
                    [
                        'question_template' => 'Titik \\( P({x}, {y}) \\) dirotasi 90° berlawanan arah jarum jam terhadap titik asal. Berapakah absis bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 15],
                            'y' => ['min' => 2, 'max' => 12],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => '-y',
                    ],
                    [
                        'question_template' => 'Titik \\( Q({x}, {y}) \\) dirotasi 180° terhadap titik asal. Berapakah ordinat bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 5, 'max' => 18],
                            'y' => ['min' => 3, 'max' => 15],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => '-y',
                    ],
                    [
                        'question_template' => 'Titik \\( R({x}, {y}) \\) didilatasikan terhadap O(0,0) dengan faktor skala \\( k = {k} \\). Berapakah absis bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 10],
                            'y' => ['min' => 2, 'max' => 8],
                            'k' => ['min' => 2, 'max' => 5],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => 'x * k',
                    ],
                    [
                        'question_template' => 'Titik \\( S({x}, {y}) \\) didilatasikan terhadap O(0,0) dengan faktor skala \\( k = {k} \\). Berapakah ordinat bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 10],
                            'y' => ['min' => 2, 'max' => 8],
                            'k' => ['min' => 2, 'max' => 5],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => 'y * k',
                    ],
                    [
                        'question_template' => 'Titik \\( M({x}, {y}) \\) dicerminkan terhadap garis \\( y = x \\). Berapakah absis bayangan?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 15],
                            'y' => ['min' => 5, 'max' => 20],
                            'graph_formula' => '({x}, {y}), y = x',
                        ],
                        'answer_formula' => 'y',
                    ],
                ]
            ],
            // =====================================================
            // BAB 4: KESEBANGUNAN DAN KEKONGRUENAN
            // =====================================================
            [
                'title' => 'Bab 4: Kesebangunan dan Kekongruenan',
                'order' => 4,
                'materials' => [
                    [
                        'judul' => 'A. Kesebangunan Dua Bangun Datar',
                        'ringkasan' => 'Menerapkan perbandingan sisi-sisi bersesuaian pada bangun datar sebangun.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Kesebangunan</h3><p>Dua bangun datar sebangun jika sudut-sudut yang bersesuaian sama besar dan perbandingan panjang sisi-sisi yang bersesuaian adalah senilai (sama).</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Kekongruenan Dua Segitiga',
                        'ringkasan' => 'Memahami syarat dua segitiga kongruen: SAS, ASA, SSS.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Kekongruenan</h3><p>Dua segitiga kongruen jika bentuk dan ukurannya sama persis. Syarat kekongruenan:</p><ul><li>Sisi-Sisi-Sisi (SSS)</li><li>Sisi-Sudut-Sisi (SAS)</li><li>Sudut-Sisi-Sudut (ASA)</li></ul>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Kesebangunan Dua Segitiga',
                        'ringkasan' => 'Menentukan panjang sisi segitiga yang sebangun menggunakan perbandingan.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Kesebangunan Segitiga</h3><p>Dua segitiga sebangun jika: dua sudut bersesuaian sama (AA), atau perbandingan dua sisi bersesuaian sama dan sudut apitnya sama (SAS), atau ketiga sisi bersesuaian sebanding (SSS).</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Suatu tongkat tinggi {t1} cm memiliki bayangan {b1} cm. Pada saat yang sama, sebuah pohon memiliki bayangan {b2} cm. Berapakah tinggi pohon tersebut (cm)?',
                        'variable_rules' => [
                            't1' => ['min' => 100, 'max' => 200, 'kelipatan' => 10],
                            'k' => ['min' => 3, 'max' => 8],
                            'b1' => ['min' => 50, 'max' => 100, 'kelipatan' => 10],
                            'b2' => ['type' => 'derived', 'formula' => 'b1 * k'],
                        ],
                        'answer_formula' => 't1 * k',
                    ],
                    [
                        'question_template' => 'Dua segitiga sebangun dengan perbandingan sisi \\( 1 : {k} \\). Jika luas segitiga pertama {l1} cm², berapakah luas segitiga kedua (cm²)?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 5],
                            'l1' => ['min' => 10, 'max' => 30],
                        ],
                        'answer_formula' => 'l1 * (k * k)',
                    ],
                    [
                        'question_template' => 'Sebuah tiang setinggi {h} meter menghasilkan bayangan sepanjang {b} meter. Pada waktu yang sama, sebuah gedung menghasilkan bayangan sepanjang {bg} meter. Berapa tinggi gedung (meter)?',
                        'variable_rules' => [
                            'h' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 3, 'max' => 6],
                            'f' => ['min' => 5, 'max' => 10],
                            'bg' => ['type' => 'derived', 'formula' => 'b * f'],
                        ],
                        'answer_formula' => 'h * f',
                    ],
                    [
                        'question_template' => 'Pada peta berskala 1 : {skala}, jarak dua kota di peta adalah {jp} cm. Berapakah jarak sebenarnya dalam meter?',
                        'variable_rules' => [
                            'skala' => ['min' => 10000, 'max' => 50000, 'kelipatan' => 10000],
                            'jp' => ['min' => 5, 'max' => 15],
                        ],
                        'answer_formula' => '(jp * skala) / 100',
                    ],
                    [
                        'question_template' => 'Dua persegi panjang sebangun. Persegi panjang pertama berukuran {p1} cm × {l1} cm. Jika panjang persegi panjang kedua adalah {p2} cm, berapakah lebarnya (cm)?',
                        'variable_rules' => [
                            'p1' => ['min' => 6, 'max' => 12],
                            'l1' => ['min' => 3, 'max' => 5],
                            'k' => ['min' => 2, 'max' => 4],
                            'p2' => ['type' => 'derived', 'formula' => 'p1 * k'],
                        ],
                        'answer_formula' => 'l1 * k',
                    ],
                    [
                        'question_template' => 'Segitiga ABC sebangun dengan segitiga DEF. Sisi AB = {ab} cm, BC = {bc} cm, dan DE = {de} cm. Berapakah panjang EF (cm)?',
                        'variable_rules' => [
                            'ab' => ['min' => 4, 'max' => 8],
                            'bc' => ['min' => 6, 'max' => 12],
                            'k' => ['min' => 2, 'max' => 4],
                            'de' => ['type' => 'derived', 'formula' => 'ab * k'],
                        ],
                        'answer_formula' => 'bc * k',
                    ],
                    [
                        'question_template' => 'Perbandingan sisi dua segitiga sebangun adalah {a} : {b}. Jika keliling segitiga pertama {kl1} cm, berapakah keliling segitiga kedua (cm)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 3],
                            'b' => ['min' => 4, 'max' => 6],
                            'kl1' => ['min' => 12, 'max' => 24, 'kelipatan' => 2],
                        ],
                        'answer_formula' => '(kl1 * b) / a',
                    ],
                    [
                        'question_template' => 'Sebuah foto berukuran {p} cm × {l} cm akan diperbesar dengan faktor skala {k}. Berapakah luas foto setelah diperbesar (cm²)?',
                        'variable_rules' => [
                            'p' => ['min' => 6, 'max' => 10],
                            'l' => ['min' => 4, 'max' => 8],
                            'k' => ['min' => 2, 'max' => 4],
                        ],
                        'answer_formula' => '(p * k) * (l * k)',
                    ],
                    [
                        'question_template' => 'Model sebuah gedung dibuat dengan skala 1 : {skala}. Tinggi model adalah {tm} cm. Berapakah tinggi gedung sebenarnya dalam meter?',
                        'variable_rules' => [
                            'skala' => ['min' => 100, 'max' => 200, 'kelipatan' => 50],
                            'tm' => ['min' => 15, 'max' => 30],
                        ],
                        'answer_formula' => '(tm * skala) / 100',
                    ],
                    [
                        'question_template' => 'Dua segitiga sebangun dengan perbandingan keliling {a} : {b}. Perbandingan luasnya adalah?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 4],
                            'b' => ['min' => 5, 'max' => 8],
                        ],
                        'answer_formula' => '(a * a) + (b * b)',
                        // Note: This asks for a² : b², student answers a²+b² which is sum of the ratio terms
                    ],
                ]
            ],
            // =====================================================
            // BAB 5: BANGUN RUANG SISI LENGKUNG
            // =====================================================
            [
                'title' => 'Bab 5: Bangun Ruang Sisi Lengkung',
                'order' => 5,
                'materials' => [
                    [
                        'judul' => 'A. Tabung',
                        'ringkasan' => 'Menghitung volume dan luas permukaan tabung.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Tabung</h3><p>Volume: \\( V = \\pi r^2 t \\)</p><p>Luas Selimut: \\( 2\\pi r t \\)</p><p>Luas Permukaan: \\( 2\\pi r(r + t) \\)</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Kerucut',
                        'ringkasan' => 'Menghitung volume dan luas permukaan kerucut.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Kerucut</h3><p>Volume: \\( V = \\frac{1}{3} \\pi r^2 t \\)</p><p>Luas Selimut: \\( \\pi r s \\) (s = garis pelukis)</p><p>Garis pelukis: \\( s = \\sqrt{r^2 + t^2} \\)</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Bola',
                        'ringkasan' => 'Menghitung volume dan luas permukaan bola.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Bola</h3><p>Volume: \\( V = \\frac{4}{3} \\pi r^3 \\)</p><p>Luas Permukaan: \\( 4 \\pi r^2 \\)</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Suatu tabung memiliki jari-jari alas \\( r = {r} \\) cm dan tinggi \\( t = {t} \\) cm. Menggunakan \\( \\pi = \\frac{22}{7} \\), berapakah volume tabung tersebut (cm³)?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 28, 'kelipatan' => 7],
                            't' => ['min' => 10, 'max' => 30],
                        ],
                        'answer_formula' => '(22 / 7) * (r * r) * t',
                    ],
                    [
                        'question_template' => 'Suatu kerucut memiliki jari-jari alas \\( r = {r} \\) cm dan tinggi \\( t = {t} \\) cm. Menggunakan \\( \\pi = \\frac{22}{7} \\), berapakah volume kerucut tersebut (cm³)?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 21, 'kelipatan' => 7],
                            't' => ['min' => 9, 'max' => 30, 'kelipatan' => 3],
                        ],
                        'answer_formula' => '(1 / 3) * (22 / 7) * (r * r) * t',
                    ],
                    [
                        'question_template' => 'Sebuah bola memiliki jari-jari \\( r = {r} \\) cm. Menggunakan \\( \\pi = \\frac{22}{7} \\), berapakah volume bola tersebut (cm³)?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 21, 'kelipatan' => 7],
                        ],
                        'answer_formula' => '(4 / 3) * (22 / 7) * (r * r * r)',
                    ],
                    [
                        'question_template' => 'Luas selimut tabung dengan jari-jari \\( r = {r} \\) cm dan tinggi \\( t = {t} \\) cm (gunakan \\( \\pi = \\frac{22}{7} \\)) adalah ... cm²?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 21, 'kelipatan' => 7],
                            't' => ['min' => 10, 'max' => 25],
                        ],
                        'answer_formula' => '2 * (22 / 7) * r * t',
                    ],
                    [
                        'question_template' => 'Sebuah drum berbentuk tabung berisi air penuh. Jari-jari drum {r} cm dan tingginya {t} cm. Berapa liter air di dalam drum? (1 liter = 1000 cm³, gunakan \\( \\pi = \\frac{22}{7} \\))',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 14, 'kelipatan' => 7],
                            't' => ['min' => 20, 'max' => 50, 'kelipatan' => 10],
                        ],
                        'answer_formula' => '((22 / 7) * (r * r) * t) / 1000',
                    ],
                    [
                        'question_template' => 'Volume kerucut adalah sepertiga volume tabung dengan alas dan tinggi sama. Jika volume tabung yang dimaksud adalah {vt} cm³, berapakah volume kerucut (cm³)?',
                        'variable_rules' => [
                            'vt' => ['min' => 300, 'max' => 900, 'kelipatan' => 3],
                        ],
                        'answer_formula' => 'vt / 3',
                    ],
                    [
                        'question_template' => 'Luas permukaan bola dengan jari-jari \\( r = {r} \\) cm (gunakan \\( \\pi = \\frac{22}{7} \\)) adalah ... cm²?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 21, 'kelipatan' => 7],
                        ],
                        'answer_formula' => '4 * (22 / 7) * (r * r)',
                    ],
                    [
                        'question_template' => 'Perbandingan volume bola dan kerucut yang memiliki jari-jari dan tinggi yang sama (\\( t = 2r \\)) adalah?',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 7],
                        ],
                        'answer_formula' => '2',
                    ],
                    [
                        'question_template' => 'Sebuah es krim berbentuk kerucut memiliki jari-jari {r} cm dan tinggi {t} cm. Berapa cm³ es krim yang bisa ditampung? (gunakan \\( \\pi = \\frac{22}{7} \\))',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 14, 'kelipatan' => 7],
                            't' => ['min' => 12, 'max' => 21, 'kelipatan' => 3],
                        ],
                        'answer_formula' => '(1 / 3) * (22 / 7) * (r * r) * t',
                    ],
                    [
                        'question_template' => 'Sebuah bola basket memiliki diameter {d} cm. Berapakah jari-jarinya?',
                        'variable_rules' => [
                            'd' => ['min' => 14, 'max' => 42, 'kelipatan' => 14],
                        ],
                        'answer_formula' => 'd / 2',
                    ],
                ]
            ],
            // ============================================================
            // BAB 6: ARITMETIKA SOSIAL & PERBANDINGAN
            // ============================================================
            [
                'title' => 'Bab 6: Aritmetika Sosial dan Perbandingan',
                'order' => 6,
                'materials' => [
                    [
                        'judul' => 'A. Diskon, Pajak, dan Bunga Tunggal',
                        'ringkasan' => 'Perhitungan diskon, persentase keuntungan/kerugian, dan bunga tunggal tabungan.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Aritmetika Sosial</h3><ul><li><strong>Harga Setelah Diskon:</strong> Harga Awal - (Persen Diskon × Harga Awal)</li><li><strong>Bunga Tunggal:</strong> Modal × Persen Bunga × (Bulan / 12)</li><li><strong>Perbandingan Senilai:</strong> a1/b1 = a2/b2</li><li><strong>Perbandingan Berbalik Nilai:</strong> a1 × b1 = a2 × b2</li></ul>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Sebuah baju seharga Rp{h}0.000 mendapat diskon {d}%. Berapakah harga baju setelah diskon? (Tulis dalam satuan ribuan, misal Rp80.000 ditulis 80)',
                        'variable_rules' => [
                            'h' => ['min' => 50, 'max' => 200, 'kelipatan' => 10],
                            'd' => ['min' => 10, 'max' => 50, 'kelipatan' => 5],
                        ],
                        'answer_formula' => 'diskon(h, d)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Harga diskon = {h} - ({h} × {d}%)\n= [[diskon(h, d)]] ribu rupiah",
                    ],
                    [
                        'question_template' => 'Pak Ahmad menabung uang sebesar Rp{m}00.000 di bank dengan bunga tunggal {b}% per tahun. Berapakah bunga yang diperoleh setelah {t} bulan? (Tulis dalam ratusan ribu rupiah)',
                        'variable_rules' => [
                            'm' => ['min' => 10, 'max' => 50],
                            'b' => ['min' => 6, 'max' => 18, 'kelipatan' => 6],
                            't' => ['min' => 2, 'max' => 12, 'kelipatan' => 2],
                        ],
                        'answer_formula' => 'bunga_tunggal(m, b, t)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Bunga = Modal × %Bunga × (Bulan / 12)\n= {m} × {b}% × ({t}/12)\n= [[bunga_tunggal(m, b, t)]]",
                    ],
                    [
                        'question_template' => 'Dari {total} siswa di kelas, sebanyak {bagian} siswa mengikuti ekstrakurikuler Pramuka. Berapa persenkah siswa yang mengikuti Pramuka?',
                        'variable_rules' => [
                            'total' => ['min' => 20, 'max' => 50, 'kelipatan' => 5],
                            'bagian' => ['min' => 5, 'max' => 20],
                        ],
                        'answer_formula' => 'persen(bagian, total)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Persentase = ({bagian} / {total}) × 100%\n= [[persen(bagian, total)]]%",
                    ],
                    [
                        'question_template' => 'Untuk membuat {a1} porsi kue diperlukan {b1} kg tepung terigu. Berapa kg tepung terigu yang diperlukan untuk membuat {a2} porsi kue? (Perbandingan Senilai)',
                        'variable_rules' => [
                            'a1' => ['min' => 10, 'max' => 20, 'kelipatan' => 2],
                            'b1' => ['min' => 2, 'max' => 6],
                            'a2' => ['min' => 30, 'max' => 60, 'kelipatan' => 5],
                        ],
                        'answer_formula' => 'perbandingan_senilai(a1, b1, a2)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Sebuah proyek pembangunan dapat diselesaikan oleh {a1} pekerja dalam waktu {b1} hari. Jika proyek ingin diselesaikan dalam waktu {a2} hari, berapa banyak pekerja yang dibutuhkan? (Perbandingan Berbalik Nilai)',
                        'variable_rules' => [
                            'a1' => ['min' => 6, 'max' => 12],
                            'b1' => ['min' => 20, 'max' => 40, 'kelipatan' => 10],
                            'a2' => ['min' => 10, 'max' => 20, 'kelipatan' => 5],
                        ],
                        'answer_formula' => 'perbandingan_berbalik(a1, b1, a2)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Jarak dua kota pada peta berskala 1 : {s}000 adalah {jp} cm. Berapakah jarak sebenarnya dalam meter?',
                        'variable_rules' => [
                            'jp' => ['min' => 3, 'max' => 10],
                            's' => ['min' => 50, 'max' => 200, 'kelipatan' => 50],
                        ],
                        'answer_formula' => 'skala(jp, s * 10)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Tuliskan bentuk rasio sederhana dari {a} : {b}.',
                        'variable_rules' => [
                            'a' => ['min' => 12, 'max' => 48, 'kelipatan' => 6],
                            'b' => ['min' => 18, 'max' => 60, 'kelipatan' => 6],
                        ],
                        'answer_formula' => 'rasio(a, b)',
                        'jawaban_format' => 'aljabar',
                    ],
                    [
                        'question_template' => 'Tentukan penyelesaian dari persamaan linear {a}x + {b} = 0.',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 8],
                            'b' => ['min' => -24, 'max' => 24, 'kelipatan' => 2],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => 'plsv(a, b)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Hitunglah nilai pembulatan dari bilangan desimal {val} ke {n} angka di belakang koma.',
                        'variable_rules' => [
                            'val' => ['type' => 'float', 'min' => 12.3456, 'max' => 89.9876, 'decimals' => 4],
                            'n' => ['min' => 1, 'max' => 2],
                        ],
                        'answer_formula' => 'pembulatan(val, n)',
                        'jawaban_format' => 'desimal',
                        'pembahasan_template' => "Nilai awal = {val}\nDibulatkan ke {n} angka di belakang koma:\n= [[pembulatan(val, n)]]",
                    ],
                    [
                        'question_template' => 'Tentukan hasil perpangkatan {a} pangkat {n}.',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'n' => ['min' => 2, 'max' => 4],
                        ],
                        'answer_formula' => 'pangkat(a, n)',
                        'jawaban_format' => 'angka',
                    ],
                ]
            ],
            // ============================================================
            // BAB 7: STATISTIKA LANJUT & PENGAYAAN MATEMATIKA
            // ============================================================
            [
                'title' => 'Bab 7: Statistika Lanjut dan Pengayaan Matematika',
                'order' => 7,
                'materials' => [
                    [
                        'judul' => 'A. Ukuran Penyebaran Data & Trigonometri Dasar',
                        'ringkasan' => 'Jangkauan data, simpangan baku, variansi, serta perbandingan trigonometri dasar pada segitiga siku-siku.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Statistika Lanjut & Trigonometri</h3><ul><li><strong>Jangkauan (Range):</strong> Nilai Maksimum - Nilai Minimum</li><li><strong>Jangkauan Interkuartil:</strong> Q3 - Q1</li><li><strong>Trigonometri Dasar:</strong> sin(θ), cos(θ), tan(θ)</li></ul>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Diberikan data nilai: {a}, {b}, {c}, {d}, {e}. Tentukan jangkauan (range) dari data tersebut!',
                        'variable_rules' => [
                            'a' => ['min' => 4, 'max' => 10],
                            'b' => ['min' => 12, 'max' => 18],
                            'c' => ['min' => 7, 'max' => 15],
                            'd' => ['min' => 20, 'max' => 25],
                            'e' => ['min' => 1, 'max' => 5],
                        ],
                        'answer_formula' => 'jangkauan(a, b, c, d, e)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Diberikan kumpulan data: {a}, {b}, {c}, {d}. Tentukan nilai rata-rata dari data tersebut!',
                        'variable_rules' => [
                            'a' => ['min' => 60, 'max' => 90],
                            'b' => ['min' => 60, 'max' => 90],
                            'c' => ['min' => 60, 'max' => 90],
                            'd' => ['min' => 60, 'max' => 90],
                        ],
                        'answer_formula' => 'rata_rata(a, b, c, d)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Tentukan nilai maksimum dari sekumpulan angka: {a}, {b}, {c}, {d}, {e}.',
                        'variable_rules' => [
                            'a' => ['min' => 10, 'max' => 50],
                            'b' => ['min' => 10, 'max' => 50],
                            'c' => ['min' => 10, 'max' => 50],
                            'd' => ['min' => 10, 'max' => 50],
                            'e' => ['min' => 10, 'max' => 50],
                        ],
                        'answer_formula' => 'nilai_maks(a, b, c, d, e)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Tentukan nilai minimum dari sekumpulan angka: {a}, {b}, {c}, {d}, {e}.',
                        'variable_rules' => [
                            'a' => ['min' => 10, 'max' => 50],
                            'b' => ['min' => 10, 'max' => 50],
                            'c' => ['min' => 10, 'max' => 50],
                            'd' => ['min' => 10, 'max' => 50],
                            'e' => ['min' => 10, 'max' => 50],
                        ],
                        'answer_formula' => 'nilai_min(a, b, c, d, e)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Sebuah kerucut memiliki jari-jari {r} cm dan garis pelukis {s} cm. Hitunglah luas permukaan kerucut tersebut! (Gunakan π = 3.14159)',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 14],
                            's' => ['min' => 10, 'max' => 20],
                        ],
                        'answer_formula' => 'luas_permukaan_kerucut(r, s)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Sebuah bola memiliki jari-jari {r} cm. Hitunglah luas permukaannya! (Gunakan π = 3.14159)',
                        'variable_rules' => [
                            'r' => ['min' => 5, 'max' => 15],
                        ],
                        'answer_formula' => 'luas_permukaan_bola(r)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Sebuah tabung memiliki jari-jari {r} cm dan tinggi {t} cm. Hitunglah luas permukaan tabung! (Gunakan π = 3.14159)',
                        'variable_rules' => [
                            'r' => ['min' => 7, 'max' => 14],
                            't' => ['min' => 10, 'max' => 25],
                        ],
                        'answer_formula' => 'luas_permukaan_tabung(r, t)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Hitunglah garis pelukis kerucut yang memiliki jari-jari alas {r} cm dan tinggi {t} cm!',
                        'variable_rules' => [
                            'r' => ['min' => 5, 'max' => 12],
                            't' => ['min' => 12, 'max' => 16],
                        ],
                        'answer_formula' => 'garis_pelukis_kerucut(r, t)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Sebuah kubah masjid berbentuk setengah bola dengan jari-jari {r} meter. Hitunglah volume setengah bola tersebut! (Gunakan π = 3.14159)',
                        'variable_rules' => [
                            'r' => ['min' => 3, 'max' => 9],
                        ],
                        'answer_formula' => 'volume_setengah_bola(r)',
                        'jawaban_format' => 'angka',
                    ],
                    [
                        'question_template' => 'Dua segitiga sebangun memiliki perbandingan sisi a : b = {a} : {b}. Jika panjang sisi yang bersesuaian pada segitiga kedua adalah {c} cm, berapakah panjang sisi pada segitiga pertama?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 6],
                            'b' => ['min' => 3, 'max' => 8],
                            'c' => ['min' => 12, 'max' => 24, 'kelipatan' => 4],
                        ],
                        'answer_formula' => 'sisi_sebangun(a, b, c)',
                        'jawaban_format' => 'angka',
                    ],
                ]
            ],
            // =====================================================
            // BAB DEMO: ETALASE FITUR TERBARU (bobot, akar, grafik, desimal, pecahan, himpunan)
            // Dibuat khusus untuk demonstrasi/video — memuat semua upgrade dalam satu bab.
            // =====================================================
            [
                'title' => 'Bab Demo: Etalase Fitur Terbaru',
                'order' => 99,
                'materials' => [
                    [
                        'judul' => 'Tentang Bab Demo Ini',
                        'ringkasan' => 'Kumpulan soal contoh yang menampilkan seluruh fitur terbaru sistem.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Etalase Fitur Terbaru</h3><p>Bab ini berisi soal contoh untuk mendemonstrasikan upgrade sistem: <strong>bobot bervariasi</strong>, <strong>bentuk akar</strong>, <strong>grafik Desmos</strong>, <strong>variabel desimal</strong>, <strong>pecahan</strong>, dan <strong>himpunan</strong>.</p>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        // Fungsi kuadrat — jawaban KOORDINAT + grafik Desmos (HOTS)
                        'question_template' => 'Diketahui fungsi kuadrat \\( f(x) = x^2 + {b}x + {c} \\). Tentukan koordinat titik puncak (titik balik) grafiknya. Gunakan grafik Desmos di bawah sebagai bantuan.',
                        'variable_rules' => [
                            'b' => ['type' => 'integer', 'min' => -6, 'max' => 6],
                            'c' => ['type' => 'integer', 'min' => -8, 'max' => 8],
                            'graph_formula' => 'y = x^2 + {b}x + {c}',
                        ],
                        'answer_formula' => 'titik_puncak(1, b, c)',
                        'jawaban_format' => 'koordinat',
                        'bobot' => 25,
                        'pembahasan_template' => 'Sumbu simetri \\( x = -\\frac{b}{2a} \\), lalu substitusikan untuk memperoleh nilai y titik puncak.',
                    ],
                    [
                        // Persamaan kuadrat — jawaban HIMPUNAN penyelesaian
                        'question_template' => 'Tentukan himpunan penyelesaian dari persamaan kuadrat \\( x^2 - {b}x + {c} = 0 \\). Tulis dalam bentuk {x1, x2}.',
                        'variable_rules' => [
                            'p' => ['type' => 'integer', 'min' => 1, 'max' => 6],
                            'q' => ['type' => 'integer', 'min' => 1, 'max' => 6],
                            'b' => ['type' => 'derived', 'formula' => 'p + q'],
                            'c' => ['type' => 'derived', 'formula' => 'p * q'],
                        ],
                        'answer_formula' => 'himpunan(p, q)',
                        'jawaban_format' => 'himpunan',
                        'bobot' => 25,
                        'pembahasan_template' => 'Cari dua bilangan yang jumlahnya {b} dan hasil kalinya {c}, yaitu akar-akar {p} dan {q}.',
                    ],
                    [
                        // Bentuk akar — jawaban a√b
                        'question_template' => 'Sederhanakan bentuk akar \\( \\sqrt{ {n} } \\) ke bentuk paling sederhana \\( a\\sqrt{b} \\).',
                        'variable_rules' => [
                            'basis' => ['type' => 'integer', 'min' => 2, 'max' => 6],
                            'faktor' => ['type' => 'integer', 'min' => 2, 'max' => 3],
                            'n' => ['type' => 'derived', 'formula' => 'basis * basis * faktor'],
                        ],
                        'answer_formula' => 'sederhanakan_akar(n)',
                        'jawaban_format' => 'akar',
                        'bobot' => 20,
                        'pembahasan_template' => 'Uraikan {n} atas faktor kuadrat sempurna terbesar, lalu keluarkan dari tanda akar.',
                    ],
                    [
                        // Aljabar — faktorisasi kuadrat
                        'question_template' => 'Faktorkan bentuk kuadrat \\( x^2 + {b}x + {c} \\) menjadi perkalian dua binomial \\( (x+p)(x+q) \\).',
                        'variable_rules' => [
                            'p' => ['type' => 'integer', 'min' => 1, 'max' => 6],
                            'q' => ['type' => 'integer', 'min' => 1, 'max' => 6],
                            'b' => ['type' => 'derived', 'formula' => 'p + q'],
                            'c' => ['type' => 'derived', 'formula' => 'p * q'],
                        ],
                        'answer_formula' => 'faktorisasi_kuadrat(p, q)',
                        'jawaban_format' => 'aljabar',
                        'bobot' => 20,
                        'pembahasan_template' => 'Cari dua bilangan yang jumlahnya {b} dan hasil kalinya {c}, yaitu {p} dan {q}.',
                    ],
                    [
                        // Statistika lanjutan — simpangan baku (jawaban angka desimal)
                        'question_template' => 'Diberikan data: {a}, {b}, {c}, {d}, {e}. Hitunglah simpangan baku (standar deviasi) populasi data tersebut. Bulatkan hingga 2 angka di belakang koma.',
                        'variable_rules' => [
                            'a' => ['type' => 'integer', 'min' => 2, 'max' => 10],
                            'b' => ['type' => 'integer', 'min' => 2, 'max' => 10],
                            'c' => ['type' => 'integer', 'min' => 2, 'max' => 10],
                            'd' => ['type' => 'integer', 'min' => 2, 'max' => 10],
                            'e' => ['type' => 'integer', 'min' => 2, 'max' => 10],
                        ],
                        'answer_formula' => 'simpangan_baku(a, b, c, d, e)',
                        'jawaban_format' => 'angka',
                        'bobot' => 25,
                        'pembahasan_template' => 'Hitung rata-rata, lalu akar dari rata-rata kuadrat selisih tiap data terhadap rata-rata.',
                    ],
                    [
                        // Statistika lanjutan — jangkauan interkuartil (Q3 - Q1)
                        'question_template' => 'Diberikan data: {a}, {b}, {c}, {d}, {e}. Tentukan jangkauan interkuartil \\( (Q_3 - Q_1) \\) dari data tersebut.',
                        'variable_rules' => [
                            'a' => ['type' => 'integer', 'min' => 2, 'max' => 20],
                            'b' => ['type' => 'integer', 'min' => 2, 'max' => 20],
                            'c' => ['type' => 'integer', 'min' => 2, 'max' => 20],
                            'd' => ['type' => 'integer', 'min' => 2, 'max' => 20],
                            'e' => ['type' => 'integer', 'min' => 2, 'max' => 20],
                        ],
                        'answer_formula' => 'jangkauan_interkuartil(a, b, c, d, e)',
                        'jawaban_format' => 'angka',
                        'bobot' => 20,
                        'pembahasan_template' => 'Urutkan data, tentukan kuartil bawah (Q1) dan atas (Q3), lalu hitung Q3 - Q1.',
                    ],
                    [
                        // Kombinatorik — kombinasi C(n, r) (jawaban bulat)
                        'question_template' => 'Dari {n} orang siswa akan dipilih {r} orang untuk membentuk sebuah tim. Berapa banyak cara pemilihan yang mungkin (kombinasi)?',
                        'variable_rules' => [
                            'n' => ['type' => 'integer', 'min' => 5, 'max' => 9],
                            'r' => ['type' => 'integer', 'min' => 2, 'max' => 4],
                        ],
                        'answer_formula' => 'kombinasi(n, r)',
                        'jawaban_format' => 'bulat',
                        'bobot' => 15,
                        'pembahasan_template' => 'Gunakan rumus kombinasi \\( C(n,r) = \\frac{n!}{r!\\,(n-r)!} \\).',
                    ],
                    [
                        // Bangun ruang — luas permukaan bola (jawaban angka desimal)
                        'question_template' => 'Sebuah bola memiliki jari-jari {r} cm. Hitunglah luas permukaannya (dalam cm²). Bulatkan hingga 2 angka di belakang koma.',
                        'variable_rules' => [
                            'r' => ['type' => 'integer', 'min' => 7, 'max' => 21, 'kelipatan' => 7],
                        ],
                        'answer_formula' => 'luas_permukaan_bola(r)',
                        'jawaban_format' => 'angka',
                        'bobot' => 15,
                        'pembahasan_template' => 'Luas permukaan bola = \\( 4\\pi r^2 \\).',
                    ],
                    [
                        // Transformasi geometri — rotasi 90 + grafik (jawaban koordinat)
                        'question_template' => 'Titik \\( A({x}, {y}) \\) dirotasikan sebesar 90° berlawanan arah jarum jam terhadap titik asal O. Tentukan koordinat bayangannya. Lihat posisi titik A pada grafik.',
                        'variable_rules' => [
                            'x' => ['type' => 'integer', 'min' => -5, 'max' => 5],
                            'y' => ['type' => 'integer', 'min' => -5, 'max' => 5],
                            'graph_formula' => '({x}, {y})',
                        ],
                        'answer_formula' => 'rotasi_90(x, y)',
                        'jawaban_format' => 'koordinat',
                        'bobot' => 15,
                        'pembahasan_template' => 'Rotasi 90° berlawanan arah jarum jam: (x, y) menjadi (-y, x).',
                    ],
                    [
                        // Barisan geometri — suku ke-n (jawaban bulat)
                        'question_template' => 'Diketahui barisan geometri dengan suku pertama {a} dan rasio {r}. Tentukan suku ke-{n} dari barisan tersebut.',
                        'variable_rules' => [
                            'a' => ['type' => 'integer', 'min' => 1, 'max' => 4],
                            'r' => ['type' => 'integer', 'min' => 2, 'max' => 3],
                            'n' => ['type' => 'integer', 'min' => 4, 'max' => 7],
                        ],
                        'answer_formula' => 'suku_n_geometri(a, r, n)',
                        'jawaban_format' => 'bulat',
                        'bobot' => 15,
                        'pembahasan_template' => 'Suku ke-n barisan geometri = \\( a \\cdot r^{n-1} \\).',
                    ],
                    [
                        // Aritmetika sosial — bunga tunggal (jawaban angka)
                        'question_template' => 'Modal sebesar Rp{m} ditabung dengan bunga tunggal {p}% per tahun selama {bln} bulan. Hitunglah besar bunga yang diperoleh (dalam rupiah).',
                        'variable_rules' => [
                            'm' => ['type' => 'integer', 'min' => 500000, 'max' => 2000000, 'kelipatan' => 100000],
                            'p' => ['type' => 'integer', 'min' => 3, 'max' => 9],
                            'bln' => ['type' => 'integer', 'min' => 6, 'max' => 18],
                        ],
                        'answer_formula' => 'bunga_tunggal(m, p, bln)',
                        'jawaban_format' => 'angka',
                        'bobot' => 15,
                        'pembahasan_template' => 'Bunga = modal x (persen/100) x (bulan/12).',
                    ],
                    [
                        // Bangun ruang — volume kerucut (jawaban DESIMAL, dibulatkan 2 angka)
                        'question_template' => 'Sebuah kerucut memiliki jari-jari alas {r} cm dan tinggi {t} cm. Hitunglah volumenya (dalam cm³). Bulatkan hingga 2 angka di belakang koma.',
                        'variable_rules' => [
                            'r' => ['type' => 'integer', 'min' => 3, 'max' => 10],
                            't' => ['type' => 'integer', 'min' => 6, 'max' => 15],
                        ],
                        'answer_formula' => 'pembulatan(volume_kerucut(r, t), 2)',
                        'jawaban_format' => 'desimal',
                        'bobot' => 20,
                        'pembahasan_template' => 'Volume kerucut = \\( \\frac{1}{3}\\pi r^2 t \\).',
                    ],
                    [
                        // Peluang — jawaban PECAHAN
                        'question_template' => 'Sebuah percobaan memiliki {k} hasil yang diharapkan dari {t} hasil yang mungkin (semua berpeluang sama). Tentukan peluang kejadian tersebut dalam bentuk pecahan a/b.',
                        'variable_rules' => [
                            'k' => ['type' => 'integer', 'min' => 1, 'max' => 5],
                            't' => ['type' => 'integer', 'min' => 6, 'max' => 12],
                        ],
                        'answer_formula' => 'pecahan(k, t)',
                        'jawaban_format' => 'pecahan',
                        'bobot' => 10,
                        'pembahasan_template' => 'Peluang = banyak hasil diharapkan / banyak hasil mungkin = {k}/{t}.',
                    ],
                    [
                        // Persamaan garis — gradien melalui dua titik (jawaban PECAHAN)
                        'question_template' => 'Tentukan gradien (kemiringan) garis yang melalui titik \\( ({x1}, {y1}) \\) dan \\( ({x2}, {y2}) \\). Tulis dalam bentuk pecahan a/b.',
                        'variable_rules' => [
                            'x1' => ['type' => 'integer', 'min' => 1, 'max' => 3],
                            'y1' => ['type' => 'integer', 'min' => 1, 'max' => 9],
                            'x2' => ['type' => 'integer', 'min' => 4, 'max' => 7],
                            'y2' => ['type' => 'integer', 'min' => 1, 'max' => 9],
                        ],
                        'answer_formula' => 'pecahan(y2 - y1, x2 - x1)',
                        'jawaban_format' => 'pecahan',
                        'bobot' => 15,
                        'pembahasan_template' => 'Gradien \\( m = \\frac{y_2 - y_1}{x_2 - x_1} \\).',
                    ],
                    [
                        // Persamaan garis lurus — jawaban ALJABAR (y = mx + c)
                        'question_template' => 'Tentukan persamaan garis lurus yang bergradien {m} dan memotong sumbu-y di titik (0, {c}). Tulis dalam bentuk y = mx + c.',
                        'variable_rules' => [
                            'm' => ['type' => 'integer', 'min' => 1, 'max' => 5],
                            'c' => ['type' => 'integer', 'min' => -6, 'max' => 6],
                        ],
                        'answer_formula' => 'persamaan_garis(m, c)',
                        'jawaban_format' => 'aljabar',
                        'bobot' => 15,
                        'pembahasan_template' => 'Garis bergradien m dengan konstanta c memiliki persamaan y = {m}x + {c}.',
                    ],
                ]
            ],
        ];

        foreach ($chaptersData as $chapter) {
            $bab = Bab::create([
                'kelas_id' => $kelas->id,
                'title' => $chapter['title'],
                'order' => $chapter['order'],
            ]);

            foreach ($chapter['materials'] as $materi) {
                Materi::create([
                    'kelas_id' => $kelas->id,
                    'bab_id' => $bab->id,
                    'judul' => $materi['judul'],
                    'slug' => Str::slug($materi['judul'] . ' ' . uniqid()),
                    'ringkasan' => $materi['ringkasan'],
                    'tipe' => $materi['tipe'],
                    'konten_teks' => $materi['konten_teks'] ?? null,
                    'order' => $materi['order'],
                ]);
            }

            foreach ($chapter['questions'] as $soal) {
                Soal::create([
                    'bab_id' => $bab->id,
                    'question_template' => $soal['question_template'],
                    'variable_rules' => $soal['variable_rules'],
                    'answer_formula' => $soal['answer_formula'],
                    'bobot' => $soal['bobot'] ?? 10,
                    'jawaban_format' => $soal['jawaban_format'] ?? 'angka',
                    'pembahasan_template' => $soal['pembahasan_template'] ?? null,
                ]);
            }
        }

        $this->command->info("Kelas '{$kelas->name}' berhasil dibuat beserta materi dan bank soal dinamis!");
    }
}
