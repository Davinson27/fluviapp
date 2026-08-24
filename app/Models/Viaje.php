<?php
// =======================================================
// Modelo: Viaje (Itinerarios, Zarpes y Manifiestos)
// =======================================================

require_once __DIR__ . '/Model.php';

class Viaje extends Model {
    protected string $table = 'viajes';

    public function allWithDetails(string $estadoFilter = null): array {
        $sql = "
            SELECT v.*,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio,
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
        ";
        if ($estadoFilter) {
            $sql .= " WHERE v.estado = :estado ";
        }
        $sql .= " ORDER BY v.fecha_salida DESC, v.hora_salida DESC";

        if ($estadoFilter) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['estado' => $estadoFilter]);
            return $stmt->fetchAll();
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithDetails(int $id): ?array {
        $sql = "
            SELECT v.*,
                   r.distancia_km, r.duracion_estimada_min, r.tarifa_base,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
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

    public function descontarCupo(int $id): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET cupos_disponibles = cupos_disponibles - 1 WHERE id = :id AND cupos_disponibles > 0");
        return $stmt->execute(['id' => $id]);
    }

    public function descontarCarga(int $id, float $kilos): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET capacidad_carga_disponible_kg = capacidad_carga_disponible_kg - :kilos WHERE id = :id AND capacidad_carga_disponible_kg >= :kilos");
        return $stmt->execute(['id' => $id, 'kilos' => $kilos]);
    }
}
