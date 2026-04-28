<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\DateHelper;
use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    public function testGenerarFechasDiaria(): void
    {
        $fechas = DateHelper::generarFechas('2025-01-01', 3, 'diaria');
        $this->assertSame(['2025-01-01', '2025-01-02', '2025-01-03'], $fechas);
    }

    public function testGenerarFechasSemanal(): void
    {
        $fechas = DateHelper::generarFechas('2025-01-06', 3, 'semanal');
        $this->assertSame(['2025-01-06', '2025-01-13', '2025-01-20'], $fechas);
    }

    public function testGenerarFechasQuincenal(): void
    {
        $fechas = DateHelper::generarFechas('2025-01-01', 3, 'quincenal');
        $this->assertSame(['2025-01-01', '2025-01-16', '2025-01-31'], $fechas);
    }

    public function testGenerarFechasMensual(): void
    {
        $fechas = DateHelper::generarFechas('2025-01-31', 3, 'mensual');
        // 31/01 + 1 mes → 28/02 (febrero 2025 tiene 28 días)
        $this->assertSame('2025-01-31', $fechas[0]);
        $this->assertSame('2025-02-28', $fechas[1]);
        $this->assertSame('2025-03-28', $fechas[2]);
    }

    public function testGenerarFechasCantidadCero(): void
    {
        $fechas = DateHelper::generarFechas('2025-01-01', 0, 'diaria');
        $this->assertSame([], $fechas);
    }

    public function testGenerarFechasFrecuenciaInvalidaLanzaExcepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateHelper::generarFechas('2025-01-01', 2, 'invalida');
    }

    public function testFormatoArg(): void
    {
        $this->assertSame('15/03/2025', DateHelper::formatoArg('2025-03-15'));
    }
}
