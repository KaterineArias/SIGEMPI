<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\User;

class MantenimientoController extends Controller
{
    // Mostrar la lista y aplicar el filtro por técnico
    public function index(Request $request)
    {
        $query = Mantenimiento::with(['equipo', 'tecnico']);

        // Si es Técnico, solo ve sus propios mantenimientos
        if (session('rol') === 'Tecnico') {
            $query->where('ID_Tecnico', session('id_user'));
        } elseif ($request->has('tecnico_id') && $request->tecnico_id != '') {
            $query->where('ID_Tecnico', $request->tecnico_id);
        }

        $mantenimientos = $query->get();
        $tecnicos = User::where('Rol', 'Tecnico')->get();

        return view('mantenimientos.index', compact('mantenimientos', 'tecnicos'));
    }

    // Mostrar el formulario de programación
    public function create()
    {
        $equipos = Equipo::where('Estado', '!=', 'De Baja')->get();
        $tecnicos = User::where('Rol', 'Tecnico')->get();

        return view('mantenimientos.create', compact('equipos', 'tecnicos'));
    }

    // Guardar el nuevo mantenimiento
    public function store(Request $request)
    {
        // Si es Técnico, forzar su propio ID
        if (session('rol') === 'Tecnico') {
            $request->merge(['ID_Tecnico' => session('id_user')]);
        }

        $request->validate([
            'ID_Equipo'        => 'required|exists:Equipos,ID_Equipo',
            'ID_Tecnico'       => 'required|exists:Users,ID_User',
            'Fecha_Programada' => 'required|date|after_or_equal:today',
            'Observaciones'    => 'nullable|string|max:1000',
        ]);

        Mantenimiento::create([
            'ID_Equipo'             => $request->ID_Equipo,
            'ID_Tecnico'            => $request->ID_Tecnico,
            'Fecha_Programada'      => $request->Fecha_Programada,
            'Estado_Mantenimiento'  => 'Programado',
            'Observaciones'         => $request->Observaciones,
        ]);

        $ruta = session('rol') === 'Tecnico'
            ? 'dashboard.tecnico'
            : 'mantenimientos.index';

        return redirect()->route($ruta)
                         ->with('success', 'Mantenimiento programado correctamente.');
    }

// Vista Mis Asignaciones para el técnico
public function misAsignaciones()
{
    $asignaciones = Mantenimiento::with(['equipo'])
        ->where('ID_Tecnico', session('id_user'))
        ->orderByRaw("CASE Estado_Mantenimiento
            WHEN 'Programado'   THEN 1
            WHEN 'Reprogramado' THEN 2
            WHEN 'Completado'   THEN 3
            WHEN 'Cancelado'    THEN 4
            ELSE 5 END")
        ->get();

    return view('mantenimientos.mis-asignaciones', compact('asignaciones'));
}

// Cambiar estado de un mantenimiento
public function cambiarEstado(Request $request, Mantenimiento $mantenimiento)
{
    $nuevoEstado = $request->estado;
    $rol = session('rol');

    // Validar que el estado sea permitido según el rol
    $estadosCoordinador = ['Reprogramado', 'Cancelado'];
    $estadosTecnico     = ['Completado'];

    if ($rol === 'Coordinador' && !in_array($nuevoEstado, $estadosCoordinador)) {
        return back()->withErrors(['estado' => 'Acción no permitida.']);
    }

    if ($rol === 'Tecnico') {
        // El técnico solo puede completar sus propias asignaciones
        if (!in_array($nuevoEstado, $estadosTecnico)) {
            return back()->withErrors(['estado' => 'Acción no permitida.']);
        }
        if ($mantenimiento->ID_Tecnico !== session('id_user')) {
            abort(403);
        }
    }

    // No se puede modificar un mantenimiento ya Completado o Cancelado
    if (in_array($mantenimiento->Estado_Mantenimiento, ['Completado', 'Cancelado'])) {
        return back()->withErrors(['estado' => 'Este mantenimiento ya no puede modificarse.']);
    }

    $data = ['Estado_Mantenimiento' => $nuevoEstado];

    // Si reprograman, guardar nueva fecha
    if ($nuevoEstado === 'Reprogramado') {
        $request->validate([
            'Fecha_Programada' => 'required|date|after_or_equal:today',
        ]);
        $data['Fecha_Programada'] = $request->Fecha_Programada;
    }

    // Guardar observaciones si vienen
    if ($request->filled('Observaciones')) {
        $data['Observaciones'] = $request->Observaciones;
    }

    $mantenimiento->update($data);

    $ruta = $rol === 'Tecnico' ? 'mantenimientos.mis-asignaciones' : 'mantenimientos.index';

    return redirect()->route($ruta)
                     ->with('success', 'Mantenimiento actualizado correctamente.');
}

    public function show(Mantenimiento $mantenimiento)
    {
        //
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        //
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        //
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        //
    }
}