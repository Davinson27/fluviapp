<?php
// =======================================================
// Modelo: Muelle (Puertos Fluviales)
// =======================================================

require_once __DIR__ . '/Model.php';

class Muelle extends Model {
    protected string $table = 'muelles';

    public function getActivos(): array {
        $stmt = $this->db->query("SELECT * FROM `{$this->table}` WHERE estado = 'activo' ORDER BY departamento ASC, municipio ASC");
        return $stmt->fetchAll();
    }

    public function getDepartamentos(): array {
        $stmt = $this->db->query("
            SELECT departamento, COUNT(*) as total_muelles, COUNT(DISTINCT rio) as total_rios
            FROM `{$this->table}`
            WHERE estado = 'activo' AND departamento IS NOT NULL AND departamento != ''
            GROUP BY departamento
            ORDER BY departamento ASC
        ");
        return $stmt->fetchAll();
    }

    public function getRiosUnicos(): array {
        $stmt = $this->db->query("
            SELECT rio, COUNT(*) as total_muelles, COUNT(DISTINCT departamento) as total_departamentos
            FROM `{$this->table}`
            WHERE estado = 'activo' AND rio IS NOT NULL AND rio != ''
            GROUP BY rio
            ORDER BY total_muelles DESC, rio ASC
        ");
        return $stmt->fetchAll();
    }

    public function getByDepartamento(string $departamento): array {
        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}`
            WHERE estado = 'activo' AND departamento = :dep
            ORDER BY municipio ASC
        ");
        $stmt->execute(['dep' => $departamento]);
        return $stmt->fetchAll();
    }

    public function getByRio(string $rio): array {
        $stmt = $this->db->prepare("
            SELECT * FROM `{$this->table}`
            WHERE estado = 'activo' AND rio = :rio
            ORDER BY departamento ASC, municipio ASC
        ");
        $stmt->execute(['rio' => $rio]);
        return $stmt->fetchAll();
    }

    public function getForMap(array $filtros = []): array {
        $sql = "
            SELECT id, nombre, rio, municipio, departamento, latitud, longitud, descripcion
            FROM `{$this->table}`
            WHERE estado = 'activo' AND latitud IS NOT NULL AND longitud IS NOT NULL
        ";
        $params = [];

        if (!empty($filtros['departamento'])) {
            $sql .= " AND departamento = :departamento";
            $params['departamento'] = $filtros['departamento'];
        }

        if (!empty($filtros['rio'])) {
            $sql .= " AND rio = :rio";
            $params['rio'] = $filtros['rio'];
        }

        $sql .= " ORDER BY departamento ASC, nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getGroupedByDepartamento(): array {
        $all = $this->getActivos();
        $grouped = [];
        foreach ($all as $m) {
            $dep = $m['departamento'] ?: 'Otros';
            if (!isset($grouped[$dep])) {
                $grouped[$dep] = [];
            }
            $grouped[$dep][] = $m;
        }
        ksort($grouped);
        return $grouped;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO `{$this->table}` (nombre, rio, municipio, departamento, latitud, longitud, descripcion, estado)
            VALUES (:nombre, :rio, :municipio, :departamento, :latitud, :longitud, :descripcion, :estado)
        ");
        $stmt->execute([
            'nombre'       => $data['nombre'],
            'rio'          => $data['rio'],
            'municipio'    => $data['municipio'],
            'departamento' => $data['departamento'],
            'latitud'      => $data['latitud'] ?? null,
            'longitud'     => $data['longitud'] ?? null,
            'descripcion'  => $data['descripcion'] ?? null,
            'estado'       => $data['estado'] ?? 'activo'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE `{$this->table}`
            SET nombre = :nombre, rio = :rio, municipio = :municipio, departamento = :departamento,
                latitud = :latitud, longitud = :longitud, descripcion = :descripcion, estado = :estado
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'           => $id,
            'nombre'       => $data['nombre'],
            'rio'          => $data['rio'],
            'municipio'    => $data['municipio'],
            'departamento' => $data['departamento'],
            'latitud'      => $data['latitud'] ?? null,
            'longitud'     => $data['longitud'] ?? null,
            'descripcion'  => $data['descripcion'] ?? null,
            'estado'       => $data['estado'] ?? 'activo'
        ]);
    }

    /**
     * Obtiene coordenadas promedio de referencia para un departamento o municipio
     * garantizando que cualquier muelle nuevo siempre tenga coordenadas válidas y aparezca en el mapa.
     */
    public function getCoordenadasAproximadas(string $departamento, string $municipio = ''): array {
        // 1. Intentar buscar si ya existe algún muelle en ese municipio
        if (!empty($municipio)) {
            $stmt = $this->db->prepare("SELECT latitud, longitud FROM `{$this->table}` WHERE municipio = :mun AND latitud IS NOT NULL AND longitud IS NOT NULL LIMIT 1");
            $stmt->execute(['mun' => $municipio]);
            $row = $stmt->fetch();
            if ($row && !empty($row['latitud']) && !empty($row['longitud'])) {
                // Pequeño desplazamiento de ~500m para no superponer exactamente
                return [
                    'latitud'  => round((float)$row['latitud'] + (mt_rand(-20, 20) / 10000), 7),
                    'longitud' => round((float)$row['longitud'] + (mt_rand(-20, 20) / 10000), 7)
                ];
            }
        }

        // 2. Intentar promedio por departamento
        $stmt = $this->db->prepare("SELECT AVG(latitud) as avg_lat, AVG(longitud) as avg_lng FROM `{$this->table}` WHERE departamento = :dep AND latitud IS NOT NULL AND longitud IS NOT NULL");
        $stmt->execute(['dep' => $departamento]);
        $row = $stmt->fetch();
        if ($row && !empty($row['avg_lat']) && !empty($row['avg_lng'])) {
            return [
                'latitud'  => round((float)$row['avg_lat'] + (mt_rand(-30, 30) / 10000), 7),
                'longitud' => round((float)$row['avg_lng'] + (mt_rand(-30, 30) / 10000), 7)
            ];
        }

        // 3. Coordenadas predeterminadas por departamento en Colombia
        $coordenadasDeptos = [
            'Amazonas'           => ['lat' => -3.6222, 'lng' => -70.0225],
            'Antioquia'          => ['lat' => 6.9888, 'lng' => -75.1473],
            'Arauca'             => ['lat' => 7.0561, 'lng' => -71.0939],
            'Atlántico'          => ['lat' => 10.6527, 'lng' => -74.8016],
            'Bolívar'            => ['lat' => 9.1743, 'lng' => -74.5607],
            'Boyacá'             => ['lat' => 5.9329, 'lng' => -74.5885],
            'Caldas'             => ['lat' => 5.4626, 'lng' => -74.9763],
            'Caquetá'            => ['lat' => 1.1685, 'lng' => -75.3955],
            'Casanare'           => ['lat' => 4.8045, 'lng' => -71.8136],
            'Cauca'              => ['lat' => 2.6710, 'lng' => -77.7755],
            'Cesar'              => ['lat' => 8.7329, 'lng' => -73.7889],
            'Chocó'              => ['lat' => 5.9965, 'lng' => -76.8966],
            'Córdoba'            => ['lat' => 8.4908, 'lng' => -75.6634],
            'Cundinamarca'       => ['lat' => 4.8839, 'lng' => -74.7303],
            'Guainía'            => ['lat' => 3.8985, 'lng' => -68.8566],
            'Guaviare'           => ['lat' => 2.2667, 'lng' => -72.6499],
            'Huila'              => ['lat' => 3.0741, 'lng' => -75.2508],
            'Magdalena'          => ['lat' => 9.2749, 'lng' => -74.3249],
            'Meta'               => ['lat' => 3.3704, 'lng' => -72.7377],
            'Nariño'             => ['lat' => 1.9355, 'lng' => -78.4282],
            'Norte de Santander' => ['lat' => 8.6653, 'lng' => -72.6868],
            'Putumayo'           => ['lat' => 0.3355, 'lng' => -75.9615],
            'Risaralda'          => ['lat' => 4.8928, 'lng' => -75.8788],
            'Santander'          => ['lat' => 7.2691, 'lng' => -73.7504],
            'Sucre'              => ['lat' => 8.7941, 'lng' => -75.0793],
            'Tolima'             => ['lat' => 4.7608, 'lng' => -74.7730],
            'Valle del Cauca'    => ['lat' => 4.3149, 'lng' => -76.4725],
            'Vaupés'             => ['lat' => 1.1341, 'lng' => -70.7667],
            'Vichada'            => ['lat' => 5.5562, 'lng' => -69.5903],
        ];

        if (isset($coordenadasDeptos[$departamento])) {
            return [
                'latitud'  => $coordenadasDeptos[$departamento]['lat'],
                'longitud' => $coordenadasDeptos[$departamento]['lng']
            ];
        }

        // Centro geográfico de Colombia por defecto
        return ['latitud' => 4.5709, 'longitud' => -74.2973];
    }
}
