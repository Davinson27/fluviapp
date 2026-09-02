<?php
// =======================================================
// Controlador: RutasController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Ruta.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';

class RutasController extends Controller {
    private Ruta $rutaModel;
    private Muelle $muelleModel;

    public function __construct() {
        AuthHelper::requireStaff();
        $this->rutaModel = new Ruta();
        $this->muelleModel = new Muelle();
    }

    public function index(): void {
        $rutas = $this->rutaModel->allWithMuelles();
        $muelles = $this->muelleModel->getActivos();

        $this->render('rutas/index', [
            'pageTitle' => 'Rutas Fluviales - ' . APP_NAME,
            'rutas'     => $rutas,
            'muelles'   => $muelles
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/rutas');
        }

        $origenId = (int)($_POST['muelle_origen_id'] ?? 0);
        $destinoId = (int)($_POST['muelle_destino_id'] ?? 0);
        $distancia = (float)($_POST['distancia_km'] ?? 0);
        $duracion = (int)($_POST['duracion_estimada_min'] ?? 60);
        $tarifa = (float)($_POST['tarifa_base'] ?? 0);
        $estado = $_POST['estado'] ?? 'activa';

        if ($origenId === 0 || $destinoId === 0 || $origenId === $destinoId) {
            SessionHelper::setFlash('danger', 'Seleccione un muelle de origen y un muelle de destino válidos y diferentes.');
            $this->redirect('/rutas');
        }

        $this->rutaModel->create([
            'muelle_origen_id'      => $origenId,
            'muelle_destino_id'     => $destinoId,
            'distancia_km'          => $distancia,
            'duracion_estimada_min' => $duracion,
            'tarifa_base'           => $tarifa,
            'estado'                => $estado
        ]);

        SessionHelper::setFlash('success', 'Ruta fluvial registrada exitosamente.');
        $this->redirect('/rutas');
    }

    public function actualizarTarifa(): void {
        AuthHelper::requireRoles(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/rutas');
        }

        $id = (int)($_POST['id'] ?? 0);
        $tarifa = (float)($_POST['tarifa_base'] ?? 0);

        if ($id > 0 && $tarifa > 0) {
            $this->rutaModel->updateTarifa($id, $tarifa);
            SessionHelper::setFlash('success', 'Tarifa base de la ruta actualizada a $' . number_format($tarifa, 0, ',', '.') . ' COP.');
        } else {
            SessionHelper::setFlash('danger', 'Ingrese un valor de tarifa válido.');
        }

        $this->redirect('/rutas');
    }

    public function delete(): void {
        AuthHelper::requireRoles(['admin']);
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->rutaModel->delete($id);
            SessionHelper::setFlash('success', 'Ruta eliminada correctamente.');
        }
        $this->redirect('/rutas');
    }
}
