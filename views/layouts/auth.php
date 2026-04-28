<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Crédinor</title>
    <link rel="icon" type="image/png" href="<?= asset('img/logo.png') ?>">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Iconsax -->
    <link href="https://iconsax.gitlab.io/i/icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="h-full flex items-center justify-center bg-slate-50 p-4 text-slate-900 relative overflow-hidden">
    <!-- Fondos decorativos -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

    <?= $content ?>
</body>
</html>
