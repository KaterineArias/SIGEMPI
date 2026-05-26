<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {


      Schema::create('intervencions', function (Blueprint $table) {
    $table->id();
    
    // 1. Creamos la columna del mismo tipo de dato que tienen los compañeros (INT)
    $table->integer('mantenimiento_id');
    
    // 2. Le decimos explícitamente a SQL Server que apunte a 'ID_Mantenimiento' en la tabla 'Mantenimientos'
    $table->foreign('mantenimiento_id')->references('ID_Mantenimiento')->on('Mantenimientos');
    
    $table->text('descripcion_tecnica'); 
    $table->text('repuestos_utilizados')->nullable(); 
    $table->enum('estado_final', ['Exitoso', 'Pendiente Repuesto', 'De Baja']);
    $table->dateTime('fecha_intervencion');
    $table->timestamps();
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervencions');
    }
};
