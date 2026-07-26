<?php

namespace Tests\Feature\BlackBox;

use App\Models\Bab;
use App\Models\Jawaban;
use App\Models\Kelas;
use App\Models\SesiUjian;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A10_HasilRiwayatTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $siswa;
    private $siswaLain;
    private $kelas;
    private $bab;
    private $soal;
    private $sesi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guru     = User::factory()->create(['role' => 'guru']);
        $this->siswa    = User::factory()->create(['role' => 'siswa']);
        $this->siswaLain = User::factory()->create(['role' => 'siswa']);

        $this->kelas = Kelas::create([
            'guru_id'    => $this->guru->id,
            'name'       => 'Kelas Test',
            'tahun'      => '2026',
            'class_code' => 'XYZ123',
        ]);

        $this->kelas->siswa()->attach($this->siswa->id);

        $kelasLain = Kelas::create([
            'guru_id'    => $this->guru->id,
            'name'       => 'Kelas Lain',
            'tahun'      => '2026',
            'class_code' => 'OTHER1',
        ]);
        $kelasLain->siswa()->attach($this->siswaLain->id);

        $this->bab = Bab::create([
            'kelas_id' => $this->kelas->id,
            'title'    => 'Bab 1',
            'order'    => 1,
        ]);

        $this->soal = Soal::create([
            'bab_id'            => $this->bab->id,
            'question_template' => 'Berapa {x} + {y}?',
            'variable_rules'    => ['x' => ['min' => 1, 'max' => 5], 'y' => ['min' => 1, 'max' => 5]],
            'answer_formula'    => 'x + y',
        ]);

        // Buat sesi selesai dengan 1 jawaban benar
        $this->sesi = SesiUjian::create([
            'user_id'     => $this->siswa->id,
            'bab_id'      => $this->bab->id,
            'started_at'  => now()->subMinutes(15),
            'finished_at' => now(),
        ]);

        Jawaban::create([
            'sesi_ujian_id'       => $this->sesi->id,
            'soal_id'             => $this->soal->id,
            'generated_variables' => ['x' => 2, 'y' => 3],
            'student_answer'      => '5',
            'is_correct'          => true,
        ]);
    }

    /** PB-37: Siswa dapat melihat auto-feedback (hasil nilai) setelah menyelesaikan kuis */
    public function test_pb_37_melihat_auto_feedback_hasil(): void
    {
        $response = $this->actingAs($this->siswa)
            ->get(route('siswa.ujian.hasil', $this->sesi));

        $response->assertStatus(200);
        // Halaman hasil menampilkan informasi nilai/skor
        $response->assertSee('100'); // nilai 100 karena 1 benar dari 1 soal
    }

    /** PB-38: Siswa lain tidak dapat mengakses halaman hasil milik siswa lain (403) */
    public function test_pb_38_otorisasi_akses_hasil(): void
    {
        // Siswa lain mencoba mengakses hasil sesi milik $siswa
        $response = $this->actingAs($this->siswaLain)
            ->get(route('siswa.ujian.hasil', $this->sesi));

        $response->assertStatus(403);
    }

    /** PB-39: Siswa dapat melihat riwayat ujian yang telah diselesaikan */
    public function test_pb_39_riwayat_ujian(): void
    {
        $response = $this->actingAs($this->siswa)
            ->get(route('siswa.ujian.riwayat'));

        $response->assertStatus(200);
        // Halaman riwayat menampilkan judul bab dari sesi yang sudah selesai
        $response->assertSee('Bab 1');
    }
}
