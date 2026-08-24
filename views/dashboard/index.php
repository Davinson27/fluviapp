<!-- Estadísticas Generales -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Ingresos Totales</span>
                    <h3 class="fw-bold mb-0 text-dark">$<?= number_format($ingresosTotales, 0, ',', '.') ?></h3>
                    <small class="text-success"><i class="fa-solid fa-arrow-trend-up me-1"></i>Boletos + Cargas</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-money-bill-wave fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Boletos Emitidos</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalBoletos ?></h3>
                    <small class="text-muted">Pasajes fluviales</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fa-solid fa-ticket fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Flota de Embarcaciones</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalEmbarcaciones ?></h3>
                    <small class="text-muted">Lanchas, Ferries, Barcazas</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                    <i class="fa-solid fa-ship fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Guías de Carga</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $totalCargas ?></h3>
                    <small class="text-muted">Encomiendas fluviales</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-boxes-packing fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones Rápidas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card p-3 bg-white">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bolt me-2 text-warning"></i>Acciones Rápidas</h6>
                    <small class="text-muted">Operaciones frecuentes en muelle y taquilla</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= BASE_URL ?>/boletos/crear" class="btn btn-success btn-sm fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i>Emitir Boleto
                    </a>
                    <a href="<?= BASE_URL ?>/cargas/crear" class="btn btn-primary btn-sm fw-semibold">
                        <i class="fa-solid fa-box me-1"></i>Registrar Carga
                    </a>
                    <a href="<?= BASE_URL ?>/viajes/crear" class="btn btn-warning btn-sm fw-semibold text-dark">
                        <i class="fa-solid fa-calendar-plus me-1"></i>Programar Itinerario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de Resumen -->
<div class="row g-4">
    <!-- Itinerarios Próximos -->
    <div class="col-12 col-lg-7">
        <div class="card bg-white h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-compass me-2 text-primary"></i>Itinerarios y Zarpes Recientes</span>
                <a href="<?= BASE_URL ?>/viajes" class="btn btn-outline-primary btn-sm">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Ruta</th>
                                <th>Embarcación</th>
                                <th>Fecha / Hora</th>
                                <th>Cupos</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($viajesHoy)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No hay viajes programados</td></tr>
                            <?php else: ?>
                                <?php foreach ($viajesHoy as $v): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($v['codigo_viaje']) ?></td>
                                        <td><?= htmlspecialchars($v['origen_nombre']) ?> &rarr; <?= htmlspecialchars($v['destino_nombre']) ?></td>
                                        <td><?= htmlspecialchars($v['embarcacion_nombre']) ?></td>
                                        <td><?= $v['fecha_salida'] ?> <?= substr($v['hora_salida'], 0, 5) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $v['cupos_disponibles'] ?> / <?= $v['embarcacion_cap_pasajeros'] ?></span></td>
                                        <td>
                                            <?php
                                            $badgeClass = match($v['estado']) {
                                                'programado'    => 'bg-info',
                                                'en_embarque'   => 'bg-warning text-dark',
                                                'en_navegacion' => 'bg-primary',
                                                'arribado'      => 'bg-success',
                                                'cancelado'     => 'bg-danger',
                                                default         => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?> text-uppercase"><?= str_replace('_', ' ', $v['estado']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Boletos Vendidos -->
    <div class="col-12 col-lg-5">
        <div class="card bg-white h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-ticket me-2 text-success"></i>Últimos Boletos</span>
                <a href="<?= BASE_URL ?>/boletos" class="btn btn-outline-success btn-sm">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Boleto</th>
                                <th>Pasajero</th>
                                <th>Tarifa</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimosBoletos)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No se han emitido boletos</td></tr>
                            <?php else: ?>
                                <?php foreach ($ultimosBoletos as $b): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-dark"><?= htmlspecialchars($b['codigo_boleto']) ?></span></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($b['pasajero_nombre']) ?></div>
                                            <small class="text-muted">Doc: <?= htmlspecialchars($b['pasajero_documento']) ?></small>
                                        </td>
                                        <td class="text-success fw-bold">$<?= number_format($b['precio_pagado'], 0, ',', '.') ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $b['id'] ?>" target="_blank" class="btn btn-light btn-sm text-primary" title="Imprimir Tiquete">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
