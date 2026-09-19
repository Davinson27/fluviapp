<?php
// =======================================================
// Vista: Entrega Digital de Encomiendas - FluviApp v2.0
// Firma Táctil en Pantalla y Foto de Evidencia
// =======================================================
?>

<div class="row g-4">
    <!-- Formulario de Entrega Digital -->
    <div class="col-12 col-lg-7">
        <div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-gradient text-white py-3 px-4" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-box-open me-2"></i>Despacho y Entrega Digital de Carga</h5>
            </div>
            <div class="card-body p-4">
                <form id="form_entrega_carga" onsubmit="enviarEntrega(event)">
                    <!-- Selección de Guía -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Número de Guía de Encomienda *</label>
                        <div class="input-group">
                            <input type="text" id="guia_numero_input" class="form-control" placeholder="Ej: CRG-20260919-XYZ123" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="buscarGuiaRapida()">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Verificar
                            </button>
                        </div>
                    </div>

                    <!-- Datos Destinatario (Auto-rellenable) -->
                    <div id="guia_info_card" class="p-3 bg-light rounded-3 border mb-3 d-none">
                        <div class="row g-2 small">
                            <div class="col-6"><strong>Destinatario:</strong> <span id="info_destinatario">--</span></div>
                            <div class="col-6"><strong>Teléfono:</strong> <span id="info_telefono">--</span></div>
                            <div class="col-12"><strong>Descripción:</strong> <span id="info_descripcion">--</span></div>
                            <div class="col-6"><strong>Peso:</strong> <span id="info_peso">--</span> kg</div>
                            <div class="col-6"><strong>Flete:</strong> $<span id="info_flete">--</span> COP</div>
                        </div>
                    </div>

                    <!-- Firma Táctil del Destinatario -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="fa-solid fa-signature me-1 text-primary"></i> Firma Táctil del Destinatario *
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="limpiarFirma()">
                                <i class="fa-solid fa-eraser me-1"></i> Limpiar Firma
                            </button>
                        </div>
                        <div class="border rounded-3 bg-white shadow-sm overflow-hidden" style="touch-action: none;">
                            <canvas id="signature_canvas" height="150" style="width: 100%; cursor: crosshair;"></canvas>
                        </div>
                        <div class="form-text small text-muted">El receptor debe firmar directamente con su dedo o lápiz táctil.</div>
                    </div>

                    <!-- Foto de Evidencia (Cámara del dispositivo) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">
                            <i class="fa-solid fa-camera me-1 text-success"></i> Foto de Evidencia de Entrega (Opcional)
                        </label>
                        <input type="file" id="foto_input" class="form-control" accept="image/*" capture="environment" onchange="previsualizarFoto(event)">
                        <div id="foto_preview_box" class="mt-2 text-center d-none">
                            <img id="foto_preview" src="" alt="Previsualización" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 180px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm" id="btn_confirmar_entrega">
                        <i class="fa-solid fa-circle-check me-2"></i> Confirmar Entrega Digital
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Encomiendas Pendientes de Entrega -->
    <div class="col-12 col-lg-5">
        <div class="card bg-white shadow-sm border-0 rounded-4">
            <div class="card-header bg-light py-3 px-4 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-boxes-packing me-2 text-secondary"></i>En Tránsito / Pendientes</span>
                <span class="badge bg-secondary"><?= count($cargasPendientes) ?> guías</span>
            </div>
            <div class="card-body p-3" style="max-height: 520px; overflow-y: auto;">
                <?php if (empty($cargasPendientes)): ?>
                    <p class="text-muted small text-center my-4">No hay encomiendas pendientes por entregar.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($cargasPendientes as $c): ?>
                            <a href="javascript:void(0)" 
                               onclick="seleccionarGuia('<?= htmlspecialchars($c['guia_numero']) ?>', '<?= htmlspecialchars($c['destinatario_nombre']) ?>', '<?= htmlspecialchars($c['destinatario_telefono']) ?>', '<?= htmlspecialchars($c['descripcion_carga']) ?>', '<?= $c['peso_kg'] ?>', '<?= number_format($c['valor_flete'], 0) ?>')" 
                               class="list-group-item list-group-item-action px-2 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark small">Guía: <?= htmlspecialchars($c['guia_numero']) ?></strong>
                                    <span class="badge bg-warning text-dark small text-uppercase"><?= $c['estado'] ?></span>
                                </div>
                                <div class="text-muted small">
                                    <strong>Destino:</strong> <?= htmlspecialchars($c['destinatario_nombre']) ?> (<?= htmlspecialchars($c['destino_nombre'] ?? '') ?>)
                                </div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    <?= htmlspecialchars(substr($c['descripcion_carga'], 0, 50)) ?>... &bull; <strong><?= $c['peso_kg'] ?> kg</strong>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Manejo de Canvas de Firma Táctil
