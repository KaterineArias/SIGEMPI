<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}