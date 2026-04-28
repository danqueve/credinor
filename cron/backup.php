<?php
/**
 * cron/backup.php
 *
 * Backup diario de la base de datos. Retiene los últimos 7 dumps.
 *
 *   30 1 * * * php /var/www/credinor/cron/backup.php >> /var/log/credinor_backup.log 2>&1
 *
 * Requiere: mysqldump en el PATH del sistema.
 * El directorio backups/ debe estar fuera del webroot o protegido por .htaccess.
 */

require __DIR__ . '/_bootstrap.php';

$lockFile = sys_get_temp_dir() . '/credinor_backup.lock';
$lock = fopen($lockFile, 'c');
if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
    echo "[" . date('Y-m-d H:i:s') . "] SKIP backup — otra instancia ya está corriendo.\n";
    exit(0);
}

$backupDir = ROOT_PATH . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0750, true);
}

// Proteger con .htaccess si no existe
$htaccess = $backupDir . '/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Require all denied\n");
}

$host     = $_ENV['DB_HOST']     ?? 'localhost';
$port     = $_ENV['DB_PORT']     ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? '';
$username = $_ENV['DB_USERNAME'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

if (!$database || !$username) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR backup: Variables DB no configuradas.\n";
    flock($lock, LOCK_UN);
    fclose($lock);
    exit(1);
}

$filename  = $backupDir . '/backup_' . date('Y-m-d_His') . '.sql.gz';
$mysqlpass = escapeshellarg($password);
$mysqluser = escapeshellarg($username);
$mysqldb   = escapeshellarg($database);
$mysqlhost = escapeshellarg($host);
$mysqlport = escapeshellarg($port);

// Construir comando sin credenciales en argumentos directos (usa MYSQL_PWD)
$env     = "MYSQL_PWD={$mysqlpass}";
$cmd     = "{$env} mysqldump --single-transaction --quick --routines"
         . " -h {$mysqlhost} -P {$mysqlport} -u {$mysqluser} {$mysqldb}"
         . " | gzip > " . escapeshellarg($filename) . " 2>&1";

exec($cmd, $output, $exitCode);

if ($exitCode !== 0 || !file_exists($filename) || filesize($filename) === 0) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR backup: mysqldump falló (exit {$exitCode}).\n";
    @unlink($filename);
    flock($lock, LOCK_UN);
    fclose($lock);
    exit(1);
}

echo sprintf(
    "[%s] Backup completado — %s (%.1f KB)\n",
    date('Y-m-d H:i:s'),
    basename($filename),
    filesize($filename) / 1024
);

// Rotar: conservar solo los últimos 7 backups
$files = glob($backupDir . '/backup_*.sql.gz') ?: [];
usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
foreach (array_slice($files, 7) as $old) {
    unlink($old);
    echo "[" . date('Y-m-d H:i:s') . "] Backup eliminado: " . basename($old) . "\n";
}

flock($lock, LOCK_UN);
fclose($lock);
