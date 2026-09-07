<?php
// =======================================================
// Modelo: Usuario
// =======================================================

require_once __DIR__ . '/Model.php';

class Usuario extends Model {
    protected string $table = 'usuarios';

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM `{$this->table}` WHERE email = :email AND id != :excludeId");
        $stmt->execute(['email' => $email, 'excludeId' => $excludeId]);
        $row = $stmt->fetch();
        return ((int)($row['total'] ?? 0)) > 0;
    }

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

    public function allWithFilter(?string $departamento = null): array {
        if (!empty($departamento)) {
            $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE departamento = :departamento ORDER BY id ASC");
            $stmt->execute(['departamento' => $departamento]);
            return $stmt->fetchAll();
        }
        return $this->all('id ASC');
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (nombre, email, documento, password, rol, estado, telefono, departamento, genero, foto)
            VALUES (:nombre, :email, :documento, :password, :rol, :estado, :telefono, :departamento, :genero, :foto)
        ");
        $stmt->execute([
            'nombre'       => $data['nombre'],
            'email'        => $data['email'],
            'documento'    => $data['documento'] ?? null,
            'password'     => password_hash($data['password'], PASSWORD_BCRYPT),
            'rol'          => $data['rol'] ?? 'cliente',
            'estado'       => $data['estado'] ?? 'activo',
            'telefono'     => $data['telefono'] ?? null,
            'departamento' => $data['departamento'] ?? null,
            'genero'       => in_array($data['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $data['genero'] : 'masculino',
            'foto'         => $data['foto'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $genero = in_array($data['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $data['genero'] : 'masculino';
        $fotoSql = array_key_exists('foto', $data) ? ", foto = :foto" : "";

        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}` 
                SET nombre = :nombre, email = :email, documento = :documento, password = :password, rol = :rol, estado = :estado, telefono = :telefono, departamento = :departamento, genero = :genero {$fotoSql}
                WHERE id = :id
            ");
            $params = [
                'id'           => $id,
                'nombre'       => $data['nombre'],
                'email'        => $data['email'],
                'documento'    => $data['documento'] ?? null,
                'password'     => password_hash($data['password'], PASSWORD_BCRYPT),
                'rol'          => $data['rol'],
                'estado'       => $data['estado'],
                'telefono'     => $data['telefono'] ?? null,
                'departamento' => $data['departamento'] ?? null,
                'genero'       => $genero,
            ];
            if (array_key_exists('foto', $data)) {
                $params['foto'] = $data['foto'];
            }
            return $stmt->execute($params);
        } else {
            $stmt = $this->db->prepare("
                UPDATE `{$this->table}` 
                SET nombre = :nombre, email = :email, documento = :documento, rol = :rol, estado = :estado, telefono = :telefono, departamento = :departamento, genero = :genero {$fotoSql}
                WHERE id = :id
            ");
            $params = [
                'id'           => $id,
                'nombre'       => $data['nombre'],
                'email'        => $data['email'],
                'documento'    => $data['documento'] ?? null,
                'rol'          => $data['rol'],
                'estado'       => $data['estado'],
                'telefono'     => $data['telefono'] ?? null,
                'departamento' => $data['departamento'] ?? null,
                'genero'       => $genero,
            ];
            if (array_key_exists('foto', $data)) {
                $params['foto'] = $data['foto'];
            }
            return $stmt->execute($params);
        }
    }

    public function updatePerfil(int $id, array $data): bool {
        $fields = [
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'genero'   => in_array($data['genero'] ?? '', ['masculino', 'femenino', 'otro']) ? $data['genero'] : 'masculino'
        ];
        $sql = "UPDATE `{$this->table}` SET nombre = :nombre, email = :email, telefono = :telefono, genero = :genero";

        if (!empty($data['documento'])) {
            $sql .= ", documento = :documento";
            $fields['documento'] = $data['documento'];
        }
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $fields['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (array_key_exists('foto', $data)) {
            $sql .= ", foto = :foto";
            $fields['foto'] = $data['foto'];
        }

        $sql .= " WHERE id = :id";
        $fields['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($fields);
    }
}
