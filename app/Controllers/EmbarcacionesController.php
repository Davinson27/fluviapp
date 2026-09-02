<?php
// =======================================================
// Controlador: EmbarcacionesController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Embarcacion.php';

class EmbarcacionesController extends Controller {
    private Embarcacion $embarcacionModel;

    public function __construct() {
        AuthHelper::requireStaff();
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
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'Error al guardar la embarcación.'));
            $this->redirect('/embarcaciones/crear');
        }
    }

    public function edit(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        $id = (int)($_GET['id'] ?? 0);
        $embarcacion = $this->embarcacionModel->find($id);
        if (!$embarcacion) {
            SessionHelper::setFlash('danger', 'La embarcación no existe.');
            $this->redirect('/embarcaciones');
        }
        $this->render('embarcaciones/edit', [
            'pageTitle'    => 'Editar Embarcación - ' . APP_NAME,
            'embarcacion'  => $embarcacion
        ]);
    }

    public function update(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/embarcaciones');
        }

        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $matricula = trim($_POST['matricula'] ?? '');
        $tipos = ['lancha_rapida', 'ferry', 'bote_motor', 'barcaza_carga'];
        $tipo = in_array($_POST['tipo'] ?? '', $tipos, true) ? $_POST['tipo'] : 'lancha_rapida';
        $estados = ['operativo', 'mantenimiento', 'fuera_servicio'];
        $estado = in_array($_POST['estado'] ?? '', $estados, true) ? $_POST['estado'] : 'operativo';
        $capPasajeros = max(0, (int)($_POST['capacidad_pasajeros'] ?? 0));
        $capCarga = max(0, (float)($_POST['capacidad_carga_kg'] ?? 0));

        if ($id <= 0 || empty($nombre) || empty($matricula)) {
            SessionHelper::setFlash('danger', 'Datos incompletos para actualizar la embarcación.');
            $this->redirect('/embarcaciones');
        }

        try {
            $this->embarcacionModel->update($id, [
                'nombre'              => $nombre,
                'matricula'           => $matricula,
                'tipo'                => $tipo,
                'capacidad_pasajeros' => $capPasajeros,
                'capacidad_carga_kg'  => $capCarga,
                'estado'              => $estado
            ]);
            SessionHelper::setFlash('success', 'Embarcación actualizada correctamente.');
            $this->redirect('/embarcaciones');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', $this->userErrorMessage($e, 'Error al actualizar la embarcación.'));
            $this->redirect('/embarcaciones/editar?id=' . $id);
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
