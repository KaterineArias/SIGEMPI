CREATE DATABASE SIGEMPI_DB
GO
USE SIGEMPI_DB

CREATE TABLE Roles_User
(
ID_Rol int IDENTITY(1,1) PRIMARY KEY,
Rol varchar(30) unique not null,
Fecha_CreacionRol datetime DEFAULT GETDATE()
)

INSERT INTO Roles_User(Rol)
VALUES ('Coordinador'), ('Tecnico')

CREATE TABLE Estado_Usuario
(
ID_EstadoUsuario INT IDENTITY(1,1) PRIMARY KEY,
Estado varchar(40) not null unique,
Fecha_CreacionEstadoUsuario datetime DEFAULT getdate()
)


INSERT INTO Estado_Usuario(Estado)
Values ('Activo'),('Inactivo')

CREATE TABLE Users
(
ID_User int IDENTITY(1,1) PRIMARY KEY,
ID_Rol INT FOREIGN KEY REFERENCES Roles_User(ID_Rol),
ID_EstadoUsuario INT FOREIGN KEY REFERENCES Estado_Usuario(ID_EstadoUsuario),
Usuario varchar(50) UNIQUE not null,
Correo_User varchar(100) not null,
Password_Hash varchar(200) not null,
-- Hash para agregarle un encriptado a las contraseñas
Fecha_CreacionUser datetime DEFAULT GETDATE() -- Para saber cuándo se registró el usuario
)

INSERT INTO Users (ID_Rol, ID_EstadoUsuario, Usuario, Correo_User, Password_Hash)
VALUES
(1, 1, 'admin',     'admin@sistema.com',         '$2y$10$sgOFIrNvaepkgNp1EGQjt.VEY5/Y0e8erdDETltX7fqGy.ngDd6eW'),
(2, 1, 'tecnico',   'tecnico@sistema.com',       '$2y$10$sgOFIrNvaepkgNp1EGQjt.VEY5/Y0e8erdDETltX7fqGy.ngDd6eW')
    
CREATE TABLE Tipos_Equipo
(
ID_Tipo INT IDENTITY(1,1) PRIMARY KEY,
Nombre_Tipo varchar(40) unique not null,
Fecha_CreacionTipo datetime DEFAULT GETDATE()
)

INSERT INTO Tipos_Equipo(Nombre_Tipo)
VALUES ('Escritorio'),('Laptop'),('Servidor'),('Impresora'),('Plotter')

CREATE TABLE Estado_Equipo
(
ID_Estado INT IDENTITY(1,1) PRIMARY KEY,
Estado varchar(50) unique not null,
Fecha_CreacionEstado datetime DEFAULT GETDATE()
)

INSERT INTO Estado_Equipo(Estado)
Values ('Dañado'),('Activo'),('Bodega'),('Inactivo'),('De Baja')

CREATE TABLE Departamento
(
ID_Departamento INT IDENTITY(1,1) PRIMARY KEY,
NombreDepartamento varchar(100) not null
)

INSERT INTO Departamento(NombreDepartamento)
VALUES 
('Ahuachapán'), ('Santa Ana'), ('Sonsonate'), ('La Libertad'), 
('Chalatenango'), ('Cuscatlán'), ('San Salvador'), ('La Paz'), 
('Cabañas'), ('San Vicente'), ('Usulután'), ('San Miguel'), 
('Morazán'), ('La Unión');

CREATE TABLE Municipio
(
ID_Municipio INT IDENTITY(1,1) PRIMARY KEY,
ID_Departamento int FOREIGN KEY REFERENCES Departamento(ID_Departamento),
NombreMunicipio varchar(100) not null
)

INSERT INTO Municipio (ID_Departamento, NombreMunicipio)
VALUES
(1, 'Ahuachapán'), (1, 'Apaneca'), (1, 'Atiquizaya'), (1, 'Concepción de Ataco'),
(2, 'Santa Ana'), (2, 'Chalchuapa'), (2, 'Metapán'), (2, 'Coatepeque'),
(3, 'Sonsonate'), (3, 'Acajutla'), (3, 'Izalco'), (3, 'Nahuizalco'),
(4, 'Santa Tecla'), (4, 'Antiguo Cuscatlán'), (4, 'La Libertad'), (4, 'Zaragoza'),
(7, 'San Salvador'), (7, 'Soyapango'), (7, 'Mejicanos'), (7, 'Apopa'), (7, 'Ilopango'), (7, 'San Marcos'),
(12, 'San Miguel'), (12, 'Ciudad Barrios'), (12, 'Chirilagua'), (12, 'Quelepa');

CREATE TABLE Ubicacion
(
ID_Ubicacion int IDENTITY(1,1) PRIMARY KEY,
ID_Municipio int FOREIGN KEY REFERENCES Municipio(ID_Municipio),
NombreSede varchar(300) unique not null
)

INSERT INTO Ubicacion (ID_Municipio, NombreSede)
VALUES
-- Ubicaciones en San Salvador (ID 17 en el script anterior)
(17, 'Hospital Nacional Rosales'),
(17, 'Oficinas Administrativas Centrales'),
(17, 'Centro de Impresión y Acabados'),
(17, 'Unidad de Salud Barrios'),

-- Ubicaciones en Santa Tecla (ID 13 en el script anterior)
(13, 'Hospital Nacional San Rafael'),
(13, 'Unidad de Salud Dr. Carlos Díaz del Pinal'),

-- Ubicaciones en Santa Ana (ID 5 en el script anterior)
(5, 'Hospital Nacional San Juan de Dios'),
(5, 'Centro Regional de Salud Occidental'),

-- Ubicaciones en Soyapango (ID 18 en el script anterior)
(18, 'Hospital Nacional Psiquiátrico Dr. José Molina Martínez'),
(18, 'Unidad de Salud Unicentro'),

-- Ubicaciones en San Miguel (ID 23 en el script anterior)
(23, 'Hospital Nacional San Juan de Dios (San Miguel)'),
(23, 'Almacén Regional de Insumos Médicos');

