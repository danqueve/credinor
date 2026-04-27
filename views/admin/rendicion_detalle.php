<?php // views/admin/rendicion_detalle.php
use App\Helpers\MoneyHelper;

$declarado = (float)$rendicion['monto_declarado'];
$recibido  = (float)($rendicion['monto_recibido'] ?? 0);
$diff      = $recibido > 0 ? $recibido - $declarado : null;
?>
<div class="max-w-4xl mx-auto space-y-6 pb-10">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="<?= url('admin/rendiciones') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
                <i class="isax isax-arrow-left-2"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Rendición #<?= $rendicion['id'] ?>
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                    <?php if ($rendicion['estado'] === 'pendiente'): ?>
                        <span class="badge-pendiente inline-flex items-center gap-1"><i class="isax isax-clock"></i> Pendiente</span>
                    <?php elseif ($rendicion['estado'] === 'confirmada'): ?>
                        <span class="badge-activo inline-flex items-center gap-1"><i class="isax isax-tick-circle"></i> Confirmada</span>
                    <?php else: ?>
                        <span class="badge-rechazado inline-flex items-center gap-1"><i class="isax isax-close-circle"></i> Rechazada</span>
                    <?php endif; ?>
                    <span class="text-slate-300">•</span>
                    <?= date('d/m/Y', strtotime($rendicion['fecha'])) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Info General -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm relative z-10">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-profile-circle text-xl"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Cobrador</span>
                    <span class="font-bold text-slate-900"><?= e($rendicion['cobrador_nombre']) ?></span>
                </div>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                    <i class="isax isax-shop text-xl"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Sucursal</span>
                    <span class="font-bold text-slate-900"><?= e($rendicion['sucursal_nombre']) ?></span>
                </div>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                    <i class="isax isax-calendar-1 text-xl"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Fecha</span>
                    <span class="font-bold text-slate-900"><?= date('d/m/Y', strtotime($rendicion['fecha'])) ?></span>
                </div>
            </div>
            
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center shrink-0">
                    <i class="isax isax-document-text text-xl"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Cobros</span>
                    <span class="font-bold text-brand-600"><?= count($rendicion['pagos']) ?> recibos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Montos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl relative overflow-hidden text-center">
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Declarado por el cobrador</span>
            <span class="font-bold text-3xl tracking-tight text-white block" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::format($declarado) ?>
            </span>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center relative">
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Físicamente Recibido</span>
            <span class="font-bold text-3xl tracking-tight <?= $recibido > 0 ? 'text-emerald-600' : 'text-slate-300' ?> block" style="font-family: 'Outfit', sans-serif;">
                <?= $recibido > 0 ? MoneyHelper::format($recibido) : '—' ?>
            </span>
        </div>
        
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center relative">
            <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Diferencia de Caja</span>
            <?php if ($diff !== null): ?>
                <?php 
                    $diffClass = 'text-emerald-600';
                    $diffBg = 'bg-emerald-50';
                    $diffIcon = 'isax-tick-circle';
                    
                    if (abs($diff) > 0.01) {
                        if ($diff < 0) {
                            $diffClass = 'text-red-500';
                            $diffBg = 'bg-red-50';
                            $diffIcon = 'isax-arrow-down';
                        } else {
                            $diffClass = 'text-blue-500';
                            $diffBg = 'bg-blue-50';
                            $diffIcon = 'isax-arrow-up-3';
                        }
                    }
                ?>
                <div class="flex items-center justify-center gap-2">
                    <span class="font-bold text-3xl tracking-tight <?= $diffClass ?>" style="font-family: 'Outfit', sans-serif;">
                        <?= $diff > 0 ? '+' : '' ?><?= MoneyHelper::format($diff) ?>
                    </span>
                </div>
            <?php else: ?>
                <span class="font-bold text-3xl tracking-tight text-slate-300 block" style="font-family: 'Outfit', sans-serif;">—</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalle de pagos -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="p-6 border-b border-slate-100/80">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="isax isax-receipt-item text-brand-500"></i> Desglose de Cobros Incluidos
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Cuota</th>
                                <th class="px-6 py-4 text-right">Capital</th>
                                <th class="px-6 py-4 text-right">Mora</th>
                                <th class="px-6 py-4 text-center">Método</th>
                                <th class="px-6 py-4 text-right">Total Cobrado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($rendicion['pagos'] as $p): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800"><?= e($p['cliente_nombre']) ?></td>
                                <td class="px-6 py-4 text-slate-500 font-medium">#<?= $p['numero_cuota'] ?></td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-700"><?= MoneyHelper::format((float)$p['monto_a_capital']) ?></td>
                                <td class="px-6 py-4 text-right font-semibold text-red-500"><?= MoneyHelper::format((float)$p['monto_a_mora']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($p['metodo_pago'] === 'transferencia'): ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-purple-600 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded uppercase tracking-wider">Transf.</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded uppercase tracking-wider">Efvo.</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-600"><?= MoneyHelper::format((float)$p['monto']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-900 text-white border-t border-slate-700">
                            <tr>
                                <td colspan="5" class="px-6 py-4 font-bold text-right text-slate-300 uppercase tracking-wider text-xs">Total Declarado</td>
                                <td class="px-6 py-4 text-right font-bold text-lg" style="font-family: 'Outfit', sans-serif;">
                                    <?= MoneyHelper::format($declarado) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Acciones solo si está pendiente -->
        <div class="lg:col-span-1 space-y-6">
            <?php if ($rendicion['estado'] === 'pendiente'): ?>
            <div class="bg-emerald-50/50 rounded-3xl p-6 border border-emerald-100 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full bg-emerald-400"></div>
                <h2 class="text-base font-bold text-emerald-900 flex items-center gap-2 mb-4">
                    <i class="isax isax-tick-circle text-emerald-600"></i> Confirmar Rendición
                </h2>
                <form method="POST" action="<?= url('admin/rendiciones/' . $rendicion['id'] . '/confirmar') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-5">
                        <label for="monto_recibido" class="block text-sm font-bold text-emerald-800 mb-2">
                            Monto físicamente recibido
                            <span class="block text-xs text-emerald-600/70 mt-0.5 font-medium">Declarado por cobrador: <?= MoneyHelper::format($declarado) ?></span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 font-bold">$</span>
                            <input id="monto_recibido" type="number" name="monto_recibido"
                                   step="0.01" min="0" required
                                   class="form-input pl-8 border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20 bg-white font-bold text-lg"
                                   value="<?= number_format($declarado, 2, '.', '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                        <i class="isax isax-verify"></i> Guardar Confirmación
                    </button>
                </form>
            </div>

            <!-- Rechazar -->
            <div class="bg-red-50/50 rounded-3xl p-6 border border-red-100 shadow-sm relative overflow-hidden" x-data="{ abierto: false }">
                <div class="absolute top-0 right-0 w-2 h-full bg-red-400"></div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-red-900 flex items-center gap-2">
                        <i class="isax isax-close-circle text-red-600"></i> Problemas
                    </h2>
                    <button type="button" @click="abierto = !abierto"
                            class="text-sm text-red-600 hover:text-red-800 font-bold bg-white px-3 py-1.5 rounded-lg shadow-sm border border-red-100 transition-colors" x-show="!abierto">
                        Rechazar Rendición
                    </button>
                </div>
                
                <div x-show="abierto" x-cloak x-transition.opacity>
                    <form method="POST" action="<?= url('admin/rendiciones/' . $rendicion['id'] . '/rechazar') ?>">
                        <?= csrf_field() ?>
                        <label for="motivo" class="block text-sm font-bold text-red-800 mb-2">Motivo del rechazo <span class="text-red-500">*</span></label>
                        <textarea name="motivo" rows="3" required
                                  class="form-input resize-none mb-4 border-red-200 focus:border-red-500 focus:ring-red-500/20 bg-white"
                                  placeholder="Explica qué problema hay con esta rendición..."></textarea>
                        
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all flex items-center justify-center">
                                Rechazar
                            </button>
                            <button type="button" @click="abierto = false" class="px-4 py-3 bg-white text-slate-500 font-bold rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-200/60 text-center">
                    <div class="w-16 h-16 rounded-full bg-white shadow-sm flex items-center justify-center mx-auto mb-4">
                        <i class="isax isax-shield-tick text-2xl text-slate-400"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Rendición Procesada</h3>
                    <p class="text-xs text-slate-500 font-medium">Esta rendición ya ha sido evaluada y no puede ser modificada.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
