-- ==========================================================
-- Rutas Intra-Departamentales para los 29 Departamentos
-- Todas las rutas conectan puertos del MISMO departamento
-- ==========================================================

USE `fluviapp`;

INSERT INTO rutas (id, muelle_origen_id, muelle_destino_id, distancia_km, duracion_estimada_min, tarifa_base, estado)
VALUES
-- 1. AMAZONAS (Intra-departamental)
(101, 10, 11, 75.00, 105, 41000, 'activa'),
(102, 11, 10, 75.00, 105, 41000, 'activa'),
(103, 10, 69, 140.00, 210, 64000, 'activa'),
(104, 69, 10, 140.00, 210, 64000, 'activa'),

-- 2. ANTIOQUIA (Intra-departamental)
(105, 70, 13, 38.00, 50, 28000, 'activa'),
(106, 13, 70, 38.00, 50, 28000, 'activa'),
(107, 13, 12, 45.00, 60, 31000, 'activa'),
(108, 12, 13, 45.00, 60, 31000, 'activa'),
(109, 12, 71, 15.00, 20, 20000, 'activa'),
(110, 71, 12, 15.00, 20, 20000, 'activa'),
(111, 14, 72, 55.00, 80, 34000, 'activa'),
(112, 72, 14, 55.00, 80, 34000, 'activa'),
(113, 15, 73, 160.00, 240, 71000, 'activa'),
(114, 73, 15, 160.00, 240, 71000, 'activa'),

-- 3. ARAUCA (Intra-departamental)
(115, 17, 16, 95.00, 180, 48000, 'activa'),
(116, 16, 17, 95.00, 180, 48000, 'activa'),

-- 4. ATLÁNTICO (Intra-departamental)
(117, 18, 91, 58.00, 75, 35000, 'activa'),
(118, 91, 18, 58.00, 75, 35000, 'activa'),
(119, 91, 90, 35.00, 45, 27000, 'activa'),
(120, 90, 91, 35.00, 45, 27000, 'activa'),

-- 5. BOLÍVAR (Intra-departamental)
(121, 19, 20, 45.00, 50, 31000, 'activa'),
(122, 20, 19, 45.00, 50, 31000, 'activa'),
(123, 19, 74, 70.00, 80, 40000, 'activa'),
(124, 74, 19, 70.00, 80, 40000, 'activa'),
(125, 74, 21, 77.00, 90, 42000, 'activa'),
(126, 21, 74, 77.00, 90, 42000, 'activa'),
(127, 19, 75, 52.00, 65, 33000, 'activa'),
(128, 75, 19, 52.00, 65, 33000, 'activa'),
(129, 75, 22, 85.00, 120, 45000, 'activa'),
(130, 22, 75, 85.00, 120, 45000, 'activa'),

-- 6. BOYACÁ (Intra-departamental)
(131, 23, 92, 25.00, 35, 24000, 'activa'),
(132, 92, 23, 25.00, 35, 24000, 'activa'),

-- 7. CALDAS (Intra-departamental)
(133, 24, 99, 65.00, 90, 38000, 'activa'),
(134, 99, 24, 65.00, 90, 38000, 'activa'),

-- 8. CAQUETÁ (Intra-departamental)
(135, 25, 26, 140.00, 300, 64000, 'activa'),
(136, 26, 25, 140.00, 300, 64000, 'activa'),
(137, 26, 76, 75.00, 120, 41000, 'activa'),
(138, 76, 26, 75.00, 120, 41000, 'activa'),
(139, 26, 77, 95.00, 150, 48000, 'activa'),
(140, 77, 26, 95.00, 150, 48000, 'activa'),

-- 9. CASANARE (Intra-departamental)
(141, 27, 93, 75.00, 110, 41000, 'activa'),
(142, 93, 27, 75.00, 110, 41000, 'activa'),

-- 10. CAUCA (Intra-departamental)
(143, 28, 29, 45.00, 75, 31000, 'activa'),
(144, 29, 28, 45.00, 75, 31000, 'activa'),

