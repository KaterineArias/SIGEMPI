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
Tipo varchar(40) unique not null,
Fecha_CreacionTipo datetime DEFAULT GETDATE()
)

INSERT INTO Tipos_Equipo(Tipo)
VALUES ('Escritorio'),('Laptop'),('Servidor'),('Impresora'),('Plotter')

CREATE TABLE Estado_Equipo
(
ID_Estado INT IDENTITY(1,1) PRIMARY KEY,
Estado varchar(50) unique not null,
Fecha_CreacionEstado datetime DEFAULT GETDATE()
)

INSERT INTO Estado_Equipo(Estado)
Values ('Dañado'),('Activo'),('Bodega'),('Inactivo'),('De Baja')

CREATE TABLE Equipos
(
ID_Equipo int IDENTITY(1,1) PRIMARY KEY,
Codigo_Inventario varchar(50) UNIQUE not null, -- El código de la viñeta física de la institución
ID_Tipo INT FOREIGN KEY REFERENCES Tipos_Equipo(ID_Tipo),
ID_Estado INT FOREIGN KEY REFERENCES Estado_Equipo(ID_Estado),
Ubicacion varchar(300) not null,
Marca varchar(60),
Modelo varchar(100)
)

INSERT INTO Equipos (Codigo_Inventario,ID_Estado,ID_Tipo,Ubicacion,Marca,Modelo)
Values ('10011','1','1','Santa Ana, Edificio #2','Dell','v1'),
('10012','2','1','Usulutan, Galerias','Apple','Mac Pro'),
 ('10013','2','1','San Miguel, Galerias','Apple','MacBook Neo'),
('10014','1','1','San Salvador, SS Centro','Lenovo','v2')

CREATE TABLE Catalogo_EstadoMantenimiento
(
ID_EstadoMantenimiento INT IDENTITY(1,1) PRIMARY KEY,
EstadoMantenimiento varchar(30) unique not null
)

INSERT INTO Catalogo_EstadoMantenimiento(EstadoMantenimiento)
Values ('Programado'),('Completado'),('Reprogramado'),('Cancelado')

CREATE TABLE Mantenimientos
(
ID_Mantenimiento INT PRIMARY KEY IDENTITY (1,1),
ID_Equipo INT FOREIGN KEY REFERENCES Equipos(ID_Equipo),
Fecha_Ingreso DATETIME DEFAULT GETDATE()
)

CREATE TABLE Historial_Mantenimiento
(
ID_Historial INT IDENTITY(1,1) PRIMARY KEY,
ID_Mantenimiento INT FOREIGN KEY REFERENCES Mantenimientos(ID_Mantenimiento),
ID_Tecnico INT FOREIGN KEY REFERENCES Users(ID_User),
ID_EstadoMantenimiento INT FOREIGN KEY REFERENCES Catalogo_EstadoMantenimiento(ID_EstadoMantenimiento),
Fecha DATETIME DEFAULT getdate(),--Fecha que guarda al momento de cambiar de estado
Observaciones varchar(1000)
)

