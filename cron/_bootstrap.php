<?php
// cron/_bootstrap.php — Bootstrap compartido para todos los cron jobs
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('CRON_MODE', true);
define('START_TIME', microtime(true));

require ROOT_PATH . '/vendor/autoload.php';

// Cargar .env
$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

// Helpers globales (espejados desde public/index.php)
function config(string $key): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require ROOT_PATH . '/src/Config/app.php';
    }
    return $cfg[$key] ?? null;
}

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Sesión mínima para que Auth::id() funcione
session_start();
$_SESSION['usuario_id']  = 1;
$_SESSION['sucursal_id'] = 1;
