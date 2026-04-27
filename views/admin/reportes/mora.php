<?php // views/admin/reportes/mora.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Reporte de Mora</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Análisis de atrasos y recargos pendientes o cobrados</p>
        </div>
    </div>

    <!-- Totales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-red-50/50 rounded-3xl p-6 border border-red-100 shadow-sm relative overflow-hidden group hover:border-red-200 transition-colors">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-danger text-8xl text-red-600"></i>
            </div>
            <div class="relative z-10">
                <span class="block text-xs font-bold uppercase tracking-wider text-red-500 mb-2">Mora Acumulada Total</span>
                <span class="block text-4xl font-extrabold text-red-600" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::formatShort($totalMora) ?>
                </span>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-3xl p-6 border border-red-500 shadow-lg relative overflow-hidden group hover:shadow-red-500/20 transition-all">
            <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="isax isax-warning-2 text-8xl text-white"></i>
            </div>
            <div class="relative z-10">
                <span class="block text-xs font-bold uppercase tracking-wider text-red-200 mb-2">Mora Pendiente de Cobro</span>
                <span class="block text-4xl font-extrabold text-white" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::formatShort($totalPend) ?>
                </span>
            </div>
        </div>
        
        <div class="bg-emerald-50/50 rounded-3xl p-6 border border-emerald-100 shadow-sm relative overflow-hidden group hover:border-emerald-200 transition-colors">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-tick-circle text-8xl text-emerald-600"></i>
            </div>
            <div class="relative z-10">
                <span class="block text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2">Mora Ya Cobrada</span>
                <span class="block text-4xl font-extrabold text-emerald-700" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::formatShort($totalMora - $totalPend) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Por cobrador -->
        <?php if (!empty($porCobrador)): ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="p-6 border-b border-slate-100/80">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="isax isax-profile-2user text-brand-500"></i> Desglose por Cobrador
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cobrador</th>
                            <th class="px-6 py-4 text-center" title="Cantidad de créditos con mora">Cant.</th>
                            <th class="px-6 py-4 text-right">Pendiente</th>
                            <th class="px-6 py-4">Recuperación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($porCobrador as $c): ?>
                        <?php
                            $pct = $c['mora_total'] > 0
                                ? round($c['mora_cobrada'] / $c['mora_total'] * 100)
                                : 0;
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs shrink-0">
                                        <?= strtoupper(substr($c['cobrador_nombre'], 0, 1)) ?>
                                    </div>
                                    <?= e($c['cobrador_nombre']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-medium text-slate-500">
                                <?= $c['total_creditos'] ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                <?= MoneyHelper::formatShort((float)$c['mora_pendiente']) ?>
                            </td>
                            <td class="px-6 py-4 w-32">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-100 rounded-full h-1.5 shadow-inner overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 h-full rounded-full transition-all" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold <?= $pct > 50 ? 'text-emerald-600' : 'text-slate-400' ?>"><?= $pct ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Top deudores -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="p-6 border-b border-slate-100/80">
                <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="isax isax-ranking text-brand-500"></i> Top 50 — Mayor mora pendiente
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-4 text-center">#</th>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 text-right">Pendiente</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($topDeudores as $i => $d): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4 text-center">
                                <span class="text-xs font-bold text-slate-400"><?= $i + 1 ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block"><?= e($d['nombre']) ?></span>
                                <span class="text-[10px] font-medium text-slate-500 uppercase flex items-center gap-1 mt-0.5">
                                    <i class="isax isax-user"></i> <?= e($d['cobrador_nombre'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                <?= MoneyHelper::formatShort((float)$d['mora_pendiente']) ?>
                                <span class="block text-[10px] font-medium text-slate-400 font-normal mt-0.5">Acumulada: <?= MoneyHelper::formatShort((float)$d['mora_acumulada']) ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('creditos/' . $d['credito_id']) ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                    <i class="isax isax-arrow-right-3"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($topDeudores)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 font-medium">No hay créditos con mora registrada actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
