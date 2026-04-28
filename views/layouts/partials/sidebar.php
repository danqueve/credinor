<?php
use App\Core\Auth;

$rol = Auth::rol();

$navAdmin = [
    ['href' => '/dashboard',          'icon' => 'isax-home-2',         'label' => 'Dashboard'],
    ['href' => '/admin/creditos',     'icon' => 'isax-document-text',  'label' => 'Créditos'],
    ['href' => '/admin/clientes',     'icon' => 'isax-profile-2user',  'label' => 'Clientes'],
    ['href' => '/admin/pagos',        'icon' => 'isax-receipt-item',   'label' => 'Pagos'],
    ['href' => '/admin/rendiciones',  'icon' => 'isax-wallet-money',   'label' => 'Rendiciones'],
    ['href' => '/admin/usuarios',     'icon' => 'isax-user',           'label' => 'Usuarios'],
    ['href' => '/admin/sucursales',   'icon' => 'isax-building',       'label' => 'Sucursales'],
    ['href' => '/admin/reportes',     'icon' => 'isax-chart-21',       'label' => 'Reportes'],
];

$navStaff = [
    ['href' => '/dashboard',            'icon' => 'isax-home-2',        'label' => 'Inicio'],
    ['href' => '/cobrador/agenda',      'icon' => 'isax-calendar-1',    'label' => 'Agenda'],
    ['href' => '/vendedor/clientes',    'icon' => 'isax-profile-2user', 'label' => 'Clientes'],
    ['href' => '/vendedor/creditos',    'icon' => 'isax-document-text', 'label' => 'Créditos'],
    ['href' => '/cobrador/caja',        'icon' => 'isax-card-tick',     'label' => 'Cerrar Caja'],
    ['href' => '/cobrador/rendiciones', 'icon' => 'isax-wallet-money',  'label' => 'Rendiciones'],
    ['href' => '/cobrador/historial',   'icon' => 'isax-clock',         'label' => 'Historial'],
];

$nav = match($rol) {
    'admin'               => $navAdmin,
    'cobrador','vendedor' => $navStaff,
    default               => [],
};

$current  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$userName = Auth::user()['nombre'] ?? 'Usuario';
$userInit = mb_strtoupper(mb_substr($userName, 0, 1));
?>

<!-- ====== SIDEBAR ====== -->
<!-- On desktop (lg+): position static, always visible via CSS.
     On mobile: fixed, hidden by default; Alpine adds .open to show. -->
<aside class="sidebar" :class="{ 'open': sidebarOpen }" role="navigation" aria-label="Menú principal">

    <!-- Logo -->
    <a href="<?= url('dashboard') ?>" class="sidebar-logo">
        <img src="<?= asset('img/logo.png') ?>" alt="Crédinor" class="h-8 w-auto object-contain">
    </a>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php foreach ($nav as $item):
            $isActive = ($item['href'] === '/dashboard')
                ? ($current === '/dashboard' || $current === '/')
                : str_starts_with($current, $item['href']);
            $cls = 'sidebar-item' . ($isActive ? ' active' : '');
        ?>
            <a href="<?= url(ltrim($item['href'], '/')) ?>"
               class="<?= $cls ?>"
               <?= $isActive ? 'aria-current="page"' : '' ?>>
                <i class="isax <?= $item['icon'] ?> sidebar-item-icon"></i>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer: user identity + logout -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $userInit ?></div>
            <div class="sidebar-user-name">
                <strong><?= e($userName) ?></strong>
                <span><?= e($rol ?? '') ?></span>
            </div>
            <a href="<?= url('logout') ?>"
               class="ml-auto w-7 h-7 flex items-center justify-center rounded-lg transition-colors"
               style="color: var(--sidebar-muted);"
               onmouseover="this.style.color='#f87171'"
               onmouseout="this.style.color='var(--sidebar-muted)'"
               aria-label="Cerrar sesión"
               title="Cerrar sesión">
                <i class="isax isax-logout" style="font-size:1rem;" aria-hidden="true"></i>
            </a>
        </div>
    </div>

</aside>
