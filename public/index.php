<?php
// public/index.php — Front Controller

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('START_TIME', microtime(true));

// Autoload
require ROOT_PATH . '/vendor/autoload.php';

// Helpers globales
function config(string $key): mixed
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require ROOT_PATH . '/src/Config/app.php';
    }
    return $cfg[$key] ?? null;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function asset(string $path): string
{
    return config('url') . '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string
{
    return config('url') . '/' . ltrim($path, '/');
}

function csrf_field(): string
{
    $token = \App\Core\Session::csrfToken();
    return '<input type="hidden" name="_csrf" value="' . $token . '">';
}

// Timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Iniciar sesión
\App\Core\Session::start();

// ============================================================
// RUTAS
// ============================================================
$router = new \App\Core\Router();
$auth   = [\App\Middleware\AuthMiddleware::class];

// Autenticación
$router->get('/login',  [\App\Controllers\AuthController::class, 'loginForm']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout'], $auth);

// Dashboard
$router->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'], $auth);
$router->get('/',          [\App\Controllers\DashboardController::class, 'index'], $auth);

// ——— CLIENTES ———————————————————————————————————————————
$router->get('/vendedor/clientes',              [\App\Controllers\ClientesController::class, 'index'],   $auth);
$router->get('/vendedor/clientes/nuevo',        [\App\Controllers\ClientesController::class, 'crear'],   $auth);
$router->post('/vendedor/clientes',             [\App\Controllers\ClientesController::class, 'store'],   $auth);
$router->get('/vendedor/clientes/{id}',         [\App\Controllers\ClientesController::class, 'show'],    $auth);
$router->get('/vendedor/clientes/{id}/editar',  [\App\Controllers\ClientesController::class, 'editar'],  $auth);
$router->post('/vendedor/clientes/{id}/editar', [\App\Controllers\ClientesController::class, 'update'],  $auth);
$router->get('/api/clientes/buscar',            [\App\Controllers\ClientesController::class, 'buscarJson'], $auth);

// Admin: clientes (solo vista)
$router->get('/admin/clientes', [\App\Controllers\ClientesController::class, 'index'], $auth);

// ——— CRÉDITOS VENDEDOR ——————————————————————————————————
$router->get('/vendedor/creditos',       [\App\Controllers\CreditosController::class, 'misCreditos'], $auth);
$router->get('/vendedor/creditos/nuevo', [\App\Controllers\CreditosController::class, 'nuevo'],       $auth);
$router->post('/vendedor/creditos',      [\App\Controllers\CreditosController::class, 'store'],       $auth);

// ——— CRÉDITOS COMPARTIDOS ———————————————————————————————
$router->get('/creditos/{id}', [\App\Controllers\CreditosController::class, 'show'], $auth);

// ——— CRÉDITOS ADMIN —————————————————————————————————————
$router->get('/admin/creditos',                      [\App\Controllers\CreditosController::class, 'adminIndex'],   $auth);
$router->get('/admin/creditos/{id}/autorizar',       [\App\Controllers\CreditosController::class, 'formAutorizar'], $auth);
$router->post('/admin/creditos/{id}/autorizar',      [\App\Controllers\CreditosController::class, 'autorizar'],    $auth);
$router->post('/admin/creditos/{id}/rechazar',       [\App\Controllers\CreditosController::class, 'rechazar'],     $auth);
$router->get('/admin/creditos/{credito_id}/pago/{cuota_id}',        [\App\Controllers\PagosController::class, 'adminForm'],       $auth);
$router->post('/admin/creditos/{credito_id}/pago/{cuota_id}',       [\App\Controllers\PagosController::class, 'adminStore'],      $auth);
$router->get('/admin/api/creditos/{credito_id}/proxima-cuota',      [\App\Controllers\PagosController::class, 'proximaCuotaJson'], $auth);
$router->post('/admin/api/creditos/{credito_id}/pago/{cuota_id}',   [\App\Controllers\PagosController::class, 'adminStoreJson'],   $auth);

    // ——— COBRADOR ————————————————————————————————————————————
    $router->get('/cobrador/agenda',      [\App\Controllers\CobradorController::class, 'agenda'],     $auth);
    $router->get('/cobrador/historial',   [\App\Controllers\CobradorController::class, 'historial'],  $auth);
    $router->get('/cobrador/caja',        [\App\Controllers\CobradorController::class, 'caja'],       $auth);
    $router->post('/cobrador/caja/cerrar',[\App\Controllers\CobradorController::class, 'cerrarCaja'], $auth);
    $router->get('/cobrador/rendiciones', [\App\Controllers\CobradorController::class, 'rendiciones'], $auth);
    $router->get('/cobrador/rendiciones/{id}', [\App\Controllers\CobradorController::class, 'rendicionDetalle'], $auth);

// ——— PAGOS ———————————————————————————————————————————————
$router->get('/cobrador/pago/{credito_id}/{cuota_id}',  [\App\Controllers\PagosController::class, 'form'],   $auth);
$router->post('/cobrador/pago/{credito_id}/{cuota_id}', [\App\Controllers\PagosController::class, 'store'],  $auth);
$router->get('/cobrador/pago/{pago_id}/recibo',         [\App\Controllers\PagosController::class, 'recibo'], $auth);

// ——— RENDICIONES ADMIN ———————————————————————————————————
$router->get('/admin/rendiciones',                          [\App\Controllers\RendicionesController::class, 'index'],     $auth);
$router->get('/admin/rendiciones/{id}',                     [\App\Controllers\RendicionesController::class, 'show'],      $auth);
$router->post('/admin/rendiciones/{id}/confirmar',          [\App\Controllers\RendicionesController::class, 'confirmar'], $auth);
$router->post('/admin/rendiciones/{id}/rechazar',           [\App\Controllers\RendicionesController::class, 'rechazar'],  $auth);

// ——— REPORTES ————————————————————————————————————————————
$router->get('/admin/reportes',                             [\App\Controllers\ReportesController::class, 'index'],      $auth);
$router->get('/admin/reportes/cartera',                     [\App\Controllers\ReportesController::class, 'cartera'],    $auth);
$router->get('/admin/reportes/mora',                        [\App\Controllers\ReportesController::class, 'mora'],       $auth);
$router->get('/admin/reportes/cobradores',                  [\App\Controllers\ReportesController::class, 'cobradores'], $auth);

// ——— SUCURSALES ——————————————————————————————————————————
$router->get('/admin/sucursales',                           [\App\Controllers\SucursalesController::class, 'index'],       $auth);
$router->get('/admin/sucursales/nueva',                     [\App\Controllers\SucursalesController::class, 'crear'],       $auth);
$router->post('/admin/sucursales',                          [\App\Controllers\SucursalesController::class, 'store'],       $auth);
$router->get('/admin/sucursales/{id}/editar',               [\App\Controllers\SucursalesController::class, 'editar'],      $auth);
$router->post('/admin/sucursales/{id}/editar',              [\App\Controllers\SucursalesController::class, 'update'],      $auth);
$router->post('/admin/sucursales/{id}/toggle',              [\App\Controllers\SucursalesController::class, 'toggleActiva'],$auth);

// ——— USUARIOS ————————————————————————————————————————————
$router->get('/admin/usuarios',                             [\App\Controllers\UsuariosController::class, 'index'],       $auth);
$router->get('/admin/usuarios/nuevo',                       [\App\Controllers\UsuariosController::class, 'crear'],       $auth);
$router->post('/admin/usuarios',                            [\App\Controllers\UsuariosController::class, 'store'],       $auth);
$router->get('/admin/usuarios/{id}/editar',                 [\App\Controllers\UsuariosController::class, 'editar'],      $auth);
$router->post('/admin/usuarios/{id}/editar',                [\App\Controllers\UsuariosController::class, 'update'],      $auth);
$router->post('/admin/usuarios/{id}/toggle',                [\App\Controllers\UsuariosController::class, 'toggleActivo'],$auth);

// ============================================================
// Dispatch
// ============================================================
$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
