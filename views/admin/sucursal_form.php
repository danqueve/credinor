<?php // views/admin/sucursal_form.php
$esEditar = $accion === 'editar';
$titulo   = $esEditar ? 'Editar Sucursal' : 'Nueva Sucursal';
$action   = $esEditar
    ? url('admin/sucursales/' . $sucursal['id'] . '/editar')
    : url('admin/sucursales');
?>
<div class="max-w-2xl mx-auto space-y-6 pb-10">

    <div class="flex items-center gap-4">
        <a href="<?= url('admin/sucursales') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
            <i class="isax isax-arrow-left-2"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= $titulo ?>
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                <?= $esEditar ? 'Modificando los datos de la sucursal #' . $sucursal['id'] : 'Registra una nueva sucursal en el sistema' ?>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
            <i class="isax isax-shop text-8xl text-brand-600"></i>
        </div>

        <form method="POST" action="<?= $action ?>" class="space-y-5 relative z-10">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Nombre de la sucursal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-shop text-slate-400"></i>
                        </div>
                        <input type="text" name="nombre"
                               value="<?= e($sucursal['nombre'] ?? '') ?>"
                               class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full" required maxlength="100"
                               placeholder="Ej: Sucursal Centro">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Dirección</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-location text-slate-400"></i>
                        </div>
                        <input type="text" name="direccion"
                               value="<?= e($sucursal['direccion'] ?? '') ?>"
                               class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full" maxlength="200"
                               placeholder="Ej: Av. San Martín 1234">
                    </div>
                </div>

                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Teléfono</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-call text-slate-400"></i>
                        </div>
                        <input type="text" name="telefono"
                               value="<?= e($sucursal['telefono'] ?? '') ?>"
                               class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full" maxlength="20"
                               placeholder="Ej: 2994123456">
                    </div>
                </div>

                <?php if ($esEditar): ?>
                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-1">Estado</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-activity text-slate-400"></i>
                        </div>
                        <select name="activa" class="form-select pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                            <option value="1" <?= ($sucursal['activa'] ?? 1) ? 'selected' : '' ?>>Activa</option>
                            <option value="0" <?= !($sucursal['activa'] ?? 1) ? 'selected' : '' ?>>Inactiva</option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-6 border-t border-slate-100">
                <button type="submit" class="btn-primary sm:flex-1 justify-center py-3">
                    <i class="isax <?= $esEditar ? 'isax-save-2' : 'isax-add' ?>"></i> 
                    <?= $esEditar ? 'Guardar Cambios' : 'Crear Sucursal' ?>
                </button>
                <a href="<?= url('admin/sucursales') ?>" class="btn-secondary sm:flex-1 justify-center py-3">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

</div>
