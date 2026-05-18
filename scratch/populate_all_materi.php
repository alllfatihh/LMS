<?php

use App\Models\Kelas;
use App\Models\Bab;
use App\Models\Materi;
use Illuminate\Support\Str;

$contents = [
    2 => [
        'title' => 'Mengenal Bilangan Rasional',
        'konten' => '
            <div>
                <h2>1. Apa itu Bilangan Rasional?</h2>
                <p>Bilangan rasional adalah bilangan yang dapat dinyatakan dalam bentuk pecahan $a/b$, di mana $a$ dan $b$ adalah bilangan bulat dan $b \neq 0$.</p>
                <ul>
                    <li>Contoh: $1/2$, $0,75$ (yang sama dengan $3/4$), dan $-5$ (yang sama dengan $-5/1$).</li>
                </ul>
                <div style="background: #fdf2f8; padding: 20px; border-radius: 15px; border: 1px solid #fbcfe8; margin-top: 20px;">
                    <h3 style="color: #9d174d;">💡 Fakta Menarik</h3>
                    <p>Semua bilangan bulat adalah bilangan rasional, tapi tidak semua bilangan rasional adalah bilangan bulat!</p>
                </div>
            </div>'
    ],
    3 => [
        'title' => 'Konsep Rasio dan Perbandingan',
        'konten' => '
            <div>
                <h2>1. Memahami Rasio</h2>
                <p>Rasio adalah cara membandingkan dua besaran. Misalnya, jika ada 2 apel dan 3 jeruk, rasio apel terhadap jeruk adalah $2:3$.</p>
                <h2>2. Penerapan Rasio</h2>
                <p>Rasio sering digunakan dalam resep masakan, peta (skala), dan kurs mata uang.</p>
                <div style="background: #f0f9ff; padding: 20px; border-radius: 15px; border: 1px solid #bae6fd; margin-top: 20px;">
                    <p><strong>Contoh:</strong> Skala peta $1:100.000$ berarti $1$ cm di peta mewakili $100.000$ cm ($1$ km) di dunia nyata.</p>
                </div>
            </div>'
    ],
    4 => [
        'title' => 'Pengenalan Bentuk Aljabar',
        'konten' => '
            <div>
                <h2>1. Unsur-unsur Aljabar</h2>
                <p>Aljabar menggunakan huruf (variabel) untuk mewakili angka yang belum diketahui.</p>
                <ul>
                    <li><strong>Variabel:</strong> Huruf seperti $x, y, z$.</li>
                    <li><strong>Koefisien:</strong> Angka di depan variabel (misal $3$ pada $3x$).</li>
                    <li><strong>Konstanta:</strong> Angka tanpa variabel (misal $5$ pada $3x + 5$).</li>
                </ul>
            </div>'
    ],
    5 => [
        'title' => 'Konsep Kesebangunan',
        'konten' => '
            <div>
                <h2>1. Apa itu Kesebangun?</h2>
                <p>Dua bangun datar dikatakan sebangun jika memiliki bentuk yang sama, meskipun ukurannya berbeda. Syaratnya:</p>
                <ul>
                    <li>Sudut-sudut yang bersesuaian sama besar.</li>
                    <li>Sisi-sisi yang bersesuaian memiliki perbandingan yang sama.</li>
                </ul>
            </div>'
    ],
    6 => [
        'title' => 'Pengolahan Data dan Diagram',
        'konten' => '
            <div>
                <h2>1. Mengumpulkan Data</h2>
                <p>Data bisa dikumpulkan melalui survei, wawancara, atau observasi.</p>
                <h2>2. Penyajian Data</h2>
                <p>Data lebih mudah dibaca jika disajikan dalam bentuk:</p>
                <ul>
                    <li><strong>Tabel:</strong> Baris dan kolom angka.</li>
                    <li><strong>Diagram Batang:</strong> Menggunakan persegi panjang.</li>
                    <li><strong>Diagram Lingkaran:</strong> Menggunakan potongan "pie".</li>
                </ul>
            </div>'
    ]
];

$allKelas = Kelas::all();

foreach ($allKelas as $kelas) {
    echo "Populating materials for Kelas: {$kelas->name}...\n";
    
    foreach ($contents as $order => $data) {
        $bab = Bab::where('kelas_id', $kelas->id)->where('order', $order)->first();
        
        if ($bab) {
            // Create only if not exists
            $exists = Materi::where('bab_id', $bab->id)->where('judul', $data['title'])->exists();
            if (!$exists) {
                Materi::create([
                    'kelas_id' => $kelas->id,
                    'bab_id' => $bab->id,
                    'judul' => $data['title'],
                    'ringkasan' => "Ringkasan konsep utama untuk " . $bab->title,
                    'slug' => Str::slug($data['title']) . '-' . $kelas->id . '-' . rand(100, 999),
                    'order' => 1,
                    'tipe' => 'teks',
                    'konten_teks' => $data['konten']
                ]);
                echo "  Added material to {$bab->title}\n";
            } else {
                echo "  Material already exists for {$bab->title}. Skipping.\n";
            }
        }
    }
}

echo "Materials populated successfully!\n";
