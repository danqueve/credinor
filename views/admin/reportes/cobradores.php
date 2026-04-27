<?php // views/admin/reportes/cobradores.php
use App\Helpers\MoneyHelper;

$periodos = ['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes'];
$totalCobrado = array_sum(array_column($cobradores, 'total_cobrado'));
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Rendimiento de Cobradores</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                Análisis de cobros desde <strong><?= date('d/m/Y', strtotime($desde)) ?></strong> hasta hoy
            </p>
        </div>
        
        <!-- Selector período -->
        <div class="bg-white rounded-xl p-1 border border-slate-200 shadow-sm inline-flex">
            <?php foreach ($periodos as $key => $label): ?>
                <a href="?periodo=<?= $key ?>"
                   class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= $periodo === $key ? 'bg-slate-900 text-white shadow' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- KPI Principal -->
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 border border-brand-500 shadow-xl relative overflow-hidden text-white flex items-center justify-between">
        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
            <i class="isax isax-wallet-money text-8xl text-white"></i>
        </div>
        
        <div class="relative z-10">
            <span class="block text-brand-200 font-bold uppercase tracking-wider text-sm mb-1">Recaudación Total del Período</span>
            <div class="flex items-baseline gap-3">
                <span class="text-5xl font-extrabold tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::format($totalCobrado) ?>
                </span>
            </div>
        </div>
        
        <div class="hidden sm:flex relative z-10 w-16 h-16 rounded-full bg-white/20 backdrop-blur-md items-center justify-center border border-white/30">
            <i class="isax isax-chart-2 text-3xl text-white"></i>
        </div>
    </div>

    <!-- Ranking -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($cobradores as $i => $c): ?>
        <?php $pct = $totalCobrado > 0 ? round((float)$c['total_cobrado'] / $totalCobrado * 100, 1) : 0; ?>
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-brand-200 transition-colors">
            
            <?php if ($i === 0 && $totalCobrado > 0): ?>
                <div class="absolute top-0 right-0">
                    <div class="bg-amber-100 text-amber-600 text-[10px] font-bold uppercase tracking-wider py-1 px-3 rounded-bl-xl border-b border-l border-amber-200 flex items-center gap-1">
                        <i class="isax isax-cup"></i> Mejor Rendimiento
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-full <?= $i === 0 ? 'bg-amber-50 text-amber-500 border-2 border-amber-200' : 'bg-brand-50 text-brand-600 border border-brand-100' ?> flex items-center justify-center font-bold text-xl shrink-0">
                    <?= mb_strtoupper(mb_substr($c['cobrador_nombre'], 0, 1)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-extrabold text-lg text-slate-800 truncate" style="font-family: 'Outfit', sans-serif;">
                        <?= e($c['cobrador_nombre']) ?>
                    </p>
                    <p class="text-xs font-bold text-slate-400 mt-0.5">Aporte al total: <span class="text-brand-600"><?= $pct ?>%</span></p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-2 mb-6 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-1000 <?= $i === 0 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-brand-400 to-brand-600' ?>"
                     style="width:<?= $pct ?>%"></div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1">
                        <i class="isax isax-card-receive"></i> Cobrado
                    </p>
                    <p class="font-bold text-slate-800 text-lg"><?= MoneyHelper::formatShort((float)$c['total_cobrado']) ?></p>
                </div>
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1">
                        <i class="isax isax-document-copy"></i> Pagos
                    </p>
                    <p class="font-bold text-slate-800 text-lg"><?= number_format((int)$c['total_pagos']) ?></p>
                </div>
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1">
                        <i class="isax isax-safe-home"></i> Rendido
                    </p>
                    <p class="font-bold <?= ((float)$c['total_rendido'] < (float)$c['total_cobrado']) ? 'text-amber-600' : 'text-emerald-600' ?> text-lg">
                        <?= MoneyHelper::formatShort((float)$c['total_rendido']) ?>
                    </p>
                </div>
                <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1">
                        <i class="isax isax-receipt-item"></i> Recibos
                    </p>
                    <p class="font-bold text-slate-800 text-lg"><?= (int)$c['rendiciones'] ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($cobradores)): ?>
            <div class="col-span-1 md:col-span-2 xl:col-span-3 bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
                <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                    <i class="isax isax-empty-wallet text-4xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">No hay cobros</h3>
                <p class="text-slate-500 font-medium">No se registraron cobros en el período seleccionado.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Histórico 30 días -->
    <?php if (!empty($historicoDias)): ?>
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="flex items-center gap-2 mb-6">
            <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 shrink-0">
                <i class="isax isax-activity text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800" style="font-family: 'Outfit', sans-serif;">Actividad de Cobros</h2>
                <p class="text-xs text-slate-500 font-medium">Evolución diaria durante los últimos 30 días</p>
            </div>
        </div>
        
        <div class="overflow-x-auto pb-2">
            <div class="flex items-end gap-1.5 h-40 min-w-max px-2">
                <?php
                $maxDia = max(array_column($historicoDias, 'total')) ?: 1;
                foreach ($historicoDias as $dia):
                    $pctH = round((float)$dia['total'] / $maxDia * 100);
                    $esHoy = $dia['fecha'] === date('Y-m-d');
                ?>
                <div class="flex flex-col items-center gap-2 group relative flex-1 min-w-[24px]">
                    <!-- Tooltip -->
                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs font-bold py-1.5 px-2.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10 shadow-xl after:content-[''] after:absolute after:top-full after:left-1/2 after:-translate-x-1/2 after:border-4 after:border-transparent after:border-t-slate-800">
                        <?= MoneyHelper::formatShort((float)$dia['total']) ?>
                        <span class="block text-[10px] text-slate-300 font-medium text-center mt-0.5"><?= date('d/m/Y', strtotime($dia['fecha'])) ?></span>
                    </div>
                    
                    <div class="w-full max-w-[32px] <?= $esHoy ? 'bg-gradient-to-t from-brand-600 to-brand-400 shadow-md shadow-brand-500/20' : 'bg-slate-200 group-hover:bg-brand-300' ?> rounded-t-md transition-all cursor-pointer relative overflow-hidden"
                         style="height:<?= max(4, $pctH) ?>%">
                        <?php if ($esHoy): ?>
                            <div class="absolute top-0 left-0 w-full h-1 bg-white/40"></div>
                        <?php endif; ?>
                    </div>
                    <div class="text-[10px] font-bold <?= $esHoy ? 'text-brand-600' : 'text-slate-400' ?> text-center">
                        <?= date('d', strtotime($dia['fecha'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