CREATE TABLE Equipos
(
ID_Equipo int IDENTITY(1,1) PRIMARY KEY,
Codigo_Inventario varchar(100) UNIQUE not null, -- El código de la viñeta física de la institución
ID_Tipo INT FOREIGN KEY REFERENCES Tipos_Equipo(ID_Tipo),
ID_Estado INT FOREIGN KEY REFERENCES Estado_Equipo(ID_Estado),
ID_Ubicacion int FOREIGN KEY REFERENCES Ubicacion(ID_Ubicacion),
Marca varchar(60),
Modelo varchar(100)
)

INSERT INTO Equipos (Codigo_Inventario, ID_Tipo, ID_Estado, ID_Ubicacion, Marca, Modelo)
VALUES
-- Equipos para el "Centro de Impresión y Acabados" (Asumiendo ID_Ubicacion = 3)
('MINSAL-IMP-2026-001', 5, 2, 3, 'HP', 'DesignJet T1600'), -- Tipo: Plotter (5), Estado: Activo (2)
('MINSAL-WS-2026-045', 1, 2, 3, 'Dell', 'Precision 3650'), -- Tipo: Escritorio (1), Estado: Activo (2)
('MINSAL-LT-2026-012', 2, 3, 3, 'Lenovo', 'ThinkPad T14'), -- Tipo: Laptop (2), Estado: Bodega (3)

-- Equipos para "Hospital Nacional Rosales" (Asumiendo ID_Ubicacion = 1)
('MINSAL-PC-2026-112', 1, 2, 1, 'Lenovo', 'ThinkCentre M70s'), -- Tipo: Escritorio (1), Estado: Activo (2)
('MINSAL-PRN-2026-003', 4, 1, 1, 'Epson', 'EcoTank L15150'),  -- Tipo: Impresora (4), Estado: Dañado (1)

-- Equipos para "Oficinas Administrativas Centrales" (Asumiendo ID_Ubicacion = 2)
('MINSAL-SRV-2026-001', 3, 2, 2, 'Dell', 'PowerEdge R740'),  -- Tipo: Servidor (3), Estado: Activo (2)
('MINSAL-PC-2026-088', 1, 5, 2, 'HP', 'ProDesk 400 G7');     -- Tipo: Escritorio (1), Estado: De Baja (5)

CREATE TABLE Catalogo_EstadoMantenimiento
(
ID_EstadoMantenimiento INT IDENTITY(1,1) PRIMARY KEY,
Nombre_EstadoMantenimiento varchar(30) unique not null
)

INSERT INTO Catalogo_EstadoMantenimiento(Nombre_EstadoMantenimiento)
Values ('Programado'),('Completado'),('Reprogramado'),('Cancelado')

CREATE TABLE Mantenimientos
(
ID_Mantenimiento INT IDENTITY(1,1) PRIMARY KEY,
ID_Equipo INT FOREIGN KEY REFERENCES Equipos(ID_Equipo),
ID_Tecnico INT FOREIGN KEY REFERENCES Users(ID_User),
Fecha_Programada DATETIME NULL,
ID_EstadoMantenimiento INT FOREIGN KEY REFERENCES Catalogo_EstadoMantenimiento(ID_EstadoMantenimiento),
Fecha_Ingreso DATETIME DEFAULT GETDATE(),
Fecha_Reprogramacion DATETIME null,
Fecha_Cierre DATETIME null,
created_at datetime null,
updated_at datetime null
)


CREATE TABLE Mantenimiento_Detalle
(
ID_Detalle INT IDENTITY(1,1) PRIMARY KEY,
ID_Mantenimiento INT FOREIGN KEY REFERENCES Mantenimientos(ID_Mantenimiento),
ID_TecnicoIntervino INT FOREIGN KEY REFERENCES Users(ID_User),
Fecha_Registro DATETIME DEFAULT GETDATE(),
Accion_Realizada  VARCHAR(500)  NULL,
Observaciones_Tecnicas VARCHAR(1000) NULL
)


CREATE TABLE Historial_Cambios_Estado
(
ID_Historial INT IDENTITY(1,1) PRIMARY KEY,
ID_Mantenimiento INT FOREIGN KEY REFERENCES Mantenimientos(ID_Mantenimiento),
ID_EstadoAnterior INT FOREIGN KEY REFERENCES Catalogo_EstadoMantenimiento(ID_EstadoMantenimiento),
ID_EstadoNuevo INT FOREIGN KEY REFERENCES Catalogo_EstadoMantenimiento(ID_EstadoMantenimiento),
ID_TecnicoAnterior INT FOREIGN KEY REFERENCES Users(ID_User),
ID_TecnicoNuevo INT FOREIGN KEY REFERENCES Users(ID_User),
ID_UsuarioModifico  INT FOREIGN KEY REFERENCES Users(ID_User),
Fecha_Cambio DATETIME DEFAULT GETDATE(),
Motivo_Cambio VARCHAR(500)  NULL
)

CREATE TABLE Auditoria_CambioEstado_Users
(
ID_Auditoria INT IDENTITY(1,1) PRIMARY KEY,
ID_User INT FOREIGN KEY REFERENCES Users(ID_User),
ID_UserAccion INT FOREIGN KEY REFERENCES Users(ID_User), --usuario que realizo el cambio
ID_EstadoAnterior INT FOREIGN KEY REFERENCES Estado_Usuario(ID_EstadoUsuario), --estado anterior
ID_EstadoNuevo INT FOREIGN KEY REFERENCES Estado_Usuario(ID_EstadoUsuario), --estado nuevo
Fecha DATETIME DEFAULT GETDATE()
)
 
 
CREATE TABLE Auditoria_Actualizacion_Users
(
ID_Auditoria INT IDENTITY(1,1) PRIMARY KEY,
ID_User INT FOREIGN KEY REFERENCES Users(ID_User),
ID_UserAccion INT FOREIGN KEY REFERENCES Users(ID_User), --usuario que realizo el cambio
ValorAnterior VARCHAR(200) NULL, --nombre usuario anterior
ValorNuevo VARCHAR(200) NULL, --nuevo nombre de usuario
Fecha DATETIME DEFAULT GETDATE()
)

