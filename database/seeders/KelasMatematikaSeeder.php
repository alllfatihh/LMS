<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Materi;
use App\Models\Soal;
use Illuminate\Support\Str;

class KelasMatematikaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil Pengguna
        $guru = User::where('role', 'guru')->first() ?? User::where('email', 'guru@example.com')->first();
        $siswa = User::where('role', 'siswa')->first() ?? User::where('email', 'siswa@example.com')->first();

        if (!$guru) {
            $this->command->error('User guru tidak ditemukan. Harap jalankan migrasi/seeder utama terlebih dahulu.');
            return;
        }

        // 2. Bersihkan Kelas Matematika SMP Kelas 7 lama agar tidak duplikat
        Kelas::where('name', 'Matematika SMP Kelas 7 (Kurikulum Merdeka)')->delete();

        // 3. Buat Kelas Baru
        $kodeKelas = Str::upper(Str::random(6));
        $kelas = Kelas::create([
            'name' => 'Matematika SMP Kelas 7 (Kurikulum Merdeka)',
            'description' => 'Materi lengkap dan soal latihan dinamis per bab disesuaikan dengan Buku Kemdikbud Kurikulum Merdeka 2022.',
            'tahun' => (int) date('Y'),
            'class_code' => $kodeKelas,
            'guru_id' => $guru->id,
        ]);

        // Masukkan siswa ke kelas ini
        if ($siswa) {
            $kelas->siswa()->syncWithoutDetaching([$siswa->id]);
            $this->command->info("Siswa {$siswa->name} berhasil didaftarkan ke kelas {$kelas->name}.");
        }

        // 4. Data Struktur Bab, Materi (Sub-bab), dan 10 Soal per Bab
        $chaptersData = [
            [
                'title' => 'Bab 1: Bilangan Bulat',
                'order' => 1,
                'materials' => [
                    [
                        'judul' => 'A. Memahami Bilangan Bulat',
                        'ringkasan' => 'Memahami bilangan bulat positif, negatif, dan nol serta posisinya pada garis bilangan.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pengertian Bilangan Bulat</h3><p>Bilangan bulat terdiri dari tiga kelompok bilangan:</p><ul><li><strong>Bilangan bulat positif:</strong> Bilangan yang nilainya lebih besar dari nol, yaitu {1, 2, 3, 4, ...}.</li><li><strong>Bilangan nol:</strong> Bilangan netral, yaitu {0}.</li><li><strong>Bilangan bulat negatif:</strong> Bilangan yang nilainya lebih kecil dari nol, ditulis dengan tanda minus (-), yaitu {-1, -2, -3, -4, ...}.</li></ul><p>Penerapan dalam kehidupan sehari-hari:</p><ul><li>Suhu di bawah nol derajat Celcius ditulis dengan tanda negatif, misalnya -5°C.</li><li>Kedalaman laut di bawah permukaan laut ditulis negatif, misalnya -150 meter.</li><li>Keuntungan ditulis positif (+), kerugian ditulis negatif (-).</li></ul><h3>Garis Bilangan dan Perbandingan</h3><p>Pada garis bilangan, bilangan negatif terletak di sebelah kiri nol, sedangkan bilangan positif terletak di sebelah kanan nol.</p><ul><li>Semakin ke <strong>kanan</strong> letak bilangan pada garis bilangan, nilainya semakin <strong>besar</strong>.</li><li>Semakin ke <strong>kiri</strong> letak bilangan pada garis bilangan, nilainya semakin <strong>kecil</strong>.</li></ul>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Operasi Hitung Bilangan Bulat',
                        'ringkasan' => 'Penjumlahan, pengurangan, perkalian, dan pembagian bilangan bulat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Operasi Hitung Bilangan Bulat</h3><ul><li><strong>Penjumlahan & Pengurangan:</strong> Bayangkan tanda positif sebagai uang dan tanda negatif sebagai hutang. Jika kamu memiliki uang 5 dan berhutang 8 (-8), maka sisa keadaanmu adalah berhutang 3 (-3).</li><li><strong>Perkalian & Pembagian:</strong> Aturan tanda:<ul><li>Positif ×/÷ Positif = Positif</li><li>Negatif ×/÷ Negatif = Positif</li><li>Positif ×/÷ Negatif = Negatif</li><li>Negatif ×/÷ Positif = Negatif</li></ul></li></ul>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Faktor Bilangan Bulat',
                        'ringkasan' => 'Memahami faktor, kelipatan, bilangan prima, FPB, dan KPK pada bilangan bulat.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Faktor dan Kelipatan</h3><p>Faktor dari suatu bilangan adalah bilangan-bilangan yang dapat membagi habis bilangan tersebut.</p><p><strong>FPB (Faktor Persekutuan Terbesar):</strong> Faktor terbesar yang sama dari dua bilangan atau lebih.</p><p><strong>KPK (Kelipatan Persekutuan Terkecil):</strong> Kelipatan terkecil yang sama dari dua bilangan atau lebih.</p>',
                        'order' => 3,
                    ],
                    [
                        'judul' => 'Video: Konsep Garis Bilangan',
                        'ringkasan' => 'Video penjelasan visual mengenai garis bilangan.',
                        'tipe' => Materi::TIPE_VIDEO,
                        'link_video' => 'https://www.youtube.com/watch?v=NybHckSEQBI',
                        'order' => 4,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Suhu di kota Puncak Jaya pada siang hari adalah {s} derajat Celcius. Jika pada malam harinya suhu tersebut turun sebanyak {t} derajat Celcius, berapakah suhu di kota tersebut pada malam hari?',
                        'variable_rules' => ['s' => ['min' => -15, 'max' => 15], 't' => ['min' => 6, 'max' => 25]],
                        'answer_formula' => 's - t',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Suhu awal = {s}°C\nPenurunan suhu = {t}°C\nSuhu malam hari = Suhu awal - Penurunan\n= {s} - {t}\n= [[s - t]]°C"
                    ],
                    [
                        'question_template' => 'Ibu Ratih meminjam uang di Koperasi Desa sebesar Rp{p}0.000,00. Pada bulan berikutnya, ia mampu mengembalikan sebesar Rp{q}0.000,00. Berapakah sisa utang Ibu Ratih? (Tulis angkanya saja dalam satuan Puluh Ribu, cth: jika sisa Rp100.000 ditulis 10)',
                        'variable_rules' => ['p' => ['min' => 25, 'max' => 50], 'q' => ['min' => 15, 'max' => 27]],
                        'answer_formula' => 'p - q',
                    ],
                    [
                        'question_template' => 'Suhu suatu ruangan mula-mula adalah {s}°C. Setelah pendingin ruangan dinyalakan, suhunya turun menjadi {t}°C. Berapa derajat Celcius penurunan suhu tersebut?',
                        'variable_rules' => ['s' => ['min' => 62, 'max' => 80], 't' => ['min' => 40, 'max' => 55]],
                        'answer_formula' => 's - t',
                    ],
                    [
                        'question_template' => 'Suhu di puncak gunung pada malam hari adalah {a}°C. Pada siang hari suhu naik sebesar {b}°C. Berapakah suhu pada siang hari?',
                        'variable_rules' => ['a' => ['min' => -25, 'max' => -6], 'b' => ['min' => 24, 'max' => 37]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Sebuah kapal selam berada pada kedalaman {d} meter di bawah permukaan laut. Kapal tersebut menyelam lagi sejauh {s} meter. Di kedalaman berapakah kapal selam tersebut sekarang?',
                        'variable_rules' => ['d' => ['min' => 100, 'max' => 150], 's' => ['min' => 50, 'max' => 75]],
                        'answer_formula' => 'd + s',
                    ],
                    [
                        'question_template' => 'Hasil dari perkalian {a} × ({b}) adalah?',
                        'variable_rules' => ['a' => ['min' => -24, 'max' => -6], 'b' => ['min' => 9, 'max' => 27]],
                        'answer_formula' => 'a * b',
                    ],
                    [
                        'question_template' => 'Sebuah helikopter turun dengan kecepatan {v} meter per detik. Berapa meter posisi helikopter tersebut setelah {t} detik?',
                        'variable_rules' => ['v' => ['min' => 9, 'max' => 24], 't' => ['min' => 15, 'max' => 30]],
                        'answer_formula' => 'v * t',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari {a}² - {b}?',
                        'variable_rules' => ['a' => ['min' => 15, 'max' => 25], 'b' => ['min' => 6, 'max' => 37]],
                        'answer_formula' => '(a * a) - b',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "= ({a} × {a}) - {b}\n= [[a * a]] - {b}\n= [[(a * a) - b]]"
                    ],
                    [
                        'question_template' => 'Rata-rata dari nilai {a}, {b}, dan {c} adalah?',
                        'variable_rules' => ['a' => ['min' => 90, 'max' => 135], 'b' => ['min' => 90, 'max' => 135], 'c' => ['min' => 90, 'max' => 135]],
                        'answer_formula' => '(a + b + c) / 3',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Rata-rata = (Jumlah semua nilai) / (Banyaknya nilai)\n= ({a} + {b} + {c}) / 3\n= [[a + b + c]] / 3\n= [[(a + b + c) / 3]]"
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari ({a} - {b}) × {c}?',
                        'variable_rules' => ['a' => ['min' => 50, 'max' => 100], 'b' => ['min' => 15, 'max' => 37], 'c' => ['min' => 6, 'max' => 15]],
                        'answer_formula' => '(a - b) * c',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Selesaikan operasi dalam kurung terlebih dahulu:\n= ({a} - {b}) × {c}\n= [[a - b]] × {c}\n= [[(a - b) * c]]"
                    ],
                    [
                        'question_template' => 'Pak Budi memiliki uang sejumlah Rp{a}0.000. Ia membeli beras seharga Rp{b}0.000 dan minyak goreng seharga Rp{c}0.000. Berapakah sisa uang Pak Budi? (Tulis angkanya saja dalam puluhan ribu)',
                        'variable_rules' => ['a' => ['min' => 50, 'max' => 75], 'b' => ['min' => 15, 'max' => 37], 'c' => ['min' => 6, 'max' => 24]],
                        'answer_formula' => 'a - b - c',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Sisa uang = Modal - (Harga Beras + Harga Minyak)
= {a} - ({b} + {c})
= [[a - b - c]]"
                    ],
                    [
                        'question_template' => 'Jika x = {x} dan y = {y}, berapakah hasil dari x² + 2xy + y²?',
                        'variable_rules' => ['x' => ['min' => 6, 'max' => 15], 'y' => ['min' => 6, 'max' => 15]],
                        'answer_formula' => '(x + y) * (x + y)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Gunakan rumus (x + y)² = x² + 2xy + y²
= ({x} + {y})²
= [[x + y]]²
= [[(x + y) * (x + y)]]"
                    ],
                    [
                        'question_template' => 'Hasil dari ({a} + {b}) × {c} - {d} adalah?',
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 25], 'b' => ['min' => 5, 'max' => 20], 'c' => ['min' => 3, 'max' => 8], 'd' => ['min' => 15, 'max' => 40]],
                        'answer_formula' => '((a + b) * c) - d',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Pak Ahmad memiliki sawah seluas {s} m². Ia membeli lagi {b} m², namun menjual {j} m². Luas sawahnya sekarang adalah ... m²',
                        'variable_rules' => ['s' => ['min' => 250, 'max' => 500], 'b' => ['min' => 100, 'max' => 200], 'j' => ['min' => 150, 'max' => 300]],
                        'answer_formula' => 's + b - j',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Ibu membeli {kg} kg gula dengan harga Rp{p}000 per kg dan membayar dengan uang Rp{m}000. Berapa ribu kembalian yang diterima Ibu?',
                        'variable_rules' => ['kg' => ['min' => 2, 'max' => 5], 'p' => ['min' => 12, 'max' => 18], 'm' => ['min' => 100, 'max' => 150]],
                        'answer_formula' => 'm - (kg * p)',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            [
                'title' => 'Bab 2: Bilangan Rasional',
                'order' => 2,
                'materials' => [
                    [
                        'judul' => 'A. Di Antara Dua Bilangan Bulat',
                        'ringkasan' => 'Mengenal bilangan rasional yang terletak di antara dua bilangan bulat berurutan.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pengertian Bilangan Rasional</h3><p>Bilangan rasional adalah bilangan yang dapat dinyatakan dalam bentuk <strong>a/b</strong>, dengan a dan b bilangan bulat dan b ≠ 0.</p><p>Di antara dua bilangan bulat berurutan, misalnya 0 dan 1, terdapat bilangan lain seperti ½, ¼, ¾, 0.25, 0.5 yang bukan bilangan bulat. Bilangan-bilangan inilah yang disebut bilangan rasional.</p><p>Contoh bilangan rasional: 1/2, 3/4, -2/3, 0.75, 1.5</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Membandingkan Bilangan Rasional',
                        'ringkasan' => 'Membandingkan dan mengurutkan bilangan rasional menggunakan garis bilangan.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Membandingkan Pecahan</h3><p>Untuk membandingkan dua pecahan dengan penyebut berbeda, samakan penyebutnya terlebih dahulu menggunakan KPK.</p><p>Contoh: Bandingkan 2/3 dan 3/4. KPK dari 3 dan 4 adalah 12. Maka 2/3 = 8/12 dan 3/4 = 9/12. Karena 8/12 < 9/12, maka 2/3 < 3/4.</p><h3>Pada Garis Bilangan</h3><p>Bilangan yang letaknya lebih ke kanan pada garis bilangan memiliki nilai yang lebih besar.</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Operasi Hitung Bilangan Rasional',
                        'ringkasan' => 'Penjumlahan, pengurangan, perkalian, dan pembagian pecahan serta desimal.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Penjumlahan dan Pengurangan Pecahan</h3><p>Jika penyebutnya sama: a/c + b/c = (a+b)/c. Jika berbeda, samakan penyebutnya dulu.</p><h3>Perkalian Pecahan</h3><p>a/b × c/d = (a×c)/(b×d). Contoh: 2/3 × 3/4 = 6/12 = 1/2.</p><h3>Pembagian Pecahan</h3><p>a/b ÷ c/d = a/b × d/c. Pembagian diubah menjadi perkalian dengan kebalikan pembagi.</p><h3>Operasi Desimal</h3><p>Desimal dijumlahkan atau dikurangkan dengan menyelaraskan koma desimal. Contoh: 1.5 + 2.25 = 3.75.</p>',
                        'order' => 3,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Berapakah hasil dari $\frac{ {a} }{ {c} } + \frac{ {b} }{ {c} }$? (Tulis dalam pecahan a/b)',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 3, 'max' => 15], 'c' => ['min' => 32, 'max' => 47]],
                        'answer_formula' => 'pecahan(a+b, c)',
                        'jawaban_format' => 'pecahan',
                        'pembahasan_template' => "Karena penyebutnya sudah sama yaitu {c}, maka kita tinggal menjumlahkan pembilangnya saja:\n\n= ({a} + {b}) / {c}\n= [[a + b]] / {c}",
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari $\frac{ {a} }{ {c} } - \frac{ {b} }{ {c} }$? (Tulis dalam pecahan a/b)',
                        'variable_rules' => ['a' => ['min' => 15, 'max' => 25], 'b' => ['min' => 3, 'max' => 12], 'c' => ['min' => 32, 'max' => 47]],
                        'answer_formula' => 'pecahan(a-b, c)',
                        'jawaban_format' => 'pecahan',
                        'pembahasan_template' => "Karena penyebutnya sama ({c}), kita tinggal mengurangkan pembilangnya:\n\n= ({a} - {b}) / {c}\n= [[a - b]] / {c}",
                    ],
                    [
                        // Operasi bilangan rasional — penjumlahan dua pecahan beda penyebut (jawaban pecahan)
                        'question_template' => 'Seorang tukang kayu memakai \\( \\frac{ {a} }{ {b} } \\) meter kawat, lalu memakai \\( \\frac{ {c} }{ {d} } \\) meter lagi. Berapa total panjang kawat yang dipakai? Tulis jawaban dalam bentuk pecahan a/b.',
                        'variable_rules' => [
                            'a' => ['type' => 'integer', 'min' => 1, 'max' => 5],
                            'b' => ['type' => 'integer', 'min' => 2, 'max' => 6],
                            'c' => ['type' => 'integer', 'min' => 1, 'max' => 5],
                            'd' => ['type' => 'integer', 'min' => 2, 'max' => 6],
                        ],
                        'answer_formula' => 'pecahan(a * d + c * b, b * d)',
                        'jawaban_format' => 'pecahan',
                        'bobot' => 10,
                        'pembahasan_template' => 'Samakan penyebut menjadi {b}×{d}, lalu jumlahkan pembilang: (({a}×{d}) + ({c}×{b})) / ({b}×{d}).',
                    ],
                    [
                        'question_template' => 'Ubahlah pecahan {a}/5 ke bentuk desimal (gunakan titik). Berapakah hasilnya?',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => 'a / 5',
                    ],
                    [
                        'question_template' => 'Hitung: {a}/10 + {b}/10. Tuliskan hasilnya dalam desimal (gunakan titik).',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => '(a + b) / 10',
                    ],
                    [
                        'question_template' => 'Suhu badan Andi saat sakit adalah {s},{d} derajat Celcius. Jika suhu normal adalah 36,5 derajat, berapa selisih suhunya? (Tulis pembilangnya saja jika hasilnya per 10, cth: 2.3 ditulis 23)',
                        'variable_rules' => ['s' => ['min' => 92, 'max' => 97], 'd' => ['min' => 3, 'max' => 27]],
                        'answer_formula' => '(s * 10 + d) - 365',
                    ],
                    [
                        'question_template' => 'Kecepatan internet download adalah {a},{b} Mbps dan upload {c},{d} Mbps. Berapa total kecepatan jika dijumlahkan? (Tuliskan pembilang per 10, contoh: 25.3 ditulis 253)',
                        'variable_rules' => ['a' => ['min' => 25, 'max' => 50], 'b' => ['min' => 3, 'max' => 27], 'c' => ['min' => 15, 'max' => 25], 'd' => ['min' => 3, 'max' => 27]],
                        'answer_formula' => '(a * 10 + b) + (c * 10 + d)',
                    ],
                    [
                        'question_template' => 'Bilangan manakah yang lebih besar: {a}/{c} atau {b}/{c}? Tulis pembilang yang lebih besar.',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 18, 'max' => 25], 'c' => ['min' => 27, 'max' => 30]],
                        'answer_formula' => 'b',
                    ],
                    [
                        'question_template' => 'Hasil dari {a} × ({b}/{a}) adalah?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 24], 'b' => ['min' => 9, 'max' => 37]],
                        'answer_formula' => 'b',
                    ],
                    [
                        'question_template' => "Sebuah botol berisi $\\frac{ {a} }{ {b} }$ liter air. Jika dituangkan $\frac{ {c} }{ {b} }$ liter, berapakah sisa air dalam botol?\n(Tulis dalam format pecahan a/b)",
                        'variable_rules' => ['a' => ['min' => 15, 'max' => 27], 'b' => ['min' => 25, 'max' => 25], 'c' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => 'pecahan(a-c, b)',
                        'jawaban_format' => 'pecahan',
                        'pembahasan_template' => "Sisa air = Air awal - Air yang dituangkan\n= ({a}/{b}) - ({c}/{b})\nKarena penyebut sama, kurangkan pembilangnya:\n= ({a} - {c}) / {b}\n= [[a - c]] / {b}"
                    ],
                    [
                        'question_template' => "Tentukan hasil perkalian pecahan: $\frac{ {a} }{ {b} } \times \frac{ {c} }{ {d} }$! (Tulis dalam format a/b)",
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 6, 'max' => 15], 'c' => ['min' => 3, 'max' => 15], 'd' => ['min' => 6, 'max' => 15]],
                        'answer_formula' => 'pecahan(a*c, b*d)',
                        'jawaban_format' => 'pecahan',
                        'pembahasan_template' => "Perkalian pecahan = (pembilang × pembilang) / (penyebut × penyebut)
= ({a} × {c}) / ({b} × {d})
= [[a * c]] / [[b * d]]"
                    ],
                    [
                        'question_template' => "Tentukan hasil pembagian pecahan: $\frac{ {a} }{ {b} } \div \frac{ {c} }{ {d} }$! (Tulis dalam format a/b)",
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 6, 'max' => 15], 'c' => ['min' => 3, 'max' => 15], 'd' => ['min' => 6, 'max' => 15]],
                        'answer_formula' => 'pecahan(a*d, b*c)',
                        'jawaban_format' => 'pecahan',
                        'pembahasan_template' => "Pembagian pecahan = membalik pecahan kedua lalu dikalikan
= ({a}/{b}) × ({d}/{c})
= ({a} × {d}) / ({b} × {c})
= [[a * d]] / [[b * c]]"
                    ],
                    [
                        'question_template' => "Sederhanakan hasil dari pengurangan $\frac{ {a} }{ {c} } - \frac{ {b} }{ {c} }$. (Tulis dalam pecahan)",
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 20], 'b' => ['min' => 2, 'max' => 9], 'c' => ['min' => 5, 'max' => 15]],
                        'answer_formula' => 'pecahan(a-b, c)',
                        'jawaban_format' => 'pecahan'
                    ],
                    [
                        'question_template' => 'Ubahlah pecahan {a}/20 ke desimal (gunakan titik)!',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 19]],
                        'answer_formula' => 'a / 20',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Harga sepotong kue adalah Rp{h}000. Jika dipotong menjadi {b} bagian sama besar, berapa ribu harga per bagian?',
                        'variable_rules' => ['h' => ['min' => 20, 'max' => 60], 'b' => ['min' => 4, 'max' => 10]],
                        'answer_formula' => 'h / b',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            [
                'title' => 'Bab 3: Rasio',
                'order' => 3,
                'materials' => [
                    [
                        'judul' => 'A. Konsep Rasio',
                        'ringkasan' => 'Menyatakan perbandingan dua besaran dan rasio ekuivalen.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pengertian Rasio</h3><p>Rasio adalah perbandingan antara dua besaran yang sejenis. Rasio dinyatakan dalam bentuk a : b, dibaca "a berbanding b".</p><p>Contoh: Dalam pembuatan susu cokelat, perbandingan susu dan cokelat adalah 3 : 1 artinya setiap 3 gelas susu dicampur dengan 1 gelas cokelat.</p><h3>Rasio Ekuivalen</h3><p>Dua rasio dikatakan ekuivalen jika memiliki nilai yang sama. Contoh: 2:3 ekuivalen dengan 4:6 dan 6:9.</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Skala',
                        'ringkasan' => 'Memahami konsep skala pada peta dan denah.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Skala Peta</h3><p>Skala menyatakan perbandingan antara ukuran pada gambar (peta/denah) dengan ukuran sebenarnya.</p><p>Skala 1 : 100.000 berarti 1 cm pada peta mewakili 100.000 cm = 1 km jarak sebenarnya.</p><h3>Rumus Skala</h3><p>Jarak sebenarnya = jarak pada peta × skala. Contoh: Jika skala peta 1:50.000 dan jarak pada peta 3 cm, maka jarak sebenarnya = 3 × 50.000 = 150.000 cm = 1,5 km.</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Laju Perubahan Satuan',
                        'ringkasan' => 'Menghitung laju dan konversi satuan menggunakan rasio.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Perbandingan Senilai</h3><p>Perbandingan senilai terjadi ketika dua besaran berubah secara proporsional. Jika satu besaran bertambah, besaran lainnya juga bertambah dengan rasio tetap.</p><p>Contoh: Jika 2 kg buah jeruk harganya Rp30.000, maka 5 kg = 5/2 × 30.000 = Rp75.000.</p><h3>Laju Perubahan</h3><p>Laju perubahan menyatakan seberapa cepat suatu besaran berubah terhadap besaran lain. Contoh: Kecepatan 60 km/jam artinya setiap 1 jam menempuh 60 km.</p>',
                        'order' => 3,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Jika skala peta adalah 1:{skala} dan jarak pada peta adalah {jarak} cm, maka jarak sebenarnya adalah ... cm',
                        'variable_rules' => ['skala' => ['min' => 150, 'max' => 750], 'jarak' => ['min' => 6, 'max' => 24]],
                        'answer_formula' => 'skala * jarak',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Jarak sebenarnya = jarak peta × skala\n= {jarak} × {skala}\n= [[jarak * skala]] cm"
                    ],
                    [
                        'question_template' => 'Jika harga 1 pensil adalah Rp{p}000, berapakah harga {n} pensil? (Tulis dalam ribuan)',
                        'variable_rules' => ['p' => ['min' => 6, 'max' => 15], 'n' => ['min' => 9, 'max' => 25]],
                        'answer_formula' => 'p * n',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Harga {n} pensil = {n} × Rp{p}.000\n= Rp[[n * p]].000\nKarena diminta dalam ribuan, jawabannya adalah [[n * p]]."
                    ],
                    [
                        'question_template' => 'Perbandingan umur Ayah dan Anak adalah {a} : 1. Jika umur Anak {u} tahun, berapakah umur Ayah?',
                        'variable_rules' => ['a' => ['min' => 9, 'max' => 15], 'u' => ['min' => 24, 'max' => 35]],
                        'answer_formula' => 'a * u',
                    ],
                    [
                        'question_template' => 'Dalam resep kue, perbandingan tepung dan gula adalah {a} : {b}. Jika tepung yang digunakan {t} gram, berapa gram gula yang dibutuhkan?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 12], 'b' => ['min' => 3, 'max' => 6], 't' => ['min' => 150, 'max' => 600]],
                        'answer_formula' => '(b * t) / a',
                    ],
                    [
                        'question_template' => 'Sebuah mobil melaju dengan kecepatan rata-rata {v} km/jam selama {t} jam. Berapakah jarak yang ditempuh mobil tersebut (dalam km)?',
                        'variable_rules' => ['v' => ['min' => 90, 'max' => 135], 't' => ['min' => 6, 'max' => 15]],
                        'answer_formula' => 'v * t',
                    ],
                    [
                        'question_template' => 'Untuk membuat adonan kue, perbandingan mentega dan tepung adalah 1 : {a}. Jika mentega yang digunakan {m} gram, berapa gram tepung yang dibutuhkan?',
                        'variable_rules' => ['a' => ['min' => 9, 'max' => 15], 'm' => ['min' => 75, 'max' => 225]],
                        'answer_formula' => 'a * m',
                    ],
                    [
                        'question_template' => 'Jarak kota A ke kota B pada peta adalah {d} cm. Jika skala peta 1 : {s}00, berapa jarak sebenarnya dalam cm?',
                        'variable_rules' => ['d' => ['min' => 9, 'max' => 25], 's' => ['min' => 6, 'max' => 24]],
                        'answer_formula' => 'd * s * 100',
                    ],
                    [
                        'question_template' => 'Sebuah foto diperbesar dengan perbandingan 1 : {r}. Jika lebar asli foto {l} cm, berapa lebar foto setelah diperbesar?',
                        'variable_rules' => ['r' => ['min' => 6, 'max' => 15], 'l' => ['min' => 12, 'max' => 25]],
                        'answer_formula' => 'r * l',
                    ],
                    [
                        'question_template' => 'Harga 1 kg beras adalah Rp{p}000. Berapakah harga {k} kg beras? (Tulis dalam ribuan)',
                        'variable_rules' => ['p' => ['min' => 30, 'max' => 45], 'k' => ['min' => 9, 'max' => 25]],
                        'answer_formula' => 'p * k',
                    ],
                    [
                        'question_template' => 'Kandungan cokelat murni dalam sebatang cokelat adalah {p} bagian dari {t} bagian total. Jika berat cokelat batang {w} gram, berapa gram cokelat murninya?',
                        'variable_rules' => ['p' => ['min' => 18, 'max' => 27], 't' => ['min' => 25, 'max' => 25], 'w' => ['min' => 75, 'max' => 300]],
                        'answer_formula' => '(p * w) / t',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Rasio kandungan cokelat murni adalah {p} per {t}.\nMaka berat cokelat murninya adalah:\n= ({p} / {t}) × {w} gram\n= [[(p * w) / t]] gram"
                    ],
                    [
                        'question_template' => 'Dalam sebuah keranjang, perbandingan buah apel dan jeruk adalah {a} : {b}. Jika jumlah seluruh buah adalah {t} buah, berapakah banyak buah apel?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 15], 'b' => ['min' => 9, 'max' => 18], 't' => ['min' => 50, 'max' => 90]],
                        'answer_formula' => '(a * t) / (a + b)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Rasio apel = {a}
Total rasio = {a} + {b} = [[a + b]]

Banyak apel = (Rasio apel / Total rasio) × Total buah
= ({a} / [[a + b]]) × {t}
= [[(a * t) / (a + b)]] buah"
                    ],
                    [
                        'question_template' => 'Waktu yang dibutuhkan untuk menempuh jarak {j} km dengan kecepatan {v1} km/jam adalah t jam. Jika kecepatan dinaikkan menjadi {v2} km/jam, berapakah waktu yang dibutuhkan (dalam jam)?',
                        'variable_rules' => ['j' => ['min' => 150, 'max' => 450], 'v1' => ['min' => 100, 'max' => 90], 'v2' => ['min' => 120, 'max' => 150]],
                        'answer_formula' => 'j / v2',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Waktu = Jarak / Kecepatan baru
= {j} / {v2}
= [[j / v2]] jam"
                    ],
                    [
                        'question_template' => 'Perbandingan panjang dan lebar persegi panjang adalah {p} : {l}. Jika panjangnya {x} cm, berapakah lebarnya?',
                        'variable_rules' => ['p' => ['min' => 5, 'max' => 8], 'l' => ['min' => 2, 'max' => 4], 'x' => ['min' => 20, 'max' => 40]],
                        'answer_formula' => '(x * l) / p',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Sebuah mobil menghabiskan {b} liter bensin untuk menempuh jarak {j} km. Berapa km jarak yang bisa ditempuh dengan {c} liter bensin?',
                        'variable_rules' => ['b' => ['min' => 5, 'max' => 10], 'j' => ['min' => 50, 'max' => 100], 'c' => ['min' => 12, 'max' => 20]],
                        'answer_formula' => '(j / b) * c',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Skala denah rumah adalah 1:{s}. Jika panjang kamar tidur di denah adalah {d} cm, berapakah panjang sebenarnya dalam cm?',
                        'variable_rules' => ['s' => ['min' => 50, 'max' => 100], 'd' => ['min' => 3, 'max' => 8]],
                        'answer_formula' => 's * d',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            [
                'title' => 'Bab 4: Bentuk Aljabar',
                'order' => 4,
                'materials' => [
                    [
                        'judul' => 'A. Unsur-Unsur Bentuk Aljabar',
                        'ringkasan' => 'Memahami variabel, koefisien, konstanta, dan suku dalam aljabar.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Apa itu Bentuk Aljabar?</h3><p>Bentuk aljabar adalah ekspresi matematika yang menggunakan huruf (variabel) untuk menyatakan bilangan yang belum diketahui nilainya.</p><h3>Unsur-unsur:</h3><ul><li><strong>Variabel</strong>: Huruf yang mewakili bilangan (contoh: x, y, n)</li><li><strong>Koefisien</strong>: Bilangan yang mengalikan variabel (dalam 3x, koefisiennya 3)</li><li><strong>Konstanta</strong>: Bilangan yang berdiri sendiri tanpa variabel (dalam 3x + 5, konstantanya 5)</li><li><strong>Suku</strong>: Bagian dari bentuk aljabar yang dipisahkan oleh + atau - (3x + 5 memiliki 2 suku)</li></ul>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Sifat-Sifat dan Operasi Aljabar',
                        'ringkasan' => 'Sifat komutatif, asosiatif, distributif dan operasi pada bentuk aljabar.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Suku Sejenis</h3><p>Suku-suku yang memiliki variabel and pangkat yang sama dapat dijumlahkan atau dikurangkan. Contoh: 3x + 2x = 5x, tetapi 3x + 2y tidak bisa disederhanakan.</p><h3>Sifat-Sifat Operasi</h3><ul><li><strong>Komutatif</strong>: a + b = b + a dan a × b = b × a</li><li><strong>Asosiatif</strong>: (a+b)+c = a+(b+c)</li><li><strong>Distributif</strong>: a(b+c) = ab + ac</li></ul><h3>Perkalian Aljabar</h3><p>Gunakan sifat distributif. Contoh: 2(3x + 4) = 6x + 8.</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Pemodelan dengan Bentuk Aljabar',
                        'ringkasan' => 'Menggunakan aljabar untuk memodelkan masalah dalam kehidupan sehari-hari.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Pemodelan Masalah</h3><p>Aljabar dapat digunakan untuk menyatakan masalah dalam kehidupan sehari-hari ke dalam kalimat matematika.</p><p>Contoh: "Harga n buah buku masing-masing Rp5.000 dan 1 pensil Rp2.000" ditulis 5000n + 2000.</p><h3>Luas dan Keliling</h3><p>Persegi dengan sisi s cm: Luas = s × s = s², Keliling = 4s. Kolam renang berbentuk persegi panjang dengan panjang p dan lebar l: Luas = p × l.</p>',
                        'order' => 3,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Nilai dari {a}x + {b} jika x = {x} adalah?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 24], 'b' => ['min' => 9, 'max' => 37], 'x' => ['min' => 6, 'max' => 21]],
                        'answer_formula' => '(a * x) + b',
                    ],
                    [
                        'question_template' => 'Sederhanakan: {a}x + {b}x = ... x. Berapakah koefisien x-nya?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 25], 'b' => ['min' => 9, 'max' => 30]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Jika x = {x}, berapakah nilai dari {a}x² + {b}?',
                        'variable_rules' => ['x' => ['min' => 6, 'max' => 15], 'a' => ['min' => 3, 'max' => 12], 'b' => ['min' => 9, 'max' => 25]],
                        'answer_formula' => '(a * x * x) + b',
                    ],
                    [
                        'question_template' => 'Hasil dari {a}({b}x + {c}) jika x = 1 adalah?',
                        'variable_rules' => ['a' => ['min' => 6, 'max' => 15], 'b' => ['min' => 3, 'max' => 12], 'c' => ['min' => 6, 'max' => 18]],
                        'answer_formula' => 'a * (b + c)',
                    ],
                    [
                        'question_template' => 'Sederhanakan: {a}x - {b}x. Berapakah koefisien x-nya?',
                        'variable_rules' => ['a' => ['min' => 24, 'max' => 37], 'b' => ['min' => 6, 'max' => 21]],
                        'answer_formula' => 'a - b',
                    ],
                    [
                        'question_template' => 'Harga {n} buah buku masing-masing Rp{h}000. Berapa total harga semuanya? (Tulis dalam ribuan)',
                        'variable_rules' => ['n' => ['min' => 9, 'max' => 24], 'h' => ['min' => 15, 'max' => 37]],
                        'answer_formula' => 'n * h',
                    ],
                    [
                        'question_template' => 'Keliling persegi dengan sisi {s} cm adalah ... cm',
                        'variable_rules' => ['s' => ['min' => 9, 'max' => 37]],
                        'answer_formula' => '4 * s',
                    ],
                    [
                        'question_template' => 'Luas persegi dengan sisi {s} cm adalah ... cm²',
                        'variable_rules' => ['s' => ['min' => 9, 'max' => 30]],
                        'answer_formula' => 's * s',
                    ],
                    [
                        'question_template' => "Titik A berada pada koordinat ({x1}, {y1}) dan digeser sejauh {dx} searah sumbu X dan {dy} searah sumbu Y. Tentukan koordinat titik A' yang baru!\n\n(Tulis dalam format (x,y) dengan tanda kurung, misal (2,3))",
                        'variable_rules' => ['x1' => ['min' => 3, 'max' => 15], 'y1' => ['min' => 3, 'max' => 15], 'dx' => ['min' => 6, 'max' => 12], 'dy' => ['min' => 6, 'max' => 12], 'graph_formula' => '({x1}, {y1})'],
                        'answer_formula' => 'translasi(x1, y1, dx, dy)',
                        'jawaban_format' => 'koordinat',
                        'pembahasan_template' => "Koordinat awal = ({x1}, {y1})\nPergeseran = ({dx}, {dy})\n\nTitik A' = ({x1} + {dx}, {y1} + {dy})\n= [[translasi(x1, y1, dx, dy)]]"
                    ],
                    [
                        'question_template' => "Tentukan himpunan penyelesaian dari persamaan kuadrat \$x^2 - [[a + b]]x + [[a * b]] = 0!\n\n(Tulis dalam format {x1, x2})",
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 15], 'b' => ['min' => 18, 'max' => 27]],
                        'answer_formula' => 'himpunan(a, b)',
                        'jawaban_format' => 'himpunan',
                        'pembahasan_template' => "Bentuk persamaan:\nx² - (x1+x2)x + (x1*x2) = 0\n\nMaka dari \$x^2 - [[a + b]]x + [[a * b]] = 0$, kita tahu:\n- Hasil tambah = [[a + b]]\n- Hasil kali = [[a * b]]\n\nKedua bilangan tersebut adalah {a} dan {b}. Jadi HP = [[himpunan(a, b)]]."
                    ],
                    [
                        'question_template' => "Diketahui dua buah titik P({x1}, {y1}) dan Q({x2}, {y2}). Tentukan titik tengah (midpoint) dari garis PQ!\n\n(Tulis dalam format (x,y) dengan tanda kurung)",
                        'variable_rules' => ['x1' => ['min' => 6, 'max' => 18], 'y1' => ['min' => 6, 'max' => 18], 'x2' => ['min' => 24, 'max' => 30], 'y2' => ['min' => 24, 'max' => 30], 'graph_formula' => '({x1}, {y1}), ({x2}, {y2})'],
                        'answer_formula' => 'format_titik((x1 + x2) / 2, (y1 + y2) / 2)',
                        'jawaban_format' => 'koordinat',
                        'pembahasan_template' => "Titik Tengah = ( (x1 + x2)/2, (y1 + y2)/2 )\n= ( ({x1} + {x2})/2, ({y1} + {y2})/2 )\n= [[format_titik((x1 + x2) / 2, (y1 + y2) / 2)]]"
                    ],
                    [
                        'question_template' => "Tentukan himpunan penyelesaian dari sistem pertidaksamaan x > {a} dan x < {b}! (Tulis himpunan bilangan bulat antara batas tersebut)",
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 6], 'b' => ['min' => 12, 'max' => 15]],
                        'answer_formula' => 'himpunan(2, 3)',
                        'jawaban_format' => 'himpunan',
                        'pembahasan_template' => "Bilangan bulat yang lebih besar dari {a} dan kurang dari {b} adalah 2 dan 3. HP = {2, 3}"
                    ],
                    [
                        'question_template' => 'Sederhanakan bentuk aljabar berikut: {a}x + {b}y - {c}x + {d}y. Koefisien untuk x adalah?',
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 20], 'b' => ['min' => 5, 'max' => 15], 'c' => ['min' => 2, 'max' => 8], 'd' => ['min' => 3, 'max' => 9]],
                        'answer_formula' => 'a - c',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Dari bentuk aljabar di atas, berapakah koefisien untuk y?',
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 20], 'b' => ['min' => 5, 'max' => 15], 'c' => ['min' => 2, 'max' => 8], 'd' => ['min' => 3, 'max' => 9]],
                        'answer_formula' => 'b + d',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Jika a = {a} dan b = {b}, berapakah nilai dari {c}a - {d}b?',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 7], 'b' => ['min' => 2, 'max' => 6], 'c' => ['min' => 4, 'max' => 8], 'd' => ['min' => 2, 'max' => 5]],
                        'answer_formula' => '(c * a) - (d * b)',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            [
                'title' => 'Bab 5: Kesebangunan',
                'order' => 5,
                'materials' => [
                    [
                        'judul' => 'A. Hubungan Antar Sudut',
                        'ringkasan' => 'Sudut berpelurus, berpenyiku, dan bertolak belakang.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Sudut Berpelurus (Supplementary)</h3><p>Dua sudut dikatakan berpelurus jika jumlahnya 180°. Contoh: Jika sudut A = 120°, maka pelurusnya = 180° - 120° = 60°.</p><h3>Sudut Berpenyiku (Complementary)</h3><p>Dua sudut dikatakan berpenyiku jika jumlahnya 90°. Contoh: Jika sudut B = 35°, maka penyikunya = 90° - 35° = 55°.</p><h3>Sudut Bertolak Belakang</h3><p>Dua sudut yang bertolak belakang selalu sama besar.</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Arti Kesebangunan',
                        'ringkasan' => 'Memahami bangun datar sebangun dan kongruen.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Kesebangunan</h3><p>Dua bangun dikatakan <strong>sebangun</strong> jika memiliki bentuk yang sama (sudut-sudut bersesuaian sama besar dan sisi-sisi bersesuaian sebanding).</p><p>Contoh: Sebuah maket bangunan adalah bentuk sebangun dari bangunan aslinya.</p><h3>Kekongruenan</h3><p>Dua bangun dikatakan <strong>kongruen</strong> jika bentuk dan ukurannya sama persis sehingga bisa saling menumpuk.</p>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Kesebangunan pada Segitiga',
                        'ringkasan' => 'Syarat dua segitiga sebangun dan menghitung sisi yang belum diketahui.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Segitiga Sebangun</h3><p>Dua segitiga sebangun jika sudut-sudut bersesuaiannya sama besar. Akibatnya sisi-sisi bersesuaiannya sebanding.</p><p>Jika segitiga ABC sebangun dengan segitiga DEF, maka: AB/DE = BC/EF = AC/DF.</p><h3>Menghitung Sisi yang Belum Diketahui</h3><p>Contoh: Segitiga ABC sebangun dengan DEF. AB = 6, DE = 3, BC = 8. Maka EF = (3/6) × 8 = 4.</p>',
                        'order' => 3,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Sudut A = {a}°. Berapakah pelurus sudut A (dalam derajat)?',
                        'variable_rules' => ['a' => ['min' => 75, 'max' => 225]],
                        'answer_formula' => '180 - a',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Dua sudut dikatakan berpelurus jika jumlahnya 180°.\nMaka, Pelurus Sudut A = 180° - Sudut A\n= 180° - {a}°\n= [[180 - a]]°"
                    ],
                    [
                        'question_template' => 'Sudut B = {b}°. Berapakah penyiku sudut B (dalam derajat)?',
                        'variable_rules' => ['b' => ['min' => 25, 'max' => 120]],
                        'answer_formula' => '90 - b',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Dua sudut dikatakan berpenyiku jika jumlahnya 90°.\nMaka, Penyiku Sudut B = 90° - Sudut B\n= 90° - {b}°\n= [[90 - b]]°"
                    ],
                    [
                        'question_template' => 'Sebuah persegi panjang memiliki panjang {p} cm dan lebar {l} cm. Berapakah luasnya (dalam cm²)?',
                        'variable_rules' => ['p' => ['min' => 15, 'max' => 50], 'l' => ['min' => 9, 'max' => 30]],
                        'answer_formula' => 'p * l',
                    ],
                    [
                        'question_template' => 'Keliling persegi panjang dengan panjang {p} cm dan lebar {l} cm adalah ... cm',
                        'variable_rules' => ['p' => ['min' => 15, 'max' => 62], 'l' => ['min' => 9, 'max' => 37]],
                        'answer_formula' => '2 * (p + l)',
                    ],
                    [
                        'question_template' => 'Sebuah segitiga ABC sebangun dengan segitiga DEF. Jika panjang sisi segitiga DEF adalah {s} cm, dan perbandingan sisi ABC terhadap DEF adalah {k} : 1, berapakah panjang sisi yang bersesuaian pada segitiga ABC?',
                        'variable_rules' => ['s' => ['min' => 15, 'max' => 37], 'k' => ['min' => 6, 'max' => 12]],
                        'answer_formula' => 's * k',
                    ],
                    [
                        'question_template' => 'Sebuah maket gedung dibuat dengan skala 1:{s}. Jika tinggi maket {h} cm, berapa tinggi gedung sebenarnya (dalam cm)?',
                        'variable_rules' => ['s' => ['min' => 75, 'max' => 300], 'h' => ['min' => 25, 'max' => 75]],
                        'answer_formula' => 's * h',
                    ],
                    [
                        'question_template' => 'Dua sudut bertolak belakang. Jika sudut pertama {a}°, berapa derajat sudut kedua?',
                        'variable_rules' => ['a' => ['min' => 50, 'max' => 240]],
                        'answer_formula' => 'a',
                    ],
                    [
                        'question_template' => 'Jumlah sudut dalam segitiga adalah 180°. Jika sudut A = {a}° dan sudut B = {b}°, berapakah sudut C?',
                        'variable_rules' => ['a' => ['min' => 75, 'max' => 120], 'b' => ['min' => 75, 'max' => 120]],
                        'answer_formula' => '180 - a - b',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Jumlah sudut = 180°\nSudut C = 180° - Sudut A - Sudut B\n= 180° - {a}° - {b}°\n= [[180 - a - b]]°"
                    ],
                    [
                        'question_template' => 'Sebuah jajar genjang memiliki alas {al} cm dan tinggi {t} cm. Berapakah luas jajar genjang tersebut?',
                        'variable_rules' => ['al' => ['min' => 15, 'max' => 37], 't' => ['min' => 12, 'max' => 30]],
                        'answer_formula' => 'al * t',
                    ],
                    [
                        'question_template' => 'Sebuah foto berukuran {p} cm × {l} cm diperbesar {k} kali. Berapa luas foto setelah diperbesar (dalam cm²)?',
                        'variable_rules' => ['p' => ['min' => 12, 'max' => 24], 'l' => ['min' => 9, 'max' => 18], 'k' => ['min' => 6, 'max' => 9]],
                        'answer_formula' => '(p * k) * (l * k)',
                    ],
                    [
                        'question_template' => 'Dua buah segitiga sebangun. Panjang sisi-sisi segitiga pertama adalah {a} cm, {b} cm, dan {c} cm. Jika sisi terpendek segitiga kedua adalah {a2} cm, berapakah panjang keliling segitiga kedua?',
                        'variable_rules' => ['a' => ['min' => 9, 'max' => 15], 'b' => ['min' => 18, 'max' => 24], 'c' => ['min' => 27, 'max' => 27], 'a2' => ['min' => 18, 'max' => 37]],
                        'answer_formula' => '(a2 / a) * (a + b + c)',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Faktor skala (k) = sisi terpendek kedua / sisi terpendek pertama
k = {a2} / {a} = [[a2 / a]]
Keliling segitiga pertama = {a} + {b} + {c} = [[a + b + c]]
Keliling segitiga kedua = k × keliling pertama
= [[a2 / a]] × [[a + b + c]]
= [[(a2 / a) * (a + b + c)]] cm"
                    ],
                    [
                        'question_template' => 'Sebuah tiang memiliki bayangan sepanjang {b1} m. Pada saat yang sama, sebuah pohon dengan tinggi {t2} m memiliki bayangan sepanjang {b2} m. Berapa tinggi tiang tersebut?',
                        'variable_rules' => ['b1' => ['min' => 12, 'max' => 24], 't2' => ['min' => 9, 'max' => 18], 'b2' => ['min' => 6, 'max' => 12]],
                        'answer_formula' => '(b1 * t2) / b2',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Gunakan perbandingan kesebangunan:
Tinggi Tiang / Tinggi Pohon = Bayangan Tiang / Bayangan Pohon
Tinggi Tiang = (Bayangan Tiang × Tinggi Pohon) / Bayangan Pohon
= ({b1} × {t2}) / {b2}
= [[(b1 * t2) / b2]] m"
                    ],
                    [
                        'question_template' => 'Dua segitiga siku-siku sebangun. Segitiga pertama memiliki alas {a1} cm dan tinggi {t1} cm. Jika alas segitiga kedua adalah {a2} cm, berapakah tingginya?',
                        'variable_rules' => ['a1' => ['min' => 3, 'max' => 8], 't1' => ['min' => 4, 'max' => 10], 'a2' => ['min' => 9, 'max' => 24]],
                        'answer_formula' => '(t1 * a2) / a1',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Luas trapesium dengan sisi sejajar {a} cm dan {b} cm, serta tinggi {t} cm adalah ... cm²',
                        'variable_rules' => ['a' => ['min' => 5, 'max' => 15], 'b' => ['min' => 10, 'max' => 25], 't' => ['min' => 4, 'max' => 12]],
                        'answer_formula' => '((a + b) * t) / 2',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Sebuah lingkaran memiliki jari-jari {r} cm. Tentukan diameternya!',
                        'variable_rules' => ['r' => ['min' => 7, 'max' => 21]],
                        'answer_formula' => '2 * r',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            [
                'title' => 'Bab 6: Data dan Diagram',
                'order' => 6,
                'materials' => [
                    [
                        'judul' => 'A. Investigasi Statistika',
                        'ringkasan' => 'Mengenal statistika dan cara mengumpulkan data.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Apa itu Statistika?</h3><p>Statistika adalah ilmu yang mempelajari cara mengumpulkan, mengolah, menyajikan, dan menganalisis data untuk mengambil kesimpulan.</p><p>Langkah-langkah investigasi statistika: (1) Merumuskan pertanyaan, (2) Mengumpulkan data, (3) Mengolah data, (4) Menyajikan data, (5) Menarik kesimpulan.</p>',
                        'order' => 1,
                    ],
                    [
                        'judul' => 'B. Macam-Macam Data',
                        'ringkasan' => 'Data kualitatif, kuantitatif, primer, dan sekunder.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Jenis Data</h3><ul><li><strong>Data Kuantitatif</strong>: Data berupa angka yang dapat dihitung. Contoh: tinggi badan, nilai ujian.</li><li><strong>Data Kualitatif</strong>: Data berupa kategori atau deskripsi. Contoh: warna favorit, jenis kelamin.</li><li><strong>Data Primer</strong>: Data yang dikumpulkan langsung oleh peneliti.</li><li><strong>Data Sekunder</strong>: Data yang diperoleh dari sumber lain.</li></ul>',
                        'order' => 2,
                    ],
                    [
                        'judul' => 'C. Diagram dalam Statistika',
                        'ringkasan' => 'Mengenal berbagai jenis diagram untuk menyajikan data.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Diagram dalam Statistika</h3><p>Diagram digunakan untuk menyajikan data secara visual agar lebih mudah dipahami oleh pembaca.</p><p>Beberapa diagram yang sering digunakan antara lain diagram batang, diagram lingkaran, dan diagram garis.</p>',
                        'order' => 3,
                    ],
                    [
                        'judul' => 'D. Diagram Batang',
                        'ringkasan' => 'Memahami dan membaca data menggunakan diagram batang.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Diagram Batang</h3><p>Diagram batang menggunakan persegi panjang (batang) untuk menunjukkan frekuensi data dari setiap kategori. Tinggi atau panjang batang menyatakan nilai atau frekuensi dari kategori tersebut.</p><p>Sangat berguna untuk membandingkan jumlah data antar kategori dengan cepat.</p>',
                        'order' => 4,
                    ],
                    [
                        'judul' => 'E. Diagram Lingkaran',
                        'ringkasan' => 'Memahami presentasi data menggunakan diagram lingkaran.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Diagram Lingkaran (Pie Chart)</h3><p>Diagram lingkaran membagi sebuah lingkaran menjadi beberapa sektor. Setiap sektor mewakili proporsi atau persentase dari suatu kategori terhadap keseluruhan data.</p><p>Total sudut pusat seluruh lingkaran adalah 360° yang setara dengan 100%.</p>',
                        'order' => 5,
                    ],
                    [
                        'judul' => 'F. Memilih Diagram yang Tepat',
                        'ringkasan' => 'Menentukan jenis diagram yang paling cocok untuk menyajikan data tertentu.',
                        'tipe' => Materi::TIPE_TEKS,
                        'konten_teks' => '<h3>Memilih Diagram yang Tepat</h3><p>Setiap diagram memiliki kegunaan tersendiri:</p><ul><li><strong>Diagram Batang:</strong> Cocok untuk membandingkan data kategori.</li><li><strong>Diagram Garis:</strong> Sangat baik untuk melihat perubahan data dari waktu ke waktu (tren).</li><li><strong>Diagram Lingkaran:</strong> Tepat untuk menunjukkan bagian dari keseluruhan (proporsi/persentase).</li></ul>',
                        'order' => 6,
                    ]
                ],
                'questions' => [
                    [
                        'question_template' => 'Rata-rata dari 2 nilai ujian Andi yaitu {a} dan {b} adalah?',
                        'variable_rules' => ['a' => ['min' => 90, 'max' => 150], 'b' => ['min' => 90, 'max' => 150]],
                        'answer_formula' => '(a + b) / 2',
                    ],
                    [
                        'question_template' => 'Jumlah siswa laki-laki di kelas A adalah {a} orang dan perempuan {b} orang. Berapa total seluruh siswa di kelas A?',
                        'variable_rules' => ['a' => ['min' => 30, 'max' => 50], 'b' => ['min' => 30, 'max' => 50]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Suatu data memiliki rata-rata {r} dan banyak data {n}. Berapakah total jumlah data tersebut?',
                        'variable_rules' => ['r' => ['min' => 37, 'max' => 75], 'n' => ['min' => 15, 'max' => 25]],
                        'answer_formula' => 'r * n',
                    ],
                    [
                        'question_template' => 'Dari {t} orang responden, sebanyak {p}% menyukai warna biru. Berapa orang yang menyukai warna biru?',
                        'variable_rules' => ['t' => ['min' => 300, 'max' => 300], 'p' => ['min' => 15, 'max' => 100]],
                        'answer_formula' => '(p * t) / 100',
                    ],
                    [
                        'question_template' => 'Dari {t} siswa, {a} siswa menyukai basket dan {b} siswa menyukai sepak bola. Berapa siswa yang menyukai olahraga lainnya?',
                        'variable_rules' => ['t' => ['min' => 75, 'max' => 100], 'a' => ['min' => 24, 'max' => 37], 'b' => ['min' => 24, 'max' => 37]],
                        'answer_formula' => 't - a - b',
                    ],
                    [
                        'question_template' => 'Nilai ulangan 5 siswa: {a}, {b}, {c}, {d}, {e}. Berapakah jumlah seluruh nilainya?',
                        'variable_rules' => ['a' => ['min' => 97, 'max' => 127], 'b' => ['min' => 105, 'max' => 135], 'c' => ['min' => 90, 'max' => 120], 'd' => ['min' => 112, 'max' => 142], 'e' => ['min' => 97, 'max' => 127]],
                        'answer_formula' => 'a + b + c + d + e',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Jumlah nilai = {a} + {b} + {c} + {d} + {e}\n= [[a + b + c + d + e]]"
                    ],
                    [
                        'question_template' => 'Banyak pengunjung taman wisata hari Senin {a} orang dan Selasa {b} orang. Berapa selisih pengunjung kedua hari tersebut?',
                        'variable_rules' => ['a' => ['min' => 150, 'max' => 450], 'b' => ['min' => 75, 'max' => 300]],
                        'answer_formula' => 'a - b',
                    ],
                    [
                        'question_template' => 'Toko A menjual {a} pasang sepatu di bulan Januari dan {b} pasang di bulan Februari. Berapa rata-rata penjualan per bulan?',
                        'variable_rules' => ['a' => ['min' => 50, 'max' => 90], 'b' => ['min' => 75, 'max' => 105]],
                        'answer_formula' => '(a + b) / 2',
                    ],
                    [
                        'question_template' => 'Jika {p}% dari {t} siswa menyukai matematika, berapa banyak siswa yang menyukai matematika?',
                        'variable_rules' => ['p' => ['min' => 50, 'max' => 75], 't' => ['min' => 300, 'max' => 300]],
                        'answer_formula' => '(p * t) / 100',
                        'jawaban_format' => 'angka',
                        'pembahasan_template' => "Siswa yang menyukai matematika = {p}% dari {t}\n= ({p} / 100) × {t}\n= [[(p * t) / 100]] siswa"
                    ],
                    [
                        'question_template' => 'Diagram batang menunjukkan jumlah mobil di tempat parkir: pagi {a} mobil, siang {b} mobil, sore {c} mobil. Berapa total mobil dalam sehari?',
                        'variable_rules' => ['a' => ['min' => 37, 'max' => 75], 'b' => ['min' => 62, 'max' => 75], 'c' => ['min' => 25, 'max' => 62]],
                        'answer_formula' => 'a + b + c',
                    ],
                    [
                        'question_template' => 'Dalam satu minggu, produksi telur ayam berturut-turut adalah {a}, {b}, {c}, {d}, {e}, {f}, {g} butir. Berapakah rata-rata produksi harian? (Tulis bilangan bulatnya saja)',
                        'variable_rules' => ['a' => ['min' => 30, 'max' => 50], 'b' => ['min' => 30, 'max' => 50], 'c' => ['min' => 30, 'max' => 50], 'd' => ['min' => 30, 'max' => 50], 'e' => ['min' => 30, 'max' => 50], 'f' => ['min' => 30, 'max' => 50], 'g' => ['min' => 30, 'max' => 50]],
                        'answer_formula' => '(a+b+c+d+e+f+g) / 7',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Diagram lingkaran menampilkan 4 hobi: Membaca ({a}°), Olahraga ({b}°), Melukis ({c}°). Berapa derajat sudut untuk hobi Musik?',
                        'variable_rules' => ['a' => ['min' => 60, 'max' => 100], 'b' => ['min' => 80, 'max' => 120], 'c' => ['min' => 40, 'max' => 70]],
                        'answer_formula' => '360 - a - b - c',
                        'jawaban_format' => 'angka'
                    ],
                    [
                        'question_template' => 'Jika total data diagram lingkaran di atas adalah {t} orang. Berapa orang yang hobi Membaca?',
                        'variable_rules' => ['a' => ['min' => 60, 'max' => 100], 'b' => ['min' => 80, 'max' => 120], 'c' => ['min' => 40, 'max' => 70], 't' => ['min' => 360, 'max' => 720, 'kelipatan' => 360]],
                        'answer_formula' => '(a / 360) * t',
                        'jawaban_format' => 'angka'
                    ]
                ]
            ],
            // ============================================================
            // BAB 7: HIMPUNAN
            // ============================================================
            [
                'title' => 'Bab 7: Himpunan',
                'order' => 7,
                'materials' => [
                    ['judul' => 'A. Konsep Himpunan', 'ringkasan' => 'Pengertian himpunan, anggota, dan notasi.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Himpunan</h3><p>Himpunan adalah kumpulan objek/benda yang terdefinisi dengan jelas. Contoh: A = {1, 2, 3, 4, 5}.</p><ul><li><strong>Irisan (∩):</strong> Anggota yang sama dari dua himpunan.</li><li><strong>Gabungan (∪):</strong> Semua anggota dari kedua himpunan.</li><li><strong>Selisih (−):</strong> Anggota himpunan pertama yang tidak ada di himpunan kedua.</li></ul>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Diketahui himpunan A = {A} dan B = {B}. Tentukan A ∩ B (irisan).', 'variable_rules' => ['A' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10], 'B' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10]], 'answer_formula' => 'irisan(A, B)', 'jawaban_format' => 'himpunan'],
                    ['question_template' => 'Diketahui himpunan A = {A} dan B = {B}. Tentukan A ∪ B (gabungan).', 'variable_rules' => ['A' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10], 'B' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10]], 'answer_formula' => 'gabungan(A, B)', 'jawaban_format' => 'himpunan'],
                    ['question_template' => 'Diketahui himpunan A = {A} dan B = {B}. Tentukan A − B (selisih).', 'variable_rules' => ['A' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10], 'B' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10]], 'answer_formula' => 'selisih(A, B)', 'jawaban_format' => 'himpunan'],
                    ['question_template' => 'Diketahui himpunan A = {A} dan B = {B}. Berapa banyak anggota irisan A ∩ B?', 'variable_rules' => ['A' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10], 'B' => ['type' => 'set', 'count' => 4, 'min' => 1, 'max' => 10]], 'answer_formula' => 'jumlah_anggota(irisan(A, B))', 'jawaban_format' => 'angka'],
                    ['question_template' => 'Tentukan FPB dari {a} dan {b}.', 'variable_rules' => ['a' => ['min' => 12, 'max' => 60, 'kelipatan' => 4], 'b' => ['min' => 12, 'max' => 60, 'kelipatan' => 6]], 'answer_formula' => 'fpb(a, b)'],
                    ['question_template' => 'Tentukan KPK dari {a} dan {b}.', 'variable_rules' => ['a' => ['min' => 4, 'max' => 15], 'b' => ['min' => 4, 'max' => 15]], 'answer_formula' => 'kpk(a, b)'],
                    ['question_template' => 'Hitunglah |{a}| (nilai mutlak dari {a}).', 'variable_rules' => ['a' => ['min' => -25, 'max' => -1]], 'answer_formula' => 'mutlak(a)'],
                    ['question_template' => 'Hitunglah nilai absolut dari {a} + ({b}).', 'variable_rules' => ['a' => ['min' => -20, 'max' => 10], 'b' => ['min' => -15, 'max' => 15]], 'answer_formula' => 'abs(a + b)'],
                    ['question_template' => 'Sebuah kelas memiliki {a} siswa yang suka matematika dan {b} siswa yang suka IPA. Jika {c} siswa menyukai keduanya, berapa total siswa yang suka minimal salah satu? (Gunakan rumus n(A∪B) = n(A) + n(B) - n(A∩B))', 'variable_rules' => ['a' => ['min' => 15, 'max' => 30], 'b' => ['min' => 12, 'max' => 25], 'c' => ['min' => 3, 'max' => 10]], 'answer_formula' => 'a + b - c'],
                    ['question_template' => 'Tentukan sisa pembagian {a} oleh {b}.', 'variable_rules' => ['a' => ['min' => 20, 'max' => 99], 'b' => ['min' => 3, 'max' => 12]], 'answer_formula' => 'sisa_bagi(a, b)'],
                ]
            ],
            // ============================================================
            // BAB 8: GARIS DAN SUDUT
            // ============================================================
            [
                'title' => 'Bab 8: Garis dan Sudut',
                'order' => 8,
                'materials' => [
                    ['judul' => 'A. Hubungan Antar Sudut', 'ringkasan' => 'Sudut pelurus, penyiku, dan bertolak belakang.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Hubungan Antar Sudut</h3><ul><li><strong>Sudut pelurus:</strong> Dua sudut yang jumlahnya 180°.</li><li><strong>Sudut penyiku:</strong> Dua sudut yang jumlahnya 90°.</li><li><strong>Sudut bertolak belakang:</strong> Dua sudut yang saling berhadapan dan besarnya sama.</li></ul>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Sudut A dan sudut B saling berpelurus. Jika sudut A = {a}°, berapakah besar sudut B?', 'variable_rules' => ['a' => ['min' => 30, 'max' => 150]], 'answer_formula' => 'sudut_pelurus(a)', 'pembahasan_template' => "Sudut pelurus = 180° - sudut A\n= 180° - {a}°\n= [[sudut_pelurus(a)]]°"],
                    ['question_template' => 'Sudut P dan sudut Q saling berpenyiku. Jika sudut P = {a}°, berapakah besar sudut Q?', 'variable_rules' => ['a' => ['min' => 10, 'max' => 80]], 'answer_formula' => 'sudut_penyiku(a)', 'pembahasan_template' => "Sudut penyiku = 90° - sudut P\n= 90° - {a}°\n= [[sudut_penyiku(a)]]°"],
                    ['question_template' => 'Dua garis berpotongan membentuk sudut {a}°. Berapakah besar sudut yang bertolak belakang dengannya?', 'variable_rules' => ['a' => ['min' => 20, 'max' => 160]], 'answer_formula' => 'sudut_bertolak(a)', 'pembahasan_template' => "Sudut bertolak belakang selalu sama besar.\nJadi sudut yang bertolak belakang = {a}°"],
                    ['question_template' => 'Pada segitiga ABC, sudut A = {a}° dan sudut B = {b}°. Berapakah besar sudut C?', 'variable_rules' => ['a' => ['min' => 30, 'max' => 80], 'b' => ['min' => 30, 'max' => 80]], 'answer_formula' => '180 - a - b'],
                    ['question_template' => 'Jika dua sudut berpelurus dan perbandingannya {a}:{b}, berapakah sudut yang lebih besar?', 'variable_rules' => ['a' => ['min' => 1, 'max' => 4], 'b' => ['min' => 2, 'max' => 5]], 'answer_formula' => '(180 * b) / (a + b)'],
                    ['question_template' => 'Jika dua sudut berpenyiku dan perbandingannya {a}:{b}, berapakah sudut yang lebih kecil?', 'variable_rules' => ['a' => ['min' => 1, 'max' => 3], 'b' => ['min' => 2, 'max' => 6]], 'answer_formula' => '(90 * a) / (a + b)'],
                    ['question_template' => 'Besar sudut yang terbentuk oleh jarum jam pada pukul {a}.00 adalah... derajat.', 'variable_rules' => ['a' => ['min' => 1, 'max' => 5]], 'answer_formula' => 'a * 30'],
                    ['question_template' => 'Sudut {a}° jika diputar setengah putaran (180°) akan menjadi berapa derajat total?', 'variable_rules' => ['a' => ['min' => 10, 'max' => 170]], 'answer_formula' => 'a + 180'],
                    ['question_template' => 'Dua sudut saling berpelurus. Sudut pertama lebih besar {d}° dari sudut kedua. Tentukan sudut pertama!', 'variable_rules' => ['d' => ['min' => 10, 'max' => 60, 'kelipatan' => 2]], 'answer_formula' => '(180 + d) / 2'],
                    ['question_template' => 'Dua sudut saling berpenyiku. Sudut pertama = (2×{a} + {b})°. Tentukan sudut kedua!', 'variable_rules' => ['a' => ['min' => 5, 'max' => 20], 'b' => ['min' => 1, 'max' => 10]], 'answer_formula' => 'sudut_penyiku(2 * a + b)'],
                ]
            ],
            // ============================================================
            // BAB 9: SEGITIGA DAN SEGIEMPAT
            // ============================================================
            [
                'title' => 'Bab 9: Segitiga dan Segiempat',
                'order' => 9,
                'materials' => [
                    ['judul' => 'A. Luas dan Keliling Bangun Datar', 'ringkasan' => 'Rumus luas dan keliling segitiga, persegi, persegi panjang, trapesium, dll.', 'tipe' => Materi::TIPE_TEKS, 'konten_teks' => '<h3>Rumus Bangun Datar</h3><ul><li>Luas Persegi = s²</li><li>Keliling Persegi = 4s</li><li>Luas Persegi Panjang = p × l</li><li>Keliling Persegi Panjang = 2(p + l)</li><li>Luas Segitiga = ½ × alas × tinggi</li><li>Luas Trapesium = ½ × (a + b) × t</li><li>Luas Jajar Genjang = alas × tinggi</li><li>Luas Belah Ketupat = ½ × d₁ × d₂</li><li>Luas Layang-layang = ½ × d₁ × d₂</li></ul>', 'order' => 1],
                ],
                'questions' => [
                    ['question_template' => 'Sebuah persegi memiliki sisi {s} cm. Hitunglah luasnya!', 'variable_rules' => ['s' => ['min' => 5, 'max' => 20]], 'answer_formula' => 'luas_persegi(s)', 'pembahasan_template' => "Luas persegi = s²\n= {s}²\n= [[luas_persegi(s)]] cm²"],
                    ['question_template' => 'Sebuah persegi memiliki sisi {s} cm. Hitunglah kelilingnya!', 'variable_rules' => ['s' => ['min' => 5, 'max' => 25]], 'answer_formula' => 'keliling_persegi(s)', 'pembahasan_template' => "Keliling persegi = 4 × s\n= 4 × {s}\n= [[keliling_persegi(s)]] cm"],
                    ['question_template' => 'Sebuah persegi panjang memiliki panjang {p} cm dan lebar {l} cm. Hitunglah luasnya!', 'variable_rules' => ['p' => ['min' => 8, 'max' => 25], 'l' => ['min' => 4, 'max' => 15]], 'answer_formula' => 'luas_persegi_panjang(p, l)', 'pembahasan_template' => "Luas = p × l = {p} × {l} = [[luas_persegi_panjang(p, l)]] cm²"],
                    ['question_template' => 'Hitunglah keliling persegi panjang dengan panjang {p} cm dan lebar {l} cm!', 'variable_rules' => ['p' => ['min' => 10, 'max' => 30], 'l' => ['min' => 5, 'max' => 20]], 'answer_formula' => 'keliling_persegi_panjang(p, l)', 'pembahasan_template' => "Keliling = 2(p + l) = 2({p} + {l}) = [[keliling_persegi_panjang(p, l)]] cm"],
                    ['question_template' => 'Sebuah segitiga memiliki alas {a} cm dan tinggi {t} cm. Hitunglah luasnya!', 'variable_rules' => ['a' => ['min' => 6, 'max' => 20], 't' => ['min' => 5, 'max' => 18]], 'answer_formula' => 'luas_segitiga(a, t)', 'pembahasan_template' => "Luas = ½ × alas × tinggi\n= ½ × {a} × {t}\n= [[luas_segitiga(a, t)]] cm²"],
                    ['question_template' => 'Sebuah trapesium memiliki sisi sejajar {a} cm dan {b} cm, serta tinggi {t} cm. Hitunglah luasnya!', 'variable_rules' => ['a' => ['min' => 8, 'max' => 20], 'b' => ['min' => 12, 'max' => 30], 't' => ['min' => 5, 'max' => 15]], 'answer_formula' => 'luas_trapesium(a, b, t)', 'pembahasan_template' => "Luas = ½ × (a + b) × t\n= ½ × ({a} + {b}) × {t}\n= [[luas_trapesium(a, b, t)]] cm²"],
                    ['question_template' => 'Sebuah jajar genjang memiliki alas {a} cm dan tinggi {t} cm. Hitunglah luasnya!', 'variable_rules' => ['a' => ['min' => 8, 'max' => 25], 't' => ['min' => 5, 'max' => 18]], 'answer_formula' => 'luas_jajar_genjang(a, t)', 'pembahasan_template' => "Luas = alas × tinggi\n= {a} × {t}\n= [[luas_jajar_genjang(a, t)]] cm²"],
                    ['question_template' => 'Sebuah belah ketupat memiliki diagonal {d1} cm dan {d2} cm. Hitunglah luasnya!', 'variable_rules' => ['d1' => ['min' => 6, 'max' => 20], 'd2' => ['min' => 8, 'max' => 24]], 'answer_formula' => 'luas_belah_ketupat(d1, d2)', 'pembahasan_template' => "Luas = ½ × d₁ × d₂\n= ½ × {d1} × {d2}\n= [[luas_belah_ketupat(d1, d2)]] cm²"],
                    ['question_template' => 'Sebuah layang-layang memiliki diagonal {d1} cm dan {d2} cm. Hitunglah luasnya!', 'variable_rules' => ['d1' => ['min' => 8, 'max' => 22], 'd2' => ['min' => 10, 'max' => 26]], 'answer_formula' => 'luas_layang_layang(d1, d2)', 'pembahasan_template' => "Luas = ½ × d₁ × d₂\n= ½ × {d1} × {d2}\n= [[luas_layang_layang(d1, d2)]] cm²"],
                    ['question_template' => 'Sebuah persegi panjang memiliki luas {luas} cm² dan panjang {p} cm. Berapakah lebarnya?', 'variable_rules' => ['p' => ['min' => 5, 'max' => 15], 'luas' => ['min' => 60, 'max' => 200, 'kelipatan' => 5]], 'answer_formula' => 'luas / p'],
                ]
            ]
        ];


        // 5. Simpan Bab, Materi, dan Soal ke Database secara berurutan
        foreach ($chaptersData as $cIndex => $chapter) {
            $bab = Bab::create([
                'kelas_id' => $kelas->id,
                'title' => $chapter['title'],
                'order' => $chapter['order'],
            ]);

            $this->command->info("Membuat {$chapter['title']}...");

            // Input Materi (Sub-bab)
            foreach ($chapter['materials'] as $materi) {
                Materi::create([
                    'kelas_id' => $kelas->id,
                    'bab_id' => $bab->id,
                    'judul' => $materi['judul'],
                    'slug' => Str::slug($materi['judul'] . ' ' . uniqid()),
                    'ringkasan' => $materi['ringkasan'],
                    'tipe' => $materi['tipe'],
                    'konten_teks' => $materi['konten_teks'] ?? null,
                    'link_video' => $materi['link_video'] ?? null,
                    'order' => $materi['order'],
                ]);
            }

            // Input 10 Soal Dinamis
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

        $this->command->info("Kelas '{$kelas->name}' berhasil dibuat beserta materi lengkap sub-bab dan 10 soal per bab!");
    }
}

