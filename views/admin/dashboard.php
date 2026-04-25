<?php // views/admin/dashboard.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-7xl mx-auto space-y-8 pb-10">

    <!-- HEADER MINIMALISTA -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/60 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Vista General</h1>
            <p class="text-sm font-medium text-slate-500 mt-2 flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">
                    <i class="isax isax-calendar-1"></i>
                    <?= date('d M, Y') ?>
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-brand-50 text-brand-600">
                    <i class="isax isax-building"></i>
                    <?= e($sucursalNombre ?? 'Global') ?>
                </span>
            </p>
        </div>
        
        <?php if ($kpis['creditos_pendientes'] > 0): ?>
        <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>" 
           class="inline-flex items-center gap-3 bg-white border border-amber-200 rounded-2xl p-2 pr-5 shadow-sm hover:shadow-md hover:border-amber-300 transition-all group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                <i class="isax isax-notification-bing text-xl"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Requiere acción</p>
                <p class="text-sm text-slate-700 font-semibold"><?= $kpis['creditos_pendientes'] ?> créditos por autorizar</p>
            </div>
            <i class="isax isax-arrow-right-1 text-slate-400 group-hover:translate-x-1 transition-transform ml-2"></i>
        </a>
        <?php endif; ?>
    </div>

    <!-- MAIN KPIs (Estilo Clean SaaS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- KPI 1: Cobrado Hoy -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 opacity-0 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-wallet-add text-6xl text-brand-600"></i>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4">
                <i class="isax isax-wallet-add text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Cobrado hoy</p>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($kpis['cobrado_hoy']) ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-slate-400">
                <span class="text-brand-600 bg-brand-50 px-2 py-0.5 rounded-md"><?= number_format($kpis['pagos_hoy']) ?></span> pagos registrados
            </div>
        </div>

        <!-- KPI 2: Cartera Total -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                <i class="isax isax-bank text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Capital Activo</p>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($kpis['cartera_total']) ?>
            </h3>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-slate-400">
                <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md"><?= number_format($kpis['creditos_activos']) ?></span> créditos vivos
            </div>
        </div>

        <!-- KPI 3: Mora Pendiente -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4">
                <i class="isax isax-graph text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Mora Pendiente</p>
            <h3 class="text-3xl font-extrabold text-rose-600 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= MoneyHelper::formatShort($kpis['mora_pendiente']) ?>
            </h3>
            <a href="<?= url('admin/reportes/mora') ?>" class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors">
                Ver detalle de mora <i class="isax isax-arrow-right-1"></i>
            </a>
        </div>

        <!-- KPI 4: Rendiciones -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow">
            <div class="w-12 h-12 rounded-2xl <?= $kpis['rendiciones_pendientes'] > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center mb-4">
                <i class="isax isax-money-tick text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-slate-500 mb-1">Rendiciones</p>
            <h3 class="text-3xl font-extrabold <?= $kpis['rendiciones_pendientes'] > 0 ? 'text-slate-900' : 'text-slate-400' ?> tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= number_format($kpis['rendiciones_pendientes']) ?>
            </h3>
            <?php if ($kpis['rendiciones_pendientes'] > 0): ?>
                <a href="<?= url('admin/rendiciones?estado=pendiente') ?>" class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                    Confirmar pendientes <i class="isax isax-arrow-right-1"></i>
                </a>
            <?php else: ?>
                <div class="mt-4 flex items-center gap-2 text-xs font-medium text-slate-400">
                    <i class="isax isax-tick-circle text-emerald-500"></i> Al día
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- SECCIÓN SECUNDARIA -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
        
        <!-- Atajos Elegantes (2 Columnas) -->
        <div class="lg:col-span-2 space-y-5">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-lg font-bold text-slate-800">Centro de Operaciones</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Acción 1 -->
                <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>" 
                   class="group flex items-center gap-5 p-5 rounded-3xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 text-brand-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="isax isax-task-square text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800 mb-0.5">Autorizar Créditos</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">Revisa y aprueba solicitudes pendientes</p>
                    </div>
                </a>

                <!-- Acción 2 -->
                <a href="<?= url('admin/rendiciones?estado=pendiente') ?>" 
                   class="group flex items-center gap-5 p-5 rounded-3xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="isax isax-card-tick text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800 mb-0.5">Validar Rendiciones</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">Control de cajas y cierres de cobradores</p>
                    </div>
                </a>

                <!-- Acción 3 -->
                <a href="<?= url('admin/reportes/cartera') ?>" 
                   class="group flex items-center gap-5 p-5 rounded-3xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="isax isax-chart text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800 mb-0.5">Reporte de Cartera</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">Análisis profundo del capital activo</p>
                    </div>
                </a>

                <!-- Acción 4 -->
                <a href="<?= url('admin/reportes/cobradores') ?>" 
                   class="group flex items-center gap-5 p-5 rounded-3xl bg-white border border-slate-100 hover:border-brand-200 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 shrink-0 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="isax isax-profile-2user text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800 mb-0.5">Productividad</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">Métricas de rendimiento por cobrador</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Panel Lateral (Actividad Reciente) -->
        <div class="space-y-5">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-lg font-bold text-slate-800">Cajas Recientes</h2>
                <?php if (!empty($rendicionesPendientes)): ?>
                    <a href="<?= url('admin/rendiciones') ?>" class="text-xs font-bold text-brand-600 hover:text-brand-800">Ver todas</a>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
                <?php if (empty($rendicionesPendientes)): ?>
                    <div class="p-10 text-center flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mb-4">
                            <i class="isax isax-coffee text-3xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Todo tranquilo</p>
                        <p class="text-xs text-slate-400 mt-1">No hay cajas pendientes por confirmar.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-slate-50">
                        <?php foreach ($rendicionesPendientes as $r): ?>
                        <li>
                            <a href="<?= url('admin/rendiciones/' . $r['id']) ?>" class="flex items-center gap-4 p-4 hover:bg-slate-50/80 transition-colors group">
                                <!-- Avatar -->
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm shrink-0 border border-slate-200">
                                    <?= mb_strtoupper(mb_substr($r['cobrador_nombre'], 0, 1)) ?>
                                </div>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-brand-600 transition-colors">
                                        <?= e($r['cobrador_nombre']) ?>
                                    </p>
                                    <p class="text-xs text-slate-400 font-medium">
                                        <?= date('d M', strtotime($r['fecha'])) ?>
                                    </p>
                                </div>
                                <!-- Monto -->
                                <div class="text-right">
                                    <p class="text-sm font-bold text-slate-900" style="font-family: 'Outfit', sans-serif;">
                                        <?= MoneyHelper::formatShort((float)$r['monto_declarado']) ?>
                                    </p>
                                    <span class="inline-block mt-1 text-[9px] px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded font-bold uppercase tracking-widest">
                                        Pendiente
                                    </span>
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
