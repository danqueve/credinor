<?php // views/cobrador/dashboard.php
$hoy = date('d/m/Y');
?>
<div class="space-y-6 pb-24 max-w-lg mx-auto">
    <!-- Header App-like -->
    <div class="bg-gradient-to-b from-slate-900 to-slate-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
            <i class="isax isax-calendar-tick text-8xl"></i>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight" style="font-family: 'Outfit', sans-serif;">Mi Agenda</h1>
                    <p class="text-slate-400 font-medium text-sm flex items-center gap-1.5 mt-1">
                        <i class="isax isax-calendar-1"></i> <?= $hoy ?>
                    </p>
                </div>
                
                <div class="text-right bg-white/10 backdrop-blur-sm rounded-2xl p-3 border border-white/10">
                    <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-0.5">Cobrado Hoy</div>
                    <div class="text-2xl font-extrabold text-white" style="font-family: 'Outfit', sans-serif;">
                        $<?= number_format($total_cobrado, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vencidas (Prioridad Alta) -->
    <?php if (!empty($agenda_vencida)): ?>
    <div>
        <div class="flex items-center gap-2 mb-3 px-1">
            <i class="isax isax-danger text-red-500"></i>
            <h2 class="text-sm font-bold text-red-600 uppercase tracking-wider">
                Vencidas (<?= count($agenda_vencida) ?>)
            </h2>
        </div>
        <div class="space-y-3">
            <?php foreach ($agenda_vencida as $c): ?>
            <div class="bg-white rounded-3xl p-4 border border-red-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="font-bold text-slate-800 truncate text-base mb-0.5">
                            <?= e($c['cliente_nombre']) ?>
                        </p>
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md w-fit border border-red-100">
                                <i class="isax isax-calendar-remove text-red-500"></i> Venció <?= date('d/m/Y', strtotime($c['fecha_vencimiento'])) ?>
                            </span>
                            <span class="text-sm font-bold text-slate-700">
                                Saldo: <span class="text-red-600">$<?= number_format($c['saldo'], 0, ',', '.') ?></span>
                            </span>
                        </div>
                    </div>
                    
                    <a href="<?= url('cobrador/pago/' . $c['credito_id'] . '/' . $c['id']) ?>"
                       class="shrink-0 bg-red-500 hover:bg-red-600 text-white w-12 h-12 rounded-2xl flex items-center justify-center shadow-md shadow-red-500/20 transition-colors active:scale-95">
                        <i class="isax isax-wallet-add text-xl"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cobros del día -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center gap-2">
                <i class="isax isax-task-square text-brand-500"></i>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">
                    Para Hoy (<?= count($agenda_hoy) ?>)
                </h2>
            </div>
            <div class="text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-1 rounded-lg border border-slate-200">
                <?= count($agenda_hoy) ?> CLIENTES
            </div>
        </div>

        <?php if (empty($agenda_hoy)): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center py-10 px-6">
                <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-4">
                    <i class="isax isax-verify text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">¡Al día!</h3>
                <p class="text-sm text-slate-500 font-medium">No tienes cobros agendados para la fecha de hoy.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($agenda_hoy as $c): ?>
                <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] active:scale-[0.98] transition-transform">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-800 truncate text-base leading-tight">
                                <?= e($c['cliente_nombre']) ?>
                            </h3>
                            <?php if (!empty($c['domicilio'])): ?>
                                <p class="text-[11px] font-medium text-slate-500 flex items-center gap-1 mt-1 truncate">
                                    <i class="isax isax-location text-slate-400"></i> <?= e($c['domicilio']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($c['telefono']): ?>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $c['telefono']) ?>"
                           target="_blank"
                           class="shrink-0 w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100" title="Contactar por WhatsApp">
                            <i class="isax isax-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Cuota #<?= $c['numero_cuota'] ?></span>
                            <span class="block font-extrabold text-brand-700 text-lg leading-none" style="font-family: 'Outfit', sans-serif;">
                                $<?= number_format($c['saldo'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <a href="<?= url('cobrador/pago/' . $c['credito_id'] . '/' . $c['id']) ?>"
                           class="btn-primary py-2 px-4 shadow-md shadow-brand-500/20 active:scale-95 text-sm">
                            <i class="isax isax-wallet-add mr-1"></i> Cobrar
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Botón Flotante Cierre de Caja (Mobile) -->
    <div class="fixed bottom-6 left-0 right-0 px-4 z-50 md:relative md:bottom-auto md:px-0">
        <div class="max-w-lg mx-auto">
            <a href="<?= url('cobrador/caja') ?>"
               class="bg-slate-900 text-white w-full rounded-2xl py-4 px-6 flex items-center justify-between shadow-xl shadow-slate-900/20 border border-slate-700 active:scale-[0.98] transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <i class="isax isax-briefcase text-xl text-white"></i>
                    </div>
                    <span class="font-bold text-base">Cierre de Caja Diario</span>
                </div>
                <i class="isax isax-arrow-right-3 opacity-50"></i>
            </a>
        </div>
    </div>
</div>
