<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table      = 'Users';
    protected $primaryKey = 'ID_User';
    public    $timestamps = false;

    protected $fillable = [
        'ID_Rol',            // Actualizado: Foránea del Rol
        'ID_EstadoUsuario',  // Añadido: Foránea del Estado del usuario
        'Usuario',
        'Correo_User',       // Añadido: Correo real de la BD
        'Password_Hash',
    ];

    protected $hidden = [
        'Password_Hash',
    ];

    public function getAuthPassword()
    {
        return $this->Password_Hash;
    }

    // Nueva Relación: Un Usuario pertenece a un Rol
    public function rol()
    {
        return $this->belongsTo(RolesUser::class, 'ID_Rol', 'ID_Rol');
    }
}