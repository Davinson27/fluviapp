<!-- Encabezado y Barra de Herramientas -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="fa-solid fa-chart-pie text-primary me-2"></i>Reportes y Métricas Fluviales Avanzadas
        </h4>
        <p class="text-muted small mb-0">Visualización interactiva, análisis de rutas, flota y proyecciones de transporte fluvial</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if (!empty($deptScope)): ?>
            <span class="badge bg-warning text-dark py-2 px-3"><i class="fa-solid fa-location-dot me-1"></i>Jurisdicción: <?= htmlspecialchars($deptScope) ?></span>
        <?php else: ?>
            <span class="badge bg-primary py-2 px-3"><i class="fa-solid fa-earth-americas me-1"></i>Consolidado Nacional</span>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm fw-semibold">
            <i class="fa-solid fa-print me-1"></i>Imprimir Reporte
        </button>
    </div>
</div>

<!-- KPIs Clave de Operaciones Fluviales -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Ingresos Totales</span>
                    <h3 class="fw-bold mb-0 text-dark">$<?= number_format($kpis['total_ingresos'], 0, ',', '.') ?></h3>
                    <small class="text-success"><i class="fa-solid fa-money-bill-trend-up me-1"></i>Boletos + Fletes</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fa-solid fa-sack-dollar fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Pasajes Vendidos</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($kpis['total_pasajes'], 0, ',', '.') ?></h3>
                    <small class="text-muted"><i class="fa-solid fa-ticket me-1"></i>Boletos fluviales</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fa-solid fa-users fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Itinerarios / Viajes</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($kpis['total_viajes'], 0, ',', '.') ?></h3>
                    <small class="text-info"><i class="fa-solid fa-compass me-1"></i>Zarpes programados</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                    <i class="fa-solid fa-route fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card bg-white p-3 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Flota y Puertos</span>
                    <h3 class="fw-bold mb-0 text-dark"><?= $kpis['total_embarcaciones'] ?> / <?= $kpis['total_muelles'] ?></h3>
                    <small class="text-warning"><i class="fa-solid fa-ship me-1"></i>Embarcaciones y Muelles</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="fa-solid fa-anchor fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PANEL INTERACTIVO DE CONTROL DE GRÁFICAS -->
<div class="card bg-white shadow-sm mb-4 border-0 rounded-4">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom pb-3 mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa-solid fa-sliders me-2 text-primary"></i>Configurador Visual de Gráficas Fluviales
                </h5>
                <small class="text-muted">Personaliza en tiempo real la información y el tipo de gráfico según tu preferencia</small>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Selector del Dataset / Reporte a visualizar -->
                <div class="input-group input-group-sm" style="min-width: 260px;">
                    <label class="input-group-text fw-semibold bg-light" for="selector-dataset">
                        <i class="fa-solid fa-layer-group text-primary me-1"></i>Reporte:
                    </label>
                    <select class="form-select fw-semibold" id="selector-dataset">
                        <option value="rutas_ingresos">Ingresos por Ruta Fluvial (Top)</option>
                        <option value="rutas_pasajeros">Pasajeros por Ruta Fluvial</option>
                        <option value="flota_viajes">Viajes por Embarcación</option>
                        <option value="flota_pasajeros">Pasajeros por Embarcación</option>
                        <option value="flota_carga">Carga Transportada por Embarcación (kg)</option>
                        <option value="tipos_embarcacion">Distribución de Flota por Tipo</option>
                        <option value="deptos_muelles">Puertos por Departamento</option>
                        <option value="metodos_pago">Recaudación por Método de Pago</option>
                        <option value="estados_viajes">Estado de Itinerarios Fluviales</option>
                    </select>
                </div>

                <!-- Selector de Tipo de Gráfica -->
                <div class="btn-group btn-group-sm" role="group" aria-label="Tipo de Gráfico" id="chart-type-buttons">
                    <button type="button" class="btn btn-outline-primary active" data-chart-type="bar" title="Gráfico de Barras">
                        <i class="fa-solid fa-chart-column me-1"></i>Barras
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-chart-type="line" title="Gráfico de Líneas">
                        <i class="fa-solid fa-chart-line me-1"></i>Líneas
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-chart-type="pie" title="Gráfico Circular (Torta)">
                        <i class="fa-solid fa-chart-pie me-1"></i>Torta
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-chart-type="doughnut" title="Gráfico de Dona">
                        <i class="fa-solid fa-circle-notch me-1"></i>Dona
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-chart-type="polarArea" title="Gráfico de Área Polar">
                        <i class="fa-solid fa-compass-drafting me-1"></i>Polar
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenedor del Lienzo de la Gráfica Principal Interactiva -->
        <div class="position-relative" style="min-height: 380px; max-height: 480px; width: 100%;">
            <canvas id="graficaInteractivaPrincipal"></canvas>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top small text-muted">
            <span id="grafica-descripcion-dinamica">
                <i class="fa-solid fa-info-circle text-info me-1"></i>Visualizando el rendimiento de las principales conexiones fluviales en Colombia.
            </span>
            <span class="badge bg-light text-dark border">
                <i class="fa-solid fa-arrows-rotate me-1 text-primary"></i>Actualización interactiva instantánea
            </span>
        </div>
    </div>
