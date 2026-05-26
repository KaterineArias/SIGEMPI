<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Intervencion;
use Illuminate\Support\Facades\DB;

class IntervencionController extends Controller
{
    // 1. Función para mostrar el formulario de registro
    public function create($id_mantenimiento)
    {
        // Buscamos el mantenimiento directamente en la tabla de los compañeros
        $mantenimiento = DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $id_mantenimiento)
            ->first();

        // Si no existe el mantenimiento, mandamos un error
        if (!$mantenimiento) {
            return redirect()->back()->with('error', 'El mantenimiento no existe.');
        }

        // Retornamos tu vista pasándole los datos del mantenimiento
        return view('intervenciones.create', compact('mantenimiento'));
    }

    // 2. Función para procesar y guardar el formulario en la BD
    public function store(Request $request)
    {
        // Validamos que los campos obligatorios vengan llenos y correctos
        $request->validate([
            'mantenimiento_id' => 'required',
            'descripcion_tecnica' => 'required|string',
            'estado_final' => 'required|in:Exitoso,Pendiente Repuesto,De Baja',
            'fecha_intervencion' => 'required|date',
        ]);
            $fecha_limpia = date('Y-m-d H:i:s', strtotime($request->fecha_intervencion));
        // Guardamos los datos usando tu Modelo Intervencion
        Intervencion::create([
            'mantenimiento_id' => $request->mantenimiento_id,
            'descripcion_tecnica' => $request->descripcion_tecnica,
            'repuestos_utilizados' => $request->repuestos_utilizados, // Puede ser nulo
            'estado_final' => $request->estado_final,
            'fecha_intervencion' => $fecha_limpia,
        ]);

        // ACTUALIZACIÓN DEL MANTENIMIENTO (Lo que te pide tu distribución de trabajo)
        // Cambiamos el estado del mantenimiento original a 'Completado' y le ponemos la fecha de atención
        DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $request->mantenimiento_id)
            ->update([
                'Estado_Mantenimiento' => 'Completado',
                'Fecha_Atencion' => now()
            ]);

        // Redireccionamos al dashboard con un mensaje de éxito
        return redirect()->route('dashboard')->with('success', 'Intervención registrada y mantenimiento completado con éxito.');
    }
}