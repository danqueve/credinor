<?php // views/shared/credito_detalle.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
use App\Core\Auth;

$badges = [
    'pendiente_autorizacion' => 'badge-pendiente',
    'activo'    => 'badge-activo',
    'finalizado'=> 'badge-finalizado',
    'rechazado' => 'badge-rechazado',
    'cancelado' => 'badge-rechazado',
];
$estadosCuota = [
    'pendiente' => 'badge-pendiente',
    'parcial'   => 'badge-parcial',
    'pagada'    => 'badge-pagada',
    'vencida'   => 'badge-vencida',
];
$backUrl = Auth::isAdmin()
    ? url('admin/creditos')
    : (Auth::isVendedor() ? url('vendedor/creditos') : url('dashboard'));
?>
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <a href="<?= $backUrl ?>" class="text-gray-400 hover:text-gray-600">←</a>
            <h1 class="text-xl font-bold">Crédito #<?= $credito['id'] ?></h1>
            <span class="<?= $badges[$credito['estado']] ?? 'badge' ?>">
                <?= ucfirst(str_replace('_', ' ', $credito['estado'])) ?>
            </span>
        </div>
        <?php if ($credito['estado'] === 'pendiente_autorizacion' && Auth::isAdmin()): ?>
            <a href="<?= url('admin/creditos/' . $credito['id'] . '/autorizar') ?>"
               class="btn-primary">✅ Autorizar</a>
        <?php endif; ?>
    </div>

    <!-- Info principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card space-y-2 text-sm">
            <h2 class="font-semibold border-b pb-2">👤 Cliente</h2>
            <p><span class="text-gray-500">Nombre:</span> <strong><?= e($credito['cliente_nombre']) ?></strong></p>
            <p><span class="text-gray-500">DNI:</span> <?= e($credito['dni']) ?></p>
            <p><span class="text-gray-500">Teléfono:</span> <?= e($credito['telefono'] ?? '—') ?></p>
            <p><span class="text-gray-500">Domicilio:</span> <?= e($credito['domicilio'] ?? '—') ?></p>
        </div>
        <div class="card space-y-2 text-sm">
            <h2 class="font-semibold border-b pb-2">💰 Condiciones</h2>
            <p><span class="text-gray-500">Prestado:</span>
                <strong class="text-green-700"><?= MoneyHelper::format((float)$credito['monto_prestado']) ?></strong></p>
            <p><span class="text-gray-500">A devolver:</span>
                <strong class="text-brand-700"><?= MoneyHelper::format((float)$credito['monto_a_devolver']) ?></strong></p>
            <p><span class="text-gray-500">Cuotas:</span>
                <?= $credito['cantidad_cuotas'] ?> × <?= $credito['frecuencia'] ?></p>
            <p><span class="text-gray-500">Cobrador:</span> <?= e($credito['cobrador_nombre'] ?? '—') ?></p>
            <?php if ((float)$credito['mora_acumulada'] > 0): ?>
            <p><span class="text-gray-500">Mora acumulada:</span>
                <span class="text-red-600 font-medium"><?= MoneyHelper::format((float)$credito['mora_acumulada']) ?></span>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de cuotas -->
    <?php if (!empty($cuotas)): ?>
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">📅 Plan de cuotas</h2>
        <div class="table-container">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Vencimiento</th>
                        <th class="text-right">Monto</th>
                        <th class="text-right">Pagado</th>
                        <th class="text-right">Saldo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuotas as $cu): ?>
                    <?php
                        $pagado = (float)($cu['monto_pagado'] ?? 0);
                        $saldo  = (float)$cu['monto'] - $pagado;
                    ?>
                    <tr>
                        <td><?= $cu['numero_cuota'] ?></td>
                        <td><?= DateHelper::formatoArg($cu['fecha_vencimiento']) ?></td>
                        <td class="text-right"><?= MoneyHelper::formatShort((float)$cu['monto']) ?></td>
                        <td class="text-right text-green-600"><?= MoneyHelper::formatShort($pagado) ?></td>
                        <td class="text-right <?= $saldo > 0 ? 'text-red-600 font-medium' : '' ?>">
                            <?= MoneyHelper::formatShort($saldo) ?>
                        </td>
                        <td><span class="<?= $estadosCuota[$cu['estado']] ?? 'badge' ?>">
                            <?= ucfirst($cu['estado']) ?>
                        </span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Log de auditoría -->
    <?php if (!empty($log)): ?>
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">📋 Historial de cambios</h2>
        <ol class="space-y-2">
            <?php foreach ($log as $entry): ?>
            <li class="flex gap-3 text-sm">
                <span class="text-gray-400 text-xs whitespace-nowrap">
                    <?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?>
                </span>
                <span>
                    <span class="font-medium"><?= e($entry['usuario_nombre']) ?></span>:
                    <?= e($entry['nota']) ?>
                    <?php if ($entry['estado_desde'] && $entry['estado_hasta']): ?>
                        <span class="text-gray-400">
                            (<?= $entry['estado_desde'] ?> → <?= $entry['estado_hasta'] ?>)
                        </span>
                    <?php endif; ?>
                </span>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php endif; ?>
</div>
