<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-calendar-plus me-2 text-primary"></i>Programar Nuevo Viaje Fluvial</span>
                <a href="<?= BASE_URL ?>/viajes" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/viajes/guardar" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ruta Fluvial *</label>
                            <select name="ruta_id" id="ruta_select" class="form-select" required>
                                <option value="">-- Seleccionar Ruta --</option>
                                <?php foreach ($rutas as $r): ?>
                                    <option value="<?= $r['id'] ?>" data-tarifa="<?= $r['tarifa_base'] ?>">
                                        <?= htmlspecialchars($r['origen_nombre']) ?> &rarr; <?= htmlspecialchars($r['destino_nombre']) ?> (Base: $<?= number_format($r['tarifa_base'], 0, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Embarcación *</label>
                            <select name="embarcacion_id" class="form-select" required>
                                <option value="">-- Seleccionar Embarcación --</option>
                                <?php foreach ($embarcaciones as $e): ?>
                                    <option value="<?= $e['id'] ?>">
                                        <?= htmlspecialchars($e['nombre']) ?> (Cap: <?= $e['capacidad_pasajeros'] ?> Pax | <?= $e['capacidad_carga_kg'] ?> kg)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Capitán / Patrón de Navegación</label>
                            <select name="capitan_id" class="form-select">
                                <option value="">-- Sin asignar --</option>
                                <?php foreach ($capitanes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?> (<?= htmlspecialchars($c['telefono']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Precio del Pasaje ($ COP) *</label>
                            <input type="number" step="100" name="precio_pasaje" id="precio_pasaje" class="form-control" placeholder="Ej: 25000" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de Salida *</label>
                            <input type="date" name="fecha_salida" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hora de Salida *</label>
                            <input type="time" name="hora_salida" class="form-control" value="08:00" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observaciones / Condiciones Fluviales</label>
                            <textarea name="observaciones" class="form-control" rows="2" placeholder="Ej: Nivel del río normal, zarpe sujeto a condiciones meteorológicas"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/viajes" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Guardar y Publicar Viaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ruta_select')?.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const tarifa = selected.getAttribute('data-tarifa');
    if (tarifa) {
        document.getElementById('precio_pasaje').value = tarifa;
    }
});
</script>
