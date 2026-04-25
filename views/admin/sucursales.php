<?php // views/admin/sucursales.php ?>
<div class="space-y-4">

    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-xl font-bold text-gray-900">Sucursales</h1>
        <a href="<?= url('admin/sucursales/nueva') ?>" class="btn-primary">
            ➕ Nueva sucursal
        </a>
    </div>

    <?php if (empty($sucursales)): ?>
        <div class="card text-center py-12">
            <div class="text-4xl mb-2">🏢</div>
            <p class="text-gray-500 font-medium">No hay sucursales registradas.</p>
            <a href="<?= url('admin/sucursales/nueva') ?>" class="btn-primary mt-4 inline-block">
                Crear primera sucursal
            </a>
        </div>
    <?php else: ?>
        <div class="card p-0 overflow-hidden">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="hidden sm:table-cell">Dirección</th>
                        <th class="hidden sm:table-cell">Teléfono</th>
                        <th>Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sucursales as $s): ?>
                    <tr>
                        <td class="font-medium text-gray-900"><?= e($s['nombre']) ?></td>
                        <td class="hidden sm:table-cell text-gray-500 text-sm">
                            <?= e($s['direccion'] ?: '—') ?>
                        </td>
                        <td class="hidden sm:table-cell text-gray-500 text-sm">
                            <?= e($s['telefono'] ?: '—') ?>
                        </td>
                        <td>
                            <?php if ($s['activa']): ?>
                                <span class="badge-activo">Activa</span>
                            <?php else: ?>
                                <span class="badge-rechazado">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= url('admin/sucursales/' . $s['id'] . '/editar') ?>"
                                   class="btn-secondary text-xs py-1 px-2">Editar</a>
                                <form method="POST"
                                      action="<?= url('admin/sucursales/' . $s['id'] . '/toggle') ?>"
                                      onsubmit="return confirm('¿Confirmar cambio de estado?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                            class="btn-secondary text-xs py-1 px-2 <?= $s['activa'] ? 'text-red-600 border-red-200 hover:bg-red-50' : 'text-green-600 border-green-200 hover:bg-green-50' ?>">
                                        <?= $s['activa'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
