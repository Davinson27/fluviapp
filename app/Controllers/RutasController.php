<?php
// =======================================================
// Controlador: RutasController (Gestión de Rutas Fluviales)
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
        $deptScope = AuthHelper::getDepartmentFilter();

        if (!empty($deptScope)) {
            $deptoFiltro = $deptScope;
            $departamentos = [['departamento' => $deptScope]];
            $muelles = $this->muelleModel->getByDepartamento($deptScope);
            $muellesGrouped = [$deptScope => $muelles];
        } else {
            $deptoFiltro = trim($_GET['departamento'] ?? '');
            $departamentos = $this->muelleModel->getDepartamentos();
            $muellesGrouped = $this->muelleModel->getGroupedByDepartamento();
            $muelles = $this->muelleModel->getActivos();
        }

        $filtros = [];
        if (!empty($deptoFiltro)) {
            $filtros['departamento'] = $deptoFiltro;
        }

        $rutas = $this->rutaModel->allWithMuellesAndFilters($filtros);

        $this->render('rutas/index', [
            'pageTitle'      => 'Rutas Fluviales por Departamento - ' . APP_NAME,
            'rutas'          => $rutas,
            'muelles'        => $muelles,
            'departamentos'  => $departamentos,
            'muellesGrouped' => $muellesGrouped,
            'deptoFiltro'    => $deptoFiltro,
            'deptScope'      => $deptScope
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/rutas');
        }

        $departamento = trim($_POST['departamento'] ?? '');
        $origenId = (int)($_POST['muelle_origen_id'] ?? 0);
        $destinoId = (int)($_POST['muelle_destino_id'] ?? 0);
        $distancia = (float)($_POST['distancia_km'] ?? 0);
        $duracion = (int)($_POST['duracion_estimada_min'] ?? 60);
        $tarifa = (float)($_POST['tarifa_base'] ?? 0);
        $estado = $_POST['estado'] ?? 'activa';
        $crearRetorno = !empty($_POST['crear_retorno']);

        if ($origenId === 0 || $destinoId === 0 || $origenId === $destinoId) {
            SessionHelper::setFlash('danger', 'Seleccione un muelle de origen y un muelle de destino válidos y diferentes.');
            $this->redirect('/rutas');
        }

        // Validar que ambos muelles pertenezcan AL MISMO DEPARTAMENTO
        $muelleOrigen = $this->muelleModel->find($origenId);
        $muelleDestino = $this->muelleModel->find($destinoId);

        if (!$muelleOrigen || !$muelleDestino) {
            SessionHelper::setFlash('danger', 'Uno o ambos muelles seleccionados no existen.');
            $this->redirect('/rutas');
        }

        if ($muelleOrigen['departamento'] !== $muelleDestino['departamento']) {
            SessionHelper::setFlash('danger', "Norma de Navegación: Las rutas deben ser exclusivamente dentro del mismo departamento ({$muelleOrigen['departamento']} ≠ {$muelleDestino['departamento']}). No se permite transporte fluvial entre departamentos diferentes.");
            $this->redirect('/rutas');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope) && ($muelleOrigen['departamento'] !== $deptScope || $muelleDestino['departamento'] !== $deptScope)) {
            SessionHelper::setFlash('danger', "No tiene permisos para crear rutas en jurisdicciones fuera de su departamento ({$deptScope}).");
            $this->redirect('/rutas');
        }

        // Crear ruta principal
        $this->rutaModel->create([
            'muelle_origen_id'      => $origenId,
            'muelle_destino_id'     => $destinoId,
            'distancia_km'          => $distancia,
            'duracion_estimada_min' => $duracion,
            'tarifa_base'           => $tarifa,
            'estado'                => $estado
        ]);

        // Crear ruta de retorno opcionalmente
        if ($crearRetorno) {
            $this->rutaModel->create([
                'muelle_origen_id'      => $destinoId,
                'muelle_destino_id'     => $origenId,
                'distancia_km'          => $distancia,
                'duracion_estimada_min' => $duracion,
                'tarifa_base'           => $tarifa,
                'estado'                => $estado
            ]);
            SessionHelper::setFlash('success', "Ruta fluvial bidireccional registrada exitosamente para el departamento de {$muelleOrigen['departamento']}.");
        } else {
            SessionHelper::setFlash('success', "Ruta fluvial registrada exitosamente para el departamento de {$muelleOrigen['departamento']}.");
        }

        $this->redirect('/rutas?departamento=' . urlencode($muelleOrigen['departamento']));
    }

    public function actualizarTarifa(): void {
        AuthHelper::requireRoles(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/rutas');
        }

        $id = (int)($_POST['id'] ?? 0);
        $tarifa = (float)($_POST['tarifa_base'] ?? 0);

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $ruta = $this->rutaModel->find($id);
            if ($ruta) {
                $mOrigen = $this->muelleModel->find((int)$ruta['muelle_origen_id']);
                if ($mOrigen && ($mOrigen['departamento'] ?? '') !== $deptScope) {
                    SessionHelper::setFlash('danger', 'No tiene permisos para actualizar tarifas de rutas fuera de su departamento.');
                    $this->redirect('/rutas');
                }
            }
        }

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

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $ruta = $this->rutaModel->find($id);
            if ($ruta) {
                $mOrigen = $this->muelleModel->find((int)$ruta['muelle_origen_id']);
                if ($mOrigen && ($mOrigen['departamento'] ?? '') !== $deptScope) {
                    SessionHelper::setFlash('danger', 'No tiene permisos para eliminar rutas fuera de su departamento.');
                    $this->redirect('/rutas');
                }
            }
        }

        if ($id > 0) {
            $this->rutaModel->delete($id);
            SessionHelper::setFlash('success', 'Ruta eliminada correctamente.');
        }
        $this->redirect('/rutas');
    }
}
