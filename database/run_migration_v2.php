<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "=== Ejecutando Migración FluviApp v2.0 ===\n";

try {
    $db = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/migration_v2.sql');
    
    // Ejecutar sentencia por sentencia para manejo fino de errores
    $db->exec($sql);
    
    echo "✅ Migración v2.0 completada exitosamente.\n";
    echo "- Tabla pagos_transacciones creada.\n";
    echo "- Columnas para QR y transacciones añadidas a boletos y cargas.\n";
    echo "- Tabla viajes_telemetria (GPS) creada.\n";
    echo "- Tablas de flota (mantenimiento, combustible, documentos) creadas.\n";
} catch (Exception $e) {
    echo "❌ Error ejecutando migración: " . $e->getMessage() . "\n";
    exit(1);
}
