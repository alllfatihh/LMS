<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bab;
use App\Models\Materi;
use App\Models\Soal;
use App\Models\TeksBab;
use Illuminate\Support\Str;

$babId = 1;
$bab = Bab::find($babId);

if (!$bab) {
    die("Bab ID $babId tidak ditemukan.");
}

echo "Mengisi data untuk: " . $bab->title . "\n";

// 1. Isi Ringkasan Bab (TeksBab)
TeksBab::updateOrCreate(
    ['bab_id' => $bab->id],
    ['content' => '<div><p>Halo selamat datang di Bab Bilangan Bulat! 👋</p><p>Pada bab ini, kita akan mempelajari:</p><ul><li>Pengertian dan pengurutan bilangan bulat</li><li>Operasi penjumlahan dan pengurangan</li><li>Operasi perkalian dan pembagian</li><li>Penerapan bilangan bulat dalam kehidupan sehari-hari</li></ul><p>Silakan pelajari modul di bawah ini secara berurutan sebelum mengerjakan latihan soal.</p></div>']
);

// 2. Bersihkan Materi lama untuk bab ini agar rapi
Materi::where('bab_id', $bab->id)->delete();

// 3. Tambah Materi Teks (Pengenalan)
Materi::create([
    'kelas_id' => $bab->kelas_id,
    'bab_id' => $bab->id,
    'judul' => '1.1 Mengenal Bilangan Bulat',
    'slug' => 'mengenal-bilangan-bulat-' . uniqid(),
    'tipe' => 'teks',
    'konten_teks' => '<div><h3>Apa itu Bilangan Bulat?</h3><p>Bilangan bulat terdiri dari bilangan bulat positif, nol, dan bilangan bulat negatif.</p><blockquote>Bilangan bulat negatif < Nol < Bilangan bulat positif</blockquote><p>Contoh penggunaan dalam kehidupan sehari-hari:</p><ul><li>Suhu di bawah nol derajat (misal: -5°C)</li><li>Kedalaman di bawah permukaan laut (misal: -100 meter)</li></ul></div>',
    'order' => 1
]);

// 4. Tambah Materi Video (Youtube)
Materi::create([
    'kelas_id' => $bab->kelas_id,
    'bab_id' => $bab->id,
    'judul' => '1.2 Video Operasi Bilangan Bulat',
    'slug' => 'video-operasi-bilangan-' . uniqid(),
    'tipe' => 'video',
    'link_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Link dummy, user bisa ganti
    'order' => 2
]);

// 5. Tambah Materi PDF (Latihan Mandiri)
Materi::create([
    'kelas_id' => $bab->kelas_id,
    'bab_id' => $bab->id,
    'judul' => '1.3 Lembar Kerja Siswa (PDF)',
    'slug' => 'lks-bilangan-bulat-' . uniqid(),
    'tipe' => 'pdf',
    'file_path' => null, // Harus diupload manual lewat UI untuk testing file system
    'order' => 3
]);

// 6. Tambah Soal Latihan (10 Soal Dinamis)
Soal::where('bab_id', $bab->id)->delete();

$soalData = [
    [
        'template' => 'Hitunglah hasil dari {{a}} + {{b}}.',
        'formula' => 'a + b',
        'vars' => ['a' => ['min' => 1, 'max' => 50], 'b' => ['min' => 1, 'max' => 50]]
    ],
    [
        'template' => 'Berapakah hasil dari {{x}} - {{y}}?',
        'formula' => 'x - y',
        'vars' => ['x' => ['min' => 50, 'max' => 100], 'y' => ['min' => 1, 'max' => 49]]
    ],
    [
        'template' => 'Tentukan hasil perkalian {{n}} x {{m}}.',
        'formula' => 'n * m',
        'vars' => ['n' => ['min' => 2, 'max' => 12], 'm' => ['min' => 2, 'max' => 12]]
    ],
    [
        'template' => 'Hasil dari {{p}} : {{q}} adalah...',
        'formula' => 'p / q',
        'vars' => ['p' => ['min' => 20, 'max' => 100], 'q' => ['min' => 2, 'max' => 5]]
    ],
    [
        'template' => 'Jika suhu udara awal adalah {{t1}}°C dan turun sebesar {{t2}}°C, berapakah suhu akhirnya?',
        'formula' => 't1 - t2',
        'vars' => ['t1' => ['min' => 10, 'max' => 30], 't2' => ['min' => 5, 'max' => 15]]
    ],
    [
        'template' => 'Seorang penyelam berada di kedalaman {{d1}} meter, lalu turun lagi {{d2}} meter. Di kedalaman berapa dia sekarang? (Gunakan bilangan bulat negatif)',
        'formula' => '-(d1 + d2)',
        'vars' => ['d1' => ['min' => 5, 'max' => 20], 'd2' => ['min' => 5, 'max' => 20]]
    ],
    [
        'template' => 'Hitunglah ({{a}} + {{b}}) x {{c}}.',
        'formula' => '(a + b) * c',
        'vars' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 1, 'max' => 5], 'c' => ['min' => 2, 'max' => 4]]
    ],
    [
        'template' => 'Selesaikan: {{x}} + ({{y}} x {{z}}).',
        'formula' => 'x + (y * z)',
        'vars' => ['x' => ['min' => 10, 'max' => 20], 'y' => ['min' => 2, 'max' => 5], 'z' => ['min' => 2, 'max' => 5]]
    ],
    [
        'template' => 'Hitunglah hasil dari {{a}} - (-{{b}}).',
        'formula' => 'a + b',
        'vars' => ['a' => ['min' => 10, 'max' => 30], 'b' => ['min' => 1, 'max' => 20]]
    ],
    [
        'template' => 'Berapakah hasil dari -{{x}} + {{y}}?',
        'formula' => 'y - x',
        'vars' => ['x' => ['min' => 20, 'max' => 40], 'y' => ['min' => 10, 'max' => 50]]
    ],
];

foreach ($soalData as $s) {
    Soal::create([
        'bab_id' => $bab->id,
        'question_template' => $s['template'],
        'answer_formula' => $s['formula'],
        'variable_rules' => $s['vars'],
    ]);
}

echo "Pengisian data selesai!\n";
