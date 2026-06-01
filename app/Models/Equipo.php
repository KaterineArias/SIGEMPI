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
        'ID_Tipo',            // Actualizado: Foránea al catálogo de tipos (Escritorio/Laptop)
        'ID_Estado',          // Actualizado: Foránea al catálogo de estados de hardware
        'ID_UbicacionMaster', // Actualizado: Foránea al maestro de ubicaciones físicas
        'Marca',
        'Modelo',
    ];

    // Relación: Un equipo tiene un estado físico (Activo, Dañado, Bodega, etc.)
    public function estado()
    {
        return $this->belongsTo(EstadoEquipo::class, 'ID_Estado', 'ID_Estado');
    }

    // Relación: Un equipo pertenece a una categoría/tipo de hardware
    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'ID_Tipo', 'ID_Tipo');
    }
}