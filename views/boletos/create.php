<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card bg-white shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="fa-solid fa-ticket me-2 text-success"></i>Emitir Boleto de Pasaje</span>
                <a href="<?= BASE_URL ?>/boletos" class="btn btn-outline-secondary btn-sm">Volver</a>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/boletos/guardar" method="POST">
                    <!-- Selección de Viaje -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Seleccionar Viaje / Itinerario Activo *</label>
                        <select name="viaje_id" id="viaje_select" class="form-select form-select-lg" required>
                            <option value="">-- Seleccionar Itinerario Disponible --</option>
                            <?php foreach ($viajesDisponibles as $v): ?>
                                <option value="<?= $v['id'] ?>" data-precio="<?= $v['precio_pasaje'] ?>" data-cupos="<?= $v['cupos_disponibles'] ?>">
                                    [<?= $v['codigo_viaje'] ?>] <?= htmlspecialchars($v['origen_nombre']) ?> &rarr; <?= htmlspecialchars($v['destino_nombre']) ?> | <?= $v['fecha_salida'] ?> <?= substr($v['hora_salida'], 0, 5) ?> (Cupos: <?= $v['cupos_disponibles'] ?> - $<?= number_format($v['precio_pasaje'], 0, ',', '.') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="cupo_alerta" class="form-text mt-1 text-muted">Seleccione un itinerario para cargar automáticamente la tarifa y validar cupos.</div>
                    </div>

                    <h6 class="fw-bold text-secondary border-bottom pb-2 mb-3">Datos del Pasajero</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Documento de Identidad (Cédula/TI) *</label>
                            <input type="text" name="pasajero_documento" class="form-control" placeholder="Ej: 1045678901" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Completo del Pasajero *</label>
                            <input type="text" name="pasajero_nombre" class="form-control" placeholder="Ej: Juan Pérez Morales" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono de Contacto</label>
                            <input type="text" name="pasajero_telefono" class="form-control" placeholder="Ej: 3001234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Método de Pago</label>
                            <select name="metodo_pago" class="form-select">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia (Nequi/Daviplata/Bancolombia)</option>
                                <option value="tarjeta">Tarjeta Débito/Crédito</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total a Cobrar ($ COP) *</label>
                            <input type="number" step="100" name="precio_pagado" id="precio_pagado" class="form-control form-control-lg fw-bold text-success" required>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <a href="<?= BASE_URL ?>/boletos" class="btn btn-light me-2">Cancelar</a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                            <i class="fa-solid fa-check me-2"></i>Emitir e Imprimir Tiquete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('viaje_select')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const precio = opt.getAttribute('data-precio');
    const cupos = opt.getAttribute('data-cupos');
    if (precio) {
        document.getElementById('precio_pagado').value = precio;
        document.getElementById('cupo_alerta').innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-check-circle me-1"></i>Cupos disponibles: ' + cupos + '</span>';
    } else {
        document.getElementById('cupo_alerta').innerText = 'Seleccione un itinerario para cargar automáticamente la tarifa.';
    }
});
</script>
