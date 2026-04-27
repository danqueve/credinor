<?php // views/admin/usuarios.php
$rolBadge = [
    'admin'    => 'bg-purple-50 text-purple-700 border-purple-200',
    'vendedor' => 'bg-blue-50 text-blue-700 border-blue-200',
    'cobrador' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
];
$rolIcon = [
    'admin'    => 'isax-security-user',
    'vendedor' => 'isax-bag-tick-2',
    'cobrador' => 'isax-money-recive',
];
?>
<div class="space-y-6 pb-10">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Usuarios</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Gestión del personal y accesos a la plataforma</p>
        </div>
        <a href="<?= url('admin/usuarios/nuevo') ?>" class="btn-primary shadow-md shadow-brand-500/20">
            <i class="isax isax-add"></i> Nuevo Usuario
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Rol</th>
                        <th class="px-6 py-4 hidden sm:table-cell">Sucursal</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($usuarios as $u): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group <?= !$u['activo'] ? 'opacity-75 bg-slate-50' : '' ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full <?= $u['activo'] ? 'bg-brand-50 text-brand-600' : 'bg-slate-200 text-slate-500' ?> flex items-center justify-center font-bold text-sm shrink-0">
                                    <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 block"><?= e($u['nombre']) ?></span>
                                    <span class="text-xs font-medium text-slate-500">@<?= e($u['username']) ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-bold <?= $rolBadge[$u['rol']] ?? 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                <i class="isax <?= $rolIcon[$u['rol']] ?? 'isax-user' ?>"></i>
                                <?= ucfirst($u['rol']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell text-slate-600 font-medium">
                            <?= $u['sucursal_nombre'] ? '<i class="isax isax-shop text-slate-400 mr-1 align-middle"></i>' . e($u['sucursal_nombre']) : '<span class="text-slate-400 font-normal italic">Global</span>' ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($u['activo']): ?>
                                <span class="badge-activo inline-flex items-center gap-1.5"><i class="isax isax-verify hidden sm:inline-block"></i> Activo</span>
                            <?php else: ?>
                                <span class="badge inline-flex items-center gap-1.5 bg-slate-100 text-slate-500"><i class="isax isax-slash hidden sm:inline-block"></i> Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST"
                                      action="<?= url('admin/usuarios/' . $u['id'] . '/toggle') ?>"
                                      onsubmit="return confirm('¿Confirmar cambio de estado?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all active:scale-95 <?= $u['activo'] ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white' ?>">
                                        <i class="isax <?= $u['activo'] ? 'isax-close-square' : 'isax-tick-square' ?>"></i>
                                        <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>

                                <a href="<?= url('admin/usuarios/' . $u['id'] . '/editar') ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                    <i class="isax isax-edit-2"></i> Editar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
            <span>Total de <span class="font-bold text-slate-800"><?= count($usuarios) ?></span> usuario(s)</span>
        </div>
    </div>
</div>
