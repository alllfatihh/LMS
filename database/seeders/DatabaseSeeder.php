<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Data contoh LMS dipasang lewat migrasi `2026_05_14_120000_data_awal_lms_contoh`
     * (`App\Database\DataAwalLms`) — jalankan `php artisan migrate` saja.
     */
    public function run(): void
    {
        // 1. Pastikan akun Guru untuk demo tersedia
        $guru = \App\Models\User::firstOrCreate(
            ['email' => 'guru@example.com'],
            [
                'name' => 'Bapak Guru Demo',
                'password' => bcrypt('password'),
                'role' => 'guru',
                'email_verified_at' => now(),
            ]
        );

        // 2. Pastikan akun Siswa untuk demo tersedia
        $siswa = \App\Models\User::firstOrCreate(
            ['email' => 'siswa@example.com'],
            [
                'name' => 'Siswa Demo',
                'password' => bcrypt('password'),
                'role' => 'siswa',
                'email_verified_at' => now(),
            ]
        );

        // 3. Jalankan Seeder Kelas Matematika SMP (Kelas 7, 8, dan 9 Kurikulum Merdeka)
        $this->call([
            KelasMatematikaSeeder::class,
            KelasMatematikaKelas8Seeder::class,
            KelasMatematikaKelas9Seeder::class,
            SiswaSeeder::class,
        ]);
    }
}
