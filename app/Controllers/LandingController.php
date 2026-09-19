<?php
// =======================================================
// Controlador: LandingController (Página de Aterrizaje Pública)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';
require_once ROOT_PATH . '/app/Models/Ruta.php';
require_once ROOT_PATH . '/app/Models/Muelle.php';
require_once ROOT_PATH . '/app/Models/Rio.php';
require_once ROOT_PATH . '/app/Models/Embarcacion.php';
require_once ROOT_PATH . '/app/Models/Carga.php';

class LandingController extends Controller {
    private Viaje $viajeModel;
    private Ruta $rutaModel;
    private Muelle $muelleModel;
    private Rio $rioModel;
    private Embarcacion $embarcacionModel;
    private Carga $cargaModel;

    public function __construct() {
        $this->viajeModel = new Viaje();
        $this->rutaModel = new Ruta();
        $this->muelleModel = new Muelle();
        $this->rioModel = new Rio();
        $this->embarcacionModel = new Embarcacion();
        $this->cargaModel = new Carga();
    }

    public function index(): void {
        // Datos maestros
        $departamentos = $this->muelleModel->getDepartamentos();
        $riosLista = $this->muelleModel->getRiosUnicos();
        $riosInfo = $this->rioModel->all();
        $embarcaciones = $this->embarcacionModel->all();
        $muellesGrouped = $this->muelleModel->getGroupedByDepartamento();

        // Parámetros de filtrado
        $departamentoFiltro = trim($_GET['departamento'] ?? '');
        $rioFiltro = trim($_GET['rio'] ?? '');
        $origenId = (int)($_GET['origen_id'] ?? 0);
        $destinoId = (int)($_GET['destino_id'] ?? 0);
        $fecha = trim($_GET['fecha'] ?? '');
        $maxPrecio = !empty($_GET['max_precio']) ? (float)$_GET['max_precio'] : 0;
        $guiaBusqueda = trim($_GET['guia'] ?? '');

        // Filtros para viajes programados
        $filtrosViajes = [];
        if ($origenId > 0) $filtrosViajes['origen_id'] = $origenId;
        if ($destinoId > 0) $filtrosViajes['destino_id'] = $destinoId;
        if (!empty($fecha)) $filtrosViajes['fecha'] = $fecha;
        $viajesDisponibles = $this->viajeModel->getViajesDisponibles($filtrosViajes);

        // Filtros para catálogo de rutas
        $filtrosRutas = [];
        if (!empty($departamentoFiltro)) $filtrosRutas['departamento'] = $departamentoFiltro;
        if (!empty($rioFiltro)) $filtrosRutas['rio'] = $rioFiltro;
        if ($maxPrecio > 0) $filtrosRutas['max_precio'] = $maxPrecio;
        $rutasFiltradas = $this->rutaModel->allWithMuellesAndFilters($filtrosRutas);

        // Datos para el mapa interactivo (todos los muelles y rutas con coordenadas)
        $muellesMapa = $this->muelleModel->getForMap([
            'departamento' => $departamentoFiltro,
            'rio'          => $rioFiltro
        ]);
        $rutasMapa = $this->rutaModel->getRutasConCoordenadas();

        // Rastreo de encomienda si se especificó guía
        $encomiendaRastreo = null;
        $errorRastreo = null;
        if (!empty($guiaBusqueda)) {
            $encomiendaRastreo = $this->cargaModel->findByGuia($guiaBusqueda);
            if (!$encomiendaRastreo) {
                $errorRastreo = "No se encontró ninguna encomienda registrada con la guía \"{$guiaBusqueda}\". Verifica el número e intenta nuevamente.";
            }
        }

        // Estadísticas nacionales dinámicas
        $stats = [
            'total_departamentos' => count($departamentos),
            'total_muelles'       => count($this->muelleModel->getActivos()),
            'total_rios'          => count($riosInfo),
            'total_rutas'         => count($this->rutaModel->getActivas()),
            'total_embarcaciones' => count($embarcaciones),
            'viajes_programados'  => count($viajesDisponibles)
        ];

        $this->renderSingle('landing/index', [
            'pageTitle'          => 'FluviApp - Red Nacional de Transporte Fluvial en Colombia | Rutas, Pasajes y Carga',
            'metaDescription'    => 'Descubre las rutas fluviales de Colombia en 29 departamentos. Consulta horarios, precios, muelles y puertos en el Río Magdalena, Cauca, Atrato, Meta, Amazonas y más. Facturación electrónica y rastreo en vivo.',
            'departamentos'      => $departamentos,
            'riosLista'          => $riosLista,
            'riosInfo'           => $riosInfo,
            'embarcaciones'      => $embarcaciones,
            'muellesGrouped'     => $muellesGrouped,
            'rutasFiltradas'     => $rutasFiltradas,
            'viajesDisponibles'  => $viajesDisponibles,
            'muellesMapa'        => $muellesMapa,
            'rutasMapa'          => $rutasMapa,
            'encomiendaRastreo'  => $encomiendaRastreo,
            'errorRastreo'       => $errorRastreo,
            'guiaBusqueda'       => $guiaBusqueda,
            'filtros'            => [
                'departamento' => $departamentoFiltro,
                'rio'          => $rioFiltro,
                'origen_id'    => $origenId,
                'destino_id'   => $destinoId,
                'fecha'        => $fecha,
                'max_precio'   => $maxPrecio
            ],
            'stats'              => $stats
        ]);
    }

    // ==========================================
    // API JSON Endpoints (para mapa y filtros)
    // ==========================================

    public function apiMuelles(): void {
        $departamento = trim($_GET['departamento'] ?? '');
        $rio = trim($_GET['rio'] ?? '');

        $filtros = [];
        if (!empty($departamento)) $filtros['departamento'] = $departamento;
        if (!empty($rio)) $filtros['rio'] = $rio;

        $muelles = $this->muelleModel->getForMap($filtros);
        $this->json(['success' => true, 'total' => count($muelles), 'data' => $muelles]);
    }

    public function apiRutas(): void {
        $departamento = trim($_GET['departamento'] ?? '');
        $rio = trim($_GET['rio'] ?? '');
        $maxPrecio = !empty($_GET['max_precio']) ? (float)$_GET['max_precio'] : 0;
        $q = trim($_GET['q'] ?? '');

        $filtros = [];
        if (!empty($departamento)) $filtros['departamento'] = $departamento;
        if (!empty($rio)) $filtros['rio'] = $rio;
        if ($maxPrecio > 0) $filtros['max_precio'] = $maxPrecio;
        if (!empty($q)) $filtros['q'] = $q;

        $rutas = $this->rutaModel->allWithMuellesAndFilters($filtros);
        $this->json(['success' => true, 'total' => count($rutas), 'data' => $rutas]);
    }

    public function apiRios(): void {
        $rios = $this->rioModel->all();
        $this->json(['success' => true, 'total' => count($rios), 'data' => $rios]);
    }

    public function apiChatbot(): void {
        header('Content-Type: application/json; charset=utf-8');
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $pregunta = trim($input['pregunta'] ?? '');

        if (empty($pregunta)) {
            echo json_encode(['respuesta' => 'Por favor escribe tu consulta para poder orientarte.']);
            exit;
        }

        require_once ROOT_PATH . '/app/Services/AsistenteFluvialIA.php';
        require_once ROOT_PATH . '/app/Helpers/AuthHelper.php';
        $usuario = AuthHelper::user();

        $ia = new AsistenteFluvialIA();
        $respuesta = $ia->responderConsultaGeneral($pregunta, $usuario);

        echo json_encode(['respuesta' => $respuesta]);
        exit;
    }
}