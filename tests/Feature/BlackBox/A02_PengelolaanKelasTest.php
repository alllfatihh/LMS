<?php

namespace Tests\Feature\BlackBox;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A02_PengelolaanKelasTest extends TestCase
{
    use RefreshDatabase;

    public function test_pb_05_membuat_kelas(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->post(route('guru.kelas.simpan'), [
            'name' => 'Matematika Kelas 7 Baru',
            'description' => 'Kelas reguler',
            'tahun' => 2026,
        ]);

        $kelas = Kelas::where('name', 'Matematika Kelas 7 Baru')->first();
        $response->assertRedirect(route('guru.kelas.tampil', $kelas));
        $this->assertDatabaseHas('kelas', [
            'name' => 'Matematika Kelas 7 Baru',
            'guru_id' => $guru->id,
            'tahun' => 2026,
        ]);
        
        // Assert class code generated
        $this->assertNotNull($kelas->class_code);
        $this->assertEquals(6, strlen($kelas->class_code));
    }

    public function test_pb_06_validasi_formulir_kelas(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->post(route('guru.kelas.simpan'), [
            'name' => '', // kosong
            'description' => 'Kelas reguler',
            'tahun' => 2026,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_pb_07_menyunting_kelas(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $kelas = Kelas::create([
            'guru_id' => $guru->id,
            'name' => 'Kelas Lama',
            'description' => 'Desc',
            'tahun' => '2025/2026',
            'class_code' => 'ABCDEF'
        ]);

        $response = $this->actingAs($guru)->put(route('guru.kelas.perbarui', $kelas), [
            'name' => 'Kelas Baru',
            'description' => 'Desc Baru',
            'tahun' => 2026,
        ]);

        $response->assertRedirect(route('guru.kelas.tampil', $kelas));
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'name' => 'Kelas Baru',
            'tahun' => 2026,
        ]);
    }

    public function test_pb_08_otorisasi_kepemilikan_kelas(): void
    {
        $guruPemilik = User::factory()->create(['role' => 'guru']);
        $guruLain = User::factory()->create(['role' => 'guru']);
        
        $kelas = Kelas::create([
            'guru_id' => $guruPemilik->id,
            'name' => 'Kelas Pemilik',
            'tahun' => '2025',
            'class_code' => 'AAAAAA'
        ]);

        // Guru lain mencoba edit
        $response = $this->actingAs($guruLain)->get(route('guru.kelas.edit', $kelas));
        $response->assertStatus(403);
    }

    public function test_pb_09_mengeluarkan_siswa(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        
        $kelas = Kelas::create([
            'guru_id' => $guru->id,
            'name' => 'Kelas Aktif',
            'tahun' => '2025',
            'class_code' => 'BBBBBB'
        ]);
        
        // Siswa masuk kelas
        $kelas->siswa()->attach($siswa->id);
        $this->assertDatabaseHas('kelas_user', ['kelas_id' => $kelas->id, 'user_id' => $siswa->id]);

        $response = $this->actingAs($guru)->delete(route('guru.kelas.keluarkan-siswa', [$kelas, $siswa]));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('kelas_user', ['kelas_id' => $kelas->id, 'user_id' => $siswa->id]);
    }
}
