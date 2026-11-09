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

    /** JSON rusak dari AI harus ditangani secara anggun dengan pesan ramah, bukan bocorkan error PHP mentah. */
    public function test_generate_soal_json_rusak_ditangani_dengan_pesan_jelas(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'ini bukan json sama sekali, AI berhalusinasi {{{']]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.ai.generate-soal', $this->bab), [
            'topik' => 'Penjumlahan dasar',
            'jumlah' => 1,
        ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        // Pesan harus ramah untuk guru, bukan bocoran detail JSON parser PHP.
        $response->assertJsonFragment(['pesan' => 'AI memberikan format respons yang tidak valid. Silakan coba generate ulang.']);
    }

    /** Soal dengan variable_rules berstruktur salah (bukan objek) harus di-skip, bukan menyebabkan 500 fatal. */
    public function test_generate_soal_skip_variable_rules_berstruktur_salah(): void
    {
        $mockResponse = [
            'soal' => [
                [
                    'question_template' => 'Berapa {x} + {y}?',
                    'variable_rules' => 'bukan objek, cuma string',
                    'answer_formula' => 'x + y',
                ],
            ],
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

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        $this->assertDatabaseMissing('soal', ['bab_id' => $this->bab->id]);
    }

    /** Jika server AI tidak merespons (timeout/koneksi gagal), pesan ke guru harus jelas, bukan crash. */
    public function test_generate_soal_koneksi_ai_gagal_ditangani_dengan_pesan_jelas(): void
    {
        Http::fake([
            'api.groq.com/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'),
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.ai.generate-soal', $this->bab), [
            'topik' => 'Penjumlahan dasar',
            'jumlah' => 1,
        ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        $response->assertJsonFragment(['pesan' => 'Server AI tidak merespons saat ini. Silakan coba lagi beberapa saat lagi.']);
    }

    /** Prompt yang dikirim ke AI harus memuat batas kurikulum dan daftar fungsi dari MathExpressionProvider. */
    public function test_prompt_generate_soal_memuat_kurikulum_dan_daftar_fungsi(): void
    {
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => json_encode(['soal' => []])]]
                ]
            ], 200)
        ]);

        $this->actingAs($this->guru)->postJson(route('guru.ai.generate-soal', $this->bab), [
            'topik' => 'Penjumlahan dasar',
            'jumlah' => 1,
        ]);

        Http::assertSent(function ($request) {
            $prompt = $request['messages'][0]['content'] ?? '';

            return str_contains($prompt, 'DILARANG KERAS')
                && str_contains($prompt, 'Kurikulum Merdeka')
                && str_contains($prompt, 'akar_kubik(x)')
                && str_contains($prompt, 'logaritma(n, basis)');
        });
    }
}
