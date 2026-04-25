<?php // views/shared/cliente_detalle.php
use App\Helpers\MoneyHelper;
use App\Core\Auth;
?>
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <a href="<?= url('vendedor/clientes') ?>" class="text-gray-400 hover:text-gray-600">←</a>
            <h1 class="text-xl font-bold"><?= e($cliente['nombre']) ?></h1>
        </div>
        <div class="flex gap-2">
            <a href="<?= url('vendedor/clientes/' . $cliente['id'] . '/editar') ?>"
               class="btn-secondary text-sm">✏️ Editar</a>
            <a href="<?= url('vendedor/creditos/nuevo?cliente_id=' . $cliente['id']) ?>"
               class="btn-primary text-sm">💸 Nuevo crédito</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card space-y-2 text-sm">
            <h2 class="font-semibold border-b pb-2">Datos personales</h2>
            <p><span class="text-gray-500">DNI:</span> <?= e($cliente['dni']) ?></p>
            <p><span class="text-gray-500">Teléfono:</span>
                <?php if ($cliente['telefono']): ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $cliente['telefono']) ?>"
                       target="_blank" class="text-green-600 hover:text-green-700">
                        📱 <?= e($cliente['telefono']) ?>
                    </a>
                <?php else: ?>—<?php endif; ?>
            </p>
            <p><span class="text-gray-500">Email:</span> <?= e($cliente['email'] ?? '—') ?></p>
            <p><span class="text-gray-500">Domicilio:</span> <?= e($cliente['domicilio'] ?? '—') ?></p>
            <p><span class="text-gray-500">Localidad:</span> <?= e($cliente['localidad'] ?? '—') ?></p>
            <?php if ($cliente['observaciones']): ?>
            <p><span class="text-gray-500">Obs:</span> <?= e($cliente['observaciones']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Mini mapa -->
        <?php if ($cliente['lat'] && $cliente['lng']): ?>
        <div class="card p-0 overflow-hidden">
            <div id="mapa-cliente" class="w-full h-48"></div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script>
            const map = L.map('mapa-cliente', {zoomControl: false, dragging: false})
                .setView([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            L.marker([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>]).addTo(map);
            </script>
        </div>
        <?php endif; ?>
    </div>

    <!-- Créditos -->
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">📋 Créditos</h2>
        <?php if (empty($creditos)): ?>
            <p class="text-sm text-gray-400">Sin créditos registrados.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($creditos as $c): ?>
                <div class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50">
                    <div>
                        <span class="font-medium"><?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?></span>
                        <span class="text-gray-400 text-xs ml-2">→ <?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php
                        $b = [
                            'pendiente_autorizacion' => 'badge-pendiente',
                            'activo' => 'badge-activo',
                            'finalizado' => 'badge-finalizado',
                            'rechazado' => 'badge-rechazado',
                        ];
                        ?>
                        <span class="<?= $b[$c['estado']] ?? 'badge' ?>">
                            <?= ucfirst(str_replace('_', ' ', $c['estado'])) ?>
                        </span>
                        <a href="<?= url('creditos/' . $c['id']) ?>" class="btn-secondary text-xs">Ver</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Link Google Maps -->
    <?php if ($cliente['lat'] && $cliente['lng']): ?>
    <div class="text-center">
        <a href="https://maps.google.com/?q=<?= $cliente['lat'] ?>,<?= $cliente['lng'] ?>"
           target="_blank" class="btn-secondary text-sm">
            🗺️ Abrir en Google Maps
        </a>
    </div>
    <?php endif; ?>
</div>
