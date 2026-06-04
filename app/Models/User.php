<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class User extends Model
{
    protected $table      = 'Users';
    protected $primaryKey = 'ID_User';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Rol',
        'ID_EstadoUsuario',
        'Usuario',
        'Correo_User',
        'Password_Hash',
    ];

    protected $hidden = ['Password_Hash'];

    // Relaciones

    public function rol()
    {
        return $this->belongsTo(RolUser::class, 'ID_Rol', 'ID_Rol');
    }

    public function estadoUsuario()
    {
        return $this->belongsTo(EstadoUsuario::class, 'ID_EstadoUsuario', 'ID_EstadoUsuario');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'ID_Tecnico', 'ID_User');
    }

    public function tokens()
    {
        return $this->hasMany(PasswordResetToken::class, 'ID_User', 'ID_User');
    }

    // Helpers

    public function estaActivo(): bool
    {
        return $this->estadoUsuario?->Estado === 'Activo';
    }

    public function esCoordinador(): bool
    {
        return $this->rol?->Rol === 'Coordinador';
    }
}