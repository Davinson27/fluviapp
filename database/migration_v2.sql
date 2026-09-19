-- =======================================================
-- Migración: FluviApp Versión 2.0 (v2.0)
-- Pagos Digitales, Validación QR, Asientos, Tracking y Flota
-- =======================================================

-- 1. Tabla de transacciones de pagos digitales (Wompi, etc.)
CREATE TABLE IF NOT EXISTS `pagos_transacciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `referencia_pago` VARCHAR(60) NOT NULL UNIQUE,
    `pasarela` VARCHAR(30) NOT NULL DEFAULT 'wompi',
    `transaccion_id_externo` VARCHAR(100) NULL,
    `metodo_pago` VARCHAR(50) NOT NULL DEFAULT 'wompi_checkout',
    `monto` DECIMAL(12,2) NOT NULL,
    `moneda` VARCHAR(10) NOT NULL DEFAULT 'COP',
    `estado` ENUM('pendiente', 'aprobado', 'rechazado', 'anulado') NOT NULL DEFAULT 'pendiente',
    `cliente_nombre` VARCHAR(120) NULL,
    `cliente_email` VARCHAR(100) NULL,
    `cliente_telefono` VARCHAR(30) NULL,
    `datos_transaccion` LONGTEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Modificaciones en tabla boletos
ALTER TABLE `boletos`
    ADD COLUMN IF NOT EXISTS `codigo_qr_token` VARCHAR(64) NULL AFTER `codigo_boleto`,
    ADD COLUMN IF NOT EXISTS `transaccion_id` INT NULL AFTER `vendido_por_id`,
    ADD COLUMN IF NOT EXISTS `fecha_embarque` DATETIME NULL AFTER `estado`;

-- Generar token QR para los boletos existentes que no lo tengan
UPDATE `boletos` 
SET `codigo_qr_token` = SHA2(CONCAT('FLV-BOL-', id, '-', codigo_boleto, '-', NOW()), 256)
WHERE `codigo_qr_token` IS NULL;

-- 3. Modificaciones en tabla cargas_encomiendas
ALTER TABLE `cargas_encomiendas`
    ADD COLUMN IF NOT EXISTS `codigo_qr_token` VARCHAR(64) NULL AFTER `guia_numero`,
    ADD COLUMN IF NOT EXISTS `largo_cm` DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER `peso_kg`,
    ADD COLUMN IF NOT EXISTS `ancho_cm` DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER `largo_cm`,
    ADD COLUMN IF NOT EXISTS `alto_cm` DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER `ancho_cm`,
    ADD COLUMN IF NOT EXISTS `peso_volumetrico_kg` DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER `alto_cm`,
    ADD COLUMN IF NOT EXISTS `firma_entrega_url` LONGTEXT NULL AFTER `estado`,
    ADD COLUMN IF NOT EXISTS `foto_evidencia_url` LONGTEXT NULL AFTER `firma_entrega_url`,
    ADD COLUMN IF NOT EXISTS `fecha_entrega` DATETIME NULL AFTER `foto_evidencia_url`,
    ADD COLUMN IF NOT EXISTS `transaccion_id` INT NULL AFTER `registrado_por_id`;

-- Generar token QR para las cargas existentes que no lo tengan
UPDATE `cargas_encomiendas` 
SET `codigo_qr_token` = SHA2(CONCAT('FLV-CRG-', id, '-', guia_numero, '-', NOW()), 256)
WHERE `codigo_qr_token` IS NULL;

-- 4. Tabla de telemetría y rastreo fluvial en vivo
CREATE TABLE IF NOT EXISTS `viajes_telemetria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `viaje_id` INT NOT NULL UNIQUE,
    `latitud` DECIMAL(10,7) NOT NULL,
    `longitud` DECIMAL(10,7) NOT NULL,
    `velocidad_kmh` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    `rumbo` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `ultima_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`viaje_id`) REFERENCES `viajes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabla de bitácora y mantenimiento de flota
CREATE TABLE IF NOT EXISTS `embarcaciones_mantenimiento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `embarcacion_id` INT NOT NULL,
    `tipo_mantenimiento` ENUM('preventivo', 'correctivo', 'motor', 'casco', 'electrico', 'emergencia') NOT NULL DEFAULT 'preventivo',
    `titulo` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `costo` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `fecha_mantenimiento` DATE NOT NULL,
    `proximo_mantenimiento` DATE NULL,
    `responsable` VARCHAR(120) NOT NULL DEFAULT '',
    `estado` ENUM('programado', 'en_proceso', 'completado') NOT NULL DEFAULT 'completado',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`embarcacion_id`) REFERENCES `embarcaciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabla de control de combustible
CREATE TABLE IF NOT EXISTS `embarcaciones_combustible` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `embarcacion_id` INT NOT NULL,
    `viaje_id` INT NULL,
    `tipo_combustible` ENUM('gasolina', 'diesel') NOT NULL DEFAULT 'gasolina',
    `galones` DECIMAL(8,2) NOT NULL,
    `precio_por_galon` DECIMAL(10,2) NOT NULL,
    `total_pagado` DECIMAL(12,2) NOT NULL,
    `fecha` DATE NOT NULL,
    `proveedor` VARCHAR(120) NULL,
    `horometro` DECIMAL(8,2) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`embarcacion_id`) REFERENCES `embarcaciones`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`viaje_id`) REFERENCES `viajes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabla de documentación legal y normativa de embarcaciones
CREATE TABLE IF NOT EXISTS `embarcaciones_documentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `embarcacion_id` INT NOT NULL,
    `tipo_documento` ENUM('patente_navegacion', 'certificado_seguridad', 'poliza_seguro', 'inspeccion_fluvial', 'otro') NOT NULL,
    `numero_documento` VARCHAR(80) NOT NULL,
    `entidad_emisora` VARCHAR(120) NOT NULL DEFAULT 'DIMAR / Mintransporte',
    `fecha_expedicion` DATE NOT NULL,
    `fecha_vencimiento` DATE NOT NULL,
    `observaciones` TEXT NULL,
    `archivo_path` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`embarcacion_id`) REFERENCES `embarcaciones`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
