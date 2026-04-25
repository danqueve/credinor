<?php // views/admin/autorizar_credito.php
use App\Helpers\MoneyHelper;
use App\Helpers\DateHelper;
?>
<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= url('admin/creditos?estado=pendiente_autorizacion') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-2xl font-bold">Autorizar crédito #<?= $credito['id'] ?></h1>
        <span class="badge-pendiente">Pendiente</span>
    </div>

    <!-- Datos del crédito -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-700 border-b pb-2">👤 Cliente</h2>
            <div class="space-y-1 text-sm">
                <p><span class="text-gray-500">Nombre:</span> <strong><?= e($credito['cliente_nombre']) ?></strong></p>
                <p><span class="text-gray-500">DNI:</span> <?= e($credito['dni']) ?></p>
                <p><span class="text-gray-500">Teléfono:</span> <?= e($credito['telefono'] ?? '—') ?></p>
                <p><span class="text-gray-500">Domicilio:</span> <?= e($credito['domicilio'] ?? '—') ?></p>
                <p><span class="text-gray-500">Sucursal:</span> <?= e($credito['sucursal_nombre']) ?></p>
            </div>
        </div>
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-700 border-b pb-2">💰 Crédito</h2>
            <div class="space-y-1 text-sm">
                <p><span class="text-gray-500">Prestado:</span>
                    <strong class="text-green-700"><?= MoneyHelper::format((float)$credito['monto_prestado']) ?></strong></p>
                <p><span class="text-gray-500">A devolver:</span>
                    <strong class="text-brand-700"><?= MoneyHelper::format((float)$credito['monto_a_devolver']) ?></strong></p>
                <p><span class="text-gray-500">Diferencia:</span>
                    <?= MoneyHelper::format((float)$credito['monto_a_devolver'] - (float)$credito['monto_prestado']) ?></p>
                <p><span class="text-gray-500">Cuotas:</span>
                    <?= $credito['cantidad_cuotas'] ?> × <?= $credito['frecuencia'] ?></p>
                <p><span class="text-gray-500">Inicio:</span>
                    <?= DateHelper::formatoArg($credito['fecha_inicio']) ?></p>
                <p><span class="text-gray-500">Primera cuota:</span>
                    <?= DateHelper::formatoArg($credito['fecha_primera_cuota']) ?></p>
                <p><span class="text-gray-500">Mora:</span>
                    <?= $credito['aplica_mora'] ? 'Sí' : 'No' ?>
                    <?php if ($credito['porcentaje_mora_diaria']): ?>
                        (<?= $credito['porcentaje_mora_diaria'] ?>% diario)
                    <?php else: ?>
                        (usa config global)
                    <?php endif; ?>
                </p>
                <?php if ($credito['observaciones']): ?>
                <p><span class="text-gray-500">Obs:</span> <?= e($credito['observaciones']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview de cuotas -->
    <div class="card">
        <h2 class="font-semibold text-gray-700 border-b pb-2 mb-3">📅 Plan de cuotas (preview)</h2>
        <p class="text-xs text-gray-400 mb-3">Las cuotas se generarán automáticamente al autorizar.</p>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 text-xs">
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
                <div class="bg-gray-50 rounded p-1.5 text-center">
                    <div class="font-medium">#<?= $i+1 ?></div>
                    <div class="text-gray-500"><?= DH::formatoArg($f) ?></div>
                    <div class="text-brand-600">$<?= number_format($mc, 0, ',', '.') ?></div>
                </div>
            <?php endforeach; ?>
            <?php if ($credito['cantidad_cuotas'] > 12): ?>
                <div class="bg-gray-50 rounded p-1.5 text-center text-gray-400 flex items-center justify-center">
                    +<?= $credito['cantidad_cuotas'] - 12 ?> más
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulario de autorización -->
    <div class="card">
        <h2 class="font-semibold text-gray-700 border-b pb-3 mb-4">✅ Autorizar</h2>
        <form method="POST" action="<?= url('admin/creditos/' . $credito['id'] . '/autorizar') ?>">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label for="cobrador_id" class="form-label">Asignar cobrador <span class="text-red-500">*</span></label>
                <select id="cobrador_id" name="cobrador_id" class="form-select" required>
                    <option value="">— Seleccioná un cobrador —</option>
                    <?php foreach ($cobradores as $cob): ?>
                        <option value="<?= $cob['id'] ?>"><?= e($cob['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-success">✅ Autorizar y generar cuotas</button>
            </div>
        </form>
    </div>

    <!-- Formulario de rechazo -->
    <div class="card border-red-100" x-data="{ abierto: false }">
        <button type="button" @click="abierto = !abierto"
                class="text-sm text-red-600 hover:text-red-800 font-medium">
            ❌ Rechazar este crédito
        </button>
        <div x-show="abierto" x-cloak class="mt-4">
            <form method="POST" action="<?= url('admin/creditos/' . $credito['id'] . '/rechazar') ?>">
                <?= csrf_field() ?>
                <label for="motivo" class="form-label">Motivo del rechazo <span class="text-red-500">*</span></label>
                <textarea id="motivo" name="motivo" rows="2" required
                          class="form-input resize-none mb-3"
                          placeholder="Ingresá el motivo..."></textarea>
                <button type="submit" class="btn-danger text-sm">Confirmar rechazo</button>
            </form>
        </div>
    </div>
</div>