CREATE TABLE Auditoria_CambioRol_Users
(
ID_Auditoria INT IDENTITY(1,1) PRIMARY KEY,
ID_User INT FOREIGN KEY REFERENCES Users(ID_User),
ID_UserAccion INT FOREIGN KEY REFERENCES Users(ID_User), --usuario que realizo el cambio
ID_RolAnterior INT FOREIGN KEY REFERENCES Roles_User(ID_Rol), --rol anterior
ID_RolNuevo INT FOREIGN KEY REFERENCES Roles_User(ID_Rol), --rol nuevo
Fecha DATETIME DEFAULT GETDATE()
)

INSERT INTO Equipos (Codigo_Inventario, ID_Tipo, ID_Estado, ID_Ubicacion, Marca, Modelo) VALUES
-- ESCRITORIOS (Tipo 1) --
('MINSAL-WS-2026-101', 1, 2, 1,  'Dell',   'OptiPlex 7090'),
('MINSAL-WS-2026-102', 1, 2, 2,  'HP',     'EliteDesk 800 G6'),
('MINSAL-WS-2026-103', 1, 3, 3,  'Lenovo', 'ThinkCentre M80s'),
('MINSAL-WS-2026-104', 1, 2, 4,  'Dell',   'OptiPlex 5090'),
('MINSAL-WS-2026-105', 1, 5, 5,  'HP',     'ProDesk 600 G6'),
('MINSAL-WS-2026-106', 1, 2, 6,  'Lenovo', 'ThinkCentre M70q'),
('MINSAL-WS-2026-107', 1, 1, 7,  'Dell',   'OptiPlex 3090'),
('MINSAL-WS-2026-108', 1, 2, 8,  'HP',     'EliteDesk 880 G6'),
('MINSAL-WS-2026-109', 1, 2, 9,  'Lenovo', 'ThinkCentre M90s'),
('MINSAL-WS-2026-110', 1, 3, 10, 'Dell',   'OptiPlex 7080'),
('MINSAL-WS-2026-111', 1, 2, 11, 'HP',     'ProDesk 400 G6'),
('MINSAL-WS-2026-112', 1, 4, 12, 'Lenovo', 'ThinkCentre M60e'),
('MINSAL-WS-2026-113', 1, 2, 1,  'Dell',   'Vostro 3888'),
('MINSAL-WS-2026-114', 1, 2, 2,  'HP',     'EliteDesk 705 G6'),
('MINSAL-WS-2026-115', 1, 1, 3,  'Lenovo', 'ThinkCentre M75s'),
('MINSAL-WS-2026-116', 1, 2, 4,  'Dell',   'OptiPlex 5080'),
('MINSAL-WS-2026-117', 1, 2, 5,  'HP',     'ProDesk 480 G7'),
('MINSAL-WS-2026-118', 1, 3, 6,  'Lenovo', 'ThinkCentre M720s'),
('MINSAL-WS-2026-119', 1, 2, 7,  'Dell',   'OptiPlex 3080'),
('MINSAL-WS-2026-120', 1, 5, 8,  'HP',     'EliteDesk 800 G5'),
('MINSAL-WS-2026-121', 1, 2, 9,  'Lenovo', 'ThinkCentre M720q'),
('MINSAL-WS-2026-122', 1, 2, 10, 'Dell',   'OptiPlex 7070'),
('MINSAL-WS-2026-123', 1, 4, 11, 'HP',     'ProDesk 600 G5'),
('MINSAL-WS-2026-124', 1, 2, 12, 'Lenovo', 'ThinkCentre M920s'),
('MINSAL-WS-2026-125', 1, 2, 1,  'Dell',   'OptiPlex 7060'),
('MINSAL-WS-2026-126', 1, 1, 2,  'HP',     'EliteDesk 705 G5'),
('MINSAL-WS-2026-127', 1, 2, 3,  'Lenovo', 'ThinkCentre M920x'),
('MINSAL-WS-2026-128', 1, 2, 4,  'Dell',   'Vostro 3670'),
('MINSAL-WS-2026-129', 1, 3, 5,  'HP',     'ProDesk 400 G5'),
('MINSAL-WS-2026-130', 1, 2, 6,  'Lenovo', 'ThinkCentre M73'),
('MINSAL-WS-2026-131', 1, 2, 7,  'Dell',   'OptiPlex 5060'),
('MINSAL-WS-2026-132', 1, 2, 8,  'HP',     'EliteDesk 800 G4'),
('MINSAL-WS-2026-133', 1, 5, 9,  'Lenovo', 'ThinkCentre M93p'),
('MINSAL-WS-2026-134', 1, 2, 10, 'Dell',   'OptiPlex 3060'),
('MINSAL-WS-2026-135', 1, 4, 11, 'HP',     'ProDesk 600 G4'),
('MINSAL-WS-2026-136', 1, 2, 12, 'Lenovo', 'ThinkCentre M83'),
('MINSAL-WS-2026-137', 1, 2, 1,  'Dell',   'OptiPlex 7050'),
('MINSAL-WS-2026-138', 1, 1, 2,  'HP',     'EliteDesk 800 G3'),
('MINSAL-WS-2026-139', 1, 2, 3,  'Lenovo', 'ThinkCentre M910s'),
('MINSAL-WS-2026-140', 1, 2, 4,  'Dell',   'OptiPlex 5050'),
('MINSAL-WS-2026-141', 1, 3, 5,  'HP',     'ProDesk 400 G4'),
('MINSAL-WS-2026-142', 1, 2, 6,  'Lenovo', 'ThinkCentre M910q'),
('MINSAL-WS-2026-143', 1, 2, 7,  'Dell',   'OptiPlex 3050'),
('MINSAL-WS-2026-144', 1, 2, 8,  'HP',     'EliteDesk 705 G4'),
('MINSAL-WS-2026-145', 1, 4, 9,  'Lenovo', 'ThinkCentre M710s'),
('MINSAL-WS-2026-146', 1, 2, 10, 'Dell',   'Vostro 3268'),
('MINSAL-WS-2026-147', 1, 5, 11, 'HP',     'ProDesk 600 G3'),
('MINSAL-WS-2026-148', 1, 2, 12, 'Lenovo', 'ThinkCentre M710q'),
('MINSAL-WS-2026-149', 1, 2, 1,  'Dell',   'OptiPlex 7040'),
('MINSAL-WS-2026-150', 1, 1, 2,  'HP',     'EliteDesk 800 G2'),
('MINSAL-WS-2026-151', 1, 2, 3,  'Dell',   'OptiPlex 7010'),
('MINSAL-WS-2026-152', 1, 3, 4,  'HP',     'EliteDesk 800 G1'),
('MINSAL-WS-2026-153', 1, 2, 5,  'Lenovo', 'ThinkCentre M83'),
('MINSAL-WS-2026-154', 1, 2, 6,  'Dell',   'OptiPlex 9020'),
('MINSAL-WS-2026-155', 1, 4, 7,  'HP',     'ProDesk 600 G1'),
('MINSAL-WS-2026-156', 1, 2, 8,  'Lenovo', 'ThinkCentre E73'),
('MINSAL-WS-2026-157', 1, 5, 9,  'Dell',   'OptiPlex 3020'),
('MINSAL-WS-2026-158', 1, 2, 10, 'HP',     'EliteDesk 705 G3'),
('MINSAL-WS-2026-159', 1, 2, 11, 'Lenovo', 'ThinkCentre M93'),
('MINSAL-WS-2026-160', 1, 1, 12, 'Dell',   'OptiPlex 5000'),
('MINSAL-WS-2026-161', 1, 2, 1,  'HP',     'ProDesk 400 G9'),
('MINSAL-WS-2026-162', 1, 2, 2,  'Lenovo', 'ThinkCentre M70s Gen 3'),
('MINSAL-WS-2026-163', 1, 3, 3,  'Dell',   'OptiPlex 3000'),
('MINSAL-WS-2026-164', 1, 2, 4,  'HP',     'EliteDesk 600 G9'),
('MINSAL-WS-2026-165', 1, 2, 5,  'Lenovo', 'ThinkCentre M70q Gen 4'),
('MINSAL-WS-2026-166', 1, 4, 6,  'Dell',   'OptiPlex 3000 Micro'),
('MINSAL-WS-2026-167', 1, 2, 7,  'HP',     'ProDesk 405 G8'),
('MINSAL-WS-2026-168', 1, 1, 8,  'Lenovo', 'ThinkCentre M90q Gen 3'),
('MINSAL-WS-2026-169', 1, 2, 9,  'Dell',   'OptiPlex 7090 MT'),
('MINSAL-WS-2026-170', 1, 5, 10, 'HP',     'EliteDesk 800 G8'),

