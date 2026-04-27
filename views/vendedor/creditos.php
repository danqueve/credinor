<?php // views/vendedor/creditos.php
use App\Helpers\MoneyHelper;

$badgesEstado = [
    'pendiente_autorizacion' => 'bg-amber-100 text-amber-800 border-amber-200',
    'activo'     => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'finalizado' => 'bg-blue-100 text-blue-800 border-blue-200',
    'rechazado'  => 'bg-red-100 text-red-800 border-red-200',
    'cancelado'  => 'bg-slate-100 text-slate-800 border-slate-200',
];
$iconsEstado = [
    'pendiente_autorizacion' => 'isax-timer-1',
    'activo'     => 'isax-verify',
    'finalizado' => 'isax-tick-circle',
    'rechazado'  => 'isax-close-circle',
    'cancelado'  => 'isax-minus-cirlce',
];
$labelsEstado = [
    'pendiente_autorizacion' => 'Pendiente',
    'activo'     => 'Activo',
    'finalizado' => 'Finalizado',
    'rechazado'  => 'Rechazado',
    'cancelado'  => 'Cancelado',
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Mis Créditos</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Cartera de créditos de tu sucursal</p>
        </div>
        <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary shadow-md shadow-brand-500/20">
            <i class="isax isax-money-send"></i> Nueva Solicitud
        </a>
    </div>

    <!-- Buscador + Tabs -->
    <div x-data="busquedaCreditos()" class="space-y-3">

        <!-- Buscador en tiempo real -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="isax isax-search-normal-1 text-slate-400"></i>
            </div>
            <input type="text"
                   x-model="q"
                   placeholder="Buscar por cliente o DNI..."
                   class="form-input pl-10 w-full bg-white border-slate-200 focus:border-brand-500">
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
                <a href="<?= url('vendedor/creditos' . ($f ? '?frecuencia=' . $f : '')) ?>"
                   class="px-4 py-2 text-sm font-bold rounded-xl transition-all flex items-center gap-2
                          <?= $isActive ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                    <i class="isax <?= $t['icon'] ?>"></i>
                    <?= $t['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>

    <!-- Tabla (dentro del x-data para que las filas accedan a `q`) -->
    <?php if (empty($creditos)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-receipt-search text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay créditos registrados</h3>
            <p class="text-slate-500 font-medium mb-6">
                No hay créditos con frecuencia <?= $frecuencia ? '<strong>' . ucfirst($frecuencia) . '</strong>' : '' ?> en tu sucursal.
            </p>
            <?php if (!$frecuencia): ?>
                <a href="<?= url('vendedor/creditos/nuevo') ?>" class="btn-primary inline-flex">
                    <i class="isax isax-money-send"></i> Crear la primera solicitud
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 text-right hidden sm:table-cell">Montos</th>
                            <th class="px-6 py-4 hidden lg:table-cell">Progreso</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($creditos as $idx => $c):
                            $total   = (int)($c['total_cuotas'] ?? 0);
                            $pagadas = (int)($c['cuotas_pagadas'] ?? 0);
                            $pct     = $total > 0 ? round($pagadas / $total * 100) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group"
                            x-show="visible(<?= $idx ?>)"
                            x-cloak>
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800 leading-tight"><?= e($c['cliente_nombre']) ?></p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5">DNI <?= e($c['dni']) ?></p>
                                <!-- Montos en mobile -->
                                <p class="text-xs font-bold text-slate-700 mt-1 sm:hidden">
                                    <?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?>
                                    <span class="text-slate-400 font-normal">total</span>
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right hidden sm:table-cell">
                                <span class="font-bold text-slate-800 block"><?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?> <span class="text-[10px] text-slate-400 font-normal uppercase">Total</span></span>
                                <span class="text-[10px] font-medium text-emerald-600 block mt-0.5">Prestado: <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?></span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell">
                                <?php if ($total > 0): ?>
                                <div class="min-w-[100px]">
                                    <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                                        <span><?= $pagadas ?> / <?= $total ?> cuotas</span>
                                        <span><?= $pct ?>%</span>
                                    </div>
                                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full <?= $pct >= 100 ? 'bg-emerald-500' : 'bg-brand-500' ?>"
                                             style="width:<?= $pct ?>%"></div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <span class="text-slate-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badgesEstado[$c['estado']] ?? 'bg-slate-100 text-slate-800 border-slate-200' ?>">
                                    <i class="isax <?= $iconsEstado[$c['estado']] ?? 'isax-info-circle' ?>"></i>
                                    <?= $labelsEstado[$c['estado']] ?? $c['estado'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if ($c['estado'] === 'activo' && !empty($c['proxima_cuota_id'])): ?>
                                    <a href="<?= url('cobrador/pago/' . $c['id'] . '/' . $c['proxima_cuota_id']) ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 active:scale-95 transition-all shadow-sm shadow-brand-500/30 whitespace-nowrap">
                                        <i class="isax isax-money-recive"></i> Cobrar
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= url('creditos/' . $c['id']) ?>"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                        <i class="isax isax-arrow-right-3"></i> Ver
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Fila vacía cuando el buscador no encuentra nada -->
                        <tr x-show="q && !hayResultados" x-cloak>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-medium text-sm">
                                Sin resultados para "<span x-text="q" class="font-bold text-slate-600"></span>"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>
                    <span x-show="!q"><?= count($creditos) ?> crédito<?= count($creditos) !== 1 ? 's' : '' ?></span>
                    <template x-if="q">
                        <span>
                            Filtrando por "<strong x-text="q"></strong>"
                        </span>
                    </template>
                </span>
                <template x-if="q">
                    <button @click="q = ''" class="text-brand-600 hover:underline font-bold flex items-center gap-1">
                        <i class="isax isax-close-circle"></i> Limpiar
                    </button>
                </template>
            </div>
        </div>
    <?php endif; ?>

    </div><!-- cierre x-data -->
</div>

<script>
function busquedaCreditos() {
    const datos = <?= json_encode(array_map(
        fn($c) => mb_strtolower($c['cliente_nombre'] . ' ' . $c['dni']),
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
