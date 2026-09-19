<?php
// =======================================================
// Controlador: CheckinController - FluviApp v2.0
// Validación QR de Abordaje en Muelle y Entrega de Carga
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Helpers/SessionHelper.php';
require_once __DIR__ . '/../Models/Boleto.php';
require_once __DIR__ . '/../Models/Carga.php';
require_once __DIR__ . '/../Models/Viaje.php';

class CheckinController extends Controller {
    private Boleto $boletoModel;
    private Carga $cargaModel;
    private Viaje $viajeModel;

    public function __construct() {
        $this->boletoModel = new Boleto();
        $this->cargaModel = new Carga();
        $this->viajeModel = new Viaje();
    }

    /**
     * Vista del escáner de abordaje QR para muelle
     */
    public function checkin(): void {
        AuthHelper::requireRole(['operador', 'taquilla', 'capitan', 'admin']);
        $user = AuthHelper::user();

        // Obtener viajes activos de hoy o en estado de embarque/programado
        $stmt = Database::getConnection()->prepare("
            SELECT v.id, v.codigo_viaje, v.fecha_salida, v.hora_salida, v.estado,
                   m1.nombre as origen_nombre, m2.nombre as destino_nombre,
                   e.nombre as embarcacion_nombre, e.capacidad_pasajeros,
                   (SELECT COUNT(*) FROM boletos b WHERE b.viaje_id = v.id AND b.estado = 'usado') as abordados,
                   (SELECT COUNT(*) FROM boletos b WHERE b.viaje_id = v.id AND b.estado != 'cancelado') as total_vendidos
            FROM viajes v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles m1 ON r.muelle_origen_id = m1.id
            JOIN muelles m2 ON r.muelle_destino_id = m2.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE v.estado IN ('programado', 'en_embarque', 'en_navegacion')
            ORDER BY v.fecha_salida ASC, v.hora_salida ASC
        ");
        $stmt->execute();
        $viajesActivos = $stmt->fetchAll();

        $this->render('operaciones/checkin', [
            'pageTitle'     => 'Validación de Abordaje (Check-in QR) - ' . APP_NAME,
            'user'          => $user,
            'viajesActivos' => $viajesActivos
        ]);
    }

    /**
     * API AJAX para validar código QR escaneado de un boleto
     */
    public function validarBoleto(): void {
        AuthHelper::requireRole(['operador', 'taquilla', 'capitan', 'admin']);
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true);
        $qrRaw = trim($input['qr_data'] ?? ($_POST['qr_data'] ?? ''));

        if (empty($qrRaw)) {
            echo json_encode(['success' => false, 'error' => 'Código QR vacío o no legible.']);
            exit;
        }

        // Parsear formato FluviApp: FLV|BOL|CODIGO|TOKEN_CORTO o buscar por código o token
        $codigoBoleto = '';
        $token = '';

        if (str_starts_with($qrRaw, 'FLV|BOL|')) {
            $partes = explode('|', $qrRaw);
            $codigoBoleto = $partes[2] ?? '';
            $token = $partes[3] ?? '';
        } else {
            $codigoBoleto = $qrRaw;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT b.*, v.codigo_viaje, v.fecha_salida, v.hora_salida, v.estado as viaje_estado,
                   m1.nombre as origen_nombre, m2.nombre as destino_nombre,
                   e.nombre as embarcacion_nombre, e.capacidad_pasajeros
            FROM boletos b
            JOIN viajes v ON b.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles m1 ON r.muelle_origen_id = m1.id
            JOIN muelles m2 ON r.muelle_destino_id = m2.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE b.codigo_boleto = :cod OR b.codigo_qr_token = :token OR b.codigo_qr_token LIKE :token_like
            LIMIT 1
        ");
        $stmt->execute([
            ':cod'        => $codigoBoleto,
            ':token'      => $token ?: $codigoBoleto,
            ':token_like' => ($token ? $token . '%' : '___NO_MATCH___')
        ]);
        $boleto = $stmt->fetch();

        if (!$boleto) {
            echo json_encode([
                'success' => false,
                'status'  => 'invalido',
                'error'   => "Boleto no registrado en el sistema: {$qrRaw}"
            ]);
            exit;
        }

        // Validar si ya fue usado
        if ($boleto['estado'] === 'usado') {
            echo json_encode([
                'success'       => false,
                'status'        => 'ya_usado',
                'error'         => "¡ATENCIÓN! Este boleto YA FUE USADO el {$boleto['fecha_embarque']}.",
                'pasajero'      => $boleto['pasajero_nombre'],
                'asiento'       => $boleto['numero_asiento'],
                'codigo_boleto' => $boleto['codigo_boleto']
            ]);
            exit;
        }

        // Validar si está cancelado
        if ($boleto['estado'] === 'cancelado') {
            echo json_encode([
                'success'       => false,
                'status'        => 'cancelado',
                'error'         => 'Este boleto se encuentra CANCELADO o ANULADO.',
                'codigo_boleto' => $boleto['codigo_boleto']
            ]);
            exit;
        }

        // Marcar como usado y registrar fecha de embarque
        $stmtUpdate = $db->prepare("
            UPDATE boletos 
            SET estado = 'usado', fecha_embarque = NOW() 
            WHERE id = :id
        ");
        $stmtUpdate->execute([':id' => $boleto['id']]);

        // Si el viaje estaba en 'programado', pasarlo automáticamente a 'en_embarque'
        if ($boleto['viaje_estado'] === 'programado') {
            $db->prepare("UPDATE viajes SET estado = 'en_embarque' WHERE id = :viaje_id")
               ->execute([':viaje_id' => $boleto['viaje_id']]);
        }

        // Contar abordados actualizados para este viaje
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM boletos WHERE viaje_id = :viaje_id AND estado = 'usado'");
        $stmtCount->execute([':viaje_id' => $boleto['viaje_id']]);
        $abordados = (int)$stmtCount->fetchColumn();

        echo json_encode([
            'success'            => true,
            'status'             => 'aprobado',
            'message'            => '¡Abordaje Autorizado!',
            'pasajero_nombre'    => $boleto['pasajero_nombre'],
            'pasajero_documento' => $boleto['pasajero_documento'],
            'numero_asiento'     => $boleto['numero_asiento'] ?? 'General',
            'codigo_boleto'      => $boleto['codigo_boleto'],
            'origen_nombre'      => $boleto['origen_nombre'],
            'destino_nombre'     => $boleto['destino_nombre'],
            'embarcacion_nombre' => $boleto['embarcacion_nombre'],
            'hora_salida'        => substr($boleto['hora_salida'], 0, 5),
            'abordados'          => $abordados,
            'capacidad'          => (int)$boleto['capacidad_pasajeros'],
            'timestamp'          => date('H:i:s')
        ]);
        exit;
    }

    /**
     * Vista de entrega de encomiendas con firma táctil y fotografía
     */
    public function entregaCarga(): void {
        AuthHelper::requireRole(['operador', 'taquilla', 'capitan', 'admin']);
        $user = AuthHelper::user();

        $stmt = Database::getConnection()->prepare("
            SELECT c.*, v.codigo_viaje, m1.nombre as origen_nombre, m2.nombre as destino_nombre
            FROM cargas_encomiendas c
            JOIN viajes v ON c.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles m1 ON r.muelle_origen_id = m1.id
            JOIN muelles m2 ON r.muelle_destino_id = m2.id
            WHERE c.estado IN ('cargada', 'en_transito', 'registrada')
            ORDER BY c.id DESC
        ");
        $stmt->execute();
        $cargasPendientes = $stmt->fetchAll();

        $this->render('operaciones/entrega_carga', [
            'pageTitle'        => 'Entrega Digital de Encomiendas - ' . APP_NAME,
            'user'             => $user,
            'cargasPendientes' => $cargasPendientes
        ]);
    }

    /**
     * API AJAX para confirmar entrega de carga con firma táctil y foto
     */
    public function confirmarEntregaCarga(): void {
        AuthHelper::requireRole(['operador', 'taquilla', 'capitan', 'admin']);
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $guiaNumero = trim($input['guia_numero'] ?? '');
        $firmaBase64 = $input['firma_base64'] ?? '';
        $fotoBase64 = $input['foto_base64'] ?? '';

        if (empty($guiaNumero)) {
            echo json_encode(['success' => false, 'error' => 'Debe indicar el número de guía.']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM cargas_encomiendas WHERE guia_numero = :guia LIMIT 1");
        $stmt->execute([':guia' => $guiaNumero]);
        $carga = $stmt->fetch();

        if (!$carga) {
            echo json_encode(['success' => false, 'error' => 'Guía de encomienda no encontrada.']);
            exit;
        }

        if ($carga['estado'] === 'entregada') {
            echo json_encode(['success' => false, 'error' => "Esta encomienda ya fue entregada el {$carga['fecha_entrega']}."]);
            exit;
        }

        // Actualizar carga con firma, foto y estado entregada
        $stmtUpdate = $db->prepare("
            UPDATE cargas_encomiendas 
            SET estado = 'entregada', 
                firma_entrega_url = :firma, 
                foto_evidencia_url = :foto, 
                fecha_entrega = NOW() 
            WHERE id = :id
        ");
        $stmtUpdate->execute([
            ':firma' => $firmaBase64 ?: null,
            ':foto'  => $fotoBase64 ?: null,
            ':id'    => $carga['id']
        ]);

        // Notificar al usuario remitente si tiene cuenta
        if (!empty($carga['usuario_id'])) {
            require_once __DIR__ . '/../Models/Notificacion.php';
            (new Notificacion())->crear(
                (int)$carga['usuario_id'],
                "Encomienda Entregada con Éxito (#{$guiaNumero})",
                "Tu encomienda dirigida a {$carga['destinatario_nombre']} ha sido entregada en muelle de destino con firma de soporte.",
                'encomienda',
                BASE_URL . '/cliente/mis-encomiendas'
            );
        }

        echo json_encode([
            'success'       => true,
            'message'       => '¡Encomienda entregada y confirmada digitalmente con éxito!',
            'destinatario'  => $carga['destinatario_nombre'],
            'guia_numero'   => $guiaNumero,
            'fecha_entrega' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}
