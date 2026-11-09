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

class A06_PemantauanAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_pb_23_memantau_hasil_kuis(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        
        $kelas = Kelas::create(['guru_id' => $guru->id, 'name' => 'Kelas Test', 'tahun' => '2026', 'class_code' => 'AAAAAA']);
        $bab = Bab::create(['kelas_id' => $kelas->id, 'title' => 'Bab 1', 'order' => 1]);
        
        $sesi = SesiUjian::create([
            'user_id' => $siswa->id,
            'bab_id' => $bab->id,
            'started_at' => now()->subMinutes(10),
            'finished_at' => now()
        ]);

        $response = $this->actingAs($guru)->get(route('beranda'));

        $response->assertStatus(200);
        $response->assertSee('Kelas Test');
        $response->assertSee('Bab 1');
        $response->assertSee($siswa->name);
    }

    public function test_pb_24_audit_sesi_ujian(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        
        $kelas = Kelas::create(['guru_id' => $guru->id, 'name' => 'Kelas Test', 'tahun' => '2026', 'class_code' => 'AAAAAA']);
        $bab = Bab::create(['kelas_id' => $kelas->id, 'title' => 'Bab 1', 'order' => 1]);
        $soal = Soal::create([
            'bab_id' => $bab->id,
            'question_template' => 'Berapa {x} + {y}?',
            'variable_rules' => ['x' => ['min' => 1, 'max' => 5], 'y' => ['min' => 1, 'max' => 5]],
            'answer_formula' => 'x + y'
        ]);
        
        $sesi = SesiUjian::create([
            'user_id' => $siswa->id,
            'bab_id' => $bab->id,
            'started_at' => now()->subMinutes(10),
            'finished_at' => now()
        ]);

        Jawaban::create([
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soal->id,
            'generated_variables' => ['x' => 2, 'y' => 3],
            'student_answer' => '5',
            'is_correct' => true
        ]);

        $response = $this->actingAs($guru)->get(route('guru.audit.tampil', $sesi));

        $response->assertStatus(200);
        $response->assertSee('Berapa 2 + 3?');
        $response->assertSee('5');
    }

    public function test_audit_link_pada_halaman_daftar_siswa(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        
        $kelas = Kelas::create(['guru_id' => $guru->id, 'name' => 'Kelas Audit Test', 'tahun' => '2026', 'class_code' => 'AUDIT1']);
        $kelas->siswa()->attach($siswa->id);
        
        $bab = Bab::create(['kelas_id' => $kelas->id, 'title' => 'Bab Aljabar', 'order' => 1]);
        $soal = Soal::create([
            'bab_id' => $bab->id,
            'question_template' => 'Hitung {a} + {b}',
            'variable_rules' => ['a' => ['min' => 1, 'max' => 5], 'b' => ['min' => 1, 'max' => 5]],
            'answer_formula' => 'a + b'
        ]);
        
        $sesi = SesiUjian::create([
            'user_id' => $siswa->id,
            'bab_id' => $bab->id,
            'started_at' => now()->subMinutes(10),
            'finished_at' => now()
        ]);

        Jawaban::create([
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soal->id,
            'generated_variables' => ['a' => 4, 'b' => 6],
            'student_answer' => '10',
            'is_correct' => true
        ]);

        // Akses halaman detail kelas (Daftar Siswa)
        $response = $this->actingAs($guru)->get(route('guru.kelas.tampil', $kelas));

        $response->assertStatus(200);
        $response->assertSee($siswa->name);
        $response->assertSee(route('guru.audit.tampil', $sesi->id));
        $response->assertSee('Bab Aljabar');
    }

    /** Skor akhir di halaman audit guru harus berbobot, konsisten dengan halaman hasil siswa. */
    public function test_skor_pada_halaman_audit_dihitung_berdasarkan_bobot_soal(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $kelas = Kelas::create(['guru_id' => $guru->id, 'name' => 'Kelas Bobot', 'tahun' => '2026', 'class_code' => 'BOBOT1']);
        $bab = Bab::create(['kelas_id' => $kelas->id, 'title' => 'Bab Bobot', 'order' => 1]);

        $soalBerat = Soal::create([
            'bab_id' => $bab->id,
            'question_template' => 'Soal berat',
            'variable_rules' => [],
            'answer_formula' => '1',
            'bobot' => 30,
        ]);

        $soalRingan = Soal::create([
            'bab_id' => $bab->id,
            'question_template' => 'Soal ringan',
            'variable_rules' => [],
            'answer_formula' => '1',
            'bobot' => 10,
        ]);

        $sesi = SesiUjian::create([
            'user_id' => $siswa->id,
            'bab_id' => $bab->id,
            'started_at' => now()->subMinutes(10),
            'finished_at' => now(),
        ]);

        Jawaban::create([
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soalBerat->id,
            'generated_variables' => [],
            'student_answer' => '1',
            'is_correct' => true,
        ]);

        Jawaban::create([
            'sesi_ujian_id' => $sesi->id,
            'soal_id' => $soalRingan->id,
            'generated_variables' => [],
            'student_answer' => '2',
            'is_correct' => false,
        ]);

        // Rata-rata jumlah soal biasa = 50%, tapi berbobot: 30/(30+10) = 75%.
        $response = $this->actingAs($guru)->get(route('guru.audit.tampil', $sesi));

        $response->assertStatus(200);
        $response->assertSee('75%');
    }

    /**
     * Regresi: halaman audit guru juga harus mengeksekusi answer_formula berupa pemanggilan
     * fungsi (mis. faktorisasi_kuadrat) untuk merekonstruksi kunci jawaban, bukan menampilkan
     * rumus mentah atau pesan "Error: ..." mentah dari exception PHP.
     */
    public function test_audit_kunci_jawaban_aljabar_berupa_fungsi_dieksekusi_bukan_mentah(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $kelas = Kelas::create(['guru_id' => $guru->id, 'name' => 'Kelas Faktorisasi', 'tahun' => '2026', 'class_code' => 'FAKTOR1']);
        $bab = Bab::create(['kelas_id' => $kelas->id, 'title' => 'Bab Faktorisasi', 'order' => 1]);

        $soal = Soal::create([
            'bab_id'            => $bab->id,
            'question_template' => 'Faktorkanlah bentuk kuadrat x^2 + {b}x + {c}!',
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
            'user_id'     => $siswa->id,
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

        $response = $this->actingAs($guru)->get(route('guru.audit.tampil', $sesi));

        $response->assertStatus(200);
        // Kunci jawaban hasil rekonstruksi harus berupa hasil evaluasi, bukan pesan error mentah.
        // (Rumus mentah tetap boleh muncul di bagian "Rumus Kunci Jawaban" sebagai referensi guru.)
        $response->assertSee('(x+5)(x+4)');
        $response->assertDontSee('Error:');
    }
}
