<?php
// =======================================================
// Modelo: Carga (Control de Fletes y Encomiendas)
// =======================================================

require_once __DIR__ . '/Model.php';

class Carga extends Model {
    protected string $table = 'cargas_encomiendas';

    public function allWithDetails(?string $departamentoFilter = null): array {
        $sql = "
            SELECT c.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida,
                   mo.nombre AS origen_nombre, mo.departamento AS origen_depto,
                   md.nombre AS destino_nombre, md.departamento AS destino_depto,
                   e.nombre AS embarcacion_nombre,
                   u.nombre AS registrado_por_nombre
            FROM `{$this->table}` c
            JOIN viajes v ON c.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON c.registrado_por_id = u.id
        ";
        if (!empty($departamentoFilter)) {
            $sql .= " WHERE (mo.departamento = :depto1 OR md.departamento = :depto2) ";
            $sql .= " ORDER BY c.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'depto1' => $departamentoFilter,
                'depto2' => $departamentoFilter
            ]);
            return $stmt->fetchAll();
        }
        $sql .= " ORDER BY c.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithDetails(int $id): ?array {
        $sql = "
            SELECT c.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula,
                   u.nombre AS registrado_por_nombre
            FROM `{$this->table}` c
            JOIN viajes v ON c.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON c.registrado_por_id = u.id
            WHERE c.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByViaje(int $viajeId): array {
        $sql = "
            SELECT * FROM `{$this->table}`
            WHERE viaje_id = :viaje_id AND estado != 'cancelada'
            ORDER BY id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['viaje_id' => $viajeId]);
        return $stmt->fetchAll();
    }

    public function findByGuia(string $guiaNumero): ?array {
        $sql = "
            SELECT c.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida, v.estado AS viaje_estado,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula,
                   u.nombre AS registrado_por_nombre
            FROM `{$this->table}` c
            JOIN viajes v ON c.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON c.registrado_por_id = u.id
            WHERE c.guia_numero = :guia_numero
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['guia_numero' => trim($guiaNumero)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getByUsuario(int $usuarioId): array {
        $sql = "
            SELECT c.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida, v.estado AS viaje_estado,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio,
                   e.nombre AS embarcacion_nombre
            FROM `{$this->table}` c
            JOIN viajes v ON c.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE c.usuario_id = :usuario_id
            ORDER BY c.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function registrar(array $data): int {
        $this->db->beginTransaction();
        try {
            $peso = (float)$data['peso_kg'];

            // Verificar capacidad disponible
            $stmtViaje = $this->db->prepare("SELECT capacidad_carga_disponible_kg, estado FROM viajes WHERE id = :id FOR UPDATE");
            $stmtViaje->execute(['id' => (int)$data['viaje_id']]);
            $viaje = $stmtViaje->fetch();

            if (!$viaje) {
                throw new Exception("El viaje seleccionado no existe.");
            }
            if (!in_array($viaje['estado'], ['programado', 'en_embarque'], true)) {
                throw new Exception("El viaje no admite registro de carga en su estado actual.");
            }
            if ((float)$viaje['capacidad_carga_disponible_kg'] < $peso) {
                throw new Exception("El peso de la carga excede la capacidad de bodega disponible para este viaje.");
            }

            // Generar número de guía único
            $guiaNumero = 'GUIA-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            $stmt = $this->db->prepare("
                INSERT INTO `{$this->table}` (
                    viaje_id, usuario_id, guia_numero, remitente_nombre, remitente_telefono,
                    destinatario_nombre, destinatario_telefono, descripcion_carga,
                    peso_kg, valor_declarado, valor_flete, estado, registrado_por_id
                )
                VALUES (
                    :viaje_id, :usuario_id, :guia_numero, :remitente_nombre, :remitente_telefono,
                    :destinatario_nombre, :destinatario_telefono, :descripcion_carga,
                    :peso_kg, :valor_declarado, :valor_flete, :estado, :registrado_por_id
                )
            ");
            $stmt->execute([
                'viaje_id'              => (int)$data['viaje_id'],
                'usuario_id'            => !empty($data['usuario_id']) ? (int)$data['usuario_id'] : null,
                'guia_numero'           => $guiaNumero,
                'remitente_nombre'      => trim($data['remitente_nombre']),
                'remitente_telefono'    => trim($data['remitente_telefono']),
                'destinatario_nombre'   => trim($data['destinatario_nombre']),
                'destinatario_telefono' => trim($data['destinatario_telefono']),
                'descripcion_carga'     => trim($data['descripcion_carga']),
                'peso_kg'               => $peso,
                'valor_declarado'       => (float)($data['valor_declarado'] ?? 0),
                'valor_flete'           => (float)$data['valor_flete'],
                'estado'                => 'registrada',
                'registrado_por_id'     => !empty($data['registrado_por_id']) ? (int)$data['registrado_por_id'] : null
            ]);
            $cargaId = (int)$this->db->lastInsertId();

            // Descontar kilos disponibles en el viaje
            $stmtUpdateViaje = $this->db->prepare("UPDATE viajes SET capacidad_carga_disponible_kg = capacidad_carga_disponible_kg - :peso WHERE id = :id");
            $stmtUpdateViaje->execute(['id' => (int)$data['viaje_id'], 'peso' => $peso]);

            $this->db->commit();
            return $cargaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateEstado(int $id, string $estado): bool {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT id, viaje_id, peso_kg, estado FROM `{$this->table}` WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $id]);
            $carga = $stmt->fetch();
            if (!$carga) {
                $this->db->rollBack();
                return false;
            }

            $estadoAnterior = $carga['estado'];
            $upd = $this->db->prepare("UPDATE `{$this->table}` SET estado = :estado WHERE id = :id");
            $upd->execute(['id' => $id, 'estado' => $estado]);

            if ($estadoAnterior !== 'cancelada' && $estado === 'cancelada') {
                $rest = $this->db->prepare("UPDATE viajes SET capacidad_carga_disponible_kg = capacidad_carga_disponible_kg + :peso WHERE id = :id");
                $rest->execute(['peso' => (float)$carga['peso_kg'], 'id' => (int)$carga['viaje_id']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
