<?php // views/admin/usuarios.php
$rolBadge = [
    'admin'    => 'badge bg-purple-100 text-purple-800',
    'vendedor' => 'badge bg-blue-100 text-blue-800',
    'cobrador' => 'badge bg-green-100 text-green-800',
];
?>
<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-2xl font-bold">👤 Usuarios</h1>
        <a href="<?= url('admin/usuarios/nuevo') ?>" class="btn-primary text-sm">+ Nuevo usuario</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr class="<?= !$u['activo'] ? 'opacity-50' : '' ?>">
                    <td class="font-medium"><?= e($u['nombre']) ?></td>
                    <td class="text-gray-500 text-sm"><?= e($u['username']) ?></td>
                    <td><span class="<?= $rolBadge[$u['rol']] ?? 'badge' ?>"><?= ucfirst($u['rol']) ?></span></td>
                    <td class="text-gray-400 text-sm"><?= e($u['sucursal_nombre'] ?? '—') ?></td>
                    <td>
                        <span class="<?= $u['activo'] ? 'badge-activo' : 'badge bg-gray-100 text-gray-500' ?>">
                            <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <a href="<?= url('admin/usuarios/' . $u['id'] . '/editar') ?>"
                           class="btn-secondary text-xs">Editar</a>
                        <form method="POST"
                              action="<?= url('admin/usuarios/' . $u['id'] . '/toggle') ?>"
                              onsubmit="return confirm('¿Confirmar cambio de estado?')">
                            <?= csrf_field() ?>
                            <button type="submit"
                                    class="<?= $u['activo'] ? 'btn-danger' : 'btn-success' ?> text-xs py-1 px-2">
                                <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
