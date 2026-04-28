<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\MoneyHelper;
use PHPUnit\Framework\TestCase;

class MoneyHelperTest extends TestCase
{
    public function testFormatBasico(): void
    {
        $this->assertSame('$ 1.500,00', MoneyHelper::format(1500.0));
    }

    public function testFormatCero(): void
    {
        $this->assertSame('$ 0,00', MoneyHelper::format(0.0));
    }

    public function testFormatShort(): void
    {
        $this->assertSame('$ 1.500', MoneyHelper::formatShort(1500.0));
    }

    public function testDistribuirCuotasExacto(): void
    {
        [$normal, $ultima] = MoneyHelper::distribuirCuotas(300.0, 3);
        $this->assertEqualsWithDelta(100.0, $normal, 0.001);
        $this->assertEqualsWithDelta(100.0, $ultima, 0.001);
    }

    public function testDistribuirCuotasConResiduoCentavos(): void
    {
        // 100 / 3 = 33.33 × 2 = 66.66 → última = 33.34
        [$normal, $ultima] = MoneyHelper::distribuirCuotas(100.0, 3);
        $this->assertEqualsWithDelta(33.33, $normal, 0.001);
        $this->assertEqualsWithDelta(33.34, $ultima, 0.001);
        $this->assertEqualsWithDelta(100.0, $normal * 2 + $ultima, 0.001);
    }

    public function testDistribuirCuotasCero(): void
    {
        [$normal, $ultima] = MoneyHelper::distribuirCuotas(500.0, 0);
        $this->assertSame(0.0, $normal);
        $this->assertSame(0.0, $ultima);
    }

    public function testDistribuirUnaCuota(): void
    {
        [$normal, $ultima] = MoneyHelper::distribuirCuotas(500.0, 1);
        $this->assertEqualsWithDelta(500.0, $ultima, 0.001);
    }
}
