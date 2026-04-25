<?php // views/admin/rendiciones.php
use App\Helpers\MoneyHelper;

$badges = [
    'pendiente'   => 'badge-pendiente',
    'confirmada'  => 'badge-activo',
    'rechazada'   => 'badge-rechazado',
];
$labels = [
    'pendiente'  => '⏳ Pendiente',
    'confirmada' => '✅ Confirmada',
    'rechazada'  => '❌ Rechazada',
];
?>
<div class="space-y-4">
    <h1 class="text-2xl font-bold">💼 Rendiciones de caja</h1>

    <!-- Tabs de estado -->
    <div class="flex gap-2 flex-wrap">
        <?php foreach (['', 'pendiente', 'confirmada', 'rechazada'] as $e): ?>
        <a href="<?= url('admin/rendiciones?estado=' . $e) ?>"
           class="<?= $estado === $e ? 'btn-primary' : 'btn-secondary' ?> text-xs">
            <?= $labels[$e] ?? 'Todas' ?>
            <?php if (isset($totales[$e]['cantidad'])): ?>
                <span class="ml-1 opacity-75">(<?= $totales[$e]['cantidad'] ?>)</span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Resumen de totales pendientes -->
    <?php if (isset($totales['pendiente'])): ?>
    <div class="card bg-yellow-50 border border-yellow-200 flex items-center gap-4">
        <span class="text-2xl">⚠️</span>
        <div>
            <p class="font-semibold text-yellow-800">
                <?= $totales['pendiente']['cantidad'] ?> rendición(es) pendiente(s) de confirmar
            </p>
            <p class="text-sm text-yellow-700">
                Total declarado: <strong><?= MoneyHelper::format((float)$totales['pendiente']['monto']) ?></strong>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabla -->
    <?php if (empty($rendiciones)): ?>
    <div class="card text-center py-10 text-gray-400">
        <div class="text-4xl mb-2">📭</div>
        <p>No hay rendiciones en este estado.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Cobrador</th>
                    <th>Sucursal</th>
                    <th>Fecha</th>
                    <th class="text-right">Declarado</th>
                    <th class="text-right">Recibido</th>
                    <th class="text-right">Diferencia</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendiciones as $r): ?>
                <?php
                    $declarado = (float)$r['monto_declarado'];
                    $recibido  = (float)($r['monto_recibido'] ?? 0);
                    $diff      = $recibido > 0 ? $recibido - $declarado : null;
                ?>
                <tr>
                    <td class="font-medium"><?= e($r['cobrador_nombre']) ?></td>
                    <td class="text-gray-500 text-sm"><?= e($r['sucursal_nombre']) ?></td>
                    <td class="text-gray-500 text-sm"><?= date('d/m/Y', strtotime($r['fecha'])) ?></td>
                    <td class="text-right font-medium"><?= MoneyHelper::formatShort($declarado) ?></td>
                    <td class="text-right">
                        <?= $recibido > 0 ? MoneyHelper::formatShort($recibido) : '—' ?>
                    </td>
                    <td class="text-right text-sm">
                        <?php if ($diff !== null): ?>
                            <span class="<?= abs($diff) < 0.01 ? 'text-green-600' : ($diff < 0 ? 'text-red-600' : 'text-blue-600') ?> font-medium">
                                <?= $diff >= 0 ? '+' : '' ?><?= MoneyHelper::formatShort($diff) ?>
                            </span>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?= $badges[$r['estado']] ?? 'badge' ?>">
                            <?= $labels[$r['estado']] ?? $r['estado'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= url('admin/rendiciones/' . $r['id']) ?>"
                           class="<?= $r['estado'] === 'pendiente' ? 'btn-primary' : 'btn-secondary' ?> text-xs">
                            <?= $r['estado'] === 'pendiente' ? 'Confirmar' : 'Ver' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
