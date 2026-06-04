USE SIGEMPI_DB
GO

-- ============================================================
-- DATOS DE PRUEBA - USERS
-- Hashes generados con bcrypt (cost=10), compatibles con
-- PHP password_verify() y Laravel Hash::check()
--
-- admin    -> password: 123456
-- tecnico  -> password: 123456
-- coord_01..coord_04 -> password: qwerty123
-- tec_01..tec_07     -> password: abc123
-- ============================================================
INSERT INTO Users (ID_Rol, ID_EstadoUsuario, Usuario, Correo_User, Password_Hash)
VALUES
-- Coordinadores (ID_Rol = 1)
(1, 1, 'admin',     'admin@sistema.com',         '$2y$10$sgOFIrNvaepkgNp1EGQjt.VEY5/Y0e8erdDETltX7fqGy.ngDd6eW'),
(1, 1, 'coord_01',  'coordinador01@sistema.com', '$2y$10$2XA/Mz79Kxd1ZuX3HgVRLOcoO.4Zyd1/ZxhsQQpkCjJsgqGBG6IZm'),
(1, 1, 'coord_02',  'coordinador02@sistema.com', '$2y$10$2XA/Mz79Kxd1ZuX3HgVRLOcoO.4Zyd1/ZxhsQQpkCjJsgqGBG6IZm'),
(1, 2, 'coord_03',  'coordinador03@sistema.com', '$2y$10$2XA/Mz79Kxd1ZuX3HgVRLOcoO.4Zyd1/ZxhsQQpkCjJsgqGBG6IZm'),
(1, 1, 'coord_04',  'coordinador04@sistema.com', '$2y$10$2XA/Mz79Kxd1ZuX3HgVRLOcoO.4Zyd1/ZxhsQQpkCjJsgqGBG6IZm'),

