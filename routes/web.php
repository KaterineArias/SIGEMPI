<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\EquipoController;
use Illuminate\Support\Facades\Route;

// ── AUTENTICACIÓN E INICIO DE SESIÓN ───────────────────────────────────────
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',  [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Primer login (Cambio de contraseña inicial)
Route::get('/primer-login',  [AuthController::class, 'showPrimerLogin'])->name('primer.login.form');
Route::post('/primer-login', [AuthController::class, 'setPrimerPassword'])->name('primer.login.post');

// Recuperación y Reseteo de contraseña
Route::get('/recuperar-password',      [AuthController::class, 'showSolicitarReset'])->name('password.solicitar');
Route::post('/recuperar-password',     [AuthController::class, 'solicitarReset'])->name('password.solicitar.post');
Route::get('/reset-password/{token}',  [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// 👑 CONEXIÓN HISTORIAL TÉCNICO: Enlace directo al método real del controlador
Route::get('/dashboard/tecnico/mantenimientos', [MantenimientoController::class, 'historialTecnico'])->name('mantenimientos.index-tecnico');

// ── INTERFACES PROTEGIDAS POR AUTENTICACIÓN ────────────────────────────────
Route::middleware(['autenticado'])->group(function () {

    // Redirección dinámica según rol del usuario logueado
    Route::get('/dashboard', function () {
        return match (session('rol')) {
            'Coordinador' => redirect()->route('dashboard.coordinador'),
            'Tecnico'     => redirect()->route('dashboard.tecnico'),
            default       => redirect()->route('login'),
        };
    })->name('dashboard');

    // 👑 PANEL DEL COORDINADOR GENERAL (Interactividad total enlazada a MantenimientoController)
    Route::get('/dashboard/coordinador', [MantenimientoController::class, 'index'])
        ->middleware('rol:Coordinador')
        ->name('dashboard.coordinador');

    // 🛡️ PANEL DEL TÉCNICO (Restaurado al controlador original para blindar variables nativas de tus compañeros)
    Route::get('/dashboard/tecnico', [DashboardController::class, 'tecnico'])
        ->middleware('rol:Tecnico')
        ->name('dashboard.tecnico');

    // ── Mantenimientos: Rutas específicas previas al Resource ──────────────
    Route::get('/mantenimientos/historial-cierres', [MantenimientoController::class, 'historialCierres'])
        ->name('mantenimientos.historial-cierres');

    Route::get('/mis-asignaciones', [MantenimientoController::class, 'misAsignaciones'])
        ->name('mantenimientos.mis-asignaciones');

    Route::get('/mis-cierres', [MantenimientoController::class, 'historialCierres'])
        ->name('mantenimientos.historial');

    Route::patch('mantenimientos/{mantenimiento}/estado', [MantenimientoController::class, 'cambiarEstado'])
        ->name('mantenimientos.cambiarEstado');

    // Controlador de Recursos de Mantenimientos
    Route::resource('mantenimientos', MantenimientoController::class);

    // ── Equipos: Rutas específicas previas al Resource ─────────────────────
    Route::get('/equipos/tecnico', [EquipoController::class, 'indexTecnico'])
        ->name('equipos.tecnico.index');

    Route::get('/equipos/{id}/historial', [EquipoController::class, 'historial'])
        ->name('equipos.historial');

    Route::resource('equipos', EquipoController::class);

    // ── Gestión de Usuarios (Exclusivo del Rol Coordinador) ──────────────────
    Route::middleware('rol:Coordinador')->group(function () {
        Route::resource('usuarios', UserController::class)->only(['index', 'create', 'store']);
        Route::get('usuarios/{usuario}/edit',  [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}',       [UserController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{usuario}/estado', [UserController::class, 'cambiarEstado'])->name('usuarios.estado');
        Route::get('usuarios/{usuario}/historial', [UserController::class, 'historial'])->name('usuarios.historial');
    });

    // ── Perfil propio del Usuario Autenticado (Cualquier Rol) ────────────────
    Route::get('/mi-perfil', [UserController::class, 'perfilForm'])->name('perfil.form');
    Route::put('/mi-perfil', [UserController::class, 'perfilUpdate'])->name('perfil.update');

    // ── Reportes Generales ───────────────────────────────────────────────────
    Route::get('/reportes', fn() => view('reportes.index'))->name('reportes.index');
});

// Endpoint de contingencia para pruebas locales
Route::get('/forzar-sesion', function() { 
    session(['id_user' => 2]); 
    return 'Sesión forzada correctamente para el técnico ID 2'; 
});