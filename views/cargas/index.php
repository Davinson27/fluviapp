<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Control de Carga y Encomiendas Fluviales</h4>
        <p class="text-muted small mb-0">Gestión de paquetes, peso en bodega, fletes y trazabilidad</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (!empty($deptScope)): ?>
            <span class="badge bg-warning text-dark py-2 px-3"><i class="fa-solid fa-location-dot me-1"></i>Jurisdicción: <?= htmlspecialchars($deptScope) ?></span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/cargas/crear" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Registrar Nueva Encomienda
        </a>
    </div>
</div>

<div class="card bg-white shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No. Guía</th>
                        <th>Descripción</th>
                        <th>Remitente &rarr; Destinatario</th>
                        <th>Viaje / Trayecto</th>
                        <th>Peso</th>
                        <th>Flete</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cargas)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No se registran encomiendas activas</td></tr>
                    <?php else: ?>
                        <?php foreach ($cargas as $c): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($c['guia_numero']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($c['descripcion_carga']) ?></div>
                                    <small class="text-muted">Declarado: $<?= number_format($c['valor_declarado'], 0, ',', '.') ?></small>
                                </td>
                                <td>
                                    <div><strong>De:</strong> <?= htmlspecialchars($c['remitente_nombre']) ?> (<?= htmlspecialchars($c['remitente_telefono']) ?>)</div>
                                    <div><strong>Para:</strong> <?= htmlspecialchars($c['destinatario_nombre']) ?> (<?= htmlspecialchars($c['destinatario_telefono']) ?>)</div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($c['origen_nombre']) ?></strong> &rarr; <strong><?= htmlspecialchars($c['destino_nombre']) ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-ship me-1"></i><?= htmlspecialchars($c['embarcacion_nombre']) ?></div>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= number_format($c['peso_kg'], 1) ?> kg</span></td>
                                <td class="fw-bold text-success">$<?= number_format($c['valor_flete'], 0, ',', '.') ?></td>
                                <td>
                                    <form action="<?= BASE_URL ?>/cargas/cambiar-estado" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <select name="estado" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="font-size:0.75rem; width:auto;">
                                            <option value="registrada" <?= $c['estado'] === 'registrada' ? 'selected' : '' ?>>Registrada</option>
                                            <option value="cargada" <?= $c['estado'] === 'cargada' ? 'selected' : '' ?>>Cargada en Bodega</option>
                                            <option value="en_transito" <?= $c['estado'] === 'en_transito' ? 'selected' : '' ?>>En Tránsito</option>
                                            <option value="entregada" <?= $c['estado'] === 'entregada' ? 'selected' : '' ?>>Entregada</option>
                                            <option value="cancelada" <?= $c['estado'] === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>/cargas/factura?id=<?= $c['id'] ?>" target="_blank" class="btn btn-outline-success btn-sm" title="Ver / Descargar Factura de Flete (PDF)">
                                        <i class="fa-solid fa-file-invoice-dollar me-1"></i>Factura
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
