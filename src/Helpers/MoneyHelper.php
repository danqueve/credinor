<?php
// src/Helpers/MoneyHelper.php
namespace App\Helpers;

class MoneyHelper
{
    public static function format(float $amount, string $symbol = '$'): string
    {
        return $symbol . ' ' . number_format($amount, 2, ',', '.');
    }

    public static function formatShort(float $amount, string $symbol = '$'): string
    {
        return $symbol . ' ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Divide el monto entre cuotas y retorna array [monto_normal, monto_ultima]
     * para ajuste de centavos en la última cuota.
     */
    public static function distribuirCuotas(float $total, int $cantidad): array
    {
        if ($cantidad <= 0) return [0.0, 0.0];
        $montoCuota = round($total / $cantidad, 2);
        $sumParcial = round($montoCuota * ($cantidad - 1), 2);
        $ultimaCuota = round($total - $sumParcial, 2);
        return [$montoCuota, $ultimaCuota];
    }
}
