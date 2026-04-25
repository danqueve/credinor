<?php // views/admin/usuario_form.php
$titulo   = $accion === 'crear' ? 'Nuevo usuario' : 'Editar usuario';
$action   = $accion === 'crear'
    ? url('admin/usuarios')
    : url('admin/usuarios/' . $usuario['id'] . '/editar');
$roles    = ['vendedor' => 'Vendedor', 'cobrador' => 'Cobrador', 'admin' => 'Administrador'];
?>
<div class="max-w-lg mx-auto">
    <div class="flex items-center gap-3 mb-5">
        <a href="<?= url('admin/usuarios') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-xl font-bold"><?= $titulo ?></h1>
    </div>

    <form method="POST" action="<?= $action ?>" class="card space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="form-label">Nombre completo <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" required class="form-input"
                   value="<?= e($usuario['nombre'] ?? '') ?>"
                   placeholder="Ej: Juan Pérez">
        </div>

        <div>
            <label class="form-label">Usuario <span class="text-red-500">*</span></label>
            <input type="text" name="username" required class="form-input"
                   value="<?= e($usuario['username'] ?? '') ?>"
                   placeholder="Ej: jperez"
                   autocomplete="off"
                   maxlength="50"
                   pattern="[a-zA-Z0-9_\-]+"
                   title="Solo letras, números, guión y guión bajo">
            <p class="text-xs text-gray-400 mt-1">Solo letras, números, _ y -. Sin espacios.</p>
        </div>

        <div>
            <label class="form-label">
                <?= $accion === 'crear' ? 'Contraseña' : 'Nueva contraseña' ?>
                <?= $accion === 'crear' ? '<span class="text-red-500">*</span>' : '' ?>
            </label>
            <input type="password" name="password"
                   <?= $accion === 'crear' ? 'required minlength="6"' : '' ?>
                   class="form-input"
                   autocomplete="new-password"
                   placeholder="<?= $accion === 'editar' ? 'Dejar vacío para no cambiar' : 'Mínimo 6 caracteres' ?>">
        </div>

        <div>
            <label class="form-label">Rol <span class="text-red-500">*</span></label>
            <select name="rol" required class="form-select">
                <?php foreach ($roles as $val => $label): ?>
                    <option value="<?= $val ?>"
                            <?= ($usuario['rol'] ?? 'vendedor') === $val ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Sucursal <span class="text-red-500">*</span></label>
            <select name="sucursal_id" required class="form-select">
                <option value="">Seleccionar...</option>
                <?php foreach ($sucursales as $s): ?>
                    <option value="<?= $s['id'] ?>"
                            <?= ($usuario['sucursal_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($accion === 'editar'): ?>
        <div class="flex items-center gap-3">
            <label class="form-label mb-0">Estado</label>
            <select name="activo" class="form-select w-auto">
                <option value="1" <?= ($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= !($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <?php endif; ?>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary flex-1">
                <?= $accion === 'crear' ? 'Crear usuario' : 'Guardar cambios' ?>
            </button>
            <a href="<?= url('admin/usuarios') ?>" class="btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
