<?php

namespace Tests\Feature;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_yang_berada_di_kelas_bisa_mengakses_materi()
    {
        $guru = User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $siswa = User::create([
            'name' => 'Siswa Pintar',
            'email' => 'siswa.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $kelas = Kelas::create([
            'name' => 'Kelas VII-A',
            'description' => 'Matematika VII-A',
            'tahun' => 2026,
            'class_code' => 'K7A123',
            'guru_id' => $guru->id,
        ]);

        // Siswa gabung kelas
        $kelas->siswa()->attach($siswa->id);

        $bab = Bab::create([
            'title' => 'Aljabar',
            'order' => 1,
            'kelas_id' => $kelas->id,
        ]);

        $materi = Materi::create([
            'judul' => 'Pengenalan Aljabar',
            'slug' => 'pengenalan-aljabar',
            'konten_teks' => 'Teks materi aljabar...',
            'tipe' => 'teks',
            'order' => 1,
            'kelas_id' => $kelas->id,
            'bab_id' => $bab->id,
        ]);

        $response = $this->actingAs($siswa)->get("/siswa/materi/{$materi->slug}");

        $response->assertOk();
        $response->assertViewHas('modul');
    }

    public function test_siswa_yang_tidak_berada_di_kelas_tidak_bisa_mengakses_materi()
    {
        $guru = User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $siswa = User::create([
            'name' => 'Siswa Pintar',
            'email' => 'siswa.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        $kelas = Kelas::create([
            'name' => 'Kelas VII-A',
            'description' => 'Matematika VII-A',
            'tahun' => 2026,
            'class_code' => 'K7A123',
            'guru_id' => $guru->id,
        ]);

        // Siswa TIDAK gabung kelas

        $bab = Bab::create([
            'title' => 'Aljabar',
            'order' => 1,
            'kelas_id' => $kelas->id,
        ]);

        $materi = Materi::create([
            'judul' => 'Pengenalan Aljabar',
            'slug' => 'pengenalan-aljabar',
            'konten_teks' => 'Teks materi aljabar...',
            'tipe' => 'teks',
            'order' => 1,
            'kelas_id' => $kelas->id,
            'bab_id' => $bab->id,
        ]);

        $response = $this->actingAs($siswa)->get("/siswa/materi/{$materi->slug}");

        // Akan diredirect ke gabung kelas karena middleware PastikanSiswaBeradaDiKelas
        $response->assertRedirect(route('siswa.gabung-kelas.index'));
    }

    public function test_guru_pemilik_kelas_bisa_melihat_pratinjau_materi()
    {
        $guru = User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $kelas = Kelas::create([
            'name' => 'Kelas VII-A',
            'description' => 'Matematika VII-A',
            'tahun' => 2026,
            'class_code' => 'K7A123',
            'guru_id' => $guru->id,
        ]);

        $bab = Bab::create([
            'title' => 'Aljabar',
            'order' => 1,
            'kelas_id' => $kelas->id,
        ]);

        $materi = Materi::create([
            'judul' => 'Pengenalan Aljabar',
            'slug' => 'pengenalan-aljabar',
            'konten_teks' => 'Teks materi aljabar...',
            'tipe' => 'teks',
            'order' => 1,
            'kelas_id' => $kelas->id,
            'bab_id' => $bab->id,
        ]);

        $response = $this->actingAs($guru)->get("/siswa/materi/{$materi->slug}");

        $response->assertOk();
        $response->assertViewHas('modul');
    }

    public function test_guru_bukan_pemilik_kelas_tidak_bisa_melihat_pratinjau_materi()
    {
        $guruOwner = User::create([
            'name' => 'Guru Owner',
            'email' => 'guruowner.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $guruLain = User::create([
            'name' => 'Guru Lain',
            'email' => 'gurulain.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $kelas = Kelas::create([
            'name' => 'Kelas VII-A',
            'description' => 'Matematika VII-A',
            'tahun' => 2026,
            'class_code' => 'K7A123',
            'guru_id' => $guruOwner->id,
        ]);

        $bab = Bab::create([
            'title' => 'Aljabar',
            'order' => 1,
            'kelas_id' => $kelas->id,
        ]);

        $materi = Materi::create([
            'judul' => 'Pengenalan Aljabar',
            'slug' => 'pengenalan-aljabar',
            'konten_teks' => 'Teks materi aljabar...',
            'tipe' => 'teks',
            'order' => 1,
            'kelas_id' => $kelas->id,
            'bab_id' => $bab->id,
        ]);

        $response = $this->actingAs($guruLain)->get("/siswa/materi/{$materi->slug}");

        $response->assertStatus(403);
    }
}
