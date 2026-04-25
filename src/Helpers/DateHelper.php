<?php
// src/Helpers/DateHelper.php
namespace App\Helpers;

class DateHelper
{
    /**
     * Genera las fechas de vencimiento de cuotas según frecuencia.
     * Retorna un array de strings 'Y-m-d'.
     */
    public static function generarFechas(
        string $fechaPrimera,
        int    $cantidad,
        string $frecuencia
    ): array {
        $fechas  = [];
        $current = new \DateTimeImmutable($fechaPrimera);

        for ($i = 0; $i < $cantidad; $i++) {
            $fechas[] = $current->format('Y-m-d');

            $current = match ($frecuencia) {
                'diaria'    => $current->modify('+1 day'),
                'semanal'   => $current->modify('+7 days'),
                'quincenal' => $current->modify('+15 days'),
                'mensual'   => self::sumarUnMes($current),
                default     => throw new \InvalidArgumentException("Frecuencia inválida: {$frecuencia}"),
            };
        }

        return $fechas;
    }

    /**
     * Suma un mes respetando el último día del mes destino.
     * Ej: 31/01 + 1 mes = 28/02 (no 03/03)
     */
    private static function sumarUnMes(\DateTimeImmutable $date): \DateTimeImmutable
    {
        $dia = (int) $date->format('d');
        $next = $date->modify('first day of next month');
        $diasEnMes = (int) $next->format('t');
        $diaFinal  = min($dia, $diasEnMes);
        return $next->setDate(
            (int) $next->format('Y'),
            (int) $next->format('m'),
            $diaFinal
        );
    }

    public static function formatoArg(string $fecha): string
    {
        return date('d/m/Y', strtotime($fecha));
    }

    public static function diasDesde(string $fecha): int
    {
        $diff = (new \DateTime())->diff(new \DateTime($fecha));
        return $diff->invert ? $diff->days : -$diff->days;
    }
}
