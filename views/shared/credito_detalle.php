<?php // views/shared/credito_detalle.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
use App\Core\Auth;

$badges = [
    'pendiente_autorizacion' => 'badge-pendiente',
    'activo'    => 'badge-activo',
    'finalizado'=> 'badge-finalizado',
    'rechazado' => 'badge-rechazado',
    'cancelado' => 'badge-rechazado',
];
$estadosCuota = [
    'pendiente' => 'badge-pendiente',
    'parcial'   => 'badge-parcial',
    'pagada'    => 'badge-pagada',
    'vencida'   => 'badge-vencida',
];
$backUrl = Auth::isAdmin()
    ? url('admin/creditos')
    : (Auth::isStaff() ? url('vendedor/creditos') : url('dashboard'));
?>
<div class="max-w-4xl mx-auto space-y-6 pb-10">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="<?= $backUrl ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
                <i class="isax isax-arrow-left-2"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Crédito #<?= $credito['id'] ?>
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                    <span class="<?= $badges[$credito['estado']] ?? 'badge' ?>">
                        <?= ucfirst(str_replace('_', ' ', $credito['estado'])) ?>
                    </span>
                    <span class="text-slate-300">•</span>
                    <?= date('d/m/Y', strtotime($credito['created_at'])) ?>
                </p>
            </div>
        </div>
        
        <?php if (Auth::isAdmin() && in_array($credito['estado'], ['activo', 'pendiente_autorizacion'], true)): ?>
            <div class="flex gap-2" x-data="{ cancelarOpen: false }">
                <a href="<?= url('admin/creditos/' . $credito['id'] . '/editar') ?>"
                   class="btn-secondary">
                    <i class="isax isax-edit-2"></i> Editar
                </a>
                <button type="button" @click="cancelarOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 border border-red-200 text-red-700 font-bold text-sm hover:bg-red-100 transition-colors">
                    <i class="isax isax-close-circle"></i> Cancelar crédito
                </button>

                <!-- Modal cancelar crédito -->
                <div x-show="cancelarOpen" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                     x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-red-100" @click.stop
                         x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <i class="isax isax-close-circle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Cancelar Crédito #<?= $credito['id'] ?></h3>
                                <p class="text-xs text-slate-500">Esta acción es irreversible si no hay pagos.</p>
                            </div>
                        </div>
                        <form method="POST" action="<?= url('admin/creditos/' . $credito['id'] . '/cancelar') ?>">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Motivo <span class="text-red-500">*</span></label>
                                <textarea name="motivo" rows="3" required class="form-input resize-none"
                                          placeholder="Describe el motivo de la cancelación..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-red-600/20 transition-all flex items-center justify-center gap-2">
                                    <i class="isax isax-close-circle"></i> Confirmar Cancelación
                                </button>
                                <button type="button" @click="cancelarOpen = false" class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors">
                                    Volver
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info principal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Tarjeta Cliente -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                <i class="isax isax-profile-circle text-8xl text-brand-600"></i>
            </div>
            
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-5">
                <i class="isax isax-user text-brand-500"></i> Información del Cliente
            </h2>
            
            <div class="space-y-4 text-sm relative z-10">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Nombre</span>
                    <a href="<?= url('vendedor/clientes/' . $credito['cliente_id']) ?>" class="font-bold text-brand-600 hover:underline">
                        <?= e($credito['cliente_nombre']) ?>
                    </a>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">DNI</span>
                    <span class="font-medium text-slate-900"><?= e($credito['dni']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Teléfono</span>
                    <?php if ($credito['telefono']): ?>
                        <span class="inline-flex items-center gap-1 font-medium text-slate-900">
                            <i class="isax isax-call text-slate-400"></i> <?= e($credito['telefono']) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-slate-400">—</span>
                    <?php endif; ?>
                </div>
                <div class="pt-2 border-t border-slate-100/80">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Domicilio</span>
                    <span class="font-medium text-slate-900"><?= e($credito['domicilio'] ?? '—') ?></span>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta Condiciones -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
                <i class="isax isax-money-tick text-8xl text-white"></i>
            </div>
            
            <h2 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-5">
                <i class="isax isax-receipt-item text-brand-400"></i> Condiciones del Préstamo
            </h2>
            
            <div class="grid grid-cols-2 gap-y-5 gap-x-4 text-sm relative z-10">
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Monto Prestado</span>
                    <span class="font-bold text-xl tracking-tight text-white" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::format((float)$credito['monto_prestado']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Monto a Devolver</span>
                    <span class="font-bold text-xl tracking-tight text-emerald-400" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::format((float)$credito['monto_a_devolver']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Plan de Cuotas</span>
                    <span class="inline-flex items-center gap-1.5 font-medium bg-slate-800/50 px-2.5 py-1 rounded-lg border border-slate-700">
                        <i class="isax isax-calendar-1 text-slate-400"></i>
                        <?= $credito['cantidad_cuotas'] ?> × <?= ucfirst($credito['frecuencia']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Cobrador Asignado</span>
                    <span class="font-medium flex items-center gap-1">
                        <i class="isax isax-user-tag text-slate-400"></i>
                        <?= e($credito['cobrador_nombre'] ?? 'Sin asignar') ?>
                    </span>
                </div>
                
                <?php if ((float)$credito['mora_acumulada'] > 0): ?>
                <div class="col-span-2 pt-4 border-t border-slate-700/50 flex items-center justify-between">
                    <span class="font-medium text-red-300 flex items-center gap-1.5">
                        <i class="isax isax-warning-2"></i> Mora acumulada
                    </span>
                    <span class="font-bold text-red-400 text-lg">
                        <?= MoneyHelper::format((float)$credito['mora_acumulada']) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tabla de cuotas -->
    <?php if (!empty($cuotas)): ?>
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-6">
            <i class="isax isax-calendar-tick text-brand-500"></i> Plan de Cuotas
        </h2>
        
        <div class="table-container">
            <table class="table w-full text-sm">
                <thead>
                    <tr>
                        <th class="w-12 text-center">#</th>
                        <th>Vencimiento</th>
                        <th class="text-right">Monto</th>
                        <th class="text-right">Pagado</th>
                        <th class="text-right">Saldo</th>
                        <th class="text-right">Estado</th>
                        <?php
                            $puedeCobrarcuota = $credito['estado'] === 'activo'
                                && (Auth::isAdmin() || (Auth::isStaff() && (int)$credito['cobrador_id'] === Auth::id()));
                        ?>
                        <?php if ($puedeCobrarcuota): ?>
                        <th class="text-center">Acción</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($cuotas as $cu): ?>
                    <?php
                        $pagado = (float)($cu['monto_pagado'] ?? 0);
                        $saldo  = (float)$cu['monto'] - $pagado;
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="text-center font-bold text-slate-400"><?= $cu['numero_cuota'] ?></td>
                        <td class="font-medium text-slate-700"><?= DateHelper::formatoArg($cu['fecha_vencimiento']) ?></td>
                        <td class="text-right font-semibold text-slate-900"><?= MoneyHelper::formatShort((float)$cu['monto']) ?></td>
                        <td class="text-right font-semibold text-emerald-600"><?= MoneyHelper::formatShort($pagado) ?></td>
                        <td class="text-right font-bold <?= $saldo > 0 ? 'text-red-500' : 'text-slate-300' ?>">
                            <?= MoneyHelper::formatShort($saldo) ?>
                        </td>
                        <td class="text-right">
                            <span class="<?= $estadosCuota[$cu['estado']] ?? 'badge' ?>">
                                <?= ucfirst($cu['estado']) ?>
                            </span>
                        </td>
                        <?php if ($puedeCobrarcuota): ?>
                        <td class="text-center py-2">
                            <?php if ($cu['estado'] !== 'pagada'): ?>
                            <?php
                                $pagoUrl = Auth::isAdmin()
                                    ? url('admin/creditos/' . $credito['id'] . '/pago/' . $cu['id'])
                                    : url('cobrador/pago/' . $credito['id'] . '/' . $cu['id']);
                            ?>
                            <a href="<?= $pagoUrl ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 active:scale-95 transition-all shadow-sm shadow-brand-500/30 whitespace-nowrap">
                                <i class="isax isax-money-recive"></i> Cobrar
                            </a>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                                <i class="isax isax-tick-circle"></i> Saldada
                            </span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Log de auditoría -->
    <?php if (!empty($log)): ?>
    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/60">
        <h2 class="text-sm font-bold text-slate-600 uppercase tracking-wider flex items-center gap-2 mb-4">
            <i class="isax isax-clock text-slate-400"></i> Historial de Cambios
        </h2>
        <div class="space-y-4">
            <?php foreach ((array)$log as $entry): ?>
            <div class="flex gap-4 items-start relative">
                <!-- Timeline dot -->
                <div class="absolute left-[11px] top-6 bottom-[-16px] w-[2px] bg-slate-200 last:hidden"></div>
                
                <div class="w-6 h-6 rounded-full bg-white border-2 border-brand-200 flex items-center justify-center shrink-0 mt-0.5 z-10">
                    <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                </div>
                
                <div class="bg-white rounded-xl p-3 border border-slate-100 shadow-sm flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                        <span class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                            <i class="isax isax-user text-slate-400"></i> <?= e($entry['usuario_nombre']) ?>
                        </span>
                        <span class="text-slate-400 text-xs font-medium flex items-center gap-1">
                            <i class="isax isax-calendar-1"></i> <?= date('d/m/Y H:i', strtotime($entry['created_at'])) ?>
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 font-medium">
                        <?= e($entry['nota']) ?>
                        <?php if ($entry['estado_desde'] && $entry['estado_hasta']): ?>
                            <span class="inline-flex items-center gap-1 ml-1 text-xs px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md">
                                <?= str_replace('_', ' ', $entry['estado_desde']) ?>
                                <i class="isax isax-arrow-right-3"></i>
                                <?= str_replace('_', ' ', $entry['estado_hasta']) ?>
                            </span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
