<div class="row mb-4">
    <div class="col-12">
        <div class="p-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-1"><i class="fa-solid fa-ship me-2"></i>Bienvenido a FluviApp</h3>
                    <p class="mb-0 text-white-50">Encuentra tus rutas fluviales, consulta horarios, precios en tiempo real y compra tus pasajes con total seguridad.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= BASE_URL ?>/cliente/mis-boletos" class="btn btn-light fw-bold text-primary shadow-sm me-2">
                        <i class="fa-solid fa-ticket me-1"></i>Mis Tiquetes
                    </a>
                    <a href="<?= BASE_URL ?>/cliente/enviar-encomienda" class="btn btn-outline-light fw-semibold">
                        <i class="fa-solid fa-box me-1"></i>Enviar Encomienda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Buscador de Itinerarios -->
<div class="card bg-white shadow-sm mb-4 border-0">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Buscar Rutas y Viajes Disponibles</h5>
        <form action="<?= BASE_URL ?>/portal" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-secondary">Muelle de Origen</label>
                    <select name="origen_id" class="form-select">
                        <option value="">-- Todos los Muelles de Origen --</option>
                        <?php foreach ($muelles as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($filtros['origen_id'] == $m['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($m['municipio'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-secondary">Muelle de Destino</label>
                    <select name="destino_id" class="form-select">
                        <option value="">-- Todos los Muelles de Destino --</option>
                        <?php foreach ($muelles as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($filtros['destino_id'] == $m['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m['nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($m['municipio'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold text-secondary">Fecha de Salida</label>
                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtros['fecha'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-search me-1"></i>Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Viajes Disponibles -->
<div class="row g-4">
    <?php if (empty($viajes)): ?>
        <div class="col-12 text-center py-5">
            <div class="display-1 text-muted mb-3"><i class="fa-solid fa-compass"></i></div>
            <h5 class="text-secondary fw-bold">No se encontraron viajes disponibles con los filtros seleccionados.</h5>
            <p class="text-muted">Prueba buscando en otra fecha o seleccionando otros muelles de salida.</p>
            <a href="<?= BASE_URL ?>/portal" class="btn btn-outline-primary btn-sm">Ver todos los viajes activos</a>
        </div>
    <?php else: ?>
        <?php foreach ($viajes as $v): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 bg-white border-0 shadow-sm stat-card">
                    <div class="card-body p-4 d-flex flex-column">
                        <!-- Cabecera de la tarjeta -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-2 py-1">
                                <?= htmlspecialchars($v['codigo_viaje'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="badge bg-success-subtle text-success fw-bold">
                                <?= $v['cupos_disponibles'] ?> cupos disponibles
                            </span>
                        </div>

                        <!-- Origen y Destino -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-primary me-2"><i class="fa-solid fa-circle-dot"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($v['origen_nombre'], ENT_QUOTES, 'UTF-8') ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($v['origen_municipio'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($v['origen_rio'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                            </div>
                            <div class="ms-2 ps-1 border-start border-2 border-primary my-1" style="height: 16px;"></div>
                            <div class="d-flex align-items-center">
                                <div class="text-danger me-2"><i class="fa-solid fa-location-dot"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($v['destino_nombre'], ENT_QUOTES, 'UTF-8') ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($v['destino_municipio'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($v['destino_rio'], ENT_QUOTES, 'UTF-8') ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Info del trayecto -->
                        <div class="bg-light p-3 rounded-3 small text-muted mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <i class="fa-regular fa-calendar me-1 text-primary"></i><?= $v['fecha_salida'] ?>
                                </div>
                                <div class="col-6">
                                    <i class="fa-regular fa-clock me-1 text-primary"></i><?= substr($v['hora_salida'], 0, 5) ?>
                                </div>
                                <div class="col-6">
                                    <i class="fa-solid fa-ship me-1 text-info"></i><?= htmlspecialchars($v['embarcacion_nombre'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <div class="col-6">
                                    <i class="fa-solid fa-stopwatch me-1 text-warning"></i>~<?= $v['duracion_estimada_min'] ?> mins
                                </div>
                            </div>
                        </div>

                        <!-- Precio y Botón de Compra -->
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Precio por Pasajero</small>
                                <span class="fs-4 fw-bold text-success">$<?= number_format($v['precio_pasaje'], 0, ',', '.') ?> <small class="fs-6 text-muted">COP</small></span>
                            </div>
                            <a href="<?= BASE_URL ?>/cliente/comprar?viaje_id=<?= $v['id'] ?>" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm">
                                <i class="fa-solid fa-cart-shopping me-1"></i>Comprar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
