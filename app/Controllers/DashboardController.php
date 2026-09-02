<?php
// =======================================================
// Controlador: DashboardController
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once ROOT_PATH . '/app/Models/Viaje.php';
require_once ROOT_PATH . '/app/Models/Embarcacion.php';
require_once ROOT_PATH . '/app/Models/Boleto.php';
require_once ROOT_PATH . '/app/Models/Carga.php';
require_once ROOT_PATH . '/app/Models/Ruta.php';

class DashboardController extends Controller {
    private Viaje $viajeModel;
    private Embarcacion $embarcacionModel;
    private Boleto $boletoModel;
    private Carga $cargaModel;
    private Ruta $rutaModel;

    public function __construct() {
        AuthHelper::requireAuth();
        if (AuthHelper::isCliente()) {
            $this->redirect('/portal');
        }
        AuthHelper::requireStaff();
        $this->viajeModel = new Viaje();
        $this->embarcacionModel = new Embarcacion();
        $this->boletoModel = new Boleto();
        $this->cargaModel = new Carga();
        $this->rutaModel = new Ruta();
    }

    public function index(): void {
        $db = Database::getConnection();

        // Métricas clave
        $totalEmbarcaciones = $this->embarcacionModel->count();
        $totalRutas = $this->rutaModel->count();
        $totalViajes = $this->viajeModel->count();
        $totalBoletos = $this->boletoModel->count();
        $totalCargas = $this->cargaModel->count();

        // Ingresos totales por boletos
        $stmtIngresos = $db->query("SELECT IFNULL(SUM(precio_pagado), 0) AS total_boletos FROM boletos WHERE estado != 'cancelado'");
        $ingresosBoletos = (float)$stmtIngresos->fetch()['total_boletos'];

        // Ingresos totales por fletes
        $stmtFletes = $db->query("SELECT IFNULL(SUM(valor_flete), 0) AS total_fletes FROM cargas_encomiendas WHERE estado != 'cancelada'");
        $ingresosFletes = (float)$stmtFletes->fetch()['total_fletes'];

        $ingresosTotales = $ingresosBoletos + $ingresosFletes;

        // Viajes activos / programados de hoy
        $viajesHoy = $this->viajeModel->allWithDetails();
        $viajesHoy = array_slice($viajesHoy, 0, 5);

        // Últimos boletos emitidos
        $ultimosBoletos = $this->boletoModel->allWithViaje();
        $ultimosBoletos = array_slice($ultimosBoletos, 0, 5);

        $this->render('dashboard/index', [
            'pageTitle'          => 'Dashboard - ' . APP_NAME,
            'totalEmbarcaciones' => $totalEmbarcaciones,
            'totalRutas'         => $totalRutas,
            'totalViajes'        => $totalViajes,
            'totalBoletos'       => $totalBoletos,
            'totalCargas'        => $totalCargas,
            'ingresosTotales'    => $ingresosTotales,
            'ingresosBoletos'    => $ingresosBoletos,
            'ingresosFletes'     => $ingresosFletes,
            'viajesHoy'          => $viajesHoy,
            'ultimosBoletos'     => $ultimosBoletos
        ]);
    }
}
