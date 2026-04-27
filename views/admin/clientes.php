<?php // views/admin/clientes.php ?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Clientes</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Directorio global de clientes activos</p>
        </div>
        <a href="<?= url('vendedor/clientes/nuevo') ?>" class="btn-primary shrink-0 shadow-md shadow-brand-500/20">
            <i class="isax isax-add"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Buscador -->
    <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="isax isax-search-normal-1 text-slate-400"></i>
                </div>
                <input type="text" name="q" value="<?= e($q) ?>"
                       placeholder="Buscar por nombre o DNI..."
                       class="form-input pl-10 w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary sm:w-auto w-full justify-center">Buscar</button>
                <?php if ($q): ?>
                    <a href="<?= url('admin/clientes') ?>" class="btn-secondary px-3" title="Limpiar búsqueda">
                        <i class="isax isax-close-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($clientes)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-profile-2user text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No se encontraron clientes</h3>
            <p class="text-slate-500 font-medium">
                <?= $q ? 'No hay resultados para la búsqueda «' . e($q) . '».' : 'Aún no hay clientes registrados en la plataforma.' ?>
            </p>
            <?php if ($q): ?>
                <a href="<?= url('admin/clientes') ?>" class="btn-secondary mt-6 inline-flex">Ver todos los clientes</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">DNI</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Teléfono</th>
                            <th class="px-6 py-4 hidden md:table-cell">Sucursal</th>
                            <th class="px-6 py-4 hidden lg:table-cell">Localidad</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($clientes as $c): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-sm shrink-0">
                                        <?= strtoupper(substr($c['nombre'], 0, 1)) ?>
                                    </div>
                                    <span class="font-bold text-slate-800"><?= e($c['nombre']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium"><?= e($c['dni']) ?></td>
                            <td class="px-6 py-4 hidden sm:table-cell text-slate-600 font-medium">
                                <?= $c['telefono'] ? '<i class="isax isax-call text-slate-400 mr-1 align-middle"></i>' . e($c['telefono']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-shop"></i> <?= e($c['sucursal_nombre'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-slate-500 font-medium">
                                <?= $c['localidad'] ? '<i class="isax isax-location text-slate-400 mr-1 align-middle"></i>' . e($c['localidad']) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= url('vendedor/clientes/' . $c['id']) ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95">
                                    <i class="isax isax-arrow-right-3"></i> Ver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Mostrando <span class="font-bold text-slate-800"><?= count($clientes) ?></span> cliente<?= count($clientes) !== 1 ? 's' : '' ?></span>
                <!-- Aquí podría ir una paginación en el futuro -->
            </div>
        </div>
    <?php endif; ?>
</div>
