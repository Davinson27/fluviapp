<?php
// =======================================================
// Migración: Soporte para Clientes, Enlaces y Precios
// =======================================================

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = Database::getConnection();
    $pdo->exec("SET NAMES utf8mb4");

    // 1. Modificar tabla usuarios: agregar 'cliente' al rol y columna documento
    $pdo->exec("ALTER TABLE `usuarios` MODIFY COLUMN `rol` ENUM('admin', 'operador', 'taquilla', 'capitan', 'cliente') NOT NULL DEFAULT 'cliente'");
    
    // Verificar si columna documento ya existe
    $cols = $pdo->query("SHOW COLUMNS FROM `usuarios` LIKE 'documento'")->fetchAll();
    if (empty($cols)) {
        $pdo->exec("ALTER TABLE `usuarios` ADD COLUMN `documento` VARCHAR(30) NULL AFTER `email`");
    }

    // 2. Modificar tabla boletos: agregar columna usuario_id
    $colsB = $pdo->query("SHOW COLUMNS FROM `boletos` LIKE 'usuario_id'")->fetchAll();
    if (empty($colsB)) {
        $pdo->exec("ALTER TABLE `boletos` ADD COLUMN `usuario_id` INT NULL AFTER `viaje_id`");
        $pdo->exec("ALTER TABLE `boletos` ADD CONSTRAINT `fk_boletos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL");
    }

    // 3. Modificar tabla cargas_encomiendas: agregar columna usuario_id
    $colsC = $pdo->query("SHOW COLUMNS FROM `cargas_encomiendas` LIKE 'usuario_id'")->fetchAll();
    if (empty($colsC)) {
        $pdo->exec("ALTER TABLE `cargas_encomiendas` ADD COLUMN `usuario_id` INT NULL AFTER `viaje_id`");
        $pdo->exec("ALTER TABLE `cargas_encomiendas` ADD CONSTRAINT `fk_cargas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL");
    }

    // 4. Crear un usuario cliente de prueba
    $clientePass = password_hash('cliente123', PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO `usuarios` (`id`, `nombre`, `email`, `documento`, `password`, `rol`, `estado`, `telefono`) 
                VALUES (5, 'Carlos Pasajero Fluvial', 'cliente@fluviapp.com', '1098765432', '$clientePass', 'cliente', 'activo', '3105557788')
                ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`), `rol`='cliente', `documento`='1098765432'");

    echo "PORTAL_MIGRATION_COMPLETED_SUCCESSFULLY\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
