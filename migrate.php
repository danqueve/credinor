<?php
/**
 * migrate.php — Runner de migraciones
 *
 * Uso:
 *   php migrate.php           → aplica migraciones pendientes
 *   php migrate.php --status  → muestra estado sin aplicar nada
 *
 * Las migraciones se aplican en orden alfabético de nombre de archivo.
 * Una vez aplicada, se registra en la tabla 'migrations' para no repetirse.
 */

declare(strict_types=1);

define('ROOT_PATH', __DIR__);

require ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

function config(string $key): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require ROOT_PATH . '/src/Config/app.php';
    }
    return $cfg[$key] ?? null;
}

// Conectar a la DB directamente (sin el singleton de la app)
$dbConfig = require ROOT_PATH . '/src/Config/database.php';
$dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";

try {
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
} catch (PDOException $e) {
    fwrite(STDERR, "Error de conexión: " . $e->getMessage() . "\n");
    exit(1);
}

// Crear tabla de control de migraciones si no existe
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        filename    VARCHAR(200) NOT NULL UNIQUE,
        applied_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Obtener migraciones ya aplicadas
$applied = $pdo->query("SELECT filename FROM migrations ORDER BY filename")
               ->fetchAll(PDO::FETCH_COLUMN);
$applied = array_flip($applied);

// Escanear archivos de migración
$files = glob(ROOT_PATH . '/migrations/*.sql');
sort($files);

$statusOnly = in_array('--status', $argv ?? [], true);

$pending   = 0;
$ejecutadas = 0;
$errores   = 0;

foreach ($files as $file) {
    $name = basename($file);
    $estado = isset($applied[$name]) ? '[OK]    ' : '[PENDING]';
    if ($statusOnly) {
        echo "{$estado} {$name}\n";
        continue;
    }

    if (isset($applied[$name])) {
        echo "[OK]     {$name}\n";
        continue;
    }

    $pending++;
    echo "[APPLY]  {$name} ... ";
    $sql = file_get_contents($file);

    try {
        $pdo->exec($sql);
        $stmt = $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->execute([$name]);
        echo "OK\n";
        $ejecutadas++;
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        $errores++;
    }
}

if (!$statusOnly) {
    echo "\n";
    if ($pending === 0) {
        echo "No hay migraciones pendientes.\n";
    } else {
        echo "Migraciones aplicadas: {$ejecutadas} / {$pending}";
        if ($errores > 0) echo " ({$errores} errores)";
        echo "\n";
    }
    exit($errores > 0 ? 1 : 0);
}
