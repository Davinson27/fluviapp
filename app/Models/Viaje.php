<?php
// =======================================================
// Modelo: Viaje (Itinerarios, Zarpes y Manifiestos)
// =======================================================

require_once __DIR__ . '/Model.php';

class Viaje extends Model {
    protected string $table = 'viajes';

    public function allWithDetails(?string $estadoFilter = null, ?string $departamentoFilter = null): array {
        $sql = "
            SELECT v.*,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.departamento AS origen_depto,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.departamento AS destino_depto,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula,
                   e.capacidad_pasajeros AS embarcacion_cap_pasajeros,
                   e.capacidad_carga_kg AS embarcacion_cap_carga,
                   u.nombre AS capitan_nombre,
                   (SELECT COUNT(*) FROM boletos b WHERE b.viaje_id = v.id AND b.estado != 'cancelado') AS boletos_vendidos,
                   (SELECT IFNULL(SUM(peso_kg), 0) FROM cargas_encomiendas c WHERE c.viaje_id = v.id AND c.estado != 'cancelada') AS carga_total_kg
            FROM `{$this->table}` v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON v.capitan_id = u.id
            WHERE 1=1
        ";
        $params = [];
        if (!empty($estadoFilter)) {
            $sql .= " AND v.estado = :estado ";
            $params['estado'] = $estadoFilter;
        }
        if (!empty($departamentoFilter)) {
            $sql .= " AND (mo.departamento = :depto1 OR md.departamento = :depto2) ";
            $params['depto1'] = $departamentoFilter;
            $params['depto2'] = $departamentoFilter;
        }
        $sql .= " ORDER BY v.fecha_salida DESC, v.hora_salida DESC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithDetails(int $id): ?array {
        $sql = "
            SELECT v.*,
                   r.distancia_km, r.duracion_estimada_min, r.tarifa_base,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   mo.departamento AS origen_depto, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
                   md.departamento AS destino_depto, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula, e.tipo AS embarcacion_tipo,
                   e.capacidad_pasajeros AS embarcacion_cap_pasajeros,
                   e.capacidad_carga_kg AS embarcacion_cap_carga,
                   u.nombre AS capitan_nombre, u.telefono AS capitan_telefono,
                   (SELECT COUNT(*) FROM boletos b WHERE b.viaje_id = v.id AND b.estado != 'cancelado') AS boletos_vendidos,
                   (SELECT IFNULL(SUM(peso_kg), 0) FROM cargas_encomiendas c WHERE c.viaje_id = v.id AND c.estado != 'cancelada') AS carga_total_kg
            FROM `{$this->table}` v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON v.capitan_id = u.id
            WHERE v.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        // Obtener capacidad de la embarcación
        $stmtEmb = $this->db->prepare("SELECT capacidad_pasajeros, capacidad_carga_kg FROM embarcaciones WHERE id = :id");
        $stmtEmb->execute(['id' => (int)$data['embarcacion_id']]);
        $emb = $stmtEmb->fetch();

        $cupos = (int)($emb['capacidad_pasajeros'] ?? 30);
        $cargaDisp = (float)($emb['capacidad_carga_kg'] ?? 500);

        // Generar código único de viaje
        $codigoViaje = 'VJ-' . date('Ymd') . '-' . rand(100, 999);

        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (
                codigo_viaje, ruta_id, embarcacion_id, capitan_id, 
                fecha_salida, hora_salida, precio_pasaje, 
                cupos_disponibles, capacidad_carga_disponible_kg, estado, observaciones
            )
            VALUES (
                :codigo_viaje, :ruta_id, :embarcacion_id, :capitan_id,
                :fecha_salida, :hora_salida, :precio_pasaje,
                :cupos_disponibles, :capacidad_carga_disponible_kg, :estado, :observaciones
            )
        ");
        $stmt->execute([
            'codigo_viaje'                  => $codigoViaje,
            'ruta_id'                       => (int)$data['ruta_id'],
            'embarcacion_id'                => (int)$data['embarcacion_id'],
            'capitan_id'                    => !empty($data['capitan_id']) ? (int)$data['capitan_id'] : null,
            'fecha_salida'                  => $data['fecha_salida'],
            'hora_salida'                   => $data['hora_salida'],
            'precio_pasaje'                 => (float)$data['precio_pasaje'],
            'cupos_disponibles'             => $cupos,
            'capacidad_carga_disponible_kg' => $cargaDisp,
            'estado'                        => $data['estado'] ?? 'programado',
            'observaciones'                 => $data['observaciones'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateEstado(int $id, string $estado): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET estado = :estado WHERE id = :id");
        return $stmt->execute(['id' => $id, 'estado' => $estado]);
    }

    public function updatePrecio(int $id, float $precio): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET precio_pasaje = :precio WHERE id = :id");
        return $stmt->execute(['id' => $id, 'precio' => $precio]);
    }

    public function updateItinerario(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `{$this->table}`
            SET capitan_id = :capitan_id,
                fecha_salida = :fecha_salida,
                hora_salida = :hora_salida,
                precio_pasaje = :precio_pasaje,
                observaciones = :observaciones
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'             => $id,
            'capitan_id'     => !empty($data['capitan_id']) ? (int)$data['capitan_id'] : null,
            'fecha_salida'   => $data['fecha_salida'],
            'hora_salida'    => $data['hora_salida'],
            'precio_pasaje'  => (float)$data['precio_pasaje'],
            'observaciones'  => $data['observaciones'] ?? null,
        ]);
    }

