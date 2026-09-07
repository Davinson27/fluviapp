<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-ticket me-2 text-primary"></i>Mis Tiquetes y Rutas Compradas</h4>
        <p class="text-muted small mb-0">Consulta tus pasajes adquiridos, estado de tus viajes y detalles del trayecto</p>
    </div>
    <a href="<?= BASE_URL ?>/portal" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-plus me-2"></i>Comprar Nuevo Pasaje
    </a>
</div>

<div class="card bg-white shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código Tiquete</th>
                        <th>Trayecto Fluvial</th>
                        <th>Fecha y Hora</th>
                        <th>Embarcación</th>
                        <th>Asiento</th>
                        <th>Tarifa Pagada</th>
                        <th>Estado Viaje</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($boletos)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="display-3 text-muted mb-3"><i class="fa-solid fa-ticket"></i></div>
                                <h6 class="fw-bold text-secondary">Aún no has comprado ningún tiquete fluvial.</h6>
                                <p class="text-muted small">Explora las rutas disponibles y adquiere tu primer pasaje.</p>
                                <a href="<?= BASE_URL ?>/portal" class="btn btn-primary btn-sm mt-2">Explorar Rutas Disponibles</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($boletos as $b): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($b['codigo_boleto'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <?= htmlspecialchars($b['origen_nombre'], ENT_QUOTES, 'UTF-8') ?> &rarr; <?= htmlspecialchars($b['destino_nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <small class="text-muted"><?= htmlspecialchars($b['origen_rio'], ENT_QUOTES, 'UTF-8') ?> &bull; ~<?= $b['duracion_estimada_min'] ?> mins</small>
                                </td>
                                <td>
                                    <div><?= $b['fecha_salida'] ?></div>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= substr($b['hora_salida'], 0, 5) ?></small>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-ship me-1 text-info"></i><?= htmlspecialchars($b['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($b['embarcacion_matricula'], ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td><span class="badge bg-secondary">Asiento #<?= $b['numero_asiento'] ?? '1' ?></span></td>
                                <td class="fw-bold text-success">$<?= number_format($b['precio_pagado'], 0, ',', '.') ?> COP</td>
                                <td>
                                    <?php
                                    $vEstado = $b['viaje_estado'] ?? 'programado';
                                    $badgeClass = match($vEstado) {
                                        'programado'    => 'bg-info',
                                        'en_embarque'   => 'bg-warning text-dark',
                                        'en_navegacion' => 'bg-primary',
                                        'arribado'      => 'bg-success',
                                        'cancelado'     => 'bg-danger',
                                        default         => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?> text-uppercase"><?= str_replace('_', ' ', $vEstado) ?></span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>/cliente/factura-boleto?id=<?= $b['id'] ?>" target="_blank" class="btn btn-primary btn-sm me-1 fw-semibold" title="Descargar Factura Digital en PDF">
                                        <i class="fa-solid fa-file-invoice-dollar me-1"></i>Factura PDF
                                    </a>
                                    <a href="<?= BASE_URL ?>/cliente/ver-ruta?id=<?= $b['id'] ?>" class="btn btn-outline-primary btn-sm me-1" title="Ver Mi Ruta y Detalles">
                                        <i class="fa-solid fa-map-location-dot me-1"></i>Ruta
                                    </a>
                                    <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $b['id'] ?>" target="_blank" class="btn btn-outline-dark btn-sm" title="Imprimir Tiquete Térmico">
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
