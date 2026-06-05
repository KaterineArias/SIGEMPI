<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MantenimientoAnaliticoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Desactivando restricciones de integridad referencial...');
        // 👑 BLINDAJE ENTERPRISE: Apagamos las llaves foráneas en todo el ecosistema afectado
        DB::statement('ALTER TABLE Historial_Cambios_Estado NOCHECK CONSTRAINT ALL;');
        DB::statement('ALTER TABLE Mantenimiento_Detalle NOCHECK CONSTRAINT ALL;');
        DB::statement('ALTER TABLE Mantenimientos NOCHECK CONSTRAINT ALL;');

        $this->command->info('Purgando el histórico de datos operativos para consistencia limpia...');
        // Purgamos primero las tablas secundarias dependientes para evitar colisiones referenciales
        DB::table('Historial_Cambios_Estado')->delete();
        DB::table('Mantenimiento_Detalle')->delete();
        
        // Ahora que las dependencias están limpias, vaciamos de forma segura la tabla maestra
        DB::table('Mantenimientos')->delete();

        $idTecnico = 2; // Asignado prioritariamente a tec_oswaldo
        $hoy = Carbon::parse('2026-06-04');

        $this->command->info('Insertando el lote analítico de 100 órdenes de trabajo estructuradas...');

        for ($i = 1; $i <= 100; $i++) {
            $idEquipo = ($i % 15) + 1; // Rotación lógica de equipos (IDs 1 al 15)
            $tipoMant = ($i % 3 == 0) ? 'Preventivo' : 'Correctivo';

            $fechaProgramada = null;
            $fechaCierre = null;
            $idEstadoMantenimiento = 1; // 1: Programado, 2: Completado

            // =======================================================================
            // FASE A: MARZO, ABRIL Y MAYO 2026 (Órdenes Históricas - Completadas)
            // =======================================================================
            if ($i <= 60) {
                $diasRestar = 5 + ($i * 1.5);
                $fechaProgramada = (clone $hoy)->subDays($diasRestar)->setTime(8, 0, 0);

                if ($i % 5 == 0) {
                    $fechaCierre = (clone $fechaProgramada)->addDays(4)->addHours(3);
                } else {
                    $fechaCierre = (clone $fechaProgramada)->subHours(6);
                }
                $idEstadoMantenimiento = 2;
            }
            // =======================================================================
            // FASE B: JUNIO 2026 (Mes Actual - Mezcla Operativa)
            // =======================================================================
            else if ($i > 60 && $i <= 85) {
                if ($i <= 65) {
                    $fechaProgramada = (clone $hoy)->subDays(3 + ($i % 4))->setTime(8, 0, 0);
                    $fechaCierre = null;
                    $idEstadoMantenimiento = 1;
                } else if ($i > 65 && $i <= 72) {
                    $fechaProgramada = (clone $hoy)->setTime(10, 0, 0);
                    $fechaCierre = null;
                    $idEstadoMantenimiento = 1;
                } else if ($i > 72 && $i <= 80) {
                    $fechaProgramada = (clone $hoy)->subDay()->setTime(9, 0, 0);
                    $fechaCierre = (clone $hoy)->setTime(14, 30, 0);
                    $idEstadoMantenimiento = 2;
                } else {
                    $fechaProgramada = (clone $hoy)->addDays(8 + ($i % 10))->setTime(8, 0, 0);
                    $fechaCierre = null;
                    $idEstadoMantenimiento = 1;
                }
            }
            // =======================================================================
            // FASE C: JULIO 2026 (Proyecciones Futuras)
            // =======================================================================
            else {
                $diasSumar = 27 + ($i % 15);
                $fechaProgramada = (clone $hoy)->addDays($diasSumar)->setTime(8, 0, 0);
                $fechaCierre = null;
                $idEstadoMantenimiento = 1;
            }

            if ($tipoMant === 'Preventivo') {
                $accion = 'Mantenimiento Preventivo Institucional Semestral';
                $observacion = 'Limpieza interna de componentes, soplado de fuentes de poder, verificación de voltajes y actualización de firmas de seguridad.';
            } else {
                $accion = 'Optimización Correctiva por reporte de lentitud / falla';
                $observacion = 'Diagnóstico técnico detallado. Reemplazo preventivo de pasta térmica conductiva, depuración de almacenamiento local y pruebas de esfuerzo.';
            }

            // Inserción de la orden de trabajo maestra
            $idMantenimientoInsertado = DB::table('Mantenimientos')->insertGetId([
                'ID_Equipo'               => $idEquipo,
                'ID_Tecnico'              => $idTecnico,
                'Fecha_Programada'        => $fechaProgramada,
                'Fecha_Cierre'            => $fechaCierre,
                'ID_EstadoMantenimiento'  => $idEstadoMantenimiento,
                'created_at'              => Carbon::now(),
                'updated_at'              => Carbon::now(),
            ]);

            // Si el estatus quedó configurado como Completado, alimentamos la tabla de detalles
            if ($idEstadoMantenimiento == 2 && $idMantenimientoInsertado) {
                DB::table('Mantenimiento_Detalle')->insert([
                    'ID_Mantenimiento'       => $idMantenimientoInsertado,
                    'ID_TecnicoIntervino'    => $idTecnico,
                    'Fecha_Registro'         => $fechaCierre,
                    'Accion_Realizada'       => $accion,
                    'Observaciones_Tecnicas' => $observacion,
                ]);
            }
        }

        $this->command->info('Restaurando restricciones de integridad en SQL Server...');
        // Volvemos a activar el chequeo de llaves foráneas para mantener la base de datos sana
        DB::statement('ALTER TABLE Historial_Cambios_Estado CHECK CONSTRAINT ALL;');
        DB::statement('ALTER TABLE Mantenimiento_Detalle CHECK CONSTRAINT ALL;');
        DB::statement('ALTER TABLE Mantenimientos CHECK CONSTRAINT ALL;');

        $this->command->info('¡Proceso culminado con éxito absoluto!');
    }
}