const canvas = document.getElementById('signature_canvas');
const ctx = canvas.getContext('2d');
let drawing = false;
let hasSigned = false;

function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = 150 * ratio;
    ctx.scale(ratio, ratio);
    ctx.strokeStyle = '#0f172a';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
}

window.addEventListener('resize', resizeCanvas);
document.addEventListener('DOMContentLoaded', resizeCanvas);

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
        x: clientX - rect.left,
        y: clientY - rect.top
    };
}

function startDraw(e) {
    drawing = true;
    hasSigned = true;
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
    if (e.touches) e.preventDefault();
}

function draw(e) {
    if (!drawing) return;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    if (e.touches) e.preventDefault();
}

function stopDraw() {
    drawing = false;
}

canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', draw);
window.addEventListener('mouseup', stopDraw);

canvas.addEventListener('touchstart', startDraw, { passive: false });
canvas.addEventListener('touchmove', draw, { passive: false });
window.addEventListener('touchend', stopDraw);

function limpiarFirma() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasSigned = false;
}

let fotoBase64 = '';
function previsualizarFoto(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        fotoBase64 = e.target.result;
        document.getElementById('foto_preview').src = fotoBase64;
        document.getElementById('foto_preview_box').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

function seleccionarGuia(guia, dest, tel, desc, peso, flete) {
    document.getElementById('guia_numero_input').value = guia;
    document.getElementById('info_destinatario').innerText = dest;
    document.getElementById('info_telefono').innerText = tel;
    document.getElementById('info_descripcion').innerText = desc;
    document.getElementById('info_peso').innerText = peso;
    document.getElementById('info_flete').innerText = flete;
    document.getElementById('guia_info_card').classList.remove('d-none');
}

function buscarGuiaRapida() {
    const guia = document.getElementById('guia_numero_input').value.trim();
    if (!guia) return;
    // Disparar selección automática si existe en la lista
    const item = Array.from(document.querySelectorAll('.list-group-item strong')).find(el => el.innerText.includes(guia));
    if (item) {
        item.closest('.list-group-item').click();
    } else {
        alert('Guía no encontrada en la lista de pendientes.');
    }
}

function enviarEntrega(e) {
    e.preventDefault();
    const guia = document.getElementById('guia_numero_input').value.trim();
    if (!guia) {
        alert('Por favor ingrese el número de guía.');
        return;
    }

    if (!hasSigned) {
        alert('El receptor debe firmar en el recuadro para confirmar la entrega.');
        return;
    }

    const firmaDataUrl = canvas.toDataURL('image/png');
    const btn = document.getElementById('btn_confirmar_entrega');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Guardando entrega...';

    fetch('<?= BASE_URL ?>/api/carga/confirmar-entrega', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            guia_numero: guia,
            firma_base64: firmaDataUrl,
            foto_base64: fotoBase64
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.message);
            window.location.reload();
        } else {
            alert('Error: ' + res.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i> Confirmar Entrega Digital';
        }
    })
    .catch(err => {
        alert('Error al conectar con el servidor.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i> Confirmar Entrega Digital';
    });
}
</script>
