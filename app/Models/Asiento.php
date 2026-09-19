<?php
// =======================================================
// Modelo: Asiento - FluviApp v2.0
// Generación dinámica y consulta de asientos por viaje
// =======================================================

require_once __DIR__ . '/Model.php';

class Asiento extends Model {

    /**
     * Obtiene la distribución y estado de todos los asientos para un viaje dado
     */
    public function getMapaAsientosPorViaje(int $viajeId): array {
        // 1. Obtener información de la embarcación del viaje
        $stmt = $this->db->prepare("
            SELECT v.id as viaje_id, v.cupos_disponibles, e.id as embarcacion_id, 
                   e.nombre as embarcacion_nombre, e.tipo as embarcacion_tipo, 
                   e.capacidad_pasajeros
            FROM viajes v
            JOIN embarcaciones e ON v.embarcacion_id = e.id
            WHERE v.id = :viaje_id
            LIMIT 1
        ");
        $stmt->execute([':viaje_id' => $viajeId]);
        $info = $stmt->fetch();

        if (!$info) {
            return [];
        }

        $capacidad = (int)$info['capacidad_pasajeros'];
        $tipo = $info['embarcacion_tipo'];

        // 2. Obtener asientos actualmente ocupados en este viaje
        $stmtBoletos = $this->db->prepare("
            SELECT numero_asiento, pasajero_nombre, codigo_boleto
            FROM boletos 
            WHERE viaje_id = :viaje_id 
              AND estado != 'cancelado' 
              AND numero_asiento IS NOT NULL
        ");
        $stmtBoletos->execute([':viaje_id' => $viajeId]);
        $ocupados = [];
        while ($row = $stmtBoletos->fetch()) {
            $ocupados[(int)$row['numero_asiento']] = $row;
        }

        // 3. Determinar configuración de columnas según tipo de embarcación
        $config = $this->getConfiguracionLayout($tipo, $capacidad);
        $asientosPorFila = $config['columnas_izquierda'] + $config['columnas_derecha'];
        $totalFilas = ceil($capacidad / $asientosPorFila);

        $asientos = [];
        $num = 1;

        for ($f = 1; $f <= $totalFilas; $f++) {
            $fila = [
                'numero_fila' => $f,
                'izquierda'   => [],
                'derecha'     => []
            ];

            // Asientos lado izquierdo (ventana/pasillo)
            for ($col = 1; $col <= $config['columnas_izquierda']; $col++) {
                if ($num <= $capacidad) {
                    $esVentana = ($col === 1);
                    $fila['izquierda'][] = [
                        'numero'       => $num,
                        'posicion'     => $esVentana ? 'Ventana' : 'Pasillo',
                        'estado'       => isset($ocupados[$num]) ? 'ocupado' : 'disponible',
                        'pasajero'     => isset($ocupados[$num]) ? $ocupados[$num]['pasajero_nombre'] : null
                    ];
                    $num++;
                }
            }

            // Asientos lado derecho (pasillo/ventana)
            for ($col = 1; $col <= $config['columnas_derecha']; $col++) {
                if ($num <= $capacidad) {
                    $esVentana = ($col === $config['columnas_derecha']);
                    $fila['derecha'][] = [
                        'numero'       => $num,
                        'posicion'     => $esVentana ? 'Ventana' : 'Pasillo',
                        'estado'       => isset($ocupados[$num]) ? 'ocupado' : 'disponible',
                        'pasajero'     => isset($ocupados[$num]) ? $ocupados[$num]['pasajero_nombre'] : null
                    ];
                    $num++;
                }
            }

            $asientos[] = $fila;
        }

        return [
            'info_embarcacion' => $info,
            'config'           => $config,
            'total_asientos'   => $capacidad,
            'ocupados_count'   => count($ocupados),
            'libres_count'     => max(0, $capacidad - count($ocupados)),
            'filas'            => $asientos
        ];
    }

    /**
     * Define la arquitectura visual de asientos por tipo de embarcación
     */
    private function getConfiguracionLayout(string $tipo, int $capacidad): array {
        return match ($tipo) {
            'ferry' => [
                'nombre'             => 'Ferry Fluvial',
                'columnas_izquierda' => 3,
                'columnas_derecha'   => 3,
                'pasillo_central'    => true,
                'cubiertas'          => $capacidad > 60 ? 2 : 1
            ],
            'bote_motor' => [
                'nombre'             => 'Bote a Motor',
                'columnas_izquierda' => 1,
                'columnas_derecha'   => 1,
                'pasillo_central'    => true,
                'cubiertas'          => 1
            ],
            default => [ // lancha_rapida o estándar
                'nombre'             => 'Lancha Rápida',
                'columnas_izquierda' => 2,
                'columnas_derecha'   => 2,
                'pasillo_central'    => true,
                'cubiertas'          => 1
            ]
        };
    }
}
