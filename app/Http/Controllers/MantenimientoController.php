<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    // LISTA DE MANTENIMIENTOS
    public function index(Request $request)
    {
        $tecnico = $request->tecnico;

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Tipo',
                'Users.Usuario as Tecnico'
            );

        // FILTRO POR TECNICO
        if ($tecnico) {
            $query->where('Mantenimientos.ID_Tecnico', $tecnico);
        }

        $mantenimientos = $query
            ->orderBy('Fecha_Programada', 'asc')
            ->get();

        $tecnicos = DB::table('Users')
            ->where('Rol', 'Tecnico')
            ->get();

        return view('mantenimientos.index', compact(
            'mantenimientos',
            'tecnicos',
            'tecnico'
        ));
    }

    // FORMULARIO
    public function create()
    {
        $equipos = DB::table('Equipos')
            ->where('Estado', 'Activo')
            ->get();

        $tecnicos = DB::table('Users')
            ->where('Rol', 'Tecnico')
            ->get();

        return view('mantenimientos.create', compact(
            'equipos',
            'tecnicos'
        ));
    }

    // GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'ID_Equipo' => 'required',
            'ID_Tecnico' => 'required',
            'Fecha_Programada' => 'required|date',
            'Observaciones' => 'nullable|string|max:1000',
        ]);

        DB::table('Mantenimientos')->insert([
            'ID_Equipo' => $request->ID_Equipo,
            'ID_Tecnico' => $request->ID_Tecnico,
            'Fecha_Programada' => $request->Fecha_Programada,
            'Estado_Mantenimiento' => 'Programado',
            'Observaciones' => $request->Observaciones,
        ]);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento programado correctamente.');
    }

    // FORMULARIO EDITAR
    public function edit($id)
    {
        $mantenimiento = DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $id)
            ->first();

        $equipos = DB::table('Equipos')
            ->where('Estado', 'Activo')
            ->get();

        $tecnicos = DB::table('Users')
            ->where('Rol', 'Tecnico')
            ->get();

        return view('mantenimientos.edit', compact(
            'mantenimiento',
            'equipos',
            'tecnicos'
        ));
    }

    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $request->validate([
            'ID_Equipo' => 'required',
            'ID_Tecnico' => 'required',
            'Fecha_Programada' => 'required|date',
            'Estado_Mantenimiento' => 'required',
        ]);

        DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $id)
            ->update([
                'ID_Equipo' => $request->ID_Equipo,
                'ID_Tecnico' => $request->ID_Tecnico,
                'Fecha_Programada' => $request->Fecha_Programada,
                'Estado_Mantenimiento' => $request->Estado_Mantenimiento,
                'Observaciones' => $request->Observaciones,
            ]);

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento actualizado correctamente');
    }

    // ELIMINAR
    public function destroy($id)
    {
        DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $id)
            ->delete();

        return redirect()
            ->route('mantenimientos.index')
            ->with('success', 'Mantenimiento eliminado correctamente');
    }
}
