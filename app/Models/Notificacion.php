<?php
// =======================================================
// Modelo: Notificacion (Sistema de Notificaciones FluviApp)
// =======================================================

require_once __DIR__ . '/Model.php';

class Notificacion extends Model {
    protected string $table = 'notificaciones';

    public function crear(int $usuarioId, string $titulo, string $mensaje, ?string $enlace = null, string $tipo = 'encomienda_entregada'): int {
        $sql = "INSERT INTO `{$this->table}` (usuario_id, tipo, titulo, mensaje, enlace, leida, created_at)
                VALUES (:usuario_id, :tipo, :titulo, :mensaje, :enlace, 0, CURRENT_TIMESTAMP)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'enlace'     => $enlace
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUsuario(int $usuarioId, int $limit = 8): array {
        $limit = max(1, min(50, $limit));
        $sql = "SELECT * FROM `{$this->table}`
                WHERE usuario_id = :usuario_id
                ORDER BY id DESC
                LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function countNoLeidas(int $usuarioId): int {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE usuario_id = :usuario_id AND leida = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return (int)$stmt->fetchColumn();
    }

    public function marcarLeida(int $id, int $usuarioId): bool {
        $sql = "UPDATE `{$this->table}` SET leida = 1 WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id, 'usuario_id' => $usuarioId]);
    }

    public function marcarTodasLeidas(int $usuarioId): bool {
        $sql = "UPDATE `{$this->table}` SET leida = 1 WHERE usuario_id = :usuario_id AND leida = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['usuario_id' => $usuarioId]);
    }
}