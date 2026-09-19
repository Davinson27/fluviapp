<?php
// =======================================================
// Modelo: Mantenimiento - FluviApp v2.0
// Bitácora técnica y mantenimiento de embarcaciones
// =======================================================

require_once __DIR__ . '/Model.php';

class Mantenimiento extends Model {
    protected string $table = 'embarcaciones_mantenimiento';

    public function allWithEmbarcacion(): array {
        $stmt = $this->db->query("
            SELECT m.*, e.nombre as embarcacion_nombre, e.matricula as embarcacion_matricula, e.tipo as embarcacion_tipo
            FROM {$this->table} m
            JOIN embarcaciones e ON m.embarcacion_id = e.id
            ORDER BY m.fecha_mantenimiento DESC
        ");
        return $stmt->fetchAll();
    }

    public function crear(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (embarcacion_id, tipo_mantenimiento, titulo, descripcion, costo, fecha_mantenimiento, proximo_mantenimiento, responsable, estado)
            VALUES
                (:embarcacion_id, :tipo, :titulo, :descripcion, :costo, :fecha, :proximo, :responsable, :estado)
        ");
        $stmt->execute([
            ':embarcacion_id' => (int)$data['embarcacion_id'],
            ':tipo'           => $data['tipo_mantenimiento'] ?? 'preventivo',
            ':titulo'         => trim($data['titulo']),
            ':descripcion'    => trim($data['descripcion']),
            ':costo'          => (float)($data['costo'] ?? 0.0),
            ':fecha'          => $data['fecha_mantenimiento'] ?? date('Y-m-d'),
            ':proximo'        => !empty($data['proximo_mantenimiento']) ? $data['proximo_mantenimiento'] : null,
            ':responsable'    => trim($data['responsable'] ?? ''),
            ':estado'         => $data['estado'] ?? 'completado'
        ]);
        return (int)$this->db->lastInsertId();
    }
}
