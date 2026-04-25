<?php // views/vendedor/creditos.php
use App\Helpers\DateHelper;
use App\Helpers\MoneyHelper;

$badges = [
    'pendiente_autorizacion' => 'badge-pendiente',
    'activo'    => 'badge-activo',
    'finalizado'=> 'badge-finalizado',
    'rechazado' => 'badge-rechazado',
    'cancelado' => 'badge-rechazado',
];
$labels = [
    'pendiente_autorizacion' => 'Pendiente',
    'activo'    => 'Activo',
    'finalizado'=> 'Finalizado',
    'rechazado' => 'Rechazado',
    'cancelado' => 'Cancelado',
];
?>
<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-2xl font-bold">Mis créditos</h1>
        <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary">💸 Nueva solicitud</a>
    </div>

    <!-- Filtro por estado -->
    <div class="flex gap-2 flex-wrap text-sm">
        <?php foreach (['', 'pendiente_autorizacion', 'activo', 'finalizado', 'rechazado'] as $e): ?>
            <a href="<?= url('vendedor/creditos?estado=' . $e) ?>"
               class="<?= $estado === $e ? 'btn-primary' : 'btn-secondary' ?> text-xs">
                <?= $labels[$e] ?? 'Todos' ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($creditos)): ?>
        <div class="card text-center py-10 text-gray-400">
            <div class="text-4xl mb-2">📋</div>
            <p>No hay créditos en este estado.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right">Prestado</th>
                        <th class="text-right">A devolver</th>
                        <th>Cuotas</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($creditos as $c): ?>
                    <tr>
                        <td>
                            <p class="font-medium"><?= e($c['cliente_nombre']) ?></p>
                            <p class="text-xs text-gray-400">DNI <?= e($c['dni']) ?></p>
                        </td>
                        <td class="text-right font-medium">
                            <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?>
                        </td>
                        <td class="text-right font-medium">
                            <?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?>
                        </td>
                        <td><?= $c['cantidad_cuotas'] ?> <?= $c['frecuencia'] ?></td>
                        <td>
                            <span class="<?= $badges[$c['estado']] ?? 'badge' ?>">
                                <?= $labels[$c['estado']] ?? $c['estado'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('creditos/' . $c['id']) ?>"
                               class="btn-secondary text-xs">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
