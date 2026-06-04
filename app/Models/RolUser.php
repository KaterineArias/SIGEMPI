<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolUser extends Model
{
    protected $table      = 'Roles_User'; 
    protected $primaryKey = 'ID_Rol';
    public    $timestamps = false;

    protected $fillable = ['Rol'];
}