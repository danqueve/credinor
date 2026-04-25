<?php // views/vendedor/dashboard.php ?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Bienvenido, <?= e(\App\Core\Auth::user()['nombre']) ?> 👋
        </h1>
        <p class="text-sm text-gray-500 mt-1">Panel del vendedor</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="kpi-card">
            <span class="kpi-value text-yellow-600"><?= $mis_pendientes ?></span>
            <span class="kpi-label">Mis créditos pendientes</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-green-600"><?= $mis_activos ?></span>
            <span class="kpi-label">Mis créditos activos</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="<?= url('vendedor/clientes/nuevo') ?>"
           class="card hover:shadow-md transition-shadow flex items-center gap-3 text-brand-700 font-medium">
            <span class="text-2xl">➕</span>
            <span>Nuevo cliente</span>
        </a>
        <a href="<?= url('vendedor/creditos/nuevo') ?>"
           class="card hover:shadow-md transition-shadow flex items-center gap-3 text-green-700 font-medium">
            <span class="text-2xl">💸</span>
            <span>Nueva solicitud de crédito</span>
        </a>
    </div>
</div>
