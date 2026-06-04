<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Módulo de Reportería Estratégica Avanzada - Universidad Don Bosco
     * Sincronizado al 100% con el script nuevo y el frontend institucional
     */
    public function index()
    {
        // 🟦 REPORTAJE 1: Alertas de Inventario Crítico (Equipos Dañados o de Baja)
        // Adaptado de la base vieja a la nueva. Filtra los equipos con ID_Estado = 1 (Dañado)
        $equiposCriticos = DB::table('Equipos')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->select(
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Equipos.Marca',
                'Equipos.Modelo',
                DB::raw("'CRÍTICO / DAÑADO' as Estado_Actual"),
                // Aplicamos limpieza forzada de caracteres especiales por si viene corrupto de base de datos
                DB::raw("REPLACE(REPLACE(Ubicacion.NombreSede, 'Impresi?n', 'Impresión'), 'Impresin', 'Impresión') as Sede"),
                DB::raw("Municipio.NombreMunicipio as Area_Interna")
            )
            ->where('Equipos.ID_Estado', 1) // 1 = Estado Dañado en el script nuevo
            ->take(15) // Limitamos para optimizar la carga a menos de 3 segundos
            ->get();

        // 🟨 REPORTAJE 2: Distribución Geográfica de Mantenimientos
        // Agrupa por la estructura jerárquica de la base nueva: Mantenimientos -> Equipos -> Ubicacion -> Municipio -> Departamento
        $reporteGeografico = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Departamento', 'Municipio.ID_Departamento', '=', 'Departamento.ID_Departamento')
            ->select(
                'Departamento.NombreDepartamento as Departamento',
                'Municipio.NombreMunicipio as Municipio',
                DB::raw('COUNT(Mantenimientos.ID_Mantenimiento) as Total_Mantenimientos')
            )
            ->groupBy('Departamento.NombreDepartamento', 'Municipio.NombreMunicipio')
            ->orderBy('Total_Mantenimientos', 'desc')
            ->get();

        // 🟩 REPORTAJE 3: Productividad del Equipo Técnico
        // Cuenta las órdenes del técnico en estado Programado (1) vs Completado (2) mapeando la tabla Users nueva
        $productividadTecnicos = DB::table('Users')
            ->where('ID_Rol', 2) // Solo perfiles con rol de técnico en la institución
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

        // Retornamos las tres colecciones de datos analíticos calzando con las variables del index de tu compañero
        return view('reportes.index', compact('equiposCriticos', 'reporteGeografico', 'productividadTecnicos'));
    }
}