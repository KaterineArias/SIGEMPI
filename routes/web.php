<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\IntervencionController; 
use App\Http\Controllers\MantenimientoAccionesController; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


// Forzamos al framework a apagar la inyección automática
config(['blueprint.timestamps' => false]);

// ── Autenticación ────────────────────────────────────────
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',  [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Rutas protegidas ─────────────────────────────────────
Route::middleware(['autenticado'])->group(function () {

    // Redirección según rol
    Route::get('/dashboard', function () {
        return match (session('rol')) {
            'Coordinador' => redirect()->route('dashboard.coordinador'),
            'Tecnico'     => redirect()->route('dashboard.tecnico'),
            default       => redirect()->route('login'),
        };
    })->name('dashboard');

    Route::get('/dashboard/coordinador', [DashboardController::class, 'coordinador'])
        ->middleware('rol:Coordinador')
        ->name('dashboard.coordinador');

    Route::get('/dashboard/tecnico', [DashboardController::class, 'tecnico'])
        ->middleware('rol:Tecnico')
        ->name('dashboard.tecnico');

    // ── Intervenciones (Módulo de Oswaldo) ───────────────
    Route::get('/intervenciones/crear/{id_mantenimiento}', [IntervencionController::class, 'create'])
        ->name('intervenciones.create')
        ->middleware('rol:Tecnico');

    Route::post('/intervenciones/guardar', [IntervencionController::class, 'store'])
        ->name('intervenciones.store')
        ->middleware('rol:Tecnico');

    // ── Mantenimientos (Sincronización de Perfiles Independientes) ──
    // Ruta exclusiva para la consulta de historial de órdenes cerradas del Técnico
    Route::get('/mantenimientos/historial-tecnico', [MantenimientoController::class, 'historialTecnico'])
        ->name('mantenimientos.index-tecnico')
        ->middleware('rol:Tecnico');

    // Resource original nativo para las operaciones globales del Coordinador
    Route::resource('mantenimientos', MantenimientoController::class);

    Route::get('/mis-asignaciones', function () {
        return 'Mis asignaciones — próximamente';
    })->name('mantenimientos.mis-asignaciones');

    // 🛡️ Acciones de Control de Tiempos y SLA (Mesa de Soporte)
    Route::post('/mantenimientos/{id}/transferir', [MantenimientoAccionesController::class, 'transferirCaso'])
        ->name('mantenimientos.transferir');
        
    Route::post('/mantenimientos/{id}/reprogramar', [MantenimientoAccionesController::class, 'guardarReprogramacion'])
        ->name('mantenimientos.reprogramar');

    // ── Otros módulos (stubs) ────────────────────────────
    Route::get('/equipos',   fn() => 'Módulo Equipos — próximamente')->name('equipos.index');
    Route::resource('usuarios', UserController::class)->middleware('rol:Coordinador');
    
    // ── Reportería Estratégica (Módulo de Oswaldo) ───────────────
    Route::get('/reportes', [App\Http\Controllers\ReporteController::class, 'index'])
        ->name('reportes.index')
        ->middleware('rol:Coordinador');
});