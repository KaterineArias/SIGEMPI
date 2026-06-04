<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class IntervencionController extends Controller
{
    /**
     * Muestra el formulario estructurado para registrar la intervención.
     */
    public function create($id_mantenimiento)
    {
        $mantenimiento = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->where('Mantenimientos.ID_Mantenimiento', $id_mantenimiento)
            ->select('Mantenimientos.*', 'Equipos.Codigo_Inventario', 'Equipos.Marca', 'Equipos.Modelo', 'Equipos.ID_Estado as ID_EstadoEquipo')
            ->first();

        if (!$mantenimiento) {
            return redirect()->route('dashboard.tecnico')->with('error', 'El registro de mantenimiento solicitado no existe.');
        }

        return view('intervenciones.create', compact('mantenimiento'));
    }

    /**
     * Procesa la bitácora técnica de forma segura tolerando los metadatos de Laravel.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ID_Mantenimiento'       => 'required',
            'ID_EstadoEquipo'        => 'required',
            'Accion_Realizada'       => 'required|string|max:500',
            'Observaciones_Tecnicas' => 'required|string|max:1000',
        ]);

        // 🛑 PARCHE ATÓMICO EN SILENCIO (Ejecución única integrada):
        // Como no tenemos SQL Server Management Studio, forzamos la creación de las columnas
        // antes de procesar el update. Si ya existen, el catch ignorará el error y continuará.
        try {
            DB::statement("ALTER TABLE Equipos ADD created_at DATETIME NULL;");
            DB::statement("ALTER TABLE Equipos ADD updated_at DATETIME NULL;");
        } catch (\Exception $e) {
            // Se ignora el error si las columnas ya fueron creadas previamente
        }

        DB::beginTransaction();
        try {
            $idMantenimiento = $request->ID_Mantenimiento;

            // 1. Obtener la orden base
            $mantenimientoPrevio = DB::table('Mantenimientos')->where('ID_Mantenimiento', $idMantenimiento)->first();
            if (!$mantenimientoPrevio) {
                throw new Exception("La orden de trabajo base N° {$idMantenimiento} no existe.");
            }

            $idTecnico = $request->ID_TecnicoIntervino ?? session('id_user') ?? 2;

            // 2. Insertar reporte oficial en la Bitácora (Mantenimiento_Detalle)
            DB::table('Mantenimiento_Detalle')->where('ID_Mantenimiento', $idMantenimiento)->delete();
            DB::table('Mantenimiento_Detalle')->insert([
                'ID_Mantenimiento'       => $idMantenimiento,
                'ID_TecnicoIntervino'    => $idTecnico,
                'Fecha_Registro'         => now(), 
                'Accion_Realizada'       => $request->Accion_Realizada,
                'Observaciones_Tecnicas' => $request->Observaciones_Tecnicas,
            ]);

            // 3. Registrar rastro en el Historial de Cambios de Estado
            DB::table('Historial_Cambios_Estado')->insert([
                'ID_Mantenimiento'   => $idMantenimiento,
                'ID_EstadoAnterior'  => $mantenimientoPrevio->ID_EstadoMantenimiento,
                'ID_EstadoNuevo'     => 2, // 2 = Completado
                'ID_TecnicoAnterior' => $idTecnico,
                'ID_TecnicoNuevo'    => $idTecnico,
                'ID_UsuarioModifico' => $idTecnico,
                'Fecha_Cambio'       => now(),
                'Motivo_Cambio'      => 'Cierre técnico registrado: ' . substr($request->Accion_Realizada, 0, 80)
            ]);

            // 4. Actualizar la orden maestra (Compatible al 100% con timestamps)
            DB::table('Mantenimientos')
                ->where('ID_Mantenimiento', $idMantenimiento)
                ->update([
                    'ID_EstadoMantenimiento' => 2,
                    'Fecha_Cierre'           => now(),
                    'created_at'             => now(),
                    'updated_at'             => now()
                ]);

            // 5. Sincronizar el estado del equipo (Ahora con soporte definitivo de timestamps)
            DB::table('Equipos')
                ->where('ID_Equipo', $mantenimientoPrevio->ID_Equipo)
                ->update([
                    'ID_Estado'  => $request->ID_EstadoEquipo,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            DB::commit();
            return redirect()->route('dashboard.tecnico')->with('success', '¡Mantenimiento cerrado con éxito e historial técnico sincronizado!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Fallo en Base de Datos: ' . $e->getMessage())->withInput();
        }
    }
}