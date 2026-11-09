<?php

namespace Tests\Unit;

use App\Services\MathExpressionProvider;
use NXP\MathExecutor;
use PHPUnit\Framework\TestCase;

class MathExpressionProviderTest extends TestCase
{
    private MathExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executor = new MathExecutor();
        (new MathExpressionProvider())->registerFunctions($this->executor);

        // EvaluasiSoalService mendaftarkan 3 fungsi tambahan ini LANGSUNG (bukan lewat
        // MathExpressionProvider) sebelum mendelegasikan sisanya ke registerFunctions().
        // Direplikasi di sini supaya test anti-drift mencerminkan mesin eksekusi sebenarnya
        // yang dipakai answer_formula, bukan MathExpressionProvider secara terisolasi.
        $this->executor->addFunction('akar', fn($n) => sqrt((float) $n));
        $this->executor->addFunction('pecahan', fn($p, $q) => "{$p}/{$q}");
        $this->executor->addFunction('urutan', fn(...$args) => implode(',', $args));
    }

    public function test_akar_pangkat_basis_positif(): void
    {
        $this->assertEqualsWithDelta(2.0, $this->executor->execute('akar_pangkat(8, 3)'), 0.0001);
    }

    public function test_akar_pangkat_basis_negatif_pangkat_ganjil_valid_secara_real(): void
    {
        // Sebelum perbaikan, ini menghasilkan NAN karena pow() PHP tidak mendukung
        // basis negatif dengan pangkat pecahan secara langsung.
        $this->assertEqualsWithDelta(-2.0, $this->executor->execute('akar_pangkat(-8, 3)'), 0.0001);
    }

    public function test_akar_pangkat_basis_negatif_pangkat_genap_domain_tidak_valid(): void
    {
        $this->assertSame(0.0, (float) $this->executor->execute('akar_pangkat(-16, 4)'));
    }

    public function test_akar_kubik(): void
    {
        $this->assertEqualsWithDelta(3.0, $this->executor->execute('akar_kubik(27)'), 0.0001);
        $this->assertEqualsWithDelta(-3.0, $this->executor->execute('akar_kubik(-27)'), 0.0001);
    }

    public function test_logaritma_basis_custom(): void
    {
        $this->assertEqualsWithDelta(3.0, $this->executor->execute('logaritma(8, 2)'), 0.0001);
        $this->assertEqualsWithDelta(2.0, $this->executor->execute('logaritma(100, 10)'), 0.0001);
    }

    public function test_logaritma_domain_tidak_valid_mengembalikan_nol(): void
    {
        $this->assertSame(0.0, (float) $this->executor->execute('logaritma(-5, 2)'));
    }

    public function test_simpangan_rata_rata(): void
    {
        // Data: 2, 4, 6, 8 -> mean 5, deviasi mutlak: 3,1,1,3 -> rata-rata = 2
        $this->assertEqualsWithDelta(2.0, $this->executor->execute('simpangan_rata_rata(2, 4, 6, 8)'), 0.0001);
    }

    public function test_luas_permukaan_prisma(): void
    {
        // luas_alas=24, keliling_alas=20, t=10 -> (2*24) + (20*10) = 248
        $this->assertEqualsWithDelta(248.0, $this->executor->execute('luas_permukaan_prisma(24, 20, 10)'), 0.0001);
    }

    public function test_luas_permukaan_limas(): void
    {
        // luas_alas=16, keliling_alas=16, tinggi_miring=5 -> 16 + (0.5*16*5) = 56
        $this->assertEqualsWithDelta(56.0, $this->executor->execute('luas_permukaan_limas(16, 16, 5)'), 0.0001);
    }

    /**
     * Anti-drift guard: setiap fungsi yang disebut di daftarUntukPrompt() (dipakai AiGuruController
     * untuk memberi tahu AI fungsi apa saja yang valid) HARUS benar-benar terdaftar di MathExecutor.
     * Kalau ini gagal, berarti prompt AI akan merekomendasikan fungsi yang sebenarnya tidak ada.
     */
    public function test_semua_fungsi_di_daftar_prompt_benar_benar_terdaftar(): void
    {
        $terdaftar = array_keys($this->executor->getFunctions());
        $daftarPrompt = (new MathExpressionProvider())->daftarUntukPrompt();

        foreach ($daftarPrompt as $kategori => $daftarFungsi) {
            foreach ($daftarFungsi as $signature) {
                $nama = substr($signature, 0, strpos($signature, '('));
                $this->assertContains(
                    $nama,
                    $terdaftar,
                    "Fungsi '{$nama}' di kategori '{$kategori}' disebut di prompt AI tapi tidak terdaftar di MathExecutor."
                );
            }
        }
    }

    /** Smoke test regresi: fungsi lama yang sudah ada tidak boleh terpengaruh perubahan ini. */
    public function test_fungsi_lama_tetap_berjalan(): void
    {
        $this->assertEqualsWithDelta(1.0, $this->executor->execute('sin(90)'), 0.0001);
        $this->assertEqualsWithDelta(1.0, $this->executor->execute('cos(0)'), 0.0001);
        $this->assertEqualsWithDelta(5.0, $this->executor->execute('hipotenusa(3, 4)'), 0.0001);
        $this->assertEqualsWithDelta(6.0, $this->executor->execute('fpb(12, 18)'), 0.0001);
        $this->assertEqualsWithDelta(36.0, $this->executor->execute('kpk(12, 18)'), 0.0001);
        $this->assertEqualsWithDelta(24.0, $this->executor->execute('volume_prisma(12, 2)'), 0.0001);
        $this->assertEqualsWithDelta(8.0, $this->executor->execute('volume_limas(12, 2)'), 0.0001);
    }
}
