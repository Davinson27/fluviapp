<?php
// =======================================================
// Componente: Mapa Visual de Asientos (Seat Map) - FluviApp v2.0
// Responsive, Interactivo y con bloqueo visual
// =======================================================
/** @var array $mapaAsientos */
$info = $mapaAsientos['info_embarcacion'] ?? [];
$filas = $mapaAsientos['filas'] ?? [];
$asientoSeleccionado = $asientoSeleccionado ?? null;
?>

<div class="card border border-primary-subtle shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <span class="fw-bold text-dark">
            <i class="fa-solid fa-chair me-2 text-primary"></i>Selección de Asiento: <?= htmlspecialchars($info['embarcacion_nombre'] ?? 'Embarcación') ?> (<?= htmlspecialchars($mapaAsientos['config']['nombre'] ?? 'Lancha') ?>)
        </span>
        <div class="d-flex gap-3 small">
            <span><span class="badge bg-success">&nbsp;</span> Disponible (<?= $mapaAsientos['libres_count'] ?? 0 ?>)</span>
            <span><span class="badge bg-danger">&nbsp;</span> Ocupado (<?= $mapaAsientos['ocupados_count'] ?? 0 ?>)</span>
            <span><span class="badge bg-primary">&nbsp;</span> Seleccionado</span>
        </div>
    </div>
    <div class="card-body bg-body-tertiary p-3">
        <!-- Indicador de Proa (Frente de la embarcación) -->
        <div class="text-center mb-3">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill border">
                <i class="fa-solid fa-compass me-1"></i> FRENTE / PROA (Cabina del Capitán)
            </span>
        </div>

        <!-- Plano del Barco -->
        <div class="boat-layout mx-auto p-3 bg-white rounded-4 border shadow-sm" style="max-width: 480px;">
            <?php foreach ($filas as $fila): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <!-- Fila Izquierda -->
                    <div class="d-flex gap-2">
                        <?php foreach ($fila['izquierda'] as $as): ?>
                            <?php 
                                $esOcupado = ($as['estado'] === 'ocupado');
                                $esActual = ($asientoSeleccionado !== null && (int)$asientoSeleccionado === (int)$as['numero']);
                                $btnClass = $esOcupado ? 'btn-danger opacity-75 disabled' : ($esActual ? 'btn-primary active' : 'btn-outline-success');
                            ?>
                            <button type="button" 
                                    class="btn <?= $btnClass ?> btn-sm seat-btn d-flex flex-column align-items-center justify-content-center p-1"
                                    style="width: 46px; height: 46px; font-size: 11px;"
                                    data-seat="<?= $as['numero'] ?>"
                                    data-pos="<?= $as['posicion'] ?>"
                                    <?= $esOcupado ? 'disabled title="Ocupado por ' . htmlspecialchars($as['pasajero'] ?? 'Pasajero') . '"' : 'title="Asiento ' . $as['numero'] . ' (' . $as['posicion'] . ')"' ?>
                                    onclick="seleccionarAsiento(<?= $as['numero'] ?>, '<?= $as['posicion'] ?>')">
                                <i class="fa-solid fa-couch"></i>
                                <span class="fw-bold"><?= $as['numero'] ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pasillo Central -->
                    <div class="px-2 text-muted small text-uppercase" style="font-size: 9px; letter-spacing: 1px;">
                        Pasillo
                    </div>

                    <!-- Fila Derecha -->
                    <div class="d-flex gap-2">
                        <?php foreach ($fila['derecha'] as $as): ?>
                            <?php 
                                $esOcupado = ($as['estado'] === 'ocupado');
                                $esActual = ($asientoSeleccionado !== null && (int)$asientoSeleccionado === (int)$as['numero']);
                                $btnClass = $esOcupado ? 'btn-danger opacity-75 disabled' : ($esActual ? 'btn-primary active' : 'btn-outline-success');
                            ?>
                            <button type="button" 
                                    class="btn <?= $btnClass ?> btn-sm seat-btn d-flex flex-column align-items-center justify-content-center p-1"
                                    style="width: 46px; height: 46px; font-size: 11px;"
                                    data-seat="<?= $as['numero'] ?>"
                                    data-pos="<?= $as['posicion'] ?>"
                                    <?= $esOcupado ? 'disabled title="Ocupado por ' . htmlspecialchars($as['pasajero'] ?? 'Pasajero') . '"' : 'title="Asiento ' . $as['numero'] . ' (' . $as['posicion'] . ')"' ?>
                                    onclick="seleccionarAsiento(<?= $as['numero'] ?>, '<?= $as['posicion'] ?>')">
                                <i class="fa-solid fa-couch"></i>
                                <span class="fw-bold"><?= $as['numero'] ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Indicador de Popa (Atrás de la embarcación / Motores) -->
        <div class="text-center mt-3">
            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill border">
                <i class="fa-solid fa-anchor me-1"></i> ATRÁS / POPA (Motores y Bodega)
            </span>
        </div>

        <!-- Asiento Elegido en texto -->
        <div class="alert alert-info mt-3 py-2 px-3 mb-0 d-flex justify-content-between align-items-center">
            <div>
                <i class="fa-solid fa-circle-info me-2"></i>
                Asiento seleccionado: 
                <strong id="seat_label_display"><?= $asientoSeleccionado ? '#' . $asientoSeleccionado : 'Ninguno (Asignación automática)' ?></strong>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarAsiento()">Liberar Selección</button>
        </div>
    </div>
</div>

<script>
function seleccionarAsiento(num, pos) {
    document.querySelectorAll('.seat-btn').forEach(btn => {
        if (!btn.classList.contains('btn-danger')) {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-success');
        }
    });

    const selectedBtn = document.querySelector(`.seat-btn[data-seat="${num}"]`);
    if (selectedBtn) {
        selectedBtn.classList.remove('btn-outline-success');
        selectedBtn.classList.add('btn-primary', 'active');
    }

    const inputAsiento = document.getElementById('input_numero_asiento');
    if (inputAsiento) {
        inputAsiento.value = num;
    }

    const labelDisplay = document.getElementById('seat_label_display');
    if (labelDisplay) {
        labelDisplay.innerHTML = `<span class="badge bg-primary fs-6">#${num} (${pos})</span>`;
    }
}

function limpiarAsiento() {
    document.querySelectorAll('.seat-btn').forEach(btn => {
        if (!btn.classList.contains('btn-danger')) {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-success');
        }
    });
    const inputAsiento = document.getElementById('input_numero_asiento');
    if (inputAsiento) inputAsiento.value = '';
    const labelDisplay = document.getElementById('seat_label_display');
    if (labelDisplay) labelDisplay.innerText = 'Ninguno (Asignación automática)';
}
</script>
