<?php // views/admin/rendiciones.php
use App\Helpers\MoneyHelper;

$badges = [
    'pendiente'   => 'badge-pendiente',
    'confirmada'  => 'badge-activo',
    'rechazada'   => 'badge-rechazado',
];
$labels = [
    'pendiente'  => 'Pendiente',
    'confirmada' => 'Confirmada',
    'rechazada'  => 'Rechazada',
];
$icons = [
    'pendiente'  => 'isax-clock',
    'confirmada' => 'isax-tick-circle',
    'rechazada'  => 'isax-close-circle',
];
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Rendiciones de Caja</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Supervisión de ingresos diarios de los cobradores</p>
        </div>
    </div>

    <!-- Tabs de estado -->
    <div class="bg-white rounded-2xl p-2 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] inline-flex flex-wrap gap-1">
        <?php foreach (['', 'pendiente', 'confirmada', 'rechazada'] as $e): ?>
            <?php $isActive = $estado === $e; ?>
            <a href="<?= url('admin/rendiciones' . ($e ? '?estado=' . $e : '')) ?>"
               class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2
                      <?= $isActive 
                          ? 'bg-slate-900 text-white shadow-md' 
                          : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' ?>">
                <?php if ($e): ?>
                    <i class="isax <?= $icons[$e] ?>"></i>
                <?php else: ?>
                    <i class="isax isax-category"></i>
                <?php endif; ?>
                <?= $e ? $labels[$e] : 'Todas' ?>
                
                <?php if (isset($totales[$e]['cantidad'])): ?>
                    <span class="ml-1 px-1.5 py-0.5 rounded-md <?= $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600' ?> text-[10px]">
                        <?= $totales[$e]['cantidad'] ?>
                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Resumen de totales pendientes -->
    <?php if (isset($totales['pendiente']) && $totales['pendiente']['cantidad'] > 0): ?>
    <div class="bg-amber-50/80 rounded-3xl p-6 border border-amber-200/50 shadow-sm flex items-start gap-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
            <i class="isax isax-wallet-money text-8xl text-amber-600"></i>
        </div>
        
        <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <i class="isax isax-warning-2 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-amber-900 mb-1" style="font-family: 'Outfit', sans-serif;">
                Atención requerida
            </h3>
            <p class="text-amber-800 font-medium">
                Existen <strong class="font-extrabold"><?= $totales['pendiente']['cantidad'] ?></strong> rendiciones pendientes de confirmar.
            </p>
            <p class="text-sm font-bold text-amber-700/80 mt-1">
                Total declarado en espera: <span class="text-amber-600 ml-1"><?= MoneyHelper::format((float)$totales['pendiente']['monto']) ?></span>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabla -->
    <?php if (empty($rendiciones)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-wallet-add text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay rendiciones</h3>
            <p class="text-slate-500 font-medium">No se encontraron rendiciones de caja en este estado.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cobrador</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Sucursal</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4 text-right">Declarado</th>
                            <th class="px-6 py-4 text-right hidden md:table-cell">Recibido</th>
                            <th class="px-6 py-4 text-right hidden lg:table-cell">Diferencia</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($rendiciones as $r): ?>
                        <?php
                            $declarado = (float)$r['monto_declarado'];
                            $recibido  = (float)($r['monto_recibido'] ?? 0);
                            $diff      = $recibido > 0 ? $recibido - $declarado : null;
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-2 font-bold text-slate-800">
                                    <div class="w-8 h-8 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center text-xs shrink-0">
                                        <?= strtoupper(substr($r['cobrador_nombre'], 0, 1)) ?>
                                    </div>
                                    <?= e($r['cobrador_nombre']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-shop"></i> <?= e($r['sucursal_nombre']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium flex items-center gap-1.5 mt-1">
                                <i class="isax isax-calendar-1 text-slate-400"></i>
                                <?= date('d/m/Y', strtotime($r['fecha'])) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">
                                <?= MoneyHelper::formatShort($declarado) ?>
                            </td>
                            <td class="px-6 py-4 text-right hidden md:table-cell font-bold <?= $recibido > 0 ? 'text-brand-600' : 'text-slate-400' ?>">
                                <?= $recibido > 0 ? MoneyHelper::formatShort($recibido) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-right hidden lg:table-cell">
                                <?php if ($diff !== null): ?>
                                    <span class="inline-flex items-center gap-1 font-bold px-2.5 py-1 rounded-lg text-xs <?= abs($diff) < 0.01 ? 'bg-emerald-50 text-emerald-600' : ($diff < 0 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600') ?>">
                                        <?php if (abs($diff) < 0.01): ?>
                                            <i class="isax isax-tick-circle"></i> OK
                                        <?php else: ?>
                                            <?= $diff >= 0 ? '+' : '' ?><?= MoneyHelper::formatShort($diff) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-300 font-bold">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="<?= $badges[$r['estado']] ?? 'badge' ?> inline-flex items-center gap-1.5">
                                    <i class="isax <?= $icons[$r['estado']] ?? 'isax-info-circle' ?> hidden sm:inline-block"></i>
                                    <?= $labels[$r['estado']] ?? $r['estado'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($r['estado'] === 'pendiente'): ?>
                                    <a href="<?= url('admin/rendiciones/' . $r['id']) ?>"
                                       class="btn-primary text-xs whitespace-nowrap shadow-sm shadow-brand-500/20">
                                        <i class="isax isax-tick-circle"></i> Confirmar
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('admin/rendiciones/' . $r['id']) ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                        <i class="isax isax-arrow-right-3"></i> Ver
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Mostrando <span class="font-bold text-slate-800"><?= count($rendiciones) ?></span> rendición(es)</span>
            </div>
        </div>
    <?php endif; ?>
</div>
