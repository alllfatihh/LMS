<?php

namespace Tests\Feature\BlackBox;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A07_GabungKelasTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $siswa;
    private $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->siswa = User::factory()->create(['role' => 'siswa']);
        $this->kelas = Kelas::create([
            'guru_id' => $this->guru->id,
            'name' => 'Kelas Test',
            'tahun' => '2026',
            'class_code' => 'XYZ123'
        ]);
    }

    public function test_pb_25_gabung_kelas_kode_benar(): void
    {
        $response = $this->actingAs($this->siswa)->post(route('siswa.gabung-kelas.simpan'), [
            'class_code' => 'XYZ123'
        ]);

        $response->assertRedirect(route('siswa.ujian.index'));
        $this->assertDatabaseHas('kelas_user', [
            'kelas_id' => $this->kelas->id,
            'user_id' => $this->siswa->id
        ]);
    }

    public function test_pb_26_gabung_kelas_kode_salah(): void
    {
        $response = $this->actingAs($this->siswa)->post(route('siswa.gabung-kelas.simpan'), [
            'class_code' => 'SALAH1'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('kelas_user', [
            'user_id' => $this->siswa->id
        ]);
    }

    public function test_pb_27_gabung_kelas_ganda(): void
    {
        $this->kelas->siswa()->attach($this->siswa->id);

        $response = $this->actingAs($this->siswa)->post(route('siswa.gabung-kelas.simpan'), [
            'class_code' => 'XYZ123'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success'); // Controller mengarahkan ke ujian.index dengan 'success'
        $this->assertEquals(1, $this->kelas->siswa()->where('user_id', $this->siswa->id)->count());
    }
}
