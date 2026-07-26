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
}
