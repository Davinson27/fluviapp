<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

echo "<pre style='font-family:monospace; background:#0f172a; color:#f8fafc; padding:20px; border-radius:8px;'>\n";
echo "=== Ejecutando Migración FluviApp v2.0 ===\n";

try {
    $db = Database::getConnection();
    $rawSql = file_get_contents(__DIR__ . '/migration_v2.sql');
    $sql = preg_replace('/^\s*USE\s+`?[a-zA-Z0-9_-]+`?\s*;/mi', '', $rawSql);
    
    $db->exec($sql);
    
    echo "✅ Migración v2.0 completada exitosamente.\n";
    echo "- Tabla pagos_transacciones creada.\n";
    echo "- Columnas para QR y transacciones añadidas a boletos y cargas.\n";
    echo "- Tabla viajes_telemetria (GPS) creada.\n";
    echo "- Tablas de flota (mantenimiento, combustible, documentos) creadas.\n";
    echo "========================================\n";
    echo "¡Base de datos lista para FluviApp v2.0!\n";
} catch (Exception $e) {
    echo "❌ Error ejecutando migración: " . $e->getMessage() . "\n";
}
echo "</pre>";
