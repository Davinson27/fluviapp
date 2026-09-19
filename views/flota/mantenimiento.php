<?php
// =======================================================
// Vista: Bitácora de Mantenimiento de Flota - FluviApp v2.0
// =======================================================
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-screwdriver-wrench me-2 text-primary"></i>Bitácora de Mantenimiento Técnico</h4>
        <p class="text-muted small mb-0">Historial preventivo y correctivo de motores, cascos y sistemas náuticos.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/flota" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i>Volver a Flota</a>
        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevoMantenimiento">
            <i class="fa-solid fa-plus me-1"></i> Registrar Mantenimiento
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
                    <th>Título / Intervención</th>
                    <th>Responsable</th>
                    <th>Costo (COP)</th>
                    <th>Próximo Servicio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mantenimientos)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay mantenimientos registrados aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mantenimientos as $m): ?>
                        <tr>
                            <td><?= $m['fecha_mantenimiento'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($m['embarcacion_nombre']) ?></strong>
                                <div class="small text-muted"><?= htmlspecialchars($m['embarcacion_matricula']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase"><?= htmlspecialchars($m['tipo_mantenimiento']) ?></span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($m['titulo']) ?></strong>
                                <div class="small text-muted"><?= htmlspecialchars(substr($m['descripcion'], 0, 80)) ?></div>
                            </td>
                            <td><?= htmlspecialchars($m['responsable'] ?: 'Taller Interno') ?></td>
                            <td class="fw-bold text-dark">$<?= number_format($m['costo'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($m['proximo_mantenimiento']): ?>
                                    <span class="badge bg-info-subtle text-info-emphasis"><?= $m['proximo_mantenimiento'] ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">No fijado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Mantenimiento -->
<div class="modal fade" id="modalNuevoMantenimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>/flota/mantenimiento/guardar" method="POST">
                <?= SessionHelper::csrfField() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-wrench me-2"></i>Registrar Mantenimiento Técnico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <label class="form-label fw-semibold">Tipo de Mantenimiento *</label>
                            <select name="tipo_mantenimiento" class="form-select" required>
                                <option value="preventivo">Preventivo Programado</option>
                                <option value="correctivo">Correctivo / Reparación</option>
                                <option value="motor">Motor Fuera de Borda / Diésel</option>
                                <option value="casco">Casco / Fibra / Aluminio</option>
                                <option value="electrico">Sistema Eléctrico y Luces</option>
                                <option value="emergencia">Emergencia en Navegación</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título del Mantenimiento *</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Ej: Cambio de aceite de motores y filtro de gasolina" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción Detallada *</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de repuestos utilizados, hallazgos, estado del motor..." required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Costo Total (COP) *</label>
                            <input type="number" name="costo" class="form-control" min="0" step="1000" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de Intervención *</label>
                            <input type="date" name="fecha_mantenimiento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Próximo Mantenimiento</label>
                            <input type="date" name="proximo_mantenimiento" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Técnico / Responsable *</label>
                            <input type="text" name="responsable" class="form-control" placeholder="Nombre del mecánico o astillero" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado *</label>
                            <select name="estado" class="form-select">
                                <option value="completado">Completado / Operativo</option>
                                <option value="en_proceso">En Proceso en Astillero</option>
                                <option value="programado">Programado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar en Bitácora</button>
                </div>
            </form>
        </div>
    </div>
</div>
