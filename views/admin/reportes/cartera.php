<?php // views/admin/reportes/cartera.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-5">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-2xl font-bold">📊 Reporte de Cartera Activa</h1>
        <a href="?<?= http_build_query(array_merge($filtros, ['export' => 'csv'])) ?>"
           class="btn-secondary text-sm">
            ⬇️ Exportar CSV
        </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="card grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <label class="form-label text-xs">Sucursal</label>
            <select name="sucursal_id" class="form-select text-sm">
                <option value="">Todas</option>
                <?php foreach ($sucursales as $s): ?>
                    <option value="<?= $s['id'] ?>"
                            <?= (int)$filtros['sucursal_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                        <?= e($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Cobrador</label>
            <select name="cobrador_id" class="form-select text-sm">
                <option value="">Todos</option>
                <?php foreach ($cobradores as $c): ?>
                    <option value="<?= $c['id'] ?>"
                            <?= (int)$filtros['cobrador_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                        <?= e($c['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Desde</label>
            <input type="date" name="desde" value="<?= e($filtros['desde']) ?>" class="form-input text-sm">
        </div>
        <div>
            <label class="form-label text-xs">Hasta</label>
            <input type="date" name="hasta" value="<?= e($filtros['hasta']) ?>" class="form-input text-sm">
        </div>
        <div class="col-span-2 sm:col-span-4 flex gap-2">
            <button type="submit" class="btn-primary text-sm">Filtrar</button>
            <a href="<?= url('admin/reportes/cartera') ?>" class="btn-secondary text-sm">Limpiar</a>
        </div>
    </form>

    <!-- KPIs resumen -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
        <div class="kpi-card">
            <span class="kpi-value text-lg"><?= $resumen['total_creditos'] ?></span>
            <span class="kpi-label">Créditos</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-lg text-green-700"><?= MoneyHelper::formatShort($resumen['total_prestado']) ?></span>
            <span class="kpi-label">Prestado</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-lg text-brand-700"><?= MoneyHelper::formatShort($resumen['total_devolver']) ?></span>
            <span class="kpi-label">A devolver</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-lg text-orange-600"><?= MoneyHelper::formatShort($resumen['saldo_capital']) ?></span>
            <span class="kpi-label">Saldo pendiente</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-lg text-red-600"><?= MoneyHelper::formatShort($resumen['mora_acumulada']) ?></span>
            <span class="kpi-label">Mora total</span>
        </div>
        <div class="kpi-card">
            <span class="kpi-value text-lg text-red-800"><?= MoneyHelper::formatShort($resumen['mora_pendiente']) ?></span>
            <span class="kpi-label">Mora pendiente</span>
        </div>
    </div>

    <!-- Tabla -->
    <?php if (empty($creditos)): ?>
    <div class="card text-center py-10 text-gray-400">
        <p>No hay créditos para los filtros seleccionados.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table text-sm">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Cobrador</th>
                    <th>Sucursal</th>
                    <th class="text-right">Prestado</th>
                    <th class="text-right">Saldo</th>
                    <th class="text-right">Mora pend.</th>
                    <th>Cuotas</th>
                    <th>Inicio</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($creditos as $c): ?>
                <?php $moraPend = (float)$c['mora_acumulada'] - (float)$c['mora_pagada']; ?>
                <tr>
                    <td>
                        <p class="font-medium"><?= e($c['cliente_nombre']) ?></p>
                        <p class="text-xs text-gray-400">DNI <?= e($c['dni']) ?></p>
                    </td>
                    <td class="text-gray-600"><?= e($c['cobrador_nombre'] ?? '—') ?></td>
                    <td class="text-gray-400 text-xs"><?= e($c['sucursal_nombre']) ?></td>
                    <td class="text-right"><?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?></td>
                    <td class="text-right font-medium text-orange-600">
                        <?= MoneyHelper::formatShort((float)$c['saldo_capital']) ?>
                    </td>
                    <td class="text-right <?= $moraPend > 0 ? 'text-red-600 font-medium' : 'text-gray-300' ?>">
                        <?= MoneyHelper::formatShort($moraPend) ?>
                    </td>
                    <td class="text-center align-middle">
                        <div class="flex flex-col items-center justify-center gap-1.5">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <?= $c['cuotas_pagadas'] ?> / <?= $c['cantidad_cuotas'] ?>
                            </span>
                            <div class="w-20 bg-slate-100 rounded-full h-1.5 shadow-inner overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-400 to-brand-600 h-1.5 rounded-full transition-all duration-500"
                                     style="width:<?= $c['cantidad_cuotas'] > 0 ? round($c['cuotas_pagadas'] / $c['cantidad_cuotas'] * 100) : 0 ?>%">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-400 text-xs"><?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></td>
                    <td>
                        <a href="<?= url('creditos/' . $c['id']) ?>" class="btn-secondary text-xs">Ver</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
