<?php

namespace Tests\Feature\BlackBox;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A08_PembelajaranMateriTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $siswa;
    private $siswaLain;
    private $kelas;
    private $bab;
    private $materi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->siswa = User::factory()->create(['role' => 'siswa']);
        $this->siswaLain = User::factory()->create(['role' => 'siswa']);
        
        $this->kelas = Kelas::create([
            'guru_id' => $this->guru->id,
            'name' => 'Kelas Test',
            'tahun' => '2026',
            'class_code' => 'XYZ123'
        ]);
        
        $this->kelas->siswa()->attach($this->siswa->id);

        // Daftarkan siswaLain ke kelas LAIN supaya middleware lolos,
        // tapi controller tetap menolak dengan 403
        $kelasLain = Kelas::create([
            'guru_id'    => $this->guru->id,
            'name'       => 'Kelas Lain',
            'tahun'      => '2026',
            'class_code' => 'OTHER1',
        ]);
        $kelasLain->siswa()->attach($this->siswaLain->id);

        $this->bab = Bab::create([
            'kelas_id' => $this->kelas->id,
            'title' => 'Bab 1',
            'order' => 1
        ]);

        $this->materi = Materi::create([
            'kelas_id' => $this->kelas->id,
            'bab_id' => $this->bab->id,
            'judul' => 'Materi Uji',
            'slug' => 'materi-uji',
            'tipe' => 'teks',
            'konten_teks' => 'Isi materi',
            'order' => 1
        ]);
    }

    public function test_pb_28_membaca_materi(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('siswa.materi.tampil', $this->materi->slug));

        $response->assertStatus(200);
        $response->assertSee('Isi materi');
    }

    public function test_pb_29_menandai_materi_dibaca(): void
    {
        $response = $this->actingAs($this->siswa)->post(route('siswa.materi.tandai-dibaca', $this->materi->slug));

        $response->assertRedirect();
        $this->assertDatabaseHas('kemajuan_materi', [
            'user_id' => $this->siswa->id,
            'materi_id' => $this->materi->id,
        ]);
    }

    public function test_pb_30_otorisasi_akses_materi(): void
    {
        // Siswa lain yang tidak tergabung ke kelas
        $response = $this->actingAs($this->siswaLain)->get(route('siswa.materi.tampil', $this->materi->slug));

        $response->assertStatus(403);
    }
}
