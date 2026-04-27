<?php // views/vendedor/credito_form.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-3xl mx-auto space-y-6 pb-10">
    <div class="flex items-center gap-4">
        <a href="<?= url(\App\Core\Auth::isAdmin() ? 'admin/creditos' : 'vendedor/creditos') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
            <i class="isax isax-arrow-left-2"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                Nuevo crédito
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                Completa los datos para crear y activar el crédito
            </p>
        </div>
    </div>

    <form method="POST" action="<?= url('vendedor/creditos') ?>"
          class="space-y-6 relative" novalidate
          x-data="creditoForm()" @submit="calcular">
        <?= csrf_field() ?>

        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <i class="isax isax-money-send text-8xl text-brand-600"></i>
            </div>
            
            <div class="space-y-6 relative z-10">

                <?php if (\App\Core\Auth::isAdmin() && !empty($sucursales)): ?>
                <!-- Sucursal (solo visible para admin) -->
                <div>
                    <label for="sucursal_id" class="form-label block text-sm font-bold text-slate-700 mb-2">
                        Sucursal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-building text-slate-400"></i>
                        </div>
                        <select id="sucursal_id" name="sucursal_id" required
                                class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                            <option value="">— Seleccionar sucursal —</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="border-t border-slate-100"></div>
                <?php endif; ?>

                <!-- Cliente -->
                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-2">Cliente Solicitante <span class="text-red-500">*</span></label>
                    <?php if ($cliente): ?>
                        <div class="flex items-center justify-between p-4 bg-brand-50 rounded-2xl border border-brand-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center shrink-0">
                                    <i class="isax isax-user text-xl"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-brand-900"><?= e($cliente['nombre']) ?></span>
                                    <span class="block text-xs font-medium text-brand-600/70">DNI <?= e($cliente['dni']) ?></span>
                                </div>
                            </div>
                            <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">
                            <a href="<?= url('vendedor/creditos/nuevo') ?>" class="text-sm font-bold text-brand-600 hover:text-brand-800 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-brand-100 transition-colors">
                                Cambiar
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-user text-slate-400"></i>
                            </div>
                            <select name="cliente_id" class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full" required>
                                <option value="">— Seleccioná un cliente —</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?> — DNI <?= e($c['dni']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <!-- Montos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="monto_prestado" class="form-label block text-sm font-bold text-slate-700 mb-2">Monto Prestado <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input id="monto_prestado" type="number" name="monto_prestado" min="1" step="0.01" required
                                   class="form-input pl-8 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full font-bold text-lg"
                                   x-model="montoPrestado"
                                   @input="calcular"
                                   placeholder="50000">
                        </div>
                    </div>
                    <div>
                        <label for="cantidad_cuotas" class="form-label block text-sm font-bold text-slate-700 mb-2">Cantidad de Cuotas <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-layer text-slate-400"></i>
                            </div>
                            <input id="cantidad_cuotas" type="number" name="cantidad_cuotas" min="1" max="365" required
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   x-model="cantCuotas"
                                   @input="calcular"
                                   placeholder="Ej: 12">
                        </div>
                    </div>
                    <div>
                        <label for="valor_cuota" class="form-label block text-sm font-bold text-slate-700 mb-2">Valor de Cuota <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input id="valor_cuota" type="number" name="valor_cuota" min="1" step="0.01" required
                                   class="form-input pl-8 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full font-bold text-lg text-emerald-600 focus:text-emerald-700"
                                   x-model="valorCuota"
                                   @input="calcular"
                                   placeholder="5000">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="monto_a_devolver" :value="montoDevolver">

                <!-- Diferencia calculada -->
                <div class="bg-slate-900 rounded-2xl p-4 md:p-5 flex flex-wrap gap-4 md:gap-8 justify-between shadow-lg" x-show="montoPrestado > 0 && montoDevolver > 0" x-cloak x-transition.opacity>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Diferencia (Interés)</span>
                        <span class="font-bold text-lg text-white flex items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                            <i class="isax isax-wallet-add text-brand-400"></i> $<span x-text="diferencia"></span>
                        </span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tasa Aplicada</span>
                        <span class="font-bold text-lg text-white flex items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                            <i class="isax isax-percentage-square text-amber-400"></i> <span x-text="porcentaje + '%'"></span>
                        </span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total a Devolver</span>
                        <span class="font-bold text-lg text-white flex items-center gap-2" style="font-family: 'Outfit', sans-serif;">
                            <i class="isax isax-receipt-item text-emerald-400"></i> $<span x-text="montoDevolver.toLocaleString('es-AR')"></span>
                        </span>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <!-- Frecuencia -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="frecuencia" class="form-label block text-sm font-bold text-slate-700 mb-2">Frecuencia de Pago <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-repeate-music text-slate-400"></i>
                            </div>
                            <select id="frecuencia" name="frecuencia" class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full" required>
                                <option value="diaria">Diaria</option>
                                <option value="semanal">Semanal</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="mensual" selected>Mensual</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="fecha_inicio" class="form-label block text-sm font-bold text-slate-700 mb-2">Fecha de Otorgamiento <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-calendar-1 text-slate-400"></i>
                            </div>
                            <input id="fecha_inicio" type="date" name="fecha_inicio" required
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div>
                        <label for="fecha_primera_cuota" class="form-label block text-sm font-bold text-slate-700 mb-2">Vencimiento 1ra Cuota <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-calendar-tick text-slate-400"></i>
                            </div>
                            <input id="fecha_primera_cuota" type="date" name="fecha_primera_cuota" required
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mora -->
                    <div class="bg-red-50/50 rounded-2xl border border-red-100 p-5" x-data="{ aplica: false }">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative flex items-center">
                                <input id="aplica_mora" type="checkbox" name="aplica_mora" value="1"
                                       class="w-5 h-5 rounded border-red-300 text-red-600 focus:ring-red-500 focus:ring-offset-0 cursor-pointer"
                                       x-model="aplica">
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-red-900">Aplicar mora por atraso</span>
                                <span class="block text-xs font-medium text-red-600/70">Calcula interés diario sobre cuotas vencidas</span>
                            </div>
                        </label>
                        
                        <div x-show="aplica" x-cloak x-transition.opacity class="mt-4 pt-4 border-t border-red-200/50">
                            <label for="porcentaje_mora" class="form-label block text-xs font-bold uppercase tracking-wider text-red-800 mb-2">% Mora diaria personalizado</label>
                            <div class="flex items-center gap-2">
                                <div class="relative w-32">
                                    <input id="porcentaje_mora" type="number" name="porcentaje_mora_diaria"
                                           min="0" max="5" step="0.01"
                                           class="form-input bg-white border-red-200 focus:border-red-500 focus:ring-red-500/20 w-full"
                                           placeholder="Ej: 0.10">
                                </div>
                                <span class="text-xs text-red-600 font-medium">Dejar vacío para usar la configuración global</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="form-label block text-sm font-bold text-slate-700 mb-2">Observaciones</label>
                        <div class="relative">
                            <div class="absolute top-3 left-3.5 flex items-start pointer-events-none">
                                <i class="isax isax-info-circle text-slate-400"></i>
                            </div>
                            <textarea id="observaciones" name="observaciones" rows="3"
                                      class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full resize-none"
                                      placeholder="Notas adicionales sobre el crédito..."></textarea>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="btn-primary sm:flex-1 justify-center py-4 text-base shadow-lg shadow-brand-500/30">
                <i class="isax isax-tick-circle"></i> Crear y Activar Crédito
            </button>
            <a href="<?= url(\App\Core\Auth::isAdmin() ? 'admin/creditos' : 'vendedor/creditos') ?>" class="btn-secondary sm:flex-1 justify-center py-4 bg-white hover:bg-slate-50 border-slate-200">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
function creditoForm() {
    return {
        montoPrestado: '',
        valorCuota: '',
        cantCuotas: '',
        montoDevolver: 0,
        diferencia: '0',
        porcentaje: '0.00',
        calcular() {
            const p = parseFloat(this.montoPrestado) || 0;
            const v = parseFloat(this.valorCuota) || 0;
            const c = parseInt(this.cantCuotas) || 0;
            this.montoDevolver = v * c;
            const interes = this.montoDevolver - p;
            this.diferencia = interes.toLocaleString('es-AR', {minimumFractionDigits: 2});
            this.porcentaje = p > 0 ? ((interes / p) * 100).toFixed(2) : '0.00';
        }
    }
}
</script>
