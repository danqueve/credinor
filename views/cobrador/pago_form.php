<?php // views/cobrador/pago_form.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
?>
<div class="max-w-lg mx-auto space-y-4 pb-10"
     x-data="pagoForm(<?= $saldo ?>, <?= $credito['mora_acumulada'] - $credito['mora_pagada'] ?>)">

    <!-- Header cliente -->
    <div class="card bg-gradient-to-r from-brand-600 to-brand-700 text-white">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center font-bold text-xl">
                <?= mb_strtoupper(mb_substr($credito['cliente_nombre'], 0, 1)) ?>
            </div>
            <div>
                <p class="font-bold text-lg"><?= e($credito['cliente_nombre']) ?></p>
                <p class="text-brand-200 text-sm">DNI <?= e($credito['dni']) ?></p>
            </div>
            <?php if ($credito['telefono']): ?>
            <a href="https://wa.me/549<?= preg_replace('/\D/', '', $credito['telefono']) ?>"
               target="_blank"
               class="ml-auto bg-white/20 hover:bg-white/30 rounded-xl p-2 transition-colors">
                💬
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info cuota seleccionada -->
    <div class="card border-l-4 border-brand-400 space-y-2">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">
                Cuota #<?= $cuota['numero_cuota'] ?> de <?= $credito['cantidad_cuotas'] ?>
            </h2>
            <?php if ($cuota['estado'] === 'vencida'): ?>
                <span class="badge-vencida">Vencida</span>
            <?php elseif ($cuota['estado'] === 'parcial'): ?>
                <span class="badge-parcial">Parcial</span>
            <?php else: ?>
                <span class="badge-pendiente">Pendiente</span>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-400 text-xs">Vencimiento</p>
                <p class="font-medium"><?= DateHelper::formatoArg($cuota['fecha_vencimiento']) ?></p>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-gray-400 text-xs">Saldo cuota</p>
                <p class="font-bold text-brand-700"><?= MoneyHelper::format($saldo) ?></p>
            </div>
            <?php $mora = (float)$credito['mora_acumulada'] - (float)$credito['mora_pagada']; ?>
            <?php if ($mora > 0): ?>
            <div class="bg-red-50 rounded-lg p-3 col-span-2">
                <p class="text-red-400 text-xs">Mora pendiente del crédito</p>
                <p class="font-bold text-red-600"><?= MoneyHelper::format($mora) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagos anteriores de esta cuota -->
    <?php if (!empty($pagosAnteriores)): ?>
    <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Pagos anteriores en esta cuota</h3>
        <?php foreach ($pagosAnteriores as $p): ?>
        <div class="flex justify-between text-sm py-1 border-b border-gray-50 last:border-0">
            <span class="text-gray-500"><?= date('d/m H:i', strtotime($p['created_at'])) ?></span>
            <span class="text-green-600 font-medium"><?= MoneyHelper::formatShort((float)$p['monto']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Formulario de pago -->
    <form method="POST" action="<?= url('cobrador/pago/' . $credito['id'] . '/' . $cuota['id']) ?>"
          class="card space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="form-label">Monto recibido <span class="text-red-500">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-2xl text-gray-400">$</span>
                <input type="number" name="monto" min="0.01" step="0.01" required
                       class="form-input pl-8 text-2xl font-bold h-14"
                       x-model="monto"
                       @input="calcular"
                       placeholder="0.00"
                       autofocus>
            </div>
            <!-- Acceso rápido -->
            <div class="flex gap-2 mt-2">
                <button type="button" @click="pagarTotal()" class="btn-secondary text-xs flex-1">
                    Saldo completo (<?= MoneyHelper::formatShort($saldo) ?>)
                </button>
                <?php if ($mora > 0): ?>
                <button type="button" @click="pagarTotalConMora()" class="btn-secondary text-xs flex-1 text-orange-600 border-orange-200">
                    + mora (<?= MoneyHelper::formatShort($saldo + $mora) ?>)
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Imputación a mora -->
        <?php if ($mora > 0): ?>
        <div x-show="parseFloat(monto) > 0">
            <label class="form-label">
                ¿Cuánto va a mora?
                <span class="text-xs text-gray-400">(máx. <?= MoneyHelper::formatShort($mora) ?>)</span>
            </label>
            <input type="number" name="monto_mora" min="0" :max="Math.min(<?= $mora ?>, parseFloat(monto))"
                   step="0.01"
                   class="form-input"
                   x-model="montoMora"
                   @input="calcular"
                   placeholder="0.00">
        </div>
        <?php else: ?>
            <input type="hidden" name="monto_mora" value="0">
        <?php endif; ?>

        <!-- Resumen dinámico -->
        <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm" x-show="parseFloat(monto) > 0">
            <div class="flex justify-between">
                <span class="text-gray-500">A capital</span>
                <span class="font-medium text-brand-700">$ <span x-text="aCapital"></span></span>
            </div>
            <div class="flex justify-between" x-show="parseFloat(montoMora) > 0">
                <span class="text-gray-500">A mora</span>
                <span class="font-medium text-red-600">$ <span x-text="montoMora"></span></span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-2">
                <span class="font-semibold">Total recibido</span>
                <span class="font-bold text-lg">$ <span x-text="monto"></span></span>
            </div>
            <div class="text-xs pt-1" :class="saldoRestante > 0 ? 'text-orange-500' : 'text-green-600'">
                <span x-text="saldoRestante > 0 ? 'Saldo restante: $ ' + saldoRestante.toFixed(2) : '✅ Cuota saldada'"></span>
            </div>
        </div>

        <!-- Botones -->
        <div class="space-y-2">
            <button type="submit" name="accion" value="registrar"
                    class="btn-primary w-full py-3 text-base font-bold">
                ✅ Registrar cobro
            </button>
            <button type="submit" name="accion" value="recibo"
                    class="btn-secondary w-full py-2.5 text-sm">
                🧾 Registrar y ver recibo PDF
            </button>
        </div>

        <a href="<?= url('cobrador/agenda') ?>" class="block text-center text-sm text-gray-400 hover:text-gray-600">
            ← Volver a la agenda
        </a>
    </form>

    <!-- Selector de otra cuota del mismo crédito -->
    <?php
    $cuotasPend = array_filter($cuotas, fn($c) => in_array($c['estado'], ['pendiente','parcial','vencida']) && (int)$c['id'] !== (int)$cuota['id']);
    ?>
    <?php if (!empty($cuotasPend)): ?>
    <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Otras cuotas pendientes</h3>
        <div class="space-y-1">
            <?php foreach ($cuotasPend as $cu): ?>
            <a href="<?= url('cobrador/pago/' . $credito['id'] . '/' . $cu['id']) ?>"
               class="flex justify-between items-center py-2 text-sm hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                <span>Cuota #<?= $cu['numero_cuota'] ?> — <?= DateHelper::formatoArg($cu['fecha_vencimiento']) ?></span>
                <span class="font-medium text-brand-700"><?= MoneyHelper::formatShort((float)$cu['monto']) ?></span>
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
