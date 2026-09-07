<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold text-dark mb-1">Itinerarios de Viajes y Zarpes</h4>
        <p class="text-muted small mb-0">Control de salidas, embarque, manifiestos y estados de navegación</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (!empty($deptScope)): ?>
            <span class="badge bg-warning text-dark py-2 px-3"><i class="fa-solid fa-location-dot me-1"></i>Jurisdicción: <?= htmlspecialchars($deptScope) ?></span>
        <?php else: ?>
            <span class="badge bg-primary py-2 px-3"><i class="fa-solid fa-earth-americas me-1"></i>Cobertura Nacional</span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/viajes/crear" class="btn btn-primary fw-semibold">
            <i class="fa-solid fa-calendar-plus me-2"></i>Programar Itinerario
        </a>
    </div>
</div>

<!-- Filtros de estado -->
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="<?= BASE_URL ?>/viajes" class="btn btn-sm <?= empty($estadoFiltro) ? 'btn-dark' : 'btn-outline-dark' ?>">Todos</a>
    <a href="<?= BASE_URL ?>/viajes?estado=programado" class="btn btn-sm <?= $estadoFiltro === 'programado' ? 'btn-info text-white' : 'btn-outline-info' ?>">Programados</a>
    <a href="<?= BASE_URL ?>/viajes?estado=en_embarque" class="btn btn-sm <?= $estadoFiltro === 'en_embarque' ? 'btn-warning' : 'btn-outline-warning' ?>">En Embarque</a>
    <a href="<?= BASE_URL ?>/viajes?estado=en_navegacion" class="btn btn-sm <?= $estadoFiltro === 'en_navegacion' ? 'btn-primary' : 'btn-outline-primary' ?>">En Navegación</a>
    <a href="<?= BASE_URL ?>/viajes?estado=arribado" class="btn btn-sm <?= $estadoFiltro === 'arribado' ? 'btn-success' : 'btn-outline-success' ?>">Arribados</a>
</div>

<div class="card bg-white shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Viaje</th>
                        <th>Ruta Fluvial</th>
                        <th>Embarcación / Capitán</th>
                        <th>Fecha / Hora Salida</th>
                        <th>Ocupación Pasajeros</th>
                        <th>Carga (kg)</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($viajes)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron itinerarios de viaje</td></tr>
                    <?php else: ?>
                        <?php foreach ($viajes as $v): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary"><?= htmlspecialchars($v['codigo_viaje']) ?></span>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($v['origen_nombre']) ?></strong> &rarr; <strong><?= htmlspecialchars($v['destino_nombre']) ?></strong>
                                    <div class="small text-muted">
                                        <?php if (in_array(AuthHelper::user()['rol'], ['admin', 'operador'])): ?>
                                        <form action="<?= BASE_URL ?>/viajes/editar-precio" method="POST" class="d-inline-flex align-items-center gap-1 mt-1">
                                            <input type="hidden" name="viaje_id" value="<?= $v['id'] ?>">
                                            <div class="input-group input-group-sm" style="width: 130px;">
                                                <span class="input-group-text p-1">$</span>
                                                <input type="number" step="100" name="precio_pasaje" class="form-control form-control-sm fw-bold text-success p-1" value="<?= $v['precio_pasaje'] ?>" required>
                                                <button type="submit" class="btn btn-outline-success btn-sm p-1" title="Actualizar Precio"><i class="fa-solid fa-check"></i></button>
                                            </div>
                                        </form>
                                        <?php else: ?>
                                        Tarifa: $<?= number_format($v['precio_pasaje'], 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-ship me-1 text-info"></i><?= htmlspecialchars($v['embarcacion_nombre']) ?></div>
                                    <small class="text-muted"><i class="fa-solid fa-user-astronaut me-1"></i><?= htmlspecialchars($v['capitan_nombre'] ?? 'Sin asignar') ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= $v['fecha_salida'] ?></div>
                                    <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= substr($v['hora_salida'], 0, 5) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= $v['boletos_vendidos'] ?> / <?= $v['embarcacion_cap_pasajeros'] ?> Pax
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= number_format($v['carga_total_kg'], 1) ?> kg
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match($v['estado']) {
                                        'programado'    => 'bg-info',
                                        'en_embarque'   => 'bg-warning text-dark',
                                        'en_navegacion' => 'bg-primary',
                                        'arribado'      => 'bg-success',
                                        'cancelado'     => 'bg-danger',
                                        default         => 'bg-secondary'
                                    };
                                    ?>
                                    <!-- Dropdown para cambiar estado del viaje -->
                                    <form action="<?= BASE_URL ?>/viajes/cambiar-estado" method="POST" class="d-inline">
                                        <input type="hidden" name="viaje_id" value="<?= $v['id'] ?>">
                                        <select name="estado" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()" style="font-size:0.75rem; width:auto;">
                                            <option value="programado" <?= $v['estado'] === 'programado' ? 'selected' : '' ?>>Programado</option>
                                            <option value="en_embarque" <?= $v['estado'] === 'en_embarque' ? 'selected' : '' ?>>En Embarque</option>
                                            <option value="en_navegacion" <?= $v['estado'] === 'en_navegacion' ? 'selected' : '' ?>>En Navegación</option>
                                            <option value="arribado" <?= $v['estado'] === 'arribado' ? 'selected' : '' ?>>Arribado</option>
                                            <option value="cancelado" <?= $v['estado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/viajes/manifiesto?id=<?= $v['id'] ?>" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold" title="Ver Manifiesto Oficial de Zarpe">
                                        <i class="fa-solid fa-file-lines me-1"></i>Manifiesto
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
