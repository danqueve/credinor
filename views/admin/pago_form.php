<?php // views/admin/pago_form.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
?>
<div class="max-w-2xl mx-auto space-y-6 pb-10"
     x-data="pagoAdminForm(<?= $saldo ?>, <?= (float)$credito['mora_acumulada'] - (float)$credito['mora_pagada'] ?>)">

    <!-- Encabezado -->
    <div class="flex items-center gap-4">
        <a href="<?= url('creditos/' . $credito['id']) ?>"
           class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
            <i class="isax isax-arrow-left-2"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                Registrar Pago
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                Crédito #<?= $credito['id'] ?> — <?= e($credito['cliente_nombre']) ?>
            </p>
        </div>
    </div>

    <!-- Aviso admin -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
        <i class="isax isax-warning-2 text-amber-500 text-xl shrink-0 mt-0.5"></i>
        <div class="text-sm text-amber-800">
            <span class="font-bold block mb-0.5">Pago manual por administrador</span>
            El pago quedará <strong>confirmado directamente</strong> y se atribuirá a
            <strong><?= e($credito['cobrador_nombre'] ?? 'cobrador asignado') ?></strong>.
            No pasará por el flujo de rendición de caja.
        </div>
    </div>

    <!-- Info cuota -->
    <?php
        $isVencida = $cuota['estado'] === 'vencida';
        $mora      = (float)$credito['mora_acumulada'] - (float)$credito['mora_pagada'];
    ?>
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1.5 h-full <?= $isVencida ? 'bg-red-500' : 'bg-brand-500' ?>"></div>

        <div class="flex justify-between items-start mb-5 pl-2">
            <div>
                <h2 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="isax isax-receipt-item text-slate-400"></i>
                    Cuota #<?= $cuota['numero_cuota'] ?>
                    <span class="text-slate-400 font-medium text-sm">/ <?= $credito['cantidad_cuotas'] ?></span>
                </h2>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1 mt-1">
                    <i class="isax isax-calendar-1"></i> Vence: <?= DateHelper::formatoArg($cuota['fecha_vencimiento']) ?>
                </p>
            </div>
            <?php if ($isVencida): ?>
                <span class="badge-vencida">Vencida</span>
            <?php elseif ($cuota['estado'] === 'parcial'): ?>
                <span class="badge-parcial">Parcial</span>
            <?php else: ?>
                <span class="badge-pendiente">Pendiente</span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-3 pl-2">
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Saldo de Cuota</span>
                <span class="block text-2xl font-extrabold text-brand-600" style="font-family: 'Outfit', sans-serif;">
                    <?= MoneyHelper::format($saldo) ?>
                </span>
            </div>
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

    <!-- Pagos anteriores -->
    <?php if (!empty($pagosAnteriores)): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
            <i class="isax isax-clock text-brand-500"></i>
            <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Pagos anteriores en esta cuota</h3>
        </div>
        <div class="divide-y divide-slate-50">
            <?php foreach ($pagosAnteriores as $p): ?>
            <div class="flex justify-between items-center text-sm p-4">
                <span class="text-slate-500 font-medium text-xs flex items-center gap-1.5">
                    <i class="isax isax-calendar-tick text-slate-400"></i>
                    <?= date('d/m/y H:i', strtotime($p['created_at'])) ?>
                </span>
                <span class="font-extrabold text-emerald-600" style="font-family: 'Outfit', sans-serif;">
                    + <?= MoneyHelper::formatShort((float)$p['monto']) ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form method="POST"
          action="<?= url('admin/creditos/' . $credito['id'] . '/pago/' . $cuota['id']) ?>"
          class="space-y-5">
        <?= csrf_field() ?>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] space-y-5">

            <!-- Monto -->
            <div>
                <label class="form-label block text-sm font-bold text-slate-700 mb-2">
                    Monto a Registrar <span class="text-red-500">*</span>
                </label>
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
                <div class="flex gap-2 mt-3">
                    <button type="button" @click="pagarTotal()"
                            class="flex-1 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold py-3 px-2 rounded-xl text-xs transition-colors border border-brand-200 active:scale-95 flex flex-col items-center gap-1">
                        <span class="opacity-70">Saldo Completo</span>
                        <span class="text-sm"><?= MoneyHelper::formatShort($saldo) ?></span>
                    </button>
                    <?php if ($mora > 0): ?>
                    <button type="button" @click="pagarTotalConMora()"
                            class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold py-3 px-2 rounded-xl text-xs transition-colors border border-red-200 active:scale-95 flex flex-col items-center gap-1">
                        <span class="opacity-70">Saldo + Mora</span>
                        <span class="text-sm"><?= MoneyHelper::formatShort($saldo + $mora) ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Imputación a mora -->
            <?php if ($mora > 0): ?>
            <div x-show="parseFloat(monto) > 0" x-cloak x-transition.opacity class="p-4 bg-red-50 rounded-2xl border border-red-100">
                <label class="form-label block text-sm font-bold text-red-900 mb-2">
                    ¿Cuánto del pago va a mora?
                    <span class="text-xs text-red-500 font-medium block mt-0.5">(Máximo <?= MoneyHelper::formatShort($mora) ?>)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-xl font-bold text-red-300">$</span>
                    </div>
                    <input type="number" name="monto_mora" min="0"
                           :max="Math.min(<?= $mora ?>, parseFloat(monto))"
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
            <div>
                <label class="form-label block text-sm font-bold text-slate-700 mb-2">Método de Pago</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="metodo_pago" value="efectivo" class="peer sr-only" checked>
                        <div class="p-3 rounded-xl border-2 border-slate-200 bg-white text-slate-500 font-bold text-sm text-center flex flex-col items-center gap-1 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 transition-all">
                            <i class="isax isax-money-tick text-2xl"></i>
                            Efectivo
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="metodo_pago" value="transferencia" class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 border-slate-200 bg-white text-slate-500 font-bold text-sm text-center flex flex-col items-center gap-1 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-700 transition-all">
                            <i class="isax isax-card-tick text-2xl"></i>
                            Transferencia
                        </div>
                    </label>
                </div>
            </div>

            <!-- Resumen dinámico -->
            <div class="bg-slate-900 rounded-2xl p-5 space-y-3 text-white" x-show="parseFloat(monto) > 0" x-cloak x-transition.opacity>
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
                <div class="bg-black/20 rounded-xl p-3 text-center border border-white/5">
                    <span class="text-xs font-bold flex items-center justify-center gap-1.5"
                          :class="saldoRestante > 0 ? 'text-amber-400' : 'text-emerald-400'">
                        <i class="isax" :class="saldoRestante > 0 ? 'isax-timer-1' : 'isax-tick-circle'"></i>
                        <span x-text="saldoRestante > 0 ? 'Saldo restante en cuota: $' + saldoRestante.toFixed(2) : 'La cuota quedará SALDADA'"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="btn-primary sm:flex-1 justify-center py-4 text-base shadow-lg shadow-brand-500/30">
                <i class="isax isax-tick-square"></i> Confirmar Pago
            </button>
            <a href="<?= url('creditos/' . $credito['id']) ?>"
               class="btn-secondary sm:flex-1 justify-center py-4 bg-white hover:bg-slate-50 border-slate-200">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
function pagoAdminForm(saldo, mora) {
    return {
        monto: '',
        montoMora: 0,
        aCapital: 0,
        saldoRestante: saldo,
        calcular() {
            const m  = parseFloat(this.monto)  || 0;
            const mm = parseFloat(this.montoMora) || 0;
            this.aCapital     = (m - mm).toFixed(2);
            this.saldoRestante = Math.max(0, saldo - (m - mm));
        },
        pagarTotal() {
            this.monto    = saldo.toFixed(2);
            this.montoMora = 0;
            this.calcular();
        },
        pagarTotalConMora() {
            this.monto    = (saldo + mora).toFixed(2);
            this.montoMora = mora.toFixed(2);
            this.calcular();
        },
    }
}
</script>
