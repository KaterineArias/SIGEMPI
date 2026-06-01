<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntervencionController extends Controller
{
    /**
     * Muestra el formulario para que el técnico registre su intervención.
     */
    public function create($id_mantenimiento)
    {
        // 1. Buscamos el mantenimiento uniendo datos esenciales del Equipo para que el técnico sepa qué va a reparar
        $mantenimiento = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->where('Mantenimientos.ID_Mantenimiento', $id_mantenimiento)
            ->select('Mantenimientos.*', 'Equipos.Codigo_Inventario', 'Equipos.Marca', 'Equipos.Modelo')
            ->first();

        // 2. Validación de seguridad
        if (!$mantenimiento) {
            return redirect()->back()->with('error', 'El registro de mantenimiento solicitado no existe.');
        }

        return view('intervenciones.create', compact('mantenimiento'));
    }

    /**
     * Procesa la bitácora técnica, audita el cambio y cierra el mantenimiento.
     */
    public function store(Request $request)
    {
        // 1. Validaciones adaptadas a los campos del formulario real
        $request->validate([
            'ID_Mantenimiento'      => 'required|exists:Mantenimientos,ID_Mantenimiento',
            'ID_TecnicoIntervino'   => 'required|exists:Users,ID_User', // Técnico que firma la acción
            'Accion_Realizada'      => 'required|string|max:500',
            'Observaciones_Tecnicas' => 'required|string|max:1000',
        ]);

        // 2. Ejecución segura mediante Transacción (Todo o Nada)
        DB::transaction(function () use ($request) {
            
            // PASO A: Insertar el detalle de la intervención en la bitácora técnica
            DB::table('Mantenimiento_Detalle')->insert([
                'ID_Mantenimiento'     => $request->ID_Mantenimiento,
                'ID_TecnicoIntervino'  => $request->ID_TecnicoIntervino,
                'Fecha_Registro'       => now(),
                'Accion_Realizada'     => $request->Accion_Realizada,
                'Observaciones_Tecnicas'=> $request->Observaciones_Tecnicas,
            ]);

            // PASO B: Inyectar rastro en la tabla de auditoría (Historial de Cambios)
            DB::table('Historial_Cambios_Estado')->insert([
                'ID_Mantenimiento'   => $request->ID_Mantenimiento,
                'ID_EstadoAnterior'  => 1, // Estado de origen: Programado
                'ID_EstadoNuevo'     => 2, // Estado de destino: Completado
                'ID_TecnicoAnterior' => $request->ID_TecnicoIntervino,
                'ID_TecnicoNuevo'    => $request->ID_TecnicoIntervino,
                'ID_UsuarioModifico' => $request->ID_TecnicoIntervino,
                'Fecha_Cambio'       => now(),
                'Motivo_Cambio'      => 'El técnico ejecutó la intervención y cerró la orden de trabajo exitosamente.'
            ]);

            // PASO C: Actualizar el estado maestro en la tabla principal de Mantenimientos
            DB::table('Mantenimientos')
                ->where('ID_Mantenimiento', $request->ID_Mantenimiento)
                ->update([
                    'ID_EstadoMantenimiento' => 2 // 2 = 'Completado' en nuestro catálogo oficial
                ]);
        });

        // 3. Redirección limpia al dashboard con mensaje de éxito
        return redirect()->route('dashboard')->with('success', 'Bitácora técnica registrada e intervención guardada con éxito.');
    }
}