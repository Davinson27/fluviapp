<?php
$currentUser = AuthHelper::user();
$facturaNumero = 'FACT-FLETE-' . date('Y', strtotime($carga['created_at'] ?? 'now')) . '-' . str_pad($carga['id'], 5, '0', STR_PAD_LEFT);
$cufeHash = hash('sha256', $carga['guia_numero'] . ($carga['created_at'] ?? '') . '9014587892' . $carga['valor_flete']);
$qrData = "https://fluviapp.com/factura/flete?guia=" . urlencode($carga['guia_numero']) . "&cufe=" . substr($cufeHash, 0, 32);
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Factura de Flete Fluvial - ' . $facturaNumero) ?></title>
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
            background: linear-gradient(135deg, #091322 0%, #064e3b 50%, #047857 100%);
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
                background: #064e3b !important;
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
            <a href="<?= BASE_URL ?>/cliente/mis-encomiendas" class="btn btn-outline-secondary btn-sm fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i>Mis Encomiendas
            </a>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="theme-toggle-btn shadow-sm" title="Alternar Modo Oscuro / Claro" aria-label="Alternar Tema">
                <i class="fa-solid fa-moon text-info"></i>
            </button>
            <button onclick="window.print()" class="btn btn-success btn-sm fw-bold shadow-sm">
                <i class="fa-solid fa-file-pdf me-1"></i>Descargar Factura de Flete (PDF) / Imprimir
            </button>
        </div>
    </div>
</div>

<!-- Contenedor Oficial de la Factura de Encomienda -->
<div class="invoice-card position-relative">
    <div class="watermark-paid">DESPACHADO</div>

    <!-- Encabezado Corporativo -->
    <div class="invoice-header">
        <div class="row align-items-center">
            <div class="col-12 col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-success text-white p-2 rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                        <i class="fa-solid fa-boxes-packing fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-white">FluviApp Carga S.A.S.</h3>
                        <small class="text-white-50 fw-semibold">División Fluvial de Fletes y Encomiendas Express</small>
                    </div>
                </div>
                <div class="small text-white-50 mt-2" style="font-size: 0.8rem; line-height: 1.4;">
                    <strong>NIT:</strong> 901.458.789-2 &bull; Régimen Responsable de IVA<br>
                    <strong>Habilitación Fluvial Carga:</strong> MinTransporte Res. Nº 2026-CARG-890<br>
                    <strong>Muelle Operativo:</strong> Terminal Fluvial de Carga &bull; Magangué, Bolívar<br>
                    <strong>Línea de Guías:</strong> (605) 687-0000 &bull; envios@fluviapp.com
                </div>
            </div>

            <div class="col-12 col-md-5 text-md-end">
                <span class="badge bg-white text-success text-uppercase px-3 py-2 fw-bold mb-2">
                    <i class="fa-solid fa-file-invoice-dollar me-1"></i>Factura Oficial de Flete Fluvial
                </span>
                <div class="h4 fw-bold text-white mb-1"><?= $facturaNumero ?></div>
                <div class="small text-white-50">
                    Guía de Encomienda: <strong class="text-white"><?= htmlspecialchars($carga['guia_numero']) ?></strong>
                </div>
                <div class="small text-white-50">
                    Fecha de Registro: <strong class="text-white"><?= $carga['created_at'] ?? date('Y-m-d H:i') ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuerpo de la Factura -->
    <div class="invoice-body">
        <!-- Ficha de Información: Remitente y Destinatario -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <div class="info-pill h-100">
                    <span class="badge bg-primary text-uppercase mb-2" style="font-size: 0.65rem;">Remitente (Origen)</span>
                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($carga['remitente_nombre']) ?></h6>
                    <div class="small text-muted mb-1">
                        <strong>Teléfono / WhatsApp:</strong> <?= htmlspecialchars($carga['remitente_telefono'] ?: 'N/A') ?>
                    </div>
                    <div class="small text-muted">
                        <strong>Muelle de Despacho:</strong> <?= htmlspecialchars($carga['origen_nombre'] ?? 'Muelle Principal') ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-pill h-100">
                    <span class="badge bg-success text-uppercase mb-2" style="font-size: 0.65rem;">Destinatario (Entrega)</span>
                    <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($carga['destinatario_nombre']) ?></h6>
                    <div class="small text-muted mb-1">
                        <strong>Teléfono de Notificación:</strong> <?= htmlspecialchars($carga['destinatario_telefono'] ?: 'N/A') ?>
                    </div>
                    <div class="small text-muted">
                        <strong>Muelle de Reclamación:</strong> <?= htmlspecialchars($carga['destino_nombre'] ?? 'Muelle Destino') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ficha del Trayecto Fluvial y Embarcación -->
        <div class="p-3 bg-light rounded-4 border mb-4">
            <div class="row align-items-center text-center">
                <div class="col-5 text-start ps-3">
                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Muelle de Salida</small>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($carga['origen_nombre']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($carga['origen_municipio']) ?> (<?= htmlspecialchars($carga['origen_rio']) ?>)</small>
                </div>
                <div class="col-2 text-center text-success">
                    <i class="fa-solid fa-boxes-packing fs-4"></i>
                    <div class="small text-muted" style="font-size: 0.7rem;">En Bodega</div>
                </div>
                <div class="col-5 text-end pe-3">
                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Muelle de Desembarque</small>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($carga['destino_nombre']) ?></div>
                    <small class="text-muted"><?= htmlspecialchars($carga['destino_municipio']) ?> (<?= htmlspecialchars($carga['destino_rio']) ?>)</small>
                </div>
            </div>
        </div>

        <!-- Tabla de Liquidación del Flete Fluvial -->
        <div class="table-responsive mb-4">
            <table class="table table-invoice table-bordered">
                <thead>
                    <tr>
                        <th style="width: 15%;">Peso Cert.</th>
                        <th style="width: 50%;">Descripción de la Mercancía / Encomienda</th>
                        <th style="width: 15%;" class="text-end">Valor Declarado</th>
                        <th style="width: 20%;" class="text-end">Valor Flete</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold">
                            <span class="badge bg-secondary fs-6"><?= number_format($carga['peso_kg'], 1) ?> kg</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($carga['descripcion_carga']) ?></div>
                            <div class="small text-muted">
                                Guía: <code><?= htmlspecialchars($carga['guia_numero']) ?></code> &bull; Embarcación: <?= htmlspecialchars($carga['embarcacion_nombre']) ?> &bull; Matrícula: <?= htmlspecialchars($carga['embarcacion_matricula'] ?? 'N/A') ?>
                            </div>
                        </td>
                        <td class="text-end text-muted">$<?= number_format($carga['valor_declarado'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold text-success fs-6">$<?= number_format($carga['valor_flete'], 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Desglose de Totales y QR -->
        <div class="row align-items-center mb-4">
            <div class="col-12 col-md-7 mb-3 mb-md-0">
                <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
                    <img src="<?= $qrUrl ?>" alt="QR de Factura Electrónica de Flete" class="border rounded p-1 bg-white me-3" style="width: 90px; height: 90px;">
                    <div>
                        <div class="fw-bold text-dark small mb-1"><i class="fa-solid fa-qrcode me-1 text-success"></i>Trazabilidad de Carga y Facturación DIAN</div>
                        <div class="text-muted" style="font-size: 0.7rem; word-break: break-all;">
                            <strong>CUFE:</strong> <?= $cufeHash ?>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                            <strong>Estado de Entrega:</strong> <span class="badge bg-success text-uppercase"><?= str_replace('_', ' ', $carga['estado']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Flete Básico Fluvial:</span>
                        <span>$<?= number_format($carga['valor_flete'], 0, ',', '.') ?> COP</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Seguro de Pérdida / Avería:</span>
                        <span class="text-success fw-semibold">Amparado (100%)</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>IVA Transporte Fluvial:</span>
                        <span class="badge bg-secondary-subtle text-secondary">$0 (Exento)</span>
                    </div>
                    <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                        <strong class="text-dark fs-6">TOTAL FLETE:</strong>
                        <strong class="text-success fs-4">$<?= number_format($carga['valor_flete'], 0, ',', '.') ?> COP</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cláusulas y Términos Legales de Carga -->
        <div class="border-top pt-3 text-muted" style="font-size: 0.72rem; line-height: 1.4;">
            <p class="mb-1">
                <strong>Condiciones de Transporte de Encomiendas Fluviales:</strong> El remitente declara bajo gravedad de juramento que la mercancía contenida no incluye sustancias ilegales, armas, explosivos ni materiales restringidos por la ley colombiana. La empresa responde hasta por el valor declarado en caso de siniestro fluvial comprobado.
            </p>
            <p class="mb-0">
                <strong>Reclamación en Muelle:</strong> El destinatario dispone de 5 días hábiles a partir de la llegada de la embarcación al muelle de destino para reclamar su paquete presentando su documento de identidad y el número de guía correspondiente.
            </p>
        </div>
    </div>
</div>

<div class="text-center text-muted small mt-4 no-print">
    Comprobante oficial emitido por <strong><?= APP_NAME ?></strong> &bull; División de Fletes y Carga Fluvial &bull; <?= date('Y') ?>
</div>

</body>
</html>