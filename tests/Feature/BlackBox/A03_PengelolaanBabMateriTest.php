<?php

namespace Tests\Feature\BlackBox;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class A03_PengelolaanBabMateriTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guru = User::factory()->create(['role' => 'guru']);
        $this->kelas = Kelas::create([
            'guru_id' => $this->guru->id,
            'name' => 'Kelas Test',
            'tahun' => '2026',
            'class_code' => 'XYZ123'
        ]);
    }

    public function test_pb_10_membuat_bab(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.bab.simpan', $this->kelas), [
            'title' => 'Bab 1 Aljabar',
            'order' => 1,
            'durasi' => 60,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bab', [
            'kelas_id' => $this->kelas->id,
            'title' => 'Bab 1 Aljabar',
            'order' => 1
        ]);
    }

    public function test_pb_11_menambah_materi_teks(): void
    {
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab 1', 'order' => 1]);

        $response = $this->actingAs($this->guru)->post(route('guru.materi.simpan', $bab), [
            'judul' => 'Pengenalan Aljabar',
            'tipe' => 'teks',
            'konten_teks' => '<p>Ini adalah konten</p>',
            'order' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'bab_id' => $bab->id,
            'judul' => 'Pengenalan Aljabar',
            'tipe' => 'teks',
            'konten_teks' => '<p>Ini adalah konten</p>'
        ]);
    }

    public function test_pb_12_menambah_materi_video(): void
    {
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab 1', 'order' => 1]);

        $response = $this->actingAs($this->guru)->post(route('guru.materi.simpan', $bab), [
            'judul' => 'Video Aljabar',
            'tipe' => 'video',
            'link_video' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
            'order' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'bab_id' => $bab->id,
            'judul' => 'Video Aljabar',
            'tipe' => 'video',
        ]);
    }

    public function test_pb_13_menambah_materi_pdf(): void
    {
        Storage::fake('public');
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab 1', 'order' => 1]);
        $file = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->guru)->post(route('guru.materi.simpan', $bab), [
            'judul' => 'Modul PDF',
            'tipe' => 'pdf',
            'berkas_pdf' => $file,
            'order' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'bab_id' => $bab->id,
            'judul' => 'Modul PDF',
            'tipe' => 'pdf',
        ]);
        
        $materi = Materi::where('judul', 'Modul PDF')->first();
        Storage::disk('public')->assertExists($materi->file_path);
    }

    public function test_pb_14_menyunting_materi(): void
    {
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab 1', 'order' => 1]);
        $materi = Materi::create([
            'kelas_id' => $this->kelas->id,
            'bab_id' => $bab->id,
            'judul' => 'Materi Lama',
            'slug' => 'materi-lama',
            'tipe' => 'teks',
            'konten_teks' => 'Lama',
            'order' => 1
        ]);

        $response = $this->actingAs($this->guru)->put(route('guru.materi.perbarui', $materi), [
            'judul' => 'Materi Baru',
            'tipe' => 'teks',
            'konten_teks' => 'Baru',
            'order' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('materi', [
            'id' => $materi->id,
            'judul' => 'Materi Baru',
            'konten_teks' => 'Baru',
            'order' => 1
        ]);
    }

    public function test_pb_15_menghapus_materi(): void
    {
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab 1', 'order' => 1]);
        $materi = Materi::create([
            'kelas_id' => $this->kelas->id,
            'bab_id' => $bab->id,
            'judul' => 'Materi Hapus',
            'slug' => 'materi-hapus',
            'tipe' => 'teks',
            'konten_teks' => 'Hapus',
            'order' => 1
        ]);

        $response = $this->actingAs($this->guru)->delete(route('guru.materi.hapus', $materi));

        $response->assertRedirect();
        $this->assertDatabaseMissing('materi', ['id' => $materi->id]);
    }
}
