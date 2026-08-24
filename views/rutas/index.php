<div class="row g-4">
    <!-- Formulario para agregar ruta -->
    <div class="col-12 col-lg-4">
        <div class="card bg-white shadow-sm">
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-route me-2 text-primary"></i>Registrar Ruta Fluvial
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/rutas/guardar" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Muelle de Origen *</label>
                        <select name="muelle_origen_id" class="form-select" required>
                            <option value="">-- Seleccione Origen --</option>
                            <?php foreach ($muelles as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['municipio']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Muelle de Destino *</label>
                        <select name="muelle_destino_id" class="form-select" required>
                            <option value="">-- Seleccione Destino --</option>
                            <?php foreach ($muelles as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['municipio']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Distancia (km)</label>
                            <input type="number" step="0.1" name="distancia_km" class="form-control" value="30" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Duración (min)</label>
                            <input type="number" name="duracion_estimada_min" class="form-control" value="45" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tarifa Base ($ COP) *</label>
                        <input type="number" step="100" name="tarifa_base" class="form-control" placeholder="Ej: 25000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activa">Activa</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i>Guardar Ruta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de rutas -->
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header fw-bold text-dark">
                <i class="fa-solid fa-map-location-dot me-2 text-danger"></i>Rutas Fluviales Habilitadas
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Trayecto Fluvial</th>
                                <th>Distancia / Tiempo</th>
                                <th>Tarifa Base</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rutas)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No hay rutas registradas</td></tr>
                            <?php else: ?>
                                <?php foreach ($rutas as $r): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= htmlspecialchars($r['origen_nombre']) ?> 
                                                <i class="fa-solid fa-arrow-right-long text-muted mx-1"></i> 
                                                <?= htmlspecialchars($r['destino_nombre']) ?>
                                            </div>
                                            <small class="text-muted"><?= htmlspecialchars($r['origen_municipio']) ?> a <?= htmlspecialchars($r['destino_municipio']) ?></small>
                                        </td>
                                        <td><?= $r['distancia_km'] ?> km <br><small class="text-muted">~<?= $r['duracion_estimada_min'] ?> mins</small></td>
                                        <td class="fw-bold text-success">$<?= number_format($r['tarifa_base'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $r['estado'] === 'activa' ? 'success' : 'danger' ?> text-uppercase">
                                                <?= $r['estado'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if (AuthHelper::user()['rol'] === 'admin'): ?>
                                            <form action="<?= BASE_URL ?>/rutas/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar esta ruta?');">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
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
