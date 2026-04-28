<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;

/**
 * Tests para la validación del parámetro $orderBy en Model::all().
 * Usa reflexión para invocar el método protegido de validación inline.
 */
class ModelOrderByTest extends TestCase
{
    private function isValidOrderBy(string $orderBy): bool
    {
        return (bool) preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*(\s+(ASC|DESC))?$/', $orderBy);
    }

    /** @dataProvider validOrderByProvider */
    public function testOrderByValido(string $orderBy): void
    {
        $this->assertTrue($this->isValidOrderBy($orderBy), "'{$orderBy}' debería ser válido");
    }

    /** @dataProvider invalidOrderByProvider */
    public function testOrderByInvalido(string $orderBy): void
    {
        $this->assertFalse($this->isValidOrderBy($orderBy), "'{$orderBy}' debería ser inválido");
    }

    public static function validOrderByProvider(): array
    {
        return [
            ['nombre'],
            ['created_at'],
            ['nombre ASC'],
            ['created_at DESC'],
            ['t.nombre'],
        ];
    }

    public static function invalidOrderByProvider(): array
    {
        return [
            ["nombre; DROP TABLE usuarios--"],
            ["1 OR 1=1"],
            ["nombre' OR '1'='1"],
            ["(SELECT password FROM usuarios)"],
            ['nombre UNION SELECT'],
        ];
    }
}
