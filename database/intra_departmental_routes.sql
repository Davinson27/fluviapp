-- ==========================================================
-- Migración: Restricción de Rutas Intra-Departamentales
-- y Asignación de Departamentos a Usuarios
-- ==========================================================

USE `fluviapp`;

-- 1. Agregar columna departamento a usuarios si no existe
SET @dbname = DATABASE();
SET @tablename = "usuarios";
SET @columnname = "departamento";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE usuarios ADD COLUMN departamento VARCHAR(100) NULL AFTER telefono;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Asignar departamento inicial a usuarios existentes
UPDATE usuarios SET departamento = 'Chocó' WHERE email = 'cliente@fluviapp.com';
UPDATE usuarios SET departamento = 'Bolívar' WHERE email = 'admin@fluviapp.com';

-- 3. Asegurar que los departamentos con 1 solo puerto tengan un segundo puerto para posibilitar rutas intra-departamentales
INSERT INTO muelles (id, nombre, rio, municipio, departamento, latitud, longitud, descripcion, estado)
VALUES
-- Atlántico
(90, 'Muelle Fluvial Suan', 'Río Magdalena', 'Suan', 'Atlántico', 10.3325000, -74.8814000, 'Muelle en el sur del Atlántico sobre el Río Magdalena', 'activo'),
(91, 'Muelle Ponedera', 'Río Magdalena', 'Ponedera', 'Atlántico', 10.6406000, -74.7533000, 'Muelle de intercambio agropecuario en el Atlántico', 'activo'),
-- Boyacá
(92, 'Muelle Puerto Serviez', 'Río Magdalena', 'Puerto Boyacá', 'Boyacá', 5.8900000, -74.5900000, 'Muelle pesquero y comercial en Boyacá', 'activo'),
-- Casanare
(93, 'Muelle Fluvial Maní', 'Río Cusiana', 'Maní', 'Casanare', 4.8158000, -72.2889000, 'Muelle arrocero y comercial de Casanare', 'activo'),
-- Guaviare
(94, 'Muelle Calamar Guaviare', 'Río Unilla', 'Calamar', 'Guaviare', 1.9606000, -72.6539000, 'Muelle de conexión en selva del Guaviare', 'activo'),
-- Huila
(95, 'Muelle Villavieja Tatacoa', 'Río Magdalena', 'Villavieja', 'Huila', 3.2208000, -75.2197000, 'Muelle turístico hacia el Desierto de la Tatacoa', 'activo'),
-- Norte de Santander
(96, 'Muelle La Gabarra', 'Río Catatumbo', 'Tibú', 'Norte de Santander', 8.9667000, -72.9667000, 'Muelle de transporte fluvial en el Catatumbo', 'activo'),
-- Risaralda
(97, 'Muelle Caimalito', 'Río Cauca', 'La Virginia', 'Risaralda', 4.8850000, -75.8750000, 'Muelle de paso y carga en el Río Cauca risaraldense', 'activo'),
-- Magdalena
(98, 'Muelle Santa Ana Magdalena', 'Brazo de Mompox', 'Santa Ana', 'Magdalena', 9.3175000, -74.5683000, 'Muelle histórico en el departamento de Magdalena', 'activo'),
-- Caldas
(99, 'Muelle La Felisa Marmato', 'Río Cauca', 'Marmato', 'Caldas', 5.4800000, -75.6000000, 'Muelle minero y artesanal en el Río Cauca', 'activo')
ON DUPLICATE KEY UPDATE 
nombre=VALUES(nombre), rio=VALUES(rio), municipio=VALUES(municipio), departamento=VALUES(departamento), 
latitud=VALUES(latitud), longitud=VALUES(longitud), descripcion=VALUES(descripcion);

-- Actualizar muelles duplicados anteriores si existen
UPDATE muelles SET nombre = 'Muelle La Valerosa Mompox', municipio = 'Mompox', departamento = 'Bolívar' WHERE id = 2;
UPDATE muelles SET nombre = 'Muelle Santa Bárbara de Pinto', municipio = 'Santa Bárbara de Pinto', departamento = 'Magdalena', rio = 'Brazo de Mompox' WHERE id = 3;

-- 4. ELIMINAR TODAS LAS RUTAS INTER-DEPARTAMENTALES (que crucen de un departamento a otro)
-- Primero desvincular o eliminar viajes que usen rutas inter-departamentales
DELETE v FROM viajes v
JOIN rutas r ON v.ruta_id = r.id
JOIN muelles mo ON r.muelle_origen_id = mo.id
JOIN muelles md ON r.muelle_destino_id = md.id
WHERE mo.departamento != md.departamento;

-- Ahora eliminar las rutas inter-departamentales
DELETE r FROM rutas r
JOIN muelles mo ON r.muelle_origen_id = mo.id
JOIN muelles md ON r.muelle_destino_id = md.id
WHERE mo.departamento != md.departamento;
