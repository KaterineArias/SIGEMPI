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
Fecha_Cierre DATETIME null
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
