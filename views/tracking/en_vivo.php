<?php
// =======================================================
// Vista: Rastreo Fluvial en Tiempo Real (Live River Tracking)
// FluviApp v2.0 - Mapa Leaflet Interactivo y Cálculo de ETA
// =======================================================
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="row g-4 mb-4">
    <!-- Encabezado y Métricas en Tiempo Real -->
    <div class="col-12">
        <div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="p-4" style="background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%); color: white;">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1 mb-2">
                            <i class="fa-solid fa-satellite-dish me-1"></i> Telemetría en Vivo
                        </span>
                        <h3 class="fw-bold mb-0">
                            <?= htmlspecialchars($viaje['origen_nombre']) ?> &rarr; <?= htmlspecialchars($viaje['destino_nombre']) ?>
                        </h3>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 text-uppercase fw-bold" id="badge_estado_viaje">
                            <?= htmlspecialchars($viaje['estado']) ?>
                        </span>
                    </div>
                </div>

                <!-- Tarjetas de Telemetría -->
                <div class="row g-3 text-dark text-center">
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Velocidad</span>
                            <span class="fs-2 fw-bold text-primary" id="card_velocidad">--</span>
                            <span class="small text-muted d-block" id="card_nudos">-- nudos</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Distancia Restante</span>
                            <span class="fs-2 fw-bold text-dark" id="card_distancia">--</span>
                            <span class="small text-muted d-block">km al puerto</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Tiempo Estimado (ETA)</span>
                            <span class="fs-2 fw-bold text-success" id="card_eta">--</span>
                            <span class="small text-muted d-block">minutos aprox.</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <span class="text-muted small text-uppercase fw-semibold d-block">Embarcación</span>
                            <span class="fs-5 fw-bold text-dark d-block text-truncate" title="<?= htmlspecialchars($viaje['embarcacion_nombre']) ?>">
                                <?= htmlspecialchars($viaje['embarcacion_nombre']) ?>
                            </span>
                            <span class="small text-muted d-block">Mat: <?= htmlspecialchars($viaje['embarcacion_matricula']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa Interactivo Leaflet -->
    <div class="col-12">
        <div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-light py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="fa-solid fa-map-location me-2 text-primary"></i>Posición Fluvial en Vivo</span>
                <span class="small text-muted" id="last_update_label">Actualizando...</span>
            </div>
            <div class="card-body p-0">
                <div id="live_map" style="height: 520px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
const viajeId = <?= (int)$viaje['id'] ?>;
let map, boatMarker, originMarker, destMarker, routeLine;

// Iconos personalizados para el mapa
const boatIcon = L.divIcon({
    className: 'custom-boat-icon',
    html: `
        <div style="background: #0284c7; color: white; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-ship"></i>
        </div>
    `,
    iconSize: [44, 44],
    iconAnchor: [22, 22]
});

const originIcon = L.divIcon({
    className: 'custom-origin-icon',
    html: `
        <div style="background: #0f172a; color: white; border: 2px solid white; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-anchor"></i>
        </div>
    `,
    iconSize: [34, 34],
    iconAnchor: [17, 17]
});

const destIcon = L.divIcon({
    className: 'custom-dest-icon',
    html: `
        <div style="background: #16a34a; color: white; border: 2px solid white; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-flag-checkered"></i>
        </div>
    `,
    iconSize: [34, 34],
    iconAnchor: [17, 17]
});

function initMap() {
    map = L.map('live_map').setView([9.2423, -74.7547], 9);

    // CartoDB Positron / OpenStreetMap Tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        maxZoom: 19
    }).addTo(map);

    actualizarTelemetria();
    setInterval(actualizarTelemetria, 8000); // Polling cada 8 segundos
}

function actualizarTelemetria() {
    fetch('<?= BASE_URL ?>/api/tracking/posicion-viaje?id=' + viajeId)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;

            // Actualizar tarjetas de métricas
            document.getElementById('card_velocidad').innerText = data.velocidad_kmh + ' km/h';
            document.getElementById('card_nudos').innerText = data.velocidad_nudos + ' nudos';
            document.getElementById('card_distancia').innerText = data.distancia_restante_km;
            document.getElementById('card_eta').innerText = '~' + data.eta_minutos;
            document.getElementById('badge_estado_viaje').innerText = data.estado;
            document.getElementById('last_update_label').innerText = 'Última señal: ' + data.ultima_actualizacion;

            const boatPos = [data.latitud, data.longitud];
            const origPos = data.origen_coords;
            const destPos = data.destino_coords;

            // Actualizar o crear marcador de barco
            if (!boatMarker) {
                boatMarker = L.marker(boatPos, { icon: boatIcon }).addTo(map);
                boatMarker.bindPopup(`<b>${data.embarcacion}</b><br>Velocidad: ${data.velocidad_kmh} km/h<br>ETA: ~${data.eta_minutos} min`);

                if (origPos[0] && origPos[1]) {
                    originMarker = L.marker(origPos, { icon: originIcon }).addTo(map)
                        .bindPopup(`<b>Puerto Origen:</b> ${data.origen}`);
                }

                if (destPos[0] && destPos[1]) {
                    destMarker = L.marker(destPos, { icon: destIcon }).addTo(map)
                        .bindPopup(`<b>Puerto Destino:</b> ${data.destino}`);
                }

                // Trazar línea de trayectoria
                if (origPos[0] && destPos[0]) {
                    routeLine = L.polyline([origPos, boatPos, destPos], {
                        color: '#0284c7',
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '8, 8'
                    }).addTo(map);
                    map.fitBounds(routeLine.getBounds(), { padding: [50, 50] });
                }
            } else {
                boatMarker.setLatLng(boatPos);
                boatMarker.setPopupContent(`<b>${data.embarcacion}</b><br>Velocidad: ${data.velocidad_kmh} km/h<br>ETA: ~${data.eta_minutos} min`);
                if (routeLine && origPos[0] && destPos[0]) {
                    routeLine.setLatLngs([origPos, boatPos, destPos]);
                }
            }
        })
        .catch(err => console.error("Error al consultar telemetría: ", err));
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
