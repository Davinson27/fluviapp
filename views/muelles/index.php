<div class="row g-4">
    <!-- Formulario para agregar muelle -->
    <div class="col-12 col-lg-4">
        <div class="card bg-white shadow-sm">
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-water me-2 text-primary"></i>Registrar Muelle / Puerto
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/muelles/guardar" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Muelle / Puerto *</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Muelle La Esperanza" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Río / Afluente *</label>
                        <input type="text" name="rio" class="form-control" placeholder="Ej: Río Magdalena" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Municipio / Ciudad *</label>
                        <input type="text" name="municipio" class="form-control" placeholder="Ej: Magangué" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Departamento *</label>
                        <?php if (!empty($deptScope)): ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($deptScope) ?>" readonly>
                            <input type="hidden" name="departamento" value="<?= htmlspecialchars($deptScope) ?>">
                            <small class="text-muted"><i class="fa-solid fa-lock me-1"></i>Fijado a su jurisdicción departamental.</small>
                        <?php else: ?>
                            <input type="text" name="departamento" class="form-control" placeholder="Ej: Bolívar" required>
                        <?php endif; ?>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Latitud (GPS)</label>
                            <input type="number" step="0.0000001" name="latitud" class="form-control" placeholder="Ej: 9.2423000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Longitud (GPS)</label>
                            <input type="number" step="0.0000001" name="longitud" class="form-control" placeholder="Ej: -74.7547000">
                        </div>
                        <div class="col-12">
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-map-pin text-info me-1"></i>Opcional: Si se deja en blanco, el sistema lo georreferenciará automáticamente para el mapa.
                            </small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción / Características</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Ej: Terminal fluvial de carga y pasajeros sobre el brazo principal"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i>Guardar Muelle
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de muelles -->
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center fw-bold text-dark">
                <span><i class="fa-solid fa-list me-2 text-info"></i>Muelles y Puertos Fluviales</span>
                <?php if (!empty($deptScope)): ?>
                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($deptScope) ?> (<?= count($muelles) ?>)</span>
                <?php else: ?>
                    <span class="badge bg-primary"><i class="fa-solid fa-earth-americas me-1"></i>Nacional (<?= count($muelles) ?>)</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Muelle / Puerto</th>
                                <th>Río</th>
                                <th>Municipio / Dpto</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($muelles)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No hay muelles registrados</td></tr>
                            <?php else: ?>
                                <?php foreach ($muelles as $m): ?>
                                    <tr>
                                        <td class="fw-bold text-dark">
                                            <i class="fa-solid fa-anchor me-2 text-secondary"></i><?= htmlspecialchars($m['nombre']) ?>
                                        </td>
                                        <td><?= htmlspecialchars($m['rio']) ?></td>
                                        <td><?= htmlspecialchars($m['municipio']) ?>, <?= htmlspecialchars($m['departamento']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $m['estado'] === 'activo' ? 'success' : 'danger' ?> text-uppercase">
                                                <?= $m['estado'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if (AuthHelper::user()['rol'] === 'admin'): ?>
                                            <a href="<?= BASE_URL ?>/muelles/editar?id=<?= $m['id'] ?>" class="btn btn-outline-primary btn-sm me-1" title="Editar Nombre y Datos del Muelle">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form action="<?= BASE_URL ?>/muelles/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar este muelle?');">
                                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar Muelle">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
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
