<?php // views/admin/sucursales.php ?>
<div class="space-y-6 pb-10">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Sucursales</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Gestión de puntos de operación físicos</p>
        </div>
        <a href="<?= url('admin/sucursales/nueva') ?>" class="btn-primary shadow-md shadow-brand-500/20">
            <i class="isax isax-add"></i> Nueva Sucursal
        </a>
    </div>

    <?php if (empty($sucursales)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-shop text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No hay sucursales</h3>
            <p class="text-slate-500 font-medium mb-6">Aún no has registrado ninguna sucursal operativa.</p>
            <a href="<?= url('admin/sucursales/nueva') ?>" class="btn-primary inline-flex">
                <i class="isax isax-add"></i> Crear la primera
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Dirección</th>
                            <th class="px-6 py-4 hidden md:table-cell">Teléfono</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($sucursales as $s): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                        <i class="isax isax-shop text-xl"></i>
                                    </div>
                                    <span class="font-bold text-slate-800 text-base"><?= e($s['nombre']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell text-slate-600 font-medium">
                                <?= $s['direccion'] ? '<i class="isax isax-location text-slate-400 mr-1 align-middle"></i>' . e($s['direccion']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-slate-600 font-medium">
                                <?= $s['telefono'] ? '<i class="isax isax-call text-slate-400 mr-1 align-middle"></i>' . e($s['telefono']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($s['activa']): ?>
                                    <span class="badge-activo inline-flex items-center gap-1.5"><i class="isax isax-verify hidden sm:inline-block"></i> Activa</span>
                                <?php else: ?>
                                    <span class="badge-rechazado inline-flex items-center gap-1.5"><i class="isax isax-close-circle hidden sm:inline-block"></i> Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST"
                                          action="<?= url('admin/sucursales/' . $s['id'] . '/toggle') ?>"
                                          onsubmit="return confirm('¿Confirmar cambio de estado?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" title="<?= $s['activa'] ? 'Desactivar' : 'Activar' ?>"
                                                class="w-8 h-8 rounded-full flex items-center justify-center transition-colors <?= $s['activa'] ? 'bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600' : 'bg-slate-50 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600' ?>">
                                            <i class="isax <?= $s['activa'] ? 'isax-minus-cirlce' : 'isax-play-circle' ?>"></i>
                                        </button>
                                    </form>
                                    
                                    <a href="<?= url('admin/sucursales/' . $s['id'] . '/editar') ?>"
                                       class="w-8 h-8 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center hover:bg-brand-50 hover:text-brand-600 transition-colors" title="Editar">
                                        <i class="isax isax-edit-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Total de <span class="font-bold text-slate-800"><?= count($sucursales) ?></span> sucursal(es)</span>
            </div>
        </div>
    <?php endif; ?>

</div>
