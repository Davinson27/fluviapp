<?php
// =======================================================
// Modelo: Boleto (Venta y Emisión de Pasajes)
// =======================================================

require_once __DIR__ . '/Model.php';

class Boleto extends Model {
    protected string $table = 'boletos';

    public function allWithViaje(?string $departamentoFilter = null): array {
        $sql = "
            SELECT b.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida,
                   mo.nombre AS origen_nombre, mo.departamento AS origen_depto,
                   md.nombre AS destino_nombre, md.departamento AS destino_depto,
                   e.nombre AS embarcacion_nombre,
                   u.nombre AS vendedor_nombre
            FROM `{$this->table}` b
            JOIN viajes v ON b.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON b.vendido_por_id = u.id
        ";
        if (!empty($departamentoFilter)) {
            $sql .= " WHERE (mo.departamento = :depto1 OR md.departamento = :depto2) ";
            $sql .= " ORDER BY b.id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'depto1' => $departamentoFilter,
                'depto2' => $departamentoFilter
            ]);
            return $stmt->fetchAll();
        }
        $sql .= " ORDER BY b.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithDetails(int $id): ?array {
        $sql = "
            SELECT b.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula,
                   u.nombre AS vendedor_nombre,
                   cap.nombre AS capitan_nombre
            FROM `{$this->table}` b
            JOIN viajes v ON b.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON b.vendido_por_id = u.id
            LEFT JOIN usuarios cap ON v.capitan_id = cap.id
            WHERE b.id = :id
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
            WHERE viaje_id = :viaje_id AND estado != 'cancelado'
            ORDER BY id ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['viaje_id' => $viajeId]);
        return $stmt->fetchAll();
    }

    public function getByUsuario(int $usuarioId): array {
        $sql = "
            SELECT b.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida, v.estado AS viaje_estado,
                   r.distancia_km, r.duracion_estimada_min,
                   mo.nombre AS origen_nombre, mo.municipio AS origen_municipio, mo.rio AS origen_rio,
                   md.nombre AS destino_nombre, md.municipio AS destino_municipio, md.rio AS destino_rio,
                   e.nombre AS embarcacion_nombre, e.matricula AS embarcacion_matricula,
                   cap.nombre AS capitan_nombre
            FROM `{$this->table}` b
            JOIN viajes v ON b.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios cap ON v.capitan_id = cap.id
            WHERE b.usuario_id = :usuario_id
            ORDER BY b.id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function emitir(array $data): int {
        $this->db->beginTransaction();
        try {
            $stmtViaje = $this->db->prepare("
                SELECT v.cupos_disponibles, v.precio_pasaje, v.estado,
                       e.capacidad_pasajeros
                FROM viajes v
                JOIN embarcaciones e ON e.id = v.embarcacion_id
                WHERE v.id = :id
                FOR UPDATE
            ");
            $stmtViaje->execute(['id' => (int)$data['viaje_id']]);
            $viaje = $stmtViaje->fetch();

            if (!$viaje) {
                throw new Exception("El viaje seleccionado no existe.");
            }
            if (!in_array($viaje['estado'], ['programado', 'en_embarque'], true)) {
                throw new Exception("El viaje no admite venta de pasajes en su estado actual.");
            }
            if ((int)$viaje['cupos_disponibles'] <= 0) {
                throw new Exception("No hay cupos disponibles en el viaje seleccionado.");
            }

            $codigoBoleto = 'BOL-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            $stmtAsiento = $this->db->prepare("SELECT COUNT(*) AS total FROM boletos WHERE viaje_id = :viaje_id AND estado != 'cancelado'");
            $stmtAsiento->execute(['viaje_id' => (int)$data['viaje_id']]);
            $rowAsiento = $stmtAsiento->fetch();
            $numAsiento = ((int)($rowAsiento['total'] ?? 0)) + 1;

            $capacidad = (int)$viaje['capacidad_pasajeros'];
            if ($capacidad > 0 && $numAsiento > $capacidad) {
                throw new Exception("No hay asientos disponibles en la embarcación.");
            }

            $precio = (float)$viaje['precio_pasaje'];
            if (!empty($data['allow_custom_price']) && isset($data['precio_pagado']) && (float)$data['precio_pagado'] > 0) {
                $precio = (float)$data['precio_pagado'];
            }

            $metodo = $data['metodo_pago'] ?? 'efectivo';
            if (!in_array($metodo, ['efectivo', 'transferencia', 'tarjeta'], true)) {
                $metodo = 'efectivo';
            }

            $stmtInsert = $this->db->prepare("
                INSERT INTO `{$this->table}` (
                    viaje_id, usuario_id, codigo_boleto, pasajero_documento, pasajero_nombre, 
                    pasajero_telefono, numero_asiento, precio_pagado, metodo_pago, estado, vendido_por_id
                )
                VALUES (
                    :viaje_id, :usuario_id, :codigo_boleto, :pasajero_documento, :pasajero_nombre,
                    :pasajero_telefono, :numero_asiento, :precio_pagado, :metodo_pago, :estado, :vendido_por_id
                )
            ");
            $stmtInsert->execute([
                'viaje_id'           => (int)$data['viaje_id'],
                'usuario_id'         => !empty($data['usuario_id']) ? (int)$data['usuario_id'] : null,
                'codigo_boleto'      => $codigoBoleto,
                'pasajero_documento' => trim($data['pasajero_documento']),
                'pasajero_nombre'    => trim($data['pasajero_nombre']),
                'pasajero_telefono'  => trim($data['pasajero_telefono'] ?? ''),
                'numero_asiento'     => $numAsiento,
                'precio_pagado'      => $precio,
                'metodo_pago'        => $metodo,
                'estado'             => 'valido',
                'vendido_por_id'     => !empty($data['vendido_por_id']) ? (int)$data['vendido_por_id'] : null
            ]);
            $boletoId = (int)$this->db->lastInsertId();

            // Descontar cupo del viaje
            $stmtUpdateViaje = $this->db->prepare("UPDATE viajes SET cupos_disponibles = cupos_disponibles - 1 WHERE id = :id AND cupos_disponibles > 0");
            $stmtUpdateViaje->execute(['id' => (int)$data['viaje_id']]);
            if ($stmtUpdateViaje->rowCount() !== 1) {
                throw new Exception("No hay cupos disponibles en el viaje seleccionado.");
            }

            $this->db->commit();
            return $boletoId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
