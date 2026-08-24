<?php
// =======================================================
// Modelo: Muelle (Puertos Fluviales)
// =======================================================

require_once __DIR__ . '/Model.php';

class Muelle extends Model {
    protected string $table = 'muelles';

    public function getActivos(): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE estado = 'activo' ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (nombre, rio, municipio, departamento, estado)
            VALUES (:nombre, :rio, :municipio, :departamento, :estado)
        ");
        $stmt->execute([
            'nombre'       => $data['nombre'],
            'rio'          => $data['rio'],
            'municipio'    => $data['municipio'],
            'departamento' => $data['departamento'],
            'estado'       => $data['estado'] ?? 'activo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `{$this->table}`
            SET nombre = :nombre, rio = :rio, municipio = :municipio, departamento = :departamento, estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'           => $id,
            'nombre'       => $data['nombre'],
            'rio'          => $data['rio'],
            'municipio'    => $data['municipio'],
            'departamento' => $data['departamento'],
            'estado'       => $data['estado'] ?? 'activo'
        ]);
    }
}
