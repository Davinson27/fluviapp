<?php
// =======================================================
// Modelo: Usuario
// =======================================================

require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    protected string $table = 'usuarios';

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getCapitanes(): array {
        $stmt = $this->db->query("SELECT id, nombre, email, telefono FROM `{$this->table}` WHERE rol = 'capitan' AND estado = 'activo' ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (nombre, email, password, rol, estado, telefono)
            VALUES (:nombre, :email, :password, :rol, :estado, :telefono)
        ");
        $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'rol'      => $data['rol'] ?? 'taquilla',
            'estado'   => $data['estado'] ?? 'activo',
            'telefono' => $data['telefono'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}` 
                SET nombre = :nombre, email = :email, password = :password, rol = :rol, estado = :estado, telefono = :telefono
                WHERE id = :id
            ");
            return $stmt->execute([
                'id'       => $id,
                'nombre'   => $data['nombre'],
                'email'    => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'rol'      => $data['rol'],
                'estado'   => $data['estado'],
                'telefono' => $data['telefono'] ?? null,
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}` 
                SET nombre = :nombre, email = :email, rol = :rol, estado = :estado, telefono = :telefono
                WHERE id = :id
            ");
            return $stmt->execute([
                'id'       => $id,
                'nombre'   => $data['nombre'],
                'email'    => $data['email'],
                'rol'      => $data['rol'],
                'estado'   => $data['estado'],
                'telefono' => $data['telefono'] ?? null,
            ]);
        }
    }
}
