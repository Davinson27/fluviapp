<?php
// =======================================================
// Controlador: TrackingController - FluviApp v2.0
// Rastreo GPS Fluvial en Vivo (Zero Hardware / Modo Capitán)
// =======================================================

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Helpers/AuthHelper.php';
require_once __DIR__ . '/../Helpers/SessionHelper.php';
require_once __DIR__ . '/../Models/Viaje.php';

class TrackingController extends Controller {
    private Viaje $viajeModel;

    public function __construct() {
        $this->viajeModel = new Viaje();
    }

    /**
     * Vista para que el Capitán transmita su posición GPS desde su celular
     */
    public function capitanNavegacion(): void {
        AuthHelper::requireRole(['capitan', 'operador', 'admin']);
        $user = AuthHelper::user();

        // Buscar viaje asignado en curso o programado para este capitán
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT v.*, r.distancia_km, r.duracion_estimada_min,
                   m1.nombre as origen_nombre, m1.latitud as origen_lat, m1.longitud as origen_lng,
                   m2.nombre as destino_nombre, m2.latitud as destino_lat, m2.longitud as destino_lng,
                   e.nombre as embarcacion_nombre, e.matricula as embarcacion_matricula
            FROM viajes v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles m1 ON r.muelle_origen_id = m1.id
            JOIN muelles m2 ON r.muelle_destino_id = m2.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE (v.capitan_id = :user_id OR :is_admin = 1)
              AND v.estado IN ('programado', 'en_embarque', 'en_navegacion')
            ORDER BY v.fecha_salida ASC, v.hora_salida ASC
            LIMIT 1
        ");
        $isAdmin = in_array($user['rol'], ['admin', 'operador']) ? 1 : 0;
        $stmt->execute([':user_id' => $user['id'], ':is_admin' => $isAdmin]);
        $viaje = $stmt->fetch();

        $this->render('tracking/capitan', [
            'pageTitle' => 'Transmisión de Navegación GPS - ' . APP_NAME,
            'user'      => $user,
            'viaje'     => $viaje
        ]);
    }

