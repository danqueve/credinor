<?php // views/admin/reportes/mora.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-5">
    <h1 class="text-2xl font-bold">🔴 Reporte de Mora</h1>

    <!-- Totales -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="kpi-card">
            <span class="kpi-value text-red-600"><?= MoneyHelper::formatShort($totalMora) ?></span>
            <span class="kpi-label">Mora acumulada total</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-red-800"><?= MoneyHelper::formatShort($totalPend) ?></span>
            <span class="kpi-label">Mora pendiente de cobro</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-orange-600"><?= MoneyHelper::formatShort($totalMora - $totalPend) ?></span>
            <span class="kpi-label">Mora ya cobrada</span>
        </div>
    </div>

    <!-- Por cobrador -->
    <?php if (!empty($porCobrador)): ?>
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">Por cobrador</h2>
        <div class="table-container">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>Cobrador</th>
                        <th class="text-right">Créditos</th>
                        <th class="text-right">Mora acumulada</th>
                        <th class="text-right">Cobrada</th>
                        <th class="text-right">Pendiente</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($porCobrador as $c): ?>
                    <?php
                        $pct = $c['mora_total'] > 0
                            ? round($c['mora_cobrada'] / $c['mora_total'] * 100)
                            : 0;
                    ?>
                    <tr>
                        <td class="font-medium"><?= e($c['cobrador_nombre']) ?></td>
                        <td class="text-right text-gray-500"><?= $c['total_creditos'] ?></td>
                        <td class="text-right"><?= MoneyHelper::formatShort((float)$c['mora_total']) ?></td>
                        <td class="text-right text-green-600"><?= MoneyHelper::formatShort((float)$c['mora_cobrada']) ?></td>
                        <td class="text-right text-red-600 font-medium"><?= MoneyHelper::formatShort((float)$c['mora_pendiente']) ?></td>
                        <td class="w-24">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width:<?= $pct ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-500"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Top deudores -->
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-3">Top 50 — Mayor mora pendiente</h2>
        <div class="table-container">
            <table class="table text-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Cobrador</th>
                        <th class="text-right">Mora acum.</th>
                        <th class="text-right">Mora pend.</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topDeudores as $i => $d): ?>
                    <tr>
                        <td class="text-gray-400"><?= $i + 1 ?></td>
                        <td>
                            <p class="font-medium"><?= e($d['nombre']) ?></p>
                            <p class="text-xs text-gray-400">DNI <?= e($d['dni']) ?></p>
                        </td>
                        <td class="text-gray-500 text-xs"><?= e($d['cobrador_nombre'] ?? '—') ?></td>
                        <td class="text-right text-red-500"><?= MoneyHelper::formatShort((float)$d['mora_acumulada']) ?></td>
                        <td class="text-right font-bold text-red-700"><?= MoneyHelper::formatShort((float)$d['mora_pendiente']) ?></td>
                        <td>
                            <a href="<?= url('creditos/' . $d['credito_id']) ?>" class="btn-secondary text-xs">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
