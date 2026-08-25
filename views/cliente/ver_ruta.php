<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-map-location-dot me-2 text-primary"></i>Detalle de Mi Ruta y Viaje
        </h4>
        <p class="text-muted small mb-0">Información completa de tu itinerario de navegación, precios y credenciales de abordaje</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/cliente/mis-boletos" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>Mis Tiquetes
        </a>
        <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $boleto['id'] ?>" target="_blank" class="btn btn-success btn-sm fw-semibold">
            <i class="fa-solid fa-print me-1"></i>Imprimir Tiquete Térmico
        </a>
    </div>
</div>

<!-- Infografía / Diagrama de la Ruta Fluvial -->
<div class="card bg-white shadow-sm border-0 mb-4 overflow-hidden">
    <div class="p-4" style="background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 50%, #0369a1 100%); color: white;">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1">Tiquete Oficial: <?= htmlspecialchars($boleto['codigo_boleto'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="text-end">
                <span class="small text-white-50">Itinerario:</span>
                <strong class="text-white ms-1"><?= htmlspecialchars($boleto['codigo_viaje'], ENT_QUOTES, 'UTF-8') ?></strong>
            </div>
        </div>

        <!-- Línea de Trayecto Fluvial -->
        <div class="row align-items-center text-center my-3">
            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="bg-white text-dark p-3 rounded-4 shadow-sm text-start">
                    <span class="badge bg-primary text-uppercase mb-2">Puerto de Origen</span>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($boleto['origen_nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <div class="text-muted small"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($boleto['origen_municipio'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-primary small fw-semibold mt-1"><i class="fa-solid fa-water me-1"></i><?= htmlspecialchars($boleto['origen_rio'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-3 mb-md-0">
                <div class="px-2">
                    <div class="text-white-50 small mb-1">
                        <i class="fa-solid fa-water-ladder me-1"></i>Distancia: <?= $boleto['distancia_km'] ?? '35' ?> km &bull; ~<?= $boleto['duracion_estimada_min'] ?? '45' ?> mins
                    </div>
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="border-top border-white border-2 flex-grow-1" style="border-style: dashed !important;"></div>
                        <div class="mx-3 bg-white text-primary p-3 rounded-circle shadow">
                            <i class="fa-solid fa-ship fs-4"></i>
                        </div>
                        <div class="border-top border-white border-2 flex-grow-1" style="border-style: dashed !important;"></div>
                    </div>
                    <div class="badge bg-warning text-dark mt-2 fw-bold text-uppercase px-3 py-1">Navegación Fluvial</div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="bg-white text-dark p-3 rounded-4 shadow-sm text-start">
                    <span class="badge bg-success text-uppercase mb-2">Puerto de Destino</span>
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($boleto['destino_nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <div class="text-muted small"><i class="fa-solid fa-flag-checkered me-1 text-success"></i><?= htmlspecialchars($boleto['destino_municipio'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="text-primary small fw-semibold mt-1"><i class="fa-solid fa-water me-1"></i><?= htmlspecialchars($boleto['destino_rio'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ficha de Detalles del Pasaje y Desglose de Precios -->
<div class="row g-4">
    <!-- Datos de Embarcación y Zarpe -->
    <div class="col-12 col-lg-7">
        <div class="card bg-white shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold text-dark border-bottom py-3">
                <i class="fa-solid fa-circle-info me-2 text-info"></i>Detalles del Zarpe y Operación
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <small class="text-muted text-uppercase fw-semibold d-block">Fecha de Salida</small>
                        <strong class="text-dark fs-6"><?= $boleto['fecha_salida'] ?></strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted text-uppercase fw-semibold d-block">Hora de Zarpe</small>
                        <strong class="text-dark fs-6"><?= substr($boleto['hora_salida'], 0, 5) ?></strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted text-uppercase fw-semibold d-block">Asiento Asignado</small>
                        <span class="badge bg-primary fs-6">Asiento #<?= $boleto['numero_asiento'] ?? '1' ?></span>
                    </div>

                    <div class="col-6 col-md-6 mt-3">
                        <small class="text-muted text-uppercase fw-semibold d-block">Embarcación</small>
                        <strong class="text-dark"><?= htmlspecialchars($boleto['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <div class="small text-muted">Matrícula: <code><?= htmlspecialchars($boleto['embarcacion_matricula'], ENT_QUOTES, 'UTF-8') ?></code></div>
                    </div>
                    <div class="col-6 col-md-6 mt-3">
                        <small class="text-muted text-uppercase fw-semibold d-block">Capitán al Mando</small>
                        <strong class="text-dark"><?= htmlspecialchars($boleto['capitan_nombre'] ?? 'Capitán Asignado', ENT_QUOTES, 'UTF-8') ?></strong>
                        <div class="small text-muted">Certificado por Autoridad Fluvial</div>
                    </div>
                </div>

                <div class="alert alert-info mt-4 mb-0 small">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    <strong>Recomendaciones para el Pasajero:</strong> Presentarse en el muelle 20 minutos antes de la hora de zarpe. Es obligatorio el uso del chaleco salvavidas durante todo el trayecto fluvial.
                </div>
            </div>
        </div>
    </div>

    <!-- Desglose de Tarifas y Pasajero -->
    <div class="col-12 col-lg-5">
        <div class="card bg-white shadow-sm border-0 h-100">
            <div class="card-header bg-white fw-bold text-dark border-bottom py-3">
                <i class="fa-solid fa-receipt me-2 text-success"></i>Comprobante y Precios
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-semibold d-block">Pasajero Titular</small>
                    <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($boleto['pasajero_nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <div class="small text-muted">Documento: <code><?= htmlspecialchars($boleto['pasajero_documento'], ENT_QUOTES, 'UTF-8') ?></code></div>
                </div>

                <div class="border-top pt-3 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tarifa Base de Pasaje:</span>
                        <span>$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?> COP</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tasa Portuaria / Seguro:</span>
                        <span class="text-success">Incluido</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Método de Pago:</span>
                        <span class="text-capitalize badge bg-light text-dark border"><?= $boleto['metodo_pago'] ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-5">TOTAL PAGADO:</span>
                        <span class="fw-bold text-success fs-4">$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?> COP</span>
                    </div>
                </div>

                <div class="text-center pt-2">
                    <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $boleto['id'] ?>" target="_blank" class="btn btn-outline-dark w-100 fw-semibold">
                        <i class="fa-solid fa-qrcode me-2"></i>Ver Tiquete Digital
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
