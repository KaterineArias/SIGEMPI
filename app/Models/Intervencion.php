<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervencion extends Model
{
    use HasFactory;

    // Le especificamos el nombre exacto de la tabla en la BD
    protected $table = 'intervencions';

    // Lista de columnas que permitiremos llenar desde los formularios
    protected $fillable = [
        'mantenimiento_id',
        'descripcion_tecnica',
        'repuestos_utilizados',
        'estado_final',
        'fecha_intervencion'
    ];
}