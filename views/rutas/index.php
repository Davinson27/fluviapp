<div class="row g-4">
    <!-- Formulario para agregar ruta por departamento -->
    <div class="col-12 col-lg-4">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header fw-bold text-dark d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-route me-2 text-primary"></i>Registrar Ruta Fluvial</span>
                <span class="badge bg-primary-subtle text-primary" style="font-size: 0.75rem;">Intra-Departamental</span>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/rutas/guardar" method="POST" id="formNuevaRuta">
                    <!-- 1. Selección de Departamento Obligatorio -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">
                            <i class="fa-solid fa-map-location-dot text-primary me-1"></i>1. Departamento de la Ruta *
                        </label>
                        <?php if (!empty($deptScope)): ?>
                            <input type="text" class="form-control form-control-lg fs-6 fw-bold" value="<?= htmlspecialchars($deptScope) ?>" readonly>
                            <input type="hidden" name="departamento" id="select_departamento" value="<?= htmlspecialchars($deptScope) ?>">
                            <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">
                                <i class="fa-solid fa-lock me-1 text-warning"></i>Jurisdicción departamental independiente fijada.
                            </small>
                        <?php else: ?>
                            <select id="select_departamento" name="departamento" class="form-select form-select-lg fs-6" required>
                                <option value="">-- Selecciona el Departamento --</option>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= htmlspecialchars($d['departamento']) ?>" <?= ($deptoFiltro === $d['departamento']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['departamento']) ?> (<?= $d['total_muelles'] ?> puertos)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">
                                <i class="fa-solid fa-lock me-1 text-warning"></i>Solo se conectan puertos del mismo departamento.
                            </small>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Puerto de Origen -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-anchor text-info me-1"></i>2. Muelle de Origen *
                        </label>
                        <select name="muelle_origen_id" id="select_origen" class="form-select" required disabled>
                            <option value="">-- Primero selecciona el departamento --</option>
                        </select>
                    </div>

                    <!-- 3. Puerto de Destino -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-location-crosshairs text-success me-1"></i>3. Muelle de Destino *
                        </label>
                        <select name="muelle_destino_id" id="select_destino" class="form-select" required disabled>
                            <option value="">-- Primero selecciona el departamento --</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Distancia (km) *</label>
                            <input type="number" step="0.1" name="distancia_km" id="input_distancia" class="form-control" value="35" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Duración (min) *</label>
                            <input type="number" name="duracion_estimada_min" id="input_duracion" class="form-control" value="50" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tarifa Base Pasaje ($ COP) *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="1000" name="tarifa_base" id="input_tarifa" class="form-control fw-bold text-success" placeholder="Ej: 28000" value="28000" required>
                            <span class="input-group-text">COP</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estado de la Ruta</label>
                        <select name="estado" class="form-select">
                            <option value="activa">Activa para Operación</option>
                            <option value="inactiva">Inactiva temporalmente</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="crear_retorno" id="crear_retorno" value="1" checked>
                        <label class="form-check-label small fw-semibold" for="crear_retorno">
                            Crear también la ruta de regreso (bidireccional)
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" id="btnGuardarRuta" disabled>
                        <i class="fa-solid fa-plus me-1"></i>Registrar Ruta Fluvial
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de rutas con filtro por departamento -->
    <div class="col-12 col-lg-8">
        <div class="card bg-white shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-map-location-dot me-2 text-danger"></i>Rutas Fluviales Habilitadas
                    </h5>
                    <small class="text-muted"><?= count($rutas) ?> rutas activas registradas</small>
                </div>

                <?php if (!empty($deptScope)): ?>
                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fa-solid fa-location-dot me-1"></i>Jurisdicción: <?= htmlspecialchars($deptScope) ?></span>
                <?php else: ?>
                    <!-- Filtro por Departamento en la Tabla -->
                    <div class="d-flex align-items-center gap-2">
                        <label for="filtro_tabla_depto" class="small text-muted mb-0 fw-semibold">Filtrar por Departamento:</label>
                        <select id="filtro_tabla_depto" class="form-select form-select-sm" style="min-width: 190px;" onchange="location.href='<?= BASE_URL ?>/rutas' + (this.value ? '?departamento=' + encodeURIComponent(this.value) : '')">
                            <option value="">-- Todos (Nacional) --</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= htmlspecialchars($d['departamento']) ?>" <?= ($deptoFiltro === $d['departamento']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['departamento']) ?> (<?= $d['total_muelles'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Departamento</th>
                                <th>Trayecto Fluvial</th>
                                <th>Distancia / Tiempo</th>
                                <th>Tarifa Base</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rutas)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="display-6 mb-2 text-muted"><i class="fa-solid fa-route"></i></div>
                                        <div>No se encontraron rutas registradas<?= !empty($deptoFiltro) ? ' para el departamento de ' . htmlspecialchars($deptoFiltro) : '' ?>.</div>
                                        <?php if (!empty($deptoFiltro)): ?>
                                            <a href="<?= BASE_URL ?>/rutas" class="btn btn-outline-primary btn-sm mt-2">Ver Todas las Rutas</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rutas as $r): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-bold">
                                                <?= htmlspecialchars($r['origen_departamento'] ?? 'Colombia') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                <?= htmlspecialchars($r['origen_nombre']) ?> 
                                                <i class="fa-solid fa-arrow-right text-primary mx-1"></i> 
                                                <?= htmlspecialchars($r['destino_nombre']) ?>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-water text-info me-1"></i><?= htmlspecialchars($r['origen_rio'] ?? 'Río') ?> &bull; <?= htmlspecialchars($r['origen_municipio']) ?> a <?= htmlspecialchars($r['destino_municipio']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div><strong><?= number_format($r['distancia_km'], 1) ?> km</strong></div>
                                            <small class="text-muted"><i class="fa-solid fa-clock me-1"></i>~<?= floor($r['duracion_estimada_min']/60) ?>h <?= ($r['duracion_estimada_min']%60) ?>m</small>
                                        </td>
                                        <td>
                                            <?php if (AuthHelper::user()['rol'] === 'admin'): ?>
                                            <form action="<?= BASE_URL ?>/rutas/editar-tarifa" method="POST" class="d-flex align-items-center gap-1">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <div class="input-group input-group-sm" style="width: 140px;">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="1000" name="tarifa_base" class="form-control fw-bold text-success" value="<?= $r['tarifa_base'] ?>" required>
                                                    <button type="submit" class="btn btn-outline-success" title="Guardar Nuevo Precio">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </div>
                                            </form>
                                            <?php else: ?>
                                            <span class="fw-bold text-success">$<?= number_format($r['tarifa_base'], 0, ',', '.') ?> COP</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $r['estado'] === 'activa' ? 'success' : 'danger' ?> text-uppercase">
                                                <?= $r['estado'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if (AuthHelper::user()['rol'] === 'admin'): ?>
                                            <form action="<?= BASE_URL ?>/rutas/eliminar" method="POST" class="d-inline" onsubmit="return confirm('¿Desea eliminar esta ruta fluvial?');">
                                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar Ruta">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para Filtrar Dinámicamente los Muelles de Origen y Destino por Departamento -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const muellesPorDepto = <?= json_encode($muellesGrouped, JSON_UNESCAPED_UNICODE) ?>;
    const selectDepto = document.getElementById('select_departamento');
    const selectOrigen = document.getElementById('select_origen');
    const selectDestino = document.getElementById('select_destino');
    const btnGuardar = document.getElementById('btnGuardarRuta');

    function actualizarPuertos(depto) {
        selectOrigen.innerHTML = '<option value="">-- Selecciona Puerto de Origen --</option>';
        selectDestino.innerHTML = '<option value="">-- Selecciona Puerto de Destino --</option>';

        if (!depto || !muellesPorDepto[depto] || muellesPorDepto[depto].length < 2) {
            selectOrigen.disabled = true;
            selectDestino.disabled = true;
            btnGuardar.disabled = true;
            if (depto && (!muellesPorDepto[depto] || muellesPorDepto[depto].length < 2)) {
                alert('El departamento seleccionado cuenta con menos de 2 puertos registrados. Se requieren al menos 2 puertos para trazar una ruta.');
            }
            return;
        }

        const puertos = muellesPorDepto[depto];
        puertos.forEach(p => {
            const optO = document.createElement('option');
            optO.value = p.id;
            optO.textContent = `${p.nombre} (${p.municipio} - ${p.rio})`;
            selectOrigen.appendChild(optO);

            const optD = document.createElement('option');
            optD.value = p.id;
            optD.textContent = `${p.nombre} (${p.municipio} - ${p.rio})`;
            selectDestino.appendChild(optD);
        });

        selectOrigen.disabled = false;
        selectDestino.disabled = false;
        btnGuardar.disabled = false;
    }

    if (selectDepto) {
        selectDepto.addEventListener('change', function () {
            actualizarPuertos(this.value);
        });

        // Si ya había un departamento seleccionado (por filtro inicial)
        if (selectDepto.value) {
            actualizarPuertos(selectDepto.value);
        }
    }

    // Evitar seleccionar el mismo origen y destino
    selectDestino.addEventListener('change', function () {
        if (this.value && this.value === selectOrigen.value) {
            alert('El muelle de destino debe ser diferente al muelle de origen.');
            this.value = '';
        }
    });

    selectOrigen.addEventListener('change', function () {
        if (this.value && this.value === selectDestino.value) {
            alert('El muelle de origen debe ser diferente al muelle de destino.');
            this.value = '';
        }
    });
});
</script>
