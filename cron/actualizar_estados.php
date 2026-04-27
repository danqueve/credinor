<?php
/**
 * cron/actualizar_estados.php
 *
 * Marca cuotas vencidas sin pago y verifica créditos finalizados.
 * Correr diariamente junto con devengar_mora.php.
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('CRON_MODE', true);

require ROOT_PATH . '/vendor/autoload.php';

session_start();
$_SESSION['usuario_id'] = 1;
$_SESSION['sucursal_id'] = 1;

$service = new \App\Services\MoraService();

try {
    $actualizadas = $service->actualizarEstadosCuotas();
    echo sprintf(
        "[%s] Estados actualizados — Cuotas marcadas como vencidas: %d\n",
        date('Y-m-d H:i:s'),
        $actualizadas
    );
} catch (\Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR estados: " . $e->getMessage() . "\n";
    exit(1);
}
