<?php
$currentUser = AuthHelper::user();
$facturaNumero = 'FACT-BOL-' . date('Y', strtotime($boleto['created_at'])) . '-' . str_pad($boleto['id'], 5, '0', STR_PAD_LEFT);
$cufeHash = hash('sha256', $boleto['codigo_boleto'] . $boleto['created_at'] . '9014587892' . $boleto['precio_pagado']);
$qrData = "https://fluviapp.com/factura/verificar?cufe=" . substr($cufeHash, 0, 32) . "&factura=" . $facturaNumero . "&val=" . (int)$boleto['precio_pagado'];
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Factura Digital - ' . $facturaNumero) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <!-- Theme Manager (Modo Oscuro) -->
    <script src="<?= BASE_URL ?>/public/js/theme.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 30px 15px;
        }
        .invoice-card {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #091322 0%, #0c2340 50%, #034b79 100%);
            color: #ffffff;
            padding: 35px 40px;
        }
        .invoice-body {
            padding: 40px;
        }
        .info-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
        }
        .watermark-paid {
            position: absolute;
            right: 40px;
            top: 280px;
            font-size: 4rem;
            font-weight: 800;
            color: rgba(16, 185, 129, 0.08);
            transform: rotate(-15deg);
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .table-invoice th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
        }
        .table-invoice td {
            padding: 16px;
            vertical-align: middle;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            .invoice-header {
                background: #0c2340 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 25px 30px !important;
            }
            .invoice-body {
                padding: 25px 30px !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

<!-- Barra Superior de Acciones (No Imprimible) -->
<div class="container no-print mb-4" style="max-width: 860px;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 bg-white p-3 rounded-4 shadow-sm border">
        <div>
            <a href="<?= BASE_URL ?>/cliente/mis-boletos" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i>Mis Tiquetes
            </a>
            <a href="<?= BASE_URL ?>/cliente/ver-ruta?id=<?= $boleto['id'] ?>" class="btn btn-outline-primary btn-sm fw-semibold ms-1">
                <i class="fa-solid fa-map-location-dot me-1"></i>Ver Mapa de Ruta
            </a>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                <i class="fa-solid fa-moon text-info"></i>
            </button>
            <a href="<?= BASE_URL ?>/boletos/ticket?id=<?= $boleto['id'] ?>" target="_blank" class="btn btn-outline-dark btn-sm fw-semibold">
                <i class="fa-solid fa-receipt me-1"></i>Tirilla Térmica
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="fa-solid fa-file-pdf me-1"></i>Descargar Factura (PDF) / Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Contenedor Oficial de la Factura -->
<div class="invoice-card position-relative">
    <div class="watermark-paid">PAGADO</div>

    <!-- Encabezado Corporativo -->
    <div class="invoice-header">
        <div class="row align-items-center">
            <div class="col-12 col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center mb-2">
                    <img src="<?= BASE_URL ?>/public/img/logo.jpg" alt="FluviApp Logo" class="brand-logo-img me-3 shadow-sm" style="width: 52px; height: 52px; border-color: rgba(255,255,255,0.7);">
                    <div>
                        <h3 class="fw-bold mb-0 text-white">FluviApp S.A.S.</h3>
                        <small class="text-info fw-semibold">Operador Integral de Transporte Fluvial</small>
                    </div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.8rem; line-height: 1.4;">
                    <strong>NIT:</strong> 901.458.789-2 &bull; Régimen Común<br>
                    <strong>Habilitación Fluvial:</strong> MinTransporte Resolución Nº 2026-FLUV-448<br>
                    <strong>Muelle Principal:</strong> Terminal Fluvial de Pasajeros &bull; Magangué, Bolívar<br>
                    <strong>Atención:</strong> (605) 687-0000 &bull; facturacion@fluviapp.com
                </div>
            </div>

            <div class="col-12 col-md-5 text-md-end">
                <span class="badge bg-success text-uppercase px-3 py-2 fw-bold mb-2">
                    <i class="fa-solid fa-circle-check me-1"></i>Factura Electrónica Oficial
                </span>
                <div class="h4 fw-bold text-white mb-1"><?= $facturaNumero ?></div>
                <div class="small text-white-50">
                    Boleto Nº: <strong class="text-white"><?= htmlspecialchars($boleto['codigo_boleto']) ?></strong>
                </div>
                <div class="small text-white-50">
                    Fecha de Emisión: <strong class="text-white"><?= $boleto['created_at'] ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo de la Factura -->
    <div class="invoice-body">
        <!-- Ficha de Información: Adquirente y Viaje -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="info-pill h-100">
                    <span class="badge bg-primary text-uppercase mb-2" style="font-size: 0.65rem;">Datos del Adquirente / Pasajero</span>
                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($boleto['pasajero_nombre']) ?></h6>
                    <div class="small text-muted mb-1">
                        <strong>Cédula / Documento:</strong> <?= htmlspecialchars($boleto['pasajero_documento']) ?>
                    </div>
                    <div class="small text-muted mb-1">
                        <strong>Teléfono de Contacto:</strong> <?= htmlspecialchars($boleto['pasajero_telefono'] ?: 'No registrado') ?>
                    </div>
                    <div class="small text-muted">
                        <strong>Asiento Asignado:</strong> <span class="badge bg-secondary">Asiento #<?= $boleto['numero_asiento'] ?? '1' ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-pill h-100">
                    <span class="badge bg-info text-dark text-uppercase mb-2" style="font-size: 0.65rem;">Detalles del Zarpe y Navegación</span>
                    <h6 class="fw-bold text-dark mb-1">Viaje: <?= htmlspecialchars($boleto['codigo_viaje']) ?></h6>
                    <div class="small text-muted mb-1">
                        <strong>Embarcación:</strong> <?= htmlspecialchars($boleto['embarcacion_nombre']) ?> (Matrícula: <?= htmlspecialchars($boleto['embarcacion_matricula']) ?>)
                    </div>
                    <div class="small text-muted mb-1">
                        <strong>Capitán al Mando:</strong> <?= htmlspecialchars($boleto['capitan_nombre'] ?? 'Capitán Certificado') ?>
                    </div>
                    <div class="small text-muted">
                        <strong>Zarpe Programado:</strong> <strong class="text-primary"><?= $boleto['fecha_salida'] ?> a las <?= substr($boleto['hora_salida'], 0, 5) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ficha del Trayecto Fluvial -->
        <div class="p-3 bg-light rounded-4 border mb-4">
            <div class="row align-items-center text-center">
                <div class="col-5 text-start ps-3">
                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Muelle de Origen</small>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($boleto['origen_nombre']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($boleto['origen_municipio']) ?> (<?= htmlspecialchars($boleto['origen_rio']) ?>)</small>
                </div>
                <div class="col-2 text-center text-primary">
                    <i class="fa-solid fa-ship fs-4"></i>
                    <div class="small text-muted" style="font-size: 0.7rem;">Directo</div>
                </div>
                <div class="col-5 text-end pe-3">
                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Muelle de Destino</small>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($boleto['destino_nombre']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($boleto['destino_municipio']) ?> (<?= htmlspecialchars($boleto['destino_rio']) ?>)</small>
                </div>
            </div>
        </div>

        <!-- Tabla de Liquidación del Servicio -->
        <div class="table-responsive mb-4">
            <table class="table table-invoice table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10%;">Cant.</th>
                        <th style="width: 55%;">Descripción del Servicio Fluvial</th>
                        <th style="width: 15%;" class="text-end">Tarifa Base</th>
                        <th style="width: 20%;" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold">1</td>
                        <td>
                            <div class="fw-bold text-dark">Servicio Regular de Transporte Fluvial Intermunicipal de Pasajeros</div>
                            <div class="small text-muted">
                                Trayecto: <?= htmlspecialchars($boleto['origen_municipio']) ?> &rarr; <?= htmlspecialchars($boleto['destino_municipio']) ?> &bull; Asiento #<?= $boleto['numero_asiento'] ?? '1' ?> &bull; Incluye Póliza SOAF
                            </div>
                        </td>
                        <td class="text-end fw-semibold">$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-dark">$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Desglose de Totales y QR -->
        <div class="row align-items-center mb-4">
            <div class="col-12 col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
                    <img src="<?= $qrUrl ?>" alt="QR de Factura Electrónica" class="border rounded p-1 bg-white me-3" style="width: 90px; height: 90px;">
                    <div>
                        <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-qrcode me-1 text-primary"></i>Verificación Digital DIAN / FluviApp</div>
                        <div class="text-muted" style="font-size: 0.7rem; word-break: break-all;">
                            <strong>CUFE:</strong> <?= $cufeHash ?>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                            <strong>Método de Pago:</strong> <?= strtoupper($boleto['metodo_pago']) ?> (Confirmado)
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Subtotal Flete/Pasaje:</span>
                        <span>$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?> COP</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>IVA (Art. 476 Numeral 2 E.T.):</span>
                        <span class="badge bg-secondary-subtle text-secondary">$0 (Exento)</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Tasa Portuaria / Seguro SOAF:</span>
                        <span>Incluido</span>
                    </div>
                    <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                        <strong class="text-dark fs-6">TOTAL PAGADO:</strong>
                        <strong class="text-primary fs-4">$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?> COP</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cláusulas y Términos Legales -->
        <div class="border-top pt-3 text-muted" style="font-size: 0.72rem; line-height: 1.4;">
            <p class="mb-1">
                <strong>Cláusula de Transporte Fluvial:</strong> Este comprobante equivale a la factura electrónica de venta para transporte fluvial de pasajeros, conforme al Decreto 358 de 2020 y Resoluciones DIAN vigentes. El pasajero cuenta con Seguro Obligatorio de Accidentes Fluviales (SOAF) amparado por póliza colectiva vigente de la empresa.
            </p>
            <p class="mb-0">
                <strong>Normas de Seguridad en Muelle:</strong> Es obligatorio el uso del chaleco salvavidas durante todo el trayecto fluvial. Los pasajeros deben presentarse con su documento de identidad y este comprobante 20 minutos antes del zarpe en el muelle de abordaje. Equipaje de mano permitido: hasta 15 kg sin costo adicional.
            </p>
        </div>
    </div>
</div>

<div class="text-center text-muted small mt-4 no-print">
    Documento emitido electrónicamente por <strong><?= APP_NAME ?></strong> &bull; Sistema de Gestión Fluvial &bull; <?= date('Y') ?>
</div>

</body>
</html>