-- LAPTOPS (Tipo 2) --
('MINSAL-LT-2026-101', 2, 2, 1,  'Lenovo', 'ThinkPad E14'),
('MINSAL-LT-2026-102', 2, 2, 2,  'HP',     'ProBook 450 G8'),
('MINSAL-LT-2026-103', 2, 3, 3,  'Dell',   'Latitude 5420'),
('MINSAL-LT-2026-104', 2, 2, 4,  'Lenovo', 'ThinkPad L14'),
('MINSAL-LT-2026-105', 2, 4, 5,  'HP',     'EliteBook 840 G8'),
('MINSAL-LT-2026-106', 2, 2, 6,  'Dell',   'Latitude 3420'),
('MINSAL-LT-2026-107', 2, 1, 7,  'Lenovo', 'ThinkPad T15'),
('MINSAL-LT-2026-108', 2, 2, 8,  'HP',     'ProBook 640 G8'),
('MINSAL-LT-2026-109', 2, 2, 9,  'Dell',   'Latitude 5520'),
('MINSAL-LT-2026-110', 2, 5, 10, 'Lenovo', 'ThinkPad X1 Carbon'),
('MINSAL-LT-2026-111', 2, 2, 11, 'HP',     'EliteBook 850 G8'),
('MINSAL-LT-2026-113', 2, 2, 12, 'Dell',   'Vostro 5510'),
('MINSAL-LT-2026-114', 2, 3, 1,  'Lenovo', 'IdeaPad 5 Pro'),
('MINSAL-LT-2026-115', 2, 2, 2,  'HP',     'ProBook 455 G8'),
('MINSAL-LT-2026-116', 2, 2, 3,  'Dell',   'Latitude 7420'),
('MINSAL-LT-2026-117', 2, 4, 4,  'Lenovo', 'ThinkPad E15'),
('MINSAL-LT-2026-118', 2, 2, 5,  'HP',     'EliteBook 830 G8'),
('MINSAL-LT-2026-119', 2, 1, 6,  'Dell',   'Latitude 5320'),
('MINSAL-LT-2026-120', 2, 2, 7,  'Lenovo', 'ThinkPad L15'),
('MINSAL-LT-2026-121', 2, 2, 8,  'HP',     'ProBook 430 G8'),
('MINSAL-LT-2026-122', 2, 5, 9,  'Dell',   'Latitude 3520'),
('MINSAL-LT-2026-123', 2, 2, 10, 'Lenovo', 'ThinkPad T14s'),
('MINSAL-LT-2026-124', 2, 2, 11, 'HP',     'EliteBook 745 G8'),
('MINSAL-LT-2026-125', 2, 3, 12, 'Dell',   'Latitude 9420'),
('MINSAL-LT-2026-126', 2, 2, 1,  'Lenovo', 'ThinkPad X13'),
('MINSAL-LT-2026-127', 2, 2, 2,  'HP',     'ProBook 650 G8'),
('MINSAL-LT-2026-128', 2, 4, 3,  'Dell',   'Latitude 5430'),
('MINSAL-LT-2026-129', 2, 2, 4,  'Lenovo', 'ThinkPad P14s'),
('MINSAL-LT-2026-130', 2, 2, 5,  'HP',     'ZBook Firefly 14 G8'),
('MINSAL-LT-2026-131', 2, 1, 6,  'Dell',   'Precision 3560'),
('MINSAL-LT-2026-132', 2, 2, 7,  'Lenovo', 'ThinkPad P15s'),
('MINSAL-LT-2026-133', 2, 2, 8,  'HP',     'EliteBook 845 G8'),
('MINSAL-LT-2026-134', 2, 3, 9,  'Dell',   'Latitude 7520'),
('MINSAL-LT-2026-135', 2, 2, 10, 'Lenovo', 'ThinkPad T14 Gen 2'),
('MINSAL-LT-2026-136', 2, 5, 11, 'HP',     'ProBook 445 G8'),
('MINSAL-LT-2026-137', 2, 2, 12, 'Dell',   'Vostro 5620'),
('MINSAL-LT-2026-138', 2, 2, 1,  'Lenovo', 'IdeaPad Slim 5'),
('MINSAL-LT-2026-139', 2, 4, 2,  'HP',     'ProBook 470 G8'),
('MINSAL-LT-2026-140', 2, 2, 3,  'Dell',   'Latitude 3320'),
('MINSAL-LT-2026-141', 2, 2, 4,  'Lenovo', 'ThinkPad E14 Gen 3'),
('MINSAL-LT-2026-142', 2, 1, 5,  'HP',     'EliteBook 860 G9'),
('MINSAL-LT-2026-143', 2, 2, 6,  'Dell',   'Latitude 5530'),
('MINSAL-LT-2026-144', 2, 2, 7,  'Lenovo', 'ThinkPad L13'),
('MINSAL-LT-2026-145', 2, 3, 8,  'HP',     'ProBook 440 G9'),
('MINSAL-LT-2026-146', 2, 2, 9,  'Dell',   'Inspiron 15 3000'),
('MINSAL-LT-2026-147', 2, 2, 10, 'Lenovo', 'V15 G2'),
('MINSAL-LT-2026-148', 2, 5, 11, 'HP',     'ProBook 635 Aero G8'),
('MINSAL-LT-2026-149', 2, 2, 12, 'Dell',   'Latitude 5440'),
('MINSAL-LT-2026-150', 2, 2, 1,  'Lenovo', 'ThinkPad T16'),
('MINSAL-LT-2026-151', 2, 4, 2,  'HP',     'EliteBook 1040 G9'),
('MINSAL-LT-2026-152', 2, 2, 3,  'Dell',   'Latitude 5480'),
('MINSAL-LT-2026-153', 2, 3, 4,  'Lenovo', 'ThinkPad T470'),
('MINSAL-LT-2026-154', 2, 1, 5,  'HP',     'EliteBook 840 G5'),
('MINSAL-LT-2026-155', 2, 2, 6,  'Dell',   'Latitude 3480'),
('MINSAL-LT-2026-156', 2, 2, 7,  'Lenovo', 'ThinkPad E470'),
('MINSAL-LT-2026-157', 2, 4, 8,  'HP',     'ProBook 640 G4'),
('MINSAL-LT-2026-158', 2, 2, 9,  'Dell',   'Latitude 5490'),
('MINSAL-LT-2026-159', 2, 5, 10, 'Lenovo', 'ThinkPad L470'),
('MINSAL-LT-2026-160', 2, 2, 11, 'HP',     'EliteBook 840 G4'),
('MINSAL-LT-2026-161', 2, 2, 12, 'Dell',   'Vostro 5470'),
('MINSAL-LT-2026-162', 2, 3, 1,  'Lenovo', 'ThinkPad X270'),
('MINSAL-LT-2026-163', 2, 2, 2,  'HP',     'ProBook 450 G4'),
('MINSAL-LT-2026-164', 2, 2, 3,  'Dell',   'Latitude 3490'),
('MINSAL-LT-2026-165', 2, 4, 4,  'Lenovo', 'IdeaPad 320'),
('MINSAL-LT-2026-166', 2, 2, 5,  'HP',     'EliteBook 850 G5'),
('MINSAL-LT-2026-167', 2, 1, 6,  'Dell',   'Inspiron 15 5000'),
('MINSAL-LT-2026-168', 2, 2, 7,  'Lenovo', 'V330-14'),
('MINSAL-LT-2026-169', 2, 2, 8,  'HP',     'ProBook 455 G5'),
('MINSAL-LT-2026-170', 2, 5, 9,  'Dell',   'Latitude 7490'),

