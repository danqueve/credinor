<?php
/**
 * cron/devengar_mora.php
 *
 * Cron diario para devengar mora.
 * Idempotente: usa UNIQUE (cuota_id, fecha) en mora_devengada.
 *
 * Ejecutar a las 00:01 de cada día:
 *   1 0 * * * php /var/www/credinor/cron/devengar_mora.php >> /var/log/credinor_mora.log 2>&1
 */

require __DIR__ . '/_bootstrap.php';

// Lock file: evita ejecuciones concurrentes
$lockFile = sys_get_temp_dir() . '/credinor_devengar_mora.lock';
$lock = fopen($lockFile, 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] SKIP devengar_mora — otra instancia ya está corriendo.\n";
    exit(0);
}

$service = new \App\Services\MoraService();

try {
    $resultado = $service->devengarDia();
    echo sprintf(
        "[%s] Mora devengada — Fecha: %s | Cuotas procesadas: %d | Omitidas: %d | Total mora: $%.2f\n",
        date('Y-m-d H:i:s'),
        $resultado['fecha'],
        $resultado['procesadas'],
        $resultado['omitidas'],
        $resultado['mora_total']
    );
} catch (\Throwable $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR mora: " . $e->getMessage() . "\n";
    flock($lock, LOCK_UN);
    fclose($lock);
    exit(1);
}

flock($lock, LOCK_UN);
fclose($lock);
