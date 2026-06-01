<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    /**
     * Muestra la lista general de mantenimientos con filtros.
     */
    public function index(Request $request)
    {
        // 1. Construimos la consulta base uniendo los catálogos relacionales reales
        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->select(
                'Mantenimientos.ID_Mantenimiento',
                'Mantenimientos.Fecha_Programada',
                'Mantenimientos.Fecha_Ingreso',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Users.Usuario as Nombre_Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento'
            );

        // 2. Aplicamos el filtro por Técnico si el Coordinador seleccionó uno en la interfaz
        if ($request->has('tecnico_id') && $request->tecnico_id != '') {
            $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
        }

        $mantenimientos = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')->get();

        // 3. Obtenemos el catálogo de técnicos reales (ID_Rol = 2 en la base de datos) para llenar el filtro de la vista
        $tecnicos = DB::table('Users')->where('ID_Rol', 2)->get();

        return view('mantenimientos.index', compact('mantenimientos', 'tecnicos'));
    }

    /**
     * Muestra el formulario para programar un nuevo mantenimiento.
     */
    public function create()
    {
        // 1. Jalamos solo equipos que NO estén dados de baja (ID_Estado != 5 en nuestro catálogo de hardware)
        $equipos = DB::table('Equipos')
            ->where('ID_Estado', '!=', 5)
            ->get();

        // 2. Jalamos los usuarios que pertenezcan al Rol de Técnico (ID_Rol = 2)
        $tecnicos = DB::table('Users')
            ->where('ID_Rol', 2)
            ->get();

        return view('mantenimientos.create', compact('equipos', 'tecnicos'));
    }

    /**
     * Almacena y agenda la orden en la base de datos de manera limpia.
     */
    public function store(Request $request)
    {
        // 1. Validaciones estrictas apuntando a las tablas y columnas definitivas
        $request->validate([
            'ID_Equipo' => 'required|exists:Equipos,ID_Equipo',
            'ID_Tecnico' => 'required|exists:Users,ID_User',
            'Fecha_Programada' => 'required|date|after_or_equal:today',
        ]);

        // 2. Inserción directa en la base de datos
        DB::table('Mantenimientos')->insert([
            'ID_Equipo' => $request->ID_Equipo,
            'ID_Tecnico' => $request->ID_Tecnico,
            'Fecha_Programada' => $request->Fecha_Programada,
            'ID_EstadoMantenimiento' => 1, // 1 = 'Programado' por defecto en nuestro catálogo real
            'Fecha_Ingreso' => now()       // Captura el momento exacto de la creación
        ]);

        return redirect()->route('mantenimientos.index')
                         ->with('success', 'Mantenimiento programado correctamente en el sistema.');
    }
}