<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-boxes-packing me-2 text-primary"></i>Mis Encomiendas y Cargas Fluviales</h4>
        <p class="text-muted small mb-0">Rastreo y estado de entrega de tus envíos fluviales</p>
    </div>
    <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-plus me-2"></i>Enviar Nueva Encomienda
    </a>
</div>

<div class="card bg-white shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Número de Guía</th>
                        <th>Descripción Carga</th>
                        <th>Destinatario</th>
                        <th>Trayecto Fluvial</th>
                        <th>Peso / Flete</th>
                        <th>Fecha Itinerario</th>
                        <th>Estado Envío</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($encomiendas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="display-3 text-muted mb-3"><i class="fa-solid fa-boxes-packing"></i></div>
                                <h6 class="fw-bold text-secondary">No tienes encomiendas registradas aún.</h6>
                                <p class="text-muted small">Realiza tus envíos fluviales de paquetes y mercancía con nosotros.</p>
                                <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="btn btn-primary btn-sm mt-2">Enviar Encomienda</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($encomiendas as $c): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($c['guia_numero'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($c['descripcion_carga'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['destinatario_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($c['destinatario_telefono'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['origen_nombre'], ENT_QUOTES, 'UTF-8') ?></strong> &rarr; <strong><?= htmlspecialchars($c['destino_nombre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-ship me-1"></i><?= htmlspecialchars($c['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td>
                                    <div><span class="badge bg-light text-dark border"><?= number_format($c['peso_kg'], 1) ?> kg</span></div>
                                    <strong class="text-success">$<?= number_format($c['valor_flete'], 0, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <div><?= $c['fecha_salida'] ?></div>
                                    <small class="text-muted"><?= substr($c['hora_salida'], 0, 5) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $cBadge = match($c['estado']) {
                                        'registrada'  => 'bg-info',
                                        'cargada'     => 'bg-warning text-dark',
                                        'en_transito' => 'bg-primary',
                                        'entregada'   => 'bg-success',
                                        'cancelada'   => 'bg-danger',
                                        default       => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $cBadge ?> text-uppercase"><?= str_replace('_', ' ', $c['estado']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
