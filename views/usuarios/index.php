<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Gestión de Usuarios y Roles</h4>
        <p class="text-muted small mb-0">Administración de cuentas, niveles de acceso y personal de muelle</p>
    </div>
    <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-user-plus me-2"></i>Registrar Nuevo Usuario
    </a>
</div>

<?php if ($isGeneralAdmin): ?>
<div class="alert alert-info border-info-subtle py-2 px-3 small mb-3 d-flex align-items-center">
    <i class="fa-solid fa-shield-halved fs-5 me-2 text-primary"></i>
    <div>
        <strong>Administrador General (Ámbito Nacional):</strong> Puedes asignar a cualquier administrador un departamento específico para que opere de forma 100% independiente en su territorio, o dejarlo en blanco para acceso general.
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning border-warning-subtle py-2 px-3 small mb-3 d-flex align-items-center">
    <i class="fa-solid fa-location-dot fs-5 me-2 text-warning"></i>
    <div>
        <strong>Administración Departamental:</strong> Estás gestionando usuarios y personal asignados exclusivamente al departamento de <strong><?= htmlspecialchars($deptScope) ?></strong>.
    </div>
</div>
<?php endif; ?>

<div class="card bg-white shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Rol Asignado</th>
                        <th>Departamento / Jurisdicción</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron usuarios registrados en esta jurisdicción</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $u['id'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($u['foto']) && file_exists(ROOT_PATH . '/public/' . ltrim($u['foto'], '/'))): ?>
                                            <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars(ltrim($u['foto'], '/')) ?>" alt="Foto" class="rounded-circle object-fit-cover me-2 shadow-sm border" style="width: 34px; height: 34px;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 text-secondary border" style="width: 34px; height: 34px;">
                                                <i class="fa-solid fa-user small"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                <td><?= htmlspecialchars($u['telefono'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php
                                    $rolBadge = match($u['rol']) {
                                        'admin'    => 'bg-danger',
                                        'operador' => 'bg-info text-white',
                                        'taquilla' => 'bg-success',
                                        'capitan'  => 'bg-primary',
                                        default    => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $rolBadge ?> text-uppercase"><?= $u['rol'] ?></span>
                                </td>
                                <td>
                                    <?php if ($u['rol'] === 'admin'): ?>
                                        <?php if (!empty($u['departamento'])): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($u['departamento']) ?>
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Admin Departamental</small>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="fa-solid fa-earth-americas me-1"></i>Nacional (General)
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Control Total</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (!empty($u['departamento'])): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fa-solid fa-map-pin me-1 text-primary"></i><?= htmlspecialchars($u['departamento']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Todos / Nacional</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $u['estado'] === 'activo' ? 'success' : 'danger' ?> text-uppercase">
                                        <?= $u['estado'] ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?= BASE_URL ?>/usuarios/editar?id=<?= $u['id'] ?>" class="btn btn-outline-primary btn-sm me-1" title="Editar Usuario">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <?php if ((int)$u['id'] !== (int)AuthHelper::user()['id']): ?>
                                    <form action="<?= BASE_URL ?>/usuarios/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar el usuario <?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?>?');">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar Usuario">
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
