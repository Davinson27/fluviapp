<?php
// =======================================================
// Controlador: EmbarcacionesController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Embarcacion.php';

class EmbarcacionesController extends Controller {
    private Embarcacion $embarcacionModel;

    public function __construct() {
        AuthHelper::requireAuth();
        $this->embarcacionModel = new Embarcacion();
    }

    public function index(): void {
        $embarcaciones = $this->embarcacionModel->all('id DESC');
        $this->render('embarcaciones/index', [
            'pageTitle'     => 'Flota Fluvial - ' . APP_NAME,
            'embarcaciones' => $embarcaciones
        ]);
    }

    public function create(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        $this->render('embarcaciones/create', [
            'pageTitle' => 'Nueva Embarcación - ' . APP_NAME
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/embarcaciones');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        $tipo = $_POST['tipo'] ?? 'lancha_rapida';
        $capPasajeros = (int)($_POST['capacidad_pasajeros'] ?? 0);
        $capCarga = (float)($_POST['capacidad_carga_kg'] ?? 0);
        $estado = $_POST['estado'] ?? 'operativo';

        if (empty($nombre) || empty($matricula)) {
            SessionHelper::setFlash('danger', 'El nombre y la matrícula son obligatorios.');
            $this->redirect('/embarcaciones/crear');
        }

        try {
            $this->embarcacionModel->create([
                'nombre'              => $nombre,
                'matricula'           => $matricula,
                'tipo'                => $tipo,
                'capacidad_pasajeros' => $capPasajeros,
                'capacidad_carga_kg'  => $capCarga,
                'estado'              => $estado
            ]);
            SessionHelper::setFlash('success', 'Embarcación registrada exitosamente.');
            $this->redirect('/embarcaciones');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al guardar la embarcación: ' . $e->getMessage());
            $this->redirect('/embarcaciones/crear');
        }
    }

    public function delete(): void {
        AuthHelper::requireRoles(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->embarcacionModel->delete($id);
            SessionHelper::setFlash('success', 'Embarcación eliminada correctamente.');
        }
        $this->redirect('/embarcaciones');
    }
}
