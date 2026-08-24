<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Gestión de Usuarios y Roles</h4>
        <p class="text-muted small mb-0">Administración de cuentas, niveles de acceso y personal de muelle</p>
    </div>
    <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary fw-semibold">
        <i class="fa-solid fa-user-plus me-2"></i>Registrar Nuevo Usuario
    </a>
</div>

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
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron usuarios registrados</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><span class="text-muted">#<?= $u['id'] ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <i class="fa-solid fa-circle-user me-2 text-secondary"></i><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?>
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
