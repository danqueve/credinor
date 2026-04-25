<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Crédinor') ?> — Crédinor Préstamos</title>
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Iconsax (Linear) -->
    <link href="https://iconsax.gitlab.io/i/icons.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-slate-900">

<!-- ======= LAYOUT PRINCIPAL ======= -->
<div class="min-h-full flex flex-col" x-data="{ sidebarOpen: false }">

    <!-- TOP NAV -->
    <nav class="glass-nav shadow-sm">
        <div class="max-w-7xl mx-auto px-5 flex items-center justify-between h-16">
            <!-- Logo + hamburger -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors"
                        aria-label="Menú">
                    <i class="isax isax-menu-1 text-2xl"></i>
                </button>
                <a href="<?= url('dashboard') ?>" class="flex items-center gap-2 font-bold text-xl tracking-tight text-brand-600" style="font-family: 'Outfit', sans-serif;">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-500/30">
                        <i class="isax isax-wallet-money"></i>
                    </div>
                    Crédinor
                </a>
            </div>

            <!-- User info -->
            <div class="flex items-center gap-4 text-sm">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="font-semibold text-slate-800">
                        <?= e(\App\Core\Auth::user()['nombre'] ?? '') ?>
                    </span>
                    <span class="text-[10px] uppercase tracking-wider font-bold text-brand-600">
                        <?= e(\App\Core\Auth::rol() ?? '') ?>
                    </span>
                </div>
                
                <div class="h-8 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                <a href="<?= url('logout') ?>"
                   class="flex items-center justify-center w-10 h-10 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                   title="Cerrar sesión">
                    <i class="isax isax-logout text-xl"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- BODY: sidebar + main -->
    <div class="flex flex-1 overflow-hidden">

        <!-- SIDEBAR -->
        <?php require ROOT_PATH . '/views/layouts/partials/sidebar.php'; ?>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?php require ROOT_PATH . '/views/layouts/partials/flash.php'; ?>
            <?= $content ?>
        </main>
    </div>
</div>

</body>
</html>
