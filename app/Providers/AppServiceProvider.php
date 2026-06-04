<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

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
     * SOLUCIÓN FULMINANTE: Fuerza a todos los modelos de la app a ignorar los timestamps automáticos.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // 🛑 EL APAGADOR RADICAL EN CALIENTE:
        // Interceptamos el evento de arranque global de los modelos de Laravel
        // y les inyectamos en tiempo de ejecución que 'timestamps' es falso.
        // Esto desactiva el campo 'updated_at' en toda la aplicación de golpe.
        if (class_exists(Model::class)) {
            Model::creating(function ($model) {
                $model->timestamps = false;
            });
            Model::updating(function ($model) {
                $model->timestamps = false;
            });
            Model::saving(function ($model) {
                $model->timestamps = false;
            });
        }
    }
}