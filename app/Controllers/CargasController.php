<?php
// =======================================================
// Controlador: CargasController (Encomiendas y Carga Fluvial)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Carga.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';

class CargasController extends Controller {
    private Carga $cargaModel;
    private Viaje $viajeModel;

    public function __construct() {
        AuthHelper::requireAuth();
        $this->cargaModel = new Carga();
        $this->viajeModel = new Viaje();
    }

    public function index(): void {
        $cargas = $this->cargaModel->allWithDetails();
        $this->render('cargas/index', [
            'pageTitle' => 'Control de Carga y Encomiendas - ' . APP_NAME,
            'cargas'    => $cargas
        ]);
    }

    public function create(): void {
        $viajesDisponibles = $this->viajeModel->allWithDetails('programado');

        $this->render('cargas/create', [
            'pageTitle'         => 'Registrar Guía de Carga / Encomienda - ' . APP_NAME,
            'viajesDisponibles' => $viajesDisponibles
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cargas');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $remitente = trim($_POST['remitente_nombre'] ?? '');
        $remTel = trim($_POST['remitente_telefono'] ?? '');
        $destinatario = trim($_POST['destinatario_nombre'] ?? '');
        $destTel = trim($_POST['destinatario_telefono'] ?? '');
        $desc = trim($_POST['descripcion_carga'] ?? '');
        $peso = (float)($_POST['peso_kg'] ?? 0);
        $valorDeclarado = (float)($_POST['valor_declarado'] ?? 0);
        $valorFlete = (float)($_POST['valor_flete'] ?? 0);
        $user = AuthHelper::user();

        if ($viajeId === 0 || empty($remitente) || empty($destinatario) || $peso <= 0) {
            SessionHelper::setFlash('danger', 'Complete los datos obligatorios de la encomienda y peso.');
            $this->redirect('/cargas/crear');
        }

        try {
            $cargaId = $this->cargaModel->registrar([
                'viaje_id'              => $viajeId,
                'remitente_nombre'      => $remitente,
                'remitente_telefono'    => $remTel,
                'destinatario_nombre'   => $destinatario,
                'destinatario_telefono' => $destTel,
                'descripcion_carga'     => $desc,
                'peso_kg'               => $peso,
                'valor_declarado'       => $valorDeclarado,
                'valor_flete'           => $valorFlete,
                'registrado_por_id'     => $user['id'] ?? null
            ]);

            SessionHelper::setFlash('success', 'Guía de carga fluvial registrada exitosamente.');
            $this->redirect('/cargas');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al registrar la carga: ' . $e->getMessage());
            $this->redirect('/cargas/crear');
        }
    }

    public function cambiarEstado(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cargas');
        }

        $id = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';

        $estadosValidos = ['registrada', 'cargada', 'en_transito', 'entregada', 'cancelada'];
        if ($id > 0 && in_array($estado, $estadosValidos)) {
            $this->cargaModel->updateEstado($id, $estado);
            SessionHelper::setFlash('success', 'Estado de la encomienda actualizado correctamente.');
        }

        $this->redirect('/cargas');
    }
}
