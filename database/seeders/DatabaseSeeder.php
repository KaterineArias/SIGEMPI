<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class IntervencionController extends Controller
{
    /**
     * Muestra el formulario estructurado para registrar la intervención.
     * Vincula el mantenimiento con los campos físicos reales del Hardware.
     */
    public function create($id_mantenimiento)
    {
        // Buscamos el mantenimiento uniendo la información del equipo asociado
        $mantenimiento = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->where('Mantenimientos.ID_Mantenimiento', $id_mantenimiento)
            ->select('Mantenimientos.*', 'Equipos.Codigo_Inventario', 'Equipos.Marca', 'Equipos.Modelo', 'Equipos.ID_Estado as ID_EstadoEquipo')
            ->first();

        // Validación de existencia
        if (!$mantenimiento) {
            return redirect()->route('dashboard.tecnico')->with('error', 'El registro de mantenimiento solicitado no existe.');
        }

        return view('intervenciones.create', compact('mantenimiento'));
    }

    /**
     * Procesa la bitácora técnica utilizando SQL Puro para evitar la inyección automática de 'updated_at'.
     */
    public function store(Request $request)
    {
        // Validación de parámetros obligatorios enviados por el formulario
        $request->validate([
            'ID_Mantenimiento'       => 'required',
            'ID_EstadoEquipo'        => 'required',
            'Accion_Realizada'       => 'required|string|max:500',
            'Observaciones_Tecnicas' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Obtener el estado previo del mantenimiento directo de la base de datos
            $mantenimientoPrevio = DB::table('Mantenimientos')
                ->where('ID_Mantenimiento', $request->ID_Mantenimiento)
                ->first();

            if (!$mantenimientoPrevio) {
                throw new Exception("La orden de trabajo base N° {$request->ID_Mantenimiento} no existe.");
            }

            // Resolución del ID de usuario responsable del cierre técnico
            $idTecnico = $request->ID_TecnicoIntervino ?? session('id_user') ?? session('id_usuario') ?? $mantenimientoPrevio->ID_TecnicoResponsable ?? 1;

            // PASO A: Inyectar reporte oficial dentro de Mantenimiento_Detalle (Bitácora)
            DB::table('Mantenimiento_Detalle')->where('ID_Mantenimiento', $request->ID_Mantenimiento)->delete();

            DB::table('Mantenimiento_Detalle')->insert([
                'ID_Mantenimiento'       => $request->ID_Mantenimiento,
                'ID_TecnicoIntervino'    => $idTecnico,
                'Fecha_Registro'         => now(), 
                'Accion_Realizada'       => $request->Accion_Realizada,
                'Observaciones_Tecnicas' => $request->Observaciones_Tecnicas,
            ]);

            // PASO B: Buscar ID del estado 'Completado' de forma dinámica
            $estadoCompletadoId = DB::table('Catalogo_EstadoMantenimiento')
                ->where('Nombre_EstadoMantenimiento', 'LIKE', '%Completado%')
                ->value('ID_EstadoMantenimiento') ?? 2;

            // PASO C: Registrar rastro histórico en la tabla de auditoría (Historial_Cambios_Estado)
            DB::table('Historial_Cambios_Estado')->insert([
                'ID_Mantenimiento'   => $request->ID_Mantenimiento,
                'ID_EstadoAnterior'  => $mantenimientoPrevio->ID_EstadoMantenimiento,
                'ID_EstadoNuevo'     => $estadoCompletadoId,
                'ID_TecnicoAnterior' => $mantenimientoPrevio->ID_TecnicoResponsable ?? $idTecnico,
                'ID_TecnicoNuevo'    => $idTecnico,
                'ID_UsuarioModifico' => $idTecnico,
                'Fecha_Cambio'       => now(),
                'Motivo_Cambio'      => 'Cierre técnico registrado con éxito: ' . substr($request->Accion_Realizada, 0, 80)
            ]);

            // SOLUCIÓN RADICAL CON SQL PURO PARA EL PASO D: Actualizar orden maestra de Mantenimientos
            // Al usar DB::statement, Laravel NO puede inyectar la columna 'updated_at' bajo ninguna circunstancia
            $fechaCierreActual = now()->format('Y-m-d H:i:s');
            DB::statement("
                UPDATE Mantenimientos 
                SET ID_EstadoMantenimiento = ?, Fecha_Cierre = ? 
                WHERE ID_Mantenimiento = ?
            ", [$estadoCompletadoId, $fechaCierreActual, $request->ID_Mantenimiento]);

            // SOLUCIÓN RADICAL CON SQL PURO PARA EL PASO E: Sincronizar el estado en la tabla Equipos
            DB::statement("
                UPDATE Equipos 
                SET ID_Estado = ? 
                WHERE ID_Equipo = ?
            ", [$request->ID_EstadoEquipo, $mantenimientoPrevio->ID_Equipo]);

            // Si las consultas en SQL Puro fueron exitosas, confirmamos los cambios de forma atómica
            DB::commit();

            return redirect()->route('dashboard.tecnico')->with('success', '¡Mantenimiento cerrado con éxito! Las bitácoras e inventarios han sido actualizados de forma correcta.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Fallo en Base de Datos: ' . $e->getMessage())
                ->withInput();
        }
    }
}