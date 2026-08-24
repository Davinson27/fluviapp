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
                   mo.nombre AS origen_nombre, mo.rio AS origen_rio, mo.municipio AS origen_municipio,
                   md.nombre AS destino_nombre, md.rio AS destino_rio, md.municipio AS destino_municipio
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
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio
            FROM `{$this->table}` r
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            WHERE r.estado = 'activa'
            ORDER BY mo.nombre ASC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithMuelles(int $id): ?array {
        $sql = "
            SELECT r.*, 
                   mo.nombre AS origen_nombre, md.nombre AS destino_nombre
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
}
