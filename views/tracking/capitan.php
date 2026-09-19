<?php
// =======================================================
// Vista: Modo Capitán - Transmisión GPS Fluvial - FluviApp v2.0
// Mobile-First para uso a bordo en celular o tablet
// =======================================================
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card bg-white shadow border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-satellite-dish me-2 text-primary"></i>Puente de Mando (GPS Capitán)</span>
                <span id="badge_gps_status" class="badge bg-secondary">GPS Desactivado</span>
            </div>

            <div class="card-body p-4 text-center">
                <?php if (!$viaje): ?>
                    <div class="alert alert-info py-4">
                        <i class="fa-solid fa-ship display-4 text-primary mb-3"></i>
                        <h5>No tienes viajes activos asignados</h5>
                        <p class="small text-muted mb-0">Cuando tengas un viaje programado o en embarque, podrás iniciar la transmisión de telemetría fluvial desde aquí.</p>
                    </div>
                <?php else: ?>
                    <!-- Info del Viaje Actual -->
                    <div class="bg-light p-3 rounded-4 border mb-4 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($viaje['codigo_viaje']) ?></span>
                            <span class="badge bg-warning text-dark text-uppercase"><?= htmlspecialchars($viaje['estado']) ?></span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">
                            <?= htmlspecialchars($viaje['origen_nombre']) ?> &rarr; <?= htmlspecialchars($viaje['destino_nombre']) ?>
                        </h5>
                        <div class="small text-muted">
                            Embarcación: <strong><?= htmlspecialchars($viaje['embarcacion_nombre']) ?></strong> (<?= htmlspecialchars($viaje['embarcacion_matricula']) ?>)
                        </div>
                    </div>

                    <!-- Indicadores de Telemetría en Vivo -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border text-center">
                                <span class="small text-muted text-uppercase d-block fw-semibold">Velocidad</span>
                                <span class="display-6 fw-bold text-primary" id="disp_speed">0.0</span>
                                <span class="small text-muted d-block">km/h</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border text-center">
                                <span class="small text-muted text-uppercase d-block fw-semibold">Rumbo</span>
                                <span class="display-6 fw-bold text-dark" id="disp_heading">0&deg;</span>
                                <span class="small text-muted d-block">Brújula</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-body-tertiary rounded-3 border text-start small font-monospace" style="font-size: 11px;">
                                <div><strong>Latitud:</strong> <span id="disp_lat">Esperando señal...</span></div>
                                <div><strong>Longitud:</strong> <span id="disp_lng">Esperando señal...</span></div>
                                <div><strong>Precisión:</strong> <span id="disp_acc">--</span> metros</div>
                                <div><strong>Puntos transmitidos:</strong> <span id="disp_count" class="badge bg-success">0</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Control -->
                    <div class="d-grid gap-2">
                        <button type="button" id="btn_toggle_gps" class="btn btn-success btn-lg py-3 fw-bold rounded-pill shadow" onclick="toggleNavegacion()">
                            <i class="fa-solid fa-location-crosshairs me-2"></i> Iniciar Transmisión Fluvial
                        </button>
                        <a href="<?= BASE_URL ?>/tracking/viaje?id=<?= $viaje['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                            <i class="fa-solid fa-map me-1"></i> Ver Mapa de Seguimiento Público
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
let watchId = null;
let transmitiendo = false;
let puntosEnviados = 0;
const viajeId = <?= $viaje ? (int)$viaje['id'] : 0 ?>;

function toggleNavegacion() {
    if (!transmitiendo) {
        iniciarTransmision();
    } else {
        detenerTransmision();
    }
}

function iniciarTransmision() {
    if (!navigator.geolocation) {
        alert("Tu dispositivo o navegador no soporta geolocalización GPS.");
        return;
    }

    const btn = document.getElementById('btn_toggle_gps');
    const badge = document.getElementById('badge_gps_status');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Conectando GPS...';

    watchId = navigator.geolocation.watchPosition(
        (pos) => {
            transmitiendo = true;
            btn.disabled = false;
            btn.className = 'btn btn-danger btn-lg py-3 fw-bold rounded-pill shadow';
            btn.innerHTML = '<i class="fa-solid fa-stop me-2"></i> Detener Transmisión';

            badge.className = 'badge bg-success';
            badge.innerHTML = '<i class="fa-solid fa-satellite me-1"></i> Transmitiendo en Vivo';

            const coords = pos.coords;
            const speedKmh = coords.speed ? (coords.speed * 3.6).toFixed(1) : 0.0;
            const heading = coords.heading ? Math.round(coords.heading) : 0;

            document.getElementById('disp_speed').innerText = speedKmh;
            document.getElementById('disp_heading').innerHTML = heading + '&deg;';
            document.getElementById('disp_lat').innerText = coords.latitude.toFixed(6);
            document.getElementById('disp_lng').innerText = coords.longitude.toFixed(6);
            document.getElementById('disp_acc').innerText = Math.round(coords.accuracy);

            enviarTelemetria(coords.latitude, coords.longitude, speedKmh, heading);
        },
        (err) => {
            console.error("Error GPS:", err);
            alert("No se pudo obtener la posición GPS. Verifique que los permisos de ubicación estén habilitados.");
            detenerTransmision();
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

function detenerTransmision() {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }
    transmitiendo = false;

    const btn = document.getElementById('btn_toggle_gps');
    const badge = document.getElementById('badge_gps_status');

    btn.disabled = false;
    btn.className = 'btn btn-success btn-lg py-3 fw-bold rounded-pill shadow';
    btn.innerHTML = '<i class="fa-solid fa-location-crosshairs me-2"></i> Iniciar Transmisión Fluvial';

    badge.className = 'badge bg-secondary';
    badge.innerText = 'GPS Desactivado';
}

function enviarTelemetria(lat, lng, speed, heading) {
    fetch('<?= BASE_URL ?>/api/tracking/actualizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            viaje_id: viajeId,
            latitud: lat,
            longitud: lng,
            velocidad_kmh: speed,
            rumbo: heading
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            puntosEnviados++;
            document.getElementById('disp_count').innerText = puntosEnviados;
        }
    })
    .catch(err => console.error("Error al transmitir telemetría: ", err));
}
</script>
