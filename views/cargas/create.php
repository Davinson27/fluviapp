<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-boxes-packing me-2 text-primary"></i>Registrar Nueva Encomienda / Carga Fluvial</span>
                <a href="<?= BASE_URL ?>/cargas" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/cargas/guardar" method="POST">
                    <!-- Selección de Viaje -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Seleccionar Viaje / Itinerario *</label>
                        <select name="viaje_id" id="viaje_select" class="form-select form-select-lg" required>
                            <option value="">-- Seleccionar Itinerario --</option>
                            <?php foreach ($viajesDisponibles as $v): ?>
                                <option value="<?= $v['id'] ?>" data-cap-carga="<?= $v['capacidad_carga_disponible_kg'] ?>">
                                    [<?= $v['codigo_viaje'] ?>] <?= htmlspecialchars($v['origen_nombre']) ?> &rarr; <?= htmlspecialchars($v['destino_nombre']) ?> | Cap. Bodega Disp: <?= number_format($v['capacidad_carga_disponible_kg'], 1) ?> kg
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-12"><h6 class="fw-bold text-secondary border-bottom pb-2">Datos del Remitente</h6></div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nombre Remitente *</label>
                            <input type="text" name="remitente_nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfono Remitente *</label>
                            <input type="text" name="remitente_telefono" class="form-control" placeholder="Ej: 3001234567" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Remitente (Opcional)</label>
                            <input type="email" name="remitente_email" class="form-control" placeholder="cliente@correo.com">
                        </div>

                        <div class="col-12 mt-4"><h6 class="fw-bold text-secondary border-bottom pb-2">Datos del Destinatario</h6></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Destinatario *</label>
                            <input type="text" name="destinatario_nombre" class="form-control" placeholder="Ej: Carlos Gómez" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono Destinatario *</label>
                            <input type="text" name="destinatario_telefono" class="form-control" placeholder="Ej: 3109876543" required>
                        </div>

                        <div class="col-12 mt-4"><h6 class="fw-bold text-secondary border-bottom pb-2">Detalles de la Carga</h6></div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción del Paquete o Mercancía *</label>
                            <textarea name="descripcion_carga" class="form-control" rows="2" placeholder="Ej: Caja sellada con repuestos de motor y herramientas" required></textarea>
                        </div>
                        <!-- Dimensiones y Cubicaje FluviApp v2.0 -->
                        <div class="col-12 mt-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-ruler-combined me-1 text-primary"></i> Dimensiones y Cubicaje (Largo x Ancho x Alto)
                                </label>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <label class="form-label small text-muted">Largo (cm)</label>
                                        <input type="number" step="0.5" id="dim_largo" name="largo_cm" class="form-control form-control-sm" placeholder="Ej: 40" oninput="calcularCubicaje()">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small text-muted">Ancho (cm)</label>
                                        <input type="number" step="0.5" id="dim_ancho" name="ancho_cm" class="form-control form-control-sm" placeholder="Ej: 30" oninput="calcularCubicaje()">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small text-muted">Alto (cm)</label>
                                        <input type="number" step="0.5" id="dim_alto" name="alto_cm" class="form-control form-control-sm" placeholder="Ej: 20" oninput="calcularCubicaje()">
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="d-flex justify-content-between align-items-center small">
                                            <span class="text-muted">Peso Volumétrico Estimado:</span>
                                            <span class="badge bg-primary fs-6" id="badge_peso_vol">0.0 kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Peso en Báscula (kg) *</label>
                            <input type="number" step="0.1" id="peso_bascula" name="peso_kg" class="form-control" placeholder="Ej: 15.5" oninput="calcularCubicaje()" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor Declarado ($ COP)</label>
                            <input type="number" step="100" name="valor_declarado" class="form-control" placeholder="Ej: 150000" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor del Flete ($ COP) *</label>
                            <input type="number" step="100" id="valor_flete_input" name="valor_flete" class="form-control fw-bold text-success" placeholder="Ej: 20000" required>
                        </div>
                    </div>

                    <script>
                    function calcularCubicaje() {
                        const l = parseFloat(document.getElementById('dim_largo').value) || 0;
                        const a = parseFloat(document.getElementById('dim_ancho').value) || 0;
                        const h = parseFloat(document.getElementById('dim_alto').value) || 0;
                        const pVol = (l > 0 && a > 0 && h > 0) ? ((l * a * h) / 5000).toFixed(1) : 0.0;
                        document.getElementById('badge_peso_vol').innerText = pVol + ' kg';

                        const pBascula = parseFloat(document.getElementById('peso_bascula').value) || 0;
                        const pMayor = Math.max(pBascula, pVol);

                        // Sugerir flete base ($2.500 COP por kg con mínimo de $15.000)
                        if (pMayor > 0) {
                            const fleteSugerido = Math.max(15000, Math.round(pMayor * 2500));
                            const fleteInput = document.getElementById('valor_flete_input');
                            if (!fleteInput.value || fleteInput.dataset.manual !== 'true') {
                                fleteInput.value = fleteSugerido;
                            }
                        }
                    }
                    document.getElementById('valor_flete_input').addEventListener('input', function() {
                        this.dataset.manual = 'true';
                    });
                    </script>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/cargas" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="fa-solid fa-check me-2"></i>Registrar Guía de Carga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
