<?php
// =======================================================
// Vista: Checkout Wompi - FluviApp v2.0
// =======================================================
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <!-- Tarjeta Principal de Pago -->
        <div class="card bg-white shadow border-0 rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-gradient text-white py-4 px-4" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1 mb-2">Paso 3 de 3</span>
                        <h4 class="fw-bold mb-0"><i class="fa-solid fa-credit-card me-2"></i>Pago Seguro en Línea</h4>
                    </div>
                    <div class="text-end">
                        <img src="https://wompi.com/assets/img/brand-logo.svg" alt="Wompi Bancolombia" style="height: 38px; filter: brightness(0) invert(1);" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Resumen de Compra -->
                <div class="bg-light p-4 rounded-3 border mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-receipt me-2 text-primary"></i>Detalle de la Reserva Fluvial
                    </h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Trayecto:</span>
                            <strong><?= htmlspecialchars($viaje['origen_nombre'] ?? 'Origen') ?> &rarr; <?= htmlspecialchars($viaje['destino_nombre'] ?? 'Destino') ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Fecha y Hora de Salida:</span>
                            <strong><?= $viaje['fecha_salida'] ?> &bull; <?= substr($viaje['hora_salida'], 0, 5) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Pasajero:</span>
                            <strong><?= htmlspecialchars($pasajeroNombre) ?> (Doc: <?= htmlspecialchars($pasajeroDoc) ?>)</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Asiento Asignado:</span>
                            <span class="badge bg-primary fs-6">
                                <?= $numeroAsiento ? 'Asiento #' . $numeroAsiento : 'Asignación en Muelle' ?>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Referencia de Pago:</span>
                            <code class="fw-bold text-dark"><?= htmlspecialchars($referencia) ?></code>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Embarcación:</span>
                            <strong><?= htmlspecialchars($viaje['embarcacion_nombre'] ?? 'Lancha Rápida') ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Monto Total -->
                <div class="p-3 bg-primary-subtle rounded-3 mb-4 border border-primary-subtle d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase small fw-bold text-primary d-block">Total a Pagar (COP):</span>
                        <span class="fs-2 fw-bold text-primary">$<?= number_format($monto, 0, ',', '.') ?></span>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success py-2 px-3 fs-6">
                            <i class="fa-solid fa-lock me-1"></i> Cifrado SHA-256
                        </span>
                    </div>
                </div>

                <!-- Botón de Pago Wompi -->
                <div class="text-center my-4">
                    <button type="button" id="btn-pagar-wompi" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                        <i class="fa-solid fa-shield-halved me-2"></i> Pagar con Wompi (PSE, Nequi, Tarjetas)
                    </button>
                    <div class="small text-muted mt-2">
                        <i class="fa-solid fa-check-circle text-success me-1"></i> PSE &bull; Nequi &bull; Bancolombia &bull; Visa &bull; Mastercard
                    </div>
                </div>

                <!-- Modo Sandbox / Pruebas -->
                <?php if ($modoWompi === 'sandbox'): ?>
                    <div class="alert alert-warning border-warning shadow-sm mt-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa-solid fa-vial-circle-check fs-4 text-warning me-2"></i>
                            <h6 class="fw-bold mb-0 text-dark">Entorno de Pruebas (Sandbox) Activo</h6>
                        </div>
                        <p class="small text-dark mb-3">
                            En este modo puedes probar el formulario real de Wompi con tarjetas de prueba, o usar el siguiente botón para simular una aprobación bancaria inmediata en 1 clic:
                        </p>
                        <a href="<?= BASE_URL ?>/cliente/simular-aprobacion-wompi?ref=<?= urlencode($referencia) ?>" class="btn btn-dark btn-sm fw-bold">
                            <i class="fa-solid fa-bolt me-1 text-warning"></i> Simular Aprobación Inmediata (Sandbox)
                        </a>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="<?= BASE_URL ?>/portal" class="btn btn-link text-muted">Cancelar y volver al portal</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Oficial de Wompi Widget -->
<script type="text/javascript" src="https://checkout.wompi.co/widget.js"></script>
<script>
document.getElementById('btn-pagar-wompi').addEventListener('click', function() {
    var checkout = new WidgetCheckout({
        currency: 'COP',
        amountInCents: <?= $montoCentavos ?>,
        reference: '<?= $referencia ?>',
        publicKey: '<?= $publicKey ?>',
        signature: {
            integrity: '<?= $firmaIntegridad ?>'
        },
        redirectUrl: '<?= $redirectUrl ?>'
    });

    checkout.open(function (result) {
        var transaction = result.transaction;
        if (transaction && transaction.status === 'APPROVED') {
            window.location.href = '<?= $redirectUrl ?>';
        }
    });
});
</script>
