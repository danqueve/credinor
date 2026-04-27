<!-- views/errors/404.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>404 — Página no encontrada</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center p-8">
        <div class="text-6xl mb-4">🔍</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Página no encontrada</h1>
        <p class="text-gray-500 mb-6">La ruta que buscás no existe.</p>
        <a href="<?= url('dashboard') ?>" class="btn-primary">← Ir al Dashboard</a>
    </div>
</body>
</html>
