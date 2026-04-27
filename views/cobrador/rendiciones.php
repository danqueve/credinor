<?php // views/cobrador/rendiciones.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;

$badges = [
    'pendiente' => 'badge-pendiente',
    'confirmada' => 'badge-activo',
    'rechazada' => 'badge-rechazado',
];
?>
<div class="max-w-lg mx-auto space-y-6 pb-24">

    <!-- Header Mobile -->
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-4">
            <a href="<?= url('cobrador/agenda') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-95 shadow-sm">
                <i class="isax isax-arrow-left-2 text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900" style="font-family: 'Outfit', sans-serif;">Mis Rendiciones</h1>
                <p class="text-slate-500 font-medium text-xs">Historial de cierres de caja</p>
            </div>
        </div>
    </div>

    <?php if (empty($rendiciones)): ?>
        <div class="bg-white rounded-3xl p-10 text-center border border-slate-100 shadow-sm">
            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="isax isax-wallet-money text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No hay rendiciones</h3>
            <p class="text-slate-500 text-sm mt-1">Aún no realizaste ningún cierre de caja.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($rendiciones as $r): ?>
            <a href="<?= url('cobrador/rendiciones/' . $r['id']) ?>" 
               class="block bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-brand-300 hover:shadow-lg hover:shadow-brand-500/10 transition-all relative overflow-hidden group">
                
                <div class="absolute right-0 top-0 bottom-0 w-2 bg-gradient-to-b from-brand-400 to-brand-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-slate-800 text-lg">Cierre #<?= $r['id'] ?></span>
                        </div>
                        <p class="text-xs font-medium text-slate-500 flex items-center gap-1.5">
                            <i class="isax isax-calendar-1 text-slate-400"></i> <?= DateHelper::formatoArg($r['fecha']) ?>
                        </p>
                    </div>
                    <span class="<?= $badges[$r['estado']] ?? 'badge' ?> px-2.5 py-1 text-[10px]">
                        <?= ucfirst($r['estado']) ?>
                    </span>
                </div>
                
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100/60 mt-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Declarado</span>
                        <span class="font-extrabold text-slate-800" style="font-family: 'Outfit', sans-serif;">
                            <?= MoneyHelper::format((float)$r['monto_declarado']) ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pagos incluidos</span>
                        <span class="font-bold text-slate-600 text-sm">
                            <?= $r['cantidad_pagos'] ?>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
