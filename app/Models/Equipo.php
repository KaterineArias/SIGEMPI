<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TipoEquipo;
use App\Models\EstadoEquipo;
use App\Models\Ubicacion;

class Equipo extends Model
{
    protected $table = 'Equipos';
    protected $primaryKey = 'ID_Equipo';
    public $timestamps = false;

    protected $fillable = [
        'Codigo_Inventario',
        'Tipo',
        'Ubicacion',
        'Marca',
        'Modelo',
        'Estado',
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'ID_Tipo', 'ID_Tipo');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoEquipo::class, 'ID_Estado', 'ID_Estado');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ID_Ubicacion', 'ID_Ubicacion');
    }
}