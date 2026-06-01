<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Pantalla principal del módulo de Reportería Avanzada
     */
    public function index()
    {
        // REPORTAJE 1: Distribución Geográfica de Mantenimientos
        // Agrupa por Departamento y Municipio de El Salvador para ver dónde se concentra la carga de TI
        $reporteGeografico = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Ubicaciones_Master', 'Equipos.ID_UbicacionMaster', '=', 'Ubicaciones_Master.ID_UbicacionMaster')
            ->join('Edificios_Sedes', 'Ubicaciones_Master.ID_Edificio', '=', 'Edificios_Sedes.ID_Edificio')
            ->join('Municipios_Geo', 'Edificios_Sedes.ID_MunicipioGeo', '=', 'Municipios_Geo.ID_MunicipioGeo')
            ->join('Departamentos_Geo', 'Municipios_Geo.ID_DeptoGeo', '=', 'Departamentos_Geo.ID_DeptoGeo')
            ->select(
                'Departamentos_Geo.Nombre_DeptoGeo as Departamento',
                'Municipios_Geo.Nombre_MunicipioGeo as Municipio',
                DB::raw('COUNT(Mantenimientos.ID_Mantenimiento) as Total_Mantenimientos')
            )
            ->groupBy('Departamentos_Geo.Nombre_DeptoGeo', 'Municipios_Geo.Nombre_MunicipioGeo')
            ->orderBy('Total_Mantenimientos', 'desc')
            ->get();

        // REPORTAJE 2: Alertas de Inventario Crítico (Equipos Dañados o de Baja)
        // Muestra de inmediato qué hardware necesita sustitución urgente y dónde está físicamente
        $equiposCriticos = DB::table('Equipos')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
            ->join('Ubicaciones_Master', 'Equipos.ID_UbicacionMaster', '=', 'Ubicaciones_Master.ID_UbicacionMaster')
            ->join('Edificios_Sedes', 'Ubicaciones_Master.ID_Edificio', '=', 'Edificios_Sedes.ID_Edificio')
            ->join('Departamentos_Inst', 'Ubicaciones_Master.ID_DepartamentoInst', '=', 'Departamentos_Inst.ID_DepartamentoInst')
            ->whereIn('Equipos.ID_Estado', [1, 5]) // 1 = Dañado, 5 = De Baja
            ->select(
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Estado_Equipo.Nombre_Estado as Estado_Actual',
                'Edificios_Sedes.Nombre_Edificio as Sede',
                'Departamentos_Inst.Nombre_DepartamentoInst as Area_Interna'
            )
            ->orderBy('Estado_Equipo.ID_Estado', 'asc')
            ->get();

        // REPORTAJE 3: Productividad del Equipo Técnico
        // Cuenta cuántas órdenes tiene cada técnico en estado Programado (1) vs Completado (2)
        $productividadTecnicos = DB::table('Users')
            ->where('ID_Rol', 2) // Solo perfiles técnicos
            ->select(
                'Users.Usuario as Tecnico',
                DB::raw("SUM(CASE WHEN Mantenimientos.ID_EstadoMantenimiento = 1 THEN 1 ELSE 0 END) as Pendientes"),
                DB::raw("SUM(CASE WHEN Mantenimientos.ID_EstadoMantenimiento = 2 THEN 1 ELSE 0 END) as Completados"),
                DB::raw("COUNT(Mantenimientos.ID_Mantenimiento) as Total_Asignados")
            )
            ->leftJoin('Mantenimientos', 'Users.ID_User', '=', 'Mantenimientos.ID_Tecnico')
            ->groupBy('Users.ID_User', 'Users.Usuario')
            ->orderBy('Completados', 'desc')
            ->get();

        // Retornamos la vista unificada pasándole las 3 colecciones de datos analíticos
        return view('reportes.index', compact('reporteGeografico', 'equiposCriticos', 'productividadTecnicos'));
    }
}