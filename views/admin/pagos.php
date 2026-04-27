<?php // views/admin/pagos.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">Historial de Pagos</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Últimos 200 registros — solo admin puede anular</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden"
         x-data="{ modalOpen: false, pagoId: null, pagoInfo: '' }">

        <!-- Modal de anulación -->
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
             x-transition:enter="transition duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 border border-slate-100"
                 @click.stop x-transition:enter="transition duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <i class="isax isax-close-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Anular Pago</h3>
                        <p class="text-xs text-slate-500 font-medium" x-text="pagoInfo"></p>
                    </div>
                </div>
                <form method="POST" :action="'/admin/pagos/' + pagoId + '/anular'">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Motivo <span class="text-red-500">*</span>
                        </label>
                        <textarea name="motivo" rows="3" required
                                  class="form-input resize-none"
                                  placeholder="Describe el motivo de la anulación..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-red-600/20 transition-all flex items-center justify-center gap-2">
                            <i class="isax isax-close-circle"></i> Confirmar Anulación
                        </button>
                        <button type="button" @click="modalOpen = false"
                                class="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-4">#</th>
                        <th class="px-4 py-4">Fecha</th>
                        <th class="px-4 py-4">Cliente</th>
                        <th class="px-4 py-4 hidden md:table-cell">Crédito / Cuota</th>
                        <th class="px-4 py-4 hidden sm:table-cell">Cobrador</th>
                        <th class="px-4 py-4 text-center hidden lg:table-cell">Método</th>
                        <th class="px-4 py-4 text-right">Monto</th>
                        <th class="px-4 py-4 text-center">Estado</th>
                        <th class="px-4 py-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($pagos as $p): ?>
                    <?php $esAnulado = $p['estado'] === 'anulado'; ?>
                    <tr class="hover:bg-slate-50/50 transition-colors <?= $esAnulado ? 'opacity-50' : '' ?>">
                        <td class="px-4 py-3 font-mono text-slate-400 text-xs"><?= $p['id'] ?></td>
                        <td class="px-4 py-3 text-slate-600 font-medium text-xs whitespace-nowrap">
                            <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                            <span class="block text-slate-400"><?= date('H:i', strtotime($p['created_at'])) ?></span>
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-800">
                            <?= e($p['cliente_nombre']) ?>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-slate-500 font-medium text-xs">
                            <a href="<?= url('creditos/' . $p['credito_id']) ?>" class="text-brand-600 hover:underline">#<?= $p['credito_id'] ?></a>
                            <span class="text-slate-400">· Cuota <?= $p['numero_cuota'] ?></span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-slate-600 font-medium text-xs"><?= e($p['cobrador_nombre']) ?></td>
                        <td class="px-4 py-3 text-center hidden lg:table-cell">
                            <?php if (($p['metodo_pago'] ?? 'efectivo') === 'transferencia'): ?>
                                <span class="badge-transferencia">Transf.</span>
                            <?php else: ?>
                                <span class="badge-efectivo">Efvo.</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right font-bold <?= $esAnulado ? 'line-through text-slate-400' : 'text-slate-900' ?>" style="font-family:'Outfit',sans-serif;">
                            <?= MoneyHelper::format((float)$p['monto']) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($esAnulado): ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded uppercase tracking-wider">
                                    Anulado
                                </span>
                                <?php if ($p['motivo_anulacion']): ?>
                                <span class="block text-[10px] text-slate-400 mt-0.5 max-w-[120px] truncate" title="<?= e($p['motivo_anulacion']) ?>">
                                    <?= e($p['motivo_anulacion']) ?>
                                </span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded uppercase tracking-wider">
                                    <?= match($p['estado']) {
                                        'pendiente_rendir' => 'Por rendir',
                                        'rendido'          => 'Rendido',
                                        'confirmado'       => 'Confirmado',
                                        default            => ucfirst($p['estado']),
                                    } ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if (!$esAnulado): ?>
                            <button type="button"
                                    @click="modalOpen = true; pagoId = <?= $p['id'] ?>; pagoInfo = '<?= e($p['cliente_nombre']) ?> · <?= MoneyHelper::format((float)$p['monto']) ?> · Cuota <?= $p['numero_cuota'] ?>'"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="isax isax-close-circle"></i> Anular
                            </button>
                            <?php else: ?>
                            <span class="text-slate-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
            <span>Mostrando <span class="font-bold text-slate-800"><?= count($pagos) ?></span> registros</span>
        </div>
    </div>
</div>
