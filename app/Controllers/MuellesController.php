<?php
// =======================================================
// Controlador: MuellesController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';

class MuellesController extends Controller {
    private Muelle $muelleModel;

    public function __construct() {
        AuthHelper::requireStaff();
        $this->muelleModel = new Muelle();
    }

    public function index(): void {
        $muelles = $this->muelleModel->all('nombre ASC');
        $this->render('muelles/index', [
            'pageTitle' => 'Muelles y Puertos Fluviales - ' . APP_NAME,
            'muelles'   => $muelles
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/muelles');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $rio = trim($_POST['rio'] ?? '');
        $municipio = trim($_POST['municipio'] ?? '');
        $departamento = trim($_POST['departamento'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        if (empty($nombre) || empty($rio) || empty($municipio)) {
            SessionHelper::setFlash('danger', 'Complete todos los campos obligatorios del muelle.');
            $this->redirect('/muelles');
        }

        $this->muelleModel->create([
            'nombre'       => $nombre,
            'rio'          => $rio,
            'municipio'    => $municipio,
            'departamento' => $departamento,
            'estado'       => $estado
        ]);

        SessionHelper::setFlash('success', 'Muelle / Puerto registrado exitosamente.');
        $this->redirect('/muelles');
    }

    public function delete(): void {
        AuthHelper::requireRoles(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->muelleModel->delete($id);
            SessionHelper::setFlash('success', 'Muelle eliminado correctamente.');
        }
        $this->redirect('/muelles');
    }
}
