<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Catálogo de Flota Fluvial</h4>
        <p class="text-muted small mb-0">Gestión y control de embarcaciones, lanchas y ferries</p>
    </div>
    <a href="<?= BASE_URL ?>/embarcaciones/crear" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-plus me-2"></i>Registrar Embarcación
    </a>
</div>

<div class="card bg-white shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Embarcación</th>
                        <th>Matrícula</th>
                        <th>Tipo</th>
                        <th>Capacidad Pasajeros</th>
                        <th>Capacidad Carga</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($embarcaciones)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay embarcaciones registradas</td></tr>
                    <?php else: ?>
                        <?php foreach ($embarcaciones as $e): ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <i class="fa-solid fa-ship me-2 text-info"></i><?= htmlspecialchars($e['nombre']) ?>
                                </td>
                                <td><code><?= htmlspecialchars($e['matricula']) ?></code></td>
                                <td class="text-capitalize"><?= str_replace('_', ' ', $e['tipo']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= $e['capacidad_pasajeros'] ?> personas</span></td>
                                <td><span class="badge bg-light text-dark border"><?= number_format($e['capacidad_carga_kg'], 0, ',', '.') ?> kg</span></td>
                                <td>
                                    <?php
                                    $estadoBadge = match($e['estado']) {
                                        'operativo'      => 'bg-success',
                                        'mantenimiento'  => 'bg-warning text-dark',
                                        'fuera_servicio' => 'bg-danger',
                                        default          => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $estadoBadge ?> text-uppercase"><?= str_replace('_', ' ', $e['estado']) ?></span>
                                </td>
                                <td class="text-end">
                                    <?php if (AuthHelper::user()['rol'] === 'admin'): ?>
                                    <form action="<?= BASE_URL ?>/embarcaciones/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta embarcación?');">
                                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
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
