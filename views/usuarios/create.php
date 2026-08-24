<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Registrar Nuevo Usuario</span>
                <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/usuarios/guardar" method="POST">
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
                            <select name="rol" class="form-select" required>
                                <option value="taquilla">Taquilla / Venta de Boletos</option>
                                <option value="operador">Operador de Muelle / Logística</option>
                                <option value="capitan">Capitán / Patrón Fluvial</option>
                                <option value="admin">Administrador General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado de la Cuenta</label>
                            <select name="estado" class="form-select" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
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
