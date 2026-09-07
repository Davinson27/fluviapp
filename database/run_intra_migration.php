<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$pdo = Database::getConnection();
$pdo->exec("SET NAMES utf8mb4");

echo "=== 1. Ejecutando migración de esquema y eliminación de rutas inter-departamentales ===\n";
$sql1 = file_get_contents(__DIR__ . '/intra_departmental_routes.sql');
$pdo->exec($sql1);
echo "Migración 1 ejecutada con éxito.\n";

echo "\n=== 2. Insertando rutas intra-departamentales para los 29 departamentos ===\n";
$sql2 = file_get_contents(__DIR__ . '/seed_intra_departmental_routes.sql');
$pdo->exec($sql2);
echo "Migración 2 ejecutada con éxito.\n";

// Verificaciones
echo "\n=== 3. Comprobación de rutas: ¿Existe alguna ruta entre departamentos diferentes? ===\n";
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM rutas r
    JOIN muelles mo ON r.muelle_origen_id = mo.id
    JOIN muelles md ON r.muelle_destino_id = md.id
    WHERE mo.departamento != md.departamento
");
$crossCount = $stmt->fetchColumn();
echo "Total rutas inter-departamentales (debe ser 0): $crossCount\n";

echo "\n=== 4. Total de rutas intra-departamentales activas ===\n";
$stmt = $pdo->query("
    SELECT mo.departamento, COUNT(*) as total_rutas
    FROM rutas r
    JOIN muelles mo ON r.muelle_origen_id = mo.id
    JOIN muelles md ON r.muelle_destino_id = md.id
    WHERE r.estado = 'activa'
    GROUP BY mo.departamento
    ORDER BY mo.departamento ASC
");
$deptos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Departamentos con rutas activas: " . count($deptos) . "\n";
foreach ($deptos as $d) {
    echo "  - " . $d['departamento'] . ": " . $d['total_rutas'] . " rutas\n";
}
