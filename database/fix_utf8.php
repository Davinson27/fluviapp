<?php
// =======================================================
// Corrección y Limpieza de Caracteres Especiales UTF-8
// =======================================================

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getConnection();
    $pdo->exec("SET NAMES utf8mb4");

    // 1. Usuarios
    $pdo->exec("UPDATE usuarios SET nombre = 'María Taquilla 1' WHERE id = 3");
    $pdo->exec("UPDATE usuarios SET nombre = 'Capitán Juan Navas' WHERE id = 4");

    // 2. Muelles
    $pdo->exec("UPDATE muelles SET nombre = 'Muelle Principal Magangué', rio = 'Río Magdalena', municipio = 'Magangué', departamento = 'Bolívar' WHERE id = 1");
    $pdo->exec("UPDATE muelles SET nombre = 'Puerto Fluvial Mompox', rio = 'Brazo de Mompox', municipio = 'Mompox', departamento = 'Bolívar' WHERE id = 2");
    $pdo->exec("UPDATE muelles SET nombre = 'Terminal Fluvial El Banco', rio = 'Río Magdalena', municipio = 'El Banco', departamento = 'Magdalena' WHERE id = 3");
    $pdo->exec("UPDATE muelles SET nombre = 'Muelle La Dorada', rio = 'Río Magdalena', municipio = 'La Dorada', departamento = 'Caldas' WHERE id = 4");

    // 3. Embarcaciones
    $pdo->exec("UPDATE embarcaciones SET nombre = 'La Perla del Río I' WHERE id = 1");
    $pdo->exec("UPDATE embarcaciones SET nombre = 'Ferry Río Grande' WHERE id = 3");
    $pdo->exec("UPDATE embarcaciones SET nombre = 'Barcaza San Jerónimo' WHERE id = 4");

    // 4. Boletos
    $pdo->exec("UPDATE boletos SET pasajero_nombre = 'Andrés Morales' WHERE id = 1");
    $pdo->exec("UPDATE boletos SET pasajero_nombre = 'Lucía Fernández' WHERE id = 2");

    // 5. Cargas
    $pdo->exec("UPDATE cargas_encomiendas SET remitente_nombre = 'Almacén Central', destinatario_nombre = 'Ferretería El Río', descripcion_carga = 'Caja de repuestos mecánicos' WHERE id = 1");
    $pdo->exec("UPDATE cargas_encomiendas SET remitente_nombre = 'Gloria Mendoza', destinatario_nombre = 'Roberto Gómez', descripcion_carga = 'Paquete de artesanías en filigrana' WHERE id = 2");

    echo "UTF8_RECORDS_FIXED_SUCCESSFULLY\n";

    echo "--- USUARIOS EN BASE DE DATOS ---\n";
    $stmt = $pdo->query("SELECT id, nombre, email, rol FROM usuarios");
    while ($row = $stmt->fetch()) {
        echo "#{$row['id']} | {$row['nombre']} | {$row['email']} | {$row['rol']}\n";
    }

    echo "--- MUELLES EN BASE DE DATOS ---\n";
    $stmtM = $pdo->query("SELECT id, nombre, rio, municipio FROM muelles");
    while ($row = $stmtM->fetch()) {
        echo "#{$row['id']} | {$row['nombre']} | {$row['rio']} | {$row['municipio']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
