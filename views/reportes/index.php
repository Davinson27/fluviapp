<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Reportes y Estadísticas Operativas</h4>
        <p class="text-muted small mb-0">Rendimiento por trayectos, volumen de pasajes y utilización de flota fluvial</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-semibold">
        <i class="fa-solid fa-print me-1"></i>Imprimir Reporte
    </button>
</div>

<div class="row g-4 mb-4">
    <!-- Reporte de Rutas -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-route me-2 text-danger"></i>Rendimiento por Ruta Fluvial
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ruta</th>
                                <th>Boletos Vendidos</th>
                                <th>Ingresos Generados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reporteRutas)): ?>
                                <tr><td colspan="3" class="text-center py-3 text-muted">Sin datos suficientes</td></tr>
                            <?php else: ?>
                                <?php foreach ($reporteRutas as $r): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($r['origen']) ?></strong> &rarr; <strong><?= htmlspecialchars($r['destino']) ?></strong></td>
                                        <td><span class="badge bg-light text-dark border"><?= $r['total_pasajes'] ?> pasajes</span></td>
                                        <td class="fw-bold text-success">$<?= number_format($r['total_ingresos'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reporte de Embarcaciones -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100">
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-ship me-2 text-info"></i>Utilización de la Flota
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Embarcación</th>
                                <th>Viajes</th>
                                <th>Pasajeros</th>
                                <th>Carga (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reporteFlota)): ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">Sin datos suficientes</td></tr>
                            <?php else: ?>
                                <?php foreach ($reporteFlota as $f): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($f['nombre']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($f['matricula']) ?></small>
                                        </td>
                                        <td><span class="badge bg-primary"><?= $f['viajes_realizados'] ?></span></td>
                                        <td><span class="badge bg-success"><?= $f['pasajeros_transportados'] ?> Pax</span></td>
                                        <td><span class="badge bg-warning text-dark"><?= number_format($f['carga_transportada_kg'], 1) ?> kg</span></td>
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
