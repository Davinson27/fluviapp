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
        $deptScope = AuthHelper::getDepartmentFilter();

        if (!empty($deptScope)) {
            // Métricas filtradas para el Administrador Departamental
            $totalEmbarcaciones = $this->embarcacionModel->count();
            
            $stmtRutas = $db->prepare("SELECT COUNT(*) FROM rutas r JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE mo.departamento = :dept");
            $stmtRutas->execute(['dept' => $deptScope]);
            $totalRutas = (int)$stmtRutas->fetchColumn();

            $stmtViajes = $db->prepare("SELECT COUNT(*) FROM viajes v JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE mo.departamento = :dept");
            $stmtViajes->execute(['dept' => $deptScope]);
            $totalViajes = (int)$stmtViajes->fetchColumn();

            $stmtBoletos = $db->prepare("SELECT COUNT(*) FROM boletos b JOIN viajes v ON b.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE mo.departamento = :dept");
            $stmtBoletos->execute(['dept' => $deptScope]);
            $totalBoletos = (int)$stmtBoletos->fetchColumn();

            $stmtCargas = $db->prepare("SELECT COUNT(*) FROM cargas_encomiendas c JOIN viajes v ON c.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE mo.departamento = :dept");
            $stmtCargas->execute(['dept' => $deptScope]);
            $totalCargas = (int)$stmtCargas->fetchColumn();

            // Ingresos departamentales
            $stmtIngresos = $db->prepare("SELECT IFNULL(SUM(b.precio_pagado), 0) FROM boletos b JOIN viajes v ON b.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE b.estado != 'cancelado' AND mo.departamento = :dept");
            $stmtIngresos->execute(['dept' => $deptScope]);
            $ingresosBoletos = (float)$stmtIngresos->fetchColumn();

            $stmtFletes = $db->prepare("SELECT IFNULL(SUM(c.valor_flete), 0) FROM cargas_encomiendas c JOIN viajes v ON c.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE c.estado != 'cancelada' AND mo.departamento = :dept");
            $stmtFletes->execute(['dept' => $deptScope]);
            $ingresosFletes = (float)$stmtFletes->fetchColumn();

            $viajesHoy = $this->viajeModel->allWithDetails(null, $deptScope);
            $viajesHoy = array_slice($viajesHoy, 0, 5);

            $ultimosBoletos = $this->boletoModel->allWithViaje($deptScope);
            $ultimosBoletos = array_slice($ultimosBoletos, 0, 5);
        } else {
            // Métricas globales / Administrador General (Nacional)
            $totalEmbarcaciones = $this->embarcacionModel->count();
            $totalRutas = $this->rutaModel->count();
            $totalViajes = $this->viajeModel->count();
            $totalBoletos = $this->boletoModel->count();
            $totalCargas = $this->cargaModel->count();

            $stmtIngresos = $db->query("SELECT IFNULL(SUM(precio_pagado), 0) AS total_boletos FROM boletos WHERE estado != 'cancelado'");
            $ingresosBoletos = (float)$stmtIngresos->fetch()['total_boletos'];

            $stmtFletes = $db->query("SELECT IFNULL(SUM(valor_flete), 0) AS total_fletes FROM cargas_encomiendas WHERE estado != 'cancelada'");
            $ingresosFletes = (float)$stmtFletes->fetch()['total_fletes'];

            $viajesHoy = $this->viajeModel->allWithDetails();
            $viajesHoy = array_slice($viajesHoy, 0, 5);

            $ultimosBoletos = $this->boletoModel->allWithViaje();
            $ultimosBoletos = array_slice($ultimosBoletos, 0, 5);
        }

        $ingresosTotales = $ingresosBoletos + $ingresosFletes;

        $this->render('dashboard/index', [
            'pageTitle'          => (!empty($deptScope) ? "Dashboard ({$deptScope}) - " : "Dashboard General - ") . APP_NAME,
            'totalEmbarcaciones' => $totalEmbarcaciones,
            'totalRutas'         => $totalRutas,
            'totalViajes'        => $totalViajes,
            'totalBoletos'       => $totalBoletos,
            'totalCargas'        => $totalCargas,
            'ingresosTotales'    => $ingresosTotales,
            'ingresosBoletos'    => $ingresosBoletos,
            'ingresosFletes'     => $ingresosFletes,
            'viajesHoy'          => $viajesHoy,
            'ultimosBoletos'     => $ultimosBoletos,
            'deptScope'          => $deptScope
        ]);
    }
}
