<?php // views/admin/sucursal_form.php
$esEditar = $accion === 'editar';
$titulo   = $esEditar ? 'Editar sucursal' : 'Nueva sucursal';
$action   = $esEditar
    ? url('admin/sucursales/' . $sucursal['id'] . '/editar')
    : url('admin/sucursales');
?>
<div class="max-w-lg mx-auto space-y-4">

    <div class="flex items-center gap-3">
        <a href="<?= url('admin/sucursales') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-xl font-bold text-gray-900"><?= $titulo ?></h1>
    </div>

    <div class="card">
        <form method="POST" action="<?= $action ?>" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="form-label">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="nombre"
                       value="<?= e($sucursal['nombre'] ?? '') ?>"
                       class="form-input" required maxlength="100"
                       placeholder="Ej: Sucursal Centro">
            </div>

            <div>
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion"
                       value="<?= e($sucursal['direccion'] ?? '') ?>"
                       class="form-input" maxlength="200"
                       placeholder="Ej: Av. San Martín 1234">
            </div>

            <div>
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono"
                       value="<?= e($sucursal['telefono'] ?? '') ?>"
                       class="form-input" maxlength="20"
                       placeholder="Ej: 2994123456">
            </div>

            <?php if ($esEditar): ?>
            <div>
                <label class="form-label">Estado</label>
                <select name="activa" class="form-input">
                    <option value="1" <?= ($sucursal['activa'] ?? 1) ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= !($sucursal['activa'] ?? 1) ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <?= $esEditar ? '💾 Guardar cambios' : '➕ Crear sucursal' ?>
                </button>
                <a href="<?= url('admin/sucursales') ?>" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

</div>
