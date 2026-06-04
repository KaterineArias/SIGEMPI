<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\EquipoController;
use Illuminate\Support\Facades\Route;

// Autenticación
Route::get('/',       [AuthController::class, 'showLogin'])->name('login');
Route::get('/login',  [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// Primer login
Route::get('/primer-login',  [AuthController::class, 'showPrimerLogin'])->name('primer.login.form');
Route::post('/primer-login', [AuthController::class, 'setPrimerPassword'])->name('primer.login.post');

// Reset de contraseña
Route::get('/recuperar-password',      [AuthController::class, 'showSolicitarReset'])->name('password.solicitar');
Route::post('/recuperar-password',     [AuthController::class, 'solicitarReset'])->name('password.solicitar.post');
Route::get('/reset-password/{token}',  [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Rutas protegidas
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

    // Mantenimientos
    Route::resource('mantenimientos', MantenimientoController::class);

    Route::patch('mantenimientos/{mantenimiento}/estado',
        [MantenimientoController::class, 'cambiarEstado'])
        ->name('mantenimientos.estado');

    Route::get('/mis-asignaciones', [MantenimientoController::class, 'misAsignaciones'])
        ->name('mantenimientos.mis-asignaciones');

    // Equipos
    Route::resource('equipos', EquipoController::class);

    // Usuarios (solo Coordinador)
    Route::middleware('rol:Coordinador')->group(function () {

        Route::resource('usuarios', UserController::class)
            ->only(['index', 'create', 'store']);

        // Editar y actualizar
        Route::get('usuarios/{usuario}/edit',   [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}',        [UserController::class, 'update'])->name('usuarios.update');

        // Cambiar estado (Activo ↔ Inactivo)
        Route::patch('usuarios/{usuario}/estado',
            [UserController::class, 'cambiarEstado'])
            ->name('usuarios.estado');

        // Historiales de auditoría
        Route::get('usuarios/{usuario}/historial',
        [UserController::class, 'historial'])
        ->name('usuarios.historial');
    });

    // Reportes
    Route::get('/reportes', fn() => view('reportes.index'))->name('reportes.index');
});