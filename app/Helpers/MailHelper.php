<?php
// =======================================================
// Helper: MailHelper (Envío de Notificaciones por Correo)
// =======================================================

class MailHelper {
    /**
     * Enviar correo electrónico HTML al remitente cuando su encomienda ha sido entregada
     */
    public static function enviarEntregaEncomienda(array $carga, string $destinatarioEmail): bool {
        if (empty($destinatarioEmail) || !filter_var($destinatarioEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $guiaNumero = htmlspecialchars($carga['guia_numero'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $remitenteNombre = htmlspecialchars($carga['remitente_nombre'] ?? 'Apreciado Usuario', ENT_QUOTES, 'UTF-8');
        $destinatarioNombre = htmlspecialchars($carga['destinatario_nombre'] ?? 'Destinatario', ENT_QUOTES, 'UTF-8');
        $descripcionCarga = htmlspecialchars($carga['descripcion_carga'] ?? 'Carga general', ENT_QUOTES, 'UTF-8');
        $origen = htmlspecialchars($carga['origen_nombre'] ?? 'Muelle Origen', ENT_QUOTES, 'UTF-8');
        $destino = htmlspecialchars($carga['destino_nombre'] ?? 'Muelle Destino', ENT_QUOTES, 'UTF-8');
        $embarcacion = htmlspecialchars($carga['embarcacion_nombre'] ?? 'Embarcación Fluvial', ENT_QUOTES, 'UTF-8');
        $fechaEntrega = date('d/m/Y h:i A');
        $soporteUrl = BASE_URL . '/cliente/mis-encomiendas';
        $appName = APP_NAME;

        $subject = "✅ ¡Tu Encomienda Guía {$guiaNumero} ha sido Entregada! - {$appName}";

        $message = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 20px; color: #1e293b; }
                .email-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
                .email-header { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; padding: 30px 25px; text-align: center; }
                .email-header h1 { margin: 0; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
                .email-header p { margin: 8px 0 0 0; opacity: 0.9; font-size: 14px; }
                .badge-success { display: inline-block; background-color: #10b981; color: white; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; margin-top: 15px; }
                .email-body { padding: 30px 25px; }
                .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
                .info-table th { text-align: left; padding: 10px 12px; background-color: #f8fafc; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0; width: 38%; }
                .info-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
                .support-box { background-color: #eff6ff; border-left: 4px solid #0284c7; padding: 15px; border-radius: 6px; margin-top: 25px; font-size: 13px; color: #1e3a8a; }
                .btn { display: inline-block; background-color: #0284c7; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 14px; text-align: center; margin: 20px 0 10px 0; }
                .email-footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <div class='email-card'>
                <div class='email-header'>
                    <h1>🚢 {$appName} Fluvial</h1>
                    <p>Confirmación de Llegada y Entrega de Carga</p>
                    <div class='badge-success'>✓ ESTADO: ENTREGADO</div>
                </div>
                <div class='email-body'>
                    <p>Hola <strong>{$remitenteNombre}</strong>,</p>
                    <p>Nos complace informarte que tu encomienda fluvial con número de guía <strong>{$guiaNumero}</strong> ha sido <strong>entregada exitosamente</strong> en su muelle de destino.</p>
                    
                    <table class='info-table'>
                        <tr>
                            <th>Número de Guía:</th>
                            <td><strong style='color:#0284c7;'>{$guiaNumero}</strong></td>
                        </tr>
                        <tr>
                            <th>Contenido / Descripción:</th>
                            <td>{$descripcionCarga}</td>
                        </tr>
                        <tr>
                            <th>Ruta Fluvial:</th>
                            <td>{$origen} &rarr; <strong>{$destino}</strong></td>
                        </tr>
                        <tr>
                            <th>Embarcación:</th>
                            <td>{$embarcacion}</td>
                        </tr>
                        <tr>
                            <th>Entregado a:</th>
                            <td>{$destinatarioNombre}</td>
                        </tr>
                        <tr>
                            <th>Fecha y Hora Entrega:</th>
                            <td>{$fechaEntrega}</td>
                        </tr>
                    </table>

                    <div style='text-align: center;'>
                        <a href='{$soporteUrl}' class='btn'>Ver Mis Encomiendas y Factura</a>
                    </div>

                    <div class='support-box'>
                        <strong>¿Tu paquete no llegó o tienes alguna observación?</strong><br>
                        Si presentas cualquier novedad con la entrega, comunícate inmediatamente con nuestro Centro de Soporte Fluvial desde el portal de encomiendas o a nuestra línea de atención directa.
                    </div>
                </div>
                <div class='email-footer'>
                    <p>{$appName} &bull; Red de Transporte y Logística Fluvial de Colombia<br>
                    Este es un correo automático generado por el sistema.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $appName . ' Fluvial <notificaciones@fluviapp.local>',
            'Reply-To: soporte@fluviapp.local',
            'X-Mailer: PHP/' . phpversion()
        ];

        // Registrar en log siempre
        $logDir = ROOT_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logEntry = "[" . date('Y-m-d H:i:s') . "] ENTREGA ENCOMIENDA: Guia {$guiaNumero} -> Correo: {$destinatarioEmail} | Estado: ";

        $sent = false;
        try {
            $sent = @mail($destinatarioEmail, $subject, $message, implode("\r\n", $headers));
        } catch (Throwable $t) {
            $sent = false;
        }

        $logEntry .= ($sent ? "ENVIADO_SMTP" : "SIMULADO_LOG_LOCAL") . "\n";
        @file_put_contents($logDir . '/notificaciones_entrega.log', $logEntry, FILE_APPEND);

        return true;
    }
}