<?php // views/admin/autorizar_credito.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
?>
<div class="max-w-4xl mx-auto space-y-6 pb-10">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
                <i class="isax isax-arrow-left-2"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Evaluar Crédito #<?= $credito['id'] ?>
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                    <span class="badge-pendiente inline-flex items-center gap-1">
                        <i class="isax isax-clock"></i> Pendiente
                    </span>
                    <span class="text-slate-300">•</span>
                    Solicitado el <?= date('d/m/Y', strtotime($credito['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Datos del crédito -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tarjeta Cliente -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
                <i class="isax isax-profile-circle text-8xl text-brand-600"></i>
            </div>
            
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-5">
                <i class="isax isax-user text-brand-500"></i> Información del Cliente
            </h2>
            
            <div class="space-y-4 text-sm relative z-10">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Nombre</span>
                    <a href="<?= url('vendedor/clientes/' . $credito['cliente_id']) ?>" class="font-bold text-brand-600 hover:underline">
                        <?= e($credito['cliente_nombre']) ?>
                    </a>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">DNI</span>
                    <span class="font-medium text-slate-900"><?= e($credito['dni']) ?></span>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Teléfono</span>
                    <?php if ($credito['telefono']): ?>
                        <span class="inline-flex items-center gap-1 font-medium text-slate-900">
                            <i class="isax isax-call text-slate-400"></i> <?= e($credito['telefono']) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-slate-400">—</span>
                    <?php endif; ?>
                </div>
                <div class="pt-2 border-t border-slate-100/80">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-0.5">Sucursal Origen</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold mt-1">
                        <i class="isax isax-shop"></i> <?= e($credito['sucursal_nombre']) ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta Crédito -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl relative overflow-hidden text-white">
            <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
                <i class="isax isax-money-tick text-8xl text-white"></i>
            </div>
            
            <h2 class="text-base font-bold text-slate-100 flex items-center gap-2 mb-5">
                <i class="isax isax-receipt-item text-brand-400"></i> Detalles Solicitados
            </h2>
            
            <div class="grid grid-cols-2 gap-y-5 gap-x-4 text-sm relative z-10">
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Monto Solicitado</span>
                    <span class="font-bold text-xl tracking-tight text-white" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::format((float)$credito['monto_prestado']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">A Devolver</span>
                    <span class="font-bold text-xl tracking-tight text-emerald-400" style="font-family: 'Outfit', sans-serif;">
                        <?= MoneyHelper::format((float)$credito['monto_a_devolver']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Plan de Cuotas</span>
                    <span class="inline-flex items-center gap-1.5 font-medium bg-slate-800/50 px-2.5 py-1 rounded-lg border border-slate-700">
                        <i class="isax isax-calendar-1 text-slate-400"></i>
                        <?= $credito['cantidad_cuotas'] ?> × <?= ucfirst($credito['frecuencia']) ?>
                    </span>
                </div>
                <div>
                    <span class="block text-xs font-medium text-slate-400 mb-0.5">Inicio / Primera</span>
                    <span class="font-medium text-slate-300">
                        <?= DateHelper::formatoArg($credito['fecha_inicio']) ?> <br>
                        <span class="text-xs text-slate-500">1ra: <?= DateHelper::formatoArg($credito['fecha_primera_cuota']) ?></span>
                    </span>
                </div>
                
                <?php if ($credito['observaciones']): ?>
                <div class="col-span-2 pt-4 border-t border-slate-700/50">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1.5">
                        <i class="isax isax-info-circle text-amber-500"></i> Observaciones
                    </span>
                    <p class="font-medium text-slate-300 leading-relaxed"><?= nl2br(e($credito['observaciones'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview de cuotas -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
            <i class="isax isax-calendar-tick text-brand-500"></i> Simulación de Cuotas
        </h2>
        <p class="text-sm text-slate-500 font-medium mb-6">Las cuotas reales se generarán automáticamente al autorizar el crédito.</p>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 text-sm">
            <?php
            use App\Helpers\DateHelper as DH;
            $fechas = DH::generarFechas(
                $credito['fecha_primera_cuota'],
                min($credito['cantidad_cuotas'], 12),
                $credito['frecuencia']
            );
            [$mc] = \App\Helpers\MoneyHelper::distribuirCuotas(
                (float)$credito['monto_a_devolver'],
                (int)$credito['cantidad_cuotas']
            );
            foreach ($fechas as $i => $f):
            ?>
                <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 text-center hover:border-brand-200 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center font-bold text-slate-400 mx-auto mb-2 text-xs">
                        #<?= $i+1 ?>
                    </div>
                    <div class="text-brand-600 font-bold mb-0.5">$<?= number_format($mc, 0, ',', '.') ?></div>
                    <div class="text-slate-500 text-xs font-medium"><?= DH::formatoArg($f) ?></div>
                </div>
            <?php endforeach; ?>
            
            <?php if ($credito['cantidad_cuotas'] > 12): ?>
                <div class="bg-slate-50 rounded-2xl p-3 border border-dashed border-slate-200 text-center flex flex-col items-center justify-center text-slate-400 font-bold">
                    <i class="isax isax-more text-2xl mb-1 text-slate-300"></i>
                    +<?= $credito['cantidad_cuotas'] - 12 ?> cuotas
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Acciones Finales -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Formulario de autorización -->
        <div class="bg-emerald-50/50 rounded-3xl p-6 border border-emerald-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-emerald-400"></div>
            <h2 class="text-lg font-bold text-emerald-900 flex items-center gap-2 mb-4">
                <i class="isax isax-tick-circle text-emerald-600"></i> Autorizar Crédito
            </h2>
            <form method="POST" action="<?= url('admin/creditos/' . $credito['id'] . '/autorizar') ?>">
                <?= csrf_field() ?>
                <div class="mb-5">
                    <label for="cobrador_id" class="block text-sm font-bold text-emerald-800 mb-2">Asignar cobrador <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-user-tag text-emerald-500"></i>
                        </div>
                        <select id="cobrador_id" name="cobrador_id" class="form-select pl-10 border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20 bg-white" required>
                            <option value="">— Seleccioná un cobrador —</option>
                            <?php foreach ($cobradores as $cob): ?>
                                <option value="<?= $cob['id'] ?>"><?= e($cob['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                    <i class="isax isax-tick-square"></i> Autorizar y Generar Cuotas
                </button>
            </form>
        </div>

        <!-- Formulario de rechazo -->
        <div class="bg-red-50/50 rounded-3xl p-6 border border-red-100 shadow-sm relative overflow-hidden" x-data="{ abierto: false }">
            <div class="absolute top-0 right-0 w-2 h-full bg-red-400"></div>
            
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-red-900 flex items-center gap-2">
                    <i class="isax isax-close-circle text-red-600"></i> Rechazar
                </h2>
                <button type="button" @click="abierto = !abierto"
                        class="text-sm text-red-600 hover:text-red-800 font-bold bg-white px-3 py-1.5 rounded-lg shadow-sm border border-red-100 transition-colors" x-show="!abierto">
                    Rechazar Solicitud
                </button>
            </div>
            
            <div x-show="abierto" x-cloak x-transition.opacity>
                <form method="POST" action="<?= url('admin/creditos/' . $credito['id'] . '/rechazar') ?>">
                    <?= csrf_field() ?>
                    <label for="motivo" class="block text-sm font-bold text-red-800 mb-2">Motivo del rechazo <span class="text-red-500">*</span></label>
                    <textarea id="motivo" name="motivo" rows="3" required
                              class="form-input resize-none mb-4 border-red-200 focus:border-red-500 focus:ring-red-500/20 bg-white"
                              placeholder="Describe brevemente por qué se rechaza este crédito..."></textarea>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all flex items-center justify-center gap-2">
                            Confirmar Rechazo
                        </button>
                        <button type="button" @click="abierto = false" class="px-4 py-3 bg-white text-slate-500 font-bold rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
            
            <div x-show="!abierto" class="text-sm text-red-700/70 font-medium">
                Esta acción cancelará la solicitud permanentemente y notificará al vendedor.
            </div>
        </div>
    </div>
</div>