-- SERVIDORES (Tipo 3) --
('MINSAL-SRV-2026-101', 3, 2, 2,  'HP',     'ProLiant DL360 Gen10'),
('MINSAL-SRV-2026-102', 3, 2, 2,  'Dell',   'PowerEdge R640'),
('MINSAL-SRV-2026-103', 3, 3, 5,  'Lenovo', 'ThinkSystem SR630'),
('MINSAL-SRV-2026-104', 3, 2, 5,  'HP',     'ProLiant DL380 Gen10'),
('MINSAL-SRV-2026-105', 3, 4, 7,  'Dell',   'PowerEdge R440'),
('MINSAL-SRV-2026-106', 3, 2, 7,  'Lenovo', 'ThinkSystem SR530'),
('MINSAL-SRV-2026-107', 3, 2, 9,  'HP',     'ProLiant ML350 Gen10'),
('MINSAL-SRV-2026-108', 3, 1, 9,  'Dell',   'PowerEdge T440'),
('MINSAL-SRV-2026-109', 3, 2, 11, 'Lenovo', 'ThinkSystem ST550'),
('MINSAL-SRV-2026-110', 3, 2, 11, 'HP',     'ProLiant DL160 Gen10'),
('MINSAL-SRV-2026-111', 3, 5, 2,  'Dell',   'PowerEdge R540'),
('MINSAL-SRV-2026-112', 3, 2, 2,  'Lenovo', 'ThinkSystem SR650'),
('MINSAL-SRV-2026-113', 3, 2, 5,  'HP',     'ProLiant DL580 Gen10'),
('MINSAL-SRV-2026-114', 3, 3, 7,  'Dell',   'PowerEdge R750'),
('MINSAL-SRV-2026-115', 3, 2, 7,  'Lenovo', 'ThinkSystem SR850'),
('MINSAL-SRV-2026-116', 3, 4, 9,  'HP',     'ProLiant DL20 Gen10'),
('MINSAL-SRV-2026-117', 3, 2, 9,  'Dell',   'PowerEdge R250'),
('MINSAL-SRV-2026-118', 3, 2, 11, 'Lenovo', 'ThinkSystem SR250'),
('MINSAL-SRV-2026-119', 3, 1, 12, 'HP',     'ProLiant ML110 Gen10'),
('MINSAL-SRV-2026-120', 3, 2, 12, 'Dell',   'PowerEdge T150'),

