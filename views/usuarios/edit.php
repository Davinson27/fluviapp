<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Editar Usuario: <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/usuarios/actualizar" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

                    <div class="row mb-4 align-items-center pb-3 border-bottom">
                        <div class="col-auto">
                            <?php if (!empty($usuario['foto']) && file_exists(ROOT_PATH . '/public/' . ltrim($usuario['foto'], '/'))): ?>
                                <img src="<?= BASE_URL ?>/public/<?= htmlspecialchars(ltrim($usuario['foto'], '/')) ?>" alt="Foto" class="rounded-circle object-fit-cover border border-2 border-primary shadow-sm" style="width: 72px; height: 72px;">
                            <?php else: ?>
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary border" style="width: 72px; height: 72px; font-size: 1.8rem;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold mb-1">Foto de Perfil del Usuario</label>
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
                            <?php if (!empty($usuario['foto'])): ?>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="eliminar_foto" value="1" id="del_foto">
                                    <label class="form-check-label text-danger small" for="del_foto">Eliminar foto actual</label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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
                                <option value="cliente" <?= $usuario['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente / Pasajero</option>
                                <?php if ($isGeneralAdmin): ?>
                                <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador (General o Departamental)</option>
                                <?php elseif ($usuario['rol'] === 'admin'): ?>
                                <option value="admin" selected>Administrador Departamental</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado de la Cuenta</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departamento Asignado / Jurisdicción</label>
                            <?php if ($isGeneralAdmin): ?>
                            <select name="departamento" class="form-select">
                                <option value="">-- Nacional / Sin departamento específico (Administrador General) --</option>
                                <?php if (!empty($departamentos)): ?>
                                    <?php foreach ($departamentos as $d): ?>
                                        <option value="<?= htmlspecialchars($d['departamento']) ?>" <?= (($usuario['departamento'] ?? '') === $d['departamento']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['departamento']) ?> (Jurisdicción Departamental)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-primary d-block mt-1">
                                <i class="fa-solid fa-circle-info me-1"></i>Para Administradores: Al asignar un departamento, se convierte en Administrador Departamental y solo verá y gestionará datos de ese territorio. Si se deja en blanco, será Administrador General Nacional.
                            </small>
                            <?php else: ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($deptScope) ?> (Jurisdicción Fija)" readonly>
                            <input type="hidden" name="departamento" value="<?= htmlspecialchars($deptScope) ?>">
                            <small class="text-muted">Como administrador departamental, este usuario permanece bajo su jurisdicción.</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Género *</label>
                            <select name="genero" class="form-select" required>
                                <option value="masculino" <?= ($usuario['genero'] ?? '') === 'masculino' ? 'selected' : '' ?>>Masculino (Señor)</option>
                                <option value="femenino" <?= ($usuario['genero'] ?? '') === 'femenino' ? 'selected' : '' ?>>Femenino (Señora)</option>
                                <option value="otro" <?= ($usuario['genero'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
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
