-- ==========================================================
-- SCRIPT DE BASE DE DATOS REAL PARA SQL SERVER (WINDOWS)
-- Proyecto: SIGEMPI
-- Generado automáticamente puro código desde el entorno local
-- ==========================================================

CREATE DATABASE SIGEMPI_DB;
GO
USE SIGEMPI_DB;
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Roles_User
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Roles_User] (
    [ID_Rol] INT NOT NULL,
    [Nombre_Rol] VARCHAR(50) NOT NULL,
    [Descripcion_Rol] VARCHAR(250) NULL,
    [Fecha_CreacionRol] DATETIME NULL DEFAULT (getdate())
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Estado_Usuario
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Estado_Usuario] (
    [ID_EstadoUsuario] INT NOT NULL,
    [Nombre_EstadoUsuario] VARCHAR(40) NOT NULL,
    [Fecha_CreacionEstadoUsuario] DATETIME NULL DEFAULT (getdate())
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Users
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Users] (
    [ID_User] INT NOT NULL,
    [ID_Rol] INT NULL,
    [ID_EstadoUsuario] INT NULL,
    [Usuario] VARCHAR(50) NOT NULL,
    [Correo_User] VARCHAR(100) NOT NULL,
    [Password_Hash] VARCHAR(200) NOT NULL,
    [Fecha_CreacionUser] DATETIME NULL DEFAULT (getdate())
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Departamentos_Geo
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Departamentos_Geo] (
    [ID_DeptoGeo] INT NOT NULL,
    [Nombre_DeptoGeo] VARCHAR(100) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Municipios_Geo
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Municipios_Geo] (
    [ID_MunicipioGeo] INT NOT NULL,
    [ID_DeptoGeo] INT NULL,
    [Nombre_MunicipioGeo] VARCHAR(100) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Edificios_Sedes
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Edificios_Sedes] (
    [ID_Edificio] INT NOT NULL,
    [ID_MunicipioGeo] INT NULL,
    [Nombre_Edificio] VARCHAR(150) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Departamentos_Inst
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Departamentos_Inst] (
    [ID_DepartamentoInst] INT NOT NULL,
    [Nombre_DepartamentoInst] VARCHAR(100) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Ubicaciones_Master
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Ubicaciones_Master] (
    [ID_UbicacionMaster] INT NOT NULL,
    [ID_Edificio] INT NULL,
    [ID_DepartamentoInst] INT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Tipos_Equipo
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Tipos_Equipo] (
    [ID_Tipo] INT NOT NULL,
    [Nombre_Tipo] VARCHAR(40) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Estado_Equipo
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Estado_Equipo] (
    [ID_Estado] INT NOT NULL,
    [Nombre_Estado] VARCHAR(50) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Equipos
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Equipos] (
    [ID_Equipo] INT NOT NULL,
    [Codigo_Inventario] VARCHAR(50) NOT NULL,
    [ID_Tipo] INT NULL,
    [ID_Estado] INT NULL,
    [ID_UbicacionMaster] INT NULL,
    [Marca] VARCHAR(60) NULL,
    [Modelo] VARCHAR(100) NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Catalogo_EstadoMantenimiento
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Catalogo_EstadoMantenimiento] (
    [ID_EstadoMantenimiento] INT NOT NULL,
    [Nombre_EstadoMantenimiento] VARCHAR(30) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Mantenimientos
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Mantenimientos] (
    [ID_Mantenimiento] INT NOT NULL,
    [ID_Equipo] INT NULL,
    [ID_Tecnico] INT NULL,
    [Fecha_Programada] DATE NOT NULL,
    [ID_EstadoMantenimiento] INT NULL,
    [Fecha_Ingreso] DATETIME NULL DEFAULT (getdate())
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Mantenimiento_Detalle
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Mantenimiento_Detalle] (
    [ID_Detalle] INT NOT NULL,
    [ID_Mantenimiento] INT NULL,
    [ID_TecnicoIntervino] INT NULL,
    [Fecha_Registro] DATETIME NULL DEFAULT (getdate()),
    [Accion_Realizada] VARCHAR(500) NOT NULL,
    [Observaciones_Tecnicas] VARCHAR(1000) NOT NULL
);
GO

-- ----------------------------------------------------------
-- Estructura para la tabla: Historial_Cambios_Estado
-- ----------------------------------------------------------
CREATE TABLE [dbo].[Historial_Cambios_Estado] (
    [ID_Historial] INT NOT NULL,
    [ID_Mantenimiento] INT NULL,
    [ID_EstadoAnterior] INT NULL,
    [ID_EstadoNuevo] INT NULL,
    [ID_TecnicoAnterior] INT NULL,
    [ID_TecnicoNuevo] INT NULL,
    [ID_UsuarioModifico] INT NULL,
    [Fecha_Cambio] DATETIME NULL DEFAULT (getdate()),
    [Motivo_Cambio] VARCHAR(500) NULL
);
GO

