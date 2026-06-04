<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
    protected $table = 'Estado_Usuario';
    protected $primaryKey = 'ID_EstadoUsuario';
    public $timestamps = false;

    protected $fillable = ['Estado'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'ID_EstadoUsuario', 'ID_EstadoUsuario');
    }
}