-- Técnicos (ID_Rol = 2)
(2, 1, 'tecnico',   'tecnico@sistema.com',       '$2y$10$sgOFIrNvaepkgNp1EGQjt.VEY5/Y0e8erdDETltX7fqGy.ngDd6eW'),
(2, 1, 'tec_01',    'tecnico01@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 1, 'tec_02',    'tecnico02@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 1, 'tec_03',    'tecnico03@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 2, 'tec_04',    'tecnico04@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 1, 'tec_05',    'tecnico05@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 1, 'tec_06',    'tecnico06@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe'),
(2, 1, 'tec_07',    'tecnico07@sistema.com',     '$2y$10$N2tpPCYtPwSrImD7GZi8SuouND5T2AlEnqu2fIEvNI1tMsbgkDjQe');
GO

-- ============================================================
-- EQUIPOS ADICIONALES
-- (Complementa los 7 ya insertados en el script base)
-- ============================================================
INSERT INTO Equipos (Codigo_Inventario, ID_Tipo, ID_Estado, ID_Ubicacion, Marca, Modelo)
VALUES
('MINSAL-LT-2026-033',  2, 2,  4, 'HP',      'EliteBook 850 G8'),
('MINSAL-LT-2026-034',  2, 2,  5, 'Dell',    'Latitude 5520'),
('MINSAL-LT-2026-035',  2, 4,  6, 'Lenovo',  'IdeaPad 5 Pro'),
('MINSAL-PC-2026-200',  1, 2,  7, 'HP',      'ProDesk 600 G6'),
('MINSAL-PC-2026-201',  1, 3,  8, 'Dell',    'OptiPlex 7090'),
('MINSAL-PRN-2026-010', 4, 2,  9, 'Canon',   'imageCLASS MF445dw'),
('MINSAL-PRN-2026-011', 4, 1, 10, 'HP',      'LaserJet Pro M404dn'),
('MINSAL-SRV-2026-002', 3, 2, 11, 'HP',      'ProLiant DL380 Gen10'),
('MINSAL-SRV-2026-003', 3, 4, 12, 'Lenovo',  'ThinkSystem SR650'),
('MINSAL-WS-2026-090',  1, 5,  2, 'Acer',    'Veriton X4680G'),
('MINSAL-LT-2026-050',  2, 2,  1, 'HP',      'ZBook Fury 15 G8'),
('MINSAL-IMP-2026-002', 5, 4,  3, 'Canon',   'imagePROGRAF TX-3000'),
('MINSAL-PC-2026-300',  1, 2,  1, 'Lenovo',  'ThinkCentre M90s'),
('MINSAL-PRN-2026-020', 4, 2,  2, 'Brother', 'MFC-L8900CDW');
GO

-- ============================================================
-- MANTENIMIENTOS (19 registros, los 4 estados cubiertos)
-- ID 1  = admin (coordinador)
-- ID 6  = tecnico | ID 7-13 = tec_01..tec_07
-- ============================================================
INSERT INTO Mantenimientos (ID_Equipo, ID_Tecnico, Fecha_Programada, ID_EstadoMantenimiento, Fecha_Ingreso, Fecha_Reprogramacion, Fecha_Cierre)
VALUES
-- Completados (2)
(1,  6,  '2026-01-10 08:00', 2, '2026-01-05 09:00', NULL,               '2026-01-10 11:30'),
(2,  7,  '2026-01-15 09:00', 2, '2026-01-10 08:00', NULL,               '2026-01-15 13:00'),
(4,  8,  '2026-01-20 10:00', 2, '2026-01-14 08:30', NULL,               '2026-01-20 12:00'),
(5,  6,  '2026-02-03 08:00', 2, '2026-01-28 09:00', NULL,               '2026-02-03 10:45'),
(6,  9,  '2026-02-10 09:00', 2, '2026-02-04 08:00', NULL,               '2026-02-10 14:00'),
(8,  10, '2026-02-18 08:00', 2, '2026-02-12 09:30', NULL,               '2026-02-18 11:00'),
(11, 11, '2026-03-05 09:00', 2, '2026-02-28 08:00', NULL,               '2026-03-05 13:30'),
(14, 7,  '2026-03-12 08:00', 2, '2026-03-06 09:00', NULL,               '2026-03-12 10:30'),
-- Programados (1)
(3,  8,  '2026-06-15 08:00', 1, '2026-06-01 09:00', NULL,               NULL),
(9,  9,  '2026-06-18 09:00', 1, '2026-06-02 08:00', NULL,               NULL),
(12, 12, '2026-06-20 10:00', 1, '2026-06-03 09:30', NULL,               NULL),
(15, 6,  '2026-06-25 08:00', 1, '2026-06-03 11:00', NULL,               NULL),
(17, 11, '2026-07-02 09:00', 1, '2026-06-03 14:00', NULL,               NULL),
-- Reprogramados (3)
(7,  10, '2026-05-20 09:00', 3, '2026-05-01 08:00', '2026-06-10 09:00', NULL),
(10, 13, '2026-05-22 08:00', 3, '2026-05-05 09:00', '2026-06-12 08:00', NULL),
(16, 7,  '2026-04-15 10:00', 3, '2026-04-01 08:30', '2026-06-20 10:00', NULL),
-- Cancelados (4)
(13, 9,  '2026-04-05 08:00', 4, '2026-03-25 09:00', NULL,               NULL),
(18, 12, '2026-03-20 09:00', 4, '2026-03-10 08:00', NULL,               NULL),
(20, 8,  '2026-02-25 08:00', 4, '2026-02-15 09:00', NULL,               NULL);
GO

-- ============================================================
-- MANTENIMIENTO_DETALLE
-- ============================================================
INSERT INTO Mantenimiento_Detalle (ID_Mantenimiento, ID_TecnicoIntervino, Fecha_Registro, Accion_Realizada, Observaciones_Tecnicas)
VALUES
(1,  6,  '2026-01-10 08:30', 'Limpieza de cabezales y reemplazo de tinta',          'Plotter con líneas discontinuas. Se sustituyeron cartuchos y se alinearon cabezales.'),
(1,  7,  '2026-01-10 10:00', 'Actualización de firmware',                           'Firmware actualizado a versión 5.2.1 sin inconvenientes.'),
(2,  7,  '2026-01-15 09:30', 'Formateo y reinstalación de Windows 11',              'Equipo lento. Se realizó respaldo previo al formateo.'),
(3,  8,  '2026-01-20 10:15', 'Diagnóstico y reemplazo de teclado',                  'Daño físico en teclas F1-F4. Reemplazado con repuesto de bodega.'),
(4,  6,  '2026-02-03 08:15', 'Limpieza interna de componentes',                     'Acumulación de polvo en disipador. Limpieza con aire comprimido.'),
(5,  9,  '2026-02-10 09:30', 'Revisión de cabezal y rodillos de arrastre',          'Rodillo con desgaste. Se solicitó cotización para reemplazo.'),
(6,  9,  '2026-02-10 11:00', 'Revisión de arreglo RAID y actualización de drivers', 'RAID 5 operando correctamente. Drivers de red y almacenamiento actualizados.'),
(7,  10, '2026-02-18 08:30', 'Sustitución de batería CMOS',                         'Equipo no arrancaba por batería agotada. Reemplazada exitosamente.'),
(8,  11, '2026-03-05 09:30', 'Mantenimiento preventivo general',                    'Sin anomalías. Limpieza general y verificación de conectores.'),
(9,  7,  '2026-03-12 08:30', 'Diagnóstico de disco duro',                           'Sectores defectuosos detectados por SMART. Se recomienda reemplazo urgente.'),
(14, 10, '2026-05-10 09:00', 'Diagnóstico de falla en pantalla',                    'Artefactos visuales en display. Pendiente autorización para reemplazo.'),
(15, 13, '2026-05-15 08:30', 'Diagnóstico de falla en encendido',                   'Equipo no enciende. Falla en fuente de poder. Repuesto solicitado a proveedor.'),
(16, 7,  '2026-04-10 10:00', 'Limpieza del sistema de arrastre de papel',           'Atasco recurrente. Rodillos con desgaste. Reprogramado para cambio de piezas.');
GO

-- ============================================================
-- HISTORIAL_CAMBIOS_ESTADO
-- ============================================================
INSERT INTO Historial_Cambios_Estado (ID_Mantenimiento, ID_EstadoAnterior, ID_EstadoNuevo, ID_TecnicoAnterior, ID_TecnicoNuevo, ID_UsuarioModifico, Fecha_Cambio, Motivo_Cambio)
VALUES
(7,  1, 3, 10, 10, 1, '2026-05-15 09:00', 'Técnico con incapacidad médica. Reprogramado para el 10 de junio.'),
(10, 1, 3, 13, 13, 2, '2026-05-18 10:00', 'Equipo en traslado temporal a otra sede.'),
(16, 1, 3,  7,  7, 1, '2026-04-08 08:00', 'Repuestos no disponibles en bodega. Pendiente llegada de pedido.'),
(13, 1, 4,  9,  9, 2, '2026-04-02 09:00', 'Equipo dado de baja antes del mantenimiento. Cancelado por coordinación.'),
(18, 1, 4, 12, 12, 1, '2026-03-15 10:00', 'Sede sin acceso por obras de remodelación.'),
(19, 1, 4,  8,  8, 2, '2026-02-18 08:00', 'Equipo con baja definitiva por dictamen técnico.'),
(1,  1, 2,  6,  6, 1, '2026-01-10 11:30', 'Mantenimiento finalizado sin incidencias.'),
(4,  1, 2,  6,  6, 1, '2026-02-03 10:45', 'Mantenimiento preventivo completado correctamente.');
GO

-- ============================================================
-- AUDITORIA_CAMBIOESTADO_USERS
-- coord_03 (ID4) y tec_04 (ID10) son los Inactivos
-- ============================================================
INSERT INTO Auditoria_CambioEstado_Users (ID_User, ID_UserAccion, ID_EstadoAnterior, ID_EstadoNuevo, Fecha)
VALUES
(4,  1, 1, 2, '2026-02-10 09:00'),
(10, 1, 1, 2, '2026-03-01 10:00'),
(4,  2, 2, 1, '2026-04-05 08:30'),
(10, 3, 2, 1, '2026-05-12 09:00');
GO

-- ============================================================
-- AUDITORIA_ACTUALIZACION_USERS
-- ============================================================
INSERT INTO Auditoria_Actualizacion_Users (ID_User, ID_UserAccion, ValorAnterior, ValorNuevo, Fecha)
VALUES
(7,  1, 'tec_01', 'tec_01_v2',     '2026-01-20 10:00'),
(9,  2, 'tec_03', 'tec_03_sv',     '2026-02-14 09:00'),
(11, 3, 'tec_05', 'tec_05_minsal', '2026-03-10 11:00');
GO

-- ============================================================
-- AUDITORIA_CAMBIOROL_USERS
-- ============================================================
INSERT INTO Auditoria_CambioRol_Users (ID_User, ID_UserAccion, ID_RolAnterior, ID_RolNuevo, Fecha)
VALUES
(8,  1, 2, 1, '2026-02-01 08:00'),
(8,  1, 1, 2, '2026-02-28 08:00'),
(13, 2, 2, 1, '2026-04-10 09:00');
GO

-- Mantenimiento programado VENCIDO (Fecha_Programada ya pasó y sigue sin cerrarse)
INSERT INTO Mantenimientos (ID_Equipo, ID_Tecnico, Fecha_Programada, ID_EstadoMantenimiento, Fecha_Ingreso, Fecha_Reprogramacion, Fecha_Cierre)
VALUES (19, 7, '2026-05-10 08:00', 1, '2026-05-01 09:00', NULL, NULL);
GO