<?php
// =======================================================
// Modelo: Embarcacion (Flota Fluvial)
// =======================================================

require_once __DIR__ . '/Model.php';

class Embarcacion extends Model {
    protected string $table = 'embarcaciones';

    public function getOperativas(): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE estado = 'operativo' ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (nombre, matricula, tipo, capacidad_pasajeros, capacidad_carga_kg, estado)
            VALUES (:nombre, :matricula, :tipo, :capacidad_pasajeros, :capacidad_carga_kg, :estado)
        ");
        $stmt->execute([
            'nombre'              => $data['nombre'],
            'matricula'           => strtoupper(trim($data['matricula'])),
            'tipo'                => $data['tipo'],
            'capacidad_pasajeros' => (int)$data['capacidad_pasajeros'],
            'capacidad_carga_kg'  => (float)$data['capacidad_carga_kg'],
            'estado'              => $data['estado'] ?? 'operativo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `{$this->table}`
            SET nombre = :nombre, matricula = :matricula, tipo = :tipo, 
                capacidad_pasajeros = :capacidad_pasajeros, capacidad_carga_kg = :capacidad_carga_kg, estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'                  => $id,
            'nombre'              => $data['nombre'],
            'matricula'           => strtoupper(trim($data['matricula'])),
            'tipo'                => $data['tipo'],
            'capacidad_pasajeros' => (int)$data['capacidad_pasajeros'],
            'capacidad_carga_kg'  => (float)$data['capacidad_carga_kg'],
            'estado'              => $data['estado'] ?? 'operativo'
        ]);
    }
}
