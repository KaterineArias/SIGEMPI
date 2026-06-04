<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimientos extends Model
{
    // Vinculación estricta a la tabla de la base de datos
    protected $table = 'Mantenimientos';

    // Llave primaria física real
    protected $primaryKey = 'ID_Mantenimiento';

    // 🛑 LA PIEZA CLAVE: Apaga por completo la inyección automática de 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'ID_Equipo',
        'ID_Tecnico',
        'Fecha_Programada',
        'Fecha_Cierre',
        'ID_EstadoMantenimiento',
        'Fecha_Ingreso',
        'Fecha_Reprogramacion',
        'Observaciones'
    ];
}