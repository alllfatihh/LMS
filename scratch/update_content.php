<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Materi;

// Cari materi yang sudah kita buat tadi
$materi = Materi::where('judul', 'like', '%Mengenal Bilangan Bulat%')->first();

if (!$materi) {
    die("Materi tidak ditemukan.");
}

$htmlContent = '
<div>
    <h2>1. Apa itu Bilangan Bulat?</h2>
    <p>Bilangan bulat bukan sekadar angka biasa. Ia adalah kumpulan bilangan yang utuh (tidak pecah). Bilangan bulat terdiri dari tiga bagian utama yang membentuk satu kesatuan pada garis bilangan:</p>
    <ul>
        <li><strong>Bilangan Bulat Positif:</strong> Bilangan di kanan nol ($1, 2, 3, \dots$)</li>
        <li><strong>Bilangan Nol:</strong> Titik tengah atau netral ($0$)</li>
        <li><strong>Bilangan Bulat Negatif:</strong> Bilangan di kiri nol ($-1, -2, -3, \dots$)</li>
    </ul>

    <h2>2. Visualisasi Garis Bilangan</h2>
    <p>Untuk memahami posisinya, perhatikan ilustrasi konsep di bawah ini:</p>
    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; text-align: center; border: 1px dashed #cbd5e1; margin: 20px 0;">
        <p style="font-family: monospace; font-size: 1.2rem; letter-spacing: 4px;">
            <span style="color: #ed1e28;">-3 -- -2 -- -1</span> -- <span style="font-weight: bold;">0</span> -- <span style="color: #2563eb;">1 -- 2 -- 3</span>
        </p>
        <p style="font-size: 0.75rem; color: #64748b; margin-top: 10px;">(Kiri: Negatif | Tengah: Nol | Kanan: Positif)</p>
    </div>

    <h2>3. Bilangan Bulat dalam Kehidupan Sehari-hari</h2>
    <p>Mengapa kita harus belajar angka negatif? Karena di dunia nyata, angka ini sering kita temui, contohnya:</p>
    <table>
        <thead>
            <tr>
                <th>Situasi</th>
                <th>Penggunaan Bilangan Bulat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Suhu Udara</strong></td>
                <td>Di puncak gunung yang sangat dingin, suhu bisa mencapai 5°C di bawah nol, ditulis: $-5^\circ\text{C}$.</td>
            </tr>
            <tr>
                <td><strong>Kedalaman Laut</strong></td>
                <td>Seorang penyelam berada 100 meter di bawah permukaan laut, ditulis: $-100 \text{ meter}$.</td>
            </tr>
            <tr>
                <td><strong>Laba Rugi</strong></td>
                <td>Seorang pedagang yang mengalami kerugian sebesar 50 ribu, ditulis: $-50.000$.</td>
            </tr>
        </tbody>
    </table>

    <h2>4. Membandingkan Bilangan Bulat</h2>
    <p>Aturan main pada garis bilangan:</p>
    <ul>
        <li>Semakin ke <strong>kanan</strong> posisi angka, nilainya semakin <strong>besar</strong>.</li>
        <li>Semakin ke <strong>kiri</strong> posisi angka, nilainya semakin <strong>kecil</strong>.</li>
    </ul>
    
    <div style="background: #fff1f2; padding: 15px; border-left: 4px solid #ed1e28; margin: 20px 0;">
        <p><strong>Contoh Kasus:</strong></p>
        <p>$5 > -10$ (Meskipun angka 10 terlihat besar, karena ada tanda negatif, nilainya jauh lebih kecil dari 5).</p>
        <p>$-2 > -5$ (Suhu $-2^\circ\text{C}$ lebih hangat/tinggi daripada $-5^\circ\text{C}$).</p>
    </div>

    <div style="background: #f0fdf4; padding: 20px; border-radius: 15px; border: 1px solid #dcfce7; margin-top: 30px;">
        <h3 style="margin-top: 0; color: #166534;">💡 Tips Belajar (Anti-Gravity Style)</h3>
        <p>Ingatlah bahwa tanda negatif ($-$) itu seperti <strong>"Hutang"</strong> dan tanda positif ($+$) itu seperti <strong>"Uang"</strong>.</p>
        <ul>
            <li>$-10$ berarti hutang kamu banyak, jadi posisi kamu makin "rendah" secara ekonomi.</li>
            <li>$10$ berarti kamu punya uang, jadi posisi kamu aman dan "tinggi".</li>
        </ul>
    </div>
</div>
';

$materi->update([
    'konten_teks' => $htmlContent
]);

echo "Konten materi berhasil diperbarui dengan format profesional!\n";
