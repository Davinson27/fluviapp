<?php
// =======================================================
// Servicio: PaymentService - Pasarela de Pagos Wompi
// FluviApp v2.0 (Soporte Sandbox y Producción para Colombia)
// =======================================================

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Helpers/QrCodeHelper.php';

class PaymentService {
    private string $mode;
    private string $publicKey;
    private string $privateKey;
    private string $integritySecret;
    private string $eventsSecret;
    private PDO $db;

    public function __construct() {
        $this->mode = defined('WOMPI_MODE') ? WOMPI_MODE : 'sandbox';
        $this->publicKey = defined('WOMPI_PUBLIC_KEY') ? WOMPI_PUBLIC_KEY : '';
        $this->privateKey = defined('WOMPI_PRIVATE_KEY') ? WOMPI_PRIVATE_KEY : '';
        $this->integritySecret = defined('WOMPI_INTEGRITY_SECRET') ? WOMPI_INTEGRITY_SECRET : '';
        $this->eventsSecret = defined('WOMPI_EVENTS_SECRET') ? WOMPI_EVENTS_SECRET : '';
        $this->db = Database::getConnection();
    }

    public function getPublicKey(): string {
        return $this->publicKey;
    }

    public function getMode(): string {
        return $this->mode;
    }

    /**
     * Calcula la firma de integridad SHA-256 requerida por el Widget de Wompi
     * Fórmula: sha256(referencia + montoEnCentavos + moneda + integritySecret)
     */
    public function calcularFirmaIntegridad(string $referencia, int $montoEnCentavos, string $moneda = 'COP'): string {
        $cadena = $referencia . $montoEnCentavos . $moneda . $this->integritySecret;
        return hash('sha256', $cadena);
    }

