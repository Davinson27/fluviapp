<?php
// =======================================================
// Controlador: ViajesController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';
require_once ROOT_PATH . '/app/Models/Ruta.php';
require_once ROOT_PATH . '/app/Models/Embarcacion.php';
require_once ROOT_PATH . '/app/Models/Usuario.php';
require_once ROOT_PATH . '/app/Models/Boleto.php';
require_once ROOT_PATH . '/app/Models/Carga.php';

class ViajesController extends Controller {
    private Viaje $viajeModel;
    private Ruta $rutaModel;
    private Embarcacion $embarcacionModel;
    private Usuario $usuarioModel;
    private Boleto $boletoModel;
    private Carga $cargaModel;

    public function __construct() {
        AuthHelper::requireStaff();
        $this->viajeModel = new Viaje();
        $this->rutaModel = new Ruta();
        $this->embarcacionModel = new Embarcacion();
        $this->usuarioModel = new Usuario();
        $this->boletoModel = new Boleto();
        $this->cargaModel = new Carga();
    }

    public function index(): void {
        $estado = $_GET['estado'] ?? null;
        $deptScope = AuthHelper::getDepartmentFilter();
        $viajes = $this->viajeModel->allWithDetails($estado, $deptScope);

        $this->render('viajes/index', [
            'pageTitle'    => 'Programación de Viajes y Zarpes - ' . APP_NAME,
            'viajes'       => $viajes,
            'estadoFiltro' => $estado,
            'deptScope'    => $deptScope
        ]);
    }

    public function create(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        $deptScope = AuthHelper::getDepartmentFilter();

        if (!empty($deptScope)) {
            $rutas = $this->rutaModel->allWithMuellesAndFilters(['departamento' => $deptScope]);
        } else {
            $rutas = $this->rutaModel->getActivas();
        }

        $embarcaciones = $this->embarcacionModel->getOperativas();
        $capitanes = $this->usuarioModel->getCapitanes();

        $this->render('viajes/create', [
            'pageTitle'     => 'Programar Nuevo Itinerario de Viaje - ' . APP_NAME,
            'rutas'         => $rutas,
            'embarcaciones' => $embarcaciones,
            'capitanes'     => $capitanes,
            'deptScope'     => $deptScope
        ]);
    }

    public function store(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/viajes');
        }

        $rutaId = (int)($_POST['ruta_id'] ?? 0);
        $embarcacionId = (int)($_POST['embarcacion_id'] ?? 0);
        $capitanId = !empty($_POST['capitan_id']) ? (int)$_POST['capitan_id'] : null;
        $fechaSalida = $_POST['fecha_salida'] ?? '';
        $horaSalida = $_POST['hora_salida'] ?? '';
        $precioPasaje = (float)($_POST['precio_pasaje'] ?? 0);
        $observaciones = trim($_POST['observaciones'] ?? '');

        if ($rutaId === 0 || $embarcacionId === 0 || empty($fechaSalida) || empty($horaSalida)) {
            SessionHelper::setFlash('danger', 'Por favor complete todos los campos obligatorios.');
            $this->redirect('/viajes/crear');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $ruta = $this->rutaModel->find($rutaId);
            if ($ruta) {
                $mOrigen = $this->muelleModel->find((int)$ruta['muelle_origen_id']);
                if ($mOrigen && ($mOrigen['departamento'] ?? '') !== $deptScope) {
                    SessionHelper::setFlash('danger', "No tiene permisos para programar viajes fuera de su departamento ({$deptScope}).");
                    $this->redirect('/viajes/crear');
                }
            }
        }

        try {
            $this->viajeModel->create([
                'ruta_id'        => $rutaId,
                'embarcacion_id' => $embarcacionId,
                'capitan_id'     => $capitanId,
                'fecha_salida'   => $fechaSalida,
                'hora_salida'    => $horaSalida,
                'precio_pasaje'  => $precioPasaje,
                'observaciones'  => $observaciones,
                'estado'         => 'programado'
            ]);
            SessionHelper::setFlash('success', 'Itinerario de viaje programado exitosamente.');
            $this->redirect('/viajes');
        } catch (Exception $e) {
            SessionHelper::setFlash('danger', 'Error al programar el viaje: ' . $e->getMessage());
            $this->redirect('/viajes/crear');
        }
    }

    public function cambiarEstado(): void {
        AuthHelper::requireRoles(['admin', 'operador', 'capitan']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/viajes');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $nuevoEstado = $_POST['estado'] ?? '';

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $viaje = $this->viajeModel->findWithDetails($viajeId);
            if ($viaje && ($viaje['origen_depto'] ?? '') !== $deptScope && ($viaje['destino_depto'] ?? '') !== $deptScope) {
                SessionHelper::setFlash('danger', 'No tiene permisos para modificar viajes de otro departamento.');
                $this->redirect('/viajes');
            }
        }

        $estadosValidos = ['programado', 'en_embarque', 'en_navegacion', 'arribado', 'cancelado'];
        if ($viajeId > 0 && in_array($nuevoEstado, $estadosValidos)) {
            $this->viajeModel->updateEstado($viajeId, $nuevoEstado);
            SessionHelper::setFlash('success', "Estado del viaje actualizado a '" . strtoupper(str_replace('_', ' ', $nuevoEstado)) . "'.");
        }

        $this->redirect('/viajes');
    }

    public function actualizarPrecio(): void {
        AuthHelper::requireRoles(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/viajes');
        }

        $viajeId = (int)($_POST['viaje_id'] ?? 0);
        $precio = (float)($_POST['precio_pasaje'] ?? 0);

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope)) {
            $viaje = $this->viajeModel->findWithDetails($viajeId);
            if ($viaje && ($viaje['origen_depto'] ?? '') !== $deptScope && ($viaje['destino_depto'] ?? '') !== $deptScope) {
                SessionHelper::setFlash('danger', 'No tiene permisos para modificar precios de viajes de otro departamento.');
                $this->redirect('/viajes');
            }
        }

        if ($viajeId > 0 && $precio > 0) {
            $this->viajeModel->updatePrecio($viajeId, $precio);
            SessionHelper::setFlash('success', 'Precio del pasaje actualizado a $' . number_format($precio, 0, ',', '.') . ' COP.');
        } else {
            SessionHelper::setFlash('danger', 'Ingrese un precio válido para el viaje.');
        }

        $this->redirect('/viajes');
    }

    public function manifiesto(): void {
        $viajeId = (int)($_GET['id'] ?? 0);
        $viaje = $this->viajeModel->findWithDetails($viajeId);

        if (!$viaje) {
            SessionHelper::setFlash('danger', 'El viaje solicitado no existe.');
            $this->redirect('/viajes');
        }

        $deptScope = AuthHelper::getDepartmentFilter();
        if (!empty($deptScope) && ($viaje['origen_depto'] ?? '') !== $deptScope && ($viaje['destino_depto'] ?? '') !== $deptScope) {
            SessionHelper::setFlash('danger', 'No tiene permisos para consultar manifiestos de otros departamentos.');
            $this->redirect('/viajes');
        }

        $pasajeros = $this->boletoModel->getByViaje($viajeId);
        $cargas = $this->cargaModel->getByViaje($viajeId);

        $this->renderSingle('viajes/manifiesto', [
            'pageTitle' => 'Manifiesto Fluvial de Zarpe - ' . $viaje['codigo_viaje'],
            'viaje'     => $viaje,
            'pasajeros' => $pasajeros,
            'cargas'    => $cargas
        ]);
    }
}
