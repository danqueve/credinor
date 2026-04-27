<?php // views/vendedor/dashboard.php
use App\Helpers\MoneyHelper;
?>
<div class="space-y-5 pb-10 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="relative overflow-hidden rounded-3xl p-8 text-white shadow-2xl shadow-indigo-900/30"
         style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #7c3aed 100%);">
        <div class="absolute inset-0 pointer-events-none opacity-[0.07]"
             style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -right-2 top-12 w-28 h-28 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 text-indigo-200 font-bold uppercase tracking-widest text-[10px] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-300 inline-block"></span>
                    Panel de Control &middot; <?= ucfirst(\App\Core\Auth::rol()) ?>
                </span>
                <h1 class="text-4xl font-extrabold tracking-tight leading-none" style="font-family:'Outfit',sans-serif;">
                    Hola, <?= e(\App\Core\Auth::user()['nombre']) ?> 👋
                </h1>
                <p class="text-indigo-200 font-medium mt-2 text-sm">¿Qué deseas gestionar hoy?</p>
            </div>
            <a href="<?= url('cobrador/agenda') ?>"
               class="shrink-0 inline-flex items-center gap-2 bg-white text-indigo-700 font-bold px-5 py-3 rounded-2xl transition-all hover:bg-indigo-50 active:scale-95 text-sm shadow-lg shadow-indigo-900/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008z"/>
                </svg>
                Ver mi Agenda
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Cobrado Hoy -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4 sm:col-span-1">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 text-emerald-600"
                 style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Cobrado Hoy</p>
                <p class="text-2xl font-extrabold text-slate-900 leading-none" style="font-family:'Outfit',sans-serif;">
                    <?= MoneyHelper::formatShort((float)$cobrado_hoy) ?>
                </p>
            </div>
        </div>

        <!-- Cuotas Pendientes Hoy -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 text-indigo-600"
                 style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Pendientes Hoy</p>
                <p class="text-2xl font-extrabold text-slate-900 leading-none" style="font-family:'Outfit',sans-serif;">
                    <?= (int)$cuotas_hoy ?>
                </p>
            </div>
        </div>

        <!-- Cuotas Vencidas -->
        <div class="rounded-2xl p-5 border shadow-sm flex items-center gap-4
            <?= (int)$cuotas_vencidas > 0 ? 'bg-red-50 border-red-200' : 'bg-white border-slate-100' ?>">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0
                <?= (int)$cuotas_vencidas > 0 ? 'text-red-600' : 'text-slate-400' ?>"
                 style="background: <?= (int)$cuotas_vencidas > 0 ? 'linear-gradient(135deg,#fee2e2,#fecaca)' : 'linear-gradient(135deg,#f1f5f9,#e2e8f0)' ?>;">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider <?= (int)$cuotas_vencidas > 0 ? 'text-red-400' : 'text-slate-400' ?> mb-0.5">Vencidas</p>
                <p class="text-2xl font-extrabold leading-none <?= (int)$cuotas_vencidas > 0 ? 'text-red-600' : 'text-slate-900' ?>" style="font-family:'Outfit',sans-serif;">
                    <?= (int)$cuotas_vencidas ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Tarjetas principales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Agenda de cobros -->
        <a href="<?= url('cobrador/agenda') ?>"
           class="relative overflow-hidden rounded-2xl p-6 flex flex-col gap-3 transition-all hover:scale-[1.01] active:scale-[0.99] group"
           style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute right-8 bottom-8 w-16 h-16 rounded-full bg-white/5 pointer-events-none"></div>
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest">Operación Principal</p>
                <p class="text-xl font-extrabold text-white tracking-tight mt-1" style="font-family:'Outfit',sans-serif;">Mi Agenda de Cobros</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                        <?= (int)$cuotas_hoy + (int)$cuotas_vencidas ?> cuota<?= ((int)$cuotas_hoy + (int)$cuotas_vencidas) !== 1 ? 's' : '' ?> pendiente<?= ((int)$cuotas_hoy + (int)$cuotas_vencidas) !== 1 ? 's' : '' ?>
                    </span>
                    <svg class="w-4 h-4 text-indigo-200 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Mis Créditos -->
        <a href="<?= url('vendedor/creditos') ?>"
           class="relative overflow-hidden rounded-2xl p-6 bg-white border border-slate-100 shadow-sm flex flex-col gap-3 transition-all hover:scale-[1.01] hover:shadow-md hover:border-indigo-200 active:scale-[0.99] group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-indigo-50 pointer-events-none"></div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mis Créditos</p>
                <p class="text-xl font-extrabold text-slate-900 tracking-tight mt-1" style="font-family:'Outfit',sans-serif;">
                    <?= (int)$mis_activos ?> <span class="text-base font-medium text-slate-400">activos</span>
                </p>
                <div class="flex items-center gap-1 mt-2 text-xs text-slate-500 font-medium">
                    Ver todos mis créditos
                    <svg class="w-3.5 h-3.5 text-indigo-400 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Accesos Rápidos -->
    <div>
        <h2 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
            Accesos Rápidos
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

            <a href="<?= url('vendedor/clientes/nuevo') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform"
                     style="background: linear-gradient(135deg,#e0e7ff,#c7d2fe);">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-700 text-sm">Nuevo Cliente</span>
            </a>

            <a href="<?= url('vendedor/clientes') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-violet-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-violet-600 group-hover:scale-110 transition-transform"
                     style="background: linear-gradient(135deg,#ede9fe,#ddd6fe);">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-700 text-sm">Clientes</span>
            </a>

            <a href="<?= url('vendedor/creditos/nuevo') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-emerald-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform"
                     style="background: linear-gradient(135deg,#d1fae5,#a7f3d0);">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-700 text-sm">Nuevo Crédito</span>
            </a>

            <a href="<?= url('cobrador/historial') ?>"
               class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-sky-200 hover:-translate-y-0.5 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-sky-600 group-hover:scale-110 transition-transform"
                     style="background: linear-gradient(135deg,#e0f2fe,#bae6fd);">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-slate-700 text-sm">Mis Cobros</span>
            </a>
        </div>
    </div>

    <!-- Cierre de Caja -->
    <a href="<?= url('cobrador/caja') ?>"
       class="flex items-center justify-between rounded-2xl p-5 border border-slate-700 shadow-lg hover:bg-slate-800 active:scale-[0.99] transition-all group"
       style="background: linear-gradient(135deg,#0f172a,#1e293b);">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-white shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-base text-white leading-none">Cierre de Caja Diario</p>
                <p class="text-xs text-slate-400 font-medium mt-1">Rendir los cobros del día al administrador</p>
            </div>
        </div>
        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-slate-300 group-hover:bg-white/20 transition-colors shrink-0">
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </div>
    </a>

</div>
