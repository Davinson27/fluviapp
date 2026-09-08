<?php
// =======================================================
// Modelo: Soporte (Incidencias y Reclamaciones de Cargas)
// =======================================================

require_once __DIR__ . '/Model.php';

class Soporte extends Model {
    protected string $table = 'soporte_incidencias';

    public function crearIncidencia(array $data): int {
        $sql = "INSERT INTO `{$this->table}` (
                    carga_id, usuario_id, guia_numero, contacto_nombre,
                    contacto_telefono, contacto_email, asunto, mensaje, estado, created_at
                ) VALUES (
                    :carga_id, :usuario_id, :guia_numero, :contacto_nombre,
                    :contacto_telefono, :contacto_email, :asunto, :mensaje, 'abierto', CURRENT_TIMESTAMP
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'carga_id'          => !empty($data['carga_id']) ? (int)$data['carga_id'] : null,
            'usuario_id'        => !empty($data['usuario_id']) ? (int)$data['usuario_id'] : null,
            'guia_numero'       => $data['guia_numero'] ?? null,
            'contacto_nombre'   => trim($data['contacto_nombre'] ?? ''),
            'contacto_telefono' => trim($data['contacto_telefono'] ?? ''),
            'contacto_email'    => trim($data['contacto_email'] ?? ''),
            'asunto'            => trim($data['asunto'] ?? 'Encomienda no entregada / Retraso'),
            'mensaje'           => trim($data['mensaje'] ?? '')
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUsuario(int $usuarioId): array {
        $sql = "SELECT s.*, c.descripcion_carga, c.estado AS carga_estado
                FROM `{$this->table}` s
                LEFT JOIN cargas_encomiendas c ON s.carga_id = c.id
                WHERE s.usuario_id = :usuario_id
                ORDER BY s.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }
}