-- 11. CESAR (Intra-departamental)
(145, 30, 31, 38.00, 45, 28000, 'activa'),
(146, 31, 30, 38.00, 45, 28000, 'activa'),
(147, 31, 78, 62.00, 80, 37000, 'activa'),
(148, 78, 31, 62.00, 80, 37000, 'activa'),

-- 12. CHOCÓ (Intra-departamental)
(149, 32, 79, 54.00, 80, 34000, 'activa'),
(150, 79, 32, 54.00, 80, 34000, 'activa'),
(151, 79, 33, 156.00, 210, 70000, 'activa'),
(152, 33, 79, 156.00, 210, 70000, 'activa'),
(153, 33, 80, 122.00, 165, 58000, 'activa'),
(154, 80, 33, 122.00, 165, 58000, 'activa'),
(155, 80, 34, 52.00, 75, 33000, 'activa'),
(156, 34, 80, 52.00, 75, 33000, 'activa'),
(157, 35, 81, 18.00, 30, 21000, 'activa'),
(158, 81, 35, 18.00, 30, 21000, 'activa'),
(159, 81, 36, 95.00, 140, 48000, 'activa'),
(160, 36, 81, 95.00, 140, 48000, 'activa'),

-- 13. CÓRDOBA (Intra-departamental)
(161, 82, 37, 78.00, 100, 42000, 'activa'),
(162, 37, 82, 78.00, 100, 42000, 'activa'),
(163, 37, 38, 76.00, 95, 42000, 'activa'),
(164, 38, 37, 76.00, 95, 42000, 'activa'),
(165, 39, 83, 42.00, 55, 30000, 'activa'),
(166, 83, 39, 42.00, 55, 30000, 'activa'),

-- 14. CUNDINAMARCA (Intra-departamental)
(167, 40, 41, 115.00, 180, 55000, 'activa'),
(168, 41, 40, 115.00, 180, 55000, 'activa'),

-- 15. GUAINÍA (Intra-departamental)
(169, 42, 88, 250.00, 420, 102000, 'activa'),
(170, 88, 42, 250.00, 420, 102000, 'activa'),

-- 16. GUAVIARE (Intra-departamental)
(171, 43, 94, 90.00, 140, 46000, 'activa'),
(172, 94, 43, 90.00, 140, 46000, 'activa'),

-- 17. HUILA (Intra-departamental)
(173, 44, 95, 42.00, 60, 30000, 'activa'),
(174, 95, 44, 42.00, 60, 30000, 'activa'),

-- 18. MAGDALENA (Intra-departamental)
(175, 3, 98, 48.00, 60, 32000, 'activa'),
(176, 98, 3, 48.00, 60, 32000, 'activa'),
(177, 98, 46, 65.00, 80, 38000, 'activa'),
(178, 46, 98, 65.00, 80, 38000, 'activa'),

-- 19. META (Intra-departamental)
(179, 47, 48, 133.00, 160, 62000, 'activa'),
(180, 48, 47, 133.00, 160, 62000, 'activa'),
(181, 48, 86, 120.00, 180, 57000, 'activa'),
(182, 86, 48, 120.00, 180, 57000, 'activa'),

-- 20. NARIÑO (Intra-departamental)
(183, 50, 51, 140.00, 210, 64000, 'activa'),
(184, 51, 50, 140.00, 210, 64000, 'activa'),
(185, 51, 52, 60.00, 90, 36000, 'activa'),
(186, 52, 51, 60.00, 90, 36000, 'activa'),

-- 21. NORTE DE SANTANDER (Intra-departamental)
(187, 53, 96, 75.00, 120, 41000, 'activa'),
(188, 96, 53, 75.00, 120, 41000, 'activa'),

-- 22. PUTUMAYO (Intra-departamental)
(189, 84, 54, 35.00, 45, 27000, 'activa'),
(190, 54, 84, 35.00, 45, 27000, 'activa'),
(191, 54, 55, 325.00, 360, 129000, 'activa'),
(192, 55, 54, 325.00, 360, 129000, 'activa'),

-- 23. RISARALDA (Intra-departamental)
(193, 56, 97, 18.00, 25, 21000, 'activa'),
(194, 97, 56, 18.00, 25, 21000, 'activa'),

