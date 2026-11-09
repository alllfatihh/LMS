<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Materi;
use App\Models\Soal;
use Illuminate\Support\Str;

class KelasMatematikaKelas8Seeder extends Seeder
{
    /**
     * Run the database seeds for Kelas 8 Matematika.
     */
    public function run(): void
    {
        $guru = User::where('role', 'guru')->first() ?? User::where('email', 'guru@example.com')->first();
        $siswa = User::where('role', 'siswa')->first() ?? User::where('email', 'siswa@example.com')->first();

        if (!$guru) {
            return;
        }

        // Hapus kelas lama jika ada
        Kelas::where('name', 'Matematika SMP Kelas 8 (Kurikulum Merdeka)')->delete();

        // Buat Kelas 8
        $kodeKelas = Str::upper(Str::random(6));
        $kelas = Kelas::create([
            'name' => 'Matematika SMP Kelas 8 (Kurikulum Merdeka)',
            'description' => 'Materi lengkap dan bank soal dinamis tingkat lanjut untuk SMP Kelas 8: Barisan Bilangan, Pythagoras, Persamaan Garis, SPLDV, dan Statistika.',
            'tahun' => (int) date('Y'),
            'class_code' => $kodeKelas,
            'guru_id' => $guru->id,
        ]);

        if ($siswa) {
            $kelas->siswa()->syncWithoutDetaching([$siswa->id]);
        }

        $chaptersData = [
            [
                'title' => 'Bab 1: Pola Bilangan dan Barisan',
                'order' => 1,
                'materials' => [
                    [
                        'judul' => 'A. Pola Bilangan dan Konfigurasi Objek',
                        'ringkasan' => 'Mengenal pola bilangan persegi, segitiga, dan barisan aritmetika tingkat dasar.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pola Bilangan</h3><p>Pola bilangan adalah susunan angka yang membentuk keteraturan tertentu.</p><ul><li><strong>Barisan Aritmetika:</strong> Barisan dengan beda (selisih) tetap antar suku berdekatan. Rumus suku ke-n: \\( U_n = a + (n - 1)b \\).</li><li><strong>Deret Aritmetika:</strong> Jumlah n suku pertama: \\( S_n = \\frac{n}{2}(2a + (n-1)b) \\).</li></ul>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Diberikan suatu barisan aritmetika dengan suku pertama \\( a = {a} \\) dan beda \\( b = {b} \\). Hitunglah suku ke-{n} (\\( U_{n} \\)) dari barisan tersebut?',
                        'variable_rules' => [
                            'a' => ['min' => 6, 'max' => 25],
                            'b' => ['min' => 6, 'max' => 18],
                            'n' => ['min' => 15, 'max' => 37],
                        ],
                        'answer_formula' => 'a + (n - 1) * b',
                    ],
                    [
                        'question_template' => 'Hitunglah suku ke-{n} dari barisan bilangan aritmetika jika rumus suku ke-n ditentukan oleh \\( U_n = {k}n + {c} \\)?',
                        'variable_rules' => [
                            'k' => ['min' => 6, 'max' => 15],
                            'c' => ['min' => 3, 'max' => 27],
                            'n' => ['min' => 12, 'max' => 30],
                        ],
                        'answer_formula' => '(k * n) + c',
                    ],
                    [
                        'question_template' => 'Pada sebuah gedung pertunjukan, baris terdepan berisi {a} kursi. Setiap baris di belakangnya bertambah {b} kursi. Apabila ada {n} baris kursi, berapakah banyak kursi pada baris terakhir?',
                        'variable_rules' => [
                            'a' => ['min' => 25, 'max' => 62],
                            'b' => ['min' => 6, 'max' => 12],
                            'n' => ['min' => 18, 'max' => 30],
                        ],
                        'answer_formula' => 'a + (n - 1) * b',
                    ],
                    [
                        'question_template' => 'Hitunglah jumlah {n} suku pertama (\\( S_{n} \\)) dari deret aritmetika dengan suku pertama \\( a = {a} \\) dan suku ke-{n} adalah {un}?',
                        'variable_rules' => [
                            'n' => ['min' => 12, 'max' => 25, 'kelipatan' => 2],
                            'a' => ['min' => 6, 'max' => 25],
                            'un' => ['min' => 50, 'max' => 75],
                        ],
                        'answer_formula' => '(n * (a + un)) / 2',
                    ],
                    [
                        'question_template' => 'Diketahui barisan geometri dengan suku pertama \\( a = {a} \\) dan rasio \\( r = 2 \\). Berapakah nilai suku ke-5 (\\( U_5 \\)) dari barisan tersebut?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 10],
                        ],
                        'answer_formula' => 'a * 16',
                    ],
                    [
                        'question_template' => 'Dalam suatu kelas, banyak kursi pada baris pertama adalah {a} buah. Setiap baris di belakangnya bertambah {b} kursi. Berapakah banyak kursi pada baris ke-{n}?',
                        'variable_rules' => [
                            'a' => ['min' => 10, 'max' => 20],
                            'b' => ['min' => 2, 'max' => 5],
                            'n' => ['min' => 6, 'max' => 15],
                        ],
                        'answer_formula' => 'a + (n - 1) * b',
                    ],
                    [
                        'question_template' => 'Sebuah deret aritmetika memiliki beda \\( b = {b} \\). Jika suku ke-{n} adalah {un}, berapakah suku pertama (\\( a \\)) dari deret tersebut?',
                        'variable_rules' => [
                            'b' => ['min' => 2, 'max' => 8],
                            'n' => ['min' => 10, 'max' => 25],
                            'a' => ['min' => 5, 'max' => 20],
                            'un' => ['type' => 'derived', 'formula' => 'a + (n - 1) * b'],
                        ],
                        'answer_formula' => 'a',
                    ],
                    [
                        'question_template' => 'Jumlah dua suku pertama barisan geometri adalah {s2}. Jika suku pertama adalah {a}, berapakah rasio (\\( r \\)) barisan tersebut?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 10],
                            'r' => ['min' => 2, 'max' => 5],
                            's2' => ['type' => 'derived', 'formula' => 'a + (a * r)'],
                        ],
                        'answer_formula' => 'r',
                    ],
                    [
                        'question_template' => 'Terdapat barisan aritmetika 5, 8, 11, 14, ... . Angka kelipatan 3 yang menjadi beda (b) jika kita mengalikan suku pertama dengan konstanta {k} adalah?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => '3 * k',
                    ],
                    [
                        'question_template' => 'Suku ke-n sebuah barisan ditentukan oleh \\( U_n = n^2 - {c} \\). Berapakah suku ke-{n}?',
                        'variable_rules' => [
                            'n' => ['min' => 5, 'max' => 12],
                            'c' => ['min' => 10, 'max' => 20],
                        ],
                        'answer_formula' => '(n * n) - c',
                    ],
                ]
            ],
            [
                'title' => 'Bab 2: Teorema Pythagoras',
                'order' => 2,
                'materials' => [
                    [
                        'judul' => 'A. Pembuktian dan Rumus Teorema Pythagoras',
                        'ringkasan' => 'Menghitung panjang sisi segitiga siku-siku menggunakan hubungan kuadrat sisi miring.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Teorema Pythagoras</h3><p>Pada segitiga siku-siku dengan sisi penyiku a dan b, serta hipotenusa (sisi miring) c, berlaku hubungan:</p><p>\\( c^2 = a^2 + b^2 \\iff c = \\sqrt{a^2 + b^2} \\)</p><p>Tripel Pythagoras dasar antara lain: (3, 4, 5), (5, 12, 13), (6, 8, 10), (7, 24, 25), (8, 15, 17).</p>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Suatu segitiga siku-siku memiliki panjang sisi siku-siku \\( a = {a} \\) cm dan \\( b = {b} \\) cm. Hitunglah panjang hipotenusa (sisi miring \\( c \\)) segitiga tersebut dalam cm?',
                        'variable_rules' => [
                            'k' => ['min' => 3, 'max' => 18],
                            'a' => ['type' => 'derived', 'formula' => '3 * k'],
                            'b' => ['type' => 'derived', 'formula' => '4 * k'],
                        ],
                        'answer_formula' => 'sqrt((a * a) + (b * b))',
                    ],
                    [
                        'question_template' => 'Suatu tangga dengan panjang {c} meter disandarkan pada dinding tegak. Jarak ujung bawah tangga ke dinding adalah {a} meter. Hitunglah tinggi dinding yang dicapai tangga tersebut dalam meter?',
                        'variable_rules' => [
                            'k' => ['min' => 3, 'max' => 12],
                            'a' => ['type' => 'derived', 'formula' => '5 * k'],
                            'c' => ['type' => 'derived', 'formula' => '13 * k'],
                        ],
                        'answer_formula' => 'sqrt((c * c) - (a * a))',
                    ],
                    [
                        'question_template' => 'Suatu kapal berlayar ke arah barat sejauh {a} km, kemudian berbelok ke arah utara sejauh {b} km. Hitunglah jarak terpendek posisi kapal sekarang dari titik awal keberangkatan?',
                        'variable_rules' => [
                            'k' => ['min' => 3, 'max' => 15],
                            'a' => ['type' => 'derived', 'formula' => '6 * k'],
                            'b' => ['type' => 'derived', 'formula' => '8 * k'],
                        ],
                        'answer_formula' => 'sqrt((a * a) + (b * b))',
                    ],
                    [
                        'question_template' => 'Sebuah kapal berlayar sejauh {a} km ke arah Utara, kemudian berbelok ke arah Timur sejauh {b} km. Berapakah jarak terpendek kapal tersebut dari titik awal keberangkatannya (dalam km)?',
                        'variable_rules' => [
                            'k' => ['min' => 4, 'max' => 12],
                            'a' => ['type' => 'derived', 'formula' => '5 * k'],
                            'b' => ['type' => 'derived', 'formula' => '12 * k'],
                        ],
                        'answer_formula' => '13 * k',
                    ],
                    [
                        'question_template' => 'Sebuah tangga yang panjangnya {c} meter bersandar pada tembok. Jarak ujung bawah tangga terhadap tembok adalah {a} meter. Berapakah tinggi tembok yang dicapai oleh ujung atas tangga tersebut?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 6],
                            'c' => ['type' => 'derived', 'formula' => '10 * k'],
                            'a' => ['type' => 'derived', 'formula' => '6 * k'],
                        ],
                        'answer_formula' => '8 * k',
                    ],
                    [
                        'question_template' => 'Panjang sisi siku-siku suatu segitiga adalah {a} cm dan {b} cm. Jika luasnya adalah {l} cm², dan hipotenusanya adalah {c} cm, berapakah keliling segitiga tersebut?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 5],
                            'a' => ['type' => 'derived', 'formula' => '3 * k'],
                            'b' => ['type' => 'derived', 'formula' => '4 * k'],
                            'c' => ['type' => 'derived', 'formula' => '5 * k'],
                            'l' => ['type' => 'derived', 'formula' => '(a * b) / 2'],
                        ],
                        'answer_formula' => 'a + b + c',
                    ],
                    [
                        'question_template' => 'Dua tiang berdampingan berjarak {d} meter. Tinggi tiang pertama {t1} meter dan tinggi tiang kedua {t2} meter. Panjang kawat penghubung kedua ujung tiang adalah ... meter?',
                        'variable_rules' => [
                            'k' => ['min' => 1, 'max' => 4],
                            'd' => ['type' => 'derived', 'formula' => '12 * k'],
                            't1' => ['min' => 5, 'max' => 10],
                            'selisih' => ['type' => 'derived', 'formula' => '5 * k'],
                            't2' => ['type' => 'derived', 'formula' => 't1 + selisih'],
                        ],
                        'answer_formula' => '13 * k',
                    ],
                    [
                        'question_template' => 'Sebuah persegi memiliki diagonal dengan panjang \\( d \\). Jika luas persegi tersebut adalah {l} cm², berapakah panjang salah satu sisinya (ambil nilai bulat saja)?',
                        'variable_rules' => [
                            's' => ['min' => 10, 'max' => 25],
                            'l' => ['type' => 'derived', 'formula' => 's * s'],
                        ],
                        'answer_formula' => 's',
                    ],
                    [
                        'question_template' => 'Panjang sisi belah ketupat adalah {s} cm dan salah satu diagonalnya {d1} cm. Berapakah panjang diagonal yang lain dalam cm?',
                        'variable_rules' => [
                            'k' => ['min' => 1, 'max' => 3],
                            'd1' => ['type' => 'derived', 'formula' => '6 * k * 2'],
                            'd2' => ['type' => 'derived', 'formula' => '8 * k * 2'],
                            's' => ['type' => 'derived', 'formula' => '10 * k'],
                        ],
                        'answer_formula' => 'd2',
                    ],
                ]
            ],
            [
                'title' => 'Bab 3: Persamaan Garis Lurus dan Gradien',
                'order' => 3,
                'materials' => [
                    [
                        'judul' => 'A. Menentukan Gradien Garis',
                        'ringkasan' => 'Menghitung kemiringan garis (gradien m) melalui dua titik koordinat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Gradien Garis Lurus</h3><p>Gradien \\( m \\) yang melalui titik \\( A(x_1, y_1) \\) dan \\( B(x_2, y_2) \\) dihitung dengan formula:</p><p>\\( m = \\frac{y_2 - y_1}{x_2 - x_1} \\)</p>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Hitunglah gradien garis yang melalui titik \\( A({x1}, {y1}) \\) dan titik \\( B({x2}, {y2}) \\)?',
                        'variable_rules' => [
                            'x1' => ['min' => 3, 'max' => 12],
                            'dx' => ['min' => 3, 'max' => 15],
                            'x2' => ['type' => 'derived', 'formula' => 'x1 + dx'],
                            'm' => ['min' => 6, 'max' => 18],
                            'y1' => ['min' => 3, 'max' => 24],
                            'y2' => ['type' => 'derived', 'formula' => 'y1 + (m * dx)'],
                            'graph_formula' => '({x1}, {y1}), ({x2}, {y2})',
                        ],
                        'answer_formula' => '(y2 - y1) / (x2 - x1)',
                    ],
                    [
                        'question_template' => 'Garis dengan persamaan \\( y = {m}x + {c} \\) memotong sumbu-Y di titik \\( (0, c) \\). Hitunglah nilai \\( c \\)?',
                        'variable_rules' => [
                            'm' => ['min' => 6, 'max' => 21],
                            'c' => ['min' => 9, 'max' => 37],
                            'graph_formula' => 'y = {m}x + {c}',
                        ],
                        'answer_formula' => 'c',
                    ],
                    [
                        'question_template' => 'Suatu garis lurus memiliki persamaan \\( y = {m}x + {c} \\). Berapakah gradien garis tersebut?',
                        'variable_rules' => [
                            'm' => ['min' => -10, 'max' => 15],
                            'c' => ['min' => 2, 'max' => 25],
                            'graph_formula' => 'y = {m}x + {c}',
                        ],
                        'answer_formula' => 'm',
                    ],
                    [
                        'question_template' => 'Gradien dari garis yang sejajar dengan garis \\( y = {m}x - {c} \\) adalah?',
                        'variable_rules' => [
                            'm' => ['min' => 3, 'max' => 9],
                            'c' => ['min' => 1, 'max' => 10],
                            'graph_formula' => 'y = {m}x - {c}',
                        ],
                        'answer_formula' => 'm',
                    ],
                    [
                        'question_template' => 'Sebuah garis tegak lurus dengan garis \\( y = -\\frac{1}[[{m]]}x + {c} \\). Gradien garis tersebut adalah?',
                        'variable_rules' => [
                            'm' => ['min' => 2, 'max' => 10],
                            'c' => ['min' => 5, 'max' => 15],
                            'graph_formula' => 'y = -(1/{m})x + {c}',
                        ],
                        'answer_formula' => 'm',
                    ],
                    [
                        'question_template' => 'Garis lurus \\( y = {m}x + {c} \\) memotong sumbu-X pada titik \\( (x, 0) \\). Tentukan nilai \\( -x \\) dari perpotongan tersebut?',
                        'variable_rules' => [
                            'x_neg' => ['min' => 2, 'max' => 8],
                            'm' => ['min' => 2, 'max' => 5],
                            'c' => ['type' => 'derived', 'formula' => 'x_neg * m'],
                            'graph_formula' => 'y = {m}x + {c}',
                        ],
                        'answer_formula' => 'x_neg',
                    ],
                    [
                        'question_template' => 'Tentukan nilai \\( y \\) jika garis \\( {a}x - {b}y = {c} \\) memiliki koordinat \\( x = 0 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 2, 'max' => 6],
                            'y' => ['min' => -10, 'max' => -2],
                            'c' => ['type' => 'derived', 'formula' => '-1 * b * y'],
                            'graph_formula' => '{a}x - {b}y = {c}',
                        ],
                        'answer_formula' => 'y',
                    ],
                    [
                        'question_template' => 'Berapakah gradien dari persamaan garis \\( {p}y - {q}x = {r} \\)?',
                        'variable_rules' => [
                            'p' => ['min' => 2, 'max' => 2],
                            'q' => ['min' => 4, 'max' => 10, 'kelipatan' => 2],
                            'r' => ['min' => 10, 'max' => 20],
                            'graph_formula' => '{p}y - {q}x = {r}',
                        ],
                        'answer_formula' => 'q / p',
                    ],
                    [
                        'question_template' => 'Diketahui persamaan garis \\( y = {m}x + {c} \\) melalui titik ({x}, {y}). Berapakah nilai \\( c \\)?',
                        'variable_rules' => [
                            'm' => ['min' => 2, 'max' => 5],
                            'x' => ['min' => 3, 'max' => 8],
                            'c' => ['min' => -10, 'max' => 10],
                            'y' => ['type' => 'derived', 'formula' => '(m * x) + c'],
                            'graph_formula' => 'y = {m}x + {c}, ({x}, {y})',
                        ],
                        'answer_formula' => 'c',
                    ],
                    [
                        'question_template' => 'Persamaan garis \\( y = {m}x + c \\) dan garis sejajarnya memiliki kemiringan yang sama. Berapa gradien garis yang sejajar dengan garis tersebut?',
                        'variable_rules' => [
                            'm' => ['min' => -8, 'max' => 12],
                            'graph_formula' => 'y = {m}x + 2',
                        ],
                        'answer_formula' => 'm',
                    ],
                ]
            ],
            [
                'title' => 'Bab 4: Sistem Persamaan Linear Dua Variabel (SPLDV)',
                'order' => 4,
                'materials' => [
                    [
                        'judul' => 'A. Konsep SPLDV dan Metode Penyelesaian',
                        'ringkasan' => 'Menyelesaikan SPLDV menggunakan metode eliminasi dan substitusi.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>SPLDV</h3><p>Bentuk umum SPLDV:</p><p>\\( a_1 x + b_1 y = c_1 \\)<br>\\( a_2 x + b_2 y = c_2 \\)</p>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Harga {p} buku dan {q} pulpen adalah Rp{total1}.000. Apabila harga 1 buku adalah Rp{hargaBuku}.000 dan harga 1 pulpen adalah Rp{hargaPulpen}.000, berapakah harga dari {k} buku dan {m} pulpen? (Jawab dalam ribuan rupiah, misal Rp25.000 tulis 25)',
                        'variable_rules' => [
                            'hargaBuku' => ['min' => 9, 'max' => 21],
                            'hargaPulpen' => ['min' => 6, 'max' => 12],
                            'p' => ['min' => 6, 'max' => 9],
                            'q' => ['min' => 3, 'max' => 6],
                            'total1' => ['type' => 'derived', 'formula' => '(p * hargaBuku) + (q * hargaPulpen)'],
                            'k' => ['min' => 3, 'max' => 12],
                            'm' => ['min' => 6, 'max' => 15],
                        ],
                        'answer_formula' => '(k * hargaBuku) + (m * hargaPulpen)',
                    ],
                    [
                        'question_template' => 'Apabila \\( x + y = {sum} \\) dan \\( x - y = {diff} \\), berapakah nilai dari \\( x \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 25, 'max' => 62],
                            'y' => ['min' => 6, 'max' => 24],
                            'sum' => ['type' => 'derived', 'formula' => 'x + y'],
                            'diff' => ['type' => 'derived', 'formula' => 'x - y'],
                        ],
                        'answer_formula' => '(sum + diff) / 2',
                    ],
                    [
                        'question_template' => 'Keliling sebuah taman berbentuk persegi panjang adalah {k} meter. Jika selisih panjang dan lebarnya adalah {d} meter, berapakah panjang taman tersebut?',
                        'variable_rules' => [
                            'p' => ['min' => 15, 'max' => 40],
                            'l' => ['min' => 5, 'max' => 14],
                            'k' => ['type' => 'derived', 'formula' => '2 * (p + l)'],
                            'd' => ['type' => 'derived', 'formula' => 'p - l'],
                        ],
                        'answer_formula' => 'p',
                    ],
                    [
                        'question_template' => 'Umur ayah {k} kali umur anak. Jika jumlah umur mereka adalah {sum} tahun, berapakah umur anak?',
                        'variable_rules' => [
                            'a' => ['min' => 6, 'max' => 15],
                            'k' => ['min' => 3, 'max' => 5],
                            'sum' => ['type' => 'derived', 'formula' => 'a + (k * a)'],
                        ],
                        'answer_formula' => 'a',
                    ],
                    [
                        'question_template' => 'Harga tiket anak-anak adalah Rp{ta}.000 dan dewasa Rp{td}.000. Jika terjual {a} tiket anak dan {d} tiket dewasa, berapa total pendapatan dalam ribuan rupiah?',
                        'variable_rules' => [
                            'ta' => ['min' => 15, 'max' => 25],
                            'td' => ['min' => 35, 'max' => 50],
                            'a' => ['min' => 20, 'max' => 50],
                            'd' => ['min' => 10, 'max' => 30],
                        ],
                        'answer_formula' => '(ta * a) + (td * d)',
                    ],
                    [
                        'question_template' => 'Di sebuah lahan parkir terdapat {m} motor (roda 2) dan {b} mobil (roda 4). Berapa jumlah seluruh roda kendaraan di tempat parkir tersebut?',
                        'variable_rules' => [
                            'm' => ['min' => 30, 'max' => 60],
                            'b' => ['min' => 15, 'max' => 40],
                        ],
                        'answer_formula' => '(m * 2) + (b * 4)',
                    ],
                    [
                        'question_template' => 'Sistem persamaan \\( {a}x + {b}y = {c1} \\) dan \\( {a}x - {b}y = {c2} \\). Hitunglah nilai \\( {a}x \\)?',
                        'variable_rules' => [
                            'ax' => ['min' => 10, 'max' => 30],
                            'by' => ['min' => 2, 'max' => 9],
                            'a' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 2, 'max' => 5],
                            'c1' => ['type' => 'derived', 'formula' => 'ax + by'],
                            'c2' => ['type' => 'derived', 'formula' => 'ax - by'],
                        ],
                        'answer_formula' => 'ax',
                    ],
                    [
                        'question_template' => 'Selisih umur Andi dan adiknya adalah {d} tahun. Jika umur Andi adalah {a} tahun, dan Andi lebih tua, berapakah umur adiknya?',
                        'variable_rules' => [
                            'd' => ['min' => 3, 'max' => 8],
                            'a' => ['min' => 12, 'max' => 18],
                        ],
                        'answer_formula' => 'a - d',
                    ],
                    [
                        'question_template' => 'Uang Sinta {k} kali lebih banyak dari uang Doni. Jika uang Doni adalah Rp{u}.000, berapakah total uang mereka dalam ribuan rupiah?',
                        'variable_rules' => [
                            'k' => ['min' => 2, 'max' => 5],
                            'u' => ['min' => 10, 'max' => 25],
                        ],
                        'answer_formula' => 'u + (k * u)',
                    ],
                    [
                        'question_template' => 'Diketahui x = {x} dan y = {y} merupakan solusi sistem persamaan linier. Hitung nilai {p}x + {q}y.',
                        'variable_rules' => [
                            'x' => ['min' => 2, 'max' => 6],
                            'y' => ['min' => 3, 'max' => 8],
                            'p' => ['min' => 2, 'max' => 5],
                            'q' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => '(p * x) + (q * y)',
                    ],
                ]
            ],
            [
                'title' => 'Bab 5: Statistika dan Ukuran Pemusatan Data',
                'order' => 5,
                'materials' => [
                    [
                        'judul' => 'A. Mean (Rata-rata Hitung)',
                        'ringkasan' => 'Menghitung rata-rata dari kumpulan data kuantitatif.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Statistika SMP</h3><p>Rata-rata dihitung dengan membagi jumlah seluruh nilai data dengan banyaknya data.</p>',
                        'order' => 1,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Nilai ulangan matematika 4 orang siswa adalah {n1}, {n2}, {n3}, dan {n4}. Hitunglah nilai rata-rata (mean) keempat siswa tersebut?',
                        'variable_rules' => [
                            'mean' => ['min' => 105, 'max' => 135],
                            'd1' => ['min' => -18, 'max' => 18],
                            'd2' => ['min' => -12, 'max' => 12],
                            'd3' => ['min' => -6, 'max' => 6],
                            'n1' => ['type' => 'derived', 'formula' => 'mean + d1'],
                            'n2' => ['type' => 'derived', 'formula' => 'mean + d2'],
                            'n3' => ['type' => 'derived', 'formula' => 'mean + d3'],
                            'n4' => ['type' => 'derived', 'formula' => 'mean - (d1 + d2 + d3)'],
                        ],
                        'answer_formula' => '(n1 + n2 + n3 + n4) / 4',
                    ],
                    [
                        'question_template' => 'Rata-rata nilai ujian matematika dari {n} orang siswa adalah {avg}. Apabila seorang siswa baru yang nilainya {new_val} digabungkan, berapakah total nilai seluruh siswa sekarang?',
                        'variable_rules' => [
                            'n' => ['min' => 24, 'max' => 37],
                            'avg' => ['min' => 112, 'max' => 127],
                            'new_val' => ['min' => 120, 'max' => 142],
                        ],
                        'answer_formula' => '(n * avg) + new_val',
                    ],
                    [
                        'question_template' => 'Jangkauan (range) dari kumpulan data berturut-turut: {min}, {b}, {c}, {max} adalah?',
                        'variable_rules' => [
                            'min' => ['min' => 20, 'max' => 40],
                            'b' => ['min' => 45, 'max' => 60],
                            'c' => ['min' => 65, 'max' => 75],
                            'max' => ['min' => 80, 'max' => 100],
                        ],
                        'answer_formula' => 'max - min',
                    ],
                    [
                        'question_template' => 'Median dari data: {a}, {b}, {c}, {d}, {e} adalah?',
                        'variable_rules' => [
                            'a' => ['min' => 10, 'max' => 20],
                            'b' => ['min' => 25, 'max' => 40],
                            'c' => ['min' => 45, 'max' => 60],
                            'd' => ['min' => 5, 'max' => 15],
                            'e' => ['min' => 30, 'max' => 50],
                        ],
                        'answer_formula' => 'median(a, b, c, d, e)',
                        'pembahasan_template' => "Data setelah diurutkan: [[ urutkan_naik(a, b, c, d, e) ]]\nMedian = [[ median(a, b, c, d, e) ]]"
                    ],
                    [
                        'question_template' => 'Dari data {a}, {a}, {b}, {c}, {c}, {c}, nilai berapakah yang menjadi modus (nilai yang paling sering muncul)?',
                        'variable_rules' => [
                            'a' => ['min' => 5, 'max' => 8],
                            'b' => ['min' => 9, 'max' => 12],
                            'c' => ['min' => 15, 'max' => 20],
                        ],
                        'answer_formula' => 'modus(a, a, b, c, c, c)',
                        'pembahasan_template' => "Modus adalah nilai yang paling sering muncul. Dari data tersebut, modus = [[ modus(a, a, b, c, c, c) ]]"
                    ],
                    [
                        'question_template' => 'Nilai kuartil atas (Q3) dari data: {a}, {b}, {c}, {d}, {e}, {f} adalah?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 6, 'max' => 10],
                            'c' => ['min' => 12, 'max' => 15],
                            'd' => ['min' => 16, 'max' => 20],
                            'e' => ['min' => 21, 'max' => 25],
                            'f' => ['min' => 26, 'max' => 30],
                        ],
                        'answer_formula' => 'kuartil(a, b, c, d, e, f, 3)',
                        'pembahasan_template' => "Data setelah diurutkan: [[ urutkan_naik(a, b, c, d, e, f) ]]\nKuartil Atas (Q3) = [[ kuartil(a, b, c, d, e, f, 3) ]]"
                    ],
                    [
                        'question_template' => 'Rata-rata berat badan {n1} pria adalah {b1} kg dan {n2} wanita adalah {b2} kg. Hitunglah total berat badan mereka keseluruhan!',
                        'variable_rules' => [
                            'n1' => ['min' => 10, 'max' => 15],
                            'n2' => ['min' => 8, 'max' => 12],
                            'b1' => ['min' => 65, 'max' => 75],
                            'b2' => ['min' => 50, 'max' => 60],
                        ],
                        'answer_formula' => '(n1 * b1) + (n2 * b2)',
                    ],
                    [
                        'question_template' => 'Jika simpangan baku dari suatu data adalah {s}, berapakah variansinya?',
                        'variable_rules' => [
                            's' => ['min' => 4, 'max' => 12],
                        ],
                        'answer_formula' => 's * s',
                    ],
                    [
                        'question_template' => 'Rata-rata 3 nilai tes Budi adalah {r1}. Agar rata-rata 4 nilai tesnya menjadi {r2}, berapa nilai tes keempat Budi?',
                        'variable_rules' => [
                            'r2' => ['min' => 80, 'max' => 90],
                            'r1' => ['min' => 70, 'max' => 78],
                        ],
                        'answer_formula' => '(4 * r2) - (3 * r1)',
                    ],
                    [
                        'question_template' => 'Median dari data berurutan {a}, {b}, {c}, {d} adalah separuh dari jumlah {b} dan {c}. Jika \\( b = {b} \\) dan \\( c = {c} \\), maka nilai median tersebut adalah?',
                        'variable_rules' => [
                            'a' => ['min' => 1, 'max' => 4],
                            'b' => ['min' => 10, 'max' => 18, 'kelipatan' => 2],
                            'c' => ['min' => 20, 'max' => 28, 'kelipatan' => 2],
                            'd' => ['min' => 30, 'max' => 40],
                        ],
                        'answer_formula' => '(b + c) / 2',
                    ],
                ]
            ],
            // =====================================================
            // BAB BARU: Sesuai Buku Kurikulum Merdeka Kelas 8
            // =====================================================
            [
                'title' => 'Bab 6: Bilangan Berpangkat dan Bentuk Akar',
                'order' => 6,
                'materials' => [
                    [
                        'judul' => 'A. Bilangan Berpangkat Bulat',
                        'ringkasan' => 'Memahami konsep bilangan berpangkat bulat positif, negatif, dan nol beserta sifat-sifatnya.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Bilangan Berpangkat</h3><p>Bilangan berpangkat <strong>\\( a^n \\)</strong> berarti <em>a dikali sebanyak n kali</em>.</p><ul><li>\\( a^m \\times a^n = a^{m+n} \\)</li><li>\\( a^m \\div a^n = a^{m-n} \\)</li><li>\\( (a^m)^n = a^{m \\times n} \\)</li><li>\\( a^0 = 1 \\) (untuk \\( a \\neq 0 \\))</li><li>\\( a^{-n} = \\frac{1}{a^n} \\)</li></ul>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Bilangan Bentuk Akar',
                        'ringkasan' => 'Menyederhanakan bentuk akar, menjumlahkan, mengalikan, dan merasionalkan penyebut.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Bentuk Akar</h3><p>Bilangan bentuk akar adalah bilangan yang tidak dapat disederhanakan menjadi pecahan, misalnya \\( \\sqrt{2}, \\sqrt{3}, \\sqrt{5} \\).</p><ul><li>\\( \\sqrt{a \\times b} = \\sqrt{a} \\times \\sqrt{b} \\)</li><li>Merasionalkan penyebut: \\( \\frac{a}{\\sqrt{b}} = \\frac{a\\sqrt{b}}{b} \\)</li></ul>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Penulisan Bentuk Baku (Notasi Ilmiah)',
                        'ringkasan' => 'Menulis bilangan sangat besar atau sangat kecil dalam notasi ilmiah \\( a \\times 10^n \\).',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Bentuk Baku (Notasi Ilmiah)</h3><p>Notasi ilmiah: \\( a \\times 10^n \\) dimana \\( 1 \\le a < 10 \\) dan \\( n \\) bilangan bulat.</p><p>Contoh: 45.000 = \\( 4.5 \\times 10^4 \\); 0,003 = \\( 3 \\times 10^{-3} \\)</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Hitunglah hasil dari \\( {a}^{p} \\times {a}^{q} \\)? (Nyatakan hasilnya sebagai pangkat, tuliskan eksponen akhirnya saja)',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'p' => ['min' => 3, 'max' => 6],
                            'q' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => 'p + q',
                    ],
                    [
                        'question_template' => 'Sederhanakan \\( \\frac{ {a}^{m} }{ {a}^{n} } \\). Tuliskan eksponen hasilnya!',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 7],
                            'm' => ['min' => 7, 'max' => 12],
                            'n' => ['min' => 2, 'max' => 6],
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
                        'question_template' => 'Berapakah nilai dari \\( {a}^2 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 11, 'max' => 25],
                        ],
                        'answer_formula' => 'a * a',
                    ],
                    [
                        'question_template' => 'Hitunglah \\( \\sqrt{ {k} } \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 10, 'max' => 30],
                            'k' => ['type' => 'derived', 'formula' => 'x * x'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Berapakah nilai dari \\( {a}^3 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 9],
                        ],
                        'answer_formula' => 'a * a * a',
                    ],
                    [
                        'question_template' => 'Jika \\( 2^x = {val} \\), berapakah nilai \\( x \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 4, 'max' => 10],
                            'val' => ['type' => 'derived', 'formula' => '2^x'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Hitunglah hasil \\( {a}^2 \\times {a}^3 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                        ],
                        'answer_formula' => 'a^5',
                    ],
                    [
                        'question_template' => 'Sebuah bakteri membelah diri menjadi 2 setiap jam. Jika mula-mula ada {n} bakteri, berapa banyak bakteri setelah {t} jam?',
                        'variable_rules' => [
                            'n' => ['min' => 3, 'max' => 10],
                            't' => ['min' => 3, 'max' => 5],
                        ],
                        'answer_formula' => 'n * (2^t)',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari \\( {a}^4 \\div {a}^2 \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 8],
                        ],
                        'answer_formula' => 'a * a',
                    ],
                ]
            ],
            [
                'title' => 'Bab 7: Persamaan dan Pertidaksamaan Linear Satu Variabel',
                'order' => 7,
                'materials' => [
                    [
                        'judul' => 'A. Memahami Konsep PLSV',
                        'ringkasan' => 'Mengenal bentuk umum persamaan linear satu variabel dan konsep kesetaraan (kesetimbangan).',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Persamaan Linear Satu Variabel (PLSV)</h3><p>PLSV adalah kalimat terbuka berbentuk <strong>\\( ax + b = c \\)</strong>, dengan \\( a \\neq 0 \\) dan \\( x \\) adalah variabel.</p><p>Untuk menyelesaikan PLSV, kita bisa menggunakan operasi penjumlahan/pengurangan dan perkalian/pembagian pada kedua ruas.</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Menyelesaikan PLSV',
                        'ringkasan' => 'Teknik langkah demi langkah menyelesaikan persamaan linear satu variabel.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Langkah Penyelesaian PLSV</h3><ol><li>Pindahkan suku yang mengandung variabel ke satu ruas.</li><li>Pindahkan konstanta ke ruas lainnya.</li><li>Sederhanakan kedua ruas.</li><li>Bagi kedua ruas dengan koefisien variabel.</li></ol>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Pertidaksamaan Linear Satu Variabel',
                        'ringkasan' => 'Memahami pertidaksamaan (>, <, ≥, ≤) dan cara menyelesaikannya.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pertidaksamaan Linear Satu Variabel (PtLSV)</h3><p>PtLSV berbentuk \\( ax + b > c \\), \\( ax + b < c \\), atau dengan tanda \\( \\geq, \\leq \\).</p><p><strong>Penting:</strong> Jika kedua ruas dikalikan/dibagi bilangan negatif, tanda ketidaksamaan berbalik arah!</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Tentukan nilai \\( x \\) dari persamaan \\( {a}x + {b} = {c} \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 2, 'max' => 15],
                            'a' => ['min' => 2, 'max' => 8],
                            'b' => ['min' => 5, 'max' => 20],
                            'c' => ['type' => 'derived', 'formula' => '(a * x) + b'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Tentukan nilai \\( x \\) dari persamaan \\( {a}x - {b} = {c} \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 5, 'max' => 20],
                            'a' => ['min' => 3, 'max' => 7],
                            'b' => ['min' => 10, 'max' => 30],
                            'c' => ['type' => 'derived', 'formula' => '(a * x) - b'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Tentukan nilai \\( x \\) dari persamaan \\( {a}x = {b} \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 9],
                            'x' => ['min' => 4, 'max' => 12],
                            'b' => ['type' => 'derived', 'formula' => 'a * x'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Umur Ani {a} tahun lebih tua dari umur Budi. Jika jumlah umur mereka adalah {sum} tahun, berapakah umur Budi?',
                        'variable_rules' => [
                            'budi' => ['min' => 8, 'max' => 15],
                            'a' => ['min' => 3, 'max' => 7],
                            'sum' => ['type' => 'derived', 'formula' => 'budi + (budi + a)'],
                        ],
                        'answer_formula' => 'budi',
                    ],
                    [
                        'question_template' => 'Seorang pedagang memiliki {n} kg gula. Setelah menjual beberapa kg, gula yang tersisa menjadi {sisa} kg. Berapa kg gula yang dijual?',
                        'variable_rules' => [
                            'n' => ['min' => 50, 'max' => 100],
                            'sisa' => ['min' => 15, 'max' => 40],
                        ],
                        'answer_formula' => 'n - sisa',
                    ],
                    [
                        'question_template' => 'Harga {n} buku tulis adalah Rp{total}.000. Berapakah harga 1 buku tulis dalam ribuan rupiah?',
                        'variable_rules' => [
                            'harga' => ['min' => 3, 'max' => 8],
                            'n' => ['min' => 4, 'max' => 10],
                            'total' => ['type' => 'derived', 'formula' => 'harga * n'],
                        ],
                        'answer_formula' => 'harga',
                    ],
                    [
                        'question_template' => 'Selesaikan \\( 2x + {a} = {b}x + {c} \\). Berapakah nilai \\( x \\)?',
                        'variable_rules' => [
                            'x' => ['min' => 3, 'max' => 10],
                            'a' => ['min' => 10, 'max' => 30],
                            'b' => ['min' => 3, 'max' => 6],
                            'c' => ['type' => 'derived', 'formula' => 'a + (2 * x) - (b * x)'],
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Keliling persegi panjang adalah {k} cm. Jika lebarnya {l} cm, berapakah panjangnya?',
                        'variable_rules' => [
                            'p' => ['min' => 10, 'max' => 25],
                            'l' => ['min' => 4, 'max' => 9],
                            'k' => ['type' => 'derived', 'formula' => '2 * (p + l)'],
                        ],
                        'answer_formula' => 'p',
                    ],
                    [
                        'question_template' => 'Jika \\( \\frac{x}[[{a]]} = {b} \\), berapakah nilai \\( x \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 8],
                            'b' => ['min' => 5, 'max' => 12],
                        ],
                        'answer_formula' => 'a * b',
                    ],
                    [
                        'question_template' => 'Tiga kali suatu bilangan ditambah {c} sama dengan {total}. Berapakah bilangan tersebut?',
                        'variable_rules' => [
                            'x' => ['min' => 5, 'max' => 20],
                            'c' => ['min' => 10, 'max' => 30],
                            'total' => ['type' => 'derived', 'formula' => '(3 * x) + c'],
                        ],
                        'answer_formula' => 'x',
                    ],
                ]
            ],
            [
                'title' => 'Bab 8: Relasi dan Fungsi',
                'order' => 8,
                'materials' => [
                    [
                        'judul' => 'A. Memahami Relasi',
                        'ringkasan' => 'Mengenal relasi sebagai aturan yang menghubungkan elemen himpunan A ke himpunan B.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Relasi</h3><p>Relasi dari himpunan A ke himpunan B adalah aturan yang menghubungkan anggota-anggota himpunan A dengan anggota-anggota himpunan B.</p><p>Relasi dapat dinyatakan dengan: diagram panah, himpunan pasangan berurutan, atau tabel.</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Memahami Fungsi',
                        'ringkasan' => 'Mengenal fungsi sebagai relasi khusus: setiap anggota domain tepat memiliki satu pasangan di kodomain.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Fungsi (Pemetaan)</h3><p>Fungsi adalah relasi dari himpunan A ke himpunan B yang memasangkan setiap elemen A dengan tepat satu elemen B.</p><ul><li><strong>Domain</strong> (daerah asal): Himpunan A</li><li><strong>Kodomain</strong> (daerah kawan): Himpunan B</li><li><strong>Range</strong> (daerah hasil): Anggota B yang dipasangkan</li></ul><p>Rumus fungsi: \\( f(x) = ax + b \\)</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Korespondensi Satu-Satu',
                        'ringkasan' => 'Memahami korespondensi satu-satu jika setiap anggota A dipasangkan tepat satu dengan B dan sebaliknya.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Korespondensi Satu-Satu</h3><p>Korespondensi satu-satu terjadi jika setiap elemen himpunan A berpasangan tepat satu dengan elemen himpunan B, dan sebaliknya. Syaratnya: \\( n(A) = n(B) \\).</p><p>Banyaknya korespondensi satu-satu dari A ke B dengan n anggota adalah \\( n! \\) (n faktorial).</p>',
                        'order' => 3,
                    ],
                ],
                'questions' => [
                    [
                        'question_template' => 'Diketahui fungsi \\( f(x) = {a}x + {b} \\). Hitunglah nilai \\( f({c}) \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 8],
                            'b' => ['min' => 3, 'max' => 15],
                            'c' => ['min' => 2, 'max' => 10],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => '(a * c) + b',
                    ],
                    [
                        'question_template' => 'Diketahui fungsi \\( f(x) = {a}x - {b} \\). Jika \\( f(x) = {result} \\), berapakah nilai \\( x \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 6],
                            'x' => ['min' => 5, 'max' => 15],
                            'b' => ['min' => 3, 'max' => 10],
                            'result' => ['type' => 'derived', 'formula' => '(a * x) - b'],
                            'graph_formula' => 'y = {a}x - {b}',
                        ],
                        'answer_formula' => 'x',
                    ],
                    [
                        'question_template' => 'Fungsi \\( f(x) = {a}x + {b} \\) memiliki domain \\( \\{1, 2, 3, ..., {n}\\} \\). Berapakah jumlah anggota range fungsi tersebut?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 1, 'max' => 10],
                            'n' => ['min' => 4, 'max' => 8],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => 'n',
                    ],
                    [
                        'question_template' => 'Banyaknya korespondensi satu-satu dari himpunan A = \\{1, 2, 3\\} ke himpunan B = \\{a, b, c\\} adalah ... cara?',
                        'variable_rules' => [
                            'x' => ['min' => 1, 'max' => 1],
                        ],
                        'answer_formula' => '6',
                    ],
                    [
                        'question_template' => 'Diketahui \\( f(x) = {a}x^2 \\). Hitunglah nilai \\( f({c}) \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'c' => ['min' => 3, 'max' => 8],
                            'graph_formula' => 'y = {a}x^2',
                        ],
                        'answer_formula' => 'a * (c * c)',
                    ],
                    [
                        'question_template' => 'Fungsi \\( f: x \\to {a}x + {b} \\). Hitunglah \\( f({c}) - f({d}) \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 6],
                            'b' => ['min' => 3, 'max' => 10],
                            'c' => ['min' => 5, 'max' => 10],
                            'd' => ['min' => 1, 'max' => 4],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => 'a * (c - d)',
                    ],
                    [
                        'question_template' => 'Jika \\( f(x) = {a}x + {b} \\) dan \\( f(p) = {result} \\), berapakah nilai \\( p \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 3, 'max' => 7],
                            'p' => ['min' => 4, 'max' => 12],
                            'b' => ['min' => 5, 'max' => 15],
                            'result' => ['type' => 'derived', 'formula' => '(a * p) + b'],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => 'p',
                    ],
                    [
                        'question_template' => 'Himpunan A memiliki {n} anggota dan himpunan B memiliki {m} anggota. Berapa banyak pemetaan yang mungkin dari A ke B?',
                        'variable_rules' => [
                            'n' => ['min' => 2, 'max' => 3],
                            'm' => ['min' => 3, 'max' => 4],
                        ],
                        'answer_formula' => 'm^n',
                    ],
                    [
                        'question_template' => 'Fungsi \\( f(x) = {a}x + {b} \\). Jika \\( f({c1}) + f({c2}) = {s} \\), berapakah nilai \\( {a} \\times ({c1} + {c2}) + 2 \\times {b} \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 2, 'max' => 5],
                            'b' => ['min' => 1, 'max' => 8],
                            'c1' => ['min' => 2, 'max' => 6],
                            'c2' => ['min' => 3, 'max' => 7],
                            's' => ['type' => 'derived', 'formula' => '(a * c1 + b) + (a * c2 + b)'],
                            'graph_formula' => 'y = {a}x + {b}',
                        ],
                        'answer_formula' => 's',
                    ],
                    [
                        'question_template' => 'Jika \\( f(x) = x^2 - {c} \\), berapakah nilai \\( f({a}) \\)?',
                        'variable_rules' => [
                            'a' => ['min' => 5, 'max' => 12],
                            'c' => ['min' => 10, 'max' => 30],
                        ],
                        'answer_formula' => '(a * a) - c',
                    ],
                ]
            ],
            // ============================================================
            // BAB 9: PELUANG
            // ============================================================
            [
                'title' => 'Bab 9: Peluang',
                'order' => 9,
                'materials' => [
                    ['judul' => 'A. Konsep Peluang', 'ringkasan' => 'Pengertian peluang, ruang sampel, dan kejadian.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Peluang</h3><p>Peluang suatu kejadian = jumlah kejadian yang diharapkan / jumlah seluruh kemungkinan.</p><p>P(A) = n(A) / n(S)</p>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Sebuah dadu dilempar sekali. Peluang muncul angka {a} adalah...', 'variable_rules' => ['a' => ['min' => 1, 'max' => 6]], 'answer_formula' => 'peluang(1, 6)', 'pembahasan_template' => "P(muncul {a}) = 1/6\n= [[peluang(1, 6)]]"],
                    ['question_template' => 'Dalam sebuah kantong terdapat {a} bola merah dan {b} bola biru. Peluang terambil bola merah adalah... (dalam desimal)', 'variable_rules' => ['a' => ['min' => 2, 'max' => 8], 'b' => ['min' => 3, 'max' => 10]], 'answer_formula' => 'peluang(a, a + b)'],
                    ['question_template' => 'Berapa banyak cara memilih {r} orang dari {n} orang untuk menjadi panitia? (Kombinasi)', 'variable_rules' => ['n' => ['min' => 5, 'max' => 10], 'r' => ['min' => 2, 'max' => 4]], 'answer_formula' => 'kombinasi(n, r)', 'pembahasan_template' => "C({n},{r}) = {n}! / ({r}! × ({n}-{r})!)\n= [[kombinasi(n, r)]]"],
                    ['question_template' => 'Berapa banyak cara menyusun {r} buku berbeda dari {n} buku yang tersedia? (Permutasi)', 'variable_rules' => ['n' => ['min' => 5, 'max' => 8], 'r' => ['min' => 2, 'max' => 4]], 'answer_formula' => 'permutasi(n, r)', 'pembahasan_template' => "P({n},{r}) = {n}! / ({n}-{r})!\n= [[permutasi(n, r)]]"],
                    ['question_template' => 'Hitunglah {n}! (n faktorial).', 'variable_rules' => ['n' => ['min' => 3, 'max' => 8]], 'answer_formula' => 'faktorial(n)', 'pembahasan_template' => "{n}! = [[faktorial(n)]]"],
                    ['question_template' => 'Peluang hujan pada suatu hari adalah {p}%. Jika diamati selama {n} hari, berapa frekuensi harapan hari hujan?', 'variable_rules' => ['p' => ['min' => 20, 'max' => 80, 'kelipatan' => 10], 'n' => ['min' => 10, 'max' => 30]], 'answer_formula' => 'frekuensi_harapan(p / 100, n)'],
                    ['question_template' => 'Sebuah koin dilempar {n} kali. Berapa frekuensi harapan muncul gambar?', 'variable_rules' => ['n' => ['min' => 10, 'max' => 100, 'kelipatan' => 10]], 'answer_formula' => 'frekuensi_harapan(0.5, n)'],
                    ['question_template' => 'Dari {n} siswa akan dipilih {r} orang sebagai ketua, wakil, dan sekretaris. Berapa banyak susunan yang mungkin?', 'variable_rules' => ['n' => ['min' => 6, 'max' => 10], 'r' => ['min' => 3, 'max' => 3]], 'answer_formula' => 'permutasi(n, r)'],
                    ['question_template' => 'Sebuah dadu dilempar {n} kali. Berapakah frekuensi harapan munculnya angka 6?', 'variable_rules' => ['n' => ['min' => 12, 'max' => 60, 'kelipatan' => 6]], 'answer_formula' => 'frekuensi_harapan(1/6, n)'],
                    ['question_template' => 'Dari {n} soal ujian, siswa harus mengerjakan {r} soal. Berapa banyak pilihan kombinasi soal?', 'variable_rules' => ['n' => ['min' => 8, 'max' => 12], 'r' => ['min' => 4, 'max' => 6]], 'answer_formula' => 'kombinasi(n, r)'],
                ]
            ],
            // ============================================================
            // BAB 10: LINGKARAN
            // ============================================================
            [
                'title' => 'Bab 10: Lingkaran',
                'order' => 10,
                'materials' => [
                    ['judul' => 'A. Luas dan Keliling Lingkaran', 'ringkasan' => 'Rumus luas, keliling, panjang busur, dan luas juring lingkaran.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Lingkaran</h3><ul><li>Luas = π × r²</li><li>Keliling = 2 × π × r</li><li>Panjang Busur = (sudut/360) × 2πr</li><li>Luas Juring = (sudut/360) × πr²</li></ul>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Sebuah lingkaran memiliki jari-jari {r} cm. Hitunglah luasnya! (Gunakan π = 3.14159, bulatkan 2 desimal)', 'variable_rules' => ['r' => ['min' => 5, 'max' => 14]], 'answer_formula' => 'luas_lingkaran(r)', 'pembahasan_template' => "Luas = π × r²\n= π × {r}²\n= [[luas_lingkaran(r)]] cm²"],
                    ['question_template' => 'Hitunglah keliling lingkaran dengan jari-jari {r} cm! (Gunakan π = 3.14159)', 'variable_rules' => ['r' => ['min' => 5, 'max' => 14]], 'answer_formula' => 'keliling_lingkaran(r)', 'pembahasan_template' => "Keliling = 2πr\n= 2 × π × {r}\n= [[keliling_lingkaran(r)]] cm"],
                    ['question_template' => 'Hitunglah panjang busur lingkaran berjari-jari {r} cm dengan sudut pusat {s}°!', 'variable_rules' => ['r' => ['min' => 7, 'max' => 14], 's' => ['min' => 30, 'max' => 120, 'kelipatan' => 30]], 'answer_formula' => 'panjang_busur(r, s)'],
                    ['question_template' => 'Hitunglah luas juring lingkaran berjari-jari {r} cm dengan sudut pusat {s}°!', 'variable_rules' => ['r' => ['min' => 7, 'max' => 14], 's' => ['min' => 60, 'max' => 180, 'kelipatan' => 30]], 'answer_formula' => 'luas_juring(r, s)'],
                    ['question_template' => 'Sebuah taman berbentuk lingkaran dengan diameter {d} cm. Hitunglah luasnya!', 'variable_rules' => ['d' => ['min' => 10, 'max' => 28, 'kelipatan' => 2]], 'answer_formula' => 'luas_lingkaran(d / 2)'],
                    ['question_template' => 'Sebuah roda sepeda memiliki jari-jari {r} cm. Berapa jarak yang ditempuh roda dalam satu putaran penuh?', 'variable_rules' => ['r' => ['min' => 20, 'max' => 40]], 'answer_formula' => 'keliling_lingkaran(r)'],
                    ['question_template' => 'Hitunglah luas daerah yang diarsir (juring) pada lingkaran berjari-jari {r} cm jika sudut pusatnya {s}°!', 'variable_rules' => ['r' => ['min' => 10, 'max' => 21], 's' => ['min' => 45, 'max' => 270, 'kelipatan' => 45]], 'answer_formula' => 'luas_juring(r, s)'],
                    ['question_template' => 'Sebuah lingkaran memiliki keliling {k} cm. Berapakah jari-jarinya? (Bulatkan 2 desimal)', 'variable_rules' => ['k' => ['min' => 30, 'max' => 100, 'kelipatan' => 10]], 'answer_formula' => 'k / (2 * pi)'],
                    ['question_template' => 'Luas sebuah lingkaran adalah {luas} cm². Berapakah jari-jarinya? (Bulatkan ke bilangan bulat)', 'variable_rules' => ['luas' => ['min' => 50, 'max' => 300, 'kelipatan' => 50]], 'answer_formula' => 'sqrt(luas / pi)'],
                    ['question_template' => 'Panjang busur AB pada lingkaran berjari-jari {r} cm adalah {b} cm. Berapakah sudut pusatnya dalam derajat?', 'variable_rules' => ['r' => ['min' => 7, 'max' => 14], 'b' => ['min' => 5, 'max' => 20]], 'answer_formula' => '(b * 360) / (2 * pi * r)'],
                ]
            ],
            // ============================================================
            // BAB 11: BANGUN RUANG SISI DATAR
            // ============================================================
            [
                'title' => 'Bab 11: Bangun Ruang Sisi Datar',
                'order' => 11,
                'materials' => [
                    ['judul' => 'A. Volume dan Luas Permukaan', 'ringkasan' => 'Rumus volume dan luas permukaan kubus, balok, prisma, dan limas.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Bangun Ruang Sisi Datar</h3><ul><li>Volume Kubus = s³</li><li>Luas Permukaan Kubus = 6s²</li><li>Volume Balok = p × l × t</li><li>Luas Permukaan Balok = 2(pl + pt + lt)</li><li>Volume Prisma = Luas alas × tinggi</li><li>Volume Limas = ⅓ × Luas alas × tinggi</li></ul>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Sebuah kubus memiliki rusuk {s} cm. Hitunglah volumenya!', 'variable_rules' => ['s' => ['min' => 3, 'max' => 12]], 'answer_formula' => 'volume_kubus(s)', 'pembahasan_template' => "Volume = s³\n= {s}³\n= [[volume_kubus(s)]] cm³"],
                    ['question_template' => 'Sebuah kubus memiliki rusuk {s} cm. Hitunglah luas permukaannya!', 'variable_rules' => ['s' => ['min' => 3, 'max' => 12]], 'answer_formula' => 'luas_permukaan_kubus(s)', 'pembahasan_template' => "Luas Permukaan = 6s²\n= 6 × {s}²\n= [[luas_permukaan_kubus(s)]] cm²"],
                    ['question_template' => 'Sebuah balok memiliki panjang {p} cm, lebar {l} cm, dan tinggi {t} cm. Hitunglah volumenya!', 'variable_rules' => ['p' => ['min' => 5, 'max' => 15], 'l' => ['min' => 3, 'max' => 10], 't' => ['min' => 4, 'max' => 12]], 'answer_formula' => 'volume_balok(p, l, t)', 'pembahasan_template' => "Volume = p × l × t\n= {p} × {l} × {t}\n= [[volume_balok(p, l, t)]] cm³"],
                    ['question_template' => 'Hitunglah luas permukaan balok dengan panjang {p} cm, lebar {l} cm, dan tinggi {t} cm!', 'variable_rules' => ['p' => ['min' => 5, 'max' => 15], 'l' => ['min' => 3, 'max' => 10], 't' => ['min' => 4, 'max' => 12]], 'answer_formula' => 'luas_permukaan_balok(p, l, t)', 'pembahasan_template' => "Luas = 2(pl + pt + lt)\n= 2({p}×{l} + {p}×{t} + {l}×{t})\n= [[luas_permukaan_balok(p, l, t)]] cm²"],
                    ['question_template' => 'Sebuah prisma segitiga memiliki luas alas {la} cm² dan tinggi {t} cm. Hitunglah volumenya!', 'variable_rules' => ['la' => ['min' => 10, 'max' => 50], 't' => ['min' => 5, 'max' => 15]], 'answer_formula' => 'volume_prisma(la, t)', 'pembahasan_template' => "Volume = Luas alas × tinggi\n= {la} × {t}\n= [[volume_prisma(la, t)]] cm³"],
                    ['question_template' => 'Sebuah limas segiempat memiliki luas alas {la} cm² dan tinggi {t} cm. Hitunglah volumenya!', 'variable_rules' => ['la' => ['min' => 16, 'max' => 64, 'kelipatan' => 4], 't' => ['min' => 6, 'max' => 15]], 'answer_formula' => 'volume_limas(la, t)', 'pembahasan_template' => "Volume = ⅓ × Luas alas × tinggi\n= ⅓ × {la} × {t}\n= [[volume_limas(la, t)]] cm³"],
                    ['question_template' => 'Sebuah akuarium berbentuk balok memiliki panjang {p} cm, lebar {l} cm, dan tinggi {t} cm. Berapa liter air yang dapat ditampung? (1 liter = 1000 cm³)', 'variable_rules' => ['p' => ['min' => 20, 'max' => 50, 'kelipatan' => 10], 'l' => ['min' => 10, 'max' => 30, 'kelipatan' => 10], 't' => ['min' => 10, 'max' => 30, 'kelipatan' => 10]], 'answer_formula' => 'volume_balok(p, l, t) / 1000'],
                    ['question_template' => 'Sebuah kubus memiliki volume {v} cm³. Berapakah panjang rusuknya?', 'variable_rules' => ['v' => ['type' => 'set', 'set_values' => '8,27,64,125,216,343,512,729,1000']], 'answer_formula' => 'akar_pangkat(v, 3)'],
                    ['question_template' => 'Hitunglah hipotenusa segitiga siku-siku dengan sisi {a} cm dan {b} cm!', 'variable_rules' => ['a' => ['min' => 3, 'max' => 12], 'b' => ['min' => 4, 'max' => 16]], 'answer_formula' => 'hipotenusa(a, b)'],
                    ['question_template' => 'Segitiga siku-siku memiliki hipotenusa {c} cm dan satu sisi {a} cm. Berapakah sisi lainnya?', 'variable_rules' => ['c' => ['min' => 10, 'max' => 25], 'a' => ['min' => 3, 'max' => 12]], 'answer_formula' => 'sisi_siku_siku(c, a)'],
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
