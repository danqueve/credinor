<?php
/**
 * cron/actualizar_estados.php
 *
 * Marca cuotas vencidas sin pago y verifica créditos finalizados.
 * Correr diariamente junto con devengar_mora.php.
 *
 *   2 0 * * * php /var/www/credinor/cron/actualizar_estados.php >> /var/log/credinor_estados.log 2>&1
 */

require __DIR__ . '/_bootstrap.php';

// Lock file: evita ejecuciones concurrentes
$lockFile = sys_get_temp_dir() . '/credinor_actualizar_estados.lock';
$lock = fopen($lockFile, 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] SKIP actualizar_estados — otra instancia ya está corriendo.\n";
    exit(0);
}

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
    flock($lock, LOCK_UN);
    fclose($lock);
    exit(1);
}

flock($lock, LOCK_UN);
fclose($lock);
