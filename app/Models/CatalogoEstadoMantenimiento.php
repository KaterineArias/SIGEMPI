<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoEstadoMantenimiento extends Model
{
    // Apuntamos a la tabla exacta de la base de datos
    protected $table = 'Catalogo_EstadoMantenimiento';
    
    // Definimos su llave primaria personalizada
    protected $primaryKey = 'ID_EstadoMantenimiento';
    
    // Desactivamos marcas de tiempo de Laravel
    public $timestamps = false;

    protected $fillable = [
        'Nombre_EstadoMantenimiento'
    ];

    // Relación inversa: Un estado puede estar en muchos mantenimientos
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'ID_EstadoMantenimiento', 'ID_EstadoMantenimiento');
    }
}