    public function getViajesDisponibles(array $filtros = []): array {
        $sql = "
            SELECT v.*,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   mo.departamento AS origen_depto, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
                   md.departamento AS destino_depto, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula, e.tipo AS embarcacion_tipo,
                   u.nombre AS capitan_nombre
            FROM `{$this->table}` v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON v.capitan_id = u.id
            WHERE v.estado IN ('programado', 'en_embarque') AND v.cupos_disponibles > 0
        ";
        $params = [];

        if (!empty($filtros['departamento'])) {
            $sql .= " AND (mo.departamento = :depto_orig OR md.departamento = :depto_dest) ";
            $params['depto_orig'] = $filtros['departamento'];
            $params['depto_dest'] = $filtros['departamento'];
        }

        if (!empty($filtros['origen_id'])) {
            $sql .= " AND r.muelle_origen_id = :origen_id ";
            $params['origen_id'] = (int)$filtros['origen_id'];
        }

        if (!empty($filtros['destino_id'])) {
            $sql .= " AND r.muelle_destino_id = :destino_id ";
            $params['destino_id'] = (int)$filtros['destino_id'];
        }

        if (!empty($filtros['fecha'])) {
            $sql .= " AND v.fecha_salida = :fecha ";
            $params['fecha'] = $filtros['fecha'];
        }

        $sql .= " ORDER BY v.fecha_salida ASC, v.hora_salida ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Busca fechas alternativas donde una ruta específica (o entre muelles) tenga viajes disponibles
     */
    public function getFechasAlternativasConViajes(int $origenId = 0, int $destinoId = 0, string $fechaExcluida = '', string $departamento = ''): array {
        $sql = "
            SELECT v.id AS viaje_id, v.codigo_viaje, v.fecha_salida, v.hora_salida, v.precio_pasaje, v.cupos_disponibles,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.id AS origen_id, mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   md.id AS destino_id, md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   e.nombre AS embarcacion_nombre
            FROM `{$this->table}` v
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE v.estado IN ('programado', 'en_embarque') AND v.cupos_disponibles > 0
        ";
        $params = [];

        if (!empty($departamento)) {
            $sql .= " AND (mo.departamento = :depto_orig OR md.departamento = :depto_dest) ";
            $params['depto_orig'] = $departamento;
            $params['depto_dest'] = $departamento;
        }

        if ($origenId > 0) {
            $sql .= " AND r.muelle_origen_id = :origen_id ";
            $params['origen_id'] = $origenId;
        }

        if ($destinoId > 0) {
            $sql .= " AND r.muelle_destino_id = :destino_id ";
            $params['destino_id'] = $destinoId;
        }

        if (!empty($fechaExcluida)) {
            $sql .= " AND v.fecha_salida != :fecha_excluida ";
            $params['fecha_excluida'] = $fechaExcluida;
        }

        $sql .= " ORDER BY v.fecha_salida ASC, v.hora_salida ASC LIMIT 10";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
