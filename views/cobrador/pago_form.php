<?php // views/cobrador/pago_form.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
?>
<div class="max-w-lg mx-auto space-y-6 pb-24"
     x-data="pagoForm(<?= $saldo ?>, <?= $credito['mora_acumulada'] - $credito['mora_pagada'] ?>)">

    <!-- Header Mobile con botón volver -->
    <div class="flex items-center gap-4 mb-2">
        <a href="<?= url('cobrador/agenda') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-95 shadow-sm">
            <i class="isax isax-arrow-left-2 text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900" style="font-family: 'Outfit', sans-serif;">Registrar Pago</h1>
            <p class="text-slate-500 font-medium text-xs">Ingreso de recaudación</p>
        </div>
    </div>

    <!-- Tarjeta Cliente (Destacada) -->
    <div class="bg-gradient-to-r from-brand-600 to-brand-700 rounded-3xl p-6 text-white shadow-xl shadow-brand-500/20 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
            <i class="isax isax-user text-8xl"></i>
        </div>
        
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center font-bold text-xl border border-white/30 shadow-inner shrink-0">
                <?= mb_strtoupper(mb_substr($credito['cliente_nombre'], 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-extrabold text-xl truncate" style="font-family: 'Outfit', sans-serif;"><?= e($credito['cliente_nombre']) ?></p>
                <div class="flex items-center gap-2 text-brand-100 text-xs font-medium mt-0.5">
                    <span class="flex items-center gap-1"><i class="isax isax-personalcard"></i> <?= e($credito['dni']) ?></span>
                </div>
            </div>
            
            <?php if ($credito['telefono']): ?>
            <a href="https://wa.me/549<?= preg_replace('/\D/', '', $credito['telefono']) ?>"
               target="_blank"
               class="shrink-0 w-12 h-12 bg-emerald-500 hover:bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 transition-all active:scale-95 border border-emerald-400">
                <i class="isax isax-whatsapp text-2xl"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Cuota -->
    <?php
        $isVencida = $cuota['estado'] === 'vencida';
        $bgClass = $isVencida ? 'border-red-500' : 'border-brand-500';
    ?>
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1.5 h-full <?= $bgClass ?>"></div>
        
        <div class="flex justify-between items-start mb-4 pl-2">
            <div>
                <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="isax isax-receipt-item text-slate-400"></i>
                    Cuota #<?= $cuota['numero_cuota'] ?> <span class="text-slate-400 font-medium text-sm">/ <?= $credito['cantidad_cuotas'] ?></span>
                </h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1 mt-1">
                    <i class="isax isax-calendar-1"></i> Vence: <?= DateHelper::formatoArg($cuota['fecha_vencimiento']) ?>
                </p>
            </div>
            
            <?php if ($cuota['estado'] === 'vencida'): ?>
                <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-bold border border-red-100 uppercase tracking-wider">
                    <i class="isax isax-danger"></i> Vencida
                </span>
            <?php elseif ($cuota['estado'] === 'parcial'): ?>
                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-600 px-3 py-1 rounded-lg text-[10px] font-bold border border-amber-100 uppercase tracking-wider">
                    <i class="isax isax-timer-1"></i> Parcial
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 bg-brand-50 text-brand-600 px-3 py-1 rounded-lg text-[10px] font-bold border border-brand-100 uppercase tracking-wider">
                    <i class="isax isax-clock"></i> Pendiente
                </span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-2">
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Saldo de Cuota</span>
                <span class="block text-2xl font-extrabold text-brand-600" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::format($saldo) ?>
                </span>
            </div>
            
            <?php $mora = (float)$credito['mora_acumulada'] - (float)$credito['mora_pagada']; ?>
            <?php if ($mora > 0): ?>
            <div class="bg-red-50 rounded-2xl p-4 border border-red-100">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-red-400 mb-1 flex items-center gap-1">
                    <i class="isax isax-danger text-red-500"></i> Mora Pendiente
                </span>
                <span class="block text-2xl font-extrabold text-red-600" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::format($mora) ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagos anteriores de esta cuota -->
    <?php if (!empty($pagosAnteriores)): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
            <i class="isax isax-clock text-brand-500"></i>
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Pagos anteriores en esta cuota</h3>
        </div>
        <div class="divide-y divide-slate-50">
            <?php foreach ($pagosAnteriores as $p): ?>
            <div class="flex justify-between items-center text-sm p-4 hover:bg-slate-50/50 transition-colors">
                <span class="text-slate-500 font-medium text-xs flex items-center gap-1.5">
                    <i class="isax isax-calendar-tick text-slate-400"></i> <?= date('d/m/y H:i', strtotime($p['created_at'])) ?>
                </span>
                <span class="text-emerald-600 font-extrabold" style="font-family: 'Outfit', sans-serif;">+ <?= MoneyHelper::formatShort((float)$p['monto']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario de pago -->
    <form method="POST" action="<?= url('cobrador/pago/' . $credito['id'] . '/' . $cuota['id']) ?>"
          class="space-y-6">
        <?= csrf_field() ?>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div class="mb-5">
                <label class="form-label block text-sm font-bold text-slate-700 mb-2">Monto a Cobrar <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-3xl font-extrabold text-slate-300">$</span>
                    </div>
                    <input type="number" name="monto" min="0.01" step="0.01" required
                           class="form-input pl-12 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full text-4xl font-extrabold text-brand-700 h-20 rounded-2xl shadow-inner transition-colors placeholder:text-slate-200"
                           x-model="monto"
                           @input="calcular"
                           placeholder="0.00"
                           style="font-family: 'Outfit', sans-serif;"
                           autofocus>
                </div>
                
                <!-- Acceso rápido -->
                <div class="flex gap-2 mt-4">
                    <button type="button" @click="pagarTotal()" class="flex-1 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold py-3 px-2 rounded-xl text-xs transition-colors border border-brand-200 active:scale-95 flex flex-col items-center justify-center gap-1">
                        <span class="opacity-70">Saldo Completo</span>
                        <span class="text-sm"><?= MoneyHelper::formatShort($saldo) ?></span>
                    </button>
                    <?php if ($mora > 0): ?>
                    <button type="button" @click="pagarTotalConMora()" class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold py-3 px-2 rounded-xl text-xs transition-colors border border-red-200 active:scale-95 flex flex-col items-center justify-center gap-1">
                        <span class="opacity-70">Saldo + Mora</span>
                        <span class="text-sm"><?= MoneyHelper::formatShort($saldo + $mora) ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Imputación a mora -->
            <?php if ($mora > 0): ?>
            <div x-show="parseFloat(monto) > 0" x-cloak x-transition.opacity class="mb-5 p-4 bg-red-50 rounded-2xl border border-red-100">
                <label class="form-label block text-sm font-bold text-red-900 mb-2">
                    ¿Cuánto del pago va a mora?
                    <span class="text-xs text-red-500 font-medium block mt-0.5">(Máximo <?= MoneyHelper::formatShort($mora) ?>)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-xl font-bold text-red-300">$</span>
                    </div>
                    <input type="number" name="monto_mora" min="0" :max="Math.min(<?= $mora ?>, parseFloat(monto))"
                           step="0.01"
                           class="form-input pl-10 bg-white border-red-200 focus:border-red-500 focus:ring-red-500/20 w-full text-lg font-bold text-red-700"
                           x-model="montoMora"
                           @input="calcular"
                           placeholder="0.00">
                </div>
            </div>
            <?php else: ?>
                <input type="hidden" name="monto_mora" value="0">
            <?php endif; ?>

            <!-- Método de pago -->
            <div class="mb-5">
                <label class="form-label block text-sm font-bold text-slate-700 mb-2">Método de Pago</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="metodo_pago" value="efectivo" class="peer sr-only" checked x-model="metodoPago">
                        <div class="p-3 rounded-xl border-2 border-slate-200 bg-white text-slate-500 font-bold text-sm text-center flex flex-col items-center gap-1 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 transition-all">
                            <i class="isax isax-money-tick text-2xl"></i>
                            Efectivo
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="metodo_pago" value="transferencia" class="peer sr-only" x-model="metodoPago">
                        <div class="p-3 rounded-xl border-2 border-slate-200 bg-white text-slate-500 font-bold text-sm text-center flex flex-col items-center gap-1 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 transition-all">
                            <i class="isax isax-card-tick text-2xl"></i>
                            Transferencia
                        </div>
                    </label>
                </div>
            </div>

            <!-- Resumen dinámico -->
            <div class="bg-slate-900 rounded-2xl p-5 space-y-3 text-white shadow-lg" x-show="parseFloat(monto) > 0" x-cloak x-transition.opacity>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-400 font-medium">Imputado a capital</span>
                    <span class="font-extrabold text-lg" style="font-family: 'Outfit', sans-serif;">$ <span x-text="aCapital"></span></span>
                </div>
                
                <div class="flex justify-between items-center text-sm" x-show="parseFloat(montoMora) > 0">
                    <span class="text-red-400 font-medium">Imputado a mora</span>
                    <span class="font-extrabold text-red-400 text-lg" style="font-family: 'Outfit', sans-serif;">$ <span x-text="montoMora"></span></span>
                </div>
                
                <div class="border-t border-slate-700 pt-3 flex justify-between items-center">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Total a registrar</span>
                    <span class="font-extrabold text-2xl text-emerald-400" style="font-family: 'Outfit', sans-serif;">$ <span x-text="monto"></span></span>
                </div>
                
                <div class="bg-black/20 rounded-xl p-3 text-center mt-2 border border-white/5">
                    <span class="text-xs font-bold flex items-center justify-center gap-1.5" :class="saldoRestante > 0 ? 'text-amber-400' : 'text-emerald-400'">
                        <i class="isax" :class="saldoRestante > 0 ? 'isax-timer-1' : 'isax-tick-circle'"></i>
                        <span x-text="saldoRestante > 0 ? 'Saldo restante en cuota: $' + saldoRestante.toFixed(2) : 'La cuota quedará SALDADA'"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Botones Flotantes (Mobile) -->
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/80 backdrop-blur-md border-t border-slate-200 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] z-40 md:relative md:bg-transparent md:border-0 md:p-0 md:shadow-none md:backdrop-blur-none">
            <div class="max-w-lg mx-auto space-y-3">
                <button type="submit" name="accion" value="registrar"
                        class="btn-primary w-full py-4 text-base shadow-xl shadow-brand-500/30 active:scale-[0.98] transition-transform flex items-center justify-center gap-2">
                    <i class="isax isax-tick-square text-xl"></i> Registrar Cobro
                </button>
                
                <div class="flex gap-3">
                    <button type="submit" name="accion" value="recibo"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-4 rounded-xl transition-colors border border-slate-200 flex items-center justify-center gap-2 text-sm active:scale-[0.98]">
                        <i class="isax isax-printer text-lg"></i> Recibo PDF
                    </button>
                    <a href="<?= url('cobrador/agenda') ?>" 
                       class="flex-1 bg-white hover:bg-slate-50 text-slate-600 font-bold py-3.5 px-4 rounded-xl transition-colors border border-slate-200 flex items-center justify-center gap-2 text-sm active:scale-[0.98]">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Selector de otra cuota del mismo crédito -->
    <?php
    $cuotasPend = array_filter($cuotas, fn($c) => in_array($c['estado'], ['pendiente','parcial','vencida']) && (int)$c['id'] !== (int)$cuota['id']);
    ?>
    <?php if (!empty($cuotasPend)): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden mb-24 md:mb-0">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
            <i class="isax isax-layer text-brand-500"></i>
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Otras cuotas pendientes</h3>
        </div>
        <div class="divide-y divide-slate-50">
            <?php foreach ($cuotasPend as $cu): ?>
            <a href="<?= url('cobrador/pago/' . $credito['id'] . '/' . $cu['id']) ?>"
               class="flex justify-between items-center p-4 hover:bg-brand-50 transition-colors group">
                <div>
                    <span class="block text-sm font-bold text-slate-700 group-hover:text-brand-700">Cuota #<?= $cu['numero_cuota'] ?></span>
                    <span class="block text-[10px] font-medium text-slate-400 uppercase mt-0.5"><i class="isax isax-calendar-1"></i> Vence: <?= DateHelper::formatoArg($cu['fecha_vencimiento']) ?></span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-extrabold text-brand-600" style="font-family: 'Outfit', sans-serif;"><?= MoneyHelper::formatShort((float)$cu['monto']) ?></span>
                    <i class="isax isax-arrow-right-3 text-slate-300 group-hover:text-brand-500"></i>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function pagoForm(saldo, mora) {
    return {
        monto: '',
        montoMora: 0,
        aCapital: 0,
        metodoPago: 'efectivo',
        saldoRestante: saldo,
        calcular() {
            const m = parseFloat(this.monto) || 0;
            const mm = parseFloat(this.montoMora) || 0;
            this.aCapital = (m - mm).toFixed(2);
            this.saldoRestante = Math.max(0, saldo - (m - mm));
        },
        pagarTotal() {
            this.monto = saldo.toFixed(2);
            this.montoMora = 0;
            this.calcular();
        },
        pagarTotalConMora() {
            this.monto = (saldo + mora).toFixed(2);
            this.montoMora = mora.toFixed(2);
            this.calcular();
        }
    }
}
</script>
