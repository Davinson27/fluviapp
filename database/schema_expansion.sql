-- =======================================================
-- Migración: Expansión de la Red Fluvial Nacional
-- Nuevas tablas y columnas para ríos, coordenadas GPS y departamentos
-- =======================================================

USE `fluviapp`;

-- 1. Agregar columnas de coordenadas y descripción a muelles
ALTER TABLE `muelles`
    ADD COLUMN IF NOT EXISTS `latitud` DECIMAL(10,7) NULL AFTER `departamento`,
    ADD COLUMN IF NOT EXISTS `longitud` DECIMAL(10,7) NULL AFTER `latitud`,
    ADD COLUMN IF NOT EXISTS `descripcion` TEXT NULL AFTER `longitud`;

-- 2. Crear tabla de ríos navegables
CREATE TABLE IF NOT EXISTS `rios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `longitud_total_km` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `longitud_navegable_km` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `cuenca` VARCHAR(100) NOT NULL DEFAULT '',
    `calado_promedio_pies` VARCHAR(50) NOT NULL DEFAULT '',
    `departamentos_que_conecta` TEXT NULL,
    `tipo_embarcaciones` TEXT NULL,
    `principales_cargas` TEXT NULL,
    `importancia` TEXT NULL,
    `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Actualizar coordenadas de los muelles existentes (1-4)
UPDATE `muelles` SET
    latitud = 9.2423, longitud = -74.7547,
    descripcion = 'Nodo fluvial de pasajeros y cabotaje más dinámico del Caribe colombiano'
WHERE id = 1;

UPDATE `muelles` SET
    latitud = 9.2411, longitud = -74.4267,
    descripcion = 'Patrimonio histórico y cultural sobre el Brazo de Mompox'
WHERE id = 2;

UPDATE `muelles` SET
    latitud = 8.9958, longitud = -73.9744,
    descripcion = 'La Ciudad Imperio de la Cumbia. Confluencia del Río Cesar y el Río Magdalena'
WHERE id = 3;

UPDATE `muelles` SET
    latitud = 5.4539, longitud = -74.6644,
    descripcion = 'Cabecera de navegación comercial pesada del Río Magdalena'
WHERE id = 4;
