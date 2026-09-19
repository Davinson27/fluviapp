<?php
// =======================================================
// Vista: Control de Combustible - FluviApp v2.0
// =======================================================
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-gas-pump me-2 text-success"></i>Control de Combustible Fluvial</h4>
        <p class="text-muted small mb-0">Registro de abastecimiento de gasolina / diésel, costos y rendimiento operativo.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/flota" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Volver a Flota</a>
        <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoCombustible">
            <i class="fa-solid fa-plus me-1"></i> Registrar Tanqueo
        </button>
    </div>
</div>

<div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Fecha</th>
                    <th>Embarcación</th>
                    <th>Tipo</th>
                    <th>Galones</th>
                    <th>Precio / Galón</th>
                    <th>Total Pagado</th>
                    <th>Proveedor / Estación</th>
                    <th>Viaje Asociado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($combustibles)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay registros de combustible aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($combustibles as $c): ?>
                        <tr>
                            <td><?= $c['fecha'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($c['embarcacion_nombre']) ?></strong>
                                <div class="small text-muted"><?= htmlspecialchars($c['embarcacion_matricula']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase"><?= $c['tipo_combustible'] ?></span>
                            </td>
                            <td class="fw-bold text-primary"><?= number_format($c['galones'], 1) ?> gal</td>
                            <td>$<?= number_format($c['precio_por_galon'], 0, ',', '.') ?></td>
                            <td class="fw-bold text-success">$<?= number_format($c['total_pagado'], 0, ',', '.') ?> COP</td>
                            <td><?= htmlspecialchars($c['proveedor'] ?: 'Estación Fluvial') ?></td>
                            <td>
                                <?php if (!empty($c['codigo_viaje'])): ?>
                                    <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($c['codigo_viaje']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">General</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Tanqueo -->
<div class="modal fade" id="modalNuevoCombustible" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>/flota/combustible/guardar" method="POST">
                <?= SessionHelper::csrfField() ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-gas-pump me-2"></i>Registrar Abastecimiento de Combustible</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Embarcación *</label>
                            <select name="embarcacion_id" class="form-select" required>
                                <?php foreach ($embarcaciones as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['matricula']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipo de Combustible *</label>
                            <select name="tipo_combustible" class="form-select" required>
                                <option value="gasolina">Gasolina Corriente</option>
                                <option value="diesel">Diésel Fluvial</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha *</label>
                            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Galones Abastecidos *</label>
                            <input type="number" id="input_galones" name="galones" class="form-control" step="0.1" min="1" placeholder="Ej: 35.5" oninput="calcularTotalCombustible()" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Precio por Galón (COP) *</label>
                            <input type="number" id="input_precio_galon" name="precio_por_galon" class="form-control" step="100" min="1000" placeholder="Ej: 16500" oninput="calcularTotalCombustible()" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Total Pagado (COP)</label>
                            <input type="number" id="input_total_pagado" name="total_pagado" class="form-control fw-bold bg-light" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Estación Fluvial / Proveedor</label>
                            <input type="text" name="proveedor" class="form-control" placeholder="Ej: EDS Fluvial El Muelle">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calcularTotalCombustible() {
    const gal = parseFloat(document.getElementById('input_galones').value) || 0;
    const precio = parseFloat(document.getElementById('input_precio_galon').value) || 0;
    document.getElementById('input_total_pagado').value = Math.round(gal * precio);
}
</script>
