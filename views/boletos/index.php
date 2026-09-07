<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Control de Pasajes y Boletería</h4>
        <p class="text-muted small mb-0">Emisión y registro de tiquetes para pasajeros fluviales</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (!empty($deptScope)): ?>
            <span class="badge bg-warning text-dark py-2 px-3"><i class="fa-solid fa-location-dot me-1"></i>Jurisdicción: <?= htmlspecialchars($deptScope) ?></span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/boletos/crear" class="btn btn-success fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Emitir Nuevo Boleto
        </a>
    </div>
</div>

<div class="card bg-white shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código Boleto</th>
                        <th>Pasajero</th>
                        <th>Documento</th>
                        <th>Viaje / Trayecto</th>
                        <th>Fecha / Asiento</th>
                        <th>Tarifa Pagada</th>
                        <th>Vendido Por</th>
                        <th class="text-end">Imprimir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($boletos)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No se han emitido boletos aún</td></tr>
                    <?php else: ?>
                        <?php foreach ($boletos as $b): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($b['codigo_boleto']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($b['pasajero_nombre']) ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($b['pasajero_telefono'] ?: 'N/A') ?></small>
                                </td>
                                <td><code><?= htmlspecialchars($b['pasajero_documento']) ?></code></td>
                                <td>
                                    <strong><?= htmlspecialchars($b['origen_nombre']) ?></strong> &rarr; <strong><?= htmlspecialchars($b['destino_nombre']) ?></strong>
                                    <div class="small text-muted"><i class="fa-solid fa-ship me-1"></i><?= htmlspecialchars($b['embarcacion_nombre']) ?></div>
                                </td>
                                <td>
                                    <div><?= $b['fecha_salida'] ?> <?= substr($b['hora_salida'], 0, 5) ?></div>
                                    <small class="badge bg-secondary">Asiento #<?= $b['numero_asiento'] ?? '-' ?></small>
                                </td>
                                <td class="fw-bold text-success">$<?= number_format($b['precio_pagado'], 0, ',', '.') ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($b['vendedor_nombre'] ?? 'Sistema') ?></small></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>/boletos/factura?id=<?= $b['id'] ?>" target="_blank" class="btn btn-outline-success btn-sm me-1" title="Ver / Descargar Factura Digital">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $b['id'] ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Imprimir Tiquete Térmico">
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
