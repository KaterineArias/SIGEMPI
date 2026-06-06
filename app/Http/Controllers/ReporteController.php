<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento',
                   'Mantenimientos.ID_EstadoMantenimiento', '=',
                   'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->leftJoin('Mantenimiento_Detalle',
                   'Mantenimientos.ID_Mantenimiento', '=',
                   'Mantenimiento_Detalle.ID_Mantenimiento');

        // Filtros
        if ($request->filled('tecnico_id')) {
            $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
        }
        if ($request->filled('estado')) {
            $query->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', $request->estado);
        }
        if ($request->filled('equipo_id')) {
            $query->where('Mantenimientos.ID_Equipo', $request->equipo_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('Mantenimientos.Fecha_Programada', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('Mantenimientos.Fecha_Programada', '<=', $request->fecha_hasta);
        }

        $mantenimientos = $query
            ->orderBy('Mantenimientos.Fecha_Programada', 'desc')
            ->select(
                'Mantenimientos.ID_Mantenimiento',
                'Mantenimientos.Fecha_Programada',
                'Mantenimientos.Fecha_Cierre',
                'Mantenimientos.Fecha_Reprogramacion',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion',
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado',
                'Mantenimiento_Detalle.Accion_Realizada',
                'Mantenimiento_Detalle.Observaciones_Tecnicas'
            )
            ->get();

        // Contadores resumen
        $resumen = [
            'total'        => $mantenimientos->count(),
            'completados'  => $mantenimientos->where('Estado', 'Completado')->count(),
            'programados'  => $mantenimientos->where('Estado', 'Programado')->count(),
            'reprogramados'=> $mantenimientos->where('Estado', 'Reprogramado')->count(),
            'cancelados'   => $mantenimientos->where('Estado', 'Cancelado')->count(),
        ];

        $tecnicos = DB::table('Users')
            ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
            ->where('Roles_User.Rol', 'Tecnico')
            ->select('Users.ID_User', 'Users.Usuario')
            ->get();

        $equipos = DB::table('Equipos')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->select('Equipos.ID_Equipo', 'Equipos.Codigo_Inventario',
                     'Tipos_Equipo.Nombre_Tipo as Tipo', 'Equipos.Marca')
            ->orderBy('Equipos.Codigo_Inventario')
            ->get();

        $hayFiltros = $request->hasAny(['tecnico_id','estado','equipo_id','fecha_desde','fecha_hasta']);

        return view('reportes.index', compact(
            'mantenimientos', 'resumen', 'tecnicos', 'equipos', 'hayFiltros'
        ));
    }
}