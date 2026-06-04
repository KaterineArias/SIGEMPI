<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'Mantenimientos';
    protected $primaryKey = 'ID_Mantenimiento';
    public $timestamps = false; // Desactiva created_at y updated_at de Laravel

    protected $fillable = [
        'ID_Equipo',
        'ID_Tecnico',
        'Fecha_Programada',
        'Estado_Mantenimiento',
        'Observaciones'
    ];

    // Relación con el Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'ID_Equipo', 'ID_Equipo');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'ID_Tecnico', 'ID_User');
    }

    public function estado()
    {
        return $this->belongsTo(CatalogoEstadoMantenimiento::class, 'ID_EstadoMantenimiento', 'ID_EstadoMantenimiento');
    }

    public function detalles()
    {
        return $this->hasMany(MantenimientoDetalle::class, 'ID_Mantenimiento', 'ID_Mantenimiento');
    }
}
