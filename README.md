# SIGEMPI — Sistema de Gestión de Mantenimiento del Parque Informático

Sistema web desarrollado en Laravel para la gestión y seguimiento de mantenimientos de equipos informáticos.

---

## Universidad Don Bosco

| Campo | Detalle |
|---|---|
| **Asignatura** | Análisis y Diseño de Sistemas Informáticos (ADS941) |
| **Docente** | Ing. Héctor Alexander Valladares Pérez |
| **Proyecto** | Sistema de Gestión de Mantenimiento del Parque Informático (SIGEMPI) — Fase 2 |

---

## 👥 Integrantes

| Carnet | Nombres | Apellidos |
|---|---|---|
| OH240248 | Jonatan Alexander | Orellana Hernández |
| AG231993 | Katerine Esmeralda | Arias Grande |
| SC121824 | Oswaldo Wilfredo | Saravia Cuadra |
| CT201109 | Daniela Nicole | Cruz Torres |

---

## Descripción

SIGEMPI es una aplicación web que permite al Ministerio de Salud gestionar el mantenimiento preventivo y correctivo de su parque informático. El sistema contempla dos roles principales:

- **Coordinador** — administra usuarios, equipos, programa mantenimientos, consulta reportes y auditoría de cambios.
- **Técnico** — consulta sus asignaciones, registra el cierre de mantenimientos y revisa su historial.

### Funcionalidades principales

- Registro, modificación y consulta de equipos informáticos
- Gestión de usuarios con roles y auditoría de cambios
- Programación y seguimiento de mantenimientos preventivos
- Registro de acciones realizadas al completar un mantenimiento
- Reprogramación y cancelación de mantenimientos
- Historial completo de cambios de estado por mantenimiento
- Reportes filtrables por período, equipo y técnico
- Página de auditoría de cambios de estado
- Autenticación con restricción de acceso por rol

---

## Tecnologías utilizadas

- **Backend:** PHP 8.2 / Laravel 11
- **Base de datos:** Microsoft SQL Server 2019
- **Driver:** ODBC Driver 17 for SQL Server
- **Frontend:** Blade Templates, CSS personalizado, JavaScript vanilla
- **Servidor local:** Laravel Artisan (development server)

---

## Instalación local

### Requisitos previos

- PHP >= 8.2
- Composer
- SQL Server 2019 (o superior)
- ODBC Driver 17 for SQL Server
- Extensiones PHP habilitadas: `pdo_sqlsrv`, `sqlsrv`

### Pasos

**1. Clonar el repositorio**
```bash
git clone https://github.com/tu-usuario/sigempi.git
cd sigempi
```

**2. Instalar dependencias**
```bash
composer install
```

**3. Configurar el archivo de entorno**
```bash
cp .env.example .env
```

Edita el `.env` con los datos de tu instancia de SQL Server:

```env
APP_NAME=SIGEMPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlsrv
DB_HOST=localhost
DB_PORT=1433
DB_DATABASE=SIGEMPI_DB
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

CACHE_STORE=file
SESSION_DRIVER=file
```

**4. Generar la clave de la aplicación**
```bash
php artisan key:generate
```

**5. Crear la base de datos**

Ejecuta el script SQL incluido en la carpeta `/database/QueryBD-SIGEMPI.sql` y `/database/sigempi_test_data.sql`  desde SQL Server Management Studio (SSMS) para crear la base de datos, tablas y datos iniciales.

Luego ejecuta las migraciones de Laravel para la tabla de recuperación de contraseña de un solo uso:

```bash
php artisan migrate
```

**6. Iniciar el servidor**
```bash
php artisan serve
```

Accede en tu navegador a: [http://localhost:8000](http://localhost:8000)

---

## Credenciales de prueba

| Rol | Usuario | Contraseña |
|---|---|---|
| Coordinador | `admin` | `123456` |
| Técnico | `tecnico` | `123456` |

---

Proyecto académico — Todos los derechos reservados © 2026.
