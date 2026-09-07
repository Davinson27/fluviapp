<div class="row mb-4">
    <div class="col-12">
        <div class="p-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex align-items-center">
                    <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img me-3 shadow" style="width: 62px; height: 62px; border-color: rgba(255,255,255,0.7);">
                    <div>
                        <h3 class="fw-bold mb-1">Bienvenido a FluviApp</h3>
                        <p class="mb-0 text-white-50">
                            <?php if (!empty($userDepto)): ?>
                                Portal oficial configurado para el departamento de <strong><?= htmlspecialchars($userDepto) ?></strong>. Rutas y transporte 100% intra-departamental.
                            <?php else: ?>
                                Encuentra tus rutas fluviales, consulta horarios, precios en tiempo real y compra tus pasajes con total seguridad.
                            <?php endif; ?>
                        </p>
                    </div>
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

<?php if (!empty($userDepto)): ?>
    <div class="alert alert-info d-flex align-items-center mb-4 rounded-4 shadow-sm border-0 bg-info bg-opacity-10 text-dark">
        <div class="fs-4 me-3 text-info"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
            <div class="fw-bold">Cobertura Fluvial Autorizada: Departamento de <?= htmlspecialchars($userDepto) ?></div>
            <small class="text-secondary">Conforme a la normativa fluvial nacional, solo tienes acceso a los <?= count($muelles) ?> puertos y conexiones intermunicipales dentro de <?= htmlspecialchars($userDepto) ?>.</small>
        </div>
    </div>
<?php endif; ?>

<!-- Buscador de Itinerarios -->
<div class="card bg-white shadow-sm mb-4 border-0">
    <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3">
            <i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Buscar Rutas y Viajes en <?= htmlspecialchars(!empty($userDepto) ? $userDepto : 'Colombia') ?>
        </h5>
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
        <?php if (!empty($sugerenciaIA)): ?>
            <!-- Alerta Temporal de Búsqueda Inteligente e IA -->
            <div class="col-12" id="alerta-temporal-busqueda">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3" style="border-left: 5px solid #0284c7 !important; background: linear-gradient(to right, rgba(2, 132, 199, 0.06), rgba(14, 165, 233, 0.02));">
                    <div class="card-body p-4">
                        <!-- Mensaje temporal con tratamiento según género -->
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3 p-3 rounded-circle bg-warning bg-opacity-10 text-warning fs-3 flex-shrink-0">
                                <i class="fa-solid fa-calendar-xmark"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                        <i class="fa-solid fa-bell me-1"></i>Información de Disponibilidad
                                    </span>
                                    <small class="text-muted d-none d-sm-inline">
                                        <i class="fa-regular fa-clock me-1"></i>Aviso temporal (<span id="tiempo-restante-aviso">20s</span>)
                                    </small>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <?= htmlspecialchars($sugerenciaIA['mensaje_principal']) ?>
                                </h5>
                                <p class="text-secondary mb-0">
                                    Hemos revisado en tiempo real la programación de la hidrovía en <?= htmlspecialchars(!empty($userDepto) ? $userDepto : 'su zona') ?> para asistirle.
                                </p>
                            </div>
                        </div>

                        <!-- Sección de Sugerencia Inteligente IA -->
                        <div class="p-3 rounded-3 bg-white shadow-sm border border-primary border-opacity-10 mt-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge rounded-pill bg-primary px-3 py-1 me-2 shadow-sm">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1 text-warning"></i>Asistente Fluvial IA
                                </span>
                                <small class="text-muted fst-italic">Análisis inteligente de frecuencias y zarpes cercanos</small>
                            </div>
                            <p class="text-dark mb-0 fs-6 lh-base">
                                <?= $sugerenciaIA['mensaje_ia'] ?>
                            </p>
                        </div>

                        <!-- Tarjetas de Fechas Alternativas Sugeridas -->
                        <?php if (!empty($sugerenciaIA['sugerencias'])): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="fa-regular fa-calendar-check me-2"></i>Días con Salidas Fluviales Disponibles para Esta Conexión:
                                </h6>
                                <div class="row g-3">
                                    <?php foreach ($sugerenciaIA['sugerencias'] as $sug): ?>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card h-100 border border-info border-opacity-25 shadow-sm rounded-3 bg-white hover-shadow">
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="badge bg-success-subtle text-success fw-bold">
                                                                <i class="fa-solid fa-users me-1"></i><?= $sug['cupos'] ?> cupos
                                                            </span>
                                                            <span class="badge bg-light text-muted">
                                                                <?= htmlspecialchars($sug['codigo_viaje']) ?>
                                                            </span>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-1">
                                                            <i class="fa-solid fa-calendar-day me-1 text-info"></i><?= htmlspecialchars($sug['fecha_formato']) ?>
                                                        </h6>
                                                        <small class="text-muted d-block mb-2">
                                                            <i class="fa-regular fa-clock me-1 text-primary"></i>Hora: <strong><?= $sug['hora'] ?></strong> &bull; <?= htmlspecialchars($sug['embarcacion']) ?>
                                                        </small>
                                                        <div class="small text-secondary mb-2">
                                                            <span><?= htmlspecialchars($sug['origen_nombre']) ?></span>
                                                            <i class="fa-solid fa-arrow-right mx-1 text-primary"></i>
                                                            <span><?= htmlspecialchars($sug['destino_nombre']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="pt-2 border-top d-flex justify-content-between align-items-center mt-2">
                                                        <span class="fw-bold text-success fs-5">
                                                            $<?= number_format($sug['precio'], 0, ',', '.') ?> <small class="fs-6 text-muted">COP</small>
                                                        </span>
                                                        <a href="<?= BASE_URL ?>/portal?origen_id=<?= $sug['origen_id'] ?>&destino_id=<?= $sug['destino_id'] ?>&fecha=<?= $sug['fecha'] ?>" class="btn btn-sm btn-primary fw-semibold px-3 shadow-sm">
                                                            Ver Salida <i class="fa-solid fa-arrow-right ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span class="text-muted small">
                                <i class="fa-solid fa-circle-info me-1 text-info"></i>Puedes seleccionar una de las fechas sugeridas o restablecer el filtro para explorar todas las opciones.
                            </span>
                            <a href="<?= BASE_URL ?>/portal" class="btn btn-outline-secondary btn-sm fw-semibold">
                                <i class="fa-solid fa-rotate-left me-1"></i>Ver Todos los Viajes Disponibles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="display-1 text-muted mb-3"><i class="fa-solid fa-compass"></i></div>
                <h5 class="text-secondary fw-bold">No se encontraron viajes disponibles con los filtros seleccionados.</h5>
                <p class="text-muted">Prueba buscando en otra fecha o seleccionando otros muelles de salida.</p>
                <a href="<?= BASE_URL ?>/portal" class="btn btn-outline-primary btn-sm">Ver todos los viajes activos</a>
            </div>
        <?php endif; ?>
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

<?php if (!empty($sugerenciaIA)): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const alertBox = document.getElementById('alerta-temporal-busqueda');
    if (alertBox) {
        const timerBadge = document.getElementById('tiempo-restante-aviso');
        let remainingSeconds = 20;

        const interval = setInterval(() => {
            remainingSeconds--;
            if (timerBadge) {
                timerBadge.textContent = remainingSeconds + 's';
            }
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                alertBox.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                alertBox.style.opacity = '0.75';
            }
        }, 1000);
    }
});
</script>
<?php endif; ?>
