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

        // Resumen por rutas
        $sqlRutas = "
            SELECT r.id, mo.nombre AS origen, md.nombre AS destino,
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

        // Resumen por embarcación
        $sqlFlota = "
            SELECT e.nombre, e.matricula, e.tipo,
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

        $this->render('reportes/index', [
            'pageTitle'    => 'Reportes y Estadísticas Fluviales - ' . APP_NAME,
            'reporteRutas' => $reporteRutas,
            'reporteFlota' => $reporteFlota
        ]);
    }
}
