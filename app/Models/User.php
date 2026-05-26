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
        'Usuario',
        'Rol',
        'Password_Hash',
    ];

    protected $hidden = [
        'Password_Hash',
    ];

    public function getAuthPassword()
    {
        return $this->Password_Hash;
    }
}