<?php

namespace App\Database;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\TeksBab;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/**
 * Data contoh LMS (pengguna, kelas, bab, soal, modul) dipasang lewat migrasi,
 * bukan DatabaseSeeder, agar `php artisan migrate` langsung mengisi database.
 */
final class DataAwalLms
{
    public static function pasang(): void
    {
        if (! Schema::hasTable('kelas') || ! Schema::hasTable('materi')) {
            return;
        }

        $guru = User::firstOrCreate(
            ['email' => 'guru@example.com'],
            ['name' => 'Bapak Guru', 'password' => 'password', 'role' => 'guru']
        );

        $siswa = User::firstOrCreate(
            ['email' => 'siswa@example.com'],
            ['name' => 'Siswa Pintar', 'password' => 'password', 'role' => 'siswa']
        );

        $kelas = Kelas::firstOrCreate(
            ['class_code' => 'MTK-701'],
            [
                'name' => 'Matematika Kelas 7',
                'description' => 'Kurikulum Merdeka Belajar Matematika Interaktif',
                'tahun' => (int) date('Y'),
                'guru_id' => $guru->id,
            ]
        );

        if ($kelas->tahun === null) {
            $kelas->update(['tahun' => (int) date('Y')]);
        }

        $siswa->kelasYangDiikuti()->syncWithoutDetaching([$kelas->id]);

        foreach (self::kurikulumContoh() as $index => $data) {
            $bab = Bab::updateOrCreate(
                ['kelas_id' => $kelas->id, 'title' => $data['title']],
                ['order' => $index + 1]
            );

            TeksBab::updateOrCreate(
                ['bab_id' => $bab->id],
                ['content' => $data['content']]
            );

            foreach ($data['questions'] as $qData) {
                if ($bab->daftarSoal()->where('question_template', $qData['template'])->count() === 0) {
                    Soal::create([
                        'bab_id' => $bab->id,
                        'question_template' => $qData['template'],
                        'variable_rules' => $qData['rules'],
                        'answer_formula' => $qData['formula'],
                    ]);
                }
            }
        }

        self::perbaruiBab1BilanganBulat($kelas);
        self::pasangModulMateriBab1($kelas);
    }

    private static function perbaruiBab1BilanganBulat(Kelas $kelas): void
    {
        $bab = Bab::query()
            ->where('kelas_id', $kelas->id)
            ->where('title', 'Bab 1: Bilangan Bulat')
            ->first();

        if (! $bab) {
            return;
        }

        $kontenRingkas = <<<'HTML'
<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
  <p class="font-semibold text-gray-900 mb-1">Ringkasan bab</p>
  <p>Modul lengkap (teks, video YouTube, atau PDF) untuk bab ini ada di menu <strong class="text-brand-red">Materi</strong>. Buka halaman bab siswa lalu pilih modul yang ditampilkan.</p>
</div>
HTML;

        TeksBab::updateOrCreate(
            ['bab_id' => $bab->id],
            ['content' => $kontenRingkas]
        );

        $bab->daftarSoal()->delete();

        foreach (self::soalBab1BilanganBulat() as $q) {
            Soal::create([
                'bab_id' => $bab->id,
                'question_template' => $q['question_template'],
                'variable_rules' => $q['variable_rules'],
                'answer_formula' => $q['answer_formula'],
            ]);
        }
    }

    private static function pasangModulMateriBab1(Kelas $kelas): void
    {
        $path = database_path('fixtures/modul-bilangan-bulat.html');
        if (! is_readable($path)) {
            throw new \RuntimeException('Berkas fixture tidak ditemukan: '.$path);
        }

        $html = file_get_contents($path);
        if ($html === false || trim($html) === '') {
            throw new \RuntimeException('Isi fixture modul kosong: '.$path);
        }

        $bab = Bab::query()
            ->where('kelas_id', $kelas->id)
            ->where('title', 'Bab 1: Bilangan Bulat')
            ->first();

        $atribut = [
            'kelas_id' => $kelas->id,
            'judul' => 'Bab 1: Bilangan Bulat',
            'ringkasan' => 'Pelajari garis bilangan, operasi bilangan bulat, dan FPB/KPK — modul teks interaktif.',
            'tipe' => Materi::TIPE_TEKS,
            'konten_teks' => $html,
            'link_video' => null,
            'file_path' => null,
            'order' => 1,
        ];

        if ($bab && Schema::hasColumn('materi', 'bab_id')) {
            $atribut['bab_id'] = $bab->id;
        }

        Materi::query()->updateOrCreate(
            ['slug' => 'bilangan-bulat'],
            $atribut
        );
    }

