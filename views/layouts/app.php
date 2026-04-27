<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Crédinor') ?> — Crédinor</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/iconsax/css/iconsax.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <meta name="csrf-token" content="<?= \App\Core\Session::csrfToken() ?>">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="h-full bg-slate-100/60">

<div class="flex h-full" x-data="{ sidebarOpen: false }">

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-20 lg:hidden"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak></div>

    <!-- ========== SIDEBAR ========== -->
    <?php require ROOT_PATH . '/views/layouts/partials/sidebar.php'; ?>

    <!-- ========== CONTENT AREA ========== -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        <!-- TOPBAR -->
        <header class="topbar">
            <!-- Mobile hamburger -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden mr-4 w-11 h-11 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors"
                    aria-label="Abrir menú">
                <i class="isax isax-menu-1 text-xl"></i>
            </button>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Right: User info + logout -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold text-slate-800 leading-none">
                        <?= e(\App\Core\Auth::user()['nombre'] ?? '') ?>
                    </p>
                    <p class="text-[11px] text-slate-400 mt-0.5 capitalize font-medium">
                        <?= e(\App\Core\Auth::rol() ?? '') ?>
                    </p>
                </div>

                <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold ring-2 ring-brand-200 ring-offset-1 shrink-0">
                    <?= mb_strtoupper(mb_substr(\App\Core\Auth::user()['nombre'] ?? 'U', 0, 1)) ?>
                </div>

                <div class="h-5 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                <a href="<?= url('logout') ?>"
                   class="w-11 h-11 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all"
                   title="Cerrar sesión">
                    <i class="isax isax-logout text-lg"></i>
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            <?php require ROOT_PATH . '/views/layouts/partials/flash.php'; ?>
            <?= $content ?>
        </main>

    </div>
</div>

</body>
</html>
