<?php // views/vendedor/creditos.php
use App\Helpers\DateHelper;
use App\Helpers\MoneyHelper;

$badges = [
    'pendiente_autorizacion' => 'bg-amber-100 text-amber-800 border-amber-200',
    'activo'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'finalizado'=> 'bg-blue-100 text-blue-800 border-blue-200',
    'rechazado' => 'bg-red-100 text-red-800 border-red-200',
    'cancelado' => 'bg-slate-100 text-slate-800 border-slate-200',
];

$icons = [
    'pendiente_autorizacion' => 'isax-timer-1',
    'activo'    => 'isax-verify',
    'finalizado'=> 'isax-tick-circle',
    'rechazado' => 'isax-close-circle',
    'cancelado' => 'isax-minus-cirlce',
];

$labels = [
    'pendiente_autorizacion' => 'Pendiente',
    'activo'    => 'Activo',
    'finalizado'=> 'Finalizado',
    'rechazado' => 'Rechazado',
    'cancelado' => 'Cancelado',
];
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Mis Créditos</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Historial y estado de las solicitudes enviadas</p>
        </div>
        <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary shadow-md shadow-brand-500/20">
            <i class="isax isax-money-send"></i> Nueva Solicitud
        </a>
    </div>

    <!-- Filtros de Estado -->
    <div class="bg-white rounded-2xl p-2 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] inline-flex flex-wrap gap-1">
        <?php foreach (['', 'pendiente_autorizacion', 'activo', 'finalizado', 'rechazado'] as $e): ?>
            <?php $isActive = $estado === $e; ?>
            <a href="<?= url('vendedor/creditos?estado=' . $e) ?>"
               class="px-4 py-2 text-sm font-bold rounded-xl transition-all flex items-center gap-2 <?= $isActive ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                <?php if ($e !== ''): ?>
                    <i class="isax <?= $icons[$e] ?> <?= $isActive ? 'text-white' : 'text-slate-400' ?>"></i>
                <?php endif; ?>
                <?= $labels[$e] ?? 'Todos' ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <?php if (empty($creditos)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-receipt-search text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay créditos registrados</h3>
            <p class="text-slate-500 font-medium mb-6">
                No tienes solicitudes de crédito con el estado seleccionado actualmente.
            </p>
            <?php if (!$estado): ?>
                <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary inline-flex">
                    <i class="isax isax-money-send"></i> Crear la primera solicitud
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 text-right">Montos</th>
                            <th class="px-6 py-4 hidden md:table-cell">Plan de Pago</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($creditos as $c): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="isax isax-user"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-800 block text-base"><?= e($c['cliente_nombre']) ?></span>
                                        <span class="text-xs font-medium text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="isax isax-personalcard"></i> DNI: <?= e($c['dni']) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-800 block"><?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?> <span class="text-[10px] text-slate-400 font-normal uppercase">Total</span></span>
                                <span class="text-[10px] font-medium text-emerald-600 block mt-0.5">Prestado: <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?></span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-calendar-1"></i> <?= $c['cantidad_cuotas'] ?> cuotas
                                </span>
                                <span class="block text-[10px] text-slate-400 uppercase font-bold mt-1.5 ml-1"><?= $c['frecuencia'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badges[$c['estado']] ?? 'bg-slate-100 text-slate-800 border-slate-200' ?>">
                                    <i class="isax <?= $icons[$c['estado']] ?? 'isax-info-circle' ?>"></i>
                                    <?= $labels[$c['estado']] ?? $c['estado'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('creditos/' . $c['id']) ?>"
                                   class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-colors tooltip font-bold text-xs shadow-sm" title="Ver Detalles">
                                    <i class="isax isax-eye mr-1.5"></i> Ver
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