-- IMPRESORAS (Tipo 4) --
('MINSAL-PRN-2026-101', 4, 2, 1,  'HP',      'LaserJet Pro M404n'),
('MINSAL-PRN-2026-102', 4, 2, 1,  'Epson',   'EcoTank L3250'),
('MINSAL-PRN-2026-103', 4, 3, 2,  'Canon',   'PIXMA G6020'),
('MINSAL-PRN-2026-104', 4, 2, 2,  'Brother', 'MFC-L2710DW'),
('MINSAL-PRN-2026-105', 4, 4, 3,  'HP',      'OfficeJet Pro 9015e'),
('MINSAL-PRN-2026-106', 4, 2, 3,  'Epson',   'WorkForce WF-7840'),
('MINSAL-PRN-2026-107', 4, 2, 4,  'Canon',   'imageCLASS MF445dw'),
('MINSAL-PRN-2026-108', 4, 1, 4,  'Brother', 'HL-L2395DW'),
('MINSAL-PRN-2026-109', 4, 2, 5,  'HP',      'Color LaserJet Pro M255dw'),
('MINSAL-PRN-2026-110', 4, 2, 5,  'Epson',   'EcoTank ET-5850'),
('MINSAL-PRN-2026-111', 4, 5, 6,  'Canon',   'MAXIFY GX7020'),
('MINSAL-PRN-2026-112', 4, 2, 6,  'Brother', 'MFC-J5945DW'),
('MINSAL-PRN-2026-113', 4, 2, 7,  'HP',      'LaserJet Enterprise M507dn'),
('MINSAL-PRN-2026-114', 4, 3, 7,  'Epson',   'EcoTank L6270'),
('MINSAL-PRN-2026-115', 4, 2, 8,  'Canon',   'imageCLASS MF455dw'),
('MINSAL-PRN-2026-116', 4, 4, 8,  'Brother', 'MFC-L3770CDW'),
('MINSAL-PRN-2026-117', 4, 2, 9,  'HP',      'OfficeJet Pro 8035e'),
('MINSAL-PRN-2026-118', 4, 1, 9,  'Epson',   'EcoTank L4260'),
('MINSAL-PRN-2026-119', 4, 2, 10, 'Canon',   'PIXMA TR8620'),
('MINSAL-PRN-2026-120', 4, 2, 10, 'Brother', 'HL-L3270CDW'),
('MINSAL-PRN-2026-121', 4, 5, 11, 'HP',      'Color LaserJet Enterprise M553dn'),
('MINSAL-PRN-2026-122', 4, 2, 11, 'Epson',   'WorkForce Pro WF-4830'),
('MINSAL-PRN-2026-123', 4, 2, 12, 'Canon',   'imagePROGRAF TM-200'),
('MINSAL-PRN-2026-124', 4, 3, 12, 'Brother', 'MFC-J6945DW'),
('MINSAL-PRN-2026-125', 4, 2, 1,  'HP',      'LaserJet Pro MFP M428fdn'),
('MINSAL-PRN-2026-126', 4, 2, 2,  'Epson',   'EcoTank L8050'),
('MINSAL-PRN-2026-127', 4, 4, 3,  'Canon',   'PIXMA G3260'),
('MINSAL-PRN-2026-128', 4, 2, 4,  'Brother', 'DCP-L2550DW'),
('MINSAL-PRN-2026-129', 4, 1, 5,  'HP',      'LaserJet Pro M329dw'),
('MINSAL-PRN-2026-130', 4, 2, 6,  'Epson',   'EcoTank L3210'),
('MINSAL-PRN-2026-131', 4, 2, 7,  'Canon',   'MAXIFY MB5420'),
('MINSAL-PRN-2026-132', 4, 5, 8,  'Brother', 'MFC-L2750DW'),
('MINSAL-PRN-2026-133', 4, 2, 9,  'HP',      'Color LaserJet Pro MFP M283fdw'),
('MINSAL-PRN-2026-134', 4, 2, 10, 'Epson',   'WorkForce WF-2930'),
('MINSAL-PRN-2026-135', 4, 3, 11, 'Canon',   'imageCLASS MF264dw II'),
('MINSAL-PRN-2026-136', 4, 2, 12, 'Brother', 'HL-L5100DN'),
('MINSAL-PRN-2026-137', 4, 2, 1,  'HP',      'LaserJet Enterprise MFP M635h'),
('MINSAL-PRN-2026-138', 4, 4, 2,  'Epson',   'EcoTank L6490'),
('MINSAL-PRN-2026-139', 4, 2, 3,  'Canon',   'imageCLASS D1650'),
('MINSAL-PRN-2026-140', 4, 2, 4,  'Brother', 'MFC-L8610CDW'),
('MINSAL-PRN-2026-141', 4, 1, 5,  'HP',      'OfficeJet 250 Mobile'),
('MINSAL-PRN-2026-142', 4, 2, 6,  'Epson',   'EcoTank ET-2800'),
('MINSAL-PRN-2026-143', 4, 2, 7,  'Canon',   'PIXMA MX922'),
('MINSAL-PRN-2026-144', 4, 3, 8,  'Brother', 'HL-L9310CDW'),
('MINSAL-PRN-2026-145', 4, 2, 9,  'HP',      'LaserJet Pro M118dw'),
('MINSAL-PRN-2026-146', 4, 5, 10, 'Epson',   'EcoTank L3110'),
('MINSAL-PRN-2026-147', 4, 2, 11, 'Canon',   'MAXIFY iB4120'),
('MINSAL-PRN-2026-148', 4, 2, 12, 'Brother', 'MFC-J995DW'),
('MINSAL-PRN-2026-149', 4, 4, 1,  'HP',      'Color LaserJet Pro M182nw'),
('MINSAL-PRN-2026-150', 4, 2, 2,  'Epson',   'EcoTank L5290'),
('MINSAL-PRN-2026-151', 4, 2, 3,  'Canon',   'imageCLASS MF741Cdw'),
('MINSAL-PRN-2026-152', 4, 1, 4,  'Brother', 'MFC-L5700DW'),
('MINSAL-PRN-2026-153', 4, 2, 5,  'HP',      'LaserJet Pro M15w'),
('MINSAL-PRN-2026-154', 4, 2, 6,  'Epson',   'EcoTank L3150'),
('MINSAL-PRN-2026-155', 4, 3, 7,  'Canon',   'PIXMA G4210'),
('MINSAL-PRN-2026-156', 4, 2, 8,  'Brother', 'HL-L2350DW'),
('MINSAL-PRN-2026-157', 4, 2, 9,  'HP',      'OfficeJet Pro 7740'),
('MINSAL-PRN-2026-158', 4, 5, 10, 'Epson',   'EcoTank ET-3850'),
('MINSAL-PRN-2026-159', 4, 2, 11, 'Canon',   'imageCLASS MF269dw'),
('MINSAL-PRN-2026-160', 4, 2, 12, 'Brother', 'MFC-J4535DW'),
('MINSAL-PRN-2026-161', 4, 4, 1,  'HP',      'LaserJet Pro MFP M130fw'),
('MINSAL-PRN-2026-162', 4, 2, 2,  'Epson',   'EcoTank L4150'),
('MINSAL-PRN-2026-163', 4, 2, 3,  'Canon',   'PIXMA TS8320'),
('MINSAL-PRN-2026-164', 4, 1, 4,  'Brother', 'DCP-L5650DN'),
('MINSAL-PRN-2026-165', 4, 2, 5,  'HP',      'Color LaserJet Pro MFP M479fdw'),
('MINSAL-PRN-2026-166', 4, 3, 6,  'Epson',   'WorkForce Pro WF-3820'),
('MINSAL-PRN-2026-167', 4, 2, 7,  'Canon',   'MAXIFY GX4020'),
('MINSAL-PRN-2026-168', 4, 2, 8,  'Brother', 'MFC-L2710DW'),
('MINSAL-PRN-2026-169', 4, 5, 9,  'HP',      'LaserJet Enterprise M406dn'),
('MINSAL-PRN-2026-170', 4, 2, 10, 'Epson',   'EcoTank L6580'),

