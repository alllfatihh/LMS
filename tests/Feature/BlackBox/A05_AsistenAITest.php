<?php

namespace Tests\Feature\BlackBox;

use App\Models\Bab;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class A05_AsistenAITest extends TestCase
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

    public function test_pb_19_generate_materi_dengan_ai(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => '<h1>Materi Aljabar</h1><p>Ini adalah draf materi.</p>']]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.ai.generate-materi'), [
            'poin_inti' => 'Pengenalan Aljabar',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'html' => '<h1>Materi Aljabar</h1><p>Ini adalah draf materi.</p>',
        ]);
    }

    public function test_pb_20_penanganan_galat_ai(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response('Server Error', 500)
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.ai.generate-materi'), [
            'poin_inti' => 'Pengenalan Aljabar',
        ]);

        $response->assertStatus(500);
        $response->assertJsonStructure(['success', 'pesan']);
        $response->assertJson(['success' => false]);
    }

    public function test_pb_21_generate_soal_dengan_ai(): void
    {
        $mockResponse = [
            'soal' => [
                [
                    'question_template' => 'Berapa {x} + {y}?',
                    'variable_rules' => ['x' => ['min' => 1, 'max' => 5], 'y' => ['min' => 1, 'max' => 5]],
                    'answer_formula' => 'x + y'
                ]
            ]
        ];

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => json_encode($mockResponse)]]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.ai.generate-soal', $this->bab), [
            'topik' => 'Penjumlahan dasar',
            'jumlah' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('soal', [
            'bab_id' => $this->bab->id,
            'question_template' => 'Berapa {x} + {y}?',
        ]);
    }

    public function test_pb_22_otorisasi_fitur_ai(): void
    {
        $guruLain = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guruLain)->postJson(route('guru.ai.generate-soal', $this->bab), [
            'topik' => 'Penjumlahan dasar',
            'jumlah' => 1,
        ]);

        $response->assertStatus(403);
    }
}