    /** @return array<int, array{title: string, content: string, questions: array<int, array{template: string, rules: array, formula: string}>}> */
    private static function kurikulumContoh(): array
    {
        return [
            [
                'title' => 'Bab 1: Bilangan Bulat',
                'content' => '<p>Bilangan bulat terdiri dari bilangan positif, nol, dan bilangan negatif.</p><ul><li>Memahami Bilangan Bulat</li><li>Operasi Hitung</li><li>Faktor</li></ul>',
                'questions' => [
                    ['template' => 'Berapakah hasil dari {x} + {y}?', 'rules' => ['x' => ['min' => -20, 'max' => 20], 'y' => ['min' => -20, 'max' => 20]], 'formula' => 'x + y'],
                    ['template' => 'Hitunglah nilai dari {a} - {b}.', 'rules' => ['a' => ['min' => -10, 'max' => 30], 'b' => ['min' => 10, 'max' => 50]], 'formula' => 'a - b'],
                ],
            ],
            [
                'title' => 'Bab 2: Bilangan Rasional',
                'content' => '<p>Bilangan rasional adalah bilangan yang dapat dinyatakan dalam bentuk p/q di mana p dan q adalah bilangan bulat, dan q tidak sama dengan nol.</p><ul><li>Di Antara Dua Bilangan Bulat</li><li>Membandingkan</li><li>Operasi Hitung</li></ul>',
                'questions' => [
                    ['template' => 'Jika {p} / {q} dikalikan dengan {q}, hasilnya adalah?', 'rules' => ['p' => ['min' => 1, 'max' => 10], 'q' => ['min' => 2, 'max' => 10]], 'formula' => 'p'],
                ],
            ],
            [
                'title' => 'Bab 3: Rasio',
                'content' => '<p>Rasio adalah perbandingan antara dua besaran atau lebih. Sangat berguna untuk skala dan laju perubahan.</p><ul><li>Konsep Rasio</li><li>Skala</li><li>Laju Perubahan Satuan</li></ul>',
                'questions' => [
                    ['template' => 'Jika skala peta adalah 1:{skala} dan jarak pada peta adalah {jarak} cm, maka jarak sebenarnya (dalam cm) adalah?', 'rules' => ['skala' => ['min' => 100, 'max' => 1000], 'jarak' => ['min' => 2, 'max' => 10]], 'formula' => 'skala * jarak'],
                ],
            ],
            [
                'title' => 'Bab 4: Bentuk Aljabar',
                'content' => '<p>Aljabar menggunakan huruf atau simbol untuk merepresentasikan bilangan yang belum diketahui nilainya.</p><ul><li>Unsur Aljabar</li><li>Sifat & Operasi</li><li>Pemodelan</li></ul>',
                'questions' => [
                    ['template' => 'Nilai dari {a}x + {b} jika x = {x} adalah?', 'rules' => ['a' => ['min' => 2, 'max' => 5], 'b' => ['min' => 1, 'max' => 10], 'x' => ['min' => 2, 'max' => 5]], 'formula' => '(a * x) + b'],
                ],
            ],
            [
                'title' => 'Bab 5: Kesebangunan',
                'content' => '<p>Dua bangun datar dikatakan sebangun jika sudut-sudut yang bersesuaian sama besar dan sisi-sisi yang bersesuaian memiliki perbandingan yang sama.</p><ul><li>Hubungan Sudut</li><li>Arti Kesebangunan</li><li>Kesebangunan Segitiga</li></ul>',
                'questions' => [
                    ['template' => 'Sebuah segitiga memiliki sisi sejajar yang panjangnya {a} dan {b}. Jika panjang...', 'rules' => ['a' => ['min' => 2, 'max' => 5], 'b' => ['min' => 6, 'max' => 10]], 'formula' => 'a + b'],
                ],
            ],
            [
                'title' => 'Bab 6: Data dan Diagram',
                'content' => '<p>Statistika membantu kita mengumpulkan, menyusun, dan menyajikan data agar mudah dipahami.</p><ul><li>Investigasi Statistika</li><li>Macam Data</li></ul>',
                'questions' => [
                    ['template' => 'Rata-rata dari nilai {v1}, {v2}, dan {v3} adalah?', 'rules' => ['v1' => ['min' => 60, 'max' => 90], 'v2' => ['min' => 60, 'max' => 90], 'v3' => ['min' => 60, 'max' => 90]], 'formula' => '(v1 + v2 + v3) / 3'],
                ],
            ],
        ];
    }

