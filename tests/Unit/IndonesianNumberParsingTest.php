<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\EvaluasiSoalService;

class IndonesianNumberParsingTest extends TestCase
{
    public function test_parsing_ribuan()
    {
        $service = new EvaluasiSoalService();

        // 80.000 (80 ribu) TIDAK SAMA dengan 80
        $this->assertFalse($service->jawabanNumerikSama('80.000', 80));
        
        // 80.000 (80 ribu) SAMA dengan 80000
        $this->assertTrue($service->jawabanNumerikSama('80.000', 80000));
        
        // 1.000.000 SAMA dengan 1000000
        $this->assertTrue($service->jawabanNumerikSama('1.000.000', 1000000));
        
        // 80,5 SAMA dengan 80.5
        $this->assertTrue($service->jawabanNumerikSama('80,5', 80.5));
        
        // 1.234,56 SAMA dengan 1234.56
        $this->assertTrue($service->jawabanNumerikSama('1.234,56', 1234.56));
    }
}
