<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function coordinador(Request $request)
    {
        $stats = [
            'total_equipos' => DB::table('Equipos')->count(),

            'equipos_activos' => DB::table('Equipos')
                ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
                ->where('Estado_Equipo.Estado', 'Activo')
                ->count(),

            'equipos_danados' => DB::table('Equipos')
                ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
                ->where('Estado_Equipo.Estado', 'Dañado')
                ->count(),

            'total_tecnicos' => DB::table('Users')
                ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
                ->where('Roles_User.Rol', 'Tecnico')
                ->count(),

            'mant_programados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
                ->count(),

            'mant_completados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Completado')
                ->count(),

            'mant_este_mes' => DB::table('Mantenimientos')
                ->whereMonth('Fecha_Programada', now()->month)
                ->whereYear('Fecha_Programada', now()->year)
                ->count(),
        ];

        $tecnicos = DB::table('Users')
            ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
            ->where('Roles_User.Rol', 'Tecnico')
            ->select('Users.ID_User', 'Users.Usuario')
            ->get();

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
            ->orderBy('Mantenimientos.Fecha_Programada')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',        
                'Ubicacion.NombreSede as Ubicacion',      
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento'
            );

        if ($request->filled('tecnico_id')) {
            $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
        }

        $proximos = $query->limit(20)->get();

        return view('dashboard.coordinador', compact('stats', 'proximos', 'tecnicos'));
    }

    public function tecnico()
    {
        $id_tecnico = session('id_user');

        $stats = [
            'mis_programados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Mantenimientos.ID_Tecnico', $id_tecnico)
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
                ->count(),

            'mis_completados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Mantenimientos.ID_Tecnico', $id_tecnico)
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Completado')
                ->count(),

            'mis_este_mes' => DB::table('Mantenimientos')
                ->where('ID_Tecnico', $id_tecnico)
                ->whereMonth('Fecha_Programada', now()->month)
                ->whereYear('Fecha_Programada', now()->year)
                ->count(),
        ];

        $asignaciones = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $id_tecnico)
            ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
            ->orderBy('Mantenimientos.Fecha_Programada')
            ->limit(5)
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento'
            )
            ->get();

        return view('dashboard.tecnico', compact('stats', 'asignaciones'));
    }
}