<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-5"><i class="fa-solid fa-boxes-packing me-2"></i>Solicitar Envío de Encomienda Fluvial</span>
                <a href="<?= BASE_URL ?>/cliente/mis-encomiendas" class="btn btn-outline-light btn-sm">Mis Encomiendas</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/cliente/guardar-encomienda" method="POST">
                    <!-- Selección de Itinerario / Ruta -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Seleccionar Itinerario Fluvial *</label>
                        <select name="viaje_id" id="viaje_select" class="form-select form-select-lg" required>
                            <option value="">-- Seleccionar Itinerario para Envío --</option>
                            <?php foreach ($viajesDisponibles as $v): ?>
                                <option value="<?= $v['id'] ?>">
                                    [<?= $v['codigo_viaje'] ?>] <?= htmlspecialchars($v['origen_nombre'], ENT_QUOTES, 'UTF-8') ?> &rarr; <?= htmlspecialchars($v['destino_nombre'], ENT_QUOTES, 'UTF-8') ?> | Salida: <?= $v['fecha_salida'] ?> <?= substr($v['hora_salida'], 0, 5) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-12"><h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="fa-solid fa-user me-2 text-primary"></i>Datos del Remitente</h6></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del Remitente *</label>
                            <input type="text" name="remitente_nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono del Remitente *</label>
                            <input type="text" name="remitente_telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-12 mt-4"><h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="fa-solid fa-user-tag me-2 text-success"></i>Datos del Destinatario</h6></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo del Destinatario *</label>
                            <input type="text" name="destinatario_nombre" class="form-control" placeholder="Ej: María José Morales" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono del Destinatario *</label>
                            <input type="text" name="destinatario_telefono" class="form-control" placeholder="Ej: 3123456789" required>
                        </div>

                        <div class="col-12 mt-4"><h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="fa-solid fa-box-open me-2 text-warning"></i>Detalles del Paquete / Carga</h6></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción del Contenido *</label>
                            <textarea name="descripcion_carga" class="form-control" rows="2" placeholder="Ej: Paquete sellado con documentos y muestras textiles" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Peso Estimado (kg) *</label>
                            <input type="number" step="0.1" name="peso_kg" id="peso_kg" class="form-control" placeholder="Ej: 5.0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor Declarado ($ COP)</label>
                            <input type="number" step="100" name="valor_declarado" class="form-control" placeholder="Ej: 100000" value="50000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor del Flete ($ COP) *</label>
                            <input type="number" step="100" name="valor_flete" id="valor_flete" class="form-control fw-bold text-success" placeholder="Calculado" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <a href="<?= BASE_URL ?>/cliente/mis-encomiendas" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                            <i class="fa-solid fa-paper-plane me-2"></i>Registrar Envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Cálculo automático de tarifa de flete según peso base (ej: $2.000 COP por kg mínimo $10.000)
document.getElementById('peso_kg')?.addEventListener('input', function() {
    const peso = parseFloat(this.value) || 0;
    let flete = Math.max(10000, peso * 2500);
    document.getElementById('valor_flete').value = Math.round(flete);
});
</script>
