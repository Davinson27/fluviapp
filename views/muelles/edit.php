<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold">
                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Editar Muelle / Puerto Fluvial
                </span>
                <a href="<?= BASE_URL ?>/muelles" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Volver al Listado
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/muelles/actualizar" method="POST">
                    <input type="hidden" name="id" value="<?= $muelle['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del Muelle / Puerto *</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($muelle['nombre']) ?>" required>
                            <small class="text-muted">Nombre oficial o turístico con el que se identifica el muelle.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Río / Cuenca Navegable *</label>
                            <input type="text" name="rio" class="form-control" value="<?= htmlspecialchars($muelle['rio']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Municipio / Ciudad *</label>
                            <input type="text" name="municipio" class="form-control" value="<?= htmlspecialchars($muelle['municipio']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departamento *</label>
                            <input type="text" name="departamento" class="form-control" value="<?= htmlspecialchars($muelle['departamento']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Latitud (GPS)</label>
                            <input type="number" step="0.0000001" name="latitud" class="form-control" value="<?= htmlspecialchars((string)($muelle['latitud'] ?? '')) ?>" placeholder="Ej: 9.2423000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Longitud (GPS)</label>
                            <input type="number" step="0.0000001" name="longitud" class="form-control" value="<?= htmlspecialchars((string)($muelle['longitud'] ?? '')) ?>" placeholder="Ej: -74.7547000">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Estado Operativo</label>
                            <select name="estado" class="form-select">
                                <option value="activo" <?= ($muelle['estado'] === 'activo') ? 'selected' : '' ?>>Activo (Habilitado para zarpe, rutas y mapa)</option>
                                <option value="inactivo" <?= ($muelle['estado'] === 'inactivo') ? 'selected' : '' ?>>Inactivo (Cerrado por temporada o mantenimiento)</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción / Características</label>
                            <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($muelle['descripcion'] ?? '') ?></textarea>
                            <small class="text-muted">Aparece en los detalles de las rutas y en el mapa interactivo de Colombia.</small>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/muelles" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>