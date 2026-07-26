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

class A09_PengerjaanKuisTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $siswa;
    private $kelas;
    private $bab;
    private $soal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guru  = User::factory()->create(['role' => 'guru']);
        $this->siswa = User::factory()->create(['role' => 'siswa']);

        $this->kelas = Kelas::create([
            'guru_id'    => $this->guru->id,
            'name'       => 'Kelas Test',
            'tahun'      => '2026',
            'class_code' => 'XYZ123',
        ]);

        $this->kelas->siswa()->attach($this->siswa->id);

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
    }

    /** PB-31: Siswa berhasil memulai sesi kuis baru */
    public function test_pb_31_memulai_kuis(): void
    {
        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.bab.mulai', $this->bab));

        $response->assertRedirect();
        $this->assertDatabaseHas('sesi_ujian', [
            'user_id' => $this->siswa->id,
            'bab_id'  => $this->bab->id,
        ]);
    }

    /** PB-32: Siswa yang sudah lulus (nilai >= 80) diblokir dari memulai ulang */
    public function test_pb_32_blokir_mulai_kuis_jika_sudah_lulus(): void
    {
        // Buat sesi yang sudah selesai dengan nilai 100 (semua benar)
        $sesiLama = SesiUjian::create([
            'user_id'     => $this->siswa->id,
            'bab_id'      => $this->bab->id,
            'started_at'  => now()->subMinutes(10),
            'finished_at' => now(),
        ]);

        Jawaban::create([
            'sesi_ujian_id'      => $sesiLama->id,
            'soal_id'            => $this->soal->id,
            'generated_variables'=> ['x' => 2, 'y' => 3],
            'student_answer'     => '5',
            'is_correct'         => true,
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.bab.mulai', $this->bab));

        // Harus diarahkan ke riwayat, bukan membuat sesi baru
        $response->assertRedirect(route('siswa.ujian.riwayat'));
        $response->assertSessionHas('info');
    }

    /** PB-33: Siswa yang punya sesi aktif diarahkan melanjutkan sesi tersebut */
    public function test_pb_33_lanjut_sesi_aktif(): void
    {
        $sesiAktif = SesiUjian::create([
            'user_id'    => $this->siswa->id,
            'bab_id'     => $this->bab->id,
            'started_at' => now()->subMinutes(5),
            'finished_at'=> null,
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.bab.mulai', $this->bab));

        // Harus diarahkan ke sesi yang sudah ada, bukan membuat sesi baru
        $response->assertRedirect(route('siswa.ujian.tampil-soal', $sesiAktif->id));
        $response->assertSessionHas('info');
    }

    /** PB-34: Auto-grading — jawaban benar dinilai is_correct = true */
    public function test_pb_34_auto_grading_jawaban_benar(): void
    {
        $sesi = SesiUjian::create([
            'user_id'    => $this->siswa->id,
            'bab_id'     => $this->bab->id,
            'started_at' => now(),
        ]);

        // x=2, y=3, jawaban benar = 5
        $jawaban = Jawaban::create([
            'sesi_ujian_id'       => $sesi->id,
            'soal_id'             => $this->soal->id,
            'generated_variables' => ['x' => 2, 'y' => 3],
            'student_answer'      => null,
            'is_correct'          => null,
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.jawaban.kirim', $jawaban), [
                'student_answer' => '5',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jawaban', [
            'id'         => $jawaban->id,
            'is_correct' => true,
        ]);
    }

    /** PB-35: Validasi jawaban numerik — input bukan angka ditolak */
    public function test_pb_35_validasi_jawaban_numerik(): void
    {
        $sesi = SesiUjian::create([
            'user_id'    => $this->siswa->id,
            'bab_id'     => $this->bab->id,
            'started_at' => now(),
        ]);

        $jawaban = Jawaban::create([
            'sesi_ujian_id'       => $sesi->id,
            'soal_id'             => $this->soal->id,
            'generated_variables' => ['x' => 2, 'y' => 3],
            'student_answer'      => null,
            'is_correct'          => null,
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.jawaban.kirim', $jawaban), [
                'student_answer' => 'abc', // bukan angka
            ]);

        $response->assertSessionHasErrors('student_answer');
        $this->assertDatabaseHas('jawaban', [
            'id'             => $jawaban->id,
            'student_answer' => null, // tidak berubah
        ]);
    }

    /** PB-36: Selesaikan ujian — sesi diakhiri dan diarahkan ke halaman hasil */
    public function test_pb_36_selesaikan_kuis(): void
    {
        $sesi = SesiUjian::create([
            'user_id'    => $this->siswa->id,
            'bab_id'     => $this->bab->id,
            'started_at' => now(),
            'finished_at'=> null,
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.selesaikan', $sesi));

        $response->assertRedirect(route('siswa.ujian.hasil', $sesi->id));
        $this->assertDatabaseHas('sesi_ujian', [
            'id'          => $sesi->id,
            'finished_at' => now()->toDateTimeString(),
        ]);
    }
}
