<div class="row justify-content-center">
    <div class="col-12 col-lg-9 col-xl-8">
        <div class="card bg-white shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="fs-5 fw-bold text-dark me-2">
                        <i class="fa-solid fa-id-card text-primary me-2"></i>Mi Perfil de Usuario
                    </span>
                    <span class="badge bg-primary-subtle text-primary text-uppercase px-2 py-1">
                        <?= htmlspecialchars($usuario['rol']) ?>
                    </span>
                </div>
                <a href="<?= AuthHelper::isCliente() ? (BASE_URL . '/portal') : (BASE_URL . '/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Volver
                </a>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?= BASE_URL ?>/perfil/actualizar" method="POST" enctype="multipart/form-data">
                    <!-- Sección de Fotografía de Perfil -->
                    <div class="row mb-5 align-items-center pb-4 border-bottom">
                        <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <?php if (!empty($usuario['foto']) && file_exists(ROOT_PATH . '/public/' . ltrim($usuario['foto'], '/'))): ?>
                                    <img id="preview-avatar" src="<?= BASE_URL ?>/public/<?= htmlspecialchars(ltrim($usuario['foto'], '/')) ?>" alt="Foto de Perfil" class="rounded-circle shadow object-fit-cover border border-3 border-primary" style="width: 140px; height: 140px;">
                                <?php else: ?>
                                    <div id="preview-avatar-placeholder" class="rounded-circle shadow d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary border border-3 border-primary-subtle" style="width: 140px; height: 140px; font-size: 3.5rem;">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <img id="preview-avatar" src="#" alt="Vista previa" class="rounded-circle shadow object-fit-cover border border-3 border-primary d-none" style="width: 140px; height: 140px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <h5 class="fw-bold text-dark mb-1">Foto de Perfil</h5>
                            <p class="text-muted small mb-3">
                                Sube una fotografía para personalizar tu perfil en FluviApp. Se visualizará en el panel de control, la barra de navegación y los boletos. Formatos permitidos: JPG, PNG, WEBP (máx. 5 MB).
                            </p>
                            
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <label class="btn btn-primary btn-sm mb-0">
                                    <i class="fa-solid fa-camera me-1"></i>Seleccionar Nueva Foto
                                    <input type="file" name="foto" id="input-foto" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">
                                </label>

                                <?php if (!empty($usuario['foto'])): ?>
                                    <div class="form-check ms-md-2 mt-2 mt-md-0">
                                        <input class="form-check-input" type="checkbox" name="eliminar_foto" value="1" id="eliminar_foto">
                                        <label class="form-check-label text-danger small fw-semibold" for="eliminar_foto">
                                            <i class="fa-solid fa-trash-can me-1"></i>Eliminar foto actual
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small id="file-name-feedback" class="text-info d-block mt-2 font-monospace" style="font-size: 0.8rem;"></small>
                        </div>
                    </div>

                    <!-- Datos Personales y de Cuenta -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa-solid fa-user-gear me-2 text-primary"></i>Información Personal y Contacto
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo *</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico *</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Número de Documento / Cédula</label>
                            <input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($usuario['documento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej. 1098765432">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej. 3101234567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Género</label>
                            <select name="genero" class="form-select">
                                <option value="masculino" <?= ($usuario['genero'] ?? '') === 'masculino' ? 'selected' : '' ?>>Masculino (Señor)</option>
                                <option value="femenino" <?= ($usuario['genero'] ?? '') === 'femenino' ? 'selected' : '' ?>>Femenino (Señora)</option>
                                <option value="otro" <?= ($usuario['genero'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jurisdicción / Departamento Asignado</label>
                            <input type="text" class="form-control bg-light" value="<?= !empty($usuario['departamento']) ? htmlspecialchars($usuario['departamento']) : 'Ámbito Nacional (Colombia)' ?>" readonly>
                            <small class="text-muted">El departamento asignado define la jurisdicción operativa y es fijado por el Administrador General.</small>
                        </div>
                    </div>

                    <!-- Seguridad y Contraseña -->
                    <div class="row g-3 mb-4 pt-2">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa-solid fa-lock me-2 text-primary"></i>Seguridad de la Cuenta
                            </h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Cambiar Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la contraseña actual" minlength="6">
                            <small class="text-muted">Solo completa este campo si deseas cambiar tu clave de acceso (mínimo 6 caracteres).</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="<?= AuthHelper::isCliente() ? (BASE_URL . '/portal') : (BASE_URL . '/dashboard') ?>" class="btn btn-outline-secondary px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios de Perfil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputFoto = document.getElementById('input-foto');
    const previewAvatar = document.getElementById('preview-avatar');
    const placeholder = document.getElementById('preview-avatar-placeholder');
    const feedback = document.getElementById('file-name-feedback');

    if (inputFoto) {
        inputFoto.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                feedback.textContent = 'Archivo seleccionado: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewAvatar) {
                        previewAvatar.src = e.target.result;
                        previewAvatar.classList.remove('d-none');
                    }
                    if (placeholder) {
                        placeholder.classList.add('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
