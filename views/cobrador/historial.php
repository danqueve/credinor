<?php // views/cobrador/historial.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-4 pb-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Historial de cobros</h1>
        <p class="text-sm text-gray-400 mt-0.5">Últimos 60 días</p>
    </div>

    <?php if (empty($pagos)): ?>
        <div class="card text-center py-12">
            <div class="text-4xl mb-2">📭</div>
            <p class="text-gray-500 font-medium">No hay cobros registrados en los últimos 60 días.</p>
        </div>
    <?php else: ?>

        <!-- Resumen -->
        <?php
        $totalMonto = array_sum(array_column($pagos, 'monto'));
        $countHoy   = count(array_filter($pagos, fn($p) => date('Y-m-d', strtotime($p['created_at'])) === date('Y-m-d')));
        ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="kpi-card">
                <span class="kpi-value text-brand-700"><?= MoneyHelper::formatShort($totalMonto) ?></span>
                <span class="kpi-label">Total cobrado</span>
            </div>
            <div class="kpi-card">
                <span class="kpi-value text-green-700"><?= count($pagos) ?></span>
                <span class="kpi-label">Pagos registrados</span>
            </div>
        </div>

        <!-- Listado -->
        <div class="card p-0 overflow-hidden">
            <?php
            $fechaActual = null;
            foreach ($pagos as $pago):
                $fecha = date('Y-m-d', strtotime($pago['created_at']));
                if ($fecha !== $fechaActual):
                    $fechaActual = $fecha;
            ?>
            <div class="bg-gray-50 px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <?= date('d \d\e F \d\e Y', strtotime($fecha)) ?>
                <?php if ($fecha === date('Y-m-d')): ?>
                    <span class="ml-2 badge-activo">Hoy</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                <!-- Ícono estado -->
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                    <?= $pago['estado'] === 'confirmado' ? 'bg-green-100 text-green-700' :
                       ($pago['estado'] === 'anulado' ? 'bg-red-100 text-red-500' : 'bg-yellow-100 text-yellow-700') ?>">
                    <?= $pago['estado'] === 'confirmado' ? '✅' : ($pago['estado'] === 'anulado' ? '❌' : '⏳') ?>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-800 truncate"><?= e($pago['cliente_nombre']) ?></p>
                    <p class="text-xs text-gray-400">
                        Cuota <?= $pago['numero_cuota'] ?>
                        · <?= date('H:i', strtotime($pago['created_at'])) ?>
                    </p>
                </div>

                <div class="text-right shrink-0">
                    <p class="font-bold <?= $pago['estado'] === 'anulado' ? 'line-through text-gray-400' : 'text-gray-800' ?>">
                        <?= MoneyHelper::formatShort((float)$pago['monto']) ?>
                    </p>
                    <?php if ((float)$pago['monto_a_mora'] > 0): ?>
                    <p class="text-xs text-red-500">
                        mora: <?= MoneyHelper::formatShort((float)$pago['monto_a_mora']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <a href="<?= url('cobrador/pago/' . $pago['id'] . '/recibo') ?>"
                   class="text-gray-300 hover:text-brand-600 transition-colors ml-1"
                   title="Ver recibo">
                    🧾
                </a>
            </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
