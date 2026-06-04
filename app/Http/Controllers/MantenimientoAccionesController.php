<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class MantenimientoAccionesController extends Controller
{
    /**
     * Puntos 2.a, 2.b, 2.c y Restricción de Bloqueo:
     * Transfiere un caso entre técnicos protegiendo las métricas de órdenes vencidas.
     */
    public function transferirCaso(Request $request, $idMantenimiento)
    {
        $request->validate([
            'ID_NuevoTecnico' => 'required|exists:Users,ID_User',
            'Motivo_Cambio'   => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // 1. Obtener el estado y la fecha límite actual del mantenimiento
            $mantenimiento = DB::table('Mantenimientos')->where('ID_Mantenimiento', $idMantenimiento)->first();

            if (!$mantenimiento) {
                return redirect()->back()->with('error', 'El requerimiento solicitado no existe.');
            }

            // RESTRICCIÓN DE ORO (Aportada por Oswaldo):
            // Si la fecha límite ya expiró y sigue sin resolverse, NO se permite trasladar a nadie.
            $fechaMeta = $mantenimiento->Fecha_Reprogramacion ?? $mantenimiento->Fecha_Programada;
            if (strtotime($fechaMeta) < time() && $mantenimiento->ID_EstadoMantenimiento != 2) {
                return redirect()->back()->with('error', 'El requerimiento está vencido. No puede ser trasladado para nadie, la única acción permitida por ley es proceder a cerrarlo.');
            }

            $idUsuarioModifico = session('id_user') ?? 1;

            // 2. Actualizar la orden guardando el rastro del técnico previo
            DB::table('Mantenimientos')
                ->where('ID_Mantenimiento', $idMantenimiento)
                ->update([
                    'ID_TecnicoAnterior' => $mantenimiento->ID_Tecnico,
                    'ID_Tecnico'         => $request->ID_NuevoTecnico,
                    'updated_at'         => now()
                ]);

            // 3. Registrar rastro idéntico al log de auditoría del Ministerio de Hacienda
            DB::table('Historial_Cambios_Estado')->insert([
                'ID_Mantenimiento'   => $idMantenimiento,
                'ID_EstadoAnterior'  => $mantenimiento->ID_EstadoMantenimiento,
                'ID_EstadoNuevo'     => $mantenimiento->ID_EstadoMantenimiento,
                'ID_TecnicoAnterior' => $mantenimiento->ID_Tecnico,
                'ID_TecnicoNuevo'    => $request->ID_NuevoTecnico,
                'ID_UsuarioModifico' => $idUsuarioModifico,
                'Fecha_Cambio'       => now(),
                'Motivo_Cambio'      => 'Reasignación de Ticket aceptada. Motivo: ' . $request->Motivo_Cambio
            ]);

            DB::commit();
            return redirect()->back()->with('success', '¡Requerimiento trasladado y auditado correctamente!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error transaccional en el traslado: ' . $e->getMessage());
        }
    }

    /**
     * Punto 2.d y Regla Jurídica:
     * Registra una reprogramación ("Programador Retenido") guardando una nueva meta de tiempo.
     */
    public function guardarReprogramacion(Request $request, $idMantenimiento)
    {
        $request->validate([
            'Fecha_Reprogramacion' => 'required|date|after:today',
            'Motivo_Cambio'        => 'required|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $mantenimiento = DB::table('Mantenimientos')->where('ID_Mantenimiento', $idMantenimiento)->first();

            if (!$mantenimiento) {
                return redirect()->back()->with('error', 'El requerimiento solicitado no existe.');
            }

            // REGLA DE LEY: Bloquear si la orden ya venció en su planificación de origen
            if (strtotime($mantenimiento->Fecha_Programada) < time() && $mantenimiento->ID_EstadoMantenimiento == 1) {
                return redirect()->back()->with('error', 'Por Ley: No se puede reprogramar una orden de trabajo que ya expiró. Debe proceder a rellenar la bitácora técnica y cerrarla.');
            }

            $idUsuarioModifico = session('id_user') ?? 1;

            // Actualizamos el campo nuevo sin alterar ni pisar la Fecha_Programada original
            DB::table('Mantenimientos')
                ->where('ID_Mantenimiento', $idMantenimiento)
                ->update([
                    'Fecha_Reprogramacion'   => $request->Fecha_Reprogramacion,
                    'ID_EstadoMantenimiento' => 3, // 3 = Reprogramado
                    'Observaciones'          => $request->Motivo_Cambio,
                    'updated_at'             => now()
                ]);

            // Auditoría del rastro histórico de la reprogramación
            DB::table('Historial_Cambios_Estado')->insert([
                'ID_Mantenimiento'   => $idMantenimiento,
                'ID_EstadoAnterior'  => $mantenimiento->ID_EstadoMantenimiento,
                'ID_EstadoNuevo'     => 3, // Reprogramado
                'ID_TecnicoAnterior' => $mantenimiento->ID_Tecnico,
                'ID_TecnicoNuevo'    => $mantenimiento->ID_Tecnico,
                'ID_UsuarioModifico' => $idUsuarioModifico,
                'Fecha_Cambio'       => now(),
                'Motivo_Cambio'      => 'Programador Retenido / Nueva meta de finalización programada: ' . $request->Fecha_Reprogramacion . '. Justificación: ' . $request->Motivo_Cambio
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'La reprogramación ha sido registrada. Las alertas visuales han sido recalculadas.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al inyectar la reprogramación: ' . $e->getMessage());
        }
    }
}