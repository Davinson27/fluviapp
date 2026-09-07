<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold">
                    <i class="fa-solid fa-ship me-2 text-primary"></i>Editar Embarcación de la Flota Fluvial
                </span>
                <a href="<?= BASE_URL ?>/embarcaciones" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Volver al Catálogo
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/embarcaciones/actualizar" method="POST">
                    <input type="hidden" name="id" value="<?= $embarcacion['id'] ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Embarcación *</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($embarcacion['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Matrícula Fluvial *</label>
                            <input type="text" name="matricula" class="form-control" value="<?= htmlspecialchars($embarcacion['matricula']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Embarcación</label>
                            <select name="tipo" class="form-select" required>
                                <option value="lancha_rapida" <?= ($embarcacion['tipo'] === 'lancha_rapida') ? 'selected' : '' ?>>Lancha Rápida</option>
                                <option value="ferry" <?= ($embarcacion['tipo'] === 'ferry') ? 'selected' : '' ?>>Ferry de Pasajeros / Mixto</option>
                                <option value="bote_motor" <?= ($embarcacion['tipo'] === 'bote_motor') ? 'selected' : '' ?>>Bote a Motor</option>
                                <option value="barcaza_carga" <?= ($embarcacion['tipo'] === 'barcaza_carga') ? 'selected' : '' ?>>Barcaza de Carga</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado Operativo</label>
                            <select name="estado" class="form-select" required>
                                <option value="operativo" <?= ($embarcacion['estado'] === 'operativo') ? 'selected' : '' ?>>Operativo</option>
                                <option value="mantenimiento" <?= ($embarcacion['estado'] === 'mantenimiento') ? 'selected' : '' ?>>En Mantenimiento</option>
                                <option value="fuera_servicio" <?= ($embarcacion['estado'] === 'fuera_servicio') ? 'selected' : '' ?>>Fuera de Servicio</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Capacidad de Pasajeros (Asientos) *</label>
                            <input type="number" name="capacidad_pasajeros" class="form-control" min="0" value="<?= $embarcacion['capacidad_pasajeros'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Capacidad de Carga en Bodega (kg) *</label>
                            <input type="number" step="0.01" name="capacidad_carga_kg" class="form-control" min="0" value="<?= $embarcacion['capacidad_carga_kg'] ?>" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/embarcaciones" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>