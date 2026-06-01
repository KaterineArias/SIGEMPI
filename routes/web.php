<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\IntervencionController; // ← Tu controlador importado
use Illuminate\Support\Facades\Route;

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

    // ── Mantenimientos ───────────────────────────────────
    Route::resource('mantenimientos', MantenimientoController::class);

    Route::get('/mis-asignaciones', function () {
        return 'Mis asignaciones — próximamente';
    })->name('mantenimientos.mis-asignaciones');

    // ── Intervenciones (Módulo de Oswaldo) ───────────────
    // Solo los Técnicos pueden registrar intervenciones en los mantenimientos
    Route::get('/intervenciones/crear/{id_mantenimiento}', [IntervencionController::class, 'create'])
        ->name('intervenciones.create')
        ->middleware('rol:Tecnico');

    Route::post('/intervenciones/guardar', [IntervencionController::class, 'store'])
        ->name('intervenciones.store')
        ->middleware('rol:Tecnico');

    // ── Otros módulos (stubs) ────────────────────────────
    Route::get('/equipos',   fn() => 'Módulo Equipos — próximamente')->name('equipos.index');
    Route::resource('usuarios', UserController::class)->middleware('rol:Coordinador');
    // ── Reportería Estratégica (Módulo de Oswaldo) ───────────────
Route::get('/reportes', [App\Http\Controllers\ReporteController::class, 'index'])
    ->name('reportes.index')
    ->middleware('rol:Coordinador'); // Solo el Coordinador tiene acceso a las métricas del parque
});