<?php
// =======================================================
// Controlador: ReportesController (Estadísticas y Cierres)
// =======================================================

require_once __DIR__ . '/Controller.php';

class ReportesController extends Controller {
    public function __construct() {
        AuthHelper::requireRoles(['admin', 'operador']);
    }

    public function index(): void {
        $db = Database::getConnection();
        $deptScope = AuthHelper::getDepartmentFilter();

        if (!empty($deptScope)) {
            // 1. Resumen por rutas departamentales
            $stmtRutas = $db->prepare("
                SELECT r.id, mo.nombre AS origen, md.nombre AS destino,
                       mo.departamento AS origen_depto,
                       COUNT(b.id) AS total_pasajes,
                       IFNULL(SUM(b.precio_pagado), 0) AS total_ingresos
                FROM rutas r
                JOIN muelles mo ON r.muelle_origen_id = mo.id
                JOIN muelles md ON r.muelle_destino_id = md.id
                LEFT JOIN viajes v ON v.ruta_id = r.id
                LEFT JOIN boletos b ON b.viaje_id = v.id AND b.estado != 'cancelado'
                WHERE mo.departamento = :dept
                GROUP BY r.id
                ORDER BY total_ingresos DESC
            ");
            $stmtRutas->execute(['dept' => $deptScope]);
            $reporteRutas = $stmtRutas->fetchAll();

            // 2. Resumen por embarcación en el departamento
            $stmtFlota = $db->prepare("
                SELECT e.nombre, e.matricula, e.tipo, e.capacidad_pasajeros,
                       COUNT(DISTINCT v.id) AS viajes_realizados,
                       COUNT(b.id) AS pasajeros_transportados,
                       IFNULL(SUM(c.peso_kg), 0) AS carga_transportada_kg
                FROM embarcaciones e
                LEFT JOIN viajes v ON v.embarcacion_id = e.id
                LEFT JOIN rutas r ON v.ruta_id = r.id
                LEFT JOIN muelles mo ON r.muelle_origen_id = mo.id AND mo.departamento = :dept
                LEFT JOIN boletos b ON b.viaje_id = v.id AND b.estado != 'cancelado'
                LEFT JOIN cargas_encomiendas c ON c.viaje_id = v.id AND c.estado != 'cancelada'
                WHERE (mo.departamento = :dept OR v.id IS NULL)
                GROUP BY e.id
                ORDER BY viajes_realizados DESC
            ");
            $stmtFlota->execute(['dept' => $deptScope]);
            $reporteFlota = $stmtFlota->fetchAll();

            // 3. Resumen por Departamento
            $stmtDeptos = $db->prepare("
                SELECT m.departamento,
                       COUNT(DISTINCT m.id) as total_muelles,
                       COUNT(DISTINCT m.rio) as total_rios,
                       COUNT(DISTINCT r.id) as rutas_conectadas
                FROM muelles m
                LEFT JOIN rutas r ON (r.muelle_origen_id = m.id OR r.muelle_destino_id = m.id)
                WHERE m.estado = 'activo' AND m.departamento = :dept
                GROUP BY m.departamento
            ");
            $stmtDeptos->execute(['dept' => $deptScope]);
            $reporteDeptos = $stmtDeptos->fetchAll();

            // 4. Distribución por Tipos de Embarcación
            $reporteTiposFlota = $db->query("
                SELECT tipo, COUNT(*) as cantidad, 
                       SUM(capacidad_pasajeros) as capacidad_total_pasajeros,
                       SUM(capacidad_carga_kg) as capacidad_total_carga
                FROM embarcaciones
                GROUP BY tipo
                ORDER BY cantidad DESC
            ")->fetchAll();

            // 5. Estado de Viajes departamentales
            $stmtEstados = $db->prepare("
                SELECT v.estado, COUNT(*) as total
                FROM viajes v
                JOIN rutas r ON v.ruta_id = r.id
                JOIN muelles mo ON r.muelle_origen_id = mo.id
                WHERE mo.departamento = :dept
                GROUP BY v.estado
            ");
            $stmtEstados->execute(['dept' => $deptScope]);
            $reporteEstadosViajes = $stmtEstados->fetchAll();

            // 6. Métodos de Pago en Boletos departamentales
            $stmtPagos = $db->prepare("
                SELECT b.metodo_pago, COUNT(*) as total, IFNULL(SUM(b.precio_pagado), 0) as total_recaudado
                FROM boletos b
                JOIN viajes v ON b.viaje_id = v.id
                JOIN rutas r ON v.ruta_id = r.id
                JOIN muelles mo ON r.muelle_origen_id = mo.id
                WHERE b.estado != 'cancelado' AND mo.departamento = :dept
                GROUP BY b.metodo_pago
            ");
            $stmtPagos->execute(['dept' => $deptScope]);
            $reporteMetodosPago = $stmtPagos->fetchAll();

            // 7. Resumen de Cargas Fluviales por Estado departamentales
            $stmtCargas = $db->prepare("
                SELECT c.estado, COUNT(*) as total, IFNULL(SUM(c.peso_kg), 0) as peso_total, IFNULL(SUM(c.valor_flete), 0) as fletes_total
                FROM cargas_encomiendas c
                JOIN viajes v ON c.viaje_id = v.id
                JOIN rutas r ON v.ruta_id = r.id
                JOIN muelles mo ON r.muelle_origen_id = mo.id
                WHERE mo.departamento = :dept
                GROUP BY c.estado
            ");
            $stmtCargas->execute(['dept' => $deptScope]);
            $reporteCargas = $stmtCargas->fetchAll();

            // KPIs Departamentales
            $stmtKpi1 = $db->prepare("SELECT IFNULL(SUM(b.precio_pagado), 0) FROM boletos b JOIN viajes v ON b.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE b.estado != 'cancelado' AND mo.departamento = :dept");
            $stmtKpi1->execute(['dept' => $deptScope]);
            $totalIngresosBoletos = (float)$stmtKpi1->fetchColumn();

            $stmtKpi2 = $db->prepare("SELECT IFNULL(SUM(c.valor_flete), 0) FROM cargas_encomiendas c JOIN viajes v ON c.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE c.estado != 'cancelada' AND mo.departamento = :dept");
            $stmtKpi2->execute(['dept' => $deptScope]);
            $totalIngresosCargas = (float)$stmtKpi2->fetchColumn();

            $stmtKpi3 = $db->prepare("SELECT COUNT(*) FROM boletos b JOIN viajes v ON b.viaje_id = v.id JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE b.estado != 'cancelado' AND mo.departamento = :dept");
            $stmtKpi3->execute(['dept' => $deptScope]);
            $totalPasajes = (int)$stmtKpi3->fetchColumn();

            $stmtKpi4 = $db->prepare("SELECT COUNT(*) FROM viajes v JOIN rutas r ON v.ruta_id = r.id JOIN muelles mo ON r.muelle_origen_id = mo.id WHERE mo.departamento = :dept");
            $stmtKpi4->execute(['dept' => $deptScope]);
            $totalViajes = (int)$stmtKpi4->fetchColumn();

            $stmtKpi5 = $db->prepare("SELECT COUNT(*) FROM muelles WHERE estado = 'activo' AND departamento = :dept");
            $stmtKpi5->execute(['dept' => $deptScope]);
            $totalMuelles = (int)$stmtKpi5->fetchColumn();

            $totalEmbarcaciones = (int)($db->query("SELECT COUNT(*) FROM embarcaciones")->fetchColumn());
        } else {
            // 1. Resumen por rutas (Nacional)
            $sqlRutas = "
                SELECT r.id, mo.nombre AS origen, md.nombre AS destino,
                       mo.departamento AS origen_depto,
                       COUNT(b.id) AS total_pasajes,
                       IFNULL(SUM(b.precio_pagado), 0) AS total_ingresos
                FROM rutas r
                JOIN muelles mo ON r.muelle_origen_id = mo.id
                JOIN muelles md ON r.muelle_destino_id = md.id
                LEFT JOIN viajes v ON v.ruta_id = r.id
                LEFT JOIN boletos b ON b.viaje_id = v.id AND b.estado != 'cancelado'
                GROUP BY r.id
                ORDER BY total_ingresos DESC
            ";
            $reporteRutas = $db->query($sqlRutas)->fetchAll();

            // 2. Resumen por embarcación
            $sqlFlota = "
                SELECT e.nombre, e.matricula, e.tipo, e.capacidad_pasajeros,
                       COUNT(DISTINCT v.id) AS viajes_realizados,
                       COUNT(b.id) AS pasajeros_transportados,
                       IFNULL(SUM(c.peso_kg), 0) AS carga_transportada_kg
                FROM embarcaciones e
                LEFT JOIN viajes v ON v.embarcacion_id = e.id
                LEFT JOIN boletos b ON b.viaje_id = v.id AND b.estado != 'cancelado'
                LEFT JOIN cargas_encomiendas c ON c.viaje_id = v.id AND c.estado != 'cancelada'
                GROUP BY e.id
                ORDER BY viajes_realizados DESC
            ";
            $reporteFlota = $db->query($sqlFlota)->fetchAll();

            // 3. Resumen por Departamentos
            $sqlDeptos = "
                SELECT m.departamento,
                       COUNT(DISTINCT m.id) as total_muelles,
                       COUNT(DISTINCT m.rio) as total_rios,
                       COUNT(DISTINCT r.id) as rutas_conectadas
                FROM muelles m
                LEFT JOIN rutas r ON (r.muelle_origen_id = m.id OR r.muelle_destino_id = m.id)
                WHERE m.estado = 'activo'
                GROUP BY m.departamento
                ORDER BY total_muelles DESC
            ";
            $reporteDeptos = $db->query($sqlDeptos)->fetchAll();

            // 4. Distribución por Tipos de Embarcación
            $sqlTiposFlota = "
                SELECT tipo, COUNT(*) as cantidad, 
                       SUM(capacidad_pasajeros) as capacidad_total_pasajeros,
                       SUM(capacidad_carga_kg) as capacidad_total_carga
                FROM embarcaciones
                GROUP BY tipo
                ORDER BY cantidad DESC
            ";
            $reporteTiposFlota = $db->query($sqlTiposFlota)->fetchAll();

            // 5. Estado de Viajes
            $sqlEstadosViajes = "
                SELECT estado, COUNT(*) as total
                FROM viajes
                GROUP BY estado
            ";
            $reporteEstadosViajes = $db->query($sqlEstadosViajes)->fetchAll();

            // 6. Métodos de Pago en Boletos
            $sqlMetodosPago = "
                SELECT metodo_pago, COUNT(*) as total, IFNULL(SUM(precio_pagado), 0) as total_recaudado
                FROM boletos
                WHERE estado != 'cancelado'
                GROUP BY metodo_pago
            ";
            $reporteMetodosPago = $db->query($sqlMetodosPago)->fetchAll();

            // 7. Resumen de Cargas Fluviales por Estado
            $sqlCargas = "
                SELECT estado, COUNT(*) as total, IFNULL(SUM(peso_kg), 0) as peso_total, IFNULL(SUM(valor_flete), 0) as fletes_total
                FROM cargas_encomiendas
                GROUP BY estado
            ";
            $reporteCargas = $db->query($sqlCargas)->fetchAll();

            // Métricas Totales
            $totalIngresosBoletos = (float)($db->query("SELECT IFNULL(SUM(precio_pagado), 0) FROM boletos WHERE estado != 'cancelado'")->fetchColumn());
            $totalIngresosCargas = (float)($db->query("SELECT IFNULL(SUM(valor_flete), 0) FROM cargas_encomiendas WHERE estado != 'cancelada'")->fetchColumn());
            $totalPasajes = (int)($db->query("SELECT COUNT(*) FROM boletos WHERE estado != 'cancelado'")->fetchColumn());
            $totalViajes = (int)($db->query("SELECT COUNT(*) FROM viajes")->fetchColumn());
            $totalMuelles = (int)($db->query("SELECT COUNT(*) FROM muelles WHERE estado = 'activo'")->fetchColumn());
            $totalEmbarcaciones = (int)($db->query("SELECT COUNT(*) FROM embarcaciones")->fetchColumn());
        }

        $this->render('reportes/index', [
            'pageTitle'            => (!empty($deptScope) ? "Reportes ({$deptScope}) - " : "Reportes y Estadísticas Fluviales - ") . APP_NAME,
            'reporteRutas'         => $reporteRutas,
            'reporteFlota'         => $reporteFlota,
            'reporteDeptos'        => $reporteDeptos,
            'reporteTiposFlota'    => $reporteTiposFlota,
            'reporteEstadosViajes' => $reporteEstadosViajes,
            'reporteMetodosPago'   => $reporteMetodosPago,
            'reporteCargas'        => $reporteCargas,
            'deptScope'            => $deptScope,
            'kpis'                 => [
                'total_ingresos'   => $totalIngresosBoletos + $totalIngresosCargas,
                'ingresos_boletos' => $totalIngresosBoletos,
                'ingresos_cargas'  => $totalIngresosCargas,
                'total_pasajes'    => $totalPasajes,
                'total_viajes'     => $totalViajes,
                'total_muelles'    => $totalMuelles,
                'total_embarcaciones' => $totalEmbarcaciones
            ]
        ]);
    }
}
