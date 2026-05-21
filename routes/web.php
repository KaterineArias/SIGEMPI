<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\EquipoController;
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

    Route::patch('mantenimientos/{mantenimiento}/estado',
        [MantenimientoController::class, 'cambiarEstado'])
        ->name('mantenimientos.estado');

    Route::get('/mis-asignaciones', [MantenimientoController::class, 'misAsignaciones'])
    ->name('mantenimientos.mis-asignaciones');

    // ── Otros módulos (stubs) ────────────────────────────
    Route::resource('equipos', EquipoController::class);
    Route::resource('usuarios', UserController::class)->middleware('rol:Coordinador');
    Route::get('/reportes',  fn() => 'Módulo Reportes — próximamente')->name('reportes.index');
});