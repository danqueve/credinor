<?php // views/admin/usuario_form.php
$titulo   = $accion === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario';
$action   = $accion === 'crear'
    ? url('admin/usuarios')
    : url('admin/usuarios/' . $usuario['id'] . '/editar');
$roles    = ['vendedor' => 'Vendedor', 'cobrador' => 'Cobrador', 'admin' => 'Administrador'];
?>
<div class="max-w-2xl mx-auto space-y-6 pb-10">
    <div class="flex items-center gap-4">
        <a href="<?= url('admin/usuarios') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
            <i class="isax isax-arrow-left-2"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= $titulo ?>
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                <?= $accion === 'crear' ? 'Registra un nuevo integrante al equipo' : 'Actualiza la información de acceso de ' . e($usuario['nombre']) ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
            <i class="isax isax-profile-2user text-8xl text-brand-600"></i>
        </div>

        <form method="POST" action="<?= $action ?>" class="space-y-5 relative z-10">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Nombre completo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-user text-slate-400"></i>
                        </div>
                        <input type="text" name="nombre" required class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                               value="<?= e($usuario['nombre'] ?? '') ?>"
                               placeholder="Ej: Juan Pérez">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Nombre de usuario <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-tag-user text-slate-400"></i>
                        </div>
                        <input type="text" name="username" required class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                               value="<?= e($usuario['username'] ?? '') ?>"
                               placeholder="Ej: jperez"
                               autocomplete="off"
                               maxlength="50"
                               pattern="[a-zA-Z0-9_\-]+"
                               title="Solo letras, números, guión y guión bajo">
                    </div>
                    <p class="text-xs text-slate-400 font-medium mt-1 ml-1 flex items-center gap-1">
                        <i class="isax isax-info-circle"></i> Solo letras, números, _ y -. Sin espacios.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">
                        <?= $accion === 'crear' ? 'Contraseña' : 'Nueva contraseña' ?>
                        <?= $accion === 'crear' ? '<span class="text-red-500">*</span>' : '' ?>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-lock-1 text-slate-400"></i>
                        </div>
                        <input type="password" name="password"
                               <?= $accion === 'crear' ? 'required minlength="6"' : '' ?>
                               class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                               autocomplete="new-password"
                               placeholder="<?= $accion === 'editar' ? 'Dejar vacío para mantener la actual' : 'Mínimo 6 caracteres' ?>">
                    </div>
                </div>

                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Rol <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-key-square text-slate-400"></i>
                        </div>
                        <select name="rol" required class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                            <?php foreach ($roles as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($usuario['rol'] ?? 'vendedor') === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Sucursal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-shop text-slate-400"></i>
                        </div>
                        <select name="sucursal_id" required class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= ($usuario['sucursal_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if ($accion === 'editar'): ?>
                <div class="md:col-span-2 pt-2 border-t border-slate-100">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-2">Estado de la cuenta</label>
                    <div class="flex items-center gap-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="radio" name="activo" value="1" class="sr-only peer" <?= ($usuario['activo'] ?? 1) ? 'checked' : '' ?>>
                            <div class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl peer-checked:bg-emerald-50 peer-checked:border-emerald-200 peer-checked:text-emerald-700 font-medium text-slate-600 transition-all flex items-center gap-2">
                                <i class="isax isax-verify"></i> Activo
                            </div>
                        </label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="radio" name="activo" value="0" class="sr-only peer" <?= !($usuario['activo'] ?? 1) ? 'checked' : '' ?>>
                            <div class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl peer-checked:bg-red-50 peer-checked:border-red-200 peer-checked:text-red-700 font-medium text-slate-600 transition-all flex items-center gap-2">
                                <i class="isax isax-slash"></i> Inactivo
                            </div>
                        </label>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-6 border-t border-slate-100">
                <button type="submit" class="btn-primary sm:flex-1 justify-center py-3">
                    <i class="isax <?= $accion === 'crear' ? 'isax-add' : 'isax-save-2' ?>"></i> 
                    <?= $accion === 'crear' ? 'Crear Usuario' : 'Guardar Cambios' ?>
                </button>
                <a href="<?= url('admin/usuarios') ?>" class="btn-secondary sm:flex-1 justify-center py-3">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