    /** @return array<int, array{question_template: string, variable_rules: array, answer_formula: string}> */
    private static function soalBab1BilanganBulat(): array
    {
        return [
            [
                'question_template' => 'Berapakah hasil dari {a} + {b}?',
                'variable_rules' => ['a' => ['min' => -20, 'max' => 20], 'b' => ['min' => -20, 'max' => 20]],
                'answer_formula' => 'a + b',
            ],
            [
                'question_template' => 'Hitunglah: {a} - {b} = ?',
                'variable_rules' => ['a' => ['min' => -15, 'max' => 15], 'b' => ['min' => -15, 'max' => 15]],
                'answer_formula' => 'a - b',
            ],
            [
                'question_template' => 'Nilai dari {a} × {b} adalah?',
                'variable_rules' => ['a' => ['min' => -10, 'max' => 10], 'b' => ['min' => -10, 'max' => 10]],
                'answer_formula' => 'a * b',
            ],
            [
                'question_template' => 'Hasil dari ({a}) ÷ ({b}) adalah? (jawab jika hasil bulat)',
                'variable_rules' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 1, 'max' => 5]],
                'answer_formula' => 'a * b',
            ],
            [
                'question_template' => 'Pada garis bilangan, bilangan manakah yang lebih besar: {a} atau {b}? (jawab: nilai yang lebih besar)',
                'variable_rules' => ['a' => ['min' => -30, 'max' => -1], 'b' => ['min' => 1, 'max' => 30]],
                'answer_formula' => 'b',
            ],
            [
                'question_template' => 'Suhu kota A adalah {a}°C dan suhu kota B adalah {b}°C. Berapa selisih suhunya?',
                'variable_rules' => ['a' => ['min' => -20, 'max' => 0], 'b' => ['min' => 20, 'max' => 40]],
                'answer_formula' => 'b - a',
            ],
            [
                'question_template' => 'Seorang penyelam berada di kedalaman {a} meter. Ia naik {b} meter. Sekarang ia berada di kedalaman berapa meter?',
                'variable_rules' => ['a' => ['min' => 10, 'max' => 30], 'b' => ['min' => 1, 'max' => 9]],
                'answer_formula' => 'a - b',
            ],
            [
                'question_template' => 'FPB dari {a} dan {b} adalah? (Petunjuk: cari faktor persekutuan terbesar)',
                'variable_rules' => ['a' => ['min' => 1, 'max' => 6], 'b' => ['min' => 1, 'max' => 6]],
                'answer_formula' => 'a * b',
            ],
            [
                'question_template' => 'Hasil dari −{a} × (−{b}) adalah?',
                'variable_rules' => ['a' => ['min' => 2, 'max' => 12], 'b' => ['min' => 2, 'max' => 12]],
                'answer_formula' => 'a * b',
            ],
            [
                'question_template' => 'Jika a = {a} dan b = {b}, berapakah nilai dari a² − b?',
                'variable_rules' => ['a' => ['min' => 2, 'max' => 8], 'b' => ['min' => 1, 'max' => 10]],
                'answer_formula' => '(a * a) - b',
            ],
        ];
    }
}
