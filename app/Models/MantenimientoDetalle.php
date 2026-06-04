<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MantenimientoDetalle extends Model
{
    protected $table      = 'Mantenimiento_Detalle';
    protected $primaryKey = 'ID_Detalle';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Mantenimiento',
        'ID_TecnicoIntervino',
        'Fecha_Registro',
        'Accion_Realizada',
        'Observaciones_Tecnicas',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'ID_Mantenimiento', 'ID_Mantenimiento');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'ID_TecnicoIntervino', 'ID_User');
    }
}