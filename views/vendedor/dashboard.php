<?php // views/vendedor/dashboard.php ?>
<div class="space-y-6 pb-10 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 border border-brand-500 shadow-xl relative overflow-hidden text-white flex items-center justify-between">
        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
            <i class="isax isax-profile-circle text-8xl text-white"></i>
        </div>
        
        <div class="relative z-10">
            <span class="block text-brand-200 font-bold uppercase tracking-wider text-sm mb-1">Panel de Control del Vendedor</span>
            <div class="flex items-center gap-3">
                <h1 class="text-4xl font-extrabold tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Hola, <?= e(\App\Core\Auth::user()['nombre']) ?>
                </h1>
                <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-xl">
                    👋
                </div>
            </div>
            <p class="text-brand-100 font-medium mt-2">¿Qué deseas gestionar hoy?</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-amber-200 transition-colors">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-clock text-6xl text-amber-600"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Créditos Pendientes</span>
                    <span class="block text-4xl font-extrabold text-amber-600" style="font-family: 'Outfit', sans-serif;">
                        <?= $mis_pendientes ?>
                    </span>
                    <span class="block text-xs font-medium text-amber-600/70 mt-1">Esperando autorización de caja</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <i class="isax isax-timer-1 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group hover:border-emerald-200 transition-colors">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="isax isax-verify text-6xl text-emerald-600"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Créditos Activos</span>
                    <span class="block text-4xl font-extrabold text-emerald-600" style="font-family: 'Outfit', sans-serif;">
                        <?= $mis_activos ?>
                    </span>
                    <span class="block text-xs font-medium text-emerald-600/70 mt-1">Aprobados y en curso</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                    <i class="isax isax-money-tick text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div>
        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2" style="font-family: 'Outfit', sans-serif;">
            <i class="isax isax-flash text-amber-500"></i> Accesos Rápidos
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="<?= url('vendedor/clientes/nuevo') ?>"
               class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg hover:border-brand-200 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-14 h-14 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-brand-100 transition-all">
                    <i class="isax isax-user-add text-2xl"></i>
                </div>
                <span class="font-bold text-slate-800">Nuevo Cliente</span>
            </a>
            
            <a href="<?= url('vendedor/clientes') ?>"
               class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg hover:border-brand-200 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-3 text-center group">
                <div class="w-14 h-14 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center group-hover:scale-110 group-hover:text-brand-600 group-hover:bg-brand-50 transition-all">
                    <i class="isax isax-people text-2xl"></i>
                </div>
                <span class="font-bold text-slate-800">Directorio</span>
            </a>

            <a href="<?= url('vendedor/creditos/nuevo') ?>"
               class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg hover:border-emerald-200 hover:-translate-y-1 transition-all flex flex-col items-center justify-center gap-3 text-center group sm:col-span-2">
                <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-100 transition-all">
                    <i class="isax isax-money-send text-2xl"></i>
                </div>
                <span class="font-bold text-slate-800">Nueva Solicitud de Crédito</span>
            </a>
        </div>
    </div>
</div>
