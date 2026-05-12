<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function coordinador()
    {
        $stats = [
            'total_equipos'     => DB::table('Equipos')->count(),
            'equipos_activos'   => DB::table('Equipos')->where('Estado', 'Activo')->count(),
            'equipos_danados'   => DB::table('Equipos')->where('Estado', 'Dañado')->count(),
            'total_tecnicos'    => DB::table('Users')->where('Rol', 'Tecnico')->count(),
            'mant_programados'  => DB::table('Mantenimientos')
                                    ->where('Estado_Mantenimiento', 'Programado')->count(),
            'mant_completados'  => DB::table('Mantenimientos')
                                    ->where('Estado_Mantenimiento', 'Completado')->count(),
            'mant_este_mes'     => DB::table('Mantenimientos')
                                    ->whereMonth('Fecha_Programada', now()->month)
                                    ->whereYear('Fecha_Programada', now()->year)
                                    ->count(),
        ];

        $proximos = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->where('Mantenimientos.Estado_Mantenimiento', 'Programado')
            ->orderBy('Fecha_Programada')
            ->limit(5)
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Tipo',
                'Equipos.Ubicacion',
                'Users.Usuario as Tecnico'
            )
            ->get();

        return view('dashboard.coordinador', compact('stats', 'proximos'));
    }

    public function tecnico()
    {
        $id_tecnico = session('id_user');

        $stats = [
            'mis_programados'  => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->where('Estado_Mantenimiento', 'Programado')->count(),
            'mis_completados'  => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->where('Estado_Mantenimiento', 'Completado')->count(),
            'mis_este_mes'     => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->whereMonth('Fecha_Programada', now()->month)
                                    ->whereYear('Fecha_Programada', now()->year)
                                    ->count(),
        ];

        $asignaciones = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->where('Mantenimientos.ID_Tecnico', $id_tecnico)
            ->where('Mantenimientos.Estado_Mantenimiento', 'Programado')
            ->orderBy('Fecha_Programada')
            ->limit(5)
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Tipo',
                'Equipos.Ubicacion'
            )
            ->get();

        return view('dashboard.tecnico', compact('stats', 'asignaciones'));
    }
}