<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.sidebar', function ($view) {
            $pendientes = 0;
            if (session('id_user') && session('rol') === 'Tecnico') {
                $pendientes = DB::table('Mantenimientos')
                    ->join('Catalogo_EstadoMantenimiento',
                        'Mantenimientos.ID_EstadoMantenimiento', '=',
                        'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                    ->where('Mantenimientos.ID_Tecnico', session('id_user'))
                    ->whereIn('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                        ['Programado', 'Reprogramado'])
                    ->count();
            }
            $view->with('pendientesSidebar', $pendientes);
        });
    }
}