-- PLOTTERS (Tipo 5) --
('MINSAL-PLT-2026-101', 5, 2, 3,  'HP',     'DesignJet T650'),
('MINSAL-PLT-2026-102', 5, 2, 3,  'Canon',  'imagePROGRAF TX-3100'),
('MINSAL-PLT-2026-103', 5, 3, 3,  'Epson',  'SureColor T3170'),
('MINSAL-PLT-2026-104', 5, 4, 3,  'HP',     'DesignJet T1700'),
('MINSAL-PLT-2026-105', 5, 2, 3,  'Canon',  'imagePROGRAF PRO-4100'),
('MINSAL-PLT-2026-106', 5, 1, 3,  'Epson',  'SureColor SC-T5100'),
('MINSAL-PLT-2026-107', 5, 2, 3,  'HP',     'DesignJet Z6 24-in'),
('MINSAL-PLT-2026-108', 5, 5, 3,  'Canon',  'imagePROGRAF TM-300'),
('MINSAL-PLT-2026-109', 5, 2, 3,  'Epson',  'SureColor T7770D'),
('MINSAL-PLT-2026-110', 5, 3, 3,  'HP',     'DesignJet T2600'),
('MINSAL-PLT-2026-111', 5, 2, 3,  'Canon',  'imagePROGRAF TX-4100'),
('MINSAL-PLT-2026-112', 5, 4, 3,  'Epson',  'SureColor T5170M'),
('MINSAL-PLT-2026-113', 5, 2, 2,  'HP',     'DesignJet Studio 36-in'),
('MINSAL-PLT-2026-114', 5, 2, 2,  'Canon',  'imagePROGRAF TM-350'),
('MINSAL-PLT-2026-115', 5, 2, 3,  'HP',     'DesignJet Z9+ 44-in'),
('MINSAL-PLT-2026-116', 5, 4, 3,  'Canon',  'imagePROGRAF PRO-2100'),
('MINSAL-PLT-2026-117', 5, 2, 3,  'Epson',  'SureColor SC-P9500'),
('MINSAL-PLT-2026-118', 5, 1, 3,  'HP',     'DesignJet XL 3600 MFP'),
('MINSAL-PLT-2026-119', 5, 2, 2,  'Canon',  'imagePROGRAF TM-400'),
('MINSAL-PLT-2026-120', 5, 3, 3,  'Epson',  'SureColor T5470M'),
('MINSAL-PLT-2026-121', 5, 2, 3,  'HP',     'DesignJet T930'),
('MINSAL-PLT-2026-122', 5, 5, 2,  'Canon',  'imagePROGRAF TX-2000'),
('MINSAL-PLT-2026-123', 5, 2, 3,  'Epson',  'SureColor SC-T3100'),
('MINSAL-PLT-2026-124', 5, 2, 3,  'HP',     'DesignJet Z6dr 44-in'),
('MINSAL-PLT-2026-125', 5, 4, 3,  'Canon',  'imagePROGRAF PRO-6100'),
('MINSAL-PLT-2026-126', 5, 2, 3,  'Epson',  'SureColor SC-P7500'),
('MINSAL-PLT-2026-127', 5, 1, 3,  'HP',     'DesignJet T230 24-in'),
('MINSAL-PLT-2026-128', 5, 2, 2,  'Canon',  'imagePROGRAF TM-305'),
('MINSAL-PLT-2026-129', 5, 3, 3,  'Epson',  'SureColor T7100'),
('MINSAL-PLT-2026-130', 5, 2, 3,  'HP',     'DesignJet T525 36-in');

-- ============================================================
-- SELECT COUNT(*) FROM Equipos;
-- Con los 7 originales: total esperado = 307
-- ============================================================

