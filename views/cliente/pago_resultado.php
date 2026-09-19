<?php
// =======================================================
// Vista: Resultado de Pago Wompi - FluviApp v2.0
// =======================================================
require_once __DIR__ . '/../../app/Helpers/QrCodeHelper.php';

$esAprobado = ($tx['estado'] === 'aprobado');
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow border-0 rounded-4 overflow-hidden mb-4">
            <?php if ($esAprobado): ?>
                <div class="card-header bg-success text-white py-4 px-4 text-center">
                    <i class="fa-solid fa-circle-check display-3 mb-2"></i>
                    <h3 class="fw-bold mb-1">¡Pago Aprobado con Éxito!</h3>
                    <p class="mb-0 text-white-50">Tu reserva fluvial está confirmada y tu tiquete con QR ha sido emitido.</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($boleto): ?>
                        <div class="row g-4 align-items-center mb-4 pb-4 border-bottom">
                            <!-- QR Code de Abordaje -->
                            <div class="col-md-5 text-center border-end">
                                <div class="p-3 bg-light rounded-4 border d-inline-block shadow-sm">
                                    <?php 
                                        $qrPayload = QrCodeHelper::buildPayload('BOL', $boleto['codigo_boleto'], $boleto['codigo_qr_token'] ?? $boleto['codigo_boleto']);
                                        echo QrCodeHelper::renderSvg($qrPayload, 170);
                                    ?>
                                </div>
                                <div class="small fw-bold text-dark mt-2">
                                    <i class="fa-solid fa-qrcode me-1 text-primary"></i> Código de Abordaje QR
                                </div>
                                <div class="text-muted" style="font-size: 11px;">Muestra este código al operador en el muelle</div>
                            </div>

                            <!-- Información del Boleto -->
                            <div class="col-md-7">
                                <span class="badge bg-primary px-3 py-1 mb-2 fs-6">
                                    <i class="fa-solid fa-ticket me-1"></i> Boleto #<?= htmlspecialchars($boleto['codigo_boleto']) ?>
                                </span>
                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($boleto['origen_nombre']) ?> &rarr; <?= htmlspecialchars($boleto['destino_nombre']) ?>
                                </h5>
                                <div class="text-muted small mb-3">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= $boleto['fecha_salida'] ?> &bull; <?= substr($boleto['hora_salida'], 0, 5) ?>
                                </div>

                                <div class="row g-2 small">
                                    <div class="col-6">
                                        <span class="text-muted d-block">Pasajero:</span>
                                        <strong><?= htmlspecialchars($boleto['pasajero_nombre']) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">Documento:</span>
                                        <strong><?= htmlspecialchars($boleto['pasajero_documento']) ?></strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">Asiento:</span>
                                        <span class="badge bg-primary fs-6">
                                            <?= !empty($boleto['numero_asiento']) ? 'Asiento #' . $boleto['numero_asiento'] : 'General' ?>
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block">Embarcación:</span>
                                        <strong><?= htmlspecialchars($boleto['embarcacion_nombre']) ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Datos de la Transacción -->
                    <div class="bg-light p-3 rounded-3 border mb-4 small">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <span class="text-muted">Referencia FluviApp:</span>
                                <strong><?= htmlspecialchars($tx['referencia_pago']) ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">ID Transacción Pasarela:</span>
                                <strong><?= htmlspecialchars($tx['transaccion_id_externo'] ?? 'N/A') ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">Monto Pagado:</span>
                                <strong class="text-success">$<?= number_format($tx['monto'], 0, ',', '.') ?> COP</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted">Método:</span>
                                <strong class="text-uppercase"><?= htmlspecialchars($tx['metodo_pago']) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <a href="<?= BASE_URL ?>/portal" class="btn btn-light">
                            <i class="fa-solid fa-house me-1"></i> Ir al Portal
                        </a>
                        <div class="d-flex gap-2">
                            <?php if ($boleto): ?>
                                <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $boleto['id'] ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-print me-1"></i> Imprimir Tiquete
                                </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/cliente/mis-boletos" class="btn btn-success fw-bold shadow-sm">
                                <i class="fa-solid fa-ticket-simple me-1"></i> Ver en Mis Boletos
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-header bg-warning text-dark py-4 px-4 text-center">
                    <i class="fa-solid fa-triangle-exclamation display-3 mb-2 text-warning-emphasis"></i>
                    <h3 class="fw-bold mb-1">Transacción en Estado: <?= strtoupper($tx['estado']) ?></h3>
                    <p class="mb-0">No se pudo confirmar el pago inmediatamente o fue rechazado por la entidad bancaria.</p>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="text-muted mb-4">
                        Referencia de pago: <code><?= htmlspecialchars($tx['referencia_pago']) ?></code>. Si realizaste el pago mediante PSE o Nequi, puede tardar unos minutos en ser confirmado por tu banco.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= BASE_URL ?>/portal" class="btn btn-primary">Volver al Portal</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
