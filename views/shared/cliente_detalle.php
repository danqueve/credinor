<?php // views/shared/cliente_detalle.php
use App\Helpers\MoneyHelper;
use App\Core\Auth;
?>
<div class="max-w-4xl mx-auto space-y-6 pb-10">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="<?= url(Auth::isAdmin() ? 'admin/clientes' : 'vendedor/clientes') ?>" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-600 hover:border-brand-200 hover:bg-brand-50 transition-all">
                <i class="isax isax-arrow-left-2"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    <?= e($cliente['nombre']) ?>
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                    <i class="isax isax-profile-circle text-slate-400"></i> Cliente #<?= $cliente['id'] ?>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= url('vendedor/clientes/' . $cliente['id'] . '/editar') ?>"
               class="btn-secondary">
                <i class="isax isax-edit-2"></i> Editar
            </a>
            <a href="<?= url('vendedor/creditos/nuevo?cliente_id=' . $cliente['id']) ?>"
               class="btn-primary">
                <i class="isax isax-wallet-add"></i> Nuevo crédito
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Datos personales -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                    <i class="isax isax-profile-2user text-8xl text-brand-600"></i>
                </div>
                
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-6">
                    <i class="isax isax-personalcard text-brand-500"></i> Información Personal
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8 text-sm">
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">DNI</span>
                        <span class="font-medium text-slate-900"><?= e($cliente['dni']) ?></span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Teléfono</span>
                        <?php if ($cliente['telefono']): ?>
                            <a href="https://wa.me/<?= preg_replace('/\D/', '', $cliente['telefono']) ?>"
                               target="_blank" class="inline-flex items-center gap-1.5 font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg transition-colors">
                                <i class="isax isax-whatsapp"></i> <?= e($cliente['telefono']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400">—</span>
                        <?php endif; ?>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Email</span>
                        <span class="font-medium text-slate-900"><?= e($cliente['email'] ?: '—') ?></span>
                    </div>
                    <div class="sm:col-span-2 pt-4 border-t border-slate-100">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Dirección</span>
                        <div class="flex items-start gap-2">
                            <i class="isax isax-location text-brand-500 mt-0.5"></i>
                            <span class="font-medium text-slate-900 leading-relaxed">
                                <?= e($cliente['domicilio'] ?: '—') ?>
                                <?= $cliente['localidad'] ? '<br><span class="text-slate-500 text-xs">' . e($cliente['localidad']) . '</span>' : '' ?>
                            </span>
                        </div>
                    </div>
                    <?php if ($cliente['observaciones']): ?>
                    <div class="sm:col-span-2 bg-amber-50/50 border border-amber-100 rounded-xl p-4 mt-2">
                        <span class="block text-xs font-bold uppercase tracking-wider text-amber-600/70 mb-1 flex items-center gap-1.5">
                            <i class="isax isax-info-circle text-amber-500"></i> Observaciones
                        </span>
                        <p class="font-medium text-amber-900 leading-relaxed"><?= nl2br(e($cliente['observaciones'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Historial de Créditos -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-6">
                    <i class="isax isax-document-text text-brand-500"></i> Historial de Créditos
                </h2>
                
                <?php if (empty($creditos)): ?>
                    <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mx-auto shadow-sm text-slate-300 mb-4">
                            <i class="isax isax-empty-wallet text-3xl"></i>
                        </div>
                        <p class="text-slate-500 font-medium">Sin créditos registrados.</p>
                        <a href="<?= url('vendedor/creditos/nuevo?cliente_id=' . $cliente['id']) ?>" class="text-brand-600 font-bold text-sm mt-2 inline-block hover:underline">Otorgar el primero</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($creditos as $c): ?>
                        <div class="group flex items-center justify-between p-4 rounded-2xl border border-slate-100 hover:border-brand-200 hover:shadow-md transition-all bg-white hover:bg-slate-50/50">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                    <i class="isax isax-money-recive text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-extrabold text-slate-900 text-lg" style="font-family: 'Outfit', sans-serif;">
                                        <?= MoneyHelper::formatShort((float)$c['monto_prestado']) ?>
                                    </p>
                                    <p class="text-xs font-medium text-slate-400 mt-0.5">
                                        Devuelve: <span class="text-slate-600"><?= MoneyHelper::formatShort((float)$c['monto_a_devolver']) ?></span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <?php
                                $b = [
                                    'pendiente_autorizacion' => 'badge-pendiente',
                                    'activo' => 'badge-activo',
                                    'finalizado' => 'badge-finalizado',
                                    'rechazado' => 'badge-rechazado',
                                ];
                                ?>
                                <span class="<?= $b[$c['estado']] ?? 'badge' ?> hidden sm:inline-flex">
                                    <?= ucfirst(str_replace('_', ' ', $c['estado'])) ?>
                                </span>
                                
                                <a href="<?= url('creditos/' . $c['id']) ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold hover:bg-brand-600 hover:text-white transition-all active:scale-95 shrink-0">
                                    <i class="isax isax-arrow-right-3"></i> Ver
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Panel Lateral (Mapa) -->
        <div class="lg:col-span-1 space-y-6">
            <?php if ($cliente['lat'] && $cliente['lng']): ?>
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100/80">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="isax isax-map text-brand-500"></i> Ubicación
                    </h2>
                </div>
                <div id="mapa-cliente" class="w-full h-64 bg-slate-100"></div>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                const map = L.map('mapa-cliente', {zoomControl: false, dragging: false})
                    .setView([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO'
                }).addTo(map);
                
                const customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="w-8 h-8 bg-brand-600 text-white rounded-full flex items-center justify-center shadow-lg border-2 border-white"><i class="isax isax-location"></i></div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                });
                L.marker([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>], {icon: customIcon}).addTo(map);
                </script>
                <div class="p-5 bg-slate-50 border-t border-slate-100 text-center">
                    <a href="https://maps.google.com/?q=<?= $cliente['lat'] ?>,<?= $cliente['lng'] ?>"
                       target="_blank" class="btn-secondary w-full justify-center">
                        <i class="isax isax-routing-2"></i> Abrir en Maps
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-4">
                    <i class="isax isax-map text-3xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Sin ubicación</h3>
                <p class="text-xs text-slate-500 font-medium mb-4">El cliente no tiene coordenadas guardadas.</p>
                <a href="<?= url('vendedor/clientes/' . $cliente['id'] . '/editar') ?>" class="text-brand-600 text-sm font-bold hover:underline">Agregar ubicación</a>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
