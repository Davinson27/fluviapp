<?php
// =======================================================
// Modelo: Ruta (Rutas Fluviales)
// =======================================================

require_once __DIR__ . '/Model.php';

class Ruta extends Model {
    protected string $table = 'rutas';

    public function allWithMuelles(): array {
        $sql = "
            SELECT r.*, 
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            ORDER BY r.id DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function getActivas(): array {
        $sql = "
            SELECT r.*, 
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.estado = 'activa'
            ORDER BY mo.departamento ASC, mo.municipio ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function allWithMuellesAndFilters(array $filtros = []): array {
        $sql = "
            SELECT r.*, 
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.estado = 'activa'
        ";
        $params = [];

        if (!empty($filtros['departamento'])) {
            $sql .= " AND (mo.departamento = :dep1 OR md.departamento = :dep2) ";
            $params['dep1'] = $filtros['departamento'];
            $params['dep2'] = $filtros['departamento'];
        }

        if (!empty($filtros['rio'])) {
            $sql .= " AND (mo.rio = :rio1 OR md.rio = :rio2) ";
            $params['rio1'] = $filtros['rio'];
            $params['rio2'] = $filtros['rio'];
        }

        if (!empty($filtros['origen_id'])) {
            $sql .= " AND r.muelle_origen_id = :origen_id ";
            $params['origen_id'] = (int)$filtros['origen_id'];
        }

        if (!empty($filtros['destino_id'])) {
            $sql .= " AND r.muelle_destino_id = :destino_id ";
            $params['destino_id'] = (int)$filtros['destino_id'];
        }

        if (!empty($filtros['max_precio'])) {
            $sql .= " AND r.tarifa_base <= :max_precio ";
            $params['max_precio'] = (float)$filtros['max_precio'];
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (
                mo.nombre LIKE :q1 OR mo.municipio LIKE :q2 OR mo.departamento LIKE :q3 OR
                md.nombre LIKE :q4 OR md.municipio LIKE :q5 OR md.departamento LIKE :q6 OR
                mo.rio LIKE :q7 OR md.rio LIKE :q8
            ) ";
            $qLike = '%' . $filtros['q'] . '%';
            for ($i = 1; $i <= 8; $i++) {
                $params['q' . $i] = $qLike;
            }
        }

        $sql .= " ORDER BY mo.departamento ASC, r.tarifa_base ASC ";

        if (!empty($filtros['limit'])) {
            $sql .= " LIMIT " . (int)$filtros['limit'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getRutasConCoordenadas(): array {
        $sql = "
            SELECT r.id, r.distancia_km, r.duracion_estimada_min, r.tarifa_base,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   mo.rio AS origen_rio, mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   md.rio AS destino_rio, md.latitud AS destino_lat, md.longitud AS destino_lng
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.estado = 'activa' 
              AND mo.latitud IS NOT NULL AND mo.longitud IS NOT NULL
              AND md.latitud IS NOT NULL AND md.longitud IS NOT NULL
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithMuelles(int $id): ?array {
        $sql = "
            SELECT r.*, 
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   mo.latitud AS origen_lat, mo.longitud AS origen_lng,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio, md.departamento AS destino_departamento,
                   md.latitud AS destino_lat, md.longitud AS destino_lng
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (muelle_origen_id, muelle_destino_id, distancia_km, duracion_estimada_min, tarifa_base, estado)
            VALUES (:muelle_origen_id, :muelle_destino_id, :distancia_km, :duracion_estimada_min, :tarifa_base, :estado)
        ");
        $stmt->execute([
            'muelle_origen_id'      => (int)$data['muelle_origen_id'],
            'muelle_destino_id'     => (int)$data['muelle_destino_id'],
            'distancia_km'          => (float)$data['distancia_km'],
            'duracion_estimada_min' => (int)$data['duracion_estimada_min'],
            'tarifa_base'           => (float)$data['tarifa_base'],
            'estado'                => $data['estado'] ?? 'activa'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `{$this->table}`
            SET muelle_origen_id = :muelle_origen_id, muelle_destino_id = :muelle_destino_id,
                distancia_km = :distancia_km, duracion_estimada_min = :duracion_estimada_min,
                tarifa_base = :tarifa_base, estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'                    => $id,
            'muelle_origen_id'      => (int)$data['muelle_origen_id'],
            'muelle_destino_id'     => (int)$data['muelle_destino_id'],
            'distancia_km'          => (float)$data['distancia_km'],
            'duracion_estimada_min' => (int)$data['duracion_estimada_min'],
            'tarifa_base'           => (float)$data['tarifa_base'],
            'estado'                => $data['estado'] ?? 'activa'
        ]);
    }

    public function updateTarifa(int $id, float $tarifa): bool {
        $stmt = $this->db->prepare("UPDATE `{$this->table}` SET tarifa_base = :tarifa WHERE id = :id");
        return $stmt->execute(['id' => $id, 'tarifa' => $tarifa]);
    }

    public function findByMuelles(int $origenId, int $destinoId): ?array {
        $sql = "
            SELECT r.*,
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio, mo.departamento AS origen_departamento,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio, md.departamento AS destino_departamento
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.muelle_origen_id = :origen_id AND r.muelle_destino_id = :destino_id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['origen_id' => $origenId, 'destino_id' => $destinoId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
