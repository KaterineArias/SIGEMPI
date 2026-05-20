<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MantenimientoController;

// Autenticacion
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['autenticado'])->group(function () {

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

    // Stubs temporales
    Route::get('/equipos', fn() => 'Módulo Equipos — próximamente')->name('equipos.index');
    Route::get('/usuarios', fn() => 'Módulo Usuarios — próximamente')->name('usuarios.index');
    Route::get('/reportes', fn() => 'Módulo Reportes — próximamente')->name('reportes.index');
    Route::resource('mantenimientos', MantenimientoController::class);
    Route::get('/mis-asignaciones', fn() => 'Mis asignaciones')->name('mantenimientos.mis-asignaciones');
    Route::get('/registrar-intervencion', fn() => 'Registrar intervención')->name('mantenimientos.registrar');
});