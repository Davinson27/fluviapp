<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Editar Usuario: <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/usuarios/actualizar" method="POST">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                            <small class="text-muted">Solo complete este campo si desea cambiar la clave.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol en el Sistema *</label>
                            <select name="rol" class="form-select" required>
                                <option value="taquilla" <?= $usuario['rol'] === 'taquilla' ? 'selected' : '' ?>>Taquilla / Venta de Boletos</option>
                                <option value="operador" <?= $usuario['rol'] === 'operador' ? 'selected' : '' ?>>Operador de Muelle / Logística</option>
                                <option value="capitan" <?= $usuario['rol'] === 'capitan' ? 'selected' : '' ?>>Capitán / Patrón Fluvial</option>
                                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado de la Cuenta</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
