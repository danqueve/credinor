<?php // views/admin/creditos.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;

$tabsEstado = [
    'pendiente_autorizacion' => ['label' => 'Pendiente', 'badge' => 'badge-pendiente'],
    'activo'                 => ['label' => 'Activo',    'badge' => 'badge-activo'],
    'finalizado'             => ['label' => 'Finalizado','badge' => 'badge-finalizado'],
    'rechazado'              => ['label' => 'Rechazado', 'badge' => 'badge-rechazado'],
];

$tabsFrecuencia = [
    ''          => ['label' => 'Agenda',    'icon' => 'isax-calendar-1'],
    'diaria'    => ['label' => 'Diario',    'icon' => 'isax-sun-1'],
    'semanal'   => ['label' => 'Semanal',   'icon' => 'isax-calendar-tick'],
    'quincenal' => ['label' => 'Quincenal', 'icon' => 'isax-calendar-2'],
    'mensual'   => ['label' => 'Mensual',   'icon' => 'isax-calendar-remove'],
];
?>
<div class="space-y-6 pb-10">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Créditos</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Gestión global de créditos de todas las sucursales</p>
        </div>
        <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary shrink-0 shadow-md shadow-brand-500/20">
            <i class="isax isax-add"></i> Nuevo Crédito
        </a>
    </div>

    <!-- Stats -->
    <?php if (!empty($stats)): ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                <i class="isax isax-document-text text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Activos</p>
                <p class="text-2xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;"><?= (int)$stats['activos'] ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="isax isax-money-send text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Prestado</p>
                <p class="text-2xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;"><?= MoneyHelper::formatShort((float)$stats['total_prestado']) ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <i class="isax isax-coin text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">A Recuperar</p>
                <p class="text-2xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;"><?= MoneyHelper::formatShort((float)$stats['total_a_devolver']) ?></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl <?= (float)$stats['mora_pendiente'] > 0 ? 'bg-red-50 text-red-500' : 'bg-slate-50 text-slate-400' ?> flex items-center justify-center shrink-0">
                <i class="isax isax-warning-2 text-xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Mora Pendiente</p>
                <p class="text-2xl font-extrabold <?= (float)$stats['mora_pendiente'] > 0 ? 'text-red-500' : 'text-slate-900' ?>" style="font-family:'Outfit',sans-serif;">
                    <?= MoneyHelper::formatShort((float)$stats['mora_pendiente']) ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alerta pendientes de autorización -->
    <?php if (!empty($stats) && (int)$stats['pendientes'] > 0): ?>
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3">
        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
            <i class="isax isax-clock text-base"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-amber-800">
                <?= (int)$stats['pendientes'] ?> crédito<?= (int)$stats['pendientes'] !== 1 ? 's' : '' ?> pendiente<?= (int)$stats['pendientes'] !== 1 ? 's' : '' ?> de autorización
            </p>
        </div>
        <a href="<?= url('admin/creditos?pendientes=1') ?>"
           class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 transition-all">
            <i class="isax isax-tick-circle"></i> Ir a autorizar
        </a>
    </div>
    <?php endif; ?>

    <!-- Buscador + Tabs -->
    <div x-data="busquedaCreditosAdmin()" class="flex flex-col gap-3">

        <!-- Buscador en tiempo real -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="isax isax-search-normal-1 text-slate-400"></i>
            </div>
            <input type="text"
                   x-model="q"
                   placeholder="Buscar por cliente, DNI, sucursal o cobrador..."
                   class="form-input pl-10 pr-10 w-full bg-white border-slate-200 focus:border-brand-500">
            <template x-if="q">
                <button type="button" @click="q = ''"
                        class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-slate-700">
                    <i class="isax isax-close-circle text-lg"></i>
                </button>
            </template>
        </div>

        <!-- Tabs por Frecuencia -->
        <div class="bg-white rounded-2xl p-2 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] inline-flex flex-wrap gap-1">
            <?php foreach ($tabsFrecuencia as $f => $t): ?>
                <?php $isActive = $frecuencia === $f; ?>
                <a href="<?= url('admin/creditos' . ($f ? '?frecuencia=' . $f : '')) ?>"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-2
                          <?= $isActive ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800' ?>">
                    <i class="isax <?= $t['icon'] ?>"></i>
                    <?= $t['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

    <!-- Tabla -->
    <?php if (empty($creditos)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-document-text text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay créditos</h3>
            <p class="text-slate-500 font-medium">
                <?= $q ? "Sin resultados para «" . e($q) . "»." : 'No se encontraron créditos en este estado.' ?>
            </p>
            <?php if ($q): ?>
                <a href="<?= url('admin/creditos' . ($frecuencia ? '?frecuencia=' . $frecuencia : '')) ?>"
                   class="btn-secondary mt-6 inline-flex">Limpiar búsqueda</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-4">Cliente</th>
                            <th class="px-5 py-4 hidden sm:table-cell">Sucursal</th>
                            <th class="px-5 py-4 hidden lg:table-cell">Cobrador</th>
                            <th class="px-5 py-4 text-right hidden md:table-cell">Prestado</th>
                            <th class="px-5 py-4 text-right hidden md:table-cell">A devolver</th>
                            <th class="px-5 py-4 hidden xl:table-cell">Progreso</th>
                            <th class="px-5 py-4 hidden lg:table-cell">Inicio</th>
                            <th class="px-5 py-4 text-center">Estado</th>
                            <th class="px-5 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($creditos as $idx => $c): ?>
                        <?php
                            $tab     = $tabsEstado[$c['estado']] ?? ['label' => ucfirst($c['estado']), 'badge' => 'badge'];
                            $total   = (int)($c['total_cuotas'] ?? 0);
                            $pagadas = (int)($c['cuotas_pagadas'] ?? 0);
                            $pct     = $total > 0 ? round($pagadas / $total * 100) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group"
                            x-show="visible(<?= $idx ?>)" x-cloak>
                            <!-- Cliente -->
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-800 leading-tight"><?= e($c['cliente_nombre']) ?></p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">DNI <?= e($c['dni']) ?></p>
                            </td>
                            <!-- Sucursal -->
                            <td class="px-5 py-4 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-shop"></i> <?= e($c['sucursal_nombre']) ?>
                                </span>
                            </td>
                            <!-- Cobrador -->
                            <td class="px-5 py-4 hidden lg:table-cell text-slate-600 text-xs font-medium">
                                <span class="flex items-center gap-1.5">
                                    <i class="isax isax-user-tag text-slate-400"></i>
                                    <?= e($c['cobrador_nombre'] ?? '—') ?>
                                </span>
                            </td>
                            <!-- Montos -->
                            <td class="px-5 py-4 text-right font-bold text-slate-800 hidden md:table-cell">
                                <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-brand-600 hidden md:table-cell">
                                <?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?>
                            </td>
                            <!-- Progreso cuotas -->
                            <td class="px-5 py-4 hidden xl:table-cell">
                                <?php if ($total > 0): ?>
                                <div class="min-w-[100px]">
                                    <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                                        <span><?= $pagadas ?> / <?= $total ?> cuotas</span>
                                        <span><?= $pct ?>%</span>
                                    </div>
                                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full <?= $pct >= 100 ? 'bg-emerald-500' : 'bg-brand-500' ?> transition-all"
                                             style="width:<?= $pct ?>%"></div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <span class="text-slate-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <!-- Fecha inicio -->
                            <td class="px-5 py-4 hidden lg:table-cell text-slate-500 text-xs font-medium">
                                <span class="flex items-center gap-1">
                                    <i class="isax isax-calendar-1 text-slate-400"></i>
                                    <?= DateHelper::formatoArg($c['fecha_inicio']) ?>
                                </span>
                            </td>
                            <!-- Estado -->
                            <td class="px-5 py-4 text-center">
                                <span class="<?= $tab['badge'] ?>">
                                    <?= $tab['label'] ?>
                                </span>
                            </td>
                            <!-- Acciones -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                <?php if ($c['estado'] === 'pendiente_autorizacion'): ?>
                                    <a href="<?= url('admin/creditos/' . $c['id'] . '/autorizar') ?>"
                                       class="btn-primary text-xs whitespace-nowrap shadow-sm shadow-brand-500/20">
                                        <i class="isax isax-tick-circle"></i> Evaluar
                                    </a>
                                <?php else: ?>
                                    <?php if ($c['estado'] === 'activo'): ?>
                                    <button type="button"
                                            @click="$dispatch('abrir-pago', { creditoId: <?= (int)$c['id'] ?>, clienteNombre: <?= json_encode($c['cliente_nombre']) ?> })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 active:scale-95 transition-all shadow-sm shadow-brand-500/30 whitespace-nowrap">
                                        <i class="isax isax-money-recive"></i> Pagar
                                    </button>
                                    <?php endif; ?>
                                    <a href="<?= url('creditos/' . $c['id']) ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                        <i class="isax isax-arrow-right-3"></i> Ver
                                    </a>
                                <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Fila vacía cuando la búsqueda no encuentra nada -->
                        <tr x-show="q && !hayResultados" x-cloak>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400 font-medium text-sm">
                                Sin resultados para "<span x-text="q" class="font-bold text-slate-600"></span>"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>
                    <span x-show="!q">
                        <?= count($creditos) ?> crédito<?= count($creditos) !== 1 ? 's' : '' ?>
                        <?php if ($frecuencia): ?> · <?= ucfirst($frecuencia) ?><?php endif; ?>
                    </span>
                    <template x-if="q">
                        <span>Filtrando por "<strong x-text="q"></strong>"</span>
                    </template>
                </span>
                <div class="flex items-center gap-3">
                    <template x-if="q">
                        <button @click="q = ''" class="text-brand-600 hover:underline font-bold flex items-center gap-1">
                            <i class="isax isax-close-circle"></i> Limpiar búsqueda
                        </button>
                    </template>
                    <?php if ($frecuencia || $soloPendientes): ?>
                    <a href="<?= url('admin/creditos') ?>" class="text-slate-500 hover:underline font-bold flex items-center gap-1">
                        <i class="isax isax-close-circle"></i> Limpiar filtros
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    </div><!-- cierre x-data buscador -->
</div>

<!-- ========== MODAL PAGO RÁPIDO ========== -->
<div x-data="pagoRapido()"
     x-on:abrir-pago.window="abrir($event.detail)"
     x-show="abierto"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         x-show="abierto"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cerrar()"></div>

    <!-- Panel -->
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden"
         x-show="abierto"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition duration-150 ease-in"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;">Registrar Pago</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5" x-text="clienteNombre"></p>
            </div>
            <button @click="cerrar()"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                <i class="isax isax-close-circle text-lg"></i>
            </button>
        </div>

        <!-- ——— PASO: cargando ——— -->
        <div x-show="paso === 'cargando'" class="px-6 py-12 flex flex-col items-center gap-3">
            <div class="w-12 h-12 rounded-full border-4 border-brand-100 border-t-brand-600 animate-spin"></div>
            <p class="text-sm font-medium text-slate-500">Obteniendo datos de la cuota…</p>
        </div>

        <!-- ——— PASO: error carga ——— -->
        <div x-show="paso === 'error_carga'" class="px-6 py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
                <i class="isax isax-warning-2 text-2xl"></i>
            </div>
            <p class="font-bold text-slate-800 mb-1">No se pudo cargar la cuota</p>
            <p class="text-sm text-slate-500 mb-5" x-text="errorMsg"></p>
            <button @click="cerrar()" class="btn-secondary">Cerrar</button>
        </div>

        <!-- ——— PASO: formulario ——— -->
        <div x-show="paso === 'form'" class="px-6 py-5 space-y-5">

            <!-- Info cuota -->
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4 space-y-2">
                <div class="flex justify-between text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <span>Cuota</span>
                    <span>Saldo pendiente</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-sm font-bold text-slate-700" x-text="'N° ' + (cuota ? cuota.numero_cuota : '')"></span>
                    <span class="text-xl font-extrabold text-brand-600" x-text="'$ ' + formatNum(saldo)" style="font-family:'Outfit',sans-serif;"></span>
                </div>
                <template x-if="mora > 0">
                    <div class="pt-2 border-t border-slate-100 flex justify-between text-xs">
                        <span class="font-medium text-red-500">Mora pendiente</span>
                        <span class="font-bold text-red-600" x-text="'$ ' + formatNum(mora)"></span>
                    </div>
                </template>
                <template x-if="cobrador">
                    <div class="flex items-center gap-1 text-[11px] text-slate-400 font-medium pt-1">
                        <i class="isax isax-user-tag text-slate-300"></i>
                        <span x-text="'Cobrador: ' + cobrador"></span>
                    </div>
                </template>
            </div>

            <!-- Monto -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Monto a cobrar <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                    <input type="number" step="0.01" min="0.01"
                           x-model="monto"
                           @input="monto = $event.target.value"
                           class="form-input pl-8 w-full text-lg font-bold text-slate-800"
                           placeholder="0.00">
                </div>
                <button type="button" @click="monto = saldo"
                        class="mt-1.5 text-[11px] font-bold text-brand-600 hover:underline">
                    Completar saldo ($ <span x-text="formatNum(saldo)"></span>)
                </button>
            </div>

            <!-- Mora -->
            <template x-if="mora > 0">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Imputar a mora (opcional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                        <input type="number" step="0.01" min="0"
                               x-model="montoMora"
                               class="form-input pl-8 w-full"
                               placeholder="0.00">
                    </div>
                    <button type="button" @click="montoMora = mora"
                            class="mt-1.5 text-[11px] font-bold text-red-600 hover:underline">
                        Imputar mora completa ($ <span x-text="formatNum(mora)"></span>)
                    </button>
                </div>
            </template>

            <!-- Método de pago -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">Método de pago</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="relative cursor-pointer">
                        <input type="radio" value="efectivo" x-model="metodo" class="sr-only">
                        <div :class="metodo === 'efectivo'
                                ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                                : 'bg-slate-50 border-slate-200 text-slate-600'"
                             class="px-3 py-3 border rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                            <i class="isax isax-money"></i> Efectivo
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" value="transferencia" x-model="metodo" class="sr-only">
                        <div :class="metodo === 'transferencia'
                                ? 'bg-sky-50 border-sky-300 text-sky-700'
                                : 'bg-slate-50 border-slate-200 text-slate-600'"
                             class="px-3 py-3 border rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                            <i class="isax isax-card-send"></i> Transferencia
                        </div>
                    </label>
                </div>
            </div>

            <!-- Acciones form -->
            <div class="flex gap-3 pt-1">
                <button type="button" @click="cerrar()" class="btn-secondary flex-1">Cancelar</button>
                <button type="button" @click="irAConfirmar()"
                        :disabled="!monto || parseFloat(monto) <= 0"
                        class="btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                    Continuar <i class="isax isax-arrow-right-3"></i>
                </button>
            </div>
        </div>

        <!-- ——— PASO: confirmar ——— -->
        <div x-show="paso === 'confirmar'" class="px-6 py-5 space-y-5">

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-center">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-3">Confirmar pago</p>
                <p class="text-3xl font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;">
                    $ <span x-text="formatNum(parseFloat(monto || 0))"></span>
                </p>
                <p class="text-xs text-slate-500 font-medium mt-1" x-text="clienteNombre"></p>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Cuota N°</span>
                    <span class="font-bold text-slate-800" x-text="cuota ? cuota.numero_cuota : ''"></span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Método</span>
                    <span class="font-bold text-slate-800 capitalize" x-text="metodo"></span>
                </div>
                <template x-if="parseFloat(montoMora) > 0">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Imputado a mora</span>
                        <span class="font-bold text-red-600">$ <span x-text="formatNum(parseFloat(montoMora))"></span></span>
                    </div>
                </template>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500 font-medium">Cobrador</span>
                    <span class="font-bold text-slate-800" x-text="cobrador || '—'"></span>
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-center">
                <p class="text-[11px] font-medium text-slate-500">
                    <i class="isax isax-info-circle text-amber-500"></i>
                    Este pago quedará confirmado directamente (admin).
                </p>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="paso = 'form'" class="btn-secondary flex-1">
                    <i class="isax isax-arrow-left-2"></i> Volver
                </button>
                <button type="button" @click="enviarPago()"
                        :disabled="enviando"
                        class="btn-primary flex-1 disabled:opacity-70">
                    <template x-if="!enviando"><span><i class="isax isax-tick-circle"></i> Confirmar</span></template>
                    <template x-if="enviando"><span class="flex items-center gap-2"><span class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin inline-block"></span> Procesando…</span></template>
                </button>
            </div>
        </div>

        <!-- ——— PASO: éxito ——— -->
        <div x-show="paso === 'exito'" class="px-6 py-12 flex flex-col items-center gap-4 text-center">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <i class="isax isax-tick-circle text-3xl"></i>
            </div>
            <div>
                <p class="text-lg font-extrabold text-slate-900" style="font-family:'Outfit',sans-serif;">¡Pago registrado!</p>
                <p class="text-sm text-slate-500 font-medium mt-1">$ <span x-text="formatNum(parseFloat(monto || 0))"></span> confirmado correctamente.</p>
            </div>
            <button @click="recargar()" class="btn-primary mt-2 w-full">
                <i class="isax isax-refresh-2"></i> Actualizar página
            </button>
        </div>

        <!-- ——— PASO: error envío ——— -->
        <div x-show="paso === 'error_envio'" class="px-6 py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
                <i class="isax isax-close-circle text-2xl"></i>
            </div>
            <p class="font-bold text-slate-800 mb-1">No se pudo registrar el pago</p>
            <p class="text-sm text-red-500 mb-5" x-text="errorMsg"></p>
            <div class="flex gap-3 justify-center">
                <button @click="paso = 'form'" class="btn-secondary">Reintentar</button>
                <button @click="cerrar()" class="btn-secondary">Cerrar</button>
            </div>
        </div>

    </div>
</div>

<script>
function pagoRapido() {
    return {
        abierto: false,
        paso: 'cargando',
        creditoId: null,
        cuotaId: null,
        clienteNombre: '',
        cuota: null,
        saldo: 0,
        mora: 0,
        cobrador: '',
        monto: '',
        montoMora: '',
        metodo: 'efectivo',
        enviando: false,
        errorMsg: '',

        abrir(detail) {
            this.creditoId    = detail.creditoId;
            this.clienteNombre = detail.clienteNombre;
            this.cuotaId      = null;
            this.cuota        = null;
            this.monto        = '';
            this.montoMora    = '';
            this.metodo       = 'efectivo';
            this.enviando     = false;
            this.errorMsg     = '';
            this.paso         = 'cargando';
            this.abierto      = true;
            this.cargarCuota();
        },

        cerrar() {
            this.abierto = false;
        },

        recargar() {
            window.location.reload();
        },

        formatNum(n) {
            return parseFloat(n || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        async cargarCuota() {
            try {
                const res  = await fetch(`<?= url('admin/api/creditos') ?>/${this.creditoId}/proxima-cuota`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok) {
                    this.errorMsg = data.error || 'Error al cargar datos.';
                    this.paso = 'error_carga';
                    return;
                }
                this.cuota   = data.cuota;
                this.cuotaId = data.cuota.id;
                this.saldo   = parseFloat(data.saldo);
                this.mora    = parseFloat(data.mora);
                this.cobrador = data.cobrador_nombre || '';
                this.monto   = this.saldo.toFixed(2);
                this.paso    = 'form';
            } catch (e) {
                this.errorMsg = 'Error de red. Intentá de nuevo.';
                this.paso = 'error_carga';
            }
        },

        irAConfirmar() {
            if (!this.monto || parseFloat(this.monto) <= 0) return;
            this.paso = 'confirmar';
        },

        async enviarPago() {
            this.enviando = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const fd   = new FormData();
            fd.append('_csrf',       csrf);
            fd.append('monto',       this.monto);
            fd.append('monto_mora',  this.montoMora || '0');
            fd.append('metodo_pago', this.metodo);

            try {
                const res  = await fetch(`<?= url('admin/api/creditos') ?>/${this.creditoId}/pago/${this.cuotaId}`, {
                    method: 'POST',
                    body: fd,
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.paso = 'exito';
                } else {
                    this.errorMsg = data.error || 'Error desconocido.';
                    this.paso = 'error_envio';
                }
            } catch (e) {
                this.errorMsg = 'Error de red. Intentá de nuevo.';
                this.paso = 'error_envio';
            } finally {
                this.enviando = false;
            }
        }
    };
}

function busquedaCreditosAdmin() {
    const datos = <?= json_encode(array_map(
        fn($c) => mb_strtolower(
            ($c['cliente_nombre'] ?? '') . ' ' .
            ($c['dni'] ?? '') . ' ' .
            ($c['sucursal_nombre'] ?? '') . ' ' .
            ($c['cobrador_nombre'] ?? '')
        ),
        $creditos
    )) ?>;
    return {
        q: '',
        datos,
        hayResultados() { return !this.q || datos.some(d => d.includes(this.q.toLowerCase())); },
        visible(idx)    { return !this.q || (datos[idx] ?? '').includes(this.q.toLowerCase()); },
    };
}
</script>
