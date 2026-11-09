<?php

namespace Tests\Unit;

use App\Services\EvaluasiSoalService;
use PHPUnit\Framework\TestCase;

class EvaluasiSoalServiceTest extends TestCase
{
    private EvaluasiSoalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EvaluasiSoalService();
    }

    // ==========================================
    // Variabel bertipe float
    // ==========================================

    public function test_variabel_float_berada_dalam_rentang_dan_berdesimal(): void
    {
        $aturan = [
            'x' => ['type' => 'float', 'min' => 1.5, 'max' => 2.5, 'decimals' => 2],
        ];

        for ($i = 0; $i < 20; $i++) {
            $variabel = $this->service->hasilkanVariabel($aturan);
            $this->assertGreaterThanOrEqual(1.5, $variabel['x']);
            $this->assertLessThanOrEqual(2.5, $variabel['x']);
            // Tidak lebih dari 2 digit desimal
            $this->assertEquals(
                round($variabel['x'], 2),
                $variabel['x']
            );
        }
    }

    // ==========================================
    // Variabel 'derived' dengan pembulatan eksplisit ('bulatkan')
    // ==========================================

    public function test_variabel_derived_dibulatkan_sesuai_kunci_bulatkan(): void
    {
        $aturan = [
            'a' => ['type' => 'integer', 'min' => 1, 'max' => 1],
            'b' => ['type' => 'integer', 'min' => 3, 'max' => 3],
            'hasil' => ['type' => 'derived', 'formula' => 'a / b', 'bulatkan' => 2],
        ];

        $variabel = $this->service->hasilkanVariabel($aturan);

        // 1 / 3 = 0.3333... -> dibulatkan 2 desimal jadi 0.33
        $this->assertSame(0.33, $variabel['hasil']);
    }

    public function test_variabel_derived_tanpa_bulatkan_tetap_presisi_penuh(): void
    {
        $aturan = [
            'a' => ['type' => 'integer', 'min' => 1, 'max' => 1],
            'b' => ['type' => 'integer', 'min' => 3, 'max' => 3],
            'hasil' => ['type' => 'derived', 'formula' => 'a / b'],
        ];

        $variabel = $this->service->hasilkanVariabel($aturan);

        $this->assertEqualsWithDelta(0.3333333, $variabel['hasil'], 0.00001);
    }

    // ==========================================
    // Presisi pembanding jawaban dinamis
    // ==========================================

    public function test_jawaban_desimal_presisi_tinggi_dinilai_akurat(): void
    {
        // Jawaban benar punya 3 digit desimal signifikan; siswa harus tepat sampai 3 digit.
        // Notasi koma dipakai sesuai konvensi desimal Indonesia (titik = pemisah ribuan).
        $this->assertTrue($this->service->jawabanNumerikSama('12,375', 12.375));
        // Selisih 0.075 jauh melewati toleransi presisi 3 desimal -> harus ditolak.
        $this->assertFalse($this->service->jawabanNumerikSama('12,300', 12.375));
    }

    public function test_perbandingan_desimal_sederhana_tetap_toleran_seperti_sebelumnya(): void
    {
        // Jawaban benar cuma perlu akurasi 2 desimal (perilaku lama harus tetap sama).
        $this->assertTrue($this->service->jawabanNumerikSama('12.5', 12.5));
        $this->assertTrue($this->service->jawabanNumerikSama(12, 12));
    }

    public function test_toleransi_pembulatan_bilangan_bulat_tetap_berjalan(): void
    {
        // Siswa menjawab bulat, jawaban benar desimal panjang -> tetap diterima (perilaku lama).
        $this->assertTrue($this->service->jawabanNumerikSama('36', 36.14159));
    }

    // ==========================================
    // Regresi format jawaban Indonesia (baseline, jangan sampai berubah)
    // ==========================================

    public function test_parsing_angka_indonesia_tidak_berubah(): void
    {
        $this->assertFalse($this->service->jawabanNumerikSama('80.000', 80));
        $this->assertTrue($this->service->jawabanNumerikSama('80.000', 80000));
        $this->assertTrue($this->service->jawabanNumerikSama('80,5', 80.5));
    }

    // ==========================================
    // Format jawaban lain (pecahan, himpunan, urutan, aljabar) - smoke test regresi
    // ==========================================

    public function test_format_pecahan_masih_berjalan(): void
    {
        $variabel = ['a' => 3, 'b' => 4];
        $jawabanBenar = $this->service->hitungRumus('pecahan(a, b)', $variabel);

        $this->assertTrue($this->service->cekJawaban('pecahan', '3/4', $jawabanBenar));
        $this->assertFalse($this->service->cekJawaban('pecahan', '0.75', $jawabanBenar));
    }

    public function test_format_himpunan_masih_berjalan(): void
    {
        $this->assertTrue($this->service->cekJawaban('himpunan', '{1, 2, 3}', '{3, 2, 1}'));
        $this->assertFalse($this->service->cekJawaban('himpunan', '{1, 2}', '{1, 2, 3}'));
    }

    public function test_format_aljabar_masih_berjalan(): void
    {
        $this->assertTrue($this->service->jawabanAljabarSama('2*x + 4', '2*(x + 2)'));
        $this->assertFalse($this->service->jawabanAljabarSama('2*x + 4', '2*x + 5'));
    }

    public function test_variabel_urutan_masih_berjalan(): void
    {
        $variabel = ['a' => 1, 'b' => 2, 'c' => 3];
        $jawabanBenar = $this->service->hitungRumus('urutan(a, b, c)', $variabel);

        $this->assertTrue($this->service->cekJawaban(null, '1, 2, 3', $jawabanBenar));
        $this->assertFalse($this->service->cekJawaban(null, '3, 2, 1', $jawabanBenar));
    }

    // ==========================================
    // Format Bilangan Bulat vs Bilangan Desimal Terpisah
    // ==========================================

    public function test_format_bulat_menolak_desimal_bukan_nol(): void
    {
        // Siswa menjawab bulat benar
        $this->assertTrue($this->service->cekJawaban('bulat', '42', 42));
        $this->assertTrue($this->service->cekJawaban('bulat', '-5', -5));
        $this->assertTrue($this->service->cekJawaban('bulat', '10.000', 10000)); // ribuan format ID
        $this->assertTrue($this->service->cekJawaban('bulat', '42.0', 42));
        $this->assertTrue($this->service->cekJawaban('bulat', '42,0', 42));

        // Siswa menjawab desimal ketika diminta bilangan bulat -> HARUS DITOLAK
        $this->assertFalse($this->service->cekJawaban('bulat', '42.5', 42));
        $this->assertFalse($this->service->cekJawaban('bulat', '42,5', 42));
        $this->assertFalse($this->service->cekJawaban('bulat', '42.14', 42));
    }

    public function test_format_desimal_menolak_pembulatan_bulat_jika_kunci_desimal(): void
    {
        // Siswa menjawab desimal presisi benar
        $this->assertTrue($this->service->cekJawaban('desimal', '3.14', 3.14));
        $this->assertTrue($this->service->cekJawaban('desimal', '3,14', 3.14)); // koma ID
        $this->assertTrue($this->service->cekJawaban('desimal', '0.5', 0.5));
        $this->assertTrue($this->service->cekJawaban('desimal', '0,5', 0.5));

        // Kunci adalah 36.14, siswa hanya menjawab 36 (pembulatan bulat) -> HARUS DITOLAK karena format adalah desimal
        $this->assertFalse($this->service->cekJawaban('desimal', '36', 36.14));
        $this->assertFalse($this->service->cekJawaban('desimal', '3', 3.14));
    }

    public function test_kasus_desimal_3_97_vs_4(): void
    {
        // Kasus: Kunci = 3.97, Siswa = 4
        // 1. Pada format 'desimal': 4 HARUS SALAH (karena tidak menuliskan desimal 3.97)
        $this->assertFalse($this->service->cekJawaban('desimal', '4', 3.97));
        $this->assertTrue($this->service->cekJawaban('desimal', '3.97', 3.97));
        $this->assertTrue($this->service->cekJawaban('desimal', '3,97', 3.97));

        // 2. Pada format 'bulat': 4 BENAR (karena kunci dibulatkan ke bilangan bulat terdekat)
        $this->assertTrue($this->service->cekJawaban('bulat', '4', 3.97));

        // 3. Pada format 'angka' (bebas): 4 BENAR (karena toleransi pembulatan)
        $this->assertTrue($this->service->cekJawaban('angka', '4', 3.97));
    }

    public function test_kasus_desimal_panjang_pembulatan_dua_angka(): void
    {
        // Kasus: Kunci = 3.666887 (6 desimal)
        // Siswa membulatkan standar ke 2 angka di belakang koma: 3.67 atau 3,67 -> HARUS BENAR
        $this->assertTrue($this->service->cekJawaban('desimal', '3.67', 3.666887));
        $this->assertTrue($this->service->cekJawaban('desimal', '3,67', 3.666887));

        // Siswa menuliskan presisi penuh 3.666887 -> HARUS BENAR
        $this->assertTrue($this->service->cekJawaban('desimal', '3.666887', 3.666887));

        // Siswa menjawab salah (misal 3.60 atau 3.5 atau 4) -> HARUS SALAH
        $this->assertFalse($this->service->cekJawaban('desimal', '3.60', 3.666887));
        $this->assertFalse($this->service->cekJawaban('desimal', '3.5', 3.666887));
        $this->assertFalse($this->service->cekJawaban('desimal', '4', 3.666887));
    }
}
