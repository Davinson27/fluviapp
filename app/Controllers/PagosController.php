<?php
// =======================================================
// Controlador: PagosController - FluviApp v2.0
// Gestión de Checkout Wompi, Webhooks y Resultados
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Helpers/SessionHelper.php';
require_once __DIR__ . '/../Services/PaymentService.php';
require_once __DIR__ . '/../Models/Viaje.php';
require_once __DIR__ . '/../Models/Boleto.php';

class PagosController extends Controller {
    private PaymentService $paymentService;
    private Viaje $viajeModel;
    private Boleto $boletoModel;

    public function __construct() {
        $this->paymentService = new PaymentService();
        $this->viajeModel = new Viaje();
        $this->boletoModel = new Boleto();
    }

    /**
     * Inicia el proceso de pago digital con Wompi
     */
    public function iniciarPago(): void {
        AuthHelper::requireRole(['cliente', 'admin', 'taquilla']);
        $user = AuthHelper::user();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/portal');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $viaje = $this->viajeModel->findWithDetails($viajeId);

        if (!$viaje || (int)$viaje['cupos_disponibles'] <= 0) {
            SessionHelper::flash('error', 'El viaje seleccionado no tiene cupos disponibles o no existe.');
            $this->redirect('/portal');
        }

        $userDepto = $user['departamento'] ?? '';
        if (!empty($userDepto)) {
            $userDeptoNorm = $this->normalizarTexto($userDepto);
            $origenNorm = $this->normalizarTexto($viaje['origen_depto'] ?? $viaje['origen_departamento'] ?? '');
            $destinoNorm = $this->normalizarTexto($viaje['destino_depto'] ?? $viaje['destino_departamento'] ?? '');

            if ($origenNorm !== $userDeptoNorm && $destinoNorm !== $userDeptoNorm) {
                SessionHelper::flash('error', "No puede comprar pasajes para rutas fuera de su departamento ({$userDepto}).");
                $this->redirect('/portal');
            }
        }

        $pasajeroDoc = trim($_POST['pasajero_documento'] ?? ($user['documento'] ?? ''));
        $pasajeroNombre = trim($_POST['pasajero_nombre'] ?? ($user['nombre'] ?? ''));
        $pasajeroTel = trim($_POST['pasajero_telefono'] ?? ($user['telefono'] ?? ''));
        $numeroAsiento = !empty($_POST['numero_asiento']) ? (int)$_POST['numero_asiento'] : null;

        if (empty($pasajeroDoc) || empty($pasajeroNombre)) {
            SessionHelper::flash('error', 'Debe indicar el nombre y documento del pasajero.');
            $this->redirect('/cliente/comprar?viaje_id=' . $viajeId);
        }

        // Verificar si el asiento ya está ocupado en este viaje
        if ($numeroAsiento !== null) {
            $stmt = Database::getConnection()->prepare("
                SELECT id FROM boletos 
                WHERE viaje_id = :viaje AND numero_asiento = :asiento AND estado != 'cancelado' 
                LIMIT 1
            ");
            $stmt->execute([':viaje' => $viajeId, ':asiento' => $numeroAsiento]);
            if ($stmt->fetch()) {
                SessionHelper::flash('error', "El asiento #{$numeroAsiento} ya fue seleccionado por otro pasajero. Por favor seleccione otro.");
                $this->redirect('/cliente/comprar?viaje_id=' . $viajeId);
            }
        }

        $monto = (float)$viaje['precio_pasaje'];
        $montoCentavos = (int)($monto * 100);
        $referencia = PaymentService::generarReferencia('BOL');

        // Datos para registrar la transacción pendiente
        $datosExtra = [
            'tipo'               => 'boleto',
            'viaje_id'           => $viajeId,
            'usuario_id'         => $user['id'],
            'pasajero_documento' => $pasajeroDoc,
            'pasajero_nombre'    => $pasajeroNombre,
            'pasajero_telefono'  => $pasajeroTel,
            'numero_asiento'     => $numeroAsiento
        ];

        $this->paymentService->crearTransaccion(
            $referencia,
            $monto,
            $pasajeroNombre,
            $user['email'] ?? null,
            $pasajeroTel,
            $datosExtra
        );

        $firmaIntegridad = $this->paymentService->calcularFirmaIntegridad($referencia, $montoCentavos, 'COP');
        $redirectUrl = BASE_URL . '/cliente/pago-resultado?ref=' . urlencode($referencia);

        // Renderizar vista de checkout Wompi
        $this->render('cliente/checkout_wompi', [
            'user'             => $user,
            'viaje'            => $viaje,
            'monto'            => $monto,
            'montoCentavos'    => $montoCentavos,
            'referencia'       => $referencia,
            'publicKey'        => $this->paymentService->getPublicKey(),
            'firmaIntegridad'  => $firmaIntegridad,
            'redirectUrl'      => $redirectUrl,
            'pasajeroNombre'   => $pasajeroNombre,
            'pasajeroDoc'      => $pasajeroDoc,
            'numeroAsiento'    => $numeroAsiento,
            'modoWompi'        => $this->paymentService->getMode()
        ]);
    }

    /**
     * Endpoint Webhook de Wompi (recibe confirmaciones en segundo plano)
     */
    public function webhook(): void {
        header('Content-Type: application/json; charset=utf-8');
        
        $input = file_get_contents('php://input');
        $payload = json_decode($input, true);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['error' => 'Payload JSON no válido']);
            exit;
        }

