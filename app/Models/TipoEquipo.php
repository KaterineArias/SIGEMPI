<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEquipo extends Model
{
    protected $table      = 'Tipos_Equipo';
    protected $primaryKey = 'ID_Tipo';
    public $timestamps    = false;
}