-- =======================================================
-- Base de Datos: fluviapp
-- Sistema de Gestión Fluvial (Transporte, Pasajes y Carga)
-- =======================================================

CREATE DATABASE IF NOT EXISTS `fluviapp` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fluviapp`;

-- 1. Tabla: usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rol` ENUM('admin', 'operador', 'taquilla', 'capitan') NOT NULL DEFAULT 'taquilla',
    `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    `telefono` VARCHAR(20) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla: muelles (Puertos / Muelles Fluviales)
CREATE TABLE IF NOT EXISTS `muelles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `rio` VARCHAR(100) NOT NULL,
    `municipio` VARCHAR(100) NOT NULL,
    `departamento` VARCHAR(100) NOT NULL,
    `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla: embarcaciones (Lanchas, Ferries, Barcazas)
CREATE TABLE IF NOT EXISTS `embarcaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `matricula` VARCHAR(50) NOT NULL UNIQUE,
    `tipo` ENUM('lancha_rapida', 'ferry', 'bote_motor', 'barcaza_carga') NOT NULL DEFAULT 'lancha_rapida',
    `capacidad_pasajeros` INT NOT NULL DEFAULT 0,
    `capacidad_carga_kg` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `estado` ENUM('operativo', 'mantenimiento', 'fuera_servicio') NOT NULL DEFAULT 'operativo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla: rutas fluviales
CREATE TABLE IF NOT EXISTS `rutas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `muelle_origen_id` INT NOT NULL,
    `muelle_destino_id` INT NOT NULL,
    `distancia_km` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `duracion_estimada_min` INT NOT NULL DEFAULT 60,
    `tarifa_base` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `estado` ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`muelle_origen_id`) REFERENCES `muelles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`muelle_destino_id`) REFERENCES `muelles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabla: viajes (Itinerarios y Zarpes)
CREATE TABLE IF NOT EXISTS `viajes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo_viaje` VARCHAR(30) NOT NULL UNIQUE,
    `ruta_id` INT NOT NULL,
    `embarcacion_id` INT NOT NULL,
    `capitan_id` INT NULL,
    `fecha_salida` DATE NOT NULL,
    `hora_salida` TIME NOT NULL,
    `precio_pasaje` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `cupos_disponibles` INT NOT NULL,
    `capacidad_carga_disponible_kg` DECIMAL(10,2) NOT NULL,
    `estado` ENUM('programado', 'en_embarque', 'en_navegacion', 'arribado', 'cancelado') NOT NULL DEFAULT 'programado',
    `observaciones` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`ruta_id`) REFERENCES `rutas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`embarcacion_id`) REFERENCES `embarcaciones`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`capitan_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabla: boletos (Pasajes Emitidos)
CREATE TABLE IF NOT EXISTS `boletos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `viaje_id` INT NOT NULL,
    `codigo_boleto` VARCHAR(40) NOT NULL UNIQUE,
    `pasajero_documento` VARCHAR(30) NOT NULL,
    `pasajero_nombre` VARCHAR(120) NOT NULL,
    `pasajero_telefono` VARCHAR(30) NULL,
    `numero_asiento` INT NULL,
    `precio_pagado` DECIMAL(10,2) NOT NULL,
    `metodo_pago` ENUM('efectivo', 'transferencia', 'tarjeta') NOT NULL DEFAULT 'efectivo',
    `estado` ENUM('valido', 'usado', 'cancelado') NOT NULL DEFAULT 'valido',
    `vendido_por_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`viaje_id`) REFERENCES `viajes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendido_por_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabla: cargas_encomiendas (Control de Fletes / Carga)
CREATE TABLE IF NOT EXISTS `cargas_encomiendas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `viaje_id` INT NOT NULL,
    `guia_numero` VARCHAR(40) NOT NULL UNIQUE,
    `remitente_nombre` VARCHAR(100) NOT NULL,
    `remitente_telefono` VARCHAR(30) NOT NULL,
    `destinatario_nombre` VARCHAR(100) NOT NULL,
    `destinatario_telefono` VARCHAR(30) NOT NULL,
    `descripcion_carga` TEXT NOT NULL,
    `peso_kg` DECIMAL(8,2) NOT NULL,
    `valor_declarado` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `valor_flete` DECIMAL(10,2) NOT NULL,
    `estado` ENUM('registrada', 'cargada', 'en_transito', 'entregada', 'cancelada') NOT NULL DEFAULT 'registrada',
    `registrado_por_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`viaje_id`) REFERENCES `viajes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`registrado_por_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
