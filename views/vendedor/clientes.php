<?php // views/vendedor/clientes.php ?>
<div class="space-y-6 pb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">Directorio de Clientes</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Busca y gestiona tus clientes registrados</p>
        </div>
        <a href="<?= url('vendedor/clientes/nuevo') ?>" class="btn-primary shadow-md shadow-brand-500/20">
            <i class="isax isax-user-add"></i> Nuevo Cliente
        </a>
    </div>

    <!-- Buscador -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="isax isax-search-normal-1 text-slate-400 text-lg"></i>
                </div>
                <input type="text" name="q" value="<?= e($q) ?>"
                       placeholder="Buscar por nombre, DNI o teléfono..."
                       class="form-input pl-12 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full text-base py-3">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary py-3 px-6 sm:flex-none justify-center">
                    Buscar
                </button>
                <?php if ($q): ?>
                    <a href="<?= url('vendedor/clientes') ?>" class="btn-secondary py-3 px-4 bg-slate-50 border-slate-200 hover:bg-slate-100 flex-none" title="Limpiar búsqueda">
                        <i class="isax isax-close-circle"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <?php if (empty($clientes)): ?>
        <div class="bg-white rounded-3xl p-10 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
            <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-5">
                <i class="isax isax-profile-2user text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No se encontraron clientes</h3>
            <p class="text-slate-500 font-medium mb-6">
                <?= $q ? "No hay resultados que coincidan con la búsqueda «{$q}»." : 'Aún no has registrado ningún cliente en el sistema.' ?>
            </p>
            <?php if (!$q): ?>
                <a href="<?= url('vendedor/clientes/nuevo') ?>" class="btn-primary inline-flex">
                    <i class="isax isax-user-add"></i> Agregar el primero
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Contacto</th>
                            <th class="px-6 py-4 hidden md:table-cell">Ubicación</th>
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
                                    <div>
                                        <span class="font-bold text-slate-800 block text-base"><?= e($c['nombre']) ?></span>
                                        <span class="text-xs font-medium text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="isax isax-personalcard"></i> DNI: <?= e($c['dni']) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="text-slate-600 font-medium flex items-center gap-1.5">
                                    <i class="isax isax-call text-slate-400"></i>
                                    <?= e($c['telefono'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                    <i class="isax isax-location"></i> <?= e($c['localidad'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= url('vendedor/clientes/' . $c['id']) ?>"
                                       class="btn-secondary py-1.5 px-3 text-xs bg-white hover:bg-slate-50 border-slate-200">
                                        <i class="isax isax-eye"></i> Perfil
                                    </a>
                                    <a href="<?= url('vendedor/creditos/nuevo?cliente_id=' . $c['id']) ?>"
                                       class="btn-primary py-1.5 px-3 text-xs shadow-sm shadow-brand-500/20">
                                        <i class="isax isax-money-send"></i> Nuevo Crédito
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Mostrando <span class="font-bold text-slate-800"><?= count($clientes) ?></span> cliente(s)</span>
            </div>
        </div>
    <?php endif; ?>
</div>
