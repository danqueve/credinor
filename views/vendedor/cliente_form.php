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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="dni" class="form-label block text-sm font-bold text-slate-700 mb-2">DNI <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-personalcard text-slate-400"></i>
                            </div>
                            <input id="dni" type="text" name="dni" required
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['dni'] ?? '') ?>"
                                   placeholder="Ej: 12345678">
                        </div>
                    </div>
                    <div>
                        <label for="nombre" class="form-label block text-sm font-bold text-slate-700 mb-2">Nombre Completo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-user text-slate-400"></i>
                            </div>
                            <input id="nombre" type="text" name="nombre" required
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['nombre'] ?? '') ?>"
                                   placeholder="Ej: Juan Pérez">
                        </div>
                    </div>
                    <div>
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
                    <div>
                        <label for="email" class="form-label block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="isax isax-sms text-slate-400"></i>
                            </div>
                            <input id="email" type="email" name="email"
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['email'] ?? '') ?>"
                                   placeholder="Ej: juan@mail.com">
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
                            <input id="domicilio-input" type="text" name="domicilio"
                                   class="form-input pl-10 bg-slate-50 border-slate-200 focus:bg-white focus:border-brand-500 w-full"
                                   value="<?= e($cliente['domicilio'] ?? '') ?>"
                                   placeholder="Ej: Calle 123, Barrio Centro">
                        </div>
                    </div>
                    <div>
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

                <!-- Mapa Leaflet -->
                <div>
                    <label class="form-label block text-sm font-bold text-slate-700 mb-2">
                        Geolocalización
                        <span class="text-xs text-slate-500 font-normal ml-1">(Opcional. Haz clic en el mapa para ajustar)</span>
                    </label>
                    <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-inner">
                        <div id="mapa" class="w-full h-64 z-0"></div>
                    </div>
                    <input type="hidden" name="lat" id="lat" value="<?= e($cliente['lat'] ?? '') ?>">
                    <input type="hidden" name="lng" id="lng" value="<?= e($cliente['lng'] ?? '') ?>">
                    <div class="mt-2 flex items-center gap-2 text-xs font-bold text-slate-500 bg-slate-50 w-fit px-3 py-1.5 rounded-lg border border-slate-100">
                        <i class="isax isax-gps text-brand-500 text-lg"></i>
                        <span id="coords-label">
                            <?php if (!empty($cliente['lat'])): ?>
                                Lat: <?= $cliente['lat'] ?>, Lng: <?= $cliente['lng'] ?>
                            <?php else: ?>
                                Sin ubicación marcada
                            <?php endif; ?>
                        </span>
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

<!-- Leaflet CSS + JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const latInicial = parseFloat(document.getElementById('lat').value) || -38.9516;
    const lngInicial = parseFloat(document.getElementById('lng').value) || -68.0591;
    const zoom       = document.getElementById('lat').value ? 15 : 12;

    const map = L.map('mapa').setView([latInicial, lngInicial], zoom);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
    }).addTo(map);

    let marker = null;

    // Crear icono personalizado
    const customIcon = L.divIcon({
        className: 'custom-leaflet-marker',
        html: '<div style="background-color: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 6px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    if (document.getElementById('lat').value) {
        marker = L.marker([latInicial, lngInicial], {icon: customIcon}).addTo(map);
    }

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        document.getElementById('lat').value = lat.toFixed(7);
        document.getElementById('lng').value = lng.toFixed(7);
        document.getElementById('coords-label').innerHTML =
            'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);

        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng, {icon: customIcon}).addTo(map);
    });

    // Geolocalizar el domicilio con Nominatim al salir del campo
    document.getElementById('domicilio-input').addEventListener('blur', function () {
        const q = this.value.trim();
        if (!q || document.getElementById('lat').value) return;
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q + ', Argentina'))
            .then(r => r.json())
            .then(data => {
                if (data.length) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    document.getElementById('lat').value = lat.toFixed(7);
                    document.getElementById('lng').value = lng.toFixed(7);
                    document.getElementById('coords-label').innerHTML =
                        'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6) + ' <span class="text-emerald-500">(geocodificado)</span>';
                    map.setView([lat, lng], 15);
                    if (marker) marker.setLatLng([lat, lng]);
                    else marker = L.marker([lat, lng], {icon: customIcon}).addTo(map);
                }
            }).catch(() => {});
    });
})();
</script>
