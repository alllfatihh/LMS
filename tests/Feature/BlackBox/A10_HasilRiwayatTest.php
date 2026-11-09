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

    /** Nilai akhir harus dihitung berdasarkan bobot soal, bukan rata-rata jumlah soal biasa. */
    public function test_nilai_akhir_dihitung_berdasarkan_bobot_soal(): void
    {
        $bab = Bab::create([
            'kelas_id' => $this->kelas->id,
            'title'    => 'Bab Bobot',
            'order'    => 2,
        ]);

        $soalBerat = Soal::create([
            'bab_id'            => $bab->id,
            'question_template' => 'Soal berat',
            'variable_rules'    => [],
            'answer_formula'    => '1',
            'bobot'             => 30,
        ]);

        $soalRingan = Soal::create([
            'bab_id'            => $bab->id,
            'question_template' => 'Soal ringan',
            'variable_rules'    => [],
            'answer_formula'    => '1',
            'bobot'             => 10,
        ]);

        $sesi = SesiUjian::create([
            'user_id'     => $this->siswa->id,
            'bab_id'      => $bab->id,
            'started_at'  => now()->subMinutes(10),
            'finished_at' => now(),
        ]);

        // Soal berbobot besar dijawab BENAR, soal berbobot kecil dijawab SALAH.
        Jawaban::create([
            'sesi_ujian_id'       => $sesi->id,
            'soal_id'             => $soalBerat->id,
            'generated_variables' => [],
            'student_answer'      => '1',
            'is_correct'          => true,
        ]);

        Jawaban::create([
            'sesi_ujian_id'       => $sesi->id,
            'soal_id'             => $soalRingan->id,
            'generated_variables' => [],
            'student_answer'      => '2',
            'is_correct'          => false,
        ]);

        // Rata-rata jumlah soal biasa akan menghasilkan 50%, tapi berbobot: 30/(30+10) = 75%.
        $response = $this->actingAs($this->siswa)
            ->get(route('siswa.ujian.hasil', $sesi));

        $response->assertStatus(200);
        $response->assertSee('75');
    }

    /**
     * Regresi: soal berformat 'aljabar' dengan answer_formula berupa PEMANGGILAN FUNGSI
     * (bukan template simbolik biasa) harus tetap dieksekusi dan ditampilkan sebagai hasil
     * jadi (mis. "(x+5)(x+4)"), bukan string rumus mentah "faktorisasi_kuadrat(p, q)".
     */
    public function test_kunci_jawaban_aljabar_berupa_fungsi_dieksekusi_bukan_ditampilkan_mentah(): void
    {
        $bab = Bab::create(['kelas_id' => $this->kelas->id, 'title' => 'Bab Faktorisasi', 'order' => 3]);

        $soal = Soal::create([
            'bab_id'            => $bab->id,
            'question_template' => 'Faktorkanlah bentuk kuadrat x^2 + {b}x + {c} menjadi perkalian dua binomial!',
            'variable_rules'    => [
                'p' => ['type' => 'integer', 'min' => 5, 'max' => 5],
                'q' => ['type' => 'integer', 'min' => 4, 'max' => 4],
                'b' => ['type' => 'derived', 'formula' => 'p + q'],
                'c' => ['type' => 'derived', 'formula' => 'p * q'],
            ],
            'answer_formula'    => 'faktorisasi_kuadrat(p, q)',
            'jawaban_format'    => 'aljabar',
        ]);

        $sesi = SesiUjian::create([
            'user_id'     => $this->siswa->id,
            'bab_id'      => $bab->id,
            'started_at'  => now()->subMinutes(5),
            'finished_at' => now(),
        ]);

        Jawaban::create([
            'sesi_ujian_id'       => $sesi->id,
            'soal_id'             => $soal->id,
            'generated_variables' => ['p' => 5, 'q' => 4, 'b' => 9, 'c' => 20],
            'student_answer'      => null,
            'is_correct'          => false,
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.ujian.hasil', $sesi));

        $response->assertStatus(200);
        // Bagian "Kunci" harus menampilkan hasil evaluasi, bukan rumus mentah.
        // (Rumus mentah tetap boleh muncul di bagian "Tips" sebagai referensi guru/siswa.)
        $response->assertSee('(x+5)(x+4)');
    }
}
