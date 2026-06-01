<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'Mantenimientos';
    protected $primaryKey = 'ID_Mantenimiento';
    public $timestamps = false; 

    protected $fillable = [
        'ID_Equipo',
        'ID_Tecnico',
        'Fecha_Programada',
        'ID_EstadoMantenimiento', // Actualizado: Llave foránea del catálogo
        'Fecha_Ingreso'           // Añadido: Para registrar la auditoría de creación
    ];

    // Relación con el Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'ID_Equipo', 'ID_Equipo');
    }

    // Relación con el Técnico (Usuario)
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'ID_Tecnico', 'ID_User');
    }

    // Nueva Relación con el Catálogo de Estados de Mantenimiento
    public function estadoMantenimiento()
    {
        return $this->belongsTo(CatalogoEstadoMantenimiento::class, 'ID_EstadoMantenimiento', 'ID_EstadoMantenimiento');
    }
}