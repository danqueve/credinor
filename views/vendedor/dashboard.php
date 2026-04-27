<?php // views/vendedor/dashboard.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-6 pb-10 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 border border-brand-500 shadow-xl relative overflow-hidden text-white">
        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
            <i class="isax isax-profile-circle text-8xl text-white"></i>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="block text-brand-200 font-bold uppercase tracking-wider text-xs mb-1">Panel de Control · <?= ucfirst(\App\Core\Auth::rol()) ?></span>
                <h1 class="text-3xl font-extrabold tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Hola, <?= e(\App\Core\Auth::user()['nombre']) ?>
                </h1>
                <p class="text-brand-100 font-medium mt-1 text-sm">¿Qué deseas gestionar hoy?</p>
            </div>
            <a href="<?= url('cobrador/agenda') ?>"
               class="shrink-0 inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/20 text-white font-bold px-5 py-3 rounded-2xl transition-all active:scale-95 text-sm backdrop-blur-sm">
                <i class="isax isax-calendar-tick text-lg"></i> Ver mi Agenda
            </a>
        </div>
    </div>

    <!-- KPIs: 4 tarjetas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Cobrado Hoy -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4 lg:col-span-2">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="isax isax-money-tick text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Cobrado Hoy</p>
                <p class="text-2xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;">
                    <?= MoneyHelper::formatShort((float)$cobrado_hoy) ?>
                </p>
            </div>
        </div>

        <!-- Cuotas Hoy -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                <i class="isax isax-task-square text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pendientes Hoy</p>
                <p class="text-2xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;"><?= (int)$cuotas_hoy ?></p>
            </div>
        </div>

        <!-- Cuotas Vencidas -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4 <?= (int)$cuotas_vencidas > 0 ? 'border-red-100' : '' ?>">
            <div class="w-12 h-12 rounded-xl <?= (int)$cuotas_vencidas > 0 ? 'bg-red-50 text-red-500' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center shrink-0">
                <i class="isax isax-warning-2 text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Vencidas</p>
                <p class="text-2xl font-extrabold <?= (int)$cuotas_vencidas > 0 ? 'text-red-500' : 'text-slate-900' ?>" style="font-family:'Outfit',sans-serif;">
                    <?= (int)$cuotas_vencidas ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Acción primaria: Agenda + Nuevo Crédito -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Agenda -->
        <a href="<?= url('cobrador/agenda') ?>"
           class="bg-brand-600 rounded-2xl p-6 border border-brand-500 shadow-sm hover:shadow-lg hover:bg-brand-500 transition-all relative overflow-hidden group flex flex-col gap-2">
            <div class="absolute -right-4 -bottom-4 text-[80px] text-white opacity-10 pointer-events-none">
                <i class="isax isax-calendar-1"></i>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center">
                <i class="isax isax-calendar-tick text-xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-brand-200 uppercase tracking-wider">Operación Principal</p>
                <p class="text-xl font-extrabold text-white tracking-tight mt-0.5">Mi Agenda de Cobros</p>
                <p class="text-xs text-brand-200 mt-1 flex items-center gap-1">
                    <?= (int)$cuotas_hoy + (int)$cuotas_vencidas ?> cuota<?= ((int)$cuotas_hoy + (int)$cuotas_vencidas) !== 1 ? 's' : '' ?> pendiente<?= ((int)$cuotas_hoy + (int)$cuotas_vencidas) !== 1 ? 's' : '' ?>
                    <i class="isax isax-arrow-right-1"></i>
                </p>
            </div>
        </a>

        <!-- Créditos activos -->
        <a href="<?= url('vendedor/creditos') ?>"
           class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg hover:border-brand-200 transition-all relative overflow-hidden group flex flex-col gap-2">
            <div class="absolute -right-4 -bottom-4 text-[80px] text-brand-600 opacity-[0.04] pointer-events-none">
                <i class="isax isax-document-text"></i>
            </div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                <i class="isax isax-document-text text-xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mis Créditos</p>
                <p class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5" style="font-family:'Outfit',sans-serif;">
                    <?= (int)$mis_activos ?> <span class="text-sm font-medium text-slate-400">activos</span>
                </p>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1 font-medium">
                    Ver todos mis créditos <i class="isax isax-arrow-right-1 text-brand-400"></i>
                </p>
            </div>
        </a>
    </div>

    <!-- Accesos Rápidos -->
    <div>
        <h2 class="text-base font-bold text-slate-700 mb-3 flex items-center gap-2">
            <i class="isax isax-flash text-amber-500"></i> Accesos Rápidos
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

            <a href="<?= url('vendedor/clientes/nuevo') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-md hover:border-brand-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-2.5 text-center group">
                <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                    <i class="isax isax-user-add text-xl"></i>
                </div>
                <span class="font-bold text-slate-700 text-sm">Nuevo Cliente</span>
            </a>

            <a href="<?= url('vendedor/clientes') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-md hover:border-brand-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-2.5 text-center group">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                    <i class="isax isax-people text-xl"></i>
                </div>
                <span class="font-bold text-slate-700 text-sm">Clientes</span>
            </a>

            <a href="<?= url('vendedor/creditos/nuevo') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-md hover:border-emerald-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-2.5 text-center group">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                    <i class="isax isax-money-send text-xl"></i>
                </div>
                <span class="font-bold text-slate-700 text-sm">Nuevo Crédito</span>
            </a>

            <a href="<?= url('cobrador/historial') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-2.5 text-center group">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-slate-200 transition-colors">
                    <i class="isax isax-clock text-xl"></i>
                </div>
                <span class="font-bold text-slate-700 text-sm">Mis Cobros</span>
            </a>
        </div>
    </div>

    <!-- Cierre de caja -->
    <a href="<?= url('cobrador/caja') ?>"
       class="flex items-center justify-between bg-slate-900 text-white rounded-2xl p-5 shadow-xl shadow-slate-900/20 border border-slate-700 hover:bg-slate-800 active:scale-[0.99] transition-all">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                <i class="isax isax-briefcase text-xl text-white"></i>
            </div>
            <div>
                <p class="font-bold text-base">Cierre de Caja Diario</p>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Rendir los cobros del día al administrador</p>
            </div>
        </div>
        <i class="isax isax-arrow-right-3 text-slate-400"></i>
    </a>
</div>
