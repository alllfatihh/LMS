<?php

use App\Database\DataAwalLms;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data contoh (guru, siswa, kelas MTK-701, bab, soal, modul HTML) — tanpa `php artisan db:seed`.
     * Konten modul panjang dibaca dari `database/fixtures/modul-bilangan-bulat.html`.
     */
    public function up(): void
    {
        if (! Schema::hasTable('materi')) {
            return;
        }

        DataAwalLms::pasang();
    }

    public function down(): void
    {
        // Data awal tidak dihapus otomatis (hindari kehilangan data produksi).
    }
};
