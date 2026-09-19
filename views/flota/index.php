<?php
// =======================================================
// Vista: Dashboard de Flota - FluviApp v2.0
// =======================================================
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-anchor me-2 text-primary"></i>Gestión Integral de Flota Fluvial</h4>
        <p class="text-muted small mb-0">Monitoreo técnico de embarcaciones, bitácora de mantenimiento, combustible y cumplimiento normativo DIMAR.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/flota/mantenimiento" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-wrench me-1"></i> Mantenimiento
        </a>
        <a href="<?= BASE_URL ?>/flota/combustible" class="btn btn-outline-success btn-sm">
            <i class="fa-solid fa-gas-pump me-1"></i> Combustible
        </a>
        <a href="<?= BASE_URL ?>/flota/documentos" class="btn btn-outline-warning btn-sm">
            <i class="fa-solid fa-file-shield me-1"></i> Semáforo DIMAR
        </a>
    </div>
</div>

<!-- Tarjetas KPI de Flota -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Flota Registrada</span>
                    <h3 class="fw-bold text-primary mb-0"><?= count($embarcaciones) ?></h3>
                    <span class="text-muted small">Embarcaciones activas</span>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                    <i class="fa-solid fa-ship fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Combustible Total</span>
                    <h3 class="fw-bold text-success mb-0"><?= number_format($metricasCombustible['total_galones'], 1) ?></h3>
                    <span class="text-muted small">Galones abastecidos</span>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-circle">
                    <i class="fa-solid fa-gas-pump fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Gasto Combustible</span>
                    <h3 class="fw-bold text-dark mb-0">$<?= number_format($metricasCombustible['total_gasto_combustible'], 0, ',', '.') ?></h3>
                    <span class="text-muted small">COP invertidos</span>
                </div>
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-circle">
                    <i class="fa-solid fa-money-bill-wave fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card bg-white shadow-sm border-0 rounded-4 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">Semáforo DIMAR</span>
                    <div class="d-flex gap-2 align-items-center mt-1">
                        <span class="badge bg-danger" title="Documentos vencidos"><?= $alertasDocumentos['vencidos'] ?? 0 ?> Vencidos</span>
                        <span class="badge bg-warning text-dark" title="Próximos a vencer"><?= $alertasDocumentos['por_vencer'] ?? 0 ?> Por vencer</span>
                    </div>
                </div>
                <div class="p-3 bg-danger-subtle text-danger rounded-circle">
                    <i class="fa-solid fa-shield-halved fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Embarcaciones y Mantenimientos Recientes -->
<div class="row g-4">
    <div class="col-12 col-lg-7">
        <div class="card bg-white shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i>Estado Operativo de Embarcaciones</span>
                <a href="<?= BASE_URL ?>/embarcaciones" class="btn btn-link btn-sm text-primary p-0">Ver catálogo completo</a>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Embarcación</th>
                            <th>Matrícula</th>
                            <th>Tipo</th>
                            <th>Capacidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($embarcaciones as $e): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                                <td><code><?= htmlspecialchars($e['matricula']) ?></code></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($e['tipo']) ?></span></td>
                                <td><?= $e['capacidad_pasajeros'] ?> pas / <?= number_format($e['capacidad_carga_kg']) ?> kg</td>
                                <td>
                                    <?php 
                                        $badgeEstado = match($e['estado']) {
                                            'operativo'     => 'bg-success',
                                            'mantenimiento' => 'bg-warning text-dark',
                                            default         => 'bg-danger'
                                        };
                                    ?>
                                    <span class="badge <?= $badgeEstado ?>"><?= ucfirst($e['estado']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card bg-white shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="fa-solid fa-screwdriver-wrench me-2 text-warning"></i>Últimos Mantenimientos</span>
                <a href="<?= BASE_URL ?>/flota/mantenimiento" class="btn btn-link btn-sm text-primary p-0">Ver todos</a>
            </div>
            <div class="card-body p-3">
                <?php if (empty($mantenimientosRecientes)): ?>
                    <p class="text-muted small text-center my-3">No hay mantenimientos registrados aún.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($mantenimientosRecientes as $m): ?>
                            <div class="list-group-item px-2 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-dark small"><?= htmlspecialchars($m['titulo']) ?></strong>
                                    <span class="badge bg-light text-dark border"><?= $m['fecha_mantenimiento'] ?></span>
                                </div>
                                <div class="text-muted small mb-1">
                                    Barco: <strong><?= htmlspecialchars($m['embarcacion_nombre']) ?></strong> &bull; Costo: $<?= number_format($m['costo'], 0, ',', '.') ?> COP
                                </div>
                                <div class="small text-muted" style="font-size: 11px;">
                                    <?= htmlspecialchars(substr($m['descripcion'], 0, 70)) ?>...
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
