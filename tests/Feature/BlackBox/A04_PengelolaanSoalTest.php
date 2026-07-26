<?php

namespace Tests\Feature\BlackBox;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A04_PengelolaanSoalTest extends TestCase
{
    use RefreshDatabase;

    private $guru;
    private $kelas;
    private $bab;

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
        $this->bab = Bab::create([
            'kelas_id' => $this->kelas->id,
            'title' => 'Bab 1',
            'order' => 1
        ]);
    }

    public function test_pb_16_membuat_soal_dinamis(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.soal.simpan', $this->bab), [
            'question_template' => 'Berapa {x} + {y}?',
            'variables' => [
                ['name' => 'x', 'min' => 1, 'max' => 10],
                ['name' => 'y', 'min' => 1, 'max' => 10]
            ],
            'answer_formula' => 'x + y',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('soal', [
            'bab_id' => $this->bab->id,
            'question_template' => 'Berapa {x} + {y}?',
            'answer_formula' => 'x + y',
        ]);
    }

    public function test_pb_17_pratinjau_soal(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.soal.pratinjau'), [
            'question_template' => 'Berapa {x} + {y}?',
            'variable_rules' => [
                'x' => ['min' => 1, 'max' => 10],
                'y' => ['min' => 1, 'max' => 10]
            ],
            'answer_formula' => 'x + y',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'variables',
            'parsed_template',
            'answer'
        ]);
    }

    public function test_pb_18_menyunting_soal(): void
    {
        $soal = Soal::create([
            'bab_id' => $this->bab->id,
            'question_template' => 'Berapa {x} + {y}?',
            'variable_rules' => json_decode('{"x":{"min":1,"max":10},"y":{"min":1,"max":10}}', true),
            'answer_formula' => 'x + y',
        ]);

        $response = $this->actingAs($this->guru)->put(route('guru.soal.perbarui', $soal), [
            'question_template' => 'Hitung {x} * {y}',
            'variables' => [
                ['name' => 'x', 'min' => 1, 'max' => 5],
                ['name' => 'y', 'min' => 1, 'max' => 5]
            ],
            'answer_formula' => 'x * y',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('soal', [
            'id' => $soal->id,
            'question_template' => 'Hitung {x} * {y}',
            'answer_formula' => 'x * y',
        ]);
    }
}