    /**
     * Genera una referencia única de pago para FluviApp
     */
    public static function generarReferencia(string $tipo = 'BOL'): string {
        return 'FLV-' . $tipo . '-' . date('YmdHis') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    /**
     * Registra una transacción pendiente en la base de datos
     */
    public function crearTransaccion(
        string $referencia,
        float $monto,
        ?string $clienteNombre = null,
        ?string $clienteEmail = null,
        ?string $clienteTelefono = null,
        array $datosExtra = []
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO pagos_transacciones 
                (referencia_pago, pasarela, metodo_pago, monto, moneda, estado, cliente_nombre, cliente_email, cliente_telefono, datos_transaccion)
            VALUES 
                (:ref, 'wompi', 'wompi_checkout', :monto, 'COP', 'pendiente', :nombre, :email, :tel, :datos)
        ");
        $stmt->execute([
            ':ref'    => $referencia,
            ':monto'  => $monto,
            ':nombre' => $clienteNombre,
            ':email'  => $clienteEmail,
            ':tel'    => $clienteTelefono,
            ':datos'  => json_encode($datosExtra, JSON_UNESCAPED_UNICODE)
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Obtiene una transacción por su referencia
     */
    public function obtenerPorReferencia(string $referencia): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pagos_transacciones WHERE referencia_pago = :ref LIMIT 1");
        $stmt->execute([':ref' => $referencia]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getApiUrl(): string {
        return ($this->mode === 'production')
            ? 'https://production.wompi.co/v1'
            : 'https://sandbox.wompi.co/v1';
    }

    /**
     * Consulta el estado de una transacción directamente en la API REST de Wompi
     */
    public function consultarTransaccionWompi(string $transaccionId): ?array {
        if (empty($transaccionId)) {
            return null;
        }

        $url = $this->getApiUrl() . '/transactions/' . urlencode($transaccionId);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->publicKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $json = json_decode($response, true);
            return $json['data'] ?? null;
        }

        return null;
    }

    /**
     * Sincroniza y procesa una transacción de Wompi (aprobada, rechazada, etc.)
     */
    public function sincronizarTransaccionWompi(array $wompiTx, ?array $rawPayload = null): bool {
        $referencia = $wompiTx['reference'] ?? '';
        $estadoWompi = strtoupper($wompiTx['status'] ?? '');
        $transaccionIdExterno = $wompiTx['id'] ?? '';
        $metodoPago = strtolower($wompiTx['payment_method_type'] ?? 'wompi');

        $estado = match ($estadoWompi) {
            'APPROVED' => 'aprobado',
            'DECLINED' => 'rechazado',
            'VOIDED', 'ERROR' => 'anulado',
            default => 'pendiente'
        };

        $txLocal = $this->obtenerPorReferencia($referencia);
        if (!$txLocal) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE pagos_transacciones 
            SET estado = :estado, 
                transaccion_id_externo = :ext_id, 
                metodo_pago = :metodo, 
                datos_transaccion = :datos 
            WHERE id = :id
        ");
        $stmt->execute([
            ':estado' => $estado,
            ':ext_id' => $transaccionIdExterno,
            ':metodo' => $metodoPago,
            ':datos'  => json_encode($rawPayload ?? ['wompi_api' => $wompiTx], JSON_UNESCAPED_UNICODE),
            ':id'     => $txLocal['id']
        ]);

        if ($estado === 'aprobado' && $txLocal['estado'] !== 'aprobado') {
            $this->finalizarCompraAprobada($txLocal, $wompiTx);
        }

        return true;
    }

    /**
     * Valida el evento Webhook de Wompi y procesa el resultado
     */
    public function procesarWebhook(array $payload): array {
        $event = $payload['event'] ?? '';
        if ($event !== 'transaction.updated') {
            return ['success' => true, 'message' => 'Evento ignorado'];
        }

        $tx = $payload['data']['transaction'] ?? null;
        if (!$tx) {
            return ['success' => false, 'error' => 'Payload sin datos de transacción'];
        }

        $ok = $this->sincronizarTransaccionWompi($tx, $payload);
        if (!$ok) {
            return ['success' => false, 'error' => "Transacción {$tx['reference']} no encontrada en FluviApp"];
        }

        return ['success' => true, 'estado' => $tx['status'] ?? 'OK'];
    }

    /**
     * Simula la aprobación inmediata de una transacción para entornos locales de prueba
     */
    public function simularAprobacionSandbox(string $referencia): bool {
        $txLocal = $this->obtenerPorReferencia($referencia);
        if (!$txLocal || $txLocal['estado'] === 'aprobado') {
            return false;
        }

        $txSimulada = [
            'id'                  => 'SIM-WOMPI-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
            'status'              => 'APPROVED',
            'reference'           => $referencia,
            'amount_in_cents'     => (int)($txLocal['monto'] * 100),
            'currency'            => 'COP',
            'payment_method_type' => 'NEQUI'
        ];

        $stmt = $this->db->prepare("
            UPDATE pagos_transacciones 
            SET estado = 'aprobado', 
                transaccion_id_externo = :ext_id, 
                metodo_pago = 'nequi_sandbox',
                datos_transaccion = :datos 
            WHERE id = :id
        ");
        $stmt->execute([
            ':ext_id' => $txSimulada['id'],
            ':datos'  => json_encode(['simulated' => true, 'tx' => $txSimulada]),
            ':id'     => $txLocal['id']
        ]);

        $this->finalizarCompraAprobada($txLocal, $txSimulada);
        return true;
    }

    /**
     * Ejecuta la emisión formal del boleto o confirmación de encomienda tras pago aprobado
     */
    private function finalizarCompraAprobada(array $txLocal, array $wompiTx): void {
        $datosExtra = !empty($txLocal['datos_transaccion']) ? json_decode($txLocal['datos_transaccion'], true) : [];
        $tipo = $datosExtra['tipo'] ?? 'boleto';

        if ($tipo === 'boleto') {
            $viajeId = (int)($datosExtra['viaje_id'] ?? 0);
            $usuarioId = !empty($datosExtra['usuario_id']) ? (int)$datosExtra['usuario_id'] : null;
            $pasajeroDoc = $datosExtra['pasajero_documento'] ?? 'N/A';
            $pasajeroNombre = $datosExtra['pasajero_nombre'] ?? ($txLocal['cliente_nombre'] ?? 'Pasajero');
            $pasajeroTel = $datosExtra['pasajero_telefono'] ?? ($txLocal['cliente_telefono'] ?? '');
            $numeroAsiento = !empty($datosExtra['numero_asiento']) ? (int)$datosExtra['numero_asiento'] : null;
            $precio = (float)$txLocal['monto'];

            // Generar código de boleto oficial
            $codigoBoleto = 'BOL-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
            $tokenQr = QrCodeHelper::generateToken('FLV-BOL', $viajeId, $codigoBoleto);

            // Insertar boleto
            $stmt = $this->db->prepare("
                INSERT INTO boletos 
                    (viaje_id, usuario_id, codigo_boleto, codigo_qr_token, pasajero_documento, pasajero_nombre, pasajero_telefono, numero_asiento, precio_pagado, metodo_pago, estado, transaccion_id)
                VALUES 
                    (:viaje, :user, :cod, :token, :doc, :nombre, :tel, :asiento, :precio, 'tarjeta', 'valido', :tx_id)
            ");
            $stmt->execute([
                ':viaje'   => $viajeId,
                ':user'    => $usuarioId,
                ':cod'     => $codigoBoleto,
                ':token'   => $tokenQr,
                ':doc'     => $pasajeroDoc,
                ':nombre'  => $pasajeroNombre,
                ':tel'     => $pasajeroTel,
                ':asiento' => $numeroAsiento,
                ':precio'  => $precio,
                ':tx_id'   => $txLocal['id']
            ]);

            // Descontar cupo en el viaje
            $stmtCupos = $this->db->prepare("UPDATE viajes SET cupos_disponibles = GREATEST(0, cupos_disponibles - 1) WHERE id = :viaje");
            $stmtCupos->execute([':viaje' => $viajeId]);

            // Crear notificación interna si el usuario está registrado
            if ($usuarioId) {
                require_once __DIR__ . '/../Models/Notificacion.php';
                $notifModel = new Notificacion();
                $notifModel->crear(
                    $usuarioId,
                    "¡Pago de Pasaje Confirmado! (#{$codigoBoleto})",
                    "Tu pago por \${$precio} COP mediante Wompi fue aprobado. Tu boleto y código QR de abordaje ya están disponibles en 'Mis Boletos'.",
                    'boleto',
                    BASE_URL . '/cliente/mis-boletos'
                );
            }
        }
    }
}
