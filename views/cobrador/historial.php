<?php // views/cobrador/historial.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-lg mx-auto space-y-6 pb-24">
    <!-- Header App-like -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
            <i class="isax isax-receipt-search text-8xl"></i>
        </div>
        
        <div class="relative z-10">
            <h1 class="text-2xl font-extrabold tracking-tight mb-1" style="font-family: 'Outfit', sans-serif;">Historial de Cobros</h1>
            <p class="text-slate-400 font-medium text-sm flex items-center gap-1.5">
                <i class="isax isax-calendar-1 text-slate-500"></i> Últimos 60 días
            </p>
        </div>
    </div>

    <?php if (empty($pagos)): ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-12 px-6">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-4 border border-slate-100">
                <i class="isax isax-empty-wallet text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay historial</h3>
            <p class="text-sm text-slate-500 font-medium mb-6">No se encontraron cobros registrados en los últimos 60 días.</p>
            <a href="<?= url('cobrador/agenda') ?>" class="btn-primary inline-flex justify-center shadow-md shadow-brand-500/20 active:scale-95">
                <i class="isax isax-calendar-tick mr-2"></i> Ir a la agenda
            </a>
        </div>
    <?php else: ?>

        <!-- Resumen -->
        <?php
        $totalMonto = array_sum(array_column($pagos, 'monto'));
        $countHoy   = count(array_filter($pagos, fn($p) => date('Y-m-d', strtotime($p['created_at'])) === date('Y-m-d')));
        ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                    <i class="isax isax-wallet-add text-5xl text-brand-600"></i>
                </div>
                <div class="relative z-10">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total Recaudado</span>
                    <span class="block text-2xl font-extrabold text-brand-600" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::formatShort($totalMonto) ?>
                    </span>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                    <i class="isax isax-task-square text-5xl text-emerald-600"></i>
                </div>
                <div class="relative z-10">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Operaciones</span>
                    <div class="flex items-end gap-2">
                        <span class="block text-2xl font-extrabold text-emerald-600" style="font-family: 'Outfit', sans-serif;">
                            <?= count($pagos) ?>
                        </span>
                        <?php if ($countHoy > 0): ?>
                        <span class="text-xs font-bold text-emerald-500 mb-1.5">+<?= $countHoy ?> hoy</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado Timeline -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <?php
            $fechaActual = null;
            foreach ($pagos as $pago):
                $fecha = date('Y-m-d', strtotime($pago['created_at']));
                if ($fecha !== $fechaActual):
                    $fechaActual = $fecha;
            ?>
            <div class="bg-slate-50/80 px-5 py-3 border-y border-slate-100 first:border-t-0 flex items-center justify-between sticky top-0 z-10 backdrop-blur-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i class="isax isax-calendar-1 text-slate-400"></i>
                    <?= date('d \d\e F \d\e Y', strtotime($fecha)) ?>
                </span>
                <?php if ($fecha === date('Y-m-d')): ?>
                    <span class="bg-brand-100 text-brand-700 text-[10px] font-bold px-2 py-0.5 rounded-md">HOY</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-50 last:border-0 hover:bg-slate-50/50 transition-colors group relative">
                
                <!-- Ícono estado -->
                <?php
                    $isConfirmado = $pago['estado'] === 'confirmado';
                    $isAnulado = $pago['estado'] === 'anulado';
                    $iconBg = $isConfirmado ? 'bg-emerald-50' : ($isAnulado ? 'bg-red-50' : 'bg-amber-50');
                    $iconColor = $isConfirmado ? 'text-emerald-500' : ($isAnulado ? 'text-red-500' : 'text-amber-500');
                    $iconClass = $isConfirmado ? 'isax-tick-circle' : ($isAnulado ? 'isax-close-circle' : 'isax-timer-1');
                ?>
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border border-slate-100 <?= $iconBg ?> <?= $iconColor ?>">
                    <i class="isax <?= $iconClass ?> text-xl"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate text-sm mb-0.5 <?= $isAnulado ? 'line-through text-slate-400' : '' ?>">
                        <?= e($pago['cliente_nombre']) ?>
                    </p>
                    <div class="flex items-center flex-wrap gap-2 text-[11px] font-medium text-slate-500 mt-0.5">
                        <span class="uppercase tracking-wider">Cuota #<?= $pago['numero_cuota'] ?></span>
                        <span class="w-1 h-1 rounded-full bg-slate-300 hidden sm:block"></span>
                        <span class="flex items-center gap-1"><i class="isax isax-clock"></i> <?= date('H:i', strtotime($pago['created_at'])) ?></span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <?php if ($pago['metodo_pago'] === 'transferencia'): ?>
                            <span class="text-purple-600 font-bold tracking-wider uppercase flex items-center gap-0.5"><i class="isax isax-card-tick"></i> Transf.</span>
                        <?php else: ?>
                            <span class="text-emerald-600 font-bold tracking-wider uppercase flex items-center gap-0.5"><i class="isax isax-money-tick"></i> Efvo.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-right shrink-0 flex flex-col items-end gap-1.5">
                    <p class="font-extrabold text-base <?= $isAnulado ? 'line-through text-slate-300' : 'text-slate-800' ?>" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::formatShort((float)$pago['monto']) ?>
                    </p>
                    <?php if ((float)$pago['monto_a_mora'] > 0 && !$isAnulado): ?>
                    <p class="text-[10px] font-bold text-red-500 flex items-center justify-end gap-1">
                        <i class="isax isax-danger text-red-400"></i> <?= MoneyHelper::formatShort((float)$pago['monto_a_mora']) ?> mora
                    </p>
                    <?php endif; ?>
                    <?php if (!$isAnulado): ?>
                    <a href="<?= url('cobrador/pago/' . $pago['id'] . '/recibo') ?>"
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-bold hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 border border-transparent transition-all">
                        <i class="isax isax-receipt-2"></i> Recibo
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>
