<?php // views/vendedor/credito_form.php
use App\Helpers\MoneyHelper;
?>
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= url('vendedor/creditos') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-2xl font-bold">Nueva solicitud de crédito</h1>
    </div>

    <form method="POST" action="<?= url('vendedor/creditos') ?>"
          class="card space-y-5" novalidate
          x-data="creditoForm()" @submit="calcular">
        <?= csrf_field() ?>

        <!-- Cliente -->
        <div>
            <label class="form-label">Cliente <span class="text-red-500">*</span></label>
            <?php if ($cliente): ?>
                <div class="flex items-center gap-3 p-3 bg-brand-50 rounded-lg border border-brand-200">
                    <span class="text-brand-700 font-medium">👤 <?= e($cliente['nombre']) ?></span>
                    <span class="text-xs text-gray-500">DNI <?= e($cliente['dni']) ?></span>
                    <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">
                    <a href="<?= url('vendedor/creditos/nuevo') ?>" class="ml-auto text-xs text-gray-400 hover:text-gray-600">cambiar</a>
                </div>
            <?php else: ?>
                <select name="cliente_id" class="form-select" required>
                    <option value="">— Seleccioná un cliente —</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?> — DNI <?= e($c['dni']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- Montos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="monto_prestado" class="form-label">Monto prestado <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input id="monto_prestado" type="number" name="monto_prestado" min="1" step="0.01" required
                           class="form-input pl-7"
                           x-model="montoPrestado"
                           @input="calcularDiferencia"
                           placeholder="50000">
                </div>
            </div>
            <div>
                <label for="monto_a_devolver" class="form-label">Monto a devolver <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                    <input id="monto_a_devolver" type="number" name="monto_a_devolver" min="1" step="0.01" required
                           class="form-input pl-7"
                           x-model="montoDevolver"
                           @input="calcularDiferencia"
                           placeholder="60000">
                </div>
            </div>
        </div>

        <!-- Diferencia calculada -->
        <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm flex flex-wrap gap-4" x-show="montoPrestado > 0">
            <span>💰 Diferencia: <strong x-text="'$ ' + diferencia"></strong></span>
            <span>📈 Interés: <strong x-text="porcentaje + '%'"></strong></span>
            <span>📋 Por cuota: <strong x-text="'$ ' + montoCuota"></strong></span>
        </div>

        <!-- Cuotas y frecuencia -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="cantidad_cuotas" class="form-label">Cantidad de cuotas <span class="text-red-500">*</span></label>
                <input id="cantidad_cuotas" type="number" name="cantidad_cuotas" min="1" max="365" required
                       class="form-input"
                       x-model="cantCuotas"
                       @input="calcularDiferencia"
                       placeholder="12">
            </div>
            <div>
                <label for="frecuencia" class="form-label">Frecuencia <span class="text-red-500">*</span></label>
                <select id="frecuencia" name="frecuencia" class="form-select" required>
                    <option value="semanal">Semanal</option>
                    <option value="quincenal">Quincenal</option>
                    <option value="mensual">Mensual</option>
                    <option value="diaria">Diaria</option>
                </select>
            </div>
        </div>

        <!-- Fechas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="fecha_inicio" class="form-label">Fecha de inicio <span class="text-red-500">*</span></label>
                <input id="fecha_inicio" type="date" name="fecha_inicio" required
                       class="form-input"
                       value="<?= date('Y-m-d') ?>">
            </div>
            <div>
                <label for="fecha_primera_cuota" class="form-label">Primera cuota <span class="text-red-500">*</span></label>
                <input id="fecha_primera_cuota" type="date" name="fecha_primera_cuota" required
                       class="form-input"
                       value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
            </div>
        </div>

        <!-- Mora -->
        <div class="rounded-lg border border-gray-200 p-4 space-y-3" x-data="{ aplica: true }">
            <div class="flex items-center gap-2">
                <input id="aplica_mora" type="checkbox" name="aplica_mora" value="1"
                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                       x-model="aplica" checked>
                <label for="aplica_mora" class="text-sm font-medium text-gray-700">Aplica mora diaria</label>
            </div>
            <div x-show="aplica">
                <label for="porcentaje_mora" class="form-label text-xs">% mora diaria personalizado (vacío = usa config global)</label>
                <input id="porcentaje_mora" type="number" name="porcentaje_mora_diaria"
                       min="0" max="5" step="0.01"
                       class="form-input w-32 text-sm"
                       placeholder="Ej: 0.10">
            </div>
        </div>

        <!-- Garante -->
        <div>
            <label for="garante_id" class="form-label">Garante (opcional)</label>
            <select id="garante_id" name="garante_id" class="form-select">
                <option value="">— Sin garante —</option>
                <?php foreach ($garantes as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= e($g['nombre']) ?> — DNI <?= e($g['dni']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Observaciones -->
        <div>
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="2"
                      class="form-input resize-none"
                      placeholder="Notas adicionales..."></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">✅ Enviar solicitud</button>
            <a href="<?= url('vendedor/creditos') ?>" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
function creditoForm() {
    return {
        montoPrestado: '',
        montoDevolver: '',
        cantCuotas: '',
        diferencia: '0',
        porcentaje: '0.00',
        montoCuota: '0',
        calcularDiferencia() {
            const p = parseFloat(this.montoPrestado) || 0;
            const d = parseFloat(this.montoDevolver) || 0;
            const c = parseInt(this.cantCuotas) || 1;
            this.diferencia = (d - p).toLocaleString('es-AR', {minimumFractionDigits: 2});
            this.porcentaje = p > 0 ? (((d - p) / p) * 100).toFixed(2) : '0.00';
            this.montoCuota = c > 0 ? Math.round(d / c).toLocaleString('es-AR') : '0';
        }
    }
}
</script>
