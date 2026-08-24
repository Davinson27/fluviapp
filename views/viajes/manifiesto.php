<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Manifiesto de Zarpe') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .manifest-container {
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        @media print {
            body { background: #fff; }
            .manifest-container { box-shadow: none; margin: 0; max-width: 100%; padding: 15px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="manifest-container">
    <!-- Botones de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
        <a href="<?= BASE_URL ?>/viajes" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i>Volver a Itinerarios
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-print me-1"></i>Imprimir Manifiesto Oficial
        </button>
    </div>

    <!-- Encabezado Oficial -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase mb-1">MANIFIESTO OFICIAL DE ZARPE Y NAVEGACIÓN FLUVIAL</h4>
        <div class="text-muted small">REPÚBLICA DE COLOMBIA &bull; AUTORIDAD DE TRANSPORTE FLUVIAL Y PORTUARIO</div>
        <div class="badge bg-dark mt-2 p-2 fs-6">CÓDIGO DE ITINERARIO: <?= htmlspecialchars($viaje['codigo_viaje']) ?></div>
    </div>

    <!-- Datos Generales del Viaje -->
    <div class="card mb-4 border">
        <div class="card-header bg-light fw-bold">1. DATOS DE LA EMBARCACIÓN Y TRAYECTO</div>
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-6 col-md-3"><strong>Embarcación:</strong><br><?= htmlspecialchars($viaje['embarcacion_nombre']) ?></div>
                <div class="col-6 col-md-3"><strong>Matrícula:</strong><br><?= htmlspecialchars($viaje['embarcacion_matricula']) ?></div>
                <div class="col-6 col-md-3"><strong>Capitán al mando:</strong><br><?= htmlspecialchars($viaje['capitan_nombre'] ?? 'Sin Asignar') ?></div>
                <div class="col-6 col-md-3"><strong>Teléfono Capitán:</strong><br><?= htmlspecialchars($viaje['capitan_telefono'] ?? 'N/A') ?></div>

                <div class="col-6 col-md-3 mt-2"><strong>Muelle Origen:</strong><br><?= htmlspecialchars($viaje['origen_nombre']) ?> (<?= htmlspecialchars($viaje['origen_municipio']) ?>)</div>
                <div class="col-6 col-md-3 mt-2"><strong>Muelle Destino:</strong><br><?= htmlspecialchars($viaje['destino_nombre']) ?> (<?= htmlspecialchars($viaje['destino_municipio']) ?>)</div>
                <div class="col-6 col-md-3 mt-2"><strong>Fecha / Hora Zarpe:</strong><br><?= $viaje['fecha_salida'] ?> <?= substr($viaje['hora_salida'], 0, 5) ?></div>
                <div class="col-6 col-md-3 mt-2"><strong>Río / Afluente:</strong><br><?= htmlspecialchars($viaje['origen_rio']) ?></div>
            </div>
        </div>
    </div>

    <!-- Listado de Pasajeros -->
    <div class="card mb-4 border">
        <div class="card-header bg-light fw-bold d-flex justify-content-between">
            <span>2. LISTA OFICIAL DE PASAJEROS A BORDO</span>
            <span>Total Pasajeros: <?= count($pasajeros) ?> / Capacidad: <?= $viaje['embarcacion_cap_pasajeros'] ?></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Boleto</th>
                        <th>Nombre Completo del Pasajero</th>
                        <th>Documento de Identidad</th>
                        <th>Teléfono</th>
                        <th>Asiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pasajeros)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">No se registran pasajeros emitidos en este viaje</td></tr>
                    <?php else: ?>
                        <?php foreach ($pasajeros as $idx => $p): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($p['codigo_boleto']) ?></td>
                                <td><?= htmlspecialchars($p['pasajero_nombre']) ?></td>
                                <td><?= htmlspecialchars($p['pasajero_documento']) ?></td>
                                <td><?= htmlspecialchars($p['pasajero_telefono'] ?: 'N/A') ?></td>
                                <td class="text-center"><?= $p['numero_asiento'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Listado de Carga y Encomiendas -->
    <div class="card mb-4 border">
        <div class="card-header bg-light fw-bold d-flex justify-content-between">
            <span>3. MANIFIESTO DE CARGA Y ENCOMIENDAS</span>
            <span>Total Encomiendas: <?= count($cargas) ?></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>No. Guía</th>
                        <th>Descripción del Paquete / Carga</th>
                        <th>Remitente</th>
                        <th>Destinatario</th>
                        <th>Peso (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cargas)): ?>
                        <tr><td colspan="6" class="text-center py-3 text-muted">No hay carga ni encomiendas asignadas</td></tr>
                    <?php else: ?>
                        <?php foreach ($cargas as $idx => $c): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($c['guia_numero']) ?></td>
                                <td><?= htmlspecialchars($c['descripcion_carga']) ?></td>
                                <td><?= htmlspecialchars($c['remitente_nombre']) ?></td>
                                <td><?= htmlspecialchars($c['destinatario_nombre']) ?></td>
                                <td class="text-end"><?= number_format($c['peso_kg'], 1) ?> kg</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Firmas Oficiales de Control -->
    <div class="row mt-5 pt-4 text-center">
        <div class="col-6">
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
                <strong>Firma del Capitán / Patrón</strong><br>
                <small><?= htmlspecialchars($viaje['capitan_nombre'] ?? 'Firma y Documento') ?></small>
            </div>
        </div>
        <div class="col-6">
            <div style="border-top: 1px solid #000; width: 80%; margin: 0 auto; padding-top: 5px;">
                <strong>Firma y Sello Autoridad Fluvial / Muelle</strong><br>
                <small>Capitanía de Puerto / Inspector</small>
            </div>
        </div>
    </div>
</div>

</body>
</html>
