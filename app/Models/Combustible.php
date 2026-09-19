<?php
// =======================================================
// Modelo: Combustible - FluviApp v2.0
// Control de abastecimiento, consumo y rendimiento fluvial
// =======================================================

require_once __DIR__ . '/Model.php';

class Combustible extends Model {
    protected string $table = 'embarcaciones_combustible';

    public function allWithEmbarcacion(): array {
        $stmt = $this->db->query("
            SELECT c.*, e.nombre as embarcacion_nombre, e.matricula as embarcacion_matricula,
                   v.codigo_viaje
            FROM {$this->table} c
            JOIN embarcaciones e ON c.embarcacion_id = e.id
            LEFT JOIN viajes v ON c.viaje_id = v.id
            ORDER BY c.fecha DESC
        ");
        return $stmt->fetchAll();
    }

    public function crear(array $data): int {
        $galones = (float)$data['galones'];
        $precioGalon = (float)$data['precio_por_galon'];
        $total = (float)($data['total_pagado'] ?? ($galones * $precioGalon));

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (embarcacion_id, viaje_id, tipo_combustible, galones, precio_por_galon, total_pagado, fecha, proveedor, horometro)
            VALUES
                (:embarcacion_id, :viaje_id, :tipo, :galones, :precio, :total, :fecha, :proveedor, :horometro)
        ");
        $stmt->execute([
            ':embarcacion_id' => (int)$data['embarcacion_id'],
            ':viaje_id'       => !empty($data['viaje_id']) ? (int)$data['viaje_id'] : null,
            ':tipo'           => $data['tipo_combustible'] ?? 'gasolina',
            ':galones'        => $galones,
            ':precio'         => $precioGalon,
            ':total'          => $total,
            ':fecha'          => $data['fecha'] ?? date('Y-m-d'),
            ':proveedor'      => trim($data['proveedor'] ?? ''),
            ':horometro'      => !empty($data['horometro']) ? (float)$data['horometro'] : null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getMetricas(): array {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(SUM(galones), 0) as total_galones,
                COALESCE(SUM(total_pagado), 0) as total_gasto_combustible,
                COUNT(*) as total_tanqueos
            FROM {$this->table}
        ");
        return $stmt->fetch() ?: ['total_galones' => 0, 'total_gasto_combustible' => 0, 'total_tanqueos' => 0];
    }
}
