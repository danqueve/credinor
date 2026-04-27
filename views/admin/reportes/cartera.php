<?php // views/admin/reportes/cartera.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Reporte de Cartera Activa</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Análisis del estado actual de todos los créditos en curso</p>
        </div>
        <a href="?<?= http_build_query(array_merge($filtros, ['export' => 'csv'])) ?>"
           class="btn-secondary bg-white hover:bg-slate-50 hover:text-slate-800 shadow-sm border-slate-200">
            <i class="isax isax-document-download"></i> Exportar CSV
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
            <i class="isax isax-filter text-8xl text-slate-900"></i>
        </div>
        
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 relative z-10">
            <div>
                <label class="form-label block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Sucursal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="isax isax-shop text-slate-400"></i>
                    </div>
                    <select name="sucursal_id" class="form-select pl-9 text-sm bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                        <option value="">Todas las sucursales</option>
                        <?php foreach ($sucursales as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (int)$filtros['sucursal_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                                <?= e($s['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="form-label block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Cobrador</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="isax isax-profile-2user text-slate-400"></i>
                    </div>
                    <select name="cobrador_id" class="form-select pl-9 text-sm bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                        <option value="">Todos los cobradores</option>
                        <?php foreach ($cobradores as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (int)$filtros['cobrador_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                                <?= e($c['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="form-label block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Desde</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="isax isax-calendar-1 text-slate-400"></i>
                    </div>
                    <input type="date" name="desde" value="<?= e($filtros['desde']) ?>" class="form-input pl-9 text-sm bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                </div>
            </div>
            
            <div>
                <label class="form-label block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Hasta</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="isax isax-calendar-tick text-slate-400"></i>
                    </div>
                    <input type="date" name="hasta" value="<?= e($filtros['hasta']) ?>" class="form-input pl-9 text-sm bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                </div>
            </div>
            
            <div class="col-span-1 sm:col-span-2 md:col-span-4 flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 sm:flex-none justify-center">
                    <i class="isax isax-search-normal-1"></i> Filtrar Resultados
                </button>
                <a href="<?= url('admin/reportes/cartera') ?>" class="btn-secondary flex-1 sm:flex-none justify-center bg-white border-slate-200 hover:bg-slate-50">
                    <i class="isax isax-refresh-2"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- KPIs resumen -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-slate-300 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-document-text text-6xl text-slate-900"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Créditos</span>
            <span class="block text-2xl font-bold text-slate-800" style="font-family: 'Outfit', sans-serif;">
                <?= $resumen['total_creditos'] ?>
            </span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-emerald-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-money-send text-6xl text-emerald-600"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Prestado</span>
            <span class="block text-2xl font-bold text-emerald-600" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($resumen['total_prestado']) ?>
            </span>
        </div>
        
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-5 border border-slate-700 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="isax isax-wallet-add text-6xl text-white"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">A Devolver</span>
            <span class="block text-2xl font-bold text-white" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($resumen['total_devolver']) ?>
            </span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-amber-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-timer-1 text-6xl text-amber-600"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Saldo Pendiente</span>
            <span class="block text-2xl font-bold text-amber-600" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($resumen['saldo_capital']) ?>
            </span>
        </div>
        
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-red-200 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-danger text-6xl text-red-600"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Mora Total</span>
            <span class="block text-2xl font-bold text-red-500" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($resumen['mora_acumulada']) ?>
            </span>
        </div>
        
        <div class="bg-red-50/50 rounded-3xl p-5 border border-red-100 shadow-sm relative overflow-hidden group hover:border-red-300 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-warning-2 text-6xl text-red-700"></i>
            </div>
            <span class="block text-xs font-bold uppercase tracking-wider text-red-500 mb-1">Mora Pendiente</span>
            <span class="block text-2xl font-bold text-red-700" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($resumen['mora_pendiente']) ?>
            </span>
        </div>
    </div>

    <!-- Tabla -->
    <?php if (empty($creditos)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-search-status text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay resultados</h3>
            <p class="text-slate-500 font-medium">No se encontraron créditos que coincidan con los filtros seleccionados.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Responsables</th>
                            <th class="px-6 py-4 text-right">Prestado</th>
                            <th class="px-6 py-4 text-right hidden md:table-cell">Saldo</th>
                            <th class="px-6 py-4 text-right hidden lg:table-cell">Mora Pend.</th>
                            <th class="px-6 py-4 text-center">Progreso</th>
                            <th class="px-6 py-4 text-center hidden xl:table-cell">Inicio</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($creditos as $c): ?>
                        <?php $moraPend = (float)$c['mora_acumulada'] - (float)$c['mora_pagada']; ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-sm shrink-0">
                                        <?= strtoupper(substr($c['cliente_nombre'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 block"><?= e($c['cliente_nombre']) ?></span>
                                        <span class="text-xs font-medium text-slate-500">DNI: <?= e($c['dni']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold">
                                        <i class="isax isax-profile-2user"></i> <?= e($c['cobrador_nombre'] ?? '—') ?>
                                    </span>
                                    <br>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md border border-slate-100 text-slate-500 text-[10px] font-bold">
                                        <i class="isax isax-shop"></i> <?= e($c['sucursal_nombre']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">
                                <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?>
                            </td>
                            <td class="px-6 py-4 text-right hidden md:table-cell font-bold text-amber-600">
                                <?= MoneyHelper::formatShort((float)$c['saldo_capital']) ?>
                            </td>
                            <td class="px-6 py-4 text-right hidden lg:table-cell font-bold <?= $moraPend > 0 ? 'text-red-600' : 'text-slate-300' ?>">
                                <?= MoneyHelper::formatShort($moraPend) ?>
                            </td>
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                        <?= $c['cuotas_pagadas'] ?> / <?= $c['cantidad_cuotas'] ?>
                                    </span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1.5 shadow-inner overflow-hidden">
                                        <div class="bg-gradient-to-r from-brand-400 to-brand-600 h-1.5 rounded-full transition-all duration-500"
                                             style="width:<?= $c['cantidad_cuotas'] > 0 ? round($c['cuotas_pagadas'] / $c['cantidad_cuotas'] * 100) : 0 ?>%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center hidden xl:table-cell text-slate-500 font-medium">
                                <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('creditos/' . $c['id']) ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                    <i class="isax isax-arrow-right-3"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Mostrando <span class="font-bold text-slate-800"><?= count($creditos) ?></span> crédito(s)</span>
            </div>
        </div>
    <?php endif; ?>
</div>
