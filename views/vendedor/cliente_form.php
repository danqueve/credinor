<?php // views/vendedor/cliente_form.php
$esEdicion = $accion === 'editar';
$titulo    = $esEdicion ? 'Editar cliente' : 'Nuevo cliente';
$action    = $esEdicion
    ? url('vendedor/clientes/' . $cliente['id'] . '/editar')
    : url('vendedor/clientes');
?>
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= url('vendedor/clientes') ?>" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-2xl font-bold"><?= $titulo ?></h1>
    </div>

    <form method="POST" action="<?= $action ?>" class="card space-y-4" novalidate>
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="dni" class="form-label">DNI <span class="text-red-500">*</span></label>
                <input id="dni" type="text" name="dni" required
                       class="form-input"
                       value="<?= e($cliente['dni'] ?? '') ?>"
                       placeholder="12345678">
            </div>
            <div>
                <label for="nombre" class="form-label">Nombre completo <span class="text-red-500">*</span></label>
                <input id="nombre" type="text" name="nombre" required
                       class="form-input"
                       value="<?= e($cliente['nombre'] ?? '') ?>"
                       placeholder="Juan Pérez">
            </div>
            <div>
                <label for="telefono" class="form-label">Teléfono</label>
                <input id="telefono" type="tel" name="telefono"
                       class="form-input"
                       value="<?= e($cliente['telefono'] ?? '') ?>"
                       placeholder="299-4001234">
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email"
                       class="form-input"
                       value="<?= e($cliente['email'] ?? '') ?>"
                       placeholder="juan@mail.com">
            </div>
            <div class="sm:col-span-2">
                <label for="domicilio" class="form-label">Domicilio</label>
                <input id="domicilio" type="text" name="domicilio"
                       class="form-input"
                       value="<?= e($cliente['domicilio'] ?? '') ?>"
                       placeholder="Calle 123, Barrio Centro"
                       id="domicilio-input">
            </div>
            <div>
                <label for="localidad" class="form-label">Localidad</label>
                <input id="localidad" type="text" name="localidad"
                       class="form-input"
                       value="<?= e($cliente['localidad'] ?? '') ?>"
                       placeholder="Neuquén">
            </div>
        </div>

        <!-- Mapa Leaflet para capturar lat/lng -->
        <div>
            <label class="form-label">Ubicación (opcional — clic en el mapa para marcar)</label>
            <div id="mapa" class="w-full h-56 rounded-lg border border-gray-200 z-0"></div>
            <input type="hidden" name="lat" id="lat" value="<?= e($cliente['lat'] ?? '') ?>">
            <input type="hidden" name="lng" id="lng" value="<?= e($cliente['lng'] ?? '') ?>">
            <p class="text-xs text-gray-400 mt-1" id="coords-label">
                <?php if (!empty($cliente['lat'])): ?>
                    📍 Lat: <?= $cliente['lat'] ?>, Lng: <?= $cliente['lng'] ?>
                <?php else: ?>
                    Sin ubicación marcada
                <?php endif; ?>
            </p>
        </div>

        <div>
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="3"
                      class="form-input resize-none"
                      placeholder="Notas internas..."><?= e($cliente['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary">
                <?= $esEdicion ? '💾 Guardar cambios' : '✅ Crear cliente' ?>
            </button>
            <a href="<?= url('vendedor/clientes') ?>" class="btn-secondary">Cancelar</a>
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
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;

    if (document.getElementById('lat').value) {
        marker = L.marker([latInicial, lngInicial]).addTo(map);
    }

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        document.getElementById('lat').value = lat.toFixed(7);
        document.getElementById('lng').value = lng.toFixed(7);
        document.getElementById('coords-label').textContent =
            '📍 Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);

        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng).addTo(map);
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
                    document.getElementById('coords-label').textContent =
                        '📍 Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6) + ' (geocodificado)';
                    map.setView([lat, lng], 15);
                    if (marker) marker.setLatLng([lat, lng]);
                    else marker = L.marker([lat, lng]).addTo(map);
                }
            }).catch(() => {});
    });
})();
</script>
