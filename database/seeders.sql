-- =======================================================
-- Datos Iniciales / Seeders: fluviapp
-- Contraseña por defecto para usuarios: admin123
-- =======================================================

USE `fluviapp`;

-- Usuarios iniciales
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `estado`, `telefono`) VALUES
(1, 'Administrador Principal', 'admin@fluviapp.com', '$2y$10$X3JEHnmwNOgfHU4dVodBseQ7pA62QAgtTsvK8aWjKQ7TegnYwkWp.', 'admin', 'activo', '3001234567'),
(2, 'Carlos Operador Muelle', 'operador@fluviapp.com', '$2y$10$X3JEHnmwNOgfHU4dVodBseQ7pA62QAgtTsvK8aWjKQ7TegnYwkWp.', 'operador', 'activo', '3109876543'),
(3, 'María Taquilla 1', 'taquilla@fluviapp.com', '$2y$10$X3JEHnmwNOgfHU4dVodBseQ7pA62QAgtTsvK8aWjKQ7TegnYwkWp.', 'taquilla', 'activo', '3157891234'),
(4, 'Capitán Juan Navas', 'capitan@fluviapp.com', '$2y$10$X3JEHnmwNOgfHU4dVodBseQ7pA62QAgtTsvK8aWjKQ7TegnYwkWp.', 'capitan', 'activo', '3204567890')
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`);

-- Muelles y Puertos Fluviales
INSERT INTO `muelles` (`id`, `nombre`, `rio`, `municipio`, `departamento`, `estado`) VALUES
(1, 'Muelle Principal Magangué', 'Río Magdalena', 'Magangué', 'Bolívar', 'activo'),
(2, 'Puerto Fluvial Mompox', 'Brazo de Mompox', 'Mompox', 'Bolívar', 'activo'),
(3, 'Terminal Fluvial El Banco', 'Río Magdalena', 'El Banco', 'Magdalena', 'activo'),
(4, 'Muelle La Dorada', 'Río Magdalena', 'La Dorada', 'Caldas', 'activo')
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`);

-- Embarcaciones
INSERT INTO `embarcaciones` (`id`, `nombre`, `matricula`, `tipo`, `capacidad_pasajeros`, `capacidad_carga_kg`, `estado`) VALUES
(1, 'La Perla del Río I', 'MAT-MAG-0101', 'lancha_rapida', 30, 800.00, 'operativo'),
(2, 'Expreso Fluvial Mompox', 'MAT-MOM-0202', 'lancha_rapida', 40, 1200.00, 'operativo'),
(3, 'Ferry Río Grande', 'MAT-FER-0303', 'ferry', 120, 15000.00, 'operativo'),
(4, 'Barcaza San Jerónimo', 'MAT-BAR-0404', 'barcaza_carga', 0, 30000.00, 'operativo')
ON DUPLICATE KEY UPDATE `nombre`=VALUES(`nombre`);

-- Rutas
INSERT INTO `rutas` (`id`, `muelle_origen_id`, `muelle_destino_id`, `distancia_km`, `duracion_estimada_min`, `tarifa_base`, `estado`) VALUES
(1, 1, 2, 48.00, 45, 25000.00, 'activa'),
(2, 2, 1, 48.00, 45, 25000.00, 'activa'),
(3, 2, 3, 62.00, 60, 35000.00, 'activa'),
(4, 3, 2, 62.00, 60, 35000.00, 'activa')
ON DUPLICATE KEY UPDATE `tarifa_base`=VALUES(`tarifa_base`);

-- Viajes
INSERT INTO `viajes` (`id`, `codigo_viaje`, `ruta_id`, `embarcacion_id`, `capitan_id`, `fecha_salida`, `hora_salida`, `precio_pasaje`, `cupos_disponibles`, `capacidad_carga_disponible_kg`, `estado`, `observaciones`) VALUES
(1, 'VJ-20260824-001', 1, 1, 4, CURRENT_DATE(), '08:00:00', 25000.00, 28, 750.00, 'programado', 'Zarpe matutino puntual'),
(2, 'VJ-20260824-002', 3, 2, 4, CURRENT_DATE(), '14:30:00', 35000.00, 40, 1200.00, 'programado', 'Itinerario de la tarde')
ON DUPLICATE KEY UPDATE `precio_pasaje`=VALUES(`precio_pasaje`);

-- Boletos de prueba
INSERT INTO `boletos` (`id`, `viaje_id`, `codigo_boleto`, `pasajero_documento`, `pasajero_nombre`, `pasajero_telefono`, `numero_asiento`, `precio_pagado`, `metodo_pago`, `estado`, `vendido_por_id`) VALUES
(1, 1, 'BOL-2026-0001', '1045678901', 'Andrés Morales', '3005551234', 1, 25000.00, 'efectivo', 'valido', 3),
(2, 1, 'BOL-2026-0002', '1098765432', 'Lucía Fernández', '3114449876', 2, 25000.00, 'transferencia', 'valido', 3)
ON DUPLICATE KEY UPDATE `codigo_boleto`=VALUES(`codigo_boleto`);

-- Encomiendas de prueba
INSERT INTO `cargas_encomiendas` (`id`, `viaje_id`, `guia_numero`, `remitente_nombre`, `remitente_telefono`, `destinatario_nombre`, `destinatario_telefono`, `descripcion_carga`, `peso_kg`, `valor_declarado`, `valor_flete`, `estado`, `registrado_por_id`) VALUES
(1, 1, 'GUIA-2026-0001', 'Almacén Central', '3001112233', 'Ferretería El Río', '3102223344', 'Caja de repuestos mecánicos', 25.50, 450000.00, 30000.00, 'registrada', 3),
(2, 1, 'GUIA-2026-0002', 'Gloria Mendoza', '3128889900', 'Roberto Gómez', '3157776655', 'Paquete de artesanías en filigrana', 8.00, 200000.00, 15000.00, 'registrada', 3)
ON DUPLICATE KEY UPDATE `guia_numero`=VALUES(`guia_numero`);
