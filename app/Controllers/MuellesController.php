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
        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $muelles = $this->muelleModel->getByDepartamento($deptScope);
        } else {
            $muelles = $this->muelleModel->all('nombre ASC');
        }
        $this->render('muelles/index', [
            'pageTitle' => 'Muelles y Puertos Fluviales - ' . APP_NAME,
            'muelles'   => $muelles,
            'deptScope' => $deptScope
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/muelles');
        }

        $deptScope = AuthHelper::getDepartmentFilter();

        $nombre = trim($_POST['nombre'] ?? '');
        $rio = trim($_POST['rio'] ?? '');
        $municipio = trim($_POST['municipio'] ?? '');
        $departamento = !empty($deptScope) ? $deptScope : trim($_POST['departamento'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';
        $descripcion = trim($_POST['descripcion'] ?? '');
        $latitud = !empty($_POST['latitud']) ? (float)$_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? (float)$_POST['longitud'] : null;

        if (empty($nombre) || empty($rio) || empty($municipio) || empty($departamento)) {
            SessionHelper::setFlash('danger', 'Complete todos los campos obligatorios del muelle (Nombre, Río, Municipio y Departamento).');
            $this->redirect('/muelles');
        }

        // Si no se proveyeron coordenadas manuales, auto-calcularlas según departamento y municipio
        if ($latitud === null || $longitud === null || $latitud == 0 || $longitud == 0) {
            $coords = $this->muelleModel->getCoordenadasAproximadas($departamento, $municipio);
            $latitud = $coords['latitud'];
            $longitud = $coords['longitud'];
        }

        $this->muelleModel->create([
            'nombre'       => $nombre,
            'rio'          => $rio,
            'municipio'    => $municipio,
            'departamento' => $departamento,
            'latitud'      => $latitud,
            'longitud'     => $longitud,
            'descripcion'  => !empty($descripcion) ? $descripcion : ("Muelle fluvial en {$municipio} ({$departamento}) sobre el {$rio}"),
            'estado'       => $estado
        ]);

        SessionHelper::setFlash('success', '¡Muelle / Puerto registrado exitosamente! Ha sido georreferenciado e integrado al mapa interactivo de Colombia.');
        $this->redirect('/muelles');
    }

    public function edit(): void {
        AuthHelper::requireRoles(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        $muelle = $this->muelleModel->find($id);
        if (!$muelle) {
            SessionHelper::setFlash('danger', 'El muelle solicitado no existe.');
            $this->redirect('/muelles');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope) && ($muelle['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para editar muelles fuera de su departamento (' . $deptScope . ').');
            $this->redirect('/muelles');
        }

        $this->render('muelles/edit', [
            'pageTitle' => 'Editar Muelle / Puerto - ' . APP_NAME,
            'muelle'    => $muelle,
            'deptScope' => $deptScope
        ]);
    }

    public function update(): void {
        AuthHelper::requireRoles(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/muelles');
        }

        $id = (int)($_POST['id'] ?? 0);
        $targetMuelle = $this->muelleModel->find($id);
        if (!$targetMuelle) {
            SessionHelper::setFlash('danger', 'El muelle solicitado no existe.');
            $this->redirect('/muelles');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope) && ($targetMuelle['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para actualizar muelles fuera de su departamento.');
            $this->redirect('/muelles');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $rio = trim($_POST['rio'] ?? '');
        $municipio = trim($_POST['municipio'] ?? '');
        $departamento = !empty($deptScope) ? $deptScope : trim($_POST['departamento'] ?? '');
        $estado = in_array($_POST['estado'] ?? '', ['activo', 'inactivo'], true) ? $_POST['estado'] : 'activo';
        $descripcion = trim($_POST['descripcion'] ?? '');
        $latitud = !empty($_POST['latitud']) ? (float)$_POST['latitud'] : null;
        $longitud = !empty($_POST['longitud']) ? (float)$_POST['longitud'] : null;

        if ($id <= 0 || empty($nombre) || empty($rio) || empty($municipio) || empty($departamento)) {
            SessionHelper::setFlash('danger', 'Complete todos los campos obligatorios del muelle (Nombre, Río, Municipio y Departamento).');
            $this->redirect($id > 0 ? ('/muelles/editar?id=' . $id) : '/muelles');
        }

        // Si no tiene coordenadas válidas, recalcular según ubicación
        if ($latitud === null || $longitud === null || $latitud == 0 || $longitud == 0) {
            $coords = $this->muelleModel->getCoordenadasAproximadas($departamento, $municipio);
            $latitud = $coords['latitud'];
            $longitud = $coords['longitud'];
        }

        $this->muelleModel->update($id, [
            'nombre'       => $nombre,
            'rio'          => $rio,
            'municipio'    => $municipio,
            'departamento' => $departamento,
            'latitud'      => $latitud,
            'longitud'     => $longitud,
            'descripcion'  => !empty($descripcion) ? $descripcion : ("Muelle fluvial en {$municipio} ({$departamento}) sobre el {$rio}"),
            'estado'       => $estado
        ]);

        SessionHelper::setFlash('success', "¡Muelle \"{$nombre}\" actualizado exitosamente!");
        $this->redirect('/muelles');
    }

    public function delete(): void {
        AuthHelper::requireRoles(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        $targetMuelle = $this->muelleModel->find($id);
        if (!$targetMuelle) {
            SessionHelper::setFlash('danger', 'El muelle solicitado no existe.');
            $this->redirect('/muelles');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope) && ($targetMuelle['departamento'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para eliminar muelles fuera de su departamento.');
            $this->redirect('/muelles');
        }

        if ($id > 0) {
            $this->muelleModel->delete($id);
            SessionHelper::setFlash('success', 'Muelle eliminado correctamente.');
        }
        $this->redirect('/muelles');
    }
}