INSERT INTO Equipos (Codigo_Inventario, ID_Tipo, ID_Estado, ID_Ubicacion, Marca, Modelo) VALUES
-- ESCRITORIOS adicionales --
('MINSAL-WS-2026-171', 1, 2, 1,  'Dell',   'OptiPlex 7000'),
('MINSAL-WS-2026-172', 1, 3, 2,  'HP',     'EliteDesk 600 G6'),
('MINSAL-WS-2026-173', 1, 2, 3,  'Lenovo', 'ThinkCentre M70s Gen 2'),
('MINSAL-WS-2026-174', 1, 4, 4,  'Dell',   'Vostro 3710'),
('MINSAL-WS-2026-175', 1, 2, 5,  'HP',     'ProDesk 405 G6'),
('MINSAL-WS-2026-176', 1, 1, 6,  'Lenovo', 'ThinkCentre M90q Gen 2'),
('MINSAL-WS-2026-177', 1, 2, 7,  'Dell',   'OptiPlex 5090 Tower'),
('MINSAL-WS-2026-178', 1, 5, 8,  'HP',     'EliteDesk 805 G8'),
('MINSAL-WS-2026-179', 1, 2, 9,  'Lenovo', 'ThinkCentre M80q Gen 3'),
('MINSAL-WS-2026-180', 1, 2, 10, 'Dell',   'OptiPlex 3090 Tower'),
-- LAPTOPS adicionales --
('MINSAL-LT-2026-171', 2, 2, 1,  'HP',     'ProBook 440 G10'),
('MINSAL-LT-2026-172', 2, 3, 2,  'Dell',   'Latitude 5550'),
('MINSAL-LT-2026-173', 2, 2, 3,  'Lenovo', 'ThinkPad T14 Gen 4'),
('MINSAL-LT-2026-174', 2, 1, 4,  'HP',     'EliteBook 640 G10'),
('MINSAL-LT-2026-175', 2, 2, 5,  'Dell',   'Latitude 3550'),
('MINSAL-LT-2026-176', 2, 4, 6,  'Lenovo', 'ThinkPad L14 Gen 4'),
('MINSAL-LT-2026-177', 2, 2, 7,  'HP',     'ProBook 450 G10'),
('MINSAL-LT-2026-178', 2, 2, 8,  'Dell',   'Vostro 5630'),
('MINSAL-LT-2026-179', 2, 5, 9,  'Lenovo', 'ThinkPad E15 Gen 4'),
('MINSAL-LT-2026-180', 2, 2, 10, 'HP',     'EliteBook 860 G11'),
-- IMPRESORAS adicionales --
('MINSAL-PRN-2026-171', 4, 2, 1,  'Brother', 'MFC-L9570CDW'),
('MINSAL-PRN-2026-172', 4, 3, 2,  'HP',      'LaserJet Pro M428dw'),
('MINSAL-PRN-2026-173', 4, 2, 3,  'Epson',   'EcoTank L6170'),
('MINSAL-PRN-2026-174', 4, 1, 4,  'Canon',   'PIXMA G7020'),
('MINSAL-PRN-2026-175', 4, 2, 5,  'Brother', 'HL-L8260CDW'),
('MINSAL-PRN-2026-176', 4, 4, 6,  'HP',      'Color LaserJet Enterprise M751dn'),
('MINSAL-PRN-2026-177', 4, 2, 7,  'Epson',   'EcoTank ET-16500'),
('MINSAL-PRN-2026-178', 4, 2, 8,  'Canon',   'imageCLASS MF753Cdw'),
('MINSAL-PRN-2026-179', 4, 5, 9,  'Brother', 'MFC-L6900DW'),
('MINSAL-PRN-2026-180', 4, 2, 10, 'HP',      'LaserJet Pro MFP M329dn'),
('MINSAL-PRN-2026-181', 4, 2, 11, 'Epson',   'WorkForce Pro WF-C5710'),
('MINSAL-PRN-2026-182', 4, 3, 12, 'Canon',   'imageCLASS MF656Cdw'),
('MINSAL-PRN-2026-183', 4, 2, 1,  'Brother', 'DCP-L2640DW'),
('MINSAL-PRN-2026-184', 4, 1, 2,  'HP',      'OfficeJet Pro 8028e'),
('MINSAL-PRN-2026-185', 4, 2, 3,  'Epson',   'EcoTank ET-4850'),
('MINSAL-PRN-2026-186', 4, 2, 4,  'Canon',   'PIXMA TR4622'),
('MINSAL-PRN-2026-187', 4, 4, 5,  'Brother', 'HL-L2460DW'),
('MINSAL-PRN-2026-188', 4, 2, 6,  'HP',      'LaserJet MFP M140we'),
('MINSAL-PRN-2026-189', 4, 5, 7,  'Epson',   'EcoTank ET-2400'),
('MINSAL-PRN-2026-190', 4, 2, 8,  'Canon',   'MAXIFY GX1020'),
-- SERVIDORES adicionales --
('MINSAL-SRV-2026-121', 3, 2, 2,  'Dell',   'PowerEdge R760'),
('MINSAL-SRV-2026-122', 3, 3, 5,  'HP',     'ProLiant DL360 Gen11'),
('MINSAL-SRV-2026-123', 3, 2, 7,  'Lenovo', 'ThinkSystem SR630 V3'),
('MINSAL-SRV-2026-124', 3, 4, 9,  'Dell',   'PowerEdge R460'),
('MINSAL-SRV-2026-125', 3, 2, 11, 'HP',     'ProLiant DL380 Gen11'),
-- PLOTTERS adicionales --
('MINSAL-PLT-2026-131', 5, 2, 3,  'HP',    'DesignJet T650 36-in'),
('MINSAL-PLT-2026-132', 5, 1, 3,  'Canon', 'imagePROGRAF TM-250'),
('MINSAL-PLT-2026-133', 5, 2, 3,  'Epson', 'SureColor SC-T2100'),
('MINSAL-PLT-2026-134', 5, 3, 2,  'HP',    'DesignJet T940 36-in'),
('MINSAL-PLT-2026-135', 5, 2, 3,  'Canon', 'imagePROGRAF TX-5400'),
('MINSAL-PLT-2026-136', 5, 4, 3,  'Epson', 'SureColor SC-T3100N'),
('MINSAL-PLT-2026-137', 5, 2, 3,  'HP',    'DesignJet Z9+ Pro 64-in'),
('MINSAL-PLT-2026-138', 5, 2, 2,  'Canon', 'imagePROGRAF PRO-561'),
('MINSAL-PLT-2026-139', 5, 5, 3,  'Epson', 'SureColor SC-P6500'),
('MINSAL-PLT-2026-140', 5, 2, 3,  'HP',    'DesignJet T1600dr 36-in');
