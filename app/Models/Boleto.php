<?php
// =======================================================
// Modelo: Boleto (Venta y Emisión de Pasajes)
// =======================================================

require_once __DIR__ . '/Model.php';

class Boleto extends Model {
    protected string $table = 'boletos';

    public function allWithViaje(): array {
        $sql = "
            SELECT b.*,
                   v.codigo_viaje, v.fecha_salida, v.hora_salida,
                   mo.nombre AS origen_nombre, md.nombre AS destino_nombre,
                   e.nombre AS embarcacion_nombre,
                   u.nombre AS vendedor_nombre
            FROM `{$this->table}` b
            JOIN viajes v ON b.viaje_id = v.id
            JOIN rutas r ON v.ruta_id = r.id
            JOIN muelles mo ON r.muelle_origen_id = mo.id
            JOIN muelles md ON r.muelle_destino_id = md.id
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            LEFT JOIN usuarios u ON b.vendido_por_id = u.id
            ORDER BY b.id DESC
        ";
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

    public function emitir(array $data): int {
        $this->db->beginTransaction();
        try {
            // Verificar disponibilidad de cupos
            $stmtViaje = $this->db->prepare("SELECT cupos_disponibles, precio_pasaje FROM viajes WHERE id = :id FOR UPDATE");
            $stmtViaje->execute(['id' => (int)$data['viaje_id']]);
            $viaje = $stmtViaje->fetch();

            if (!$viaje || (int)$viaje['cupos_disponibles'] <= 0) {
                throw new Exception("No hay cupos disponibles en el viaje seleccionado.");
            }

            // Generar código único de boleto
            $codigoBoleto = 'BOL-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            // Calcular número de asiento correlativo
            $stmtAsiento = $this->db->prepare("SELECT COUNT(*) AS total FROM boletos WHERE viaje_id = :viaje_id AND estado != 'cancelado'");
            $stmtAsiento->execute(['viaje_id' => (int)$data['viaje_id']]);
            $rowAsiento = $stmtAsiento->fetch();
            $numAsiento = ((int)($rowAsiento['total'] ?? 0)) + 1;

            $precio = !empty($data['precio_pagado']) ? (float)$data['precio_pagado'] : (float)$viaje['precio_pasaje'];

            $stmtInsert = $this->db->prepare("
                INSERT INTO `{$this->table}` (
                    viaje_id, codigo_boleto, pasajero_documento, pasajero_nombre, 
                    pasajero_telefono, numero_asiento, precio_pagado, metodo_pago, estado, vendido_por_id
                )
                VALUES (
                    :viaje_id, :codigo_boleto, :pasajero_documento, :pasajero_nombre,
                    :pasajero_telefono, :numero_asiento, :precio_pagado, :metodo_pago, :estado, :vendido_por_id
                )
            ");
            $stmtInsert->execute([
                'viaje_id'           => (int)$data['viaje_id'],
                'codigo_boleto'      => $codigoBoleto,
                'pasajero_documento' => trim($data['pasajero_documento']),
                'pasajero_nombre'    => trim($data['pasajero_nombre']),
                'pasajero_telefono'  => trim($data['pasajero_telefono'] ?? ''),
                'numero_asiento'     => $numAsiento,
                'precio_pagado'      => $precio,
                'metodo_pago'        => $data['metodo_pago'] ?? 'efectivo',
                'estado'             => 'valido',
                'vendido_por_id'     => !empty($data['vendido_por_id']) ? (int)$data['vendido_por_id'] : null
            ]);
            $boletoId = (int)$this->db->lastInsertId();

            // Descontar cupo del viaje
            $stmtUpdateViaje = $this->db->prepare("UPDATE viajes SET cupos_disponibles = cupos_disponibles - 1 WHERE id = :id");
            $stmtUpdateViaje->execute(['id' => (int)$data['viaje_id']]);

            $this->db->commit();
            return $boletoId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
