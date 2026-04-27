<?php // views/cobrador/cierre_caja.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-lg mx-auto space-y-6 pb-24">
    <!-- Header App-like -->
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= url('cobrador/agenda') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-95 shadow-sm">
            <i class="isax isax-arrow-left-2 text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900" style="font-family: 'Outfit', sans-serif;">Cierre de Caja</h1>
            <p class="text-slate-500 font-medium text-xs">Rendición diaria de cobranza</p>
        </div>
    </div>

    <?php if ($rendicion): ?>
        <!-- Ya cerró caja hoy -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-3xl p-8 text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden text-center">
            <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
                <i class="isax isax-verify text-8xl"></i>
            </div>
            
            <div class="relative z-10">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                    <i class="isax isax-tick-circle text-4xl text-white"></i>
                </div>
                
                <h2 class="text-xl font-extrabold tracking-tight mb-1" style="font-family: 'Outfit', sans-serif;">Caja Cerrada Correctamente</h2>
                <p class="text-emerald-100 text-sm font-medium mb-6">Tu rendición del día ya fue enviada a administración.</p>
                
                <div class="bg-black/10 rounded-2xl p-4 backdrop-blur-sm border border-white/10 mb-6">
                    <span class="block text-emerald-200 text-xs font-bold uppercase tracking-wider mb-1">Total Rendido</span>
                    <span class="block text-3xl font-extrabold" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::format((float)$rendicion['monto_declarado']) ?>
                    </span>
                </div>
                
                <div class="inline-flex items-center gap-1.5 bg-white/20 px-3 py-1.5 rounded-full text-xs font-bold mb-6">
                    <i class="isax isax-info-circle"></i> Estado: <?= ucfirst($rendicion['estado']) ?>
                </div>

                <a href="<?= url('cobrador/rendiciones') ?>" class="block w-full bg-white/10 hover:bg-white/20 text-white font-bold py-3.5 px-4 rounded-xl transition-colors border border-white/20 flex items-center justify-center gap-2 text-sm active:scale-[0.98]">
                    <i class="isax isax-clock"></i> Ver historial de rendiciones
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Pagos del día -->
        <?php if (empty($pagos)): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-12 px-6">
                <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <i class="isax isax-empty-wallet text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No hay cobros hoy</h3>
                <p class="text-sm text-slate-500 font-medium mb-6">No has registrado ningún pago en el sistema el día de hoy.</p>
                <a href="<?= url('cobrador/agenda') ?>" class="btn-primary inline-flex justify-center shadow-md shadow-brand-500/20 active:scale-95">
                    <i class="isax isax-calendar-tick mr-2"></i> Ir a la agenda
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <i class="isax isax-receipt-item text-brand-500 text-xl"></i>
                    <h2 class="font-bold text-slate-800">Detalle de Cobros</h2>
                    <span class="ml-auto bg-brand-100 text-brand-700 text-xs font-bold px-2 py-0.5 rounded-full">
                        <?= count($pagos) ?>
                    </span>
                </div>
                
                <div class="divide-y divide-slate-100">
                    <?php foreach ($pagos as $p): ?>
                    <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">
                                <?= mb_strtoupper(mb_substr($p['cliente_nombre'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm leading-tight"><?= e($p['cliente_nombre']) ?></p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">Cuota #<?= $p['numero_cuota'] ?></p>
                            </div>
                        </div>
                        <span class="font-extrabold text-brand-600" style="font-family: 'Outfit', sans-serif;">
                            <?= MoneyHelper::format((float)$p['monto']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="p-5 bg-slate-900 text-white flex justify-between items-center rounded-b-3xl">
                    <span class="text-sm font-bold uppercase tracking-wider text-slate-400">Total Recaudado</span>
                    <span class="text-2xl font-extrabold" style="font-family: 'Outfit', sans-serif;"><?= MoneyHelper::format($total) ?></span>
                </div>
            </div>

            <!-- Formulario de cierre -->
            <div class="bg-brand-50 border border-brand-200 rounded-3xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                    <i class="isax isax-shield-tick text-6xl text-brand-600"></i>
                </div>
                
                <div class="flex items-start gap-3 mb-5 relative z-10">
                    <i class="isax isax-info-circle text-brand-500 text-xl shrink-0 mt-0.5"></i>
                    <p class="text-sm text-brand-800 font-medium leading-relaxed">
                        Al confirmar, se enviará una rendición por <strong class="font-extrabold"><?= MoneyHelper::format($total) ?></strong> a administración y no podrás registrar más cobros para el día de hoy.
                    </p>
                </div>
                
                <form method="POST" action="<?= url('cobrador/caja/cerrar') ?>" class="relative z-10">
                    <?= csrf_field() ?>
                    <button type="submit"
                            onclick="return confirm('¿Confirmás el cierre de caja por <?= MoneyHelper::formatShort($total) ?>?')"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 px-6 rounded-2xl shadow-xl shadow-brand-500/30 active:scale-95 transition-all flex items-center justify-center gap-2 text-base">
                        <i class="isax isax-lock-1"></i> Confirmar Cierre Diario
                    </button>
                </form>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
