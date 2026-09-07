<?php
// =======================================================
// Modelo: Rio (Ríos Navegables de Colombia)
// =======================================================

require_once __DIR__ . '/Model.php';

class Rio extends Model {
    protected string $table = 'rios';

    public function all(string $orderBy = 'longitud_navegable_km DESC'): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE estado = 'activo' ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function getDestacados(int $limit = 8): array {
        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}` 
            WHERE estado = 'activo' 
            ORDER BY longitud_navegable_km DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByNombre(string $nombre): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE nombre = :nombre LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByDepartamento(string $departamento): array {
        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}` 
            WHERE estado = 'activo' AND departamentos_que_conecta LIKE :dep 
            ORDER BY longitud_navegable_km DESC
        ");
        $stmt->execute(['dep' => '%' . $departamento . '%']);
        return $stmt->fetchAll();
    }

    public function getCuencas(): array {
        $stmt = $this->db->query("
            SELECT cuenca, COUNT(*) as total_rios, SUM(longitud_navegable_km) as total_km_navegables 
            FROM `{$this->table}` 
            WHERE estado = 'activo' 
            GROUP BY cuenca 
            ORDER BY total_km_navegables DESC
        ");
        return $stmt->fetchAll();
    }
}
