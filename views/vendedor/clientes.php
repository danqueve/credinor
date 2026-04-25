<?php // views/vendedor/clientes.php ?>
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
        <a href="<?= url('vendedor/clientes/nuevo') ?>" class="btn-primary">
            ➕ Nuevo cliente
        </a>
    </div>

    <!-- Buscador -->
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="<?= e($q) ?>"
               placeholder="Buscar por nombre o DNI..."
               class="form-input flex-1">
        <button type="submit" class="btn-secondary">Buscar</button>
        <?php if ($q): ?>
            <a href="<?= url('vendedor/clientes') ?>" class="btn-secondary">✕</a>
        <?php endif; ?>
    </form>

    <!-- Tabla -->
    <?php if (empty($clientes)): ?>
        <div class="card text-center py-10 text-gray-400">
            <div class="text-4xl mb-2">👥</div>
            <p>No se encontraron clientes<?= $q ? " para «{$q}»" : '' ?>.</p>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Localidad</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td class="font-medium"><?= e($c['nombre']) ?></td>
                        <td class="text-gray-500"><?= e($c['dni']) ?></td>
                        <td><?= e($c['telefono'] ?? '—') ?></td>
                        <td class="text-gray-500"><?= e($c['localidad'] ?? '—') ?></td>
                        <td class="text-right">
                            <a href="<?= url('vendedor/clientes/' . $c['id']) ?>"
                               class="btn-secondary text-xs">Ver</a>
                            <a href="<?= url('vendedor/creditos/nuevo?cliente_id=' . $c['id']) ?>"
                               class="btn-primary text-xs ml-1">+ Crédito</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
