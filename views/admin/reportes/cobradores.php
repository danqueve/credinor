<?php // views/admin/reportes/cobradores.php
use App\Helpers\MoneyHelper;

$periodos = ['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes'];
$totalCobrado = array_sum(array_column($cobradores, 'total_cobrado'));
?>
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-2xl font-bold">👥 Reporte por Cobrador</h1>
        <!-- Selector período -->
        <div class="flex gap-1">
            <?php foreach ($periodos as $key => $label): ?>
            <a href="?periodo=<?= $key ?>"
               class="<?= $periodo === $key ? 'btn-primary' : 'btn-secondary' ?> text-sm">
                <?= $label ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <p class="text-sm text-gray-500">
        Período: desde <strong><?= date('d/m/Y', strtotime($desde)) ?></strong>
        hasta hoy — Total cobrado: <strong class="text-brand-700"><?= MoneyHelper::format($totalCobrado) ?></strong>
    </p>

    <!-- Ranking -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($cobradores as $i => $c): ?>
        <?php $pct = $totalCobrado > 0 ? round((float)$c['total_cobrado'] / $totalCobrado * 100, 1) : 0; ?>
        <div class="card hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center
                            font-bold text-brand-700">
                    <?= mb_strtoupper(mb_substr($c['cobrador_nombre'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold truncate"><?= e($c['cobrador_nombre']) ?></p>
                    <?php if ($i === 0 && $totalCobrado > 0): ?>
                        <span class="text-xs text-yellow-600">🏆 Mejor cobrador</span>
                    <?php endif; ?>
                </div>
                <span class="text-2xl font-bold text-brand-700">
                    <?= $pct ?>%
                </span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                <div class="bg-brand-500 h-2 rounded-full transition-all"
                     style="width:<?= $pct ?>%"></div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-sm">
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-400">Cobrado</p>
                    <p class="font-bold text-brand-700"><?= MoneyHelper::formatShort((float)$c['total_cobrado']) ?></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-400">Pagos</p>
                    <p class="font-bold"><?= number_format((int)$c['total_pagos']) ?></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-400">Rendido</p>
                    <p class="font-medium text-sm"><?= MoneyHelper::formatShort((float)$c['total_rendido']) ?></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                    <p class="text-xs text-gray-400">Rendiciones</p>
                    <p class="font-medium"><?= (int)$c['rendiciones'] ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Histórico 30 días -->
    <?php if (!empty($historicoDias)): ?>
    <div class="card">
        <h2 class="font-semibold border-b pb-2 mb-4">Cobros diarios — últimos 30 días</h2>
        <div class="overflow-x-auto">
            <div class="flex items-end gap-1 h-32 min-w-max">
                <?php
                $maxDia = max(array_column($historicoDias, 'total')) ?: 1;
                foreach ($historicoDias as $dia):
                    $pctH = round((float)$dia['total'] / $maxDia * 100);
                    $esHoy = $dia['fecha'] === date('Y-m-d');
                ?>
                <div class="flex flex-col items-center gap-1 group">
                    <div class="text-xs text-gray-400 opacity-0 group-hover:opacity-100 whitespace-nowrap">
                        <?= MoneyHelper::formatShort((float)$dia['total']) ?>
                    </div>
                    <div class="w-5 <?= $esHoy ? 'bg-brand-600' : 'bg-brand-300' ?> hover:bg-brand-500
                                    rounded-t transition-all cursor-pointer"
                         style="height:<?= max(4, $pctH) ?>%"
                         title="<?= date('d/m', strtotime($dia['fecha'])) ?>: <?= MoneyHelper::formatShort((float)$dia['total']) ?>">
                    </div>
                    <div class="text-xs text-gray-400 w-5 text-center">
                        <?= date('d', strtotime($dia['fecha'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
