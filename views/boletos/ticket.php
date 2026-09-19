<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Tiquete de Pasaje') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Courier New', Courier, monospace;
        }
        .ticket {
            width: 320px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border: 1px dashed #94a3b8;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .ticket-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .ticket-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 0.8rem;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }
        @media print {
            body { background: #fff; }
            .ticket { border: none; box-shadow: none; margin: 0; width: 100%; max-width: 300px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="text-center mt-3 no-print">
    <a href="<?= BASE_URL ?>/boletos" class="btn btn-outline-secondary btn-sm me-2">Volver</a>
    <button onclick="window.print()" class="btn btn-success btn-sm">
        <i class="fa-solid fa-print me-1"></i>Imprimir Tiquete
    </button>
</div>

<div class="ticket">
    <div class="ticket-header">
        <h5 class="fw-bold mb-0">=== <?= APP_NAME ?> ===</h5>
        <div style="font-size: 0.75rem;">TRANSPORTE FLUVIAL DE PASAJEROS</div>
        <div style="font-size: 0.75rem;">NIT: 900.123.456-7</div>
        <div class="fw-bold mt-1" style="font-size: 1rem;"><?= htmlspecialchars($boleto['codigo_boleto']) ?></div>
    </div>

    <div class="ticket-body">
        <div class="ticket-row">
            <span>Fecha Emisión:</span>
            <span><?= substr($boleto['created_at'], 0, 16) ?></span>
        </div>
        <div class="ticket-row">
            <span>Viaje:</span>
            <strong><?= htmlspecialchars($boleto['codigo_viaje']) ?></strong>
        </div>
        <div class="ticket-row">
            <span>Embarcación:</span>
            <span><?= htmlspecialchars($boleto['embarcacion_nombre']) ?></span>
        </div>
        <div class="ticket-row">
            <span>Matrícula:</span>
            <span><?= htmlspecialchars($boleto['embarcacion_matricula']) ?></span>
        </div>
        <hr style="border-top: 1px dashed #000; margin: 6px 0;">
        <div class="ticket-row">
            <span>Origen:</span>
            <strong><?= htmlspecialchars($boleto['origen_nombre']) ?></strong>
        </div>
        <div class="ticket-row">
            <span>Destino:</span>
            <strong><?= htmlspecialchars($boleto['destino_nombre']) ?></strong>
        </div>
        <div class="ticket-row">
            <span>Zarpe:</span>
            <strong><?= $boleto['fecha_salida'] ?> <?= substr($boleto['hora_salida'], 0, 5) ?></strong>
        </div>
        <hr style="border-top: 1px dashed #000; margin: 6px 0;">
        <div class="ticket-row">
            <span>Pasajero:</span>
            <strong><?= htmlspecialchars($boleto['pasajero_nombre']) ?></strong>
        </div>
        <div class="ticket-row">
            <span>Documento:</span>
            <span><?= htmlspecialchars($boleto['pasajero_documento']) ?></span>
        </div>
        <div class="ticket-row">
            <span>Asiento Asignado:</span>
            <strong style="font-size: 1rem;">#<?= $boleto['numero_asiento'] ?? '1' ?></strong>
        </div>
        <hr style="border-top: 1px dashed #000; margin: 6px 0;">
        <div class="ticket-row" style="font-size: 1rem; font-weight: bold;">
            <span>TOTAL PAGADO:</span>
            <span>$<?= number_format($boleto['precio_pagado'], 0, ',', '.') ?></span>
        </div>
        <div class="ticket-row">
            <span>Forma de Pago:</span>
            <span class="text-uppercase"><?= $boleto['metodo_pago'] ?></span>
        </div>
        <div class="ticket-row">
            <span>Atendido por:</span>
            <span><?= htmlspecialchars($boleto['vendedor_nombre'] ?? 'Taquilla') ?></span>
        </div>

        <?php 
        require_once __DIR__ . '/../../app/Helpers/QrCodeHelper.php';
        $payload = QrCodeHelper::buildPayload('BOL', $boleto['codigo_boleto'], $boleto['codigo_qr_token'] ?? $boleto['codigo_boleto']);
        ?>
        <div class="text-center my-2 pt-2 border-top">
            <?= QrCodeHelper::renderSvg($payload, 125) ?>
            <div style="font-size: 0.65rem; font-weight: bold; margin-top: 4px; letter-spacing: 0.5px;">CÓDIGO QR DE ABORDAJE</div>
        </div>
    </div>

    <div class="ticket-footer">
        <div>¡Buen viaje y gracias por su confianza!</div>
        <div style="font-size: 0.7rem; margin-top: 4px;">Presentar este tiquete al momento de abordar en el muelle. Uso obligatorio de chaleco salvavidas.</div>
    </div>
</div>

</body>
</html>
