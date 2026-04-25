<?php
// views/layouts/partials/sidebar.php
use App\Core\Auth;

$rol = Auth::rol();

$navAdmin = [
    ['href' => '/dashboard',            'icon' => 'isax-home-2',         'label' => 'Dashboard'],
    ['href' => '/admin/creditos',       'icon' => 'isax-document-text',  'label' => 'Créditos'],
    ['href' => '/admin/clientes',       'icon' => 'isax-profile-2user',  'label' => 'Clientes'],
    ['href' => '/admin/rendiciones',    'icon' => 'isax-wallet-money',   'label' => 'Rendiciones'],
    ['href' => '/admin/usuarios',       'icon' => 'isax-user',           'label' => 'Usuarios'],
    ['href' => '/admin/sucursales',     'icon' => 'isax-building',       'label' => 'Sucursales'],
    ['href' => '/admin/reportes',       'icon' => 'isax-chart-21',       'label' => 'Reportes'],
];
$navStaff = [
    ['href' => '/dashboard',          'icon' => 'isax-home-2',        'label' => 'Inicio'],
    ['href' => '/cobrador/agenda',    'icon' => 'isax-calendar-1',    'label' => 'Agenda'],
    ['href' => '/vendedor/clientes',  'icon' => 'isax-profile-2user', 'label' => 'Clientes'],
    ['href' => '/vendedor/creditos',  'icon' => 'isax-document-text', 'label' => 'Créditos'],
    ['href' => '/cobrador/caja',      'icon' => 'isax-card-tick',     'label' => 'Cerrar caja'],
    ['href' => '/cobrador/rendiciones','icon'=> 'isax-wallet-money',  'label' => 'Rendiciones'],
    ['href' => '/cobrador/historial', 'icon' => 'isax-clock',         'label' => 'Historial'],
];

$nav = match($rol) {
    'admin'    => $navAdmin,
    'cobrador', 'vendedor' => $navStaff,
    default    => [],
};

$current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!-- Sidebar: oculto en mobile por defecto, visible en md+ -->
<aside class="bg-white border-r border-gray-100 w-56 flex-shrink-0 hidden md:flex flex-col"
       x-show="sidebarOpen || window.innerWidth >= 768"
       x-cloak>
    <nav class="flex-1 py-4 px-3 space-y-1">
        <?php foreach ($nav as $item): ?>
            <?php
            $isActive = str_starts_with($current, $item['href']) && $item['href'] !== '/dashboard'
                        || $current === $item['href'];
            $classes  = $isActive
                ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold bg-brand-50 text-brand-700 relative overflow-hidden group transition-all'
                : 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all';
            ?>
            <a href="<?= url(ltrim($item['href'], '/')) ?>" class="<?= $classes ?>">
                <?php if($isActive): ?>
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-500 rounded-r-md"></div>
                <?php endif; ?>
                <i class="isax <?= $item['icon'] ?> text-[22px] <?= $isActive ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-600' ?> transition-colors"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer del sidebar -->
    <div class="p-3 border-t border-gray-100 text-xs text-gray-400 text-center">
        Crédinor v1.0
    </div>
</aside>

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen"
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/30 z-10 md:hidden"
     x-cloak></div>
<aside class="fixed inset-y-0 left-0 z-20 w-56 bg-white border-r border-gray-100 flex flex-col pt-14 md:hidden"
       x-show="sidebarOpen"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       x-cloak>
    <nav class="flex-1 py-4 px-3 space-y-1">
        <?php foreach ($nav as $item): ?>
            <a href="<?= url(ltrim($item['href'], '/')) ?>"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                <i class="isax <?= $item['icon'] ?> text-[22px] text-slate-400"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
