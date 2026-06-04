<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoEquipo extends Model
{
    protected $table      = 'Estado_Equipo';
    protected $primaryKey = 'ID_Estado';
    public $timestamps    = false;
}