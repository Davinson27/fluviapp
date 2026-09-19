<?php
// =======================================================
// Vista: Validación de Abordaje (Check-in QR) - FluviApp v2.0
// Mobile-First con Escáner de Cámara y Sonido
// =======================================================
?>

<div class="row g-4">
    <!-- Panel del Escáner y Cámara -->
    <div class="col-12 col-lg-7">
        <div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-qrcode me-2 text-primary"></i>Escáner de Abordaje QR</span>
                <span class="badge bg-success" id="scanner_status_badge"><i class="fa-solid fa-video me-1"></i>Cámara Lista</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <!-- Selector de Cámara y Controles -->
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <select id="camera_select" class="form-select form-select-sm" style="max-width: 250px;">
                        <option value="">Cargando cámaras...</option>
                    </select>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-primary" id="btn_start_scanner" onclick="iniciarEscaner()">
                            <i class="fa-solid fa-play me-1"></i> Iniciar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn_stop_scanner" onclick="detenerEscaner()">
                            <i class="fa-solid fa-pause me-1"></i> Pausar
                        </button>
                    </div>
                </div>

                <!-- Visor de la Cámara -->
                <div class="position-relative bg-black rounded-4 overflow-hidden shadow-inner" style="min-height: 280px; max-height: 380px;">
                    <div id="qr-reader" style="width: 100%;"></div>
                    <div id="scan_overlay" class="position-absolute top-50 start-50 translate-middle text-white-50 text-center pointer-events-none" style="pointer-events: none;">
                        <i class="fa-solid fa-expand display-1 mb-2 opacity-50"></i>
                        <div class="small fw-semibold">Apunte la cámara al código QR del boleto</div>
                    </div>
                </div>

                <!-- Búsqueda Manual de Respaldo -->
                <div class="mt-3 pt-3 border-top">
                    <label class="form-label small fw-bold text-muted mb-1">
                        <i class="fa-solid fa-keyboard me-1"></i> Búsqueda o Entrada Manual de Boleto
                    </label>
                    <div class="input-group">
                        <input type="text" id="manual_ticket_input" class="form-control" placeholder="Ej: BOL-20260919-ABCDEF">
                        <button class="btn btn-dark" type="button" onclick="validarManual()">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Validar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Resultados y Aforo en Tiempo Real -->
    <div class="col-12 col-lg-5">
        <!-- Tarjeta de Resultado del Escaneo -->
        <div class="card bg-white shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-light py-3 px-4 fw-bold text-dark border-bottom">
                <i class="fa-solid fa-user-check me-2 text-success"></i>Estado del Abordaje
            </div>
            <div class="card-body p-4 text-center" id="scan_result_box">
                <div class="text-muted py-4">
                    <i class="fa-solid fa-barcode display-4 text-secondary opacity-50 mb-3 d-block"></i>
                    <h6 class="fw-bold text-secondary">Esperando escaneo...</h6>
                    <p class="small text-muted mb-0">Escanee el código QR del boleto impreso o en la pantalla del celular del pasajero.</p>
                </div>
            </div>
        </div>

        <!-- Lista de Viajes Activos y Aforo -->
        <div class="card bg-white shadow-sm border-0 rounded-4">
            <div class="card-header bg-light py-3 px-4 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-ship me-2 text-primary"></i>Aforo de Viajes Activos</span>
                <span class="badge bg-primary"><?= count($viajesActivos) ?> en curso</span>
            </div>
            <div class="card-body p-3" style="max-height: 280px; overflow-y: auto;">
                <?php if (empty($viajesActivos)): ?>
                    <p class="text-muted small text-center my-3">No hay viajes programados para hoy.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($viajesActivos as $v): ?>
                            <?php 
                                $pct = $v['capacidad_pasajeros'] > 0 ? round(($v['abordados'] / $v['capacidad_pasajeros']) * 100) : 0;
                            ?>
                            <div class="list-group-item px-2 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark small"><?= htmlspecialchars($v['origen_nombre']) ?> &rarr; <?= htmlspecialchars($v['destino_nombre']) ?></strong>
                                    <span class="badge bg-dark small"><?= substr($v['hora_salida'], 0, 5) ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted small mb-1" style="font-size: 11px;">
                                    <span>Barco: <?= htmlspecialchars($v['embarcacion_nombre']) ?></span>
                                    <span><strong><?= $v['abordados'] ?></strong> de <?= $v['capacidad_pasajeros'] ?> abordados</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Librería Liviana Open-Source HTML5 QR Code -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let scanning = false;

// Audio Beeps con Web Audio API (Cero dependencias)
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function playSuccessBeep() {
    try {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime); // La5
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } catch (e) {}
}

function playErrorBeep() {
    try {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(220, audioCtx.currentTime); // La3 bajo
        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.35);
    } catch (e) {}
}

