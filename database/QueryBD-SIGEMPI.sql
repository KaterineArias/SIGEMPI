--CREATE DATABASE SIGEMPI_DB
--GO
--USE SIGEMPI_DB

CREATE TABLE Users
(
ID_User int IDENTITY(1,1) PRIMARY KEY,
Usuario varchar(50) UNIQUE not null,
Rol varchar(20) CHECK(Rol IN('Coordinador','Tecnico')),
Password_Hash varchar(200) not null,
-- Hash para agregarle un encriptado a las contrase�as
Fecha_Creacion datetime DEFAULT GETDATE() -- Para saber cu�ndo se registr� el usuario
)

CREATE TABLE Equipos
(
ID_Equipo int IDENTITY(1,1) PRIMARY KEY,
Codigo_Inventario varchar(50) UNIQUE not null, -- El c�digo de la vi�eta f�sica de la instituci�n
Tipo varchar(30) CHECK(Tipo IN('Escritorio','Laptop','Servidor','Impresora','Plotter')) not null,
Ubicacion varchar(300) not null,
Marca varchar(60),
Modelo varchar(100),
Estado varchar(50) CHECK(Estado IN ('Da�ado', 'Activo','Bodega','Inactivo','De Baja')) not null,
)

CREATE TABLE Mantenimientos
(
ID_Mantenimiento INT PRIMARY KEY IDENTITY (1,1),
ID_Equipo INT FOREIGN KEY REFERENCES Equipos(ID_Equipo),
ID_Tecnico INT FOREIGN KEY REFERENCES Users(ID_User),
Fecha_Programada date,
Fecha_Atencion date null,
Estado_Mantenimiento varchar(20) DEFAULT 'Programado' CHECK(Estado_Mantenimiento IN('Programado', 'Completado', 'Reprogramado', 'Cancelado')) not null,
Observaciones varchar(1000),
Fecha_Registro datetime DEFAULT GETDATE() -- Cu�ndo se ingres� este registro al sistema
)