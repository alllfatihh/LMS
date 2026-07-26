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
                        'question_template' => 'Suhu di kota Puncak Jaya pada siang hari adalah {{s}} derajat Celcius. Jika pada malam harinya suhu tersebut turun sebanyak {{t}} derajat Celcius, berapakah suhu di kota tersebut pada malam hari?',
                        'variable_rules' => ['s' => ['min' => -5, 'max' => 5], 't' => ['min' => 2, 'max' => 10]],
                        'answer_formula' => 's - t',
                    ],
                    [
                        'question_template' => 'Ibu Ratih meminjam uang di Koperasi Desa sebesar Rp{{p}}0.000,00. Pada bulan berikutnya, ia mampu mengembalikan sebesar Rp{{q}}0.000,00. Berapakah sisa utang Ibu Ratih? (Tulis angkanya saja dalam satuan Puluh Ribu, cth: jika sisa Rp100.000 ditulis 10)',
                        'variable_rules' => ['p' => ['min' => 10, 'max' => 20], 'q' => ['min' => 5, 'max' => 9]],
                        'answer_formula' => 'p - q',
                    ],
                    [
                        'question_template' => 'Suhu suatu ruangan mula-mula adalah {{s}}°C. Setelah pendingin ruangan dinyalakan, suhunya turun menjadi {{t}}°C. Berapa derajat Celcius penurunan suhu tersebut?',
                        'variable_rules' => ['s' => ['min' => 25, 'max' => 32], 't' => ['min' => 16, 'max' => 22]],
                        'answer_formula' => 's - t',
                    ],
                    [
                        'question_template' => 'Suhu di puncak gunung pada malam hari adalah {{a}}°C. Pada siang hari suhu naik sebesar {{b}}°C. Berapakah suhu pada siang hari?',
                        'variable_rules' => ['a' => ['min' => -10, 'max' => -2], 'b' => ['min' => 8, 'max' => 15]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Sebuah kapal selam berada pada kedalaman {{d}} meter di bawah permukaan laut. Kapal tersebut menyelam lagi sejauh {{s}} meter. Di kedalaman berapakah kapal selam tersebut sekarang?',
                        'variable_rules' => ['d' => ['min' => 40, 'max' => 100], 's' => ['min' => 20, 'max' => 50]],
                        'answer_formula' => 'd + s',
                    ],
                    [
                        'question_template' => 'Hasil dari perkalian {{a}} × ({{b}}) adalah?',
                        'variable_rules' => ['a' => ['min' => -8, 'max' => -2], 'b' => ['min' => 3, 'max' => 9]],
                        'answer_formula' => 'a * b',
                    ],
                    [
                        'question_template' => 'Sebuah helikopter turun dengan kecepatan {{v}} meter per detik. Berapa meter posisi helikopter tersebut setelah {{t}} detik?',
                        'variable_rules' => ['v' => ['min' => 3, 'max' => 8], 't' => ['min' => 5, 'max' => 12]],
                        'answer_formula' => 'v * t',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari {{a}}² - {{b}}?',
                        'variable_rules' => ['a' => ['min' => 5, 'max' => 10], 'b' => ['min' => 2, 'max' => 15]],
                        'answer_formula' => '(a * a) - b',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari {{a}} + {{b}} × {{c}}?',
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 30], 'b' => ['min' => 2, 'max' => 5], 'c' => ['min' => 3, 'max' => 6]],
                        'answer_formula' => 'a + (b * c)',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari ({{a}} - {{b}}) × {{c}}?',
                        'variable_rules' => ['a' => ['min' => 20, 'max' => 40], 'b' => ['min' => 5, 'max' => 15], 'c' => ['min' => 2, 'max' => 5]],
                        'answer_formula' => '(a - b) * c',
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
                        'question_template' => 'Berapakah hasil dari {{a}}/{{c}} + {{b}}/{{c}}? (Tulis pembilangnya saja)',
                        'variable_rules' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 1, 'max' => 5], 'c' => ['min' => 8, 'max' => 12]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Berapakah hasil dari {{a}}/{{c}} - {{b}}/{{c}}? (Tulis pembilangnya saja)',
                        'variable_rules' => ['a' => ['min' => 5, 'max' => 10], 'b' => ['min' => 1, 'max' => 4], 'c' => ['min' => 8, 'max' => 12]],
                        'answer_formula' => 'a - b',
                    ],
                    [
                        'question_template' => 'Jika {{p}} / {{q}} dikalikan dengan {{q}}, hasilnya adalah?',
                        'variable_rules' => ['p' => ['min' => 2, 'max' => 15], 'q' => ['min' => 2, 'max' => 8]],
                        'answer_formula' => 'p',
                    ],
                    [
                        'question_template' => 'Ubahlah pecahan {{a}}/5 ke bentuk desimal (gunakan titik). Berapakah hasilnya?',
                        'variable_rules' => ['a' => ['min' => 1, 'max' => 4]],
                        'answer_formula' => 'a / 5',
                    ],
                    [
                        'question_template' => 'Hitung: {{a}}/10 + {{b}}/10. Tuliskan hasilnya dalam desimal (gunakan titik).',
                        'variable_rules' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 1, 'max' => 4]],
                        'answer_formula' => '(a + b) / 10',
                    ],
                    [
                        'question_template' => 'Suhu badan Andi saat sakit adalah {{s}},{{d}} derajat Celcius. Jika suhu normal adalah 36,5 derajat, berapa selisih suhunya? (Tulis pembilangnya saja jika hasilnya per 10, cth: 2.3 ditulis 23)',
                        'variable_rules' => ['s' => ['min' => 37, 'max' => 39], 'd' => ['min' => 1, 'max' => 9]],
                        'answer_formula' => '(s * 10 + d) - 365',
                    ],
                    [
                        'question_template' => 'Kecepatan internet download adalah {{a}},{{b}} Mbps dan upload {{c}},{{d}} Mbps. Berapa total kecepatan jika dijumlahkan? (Tuliskan pembilang per 10, contoh: 25.3 ditulis 253)',
                        'variable_rules' => ['a' => ['min' => 10, 'max' => 20], 'b' => ['min' => 1, 'max' => 9], 'c' => ['min' => 5, 'max' => 10], 'd' => ['min' => 1, 'max' => 9]],
                        'answer_formula' => '(a * 10 + b) + (c * 10 + d)',
                    ],
                    [
                        'question_template' => 'Bilangan manakah yang lebih besar: {{a}}/{{c}} atau {{b}}/{{c}}? Tulis pembilang yang lebih besar.',
                        'variable_rules' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 6, 'max' => 10], 'c' => ['min' => 11, 'max' => 12]],
                        'answer_formula' => 'b',
                    ],
                    [
                        'question_template' => 'Hasil dari {{a}} × ({{b}}/{{a}}) adalah?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 8], 'b' => ['min' => 3, 'max' => 15]],
                        'answer_formula' => 'b',
                    ],
                    [
                        'question_template' => 'Sebuah botol berisi {{a}}/{{b}} liter air. Jika dituangkan {{c}}/{{b}} liter, sisa air dalam botol adalah berapa per-{{b}} liter? (Tulis pembilangnya saja)',
                        'variable_rules' => ['a' => ['min' => 5, 'max' => 9], 'b' => ['min' => 10, 'max' => 10], 'c' => ['min' => 1, 'max' => 4]],
                        'answer_formula' => 'a - c',
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
                        'question_template' => 'Jika skala peta adalah 1:{{skala}} dan jarak pada peta adalah {{jarak}} cm, maka jarak sebenarnya adalah ... cm',
                        'variable_rules' => ['skala' => ['min' => 100, 'max' => 500], 'jarak' => ['min' => 2, 'max' => 8]],
                        'answer_formula' => 'skala * jarak',
                    ],
                    [
                        'question_template' => 'Jika harga 1 pensil adalah Rp{{p}}000, berapakah harga {{n}} pensil? (Tulis dalam ribuan)',
                        'variable_rules' => ['p' => ['min' => 2, 'max' => 5], 'n' => ['min' => 3, 'max' => 10]],
                        'answer_formula' => 'p * n',
                    ],
                    [
                        'question_template' => 'Perbandingan umur Ayah dan Anak adalah {{a}} : 1. Jika umur Anak {{u}} tahun, berapakah umur Ayah?',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 5], 'u' => ['min' => 8, 'max' => 14]],
                        'answer_formula' => 'a * u',
                    ],
                    [
                        'question_template' => 'Dalam resep kue, perbandingan tepung dan gula adalah {{a}} : {{b}}. Jika tepung yang digunakan {{t}} gram, berapa gram gula yang dibutuhkan?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 4], 'b' => ['min' => 1, 'max' => 2], 't' => ['min' => 100, 'max' => 400]],
                        'answer_formula' => '(b * t) / a',
                    ],
                    [
                        'question_template' => 'Sebuah mobil melaju dengan kecepatan rata-rata {{v}} km/jam selama {{t}} jam. Berapakah jarak yang ditempuh mobil tersebut (dalam km)?',
                        'variable_rules' => ['v' => ['min' => 60, 'max' => 90], 't' => ['min' => 2, 'max' => 5]],
                        'answer_formula' => 'v * t',
                    ],
                    [
                        'question_template' => 'Untuk membuat adonan kue, perbandingan mentega dan tepung adalah 1 : {{a}}. Jika mentega yang digunakan {{m}} gram, berapa gram tepung yang dibutuhkan?',
                        'variable_rules' => ['a' => ['min' => 3, 'max' => 5], 'm' => ['min' => 50, 'max' => 150]],
                        'answer_formula' => 'a * m',
                    ],
                    [
                        'question_template' => 'Jarak kota A ke kota B pada peta adalah {{d}} cm. Jika skala peta 1 : {{s}}00, berapa jarak sebenarnya dalam cm?',
                        'variable_rules' => ['d' => ['min' => 3, 'max' => 10], 's' => ['min' => 2, 'max' => 8]],
                        'answer_formula' => 'd * s * 100',
                    ],
                    [
                        'question_template' => 'Sebuah foto diperbesar dengan perbandingan 1 : {{r}}. Jika lebar asli foto {{l}} cm, berapa lebar foto setelah diperbesar?',
                        'variable_rules' => ['r' => ['min' => 2, 'max' => 5], 'l' => ['min' => 4, 'max' => 10]],
                        'answer_formula' => 'r * l',
                    ],
                    [
                        'question_template' => 'Harga 1 kg beras adalah Rp{{p}}000. Berapakah harga {{k}} kg beras? (Tulis dalam ribuan)',
                        'variable_rules' => ['p' => ['min' => 12, 'max' => 18], 'k' => ['min' => 3, 'max' => 10]],
                        'answer_formula' => 'p * k',
                    ],
                    [
                        'question_template' => 'Kandungan cokelat murni dalam sebatang cokelat adalah {{p}} bagian dari {{t}} bagian total. Jika berat cokelat batang {{w}} gram, berapa gram cokelat murninya?',
                        'variable_rules' => ['p' => ['min' => 6, 'max' => 9], 't' => ['min' => 10, 'max' => 10], 'w' => ['min' => 50, 'max' => 200]],
                        'answer_formula' => '(p * w) / t',
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
                        'question_template' => 'Nilai dari {{a}}x + {{b}} jika x = {{x}} adalah?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 8], 'b' => ['min' => 3, 'max' => 15], 'x' => ['min' => 2, 'max' => 7]],
                        'answer_formula' => '(a * x) + b',
                    ],
                    [
                        'question_template' => 'Sederhanakan: {{a}}x + {{b}}x = ... x. Berapakah koefisien x-nya?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 10], 'b' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Jika x = {{x}}, berapakah nilai dari {{a}}x² + {{b}}?',
                        'variable_rules' => ['x' => ['min' => 2, 'max' => 5], 'a' => ['min' => 1, 'max' => 4], 'b' => ['min' => 3, 'max' => 10]],
                        'answer_formula' => '(a * x * x) + b',
                    ],
                    [
                        'question_template' => 'Hasil dari {{a}}({{b}}x + {{c}}) jika x = 1 adalah?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 5], 'b' => ['min' => 1, 'max' => 4], 'c' => ['min' => 2, 'max' => 6]],
                        'answer_formula' => 'a * (b + c)',
                    ],
                    [
                        'question_template' => 'Sederhanakan: {{a}}x - {{b}}x. Berapakah koefisien x-nya?',
                        'variable_rules' => ['a' => ['min' => 8, 'max' => 15], 'b' => ['min' => 2, 'max' => 7]],
                        'answer_formula' => 'a - b',
                    ],
                    [
                        'question_template' => 'Harga {{n}} buah buku masing-masing Rp{{h}}000. Berapa total harga semuanya? (Tulis dalam ribuan)',
                        'variable_rules' => ['n' => ['min' => 3, 'max' => 8], 'h' => ['min' => 5, 'max' => 15]],
                        'answer_formula' => 'n * h',
                    ],
                    [
                        'question_template' => 'Keliling persegi dengan sisi {{s}} cm adalah ... cm',
                        'variable_rules' => ['s' => ['min' => 3, 'max' => 15]],
                        'answer_formula' => '4 * s',
                    ],
                    [
                        'question_template' => 'Luas persegi dengan sisi {{s}} cm adalah ... cm²',
                        'variable_rules' => ['s' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => 's * s',
                    ],
                    [
                        'question_template' => 'Jika a = {{a}} dan b = {{b}}, berapakah nilai dari a² + 2ab?',
                        'variable_rules' => ['a' => ['min' => 2, 'max' => 5], 'b' => ['min' => 1, 'max' => 4]],
                        'answer_formula' => '(a * a) + (2 * a * b)',
                    ],
                    [
                        'question_template' => 'Sebuah kolam renang berbentuk persegi panjang memiliki panjang {{p}} m dan lebar {{l}} m. Berapa luas kolam?',
                        'variable_rules' => ['p' => ['min' => 10, 'max' => 25], 'l' => ['min' => 5, 'max' => 12]],
                        'answer_formula' => 'p * l',
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
                        'question_template' => 'Sudut A = {{a}}°. Berapakah pelurus sudut A (dalam derajat)?',
                        'variable_rules' => ['a' => ['min' => 30, 'max' => 150]],
                        'answer_formula' => '180 - a',
                    ],
                    [
                        'question_template' => 'Sudut B = {{b}}°. Berapakah penyiku sudut B (dalam derajat)?',
                        'variable_rules' => ['b' => ['min' => 10, 'max' => 80]],
                        'answer_formula' => '90 - b',
                    ],
                    [
                        'question_template' => 'Sebuah persegi panjang memiliki panjang {{p}} cm dan lebar {{l}} cm. Berapakah luasnya (dalam cm²)?',
                        'variable_rules' => ['p' => ['min' => 5, 'max' => 20], 'l' => ['min' => 3, 'max' => 12]],
                        'answer_formula' => 'p * l',
                    ],
                    [
                        'question_template' => 'Keliling persegi panjang dengan panjang {{p}} cm dan lebar {{l}} cm adalah ... cm',
                        'variable_rules' => ['p' => ['min' => 5, 'max' => 25], 'l' => ['min' => 3, 'max' => 15]],
                        'answer_formula' => '2 * (p + l)',
                    ],
                    [
                        'question_template' => 'Sebuah segitiga ABC sebangun dengan segitiga DEF. Jika panjang sisi segitiga DEF adalah {{s}} cm, dan perbandingan sisi ABC terhadap DEF adalah {{k}} : 1, berapakah panjang sisi yang bersesuaian pada segitiga ABC?',
                        'variable_rules' => ['s' => ['min' => 5, 'max' => 15], 'k' => ['min' => 2, 'max' => 4]],
                        'answer_formula' => 's * k',
                    ],
                    [
                        'question_template' => 'Sebuah maket gedung dibuat dengan skala 1:{{s}}. Jika tinggi maket {{h}} cm, berapa tinggi gedung sebenarnya (dalam cm)?',
                        'variable_rules' => ['s' => ['min' => 50, 'max' => 200], 'h' => ['min' => 10, 'max' => 30]],
                        'answer_formula' => 's * h',
                    ],
                    [
                        'question_template' => 'Dua sudut bertolak belakang. Jika sudut pertama {{a}}°, berapa derajat sudut kedua?',
                        'variable_rules' => ['a' => ['min' => 20, 'max' => 160]],
                        'answer_formula' => 'a',
                    ],
                    [
                        'question_template' => 'Jumlah sudut dalam segitiga adalah 180°. Jika sudut A = {{a}}° dan sudut B = {{b}}°, berapakah sudut C?',
                        'variable_rules' => ['a' => ['min' => 30, 'max' => 80], 'b' => ['min' => 30, 'max' => 80]],
                        'answer_formula' => '180 - a - b',
                    ],
                    [
                        'question_template' => 'Sebuah jajar genjang memiliki alas {{al}} cm dan tinggi {{t}} cm. Berapakah luas jajar genjang tersebut?',
                        'variable_rules' => ['al' => ['min' => 5, 'max' => 15], 't' => ['min' => 4, 'max' => 12]],
                        'answer_formula' => 'al * t',
                    ],
                    [
                        'question_template' => 'Sebuah foto berukuran {{p}} cm × {{l}} cm diperbesar {{k}} kali. Berapa luas foto setelah diperbesar (dalam cm²)?',
                        'variable_rules' => ['p' => ['min' => 4, 'max' => 8], 'l' => ['min' => 3, 'max' => 6], 'k' => ['min' => 2, 'max' => 3]],
                        'answer_formula' => '(p * k) * (l * k)',
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
                        'question_template' => 'Rata-rata dari 2 nilai ujian Andi yaitu {{a}} dan {{b}} adalah?',
                        'variable_rules' => ['a' => ['min' => 60, 'max' => 100], 'b' => ['min' => 60, 'max' => 100]],
                        'answer_formula' => '(a + b) / 2',
                    ],
                    [
                        'question_template' => 'Jumlah siswa laki-laki di kelas A adalah {{a}} orang dan perempuan {{b}} orang. Berapa total seluruh siswa di kelas A?',
                        'variable_rules' => ['a' => ['min' => 12, 'max' => 20], 'b' => ['min' => 12, 'max' => 20]],
                        'answer_formula' => 'a + b',
                    ],
                    [
                        'question_template' => 'Suatu data memiliki rata-rata {{r}} dan banyak data {{n}}. Berapakah total jumlah data tersebut?',
                        'variable_rules' => ['r' => ['min' => 15, 'max' => 30], 'n' => ['min' => 5, 'max' => 10]],
                        'answer_formula' => 'r * n',
                    ],
                    [
                        'question_template' => 'Dari {{t}} orang responden, sebanyak {{p}}% menyukai warna biru. Berapa orang yang menyukai warna biru?',
                        'variable_rules' => ['t' => ['min' => 200, 'max' => 200], 'p' => ['min' => 5, 'max' => 40]],
                        'answer_formula' => '(p * t) / 100',
                    ],
                    [
                        'question_template' => 'Dari {{t}} siswa, {{a}} siswa menyukai basket dan {{b}} siswa menyukai sepak bola. Berapa siswa yang menyukai olahraga lainnya?',
                        'variable_rules' => ['t' => ['min' => 30, 'max' => 40], 'a' => ['min' => 8, 'max' => 15], 'b' => ['min' => 8, 'max' => 15]],
                        'answer_formula' => 't - a - b',
                    ],
                    [
                        'question_template' => 'Nilai ulangan 5 siswa: {{a}}, {{b}}, {{c}}, {{d}}, {{e}}. Berapakah jumlah seluruh nilainya?',
                        'variable_rules' => ['a' => ['min' => 65, 'max' => 85], 'b' => ['min' => 70, 'max' => 90], 'c' => ['min' => 60, 'max' => 80], 'd' => ['min' => 75, 'max' => 95], 'e' => ['min' => 65, 'max' => 85]],
                        'answer_formula' => 'a + b + c + d + e',
                    ],
                    [
                        'question_template' => 'Banyak pengunjung taman wisata hari Senin {{a}} orang dan Selasa {{b}} orang. Berapa selisih pengunjung kedua hari tersebut?',
                        'variable_rules' => ['a' => ['min' => 100, 'max' => 300], 'b' => ['min' => 50, 'max' => 200]],
                        'answer_formula' => 'a - b',
                    ],
                    [
                        'question_template' => 'Toko A menjual {{a}} pasang sepatu di bulan Januari dan {{b}} pasang di bulan Februari. Berapa rata-rata penjualan per bulan?',
                        'variable_rules' => ['a' => ['min' => 20, 'max' => 60], 'b' => ['min' => 30, 'max' => 70]],
                        'answer_formula' => '(a + b) / 2',
                    ],
                    [
                        'question_template' => 'Jika {{p}}% dari {{t}} siswa menyukai matematika, berapa banyak siswa yang menyukai matematika?',
                        'variable_rules' => ['p' => ['min' => 20, 'max' => 50], 't' => ['min' => 200, 'max' => 200]],
                        'answer_formula' => '(p * t) / 100',
                    ],
                    [
                        'question_template' => 'Diagram batang menunjukkan jumlah mobil di tempat parkir: pagi {{a}} mobil, siang {{b}} mobil, sore {{c}} mobil. Berapa total mobil dalam sehari?',
                        'variable_rules' => ['a' => ['min' => 15, 'max' => 30], 'b' => ['min' => 25, 'max' => 50], 'c' => ['min' => 10, 'max' => 25]],
                        'answer_formula' => 'a + b + c',
                    ]
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
                ]);
            }
        }

        $this->command->info("Kelas '{$kelas->name}' berhasil dibuat beserta materi lengkap sub-bab dan 10 soal per bab!");
    }
}