        $resultado = $this->paymentService->procesarWebhook($payload);
        if (!$resultado['success']) {
            http_response_code(422);
            echo json_encode($resultado);
            exit;
        }

        http_response_code(200);
        echo json_encode($resultado);
        exit;
    }

    /**
     * Simulación inmediata de aprobación de pago en modo Sandbox
     */
    public function simularAprobacion(): void {
        AuthHelper::requireRole(['cliente', 'admin', 'taquilla']);
        $referencia = trim($_GET['ref'] ?? '');

        if (empty($referencia)) {
            $this->redirect('/portal');
        }

        $ok = $this->paymentService->simularAprobacionSandbox($referencia);
        if ($ok) {
            SessionHelper::flash('success', '¡Pago simulado aprobado exitosamente en Sandbox!');
        } else {
            SessionHelper::flash('info', 'La transacción ya se encontraba procesada.');
        }

        $this->redirect('/cliente/pago-resultado?ref=' . urlencode($referencia));
    }

    /**
     * Pantalla de resultado final de pago
     */
    public function resultado(): void {
        AuthHelper::requireRole(['cliente', 'admin', 'taquilla']);
        $user = AuthHelper::user();
        $referencia = trim($_GET['ref'] ?? '');
        $wompiId = trim($_GET['id'] ?? '');

        // Si Wompi adjuntó el ID de transacción al retornar, consultar el estado real en la API de Wompi
        if (!empty($wompiId)) {
            $wompiTx = $this->paymentService->consultarTransaccionWompi($wompiId);
            if ($wompiTx) {
                if (empty($referencia) && !empty($wompiTx['reference'])) {
                    $referencia = $wompiTx['reference'];
                }
                $this->paymentService->sincronizarTransaccionWompi($wompiTx);
            }
        }

        if (empty($referencia)) {
            $this->redirect('/portal');
        }

        $tx = $this->paymentService->obtenerPorReferencia($referencia);
        if (!$tx) {
            SessionHelper::flash('error', 'Transacción no encontrada.');
            $this->redirect('/portal');
        }

        // Si sigue pendiente y tiene ID externo, reintentar consulta a la API de Wompi
        if ($tx['estado'] === 'pendiente' && !empty($tx['transaccion_id_externo'])) {
            $wompiTx = $this->paymentService->consultarTransaccionWompi($tx['transaccion_id_externo']);
            if ($wompiTx && ($wompiTx['status'] ?? '') !== 'PENDING') {
                $this->paymentService->sincronizarTransaccionWompi($wompiTx);
                $tx = $this->paymentService->obtenerPorReferencia($referencia);
            }
        }

        // Si fue aprobado, buscar el boleto generado
        $boleto = null;
        if ($tx['estado'] === 'aprobado') {
            $stmt = Database::getConnection()->prepare("
                SELECT b.*, v.codigo_viaje, v.fecha_salida, v.hora_salida,
                       r.muelle_origen_id, r.muelle_destino_id,
                       m1.nombre as origen_nombre, m2.nombre as destino_nombre,
                       e.nombre as embarcacion_nombre
                FROM boletos b
                JOIN viajes v ON b.viaje_id = v.id
                JOIN rutas r ON v.ruta_id = r.id
                JOIN muelles m1 ON r.muelle_origen_id = m1.id
                JOIN muelles m2 ON r.muelle_destino_id = m2.id
                JOIN embarcaciones e ON v.embarcacion_id = e.id
                WHERE b.transaccion_id = :tx_id
                LIMIT 1
            ");
            $stmt->execute([':tx_id' => $tx['id']]);
            $boleto = $stmt->fetch();
        }

        $this->render('cliente/pago_resultado', [
            'user'        => $user,
            'tx'          => $tx,
            'boleto'      => $boleto,
            'referencia'  => $referencia
        ]);
    }

    private function normalizarTexto(string $str): string {
        $str = mb_strtolower(trim($str), 'UTF-8');
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n'
        ];
        return strtr($str, $replacements);
    }
}
