<?php // views/cobrador/dashboard.php
$hoy = date('d/m/Y');
?>
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Mi agenda</h1>
            <p class="text-sm text-gray-500"><?= $hoy ?></p>
        </div>
        <div class="text-right">
            <div class="text-lg font-bold text-green-600">
                $<?= number_format($total_cobrado, 0, ',', '.') ?>
            </div>
            <div class="text-xs text-gray-400">cobrado hoy</div>
        </div>
    </div>

    <!-- Cobros del día -->
    <div>
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">
            📅 Hoy (<?= count($agenda_hoy) ?>)
        </h2>

        <?php if (empty($agenda_hoy)): ?>
            <div class="card text-center py-8 text-gray-400">
                <div class="text-3xl mb-2">✅</div>
                <p class="text-sm">No hay cobros pendientes para hoy</p>
            </div>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($agenda_hoy as $c): ?>
                <div class="card flex items-center justify-between gap-3 hover:shadow-md transition-shadow">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            <?= e($c['cliente_nombre']) ?>
                        </p>
                        <p class="text-sm text-gray-500 truncate"><?= e($c['domicilio'] ?? '') ?></p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Cuota #<?= $c['numero_cuota'] ?> •
                            Saldo: <span class="font-medium text-gray-700">
                                $<?= number_format($c['saldo'], 0, ',', '.') ?>
                            </span>
                        </p>
                    </div>
                    <div class="flex flex-col gap-1.5 items-end flex-shrink-0">
                        <a href="<?= url('cobrador/pago/' . $c['credito_id'] . '/' . $c['id']) ?>"
                           class="btn-primary text-xs px-3 py-1.5">Cobrar</a>
                        <?php if ($c['telefono']): ?>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['telefono']) ?>"
                           target="_blank"
                           class="text-xs text-green-600 hover:text-green-700">📱 WA</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Vencidas -->
    <?php if (!empty($agenda_vencida)): ?>
    <div>
        <h2 class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-2">
            🔴 Vencidas (<?= count($agenda_vencida) ?>)
        </h2>
        <div class="space-y-2">
            <?php foreach ($agenda_vencida as $c): ?>
            <div class="card border-red-100 flex items-center justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">
                        <?= e($c['cliente_nombre']) ?>
                    </p>
                    <p class="text-xs text-red-600 mt-0.5">
                        Vencía <?= date('d/m/Y', strtotime($c['fecha_vencimiento'])) ?> •
                        Saldo: $<?= number_format($c['saldo'], 0, ',', '.') ?>
                    </p>
                </div>
                <a href="<?= url('cobrador/pago/' . $c['credito_id'] . '/' . $c['id']) ?>"
                   class="btn-danger text-xs px-3 py-1.5">Cobrar</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Botón cierre de caja -->
    <div class="pt-4 border-t border-gray-100">
        <a href="<?= url('cobrador/caja') ?>"
           class="btn-secondary w-full justify-center py-3 text-sm font-medium">
            💼 Cerrar caja del día
        </a>
    </div>
</div>