    /**
     * API para recibir coordenadas GPS del celular del capitán
     */
    public function actualizarPosicion(): void {
        AuthHelper::requireRole(['capitan', 'operador', 'admin']);
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $viajeId = (int)($input['viaje_id'] ?? 0);
        $lat = (float)($input['latitud'] ?? 0.0);
        $lng = (float)($input['longitud'] ?? 0.0);
        $velocidadKmh = max(0.0, (float)($input['velocidad_kmh'] ?? 0.0));
        $rumbo = (float)($input['rumbo'] ?? 0.0);

        if ($viajeId <= 0 || ($lat == 0.0 && $lng == 0.0)) {
            echo json_encode(['success' => false, 'error' => 'Coordenadas o viaje no válidos']);
            exit;
        }

        $db = Database::getConnection();

        // Actualizar o insertar telemetría
        $stmt = $db->prepare("
            INSERT INTO viajes_telemetria 
                (viaje_id, latitud, longitud, velocidad_kmh, rumbo, ultima_actualizacion)
            VALUES 
                (:viaje_id, :lat, :lng, :vel, :rumbo, NOW())
            ON DUPLICATE KEY UPDATE 
                latitud = VALUES(latitud),
                longitud = VALUES(longitud),
                velocidad_kmh = VALUES(velocidad_kmh),
                rumbo = VALUES(rumbo),
                ultima_actualizacion = NOW()
        ");
        $stmt->execute([
            ':viaje_id' => $viajeId,
            ':lat'      => $lat,
            ':lng'      => $lng,
            ':vel'      => $velocidadKmh,
            ':rumbo'    => $rumbo
        ]);

        // Si el viaje estaba en 'programado' o 'en_embarque', pasarlo a 'en_navegacion'
        $stmtViaje = $db->prepare("UPDATE viajes SET estado = 'en_navegacion' WHERE id = :viaje_id AND estado IN ('programado', 'en_embarque')");
        $stmtViaje->execute([':viaje_id' => $viajeId]);

        echo json_encode([
            'success'    => true,
            'timestamp'  => date('H:i:s'),
            'velocidad'  => $velocidadKmh,
            'viaje_id'   => $viajeId
        ]);
        exit;
    }

    /**
     * API pública para consultar posición en vivo y calcular ETA
     */
    public function posicionViaje(): void {
        header('Content-Type: application/json; charset=utf-8');
        $viajeId = (int)($_GET['id'] ?? 0);

        if ($viajeId <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID de viaje inválido']);
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT v.id as viaje_id, v.codigo_viaje, v.estado, v.fecha_salida, v.hora_salida,
                   m1.nombre as origen_nombre, m1.latitud as origen_lat, m1.longitud as origen_lng,
                   m2.nombre as destino_nombre, m2.latitud as destino_lat, m2.longitud as destino_lng,
                   e.nombre as embarcacion_nombre, e.matricula as embarcacion_matricula,
                   t.latitud, t.longitud, t.velocidad_kmh, t.rumbo, t.ultima_actualizacion
            FROM viajes v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles m1 ON r.muelle_origen_id = m1.id
            JOIN muelles m2 ON r.muelle_destino_id = m2.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN viajes_telemetria t ON v.id = t.viaje_id
            WHERE v.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $viajeId]);
        $viaje = $stmt->fetch();

        if (!$viaje) {
            echo json_encode(['success' => false, 'error' => 'Viaje no encontrado']);
            exit;
        }

        // Si no hay coordenadas en telemetría, usar las del muelle de origen
        $lat = !empty($viaje['latitud']) ? (float)$viaje['latitud'] : (float)($viaje['origen_lat'] ?? 9.2423);
        $lng = !empty($viaje['longitud']) ? (float)$viaje['longitud'] : (float)($viaje['origen_lng'] ?? -74.7547);
        $vel = !empty($viaje['velocidad_kmh']) ? (float)$viaje['velocidad_kmh'] : 0.0;

        // Calcular distancia al muelle de destino en km (Fórmula de Haversine)
        $destLat = (float)($viaje['destino_lat'] ?? $lat);
        $destLng = (float)($viaje['destino_lng'] ?? $lng);
        $distanciaRestanteKm = self::calcularHaversine($lat, $lng, $destLat, $destLng);

        // Calcular ETA en minutos
        $etaMinutos = 0;
        if ($vel > 3.0) {
            $etaMinutos = round(($distanciaRestanteKm / $vel) * 60);
        } else {
            // Si la embarcación está detenida o iniciando, estimar a 35 km/h promedio fluvial
            $etaMinutos = round(($distanciaRestanteKm / 35.0) * 60);
        }

        echo json_encode([
            'success'               => true,
            'viaje_id'              => $viaje['viaje_id'],
            'codigo_viaje'          => $viaje['codigo_viaje'],
            'estado'                => $viaje['estado'],
            'embarcacion'           => $viaje['embarcacion_nombre'],
            'origen'                => $viaje['origen_nombre'],
            'destino'               => $viaje['destino_nombre'],
            'origen_coords'         => [(float)$viaje['origen_lat'], (float)$viaje['origen_lng']],
            'destino_coords'        => [(float)$viaje['destino_lat'], (float)$viaje['destino_lng']],
            'latitud'               => $lat,
            'longitud'              => $lng,
            'velocidad_kmh'         => $vel,
            'velocidad_nudos'       => round($vel * 0.539957, 1),
            'rumbo'                 => (float)($viaje['rumbo'] ?? 0.0),
            'distancia_restante_km' => round($distanciaRestanteKm, 1),
            'eta_minutos'           => max(1, $etaMinutos),
            'ultima_actualizacion'  => $viaje['ultima_actualizacion'] ?? date('Y-m-d H:i:s')
        ]);
        exit;
    }

    /**
     * Vista pública interactiva con mapa Leaflet del viaje fluvial en vivo
     */
    public function viajeEnVivo(): void {
        $viajeId = (int)($_GET['id'] ?? 0);
        $viaje = $this->viajeModel->findWithDetails($viajeId);

        if (!$viaje) {
            SessionHelper::setFlash('warning', 'Viaje no encontrado para rastreo.');
            $this->redirect('/portal');
        }

        $this->render('tracking/en_vivo', [
            'pageTitle' => 'Rastreo Fluvial en Vivo: ' . $viaje['codigo_viaje'] . ' - ' . APP_NAME,
            'viaje'     => $viaje
        ]);
    }

    private static function calcularHaversine(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
