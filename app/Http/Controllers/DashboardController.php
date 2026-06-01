<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard del Coordinador: Control Global de la Flota y Órdenes
     */
    public function coordinador(Request $request)
    {
        $stats = [
            'total_equipos'    => DB::table('Equipos')->count(),
            'equipos_activos'  => DB::table('Equipos')->where('ID_Estado', 2)->count(),   // 2 = Activo
            'equipos_danados'  => DB::table('Equipos')->where('ID_Estado', 1)->count(),   // 1 = Dañado
            'total_tecnicos'   => DB::table('Users')->where('ID_Rol', 2)->count(),         // 2 = Técnico
            'mant_programados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->count(), // 1 = Programado
            'mant_completados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 2)->count(), // 2 = Completado
            'mant_este_mes'    => DB::table('Mantenimientos')
                                    ->whereMonth('Fecha_Programada', now()->month)
                                    ->whereYear('Fecha_Programada', now()->year)
                                    ->count(),
        ];

        // Lista de técnicos reales (ID_Rol = 2) para el dropdown del filtro
        $tecnicos = DB::table('Users')->where('ID_Rol', 2)->get();

        // Capturamos los filtros por Query String
        $filtro = $request->query('filtro');
        $tecnicoId = $request->query('tecnico_id');

        // Determinar si el filtro cliqueado pertenece al inventario global de equipos o a mantenimientos
        $esFiltroEquipo = in_array($filtro, ['total_equipos', 'equipos_activos', 'equipos_danados']);

        if ($esFiltroEquipo) {
            // CONTEXTO DE INVENTARIO: Consulta directa a Equipos para no perder registros
            $query = DB::table('Equipos')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicaciones_Master', 'Equipos.ID_UbicacionMaster', '=', 'Ubicaciones_Master.ID_UbicacionMaster')
                ->join('Edificios_Sedes', 'Ubicaciones_Master.ID_Edificio', '=', 'Edificios_Sedes.ID_Edificio')
                ->join('Departamentos_Inst', 'Ubicaciones_Master.ID_DepartamentoInst', '=', 'Departamentos_Inst.ID_DepartamentoInst');

            if ($filtro === 'equipos_activos') {
                $query->where('Equipos.ID_Estado', 2);
            } elseif ($filtro === 'equipos_danados') {
                $query->where('Equipos.ID_Estado', 1);
            }

            $proximos = $query->select(
                'Equipos.*',
                'Equipos.ID_Estado as Estado_Equipo',
                'Tipos_Equipo.Nombre_Tipo',
                'Edificios_Sedes.Nombre_Edificio',
                'Departamentos_Inst.Nombre_DepartamentoInst'
            )->get();

        } else {
            // CONTEXTO DE MANTENIMIENTOS: Consulta tradicional con cruce de órdenes
            $query = DB::table('Mantenimientos')
                ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicaciones_Master', 'Equipos.ID_UbicacionMaster', '=', 'Ubicaciones_Master.ID_UbicacionMaster')
                ->join('Edificios_Sedes', 'Ubicaciones_Master.ID_Edificio', '=', 'Edificios_Sedes.ID_Edificio')
                ->join('Departamentos_Inst', 'Ubicaciones_Master.ID_DepartamentoInst', '=', 'Departamentos_Inst.ID_DepartamentoInst')
                ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
                ->leftJoin('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento');

            if ($filtro) {
                if ($filtro === 'este_mes') {
                    $query->whereMonth('Mantenimientos.Fecha_Programada', now()->month)
                          ->whereYear('Mantenimientos.Fecha_Programada', now()->year);
                } elseif ($filtro === 'pendientes') {
                    $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
                } elseif ($filtro === 'completados') {
                    $query->where('Mantenimientos.ID_EstadoMantenimiento', 2);
                }
            } else {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
            }

            if ($request->filled('tecnico_id')) {
                $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
            }

            $proximos = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')
                ->select(
                    'Mantenimientos.*',
                    'Equipos.Codigo_Inventario',
                    'Equipos.Marca',
                    'Equipos.Modelo',
                    'Tipos_Equipo.Nombre_Tipo',
                    'Edificios_Sedes.Nombre_Edificio',
                    'Departamentos_Inst.Nombre_DepartamentoInst',
                    'Users.Usuario as Tecnico',
                    'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento'
                )
                ->limit(20)
                ->get();
        }

        return view('dashboard.coordinador', compact('stats', 'proximos', 'tecnicos', 'esFiltroEquipo'));
    }

    /**
     * Dashboard del Técnico: Asignaciones de Campo del Usuario Autenticado
     */
    public function tecnico(Request $request)
    {
        $id_tecnico = session('id_user');

        $stats = [
            'mis_programados' => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->where('ID_EstadoMantenimiento', 1)->count(),
            'mis_completados' => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->where('ID_EstadoMantenimiento', 2)->count(),
            'mis_este_mes'    => DB::table('Mantenimientos')
                                    ->where('ID_Tecnico', $id_tecnico)
                                    ->whereMonth('Fecha_Programada', now()->month)
                                    ->whereYear('Fecha_Programada', now()->year)
                                    ->count(),
        ];

        // Capturamos el filtro de la tarjeta del técnico
        $filtro = $request->query('filtro');

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicaciones_Master', 'Equipos.ID_UbicacionMaster', '=', 'Ubicaciones_Master.ID_UbicacionMaster')
            ->join('Edificios_Sedes', 'Ubicaciones_Master.ID_Edificio', '=', 'Edificios_Sedes.ID_Edificio')
            ->join('Departamentos_Inst', 'Ubicaciones_Master.ID_DepartamentoInst', '=', 'Departamentos_Inst.ID_DepartamentoInst')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $id_tecnico);

        if ($filtro) {
            if ($filtro === 'completados') {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 2);
            } elseif ($filtro === 'este_mes') {
                $query->whereMonth('Mantenimientos.Fecha_Programada', now()->month)
                      ->whereYear('Mantenimientos.Fecha_Programada', now()->year);
            } elseif ($filtro === 'pendientes') {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
            }
        } else {
            $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
        }

        $asignaciones = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Tipos_Equipo.Nombre_Tipo',
                'Edificios_Sedes.Nombre_Edificio',
                'Departamentos_Inst.Nombre_DepartamentoInst',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento'
            )
            ->limit(30)
            ->get();

        return view('dashboard.tecnico', compact('stats', 'asignaciones', 'filtro'));
    }
}