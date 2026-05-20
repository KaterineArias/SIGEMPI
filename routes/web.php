<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
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
    // ✅ Solo UNA vez, dentro del middleware
    Route::resource('mantenimientos', MantenimientoController::class);

    // Rutas extra que el resource NO genera
    Route::get('/mis-asignaciones', function () {
        return 'Mis asignaciones — próximamente';
    })->name('mantenimientos.mis-asignaciones');

    // ── Otros módulos (stubs) ────────────────────────────
    Route::get('/equipos',   fn() => 'Módulo Equipos — próximamente')->name('equipos.index');
    Route::get('/usuarios',  fn() => 'Módulo Usuarios — próximamente')->name('usuarios.index');
    Route::get('/reportes',  fn() => 'Módulo Reportes — próximamente')->name('reportes.index');
});