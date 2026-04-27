<?php // views/vendedor/cliente_form.php
$esEdicion = $accion === 'editar';
$titulo    = $esEdicion ? 'Editar Cliente' : 'Nuevo Cliente';
$action    = $esEdicion
    ? url('vendedor/clientes/' . $cliente['id'] . '/editar')
    : url('vendedor/clientes');
?>
<div class="max-w-3xl mx-auto space-y-6 pb-10">
    <div class="flex items-center gap-4">
        <a href="<?= url('vendedor/clientes') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
            <i class="isax isax-arrow-left-2"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                <?= $titulo ?>
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-1">
                <?= $esEdicion ? 'Actualiza los datos personales y de contacto' : 'Registra un nuevo cliente para poder otorgarle créditos' ?>
            </p>
        </div>
    </div>

    <form method="POST" action="<?= $action ?>" class="space-y-6 relative" novalidate>
        <?= csrf_field() ?>

        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <i class="isax isax-personalcard text-8xl text-brand-600"></i>
            </div>

            <div class="space-y-6 relative z-10">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
                    <i class="isax isax-user text-brand-500"></i> Información Personal
                </h2>

                <?php if (\App\Core\Auth::isAdmin() && !empty($sucursales)): ?>
                <div class="mb-5">
                    <label for="sucursal_id" class="form-label block text-sm font-bold text-slate-700 mb-2">
                        Sucursal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="isax isax-building text-slate-400"></i>
                        </div>
                        <select id="sucursal_id" name="sucursal_id" required
                                class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full">
                            <option value="">— Seleccionar sucursal —</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?= $s['id'] ?>"
                                    <?= isset($cliente['sucursal_id']) && (int)$cliente['sucursal_id'] === (int)$s['id'] ? 'selected' : '' ?>>
                                    <?= e($s['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="border-t border-slate-100 my-2"></div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="dni" class="form-label block text-sm font-bold text-slate-700 mb-2">DNI <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-personalcard text-slate-400"></i>
                            </div>
                            <input id="dni" type="text" name="dni" required
                                   class="form-input pl-10 bg-slate-50 focus:bg-white w-full <?= has_error('dni') ? 'border-red-400 focus:border-red-500' : 'border-slate-200 focus:border-brand-500' ?>"
                                   value="<?= old('dni', $cliente['dni'] ?? '') ?>"
                                   placeholder="Ej: 12345678">
                        </div>
                        <?php if (has_error('dni')): ?>
                        <p class="text-xs font-medium text-red-600 mt-1.5 flex items-center gap-1"><i class="isax isax-warning-2"></i> <?= e(form_error('dni')) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="nombre" class="form-label block text-sm font-bold text-slate-700 mb-2">Nombre Completo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-user text-slate-400"></i>
                            </div>
                            <input id="nombre" type="text" name="nombre" required
                                   class="form-input pl-10 bg-slate-50 focus:bg-white w-full <?= has_error('nombre') ? 'border-red-400 focus:border-red-500' : 'border-slate-200 focus:border-brand-500' ?>"
                                   value="<?= old('nombre', $cliente['nombre'] ?? '') ?>"
                                   placeholder="Ej: Juan Pérez">
                        </div>
                        <?php if (has_error('nombre')): ?>
                        <p class="text-xs font-medium text-red-600 mt-1.5 flex items-center gap-1"><i class="isax isax-warning-2"></i> <?= e(form_error('nombre')) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="md:col-span-2">
                        <label for="telefono" class="form-label block text-sm font-bold text-slate-700 mb-2">Teléfono</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-call text-slate-400"></i>
                            </div>
                            <input id="telefono" type="tel" name="telefono"
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['telefono'] ?? '') ?>"
                                   placeholder="Ej: 299-4001234">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-2">
                    <i class="isax isax-location text-brand-500"></i> Ubicación
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label for="domicilio" class="form-label block text-sm font-bold text-slate-700 mb-2">Domicilio</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-home-2 text-slate-400"></i>
                            </div>
                            <input id="domicilio" type="text" name="domicilio"
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['domicilio'] ?? '') ?>"
                                   placeholder="Ej: Calle 123, Barrio Centro">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="localidad" class="form-label block text-sm font-bold text-slate-700 mb-2">Localidad</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-buildings text-slate-400"></i>
                            </div>
                            <input id="localidad" type="text" name="localidad"
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['localidad'] ?? '') ?>"
                                   placeholder="Ej: Neuquén">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 my-6"></div>

                <div>
                    <label for="observaciones" class="form-label block text-sm font-bold text-slate-700 mb-2">Observaciones</label>
                    <div class="relative">
                        <div class="absolute top-3 left-3.5 flex items-start pointer-events-none">
                            <i class="isax isax-info-circle text-slate-400"></i>
                        </div>
                        <textarea id="observaciones" name="observaciones" rows="3"
                                  class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full resize-none"
                                  placeholder="Notas internas adicionales..."><?= e($cliente['observaciones'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="btn-primary sm:flex-1 justify-center py-4 text-base shadow-lg shadow-brand-500/30">
                <i class="isax <?= $esEdicion ? 'isax-save-2' : 'isax-add' ?>"></i>
                <?= $esEdicion ? 'Guardar Cambios' : 'Registrar Cliente' ?>
            </button>
            <a href="<?= url('vendedor/clientes') ?>" class="btn-secondary sm:flex-1 justify-center py-4 bg-white hover:bg-slate-50 border-slate-200">
                Cancelar
            </a>
        </div>
    </form>
</div>