-- 24. SANTANDER (Intra-departamental)
(195, 58, 57, 34.00, 45, 27000, 'activa'),
(196, 57, 58, 34.00, 45, 27000, 'activa'),
(197, 58, 85, 28.00, 40, 25000, 'activa'),
(198, 85, 58, 28.00, 40, 25000, 'activa'),

-- 25. SUCRE (Intra-departamental)
(199, 59, 60, 62.00, 105, 37000, 'activa'),
(200, 60, 59, 62.00, 105, 37000, 'activa'),

-- 26. TOLIMA (Intra-departamental)
(201, 61, 62, 45.00, 55, 31000, 'activa'),
(202, 62, 61, 45.00, 55, 31000, 'activa'),
(203, 62, 87, 35.00, 45, 27000, 'activa'),
(204, 87, 62, 35.00, 45, 27000, 'activa'),

-- 27. VALLE DEL CAUCA (Intra-departamental)
(205, 63, 64, 180.00, 260, 78000, 'activa'),
(206, 64, 63, 180.00, 260, 78000, 'activa'),

-- 28. VAUPÉS (Intra-departamental)
(207, 65, 89, 210.00, 360, 88000, 'activa'),
(208, 89, 65, 210.00, 360, 88000, 'activa'),

-- 29. VICHADA (Intra-departamental)
(209, 67, 68, 115.00, 175, 55000, 'activa'),
(210, 68, 67, 115.00, 175, 55000, 'activa'),
(211, 68, 66, 187.00, 280, 80000, 'activa'),
(212, 66, 68, 187.00, 280, 80000, 'activa')
ON DUPLICATE KEY UPDATE 
muelle_origen_id=VALUES(muelle_origen_id), muelle_destino_id=VALUES(muelle_destino_id), 
distancia_km=VALUES(distancia_km), duracion_estimada_min=VALUES(duracion_estimada_min), tarifa_base=VALUES(tarifa_base), estado=VALUES(estado);

-- 30. Asegurar viajes en rutas intra-departamentales
-- Chocó: Quibdó -> Medio Atrato
INSERT INTO viajes (id, codigo_viaje, ruta_id, embarcacion_id, fecha_salida, hora_salida, precio_pasaje, cupos_disponibles, capacidad_carga_disponible_kg, estado)
VALUES 
(101, 'VJ-CHO-001', 149, 1, CURDATE(), '07:30:00', 34000.00, 28, 500.00, 'programado'),
(102, 'VJ-CHO-002', 151, 1, CURDATE(), '10:00:00', 70000.00, 25, 450.00, 'programado'),
(103, 'VJ-CHO-003', 157, 3, CURDATE(), '08:00:00', 21000.00, 16, 200.00, 'programado'),
-- Bolívar: Magangué -> Mompox
(104, 'VJ-BOL-001', 121, 1, CURDATE(), '06:00:00', 31000.00, 30, 600.00, 'programado'),
(105, 'VJ-BOL-002', 123, 2, CURDATE(), '09:00:00', 40000.00, 45, 1200.00, 'programado'),
-- Amazonas: Leticia -> Puerto Nariño
(106, 'VJ-AMZ-001', 101, 1, CURDATE(), '08:00:00', 41000.00, 26, 400.00, 'programado'),
-- Antioquia: Puerto Berrío -> Yondó
(107, 'VJ-ANT-001', 109, 3, CURDATE(), '07:00:00', 20000.00, 18, 300.00, 'programado'),
-- Santander: Barrancabermeja -> Puerto Wilches
(108, 'VJ-SAN-001', 196, 1, CURDATE(), '08:30:00', 27000.00, 24, 400.00, 'programado')
ON DUPLICATE KEY UPDATE 
ruta_id=VALUES(ruta_id), embarcacion_id=VALUES(embarcacion_id), fecha_salida=VALUES(fecha_salida),
hora_salida=VALUES(hora_salida), precio_pasaje=VALUES(precio_pasaje), cupos_disponibles=VALUES(cupos_disponibles), estado=VALUES(estado);
