<?php // views/admin/dashboard.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-7xl mx-auto space-y-7 pb-10">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Vista General</h1>
            <p class="text-sm text-slate-400 mt-1 flex items-center gap-2 font-medium">
                <i class="isax isax-calendar-1 text-slate-300"></i>
                <?= date('d \d\e M Y') ?>
                <span class="text-slate-200">·</span>
                <i class="isax isax-building text-slate-300"></i>
                <?= e($sucursalNombre ?? 'Global') ?>
            </p>
        </div>

        <?php if ($kpis['creditos_pendientes'] > 0): ?>
        <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>"
           class="inline-flex items-center gap-3 bg-white border border-amber-200 rounded-2xl px-4 py-2.5 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group text-sm font-semibold text-amber-700">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <?= $kpis['creditos_pendientes'] ?> crédito<?= $kpis['creditos_pendientes'] !== 1 ? 's' : '' ?> pendiente<?= $kpis['creditos_pendientes'] !== 1 ? 's' : '' ?>
            <i class="isax isax-arrow-right-1 text-amber-400 group-hover:translate-x-1 transition-transform"></i>
        </a>
        <?php endif; ?>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Cobrado hoy -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-wallet-add text-xl"></i>
                </div>
                <span class="badge-up text-xs">
                    <i class="isax isax-arrow-up-3" style="font-size:10px;"></i>
                    Hoy
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Cobrado hoy</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($kpis['cobrado_hoy']) ?>
            </p>
            <p class="text-xs text-slate-400 mt-2 font-medium">
                <span class="text-slate-600 font-semibold"><?= number_format($kpis['pagos_hoy']) ?></span> pagos registrados
            </p>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-brand-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-wallet-add"></i>
            </div>
        </div>

        <!-- Capital activo -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-bank text-xl"></i>
                </div>
                <span class="badge-up text-xs">
                    <i class="isax isax-chart-1" style="font-size:10px;"></i>
                    Activo
                </span>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Capital en cartera</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($kpis['cartera_total']) ?>
            </p>
            <p class="text-xs text-slate-400 mt-2 font-medium">
                <span class="text-slate-600 font-semibold"><?= number_format($kpis['creditos_activos']) ?></span> créditos vigentes
            </p>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-blue-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-bank"></i>
            </div>
        </div>

        <!-- Mora -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="isax isax-graph text-xl"></i>
                </div>
                <?php if ((float)($kpis['mora_pendiente'] ?? 0) > 0): ?>
                <span class="badge-down text-xs">
                    <i class="isax isax-warning-2" style="font-size:10px;"></i>
                    Alerta
                </span>
                <?php else: ?>
                <span class="badge-up text-xs">
                    <i class="isax isax-tick-circle" style="font-size:10px;"></i>
                    OK
                </span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Mora pendiente</p>
            <p class="text-2xl font-bold <?= (float)($kpis['mora_pendiente'] ?? 0) > 0 ? 'text-rose-600' : 'text-slate-900' ?> tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= MoneyHelper::formatShort($kpis['mora_pendiente']) ?>
            </p>
            <a href="<?= url('admin/reportes/mora') ?>" class="text-xs text-rose-500 hover:text-rose-700 font-semibold mt-2 inline-flex items-center gap-1 transition-colors">
                Ver detalle <i class="isax isax-arrow-right-1" style="font-size:10px;"></i>
            </a>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-rose-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-graph"></i>
            </div>
        </div>

        <!-- Rendiciones -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-10 h-10 rounded-xl <?= $kpis['rendiciones_pendientes'] > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center shrink-0">
                    <i class="isax isax-money-tick text-xl"></i>
                </div>
                <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
                <span class="badge-down text-xs">
                    <i class="isax isax-clock" style="font-size:10px;"></i>
                    Pendiente
                </span>
                <?php else: ?>
                <span class="badge-up text-xs">
                    <i class="isax isax-tick-circle" style="font-size:10px;"></i>
                    Al día
                </span>
                <?php endif; ?>
            </div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Rendiciones</p>
            <p class="text-2xl font-bold text-slate-900 tracking-tight" style="font-family:'Outfit',sans-serif;">
                <?= number_format($kpis['rendiciones_pendientes']) ?>
                <span class="text-sm font-medium text-slate-400">por confirmar</span>
            </p>
            <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
            <a href="<?= url('admin/rendiciones?estado=pendiente') ?>" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold mt-2 inline-flex items-center gap-1 transition-colors">
                Confirmar <i class="isax isax-arrow-right-1" style="font-size:10px;"></i>
            </a>
            <?php else: ?>
            <p class="text-xs text-slate-400 mt-2 flex items-center gap-1 font-medium">
                <i class="isax isax-tick-circle text-emerald-400" style="font-size:12px;"></i> Sin pendientes
            </p>
            <?php endif; ?>
            <div class="absolute -right-4 -bottom-4 text-[80px] text-emerald-600 opacity-[0.035] pointer-events-none group-hover:opacity-[0.06] transition-opacity">
                <i class="isax isax-money-tick"></i>
            </div>
        </div>

    </div>

    <!-- SECONDARY ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Acciones rápidas -->
        <div class="lg:col-span-2 space-y-4">
            <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider px-1">Acciones rápidas</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>"
                   class="group flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition-all">
                        <i class="isax isax-task-square text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Autorizar Créditos</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Revisa solicitudes pendientes</p>
                    </div>
                    <i class="isax isax-arrow-right-1 text-slate-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="<?= url('admin/rendiciones?estado=pendiente') ?>"
                   class="group flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 hover:border-emerald-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="isax isax-card-tick text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Validar Rendiciones</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Control de cajas del día</p>
                    </div>
                    <i class="isax isax-arrow-right-1 text-slate-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="<?= url('admin/reportes/cartera') ?>"
                   class="group flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 hover:border-blue-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="isax isax-chart text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Reporte de Cartera</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Análisis del capital activo</p>
                    </div>
                    <i class="isax isax-arrow-right-1 text-slate-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>

                <a href="<?= url('admin/reportes/cobradores') ?>"
                   class="group flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 hover:border-violet-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 shrink-0 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-all">
                        <i class="isax isax-profile-2user text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">Productividad</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Métricas por cobrador</p>
                    </div>
                    <i class="isax isax-arrow-right-1 text-slate-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>

            </div>
        </div>

        <!-- Cajas recientes -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Cajas recientes</h2>
                <?php if (!empty($rendicionesPendientes)): ?>
                <a href="<?= url('admin/rendiciones') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-800 transition-colors">Ver todas →</a>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <?php if (empty($rendicionesPendientes)): ?>
                <div class="p-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3">
                        <i class="isax isax-coffee text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600">Todo tranquilo</p>
                    <p class="text-xs text-slate-400 mt-1">Sin cajas pendientes.</p>
                </div>
                <?php else: ?>
                <ul>
                    <?php foreach ($rendicionesPendientes as $r): ?>
                    <li class="border-b border-slate-50 last:border-0">
                        <a href="<?= url('admin/rendiciones/' . $r['id']) ?>"
                           class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50/80 transition-colors group">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0">
                                <?= mb_strtoupper(mb_substr($r['cobrador_nombre'], 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800 truncate group-hover:text-brand-600 transition-colors">
                                    <?= e($r['cobrador_nombre']) ?>
                                </p>
                                <p class="text-xs text-slate-400 font-medium"><?= date('d M', strtotime($r['fecha'])) ?></p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-slate-900" style="font-family:'Outfit',sans-serif;">
                                    <?= MoneyHelper::formatShort((float)$r['monto_declarado']) ?>
                                </p>
                                <span class="badge badge-pendiente text-[9px]">pendiente</span>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
