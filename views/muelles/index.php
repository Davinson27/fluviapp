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
                        <input type="text" name="departamento" class="form-control" placeholder="Ej: Bolívar" required>
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
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-list me-2 text-info"></i>Muelles y Puertos Fluviales
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
                                            <form action="<?= BASE_URL ?>/muelles/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar este muelle?');">
                                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
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
