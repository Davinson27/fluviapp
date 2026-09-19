<?php
// =======================================================
// Controlador: FlotaController - FluviApp v2.0
// Gestión de Flota, Mantenimiento, Combustible y Documentos
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Helpers/SessionHelper.php';
require_once __DIR__ . '/../Models/Embarcacion.php';
require_once __DIR__ . '/../Models/Mantenimiento.php';
require_once __DIR__ . '/../Models/Combustible.php';
require_once __DIR__ . '/../Models/DocumentoEmbarcacion.php';

class FlotaController extends Controller {
    private Embarcacion $embarcacionModel;
    private Mantenimiento $mantenimientoModel;
    private Combustible $combustibleModel;
    private DocumentoEmbarcacion $documentoModel;

    public function __construct() {
        $this->embarcacionModel = new Embarcacion();
        $this->mantenimientoModel = new Mantenimiento();
        $this->combustibleModel = new Combustible();
        $this->documentoModel = new DocumentoEmbarcacion();
    }

    /**
     * Dashboard general del módulo de Flota
     */
    public function index(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        $embarcaciones = $this->embarcacionModel->all();
        $metricasCombustible = $this->combustibleModel->getMetricas();
        $alertasDocumentos = $this->documentoModel->getAlertasResumen();
        $mantenimientosRecientes = array_slice($this->mantenimientoModel->allWithEmbarcacion(), 0, 5);

        $this->render('flota/index', [
            'pageTitle'               => 'Gestión Integral de Flota Fluvial - ' . APP_NAME,
            'embarcaciones'           => $embarcaciones,
            'metricasCombustible'     => $metricasCombustible,
            'alertasDocumentos'       => $alertasDocumentos,
            'mantenimientosRecientes' => $mantenimientosRecientes
        ]);
    }

    /**
     * Bitácora de Mantenimiento
     */
    public function mantenimiento(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        $mantenimientos = $this->mantenimientoModel->allWithEmbarcacion();
        $embarcaciones = $this->embarcacionModel->all();

        $this->render('flota/mantenimiento', [
            'pageTitle'      => 'Bitácora de Mantenimiento Técnico - ' . APP_NAME,
            'mantenimientos' => $mantenimientos,
            'embarcaciones'  => $embarcaciones
        ]);
    }

    public function guardarMantenimiento(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/flota/mantenimiento');
        }

        $this->mantenimientoModel->crear($_POST);
        SessionHelper::setFlash('success', 'Registro de mantenimiento guardado en la bitácora.');
        $this->redirect('/flota/mantenimiento');
    }

    /**
     * Control de Combustible
     */
    public function combustible(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        $combustibles = $this->combustibleModel->allWithEmbarcacion();
        $embarcaciones = $this->embarcacionModel->all();

        $this->render('flota/combustible', [
            'pageTitle'     => 'Control de Combustible y Rendimiento - ' . APP_NAME,
            'combustibles'  => $combustibles,
            'embarcaciones' => $embarcaciones
        ]);
    }

    public function guardarCombustible(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/flota/combustible');
        }

        $this->combustibleModel->crear($_POST);
        SessionHelper::setFlash('success', 'Tanqueo de combustible registrado con éxito.');
        $this->redirect('/flota/combustible');
    }

    /**
     * Custodia Documental con Semáforo
     */
    public function documentos(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        $documentos = $this->documentoModel->allConSemaforo();
        $embarcaciones = $this->embarcacionModel->all();

        $this->render('flota/documentos', [
            'pageTitle'     => 'Documentación Normativa y Pólizas - ' . APP_NAME,
            'documentos'    => $documentos,
            'embarcaciones' => $embarcaciones
        ]);
    }

    public function guardarDocumento(): void {
        AuthHelper::requireRole(['admin', 'operador']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/flota/documentos');
        }

        $this->documentoModel->crear($_POST);
        SessionHelper::setFlash('success', 'Documento legal registrado exitosamente.');
        $this->redirect('/flota/documentos');
    }
}
