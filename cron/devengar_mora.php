<?php
/**
 * cron/devengar_mora.php
 *
 * Cron diario para devengar mora.
 * Ejecutar a las 00:01 de cada día:
 *
 *   php C:\wamp64\www\credinor\cron\devengar_mora.php
 *
 * En WAMP/cron de hosting, agregar:
 *   1 0 * * * php /var/www/credinor/cron/devengar_mora.php >> /var/log/credinor_mora.log 2>&1
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('CRON_MODE', true);

require ROOT_PATH . '/vendor/autoload.php';

// Simular Auth::id() = 1 (sistema) para el servicio
session_start();
$_SESSION['usuario_id'] = 1;
$_SESSION['sucursal_id'] = 1;

$service = new \App\Services\MoraService();

try {
    $resultado = $service->devengarDia();
    $log = sprintf(
        "[%s] Mora devengada — Fecha: %s | Cuotas procesadas: %d | Omitidas: %d | Total mora: $%.2f\n",
        date('Y-m-d H:i:s'),
        $resultado['fecha'],
        $resultado['procesadas'],
        $resultado['omitidas'],
        $resultado['mora_total']
    );
    echo $log;
} catch (\Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR mora: " . $e->getMessage() . "\n";
    exit(1);
}
