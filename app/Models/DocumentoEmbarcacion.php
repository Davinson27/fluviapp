<?php
// =======================================================
// Modelo: DocumentoEmbarcacion - FluviApp v2.0
// Custodia documental DIMAR / Pólizas con Semáforo de Vencimiento
// =======================================================

require_once __DIR__ . '/Model.php';

class DocumentoEmbarcacion extends Model {
    protected string $table = 'embarcaciones_documentos';

    public function allConSemaforo(): array {
        $stmt = $this->db->query("
            SELECT d.*, e.nombre as embarcacion_nombre, e.matricula as embarcacion_matricula,
                   DATEDIFF(d.fecha_vencimiento, CURDATE()) as dias_restantes,
                   CASE 
                       WHEN DATEDIFF(d.fecha_vencimiento, CURDATE()) < 0 THEN 'vencido'
                       WHEN DATEDIFF(d.fecha_vencimiento, CURDATE()) <= 30 THEN 'por_vencer'
                       ELSE 'vigente'
                   END as estado_semaforo
            FROM {$this->table} d
            JOIN embarcaciones e ON d.embarcacion_id = e.id
            ORDER BY d.fecha_vencimiento ASC
        ");
        return $stmt->fetchAll();
    }

    public function crear(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (embarcacion_id, tipo_documento, numero_documento, entidad_emisora, fecha_expedicion, fecha_vencimiento, observaciones)
            VALUES
                (:embarcacion_id, :tipo, :numero, :entidad, :exp, :venc, :obs)
        ");
        $stmt->execute([
            ':embarcacion_id' => (int)$data['embarcacion_id'],
            ':tipo'           => $data['tipo_documento'],
            ':numero'         => trim($data['numero_documento']),
            ':entidad'        => trim($data['entidad_emisora'] ?? 'DIMAR / Mintransporte'),
            ':exp'            => $data['fecha_expedicion'],
            ':venc'           => $data['fecha_vencimiento'],
            ':obs'            => trim($data['observaciones'] ?? '')
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAlertasResumen(): array {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_documentos,
                SUM(CASE WHEN DATEDIFF(fecha_vencimiento, CURDATE()) < 0 THEN 1 ELSE 0 END) as vencidos,
                SUM(CASE WHEN DATEDIFF(fecha_vencimiento, CURDATE()) BETWEEN 0 AND 30 THEN 1 ELSE 0 END) as por_vencer,
                SUM(CASE WHEN DATEDIFF(fecha_vencimiento, CURDATE()) > 30 THEN 1 ELSE 0 END) as vigentes
            FROM {$this->table}
        ");
        return $stmt->fetch() ?: ['total_documentos' => 0, 'vencidos' => 0, 'por_vencer' => 0, 'vigentes' => 0];
    }
}
