<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Registrar Nuevo Usuario</span>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/usuarios/guardar" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3 align-items-center pb-3 border-bottom">
                        <div class="col-auto">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary border" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold mb-1">Foto de Perfil (Opcional)</label>
                            <input type="file" name="foto" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
                            <small class="text-muted">Formatos: JPG, PNG, WEBP (máx. 5 MB)</small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Daniel Sánchez Rúa" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="email" class="form-control" placeholder="ejemplo@fluviapp.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña de Acceso *</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Ej: 3001234567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol en el Sistema *</label>
                            <select name="rol" id="select-rol" class="form-select" required>
                                <option value="taquilla">Taquilla / Venta de Boletos</option>
                                <option value="operador">Operador de Muelle / Logística</option>
                                <option value="capitan">Capitán / Patrón Fluvial</option>
                                <option value="cliente">Cliente / Pasajero</option>
                                <?php if ($isGeneralAdmin): ?>
                                <option value="admin">Administrador (General o Departamental)</option>
                                <?php endif; ?>
                            </select>
                            <small id="rol-help" class="text-muted d-block mt-1">Seleccione el rol operativo del usuario.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado de la Cuenta</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departamento Asignado / Jurisdicción</label>
                            <?php if ($isGeneralAdmin): ?>
                            <select name="departamento" id="select-departamento" class="form-select">
                                <option value="">-- Nacional / Sin departamento específico (Acceso General) --</option>
                                <?php if (!empty($departamentos)): ?>
                                    <?php foreach ($departamentos as $d): ?>
                                        <option value="<?= htmlspecialchars($d['departamento']) ?>">
                                            <?= htmlspecialchars($d['departamento']) ?> (Jurisdicción Departamental)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small id="depto-help" class="text-primary d-block mt-1">
                                <i class="fa-solid fa-circle-info me-1"></i>Para un Administrador: Si seleccionas un departamento, operará exclusivamente en ese territorio independiente. Si lo dejas vacío, será Administrador General.
                            </small>
                            <?php else: ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($deptScope) ?> (Fijo por Jurisdicción)" readonly>
                            <input type="hidden" name="departamento" value="<?= htmlspecialchars($deptScope) ?>">
                            <small class="text-muted">Como administrador departamental, los usuarios creados pertenecerán automáticamente a su territorio.</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Género *</label>
                            <select name="genero" class="form-select" required>
                                <option value="masculino">Masculino (Señor)</option>
                                <option value="femenino">Femenino (Señora)</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/usuarios" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
