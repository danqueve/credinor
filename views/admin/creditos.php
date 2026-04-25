<?php // views/admin/creditos.php
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
$icons = [
    'pendiente_autorizacion' => 'isax-clock',
    'activo'    => 'isax-tick-circle',
    'finalizado'=> 'isax-flag-2',
    'rechazado' => 'isax-close-circle',
    'cancelado' => 'isax-slash',
];
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Créditos</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Gestión global de créditos de todas las sucursales</p>
        </div>
    </div>

    <!-- Filtro -->
    <div class="bg-white rounded-2xl p-2 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] inline-flex flex-wrap gap-1">
        <?php foreach (['', 'pendiente_autorizacion', 'activo', 'finalizado', 'rechazado'] as $e): ?>
            <?php $isActive = $estado === $e; ?>
            <a href="<?= url('admin/creditos' . ($e ? '?estado=' . $e : '')) ?>"
               class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2
                      <?= $isActive 
                          ? 'bg-slate-900 text-white shadow-md' 
                          : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' ?>">
                <?php if ($e): ?>
                    <i class="isax <?= $icons[$e] ?>"></i>
                <?php else: ?>
                    <i class="isax isax-category"></i>
                <?php endif; ?>
                <?= $e ? $labels[$e] : 'Todos' ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($creditos)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-document-text text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay créditos</h3>
            <p class="text-slate-500 font-medium">No se encontraron créditos en este estado.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Sucursal</th>
                            <th class="px-6 py-4 hidden md:table-cell">Vendedor</th>
                            <th class="px-6 py-4 text-right">Prestado</th>
                            <th class="px-6 py-4 text-right">A devolver</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($creditos as $c): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800"><?= e($c['cliente_nombre']) ?></p>
                                <p class="text-xs font-medium text-slate-500 mt-0.5">DNI <?= e($c['dni']) ?></p>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-shop"></i> <?= e($c['sucursal_nombre']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="flex items-center gap-1.5 text-slate-600 font-medium">
                                    <i class="isax isax-user text-slate-400"></i> <?= e($c['vendedor_nombre']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">
                                <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-brand-600">
                                <?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="<?= $badges[$c['estado']] ?? 'badge' ?> inline-flex items-center gap-1.5">
                                    <i class="isax <?= $icons[$c['estado']] ?? 'isax-info-circle' ?> hidden sm:inline-block"></i>
                                    <?= $labels[$c['estado']] ?? $c['estado'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if ($c['estado'] === 'pendiente_autorizacion'): ?>
                                    <a href="<?= url('admin/creditos/' . $c['id'] . '/autorizar') ?>"
                                       class="btn-primary text-xs whitespace-nowrap shadow-sm shadow-brand-500/20">
                                        <i class="isax isax-tick-circle"></i> Evaluar
                                    </a>
                                <?php else: ?>
                                    <a href="<?= url('creditos/' . $c['id']) ?>"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-brand-100 hover:text-brand-600 transition-colors tooltip" title="Ver detalle">
                                        <i class="isax isax-eye"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Mostrando <span class="font-bold text-slate-800"><?= count($creditos) ?></span> crédito<?= count($creditos) !== 1 ? 's' : '' ?></span>
            </div>
        </div>
    <?php endif; ?>
</div>