function inicializarCamaras() {
    Html5Qrcode.getCameras().then(cameras => {
        const select = document.getElementById('camera_select');
        select.innerHTML = '';
        if (cameras && cameras.length) {
            cameras.forEach((cam, i) => {
                const opt = document.createElement('option');
                opt.value = cam.id;
                opt.text = cam.label || `Cámara ${i + 1}`;
                select.appendChild(opt);
            });
            iniciarEscaner();
        } else {
            select.innerHTML = '<option>No se detectaron cámaras</option>';
        }
    }).catch(err => {
        console.error("Error al obtener cámaras: ", err);
    });
}

function iniciarEscaner() {
    const cameraId = document.getElementById('camera_select').value;
    if (!cameraId) return;

    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    if (scanning) return;

    html5QrCode.start(
        cameraId,
        {
            fps: 10,
            qrbox: { width: 220, height: 220 }
        },
        (decodedText, decodedResult) => {
            procesarEscaneo(decodedText);
        },
        (errorMessage) => {
            // Ignorar errores de frame sin QR
        }
    ).then(() => {
        scanning = true;
        document.getElementById('scanner_status_badge').className = 'badge bg-success';
        document.getElementById('scanner_status_badge').innerHTML = '<i class="fa-solid fa-circle-dot me-1 text-danger"></i> Escaneando...';
    }).catch(err => {
        console.error("No se pudo iniciar cámara: ", err);
    });
}

function detenerEscaner() {
    if (html5QrCode && scanning) {
        html5QrCode.stop().then(() => {
            scanning = false;
            document.getElementById('scanner_status_badge').className = 'badge bg-secondary';
            document.getElementById('scanner_status_badge').innerHTML = '<i class="fa-solid fa-pause me-1"></i> Pausado';
        });
    }
}

let lastScanTime = 0;
let lastScanCode = '';

function procesarEscaneo(qrData) {
    const now = Date.now();
    if (qrData === lastScanCode && (now - lastScanTime) < 3000) {
        return; // Evitar disparos repetidos en menos de 3 segundos
    }
    lastScanTime = now;
    lastScanCode = qrData;

    enviarValidacion(qrData);
}

function validarManual() {
    const input = document.getElementById('manual_ticket_input');
    const val = input.value.trim();
    if (val) {
        enviarValidacion(val);
        input.value = '';
    }
}

function enviarValidacion(qrData) {
    const box = document.getElementById('scan_result_box');
    box.innerHTML = `
        <div class="py-4 text-center">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <div class="small fw-bold">Validando credencial en muelle...</div>
        </div>
    `;

    fetch('<?= BASE_URL ?>/api/checkin/validar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.status === 'aprobado') {
            playSuccessBeep();
            box.innerHTML = `
                <div class="p-3 bg-success-subtle border border-success rounded-4 text-center">
                    <i class="fa-solid fa-circle-check text-success display-3 mb-2"></i>
                    <h4 class="fw-bold text-success mb-1">¡ABORDAJE AUTORIZADO!</h4>
                    <div class="fs-5 fw-bold text-dark mb-2">${res.pasajero_nombre}</div>
                    <div class="badge bg-primary fs-5 px-3 py-2 mb-3">
                        <i class="fa-solid fa-couch me-1"></i> Asiento: ${res.numero_asiento}
                    </div>
                    <div class="row g-2 text-start small border-top pt-2">
                        <div class="col-6"><strong>Boleto:</strong> ${res.codigo_boleto}</div>
                        <div class="col-6"><strong>Doc:</strong> ${res.pasajero_documento}</div>
                        <div class="col-6"><strong>Trayecto:</strong> ${res.origen_nombre} &rarr; ${res.destino_nombre}</div>
                        <div class="col-6"><strong>Aforo:</strong> ${res.abordados} / ${res.capacidad}</div>
                    </div>
                </div>
            `;
        } else if (res.status === 'ya_usado') {
            playErrorBeep();
            box.innerHTML = `
                <div class="p-3 bg-danger-subtle border border-danger rounded-4 text-center">
                    <i class="fa-solid fa-triangle-exclamation text-danger display-3 mb-2"></i>
                    <h4 class="fw-bold text-danger mb-1">¡BOLETO YA USADO!</h4>
                    <p class="text-dark small mb-2">${res.error}</p>
                    <div class="badge bg-dark fs-6 px-3 py-1 mb-2">Pasajero: ${res.pasajero || 'N/A'}</div>
                    <div class="small text-muted">Boleto: <code>${res.codigo_boleto}</code></div>
                </div>
            `;
        } else {
            playErrorBeep();
            box.innerHTML = `
                <div class="p-3 bg-warning-subtle border border-warning rounded-4 text-center">
                    <i class="fa-solid fa-circle-xmark text-danger display-3 mb-2"></i>
                    <h4 class="fw-bold text-danger mb-1">BOLETO NO VÁLIDO</h4>
                    <p class="text-dark small mb-0">${res.error || 'Código no reconocido.'}</p>
                </div>
            `;
        }
    })
    .catch(err => {
        playErrorBeep();
        box.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="fa-solid fa-wifi me-2"></i> Error de conexión al validar boleto.
            </div>
        `;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    inicializarCamaras();
});
</script>
