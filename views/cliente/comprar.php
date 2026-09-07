<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-5"><i class="fa-solid fa-cart-shopping me-2"></i>Confirmar y Comprar Tiquete Fluvial</span>
                <a href="<?= BASE_URL ?>/portal" class="btn btn-outline-light btn-sm">Volver al Explorador</a>
            </div>
            <div class="card-body p-4">
                <!-- Resumen del Viaje -->
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-route me-2 text-primary"></i>Resumen del Trayecto Seleccionado
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Origen:</strong> <?= htmlspecialchars($viaje['origen_nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($viaje['origen_municipio'], ENT_QUOTES, 'UTF-8') ?>)
                            <div class="small text-muted"><?= htmlspecialchars($viaje['origen_rio'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="col-md-6">
                            <strong>Destino:</strong> <?= htmlspecialchars($viaje['destino_nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($viaje['destino_municipio'], ENT_QUOTES, 'UTF-8') ?>)
                            <div class="small text-muted"><?= htmlspecialchars($viaje['destino_rio'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                        <div class="col-md-4">
                            <strong>Fecha y Hora:</strong><br>
                            <?= $viaje['fecha_salida'] ?> &bull; <?= substr($viaje['hora_salida'], 0, 5) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Embarcación:</strong><br>
                            <?= htmlspecialchars($viaje['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($viaje['embarcacion_matricula'], ENT_QUOTES, 'UTF-8') ?>)
                        </div>
                        <div class="col-md-4">
                            <strong>Capitán:</strong><br>
                            <?= htmlspecialchars($viaje['capitan_nombre'] ?? 'Por asignar', ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Compra -->
                <form action="<?= BASE_URL ?>/cliente/procesar-compra" method="POST">
                    <?= SessionHelper::csrfField() ?>
                    <input type="hidden" name="viaje_id" value="<?= $viaje['id'] ?>">
                    <input type="hidden" name="precio_pagado" value="<?= $viaje['precio_pasaje'] ?>">

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-user-check me-2 text-success"></i>Datos del Pasajero
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo del Pasajero *</label>
                            <input type="text" name="pasajero_nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento de Identidad (Cédula/TI) *</label>
                            <input type="text" name="pasajero_documento" class="form-control" value="<?= htmlspecialchars($usuario['documento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono de Contacto *</label>
                            <input type="text" name="pasajero_telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Método de Pago *</label>
                            <select name="metodo_pago" class="form-select" required>
                                <option value="transferencia">Transferencia en Línea (Nequi / Bancolombia / Daviplata)</option>
                                <option value="tarjeta">Tarjeta Débito / Crédito</option>
                                <option value="efectivo">Pago en Taquilla / Muelle antes de abordar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Desglose de Precios -->
                    <div class="p-3 bg-light rounded-3 mb-4 border d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">Total a Pagar:</span>
                            <span class="fs-3 fw-bold text-success">$<?= number_format($viaje['precio_pasaje'], 0, ',', '.') ?> COP</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success p-2 fs-6"><i class="fa-solid fa-shield-halved me-1"></i>Pago Seguro</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>/portal" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow">
                            <i class="fa-solid fa-circle-check me-2"></i>Confirmar y Obtener Tiquete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