</div>

<!-- SECCIÓN DE GRÁFICAS COMPLEMENTARIAS DUALES -->
<div class="row g-4 mb-4">
    <!-- Gráfica Secundaria 1: Departamentos & Puertos -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100 rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-bold text-dark">
                    <i class="fa-solid fa-map-location-dot text-success me-2"></i>Puertos Fluviales por Departamento
                </span>
                <span class="badge bg-success-subtle text-success fw-bold">Infraestructura</span>
            </div>
            <div class="card-body p-3">
                <div style="height: 280px; width: 100%;">
                    <canvas id="graficaDeptosMuelles"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica Secundaria 2: Recaudación por Métodos de Pago -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100 rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                <span class="fw-bold text-dark">
                    <i class="fa-solid fa-credit-card text-warning me-2"></i>Recaudación por Método de Pago
                </span>
                <span class="badge bg-warning-subtle text-warning-emphasis fw-bold">Financiero</span>
            </div>
            <div class="card-body p-3">
                <div style="height: 280px; width: 100%;">
                    <canvas id="graficaMetodosPago"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLAS DETALLADAS DE RESPALDO (Rendimiento por Ruta y Flota) -->
<div class="row g-4 mb-4">
    <!-- Reporte de Rutas -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100 rounded-4">
            <div class="card-header fw-bold text-dark bg-white border-bottom">
                <i class="fa-solid fa-route me-2 text-primary"></i>Detalle de Rendimiento por Ruta Fluvial
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 360px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top">
                            <tr>
                                <th>Ruta</th>
                                <th>Pasajes</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reporteRutas)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted">Sin datos suficientes</td></tr>
                            <?php else: ?>
                                <?php foreach ($reporteRutas as $r): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($r['origen']) ?> &rarr; <?= htmlspecialchars($r['destino']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($r['origen_depto'] ?? '') ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= $r['total_pasajes'] ?> pax</span></td>
                                        <td class="text-end fw-bold text-success">$<?= number_format($r['total_ingresos'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reporte de Embarcaciones -->
    <div class="col-12 col-lg-6">
        <div class="card bg-white shadow-sm h-100 rounded-4">
            <div class="card-header fw-bold text-dark bg-white border-bottom">
                <i class="fa-solid fa-ship me-2 text-info"></i>Utilización de la Flota Fluvial
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 360px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="sticky-top">
                            <tr>
                                <th>Embarcación</th>
                                <th>Viajes</th>
                                <th>Pasajeros</th>
                                <th class="text-end">Carga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reporteFlota)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Sin datos suficientes</td></tr>
                            <?php else: ?>
                                <?php foreach ($reporteFlota as $f): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($f['nombre']) ?></div>
                                            <small class="text-muted"><code><?= htmlspecialchars($f['matricula']) ?></code> &bull; <?= str_replace('_', ' ', $f['tipo']) ?></small>
                                        </td>
                                        <td><span class="badge bg-primary"><?= $f['viajes_realizados'] ?></span></td>
                                        <td><span class="badge bg-success"><?= $f['pasajeros_transportados'] ?> Pax</span></td>
                                        <td class="text-end"><span class="badge bg-warning text-dark"><?= number_format($f['carga_transportada_kg'], 1) ?> kg</span></td>
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

<!-- LIBRERÍA CHART.JS CDN Y SCRIPT INTERACTIVO -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Datos preparados desde PHP
    const rawData = {
        rutas: <?= json_encode($reporteRutas, JSON_UNESCAPED_UNICODE) ?>,
        flota: <?= json_encode($reporteFlota, JSON_UNESCAPED_UNICODE) ?>,
        deptos: <?= json_encode($reporteDeptos, JSON_UNESCAPED_UNICODE) ?>,
        tiposFlota: <?= json_encode($reporteTiposFlota, JSON_UNESCAPED_UNICODE) ?>,
        estadosViajes: <?= json_encode($reporteEstadosViajes, JSON_UNESCAPED_UNICODE) ?>,
        metodosPago: <?= json_encode($reporteMetodosPago, JSON_UNESCAPED_UNICODE) ?>,
        cargas: <?= json_encode($reporteCargas, JSON_UNESCAPED_UNICODE) ?>
    };

    // Paleta de colores fluviales adaptativa
    const colors = [
        '#0284c7', '#0ea5e9', '#38bdf8', '#10b981', '#34d399', 
        '#f59e0b', '#fbbf24', '#f43f5e', '#a855f7', '#6366f1',
        '#06b6d4', '#14b8a6', '#84cc16', '#eab308', '#ec4899'
    ];

    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDark ? '#f1f5f9' : '#1e293b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';

    // Referencias a los elementos del configurador
    const selectorDataset = document.getElementById('selector-dataset');
    const typeButtons = document.querySelectorAll('#chart-type-buttons button');
    const descElement = document.getElementById('grafica-descripcion-dinamica');
    const canvasPrincipal = document.getElementById('graficaInteractivaPrincipal');

    let currentChartType = 'bar';
    let chartPrincipal = null;

    // Generador de Dataset según selección
    const getDatasetConfig = (key) => {
        let labels = [];
        let data = [];
        let label = '';
        let description = '';

        switch (key) {
            case 'rutas_ingresos':
                const topRutas = rawData.rutas.slice(0, 10);
                labels = topRutas.map(r => `${r.origen} - ${r.destino}`);
                data = topRutas.map(r => parseFloat(r.total_ingresos || 0));
                label = 'Ingresos ($ COP)';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Mostrando ingresos monetarios en pesos colombianos por las principales rutas fluviales.';
                break;

            case 'rutas_pasajeros':
                const topRutasPax = rawData.rutas.slice(0, 10);
                labels = topRutasPax.map(r => `${r.origen} - ${r.destino}`);
                data = topRutasPax.map(r => parseInt(r.total_pasajes || 0));
                label = 'Boletos Vendidos';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Cantidad de pasajeros y boletos expedidos para cada trayecto fluvial.';
                break;

            case 'flota_viajes':
                labels = rawData.flota.map(f => f.nombre);
                data = rawData.flota.map(f => parseInt(f.viajes_realizados || 0));
                label = 'Viajes / Zarpes Realizados';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Número total de zarpes operados por cada embarcación de la flota.';
                break;

            case 'flota_pasajeros':
                labels = rawData.flota.map(f => f.nombre);
                data = rawData.flota.map(f => parseInt(f.pasajeros_transportados || 0));
                label = 'Pasajeros Transportados';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Total de personas movilizadas por cada motonave o lancha.';
                break;

            case 'flota_carga':
                labels = rawData.flota.map(f => f.nombre);
                data = rawData.flota.map(f => parseFloat(f.carga_transportada_kg || 0));
                label = 'Carga Transportada (kg)';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Kilogramos de carga y fletes fluviales transportados en bodega.';
                break;

            case 'tipos_embarcacion':
                labels = rawData.tiposFlota.map(t => t.tipo.replace('_', ' ').toUpperCase());
                data = rawData.tiposFlota.map(t => parseInt(t.cantidad || 0));
                label = 'Cantidad de Embarcaciones';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Distribución de la flota por tipo: lanchas rápidas, ferries, botes y barcazas.';
                break;

            case 'deptos_muelles':
                const topDeptos = rawData.deptos.slice(0, 12);
                labels = topDeptos.map(d => d.departamento);
                data = topDeptos.map(d => parseInt(d.total_muelles || 0));
                label = 'Puertos / Muelles Activos';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Número de muelles fluviales registrados por departamento.';
                break;

            case 'metodos_pago':
                labels = rawData.metodosPago.map(m => m.metodo_pago.toUpperCase());
                data = rawData.metodosPago.map(m => parseFloat(m.total_recaudado || 0));
                label = 'Recaudación ($ COP)';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Total recaudado según forma de pago (Efectivo, Tarjeta, Transferencia).';
                break;

            case 'estados_viajes':
                labels = rawData.estadosViajes.map(e => e.estado.replace('_', ' ').toUpperCase());
                data = rawData.estadosViajes.map(e => parseInt(e.total || 0));
                label = 'Total de Viajes';
                description = '<i class="fa-solid fa-info-circle text-info me-1"></i>Estado actual de los itinerarios programados, en tránsito y arribados.';
                break;
        }

        return { labels, data, label, description };
    };

    // Renderizar Gráfica Principal
    const renderChartPrincipal = () => {
        const datasetKey = selectorDataset.value;
        const config = getDatasetConfig(datasetKey);

        descElement.innerHTML = config.description;

        if (chartPrincipal) {
            chartPrincipal.destroy();
        }

        const isCircular = ['pie', 'doughnut', 'polarArea'].includes(currentChartType);

        chartPrincipal = new Chart(canvasPrincipal, {
            type: currentChartType,
            data: {
                labels: config.labels,
                datasets: [{
                    label: config.label,
                    data: config.data,
                    backgroundColor: isCircular ? colors.slice(0, config.data.length) : 'rgba(2, 132, 199, 0.75)',
                    borderColor: isCircular ? '#ffffff' : '#0284c7',
                    borderWidth: isCircular ? 2 : 1.5,
                    borderRadius: currentChartType === 'bar' ? 6 : 0,
                    fill: currentChartType === 'line' ? true : false,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: isCircular,
                        position: 'right',
                        labels: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 12 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                if (config.label.includes('$')) {
                                    return ' ' + config.label + ': $' + Number(val).toLocaleString('es-CO');
                                }
                                return ' ' + config.label + ': ' + Number(val).toLocaleString('es-CO');
                            }
                        }
                    }
                },
                scales: isCircular ? {} : {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 11 }, maxRotation: 45 }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { 
                            color: textColor, 
                            font: { family: 'Plus Jakarta Sans' },
                            callback: function(value) {
                                if (config.label.includes('$')) {
                                    return '$' + Number(value).toLocaleString('es-CO');
                                }
                                return Number(value).toLocaleString('es-CO');
                            }
                        }
                    }
                }
            }
        });
    };

    // Eventos del configurador
    selectorDataset.addEventListener('change', renderChartPrincipal);

    typeButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            typeButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentChartType = btn.getAttribute('data-chart-type');
            renderChartPrincipal();
        });
    });

    // Render Inicial Gráfica Principal
    renderChartPrincipal();

    // 2. Gráfica Secundaria: Puertos por Departamento
    const top10Deptos = rawData.deptos.slice(0, 8);
    const canvasDeptos = document.getElementById('graficaDeptosMuelles');
    if (canvasDeptos) {
        new Chart(canvasDeptos, {
            type: 'bar',
            data: {
                labels: top10Deptos.map(d => d.departamento),
                datasets: [{
                    label: 'Muelles Fluviales',
                    data: top10Deptos.map(d => d.total_muelles),
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    borderColor: '#10b981',
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { grid: { display: false }, ticks: { color: textColor } }
                }
            }
        });
    }

    // 3. Gráfica Secundaria: Métodos de Pago
    const canvasMetodos = document.getElementById('graficaMetodosPago');
    if (canvasMetodos) {
        new Chart(canvasMetodos, {
            type: 'doughnut',
            data: {
                labels: rawData.metodosPago.map(m => m.metodo_pago.toUpperCase()),
                datasets: [{
                    data: rawData.metodosPago.map(m => m.total_recaudado),
                    backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: isDark ? '#0e1b2f' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, font: { family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
    }

    // Escuchar cambios de tema (Modo Oscuro / Claro) para actualizar los colores de los ejes
    const themeObserver = new MutationObserver(() => {
        const darkNow = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const newTextColor = darkNow ? '#f1f5f9' : '#1e293b';
        const newGridColor = darkNow ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
        renderChartPrincipal();
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
});
</script>