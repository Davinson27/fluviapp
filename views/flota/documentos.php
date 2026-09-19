<?php
// =======================================================
// Vista: Custodia Documental DIMAR - Semáforo de Vencimiento
// FluviApp v2.0
// =======================================================
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-file-shield me-2 text-warning"></i>Semáforo Documental y Normativo DIMAR</h4>
        <p class="text-muted small mb-0">Control de patentes de navegación, pólizas de seguros todo riesgo y certificados de seguridad.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/flota" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Volver a Flota</a>
        <button type="button" class="btn btn-warning btn-sm fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalNuevoDocumento">
            <i class="fa-solid fa-plus me-1"></i> Registrar Documento
        </button>
    </div>
</div>

<div class="card bg-white shadow-sm border-0 rounded-4 overflow-hidden">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Semáforo</th>
                    <th>Embarcación</th>
                    <th>Tipo de Documento</th>
                    <th>Número / Radicado</th>
                    <th>Entidad Emisora</th>
                    <th>Expedición</th>
                    <th>Vencimiento</th>
                    <th>Días Restantes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documentos)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay documentos registrados aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documentos as $d): ?>
                        <tr>
                            <td>
                                <?php if ($d['estado_semaforo'] === 'vencido'): ?>
                                    <span class="badge bg-danger p-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> VENCIDO</span>
                                <?php elseif ($d['estado_semaforo'] === 'por_vencer'): ?>
                                    <span class="badge bg-warning text-dark p-2"><i class="fa-solid fa-clock me-1"></i> POR VENCER</span>
                                <?php else: ?>
                                    <span class="badge bg-success p-2"><i class="fa-solid fa-circle-check me-1"></i> VIGENTE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($d['embarcacion_nombre']) ?></strong>
                                <div class="small text-muted"><?= htmlspecialchars($d['embarcacion_matricula']) ?></div>
                            </td>
                            <td>
                                <strong class="text-dark text-capitalize"><?= str_replace('_', ' ', $d['tipo_documento']) ?></strong>
                            </td>
                            <td><code><?= htmlspecialchars($d['numero_documento']) ?></code></td>
                            <td><?= htmlspecialchars($d['entidad_emisora']) ?></td>
                            <td class="small text-muted"><?= $d['fecha_expedicion'] ?></td>
                            <td><strong><?= $d['fecha_vencimiento'] ?></strong></td>
                            <td>
                                <?php if ($d['dias_restantes'] < 0): ?>
                                    <span class="text-danger fw-bold">Venció hace <?= abs($d['dias_restantes']) ?> días</span>
                                <?php else: ?>
                                    <span class="<?= $d['dias_restantes'] <= 30 ? 'text-warning-emphasis fw-bold' : 'text-success' ?>">
                                        <?= $d['dias_restantes'] ?> días
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Documento -->
<div class="modal fade" id="modalNuevoDocumento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>/flota/documentos/guardar" method="POST">
                <?= SessionHelper::csrfField() ?>
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-shield me-2"></i>Registrar Documento / Póliza de Embarcación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Embarcación *</label>
                            <select name="embarcacion_id" class="form-select" required>
                                <?php foreach ($embarcaciones as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['matricula']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Documento *</label>
                            <select name="tipo_documento" class="form-select" required>
                                <option value="patente_navegacion">Patente de Navegación Fluvial</option>
                                <option value="certificado_seguridad">Certificado de Navegabilidad y Seguridad</option>
                                <option value="poliza_seguro">Póliza de Responsabilidad Civil Contractual / Extracontractual</option>
                                <option value="inspeccion_fluvial">Inspección Fluvial Técnica Anual</option>
                                <option value="otro">Otro Documento Legal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Número de Documento / Póliza *</label>
                            <input type="text" name="numero_documento" class="form-control" placeholder="Ej: POL-RC-98765432" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Entidad Emisora *</label>
                            <input type="text" name="entidad_emisora" class="form-control" value="DIMAR / Inspección Fluvial" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Expedición *</label>
                            <input type="date" name="fecha_expedicion" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones / Coberturas</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Amparos de la póliza, vigencia del certificado..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Guardar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
