<?php
// Script para importar expansion_rios.sql con codificación UTF-8 estricta

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

$pdo = Database::getConnection();
$pdo->exec("SET NAMES utf8mb4");

$sqlFile = __DIR__ . '/expansion_rios.sql';
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("Error: No se pudo leer expansion_rios.sql\n");
}

// Ejecutar queries
try {
    $pdo->exec($sql);
    echo "Importación UTF-8 completada exitosamente!\n";
} catch (PDOException $e) {
    echo "Error PDO: " . $e->getMessage() . "\n";
}

// Verificar departamentos
$stmt = $pdo->query("SELECT DISTINCT departamento FROM muelles ORDER BY departamento");
$deps = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Departamentos importados (" . count($deps) . "):\n";
print_r($deps);
