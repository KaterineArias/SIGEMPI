<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MantenimientosFicticiosSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpieza segura de tablas (Sintaxis SQL Server)
        DB::statement('EXEC sp_MSforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT ALL"');
        DB::table('Mantenimientos')->delete();
        DB::table('Equipos')->delete();
        DB::statement('EXEC sp_MSforeachtable "ALTER TABLE ? CHECK CONSTRAINT ALL"');

        // 2. Buscamos tu ID numérico de técnico
        $tecnico = DB::table('Users')->where('Usuario', 'tec_oswaldo')->first();
        $id_tecnico = $tecnico ? $tecnico->ID_User : 1;

        // 3. Insertamos los Equipos usando el Tipo 'Laptop' que está validado por la base de datos
        $equipos = [
            ['Codigo_Inventario' => 'LAP00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Gerencia de Recursos Humanos', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'DES00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Departamento de Contabilidad', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'IMP00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Área de Compras y Logística', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'SER00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Cuarto de Datos (Central)', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'LAP00002', 'Tipo' => 'Laptop', 'Ubicacion' => 'Departamento de Ventas', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'DES00002', 'Tipo' => 'Laptop', 'Ubicacion' => 'Atención al Cliente - Recepción', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'IMP00002', 'Tipo' => 'Laptop', 'Ubicacion' => 'Oficina de Auditoría Interna', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'SWI00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Sistemas - Planta Alta', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'LAP00003', 'Tipo' => 'Laptop', 'Ubicacion' => 'Dirección Ejecutiva', 'Estado' => 'Activo'],
            ['Codigo_Inventario' => 'PRO00001', 'Tipo' => 'Laptop', 'Ubicacion' => 'Sala de Juntas Principal', 'Estado' => 'Activo'],
        ];

        foreach ($equipos as $equipo) {
            // Insertamos el equipo y obtenemos el ID autoincremental asignado por SQL Server
            $id_asignado = DB::table('Equipos')->insertGetId($equipo);

            // Asignamos la observación correspondiente según tu código estructurado
            $observacion = '';
            $fecha = '2026-05-26';

            switch ($equipo['Codigo_Inventario']) {
                case 'LAP00001':
                    $fecha = '2026-05-26';
                    $observacion = 'Mantenimiento preventivo semestral: Limpieza de ventiladores y optimización de sistema operativo lento.';
                    break;
                case 'DES00001':
                    $fecha = '2026-05-27';
                    $observacion = 'Mantenimiento correctivo: El equipo se apaga solo tras unos minutos de uso continuo. Revisar pasta térmica.';
                    break;
                case 'IMP00001':
                    $fecha = '2026-05-28';
                    $observacion = 'Mantenimiento correctivo: Atasco constante de papel en la bandeja de alimentación número 2.';
                    break;
                case 'SER00001':
                    $fecha = '2026-05-29';
                    $observacion = 'Mantenimiento preventivo: Reemplazo programado de disco duro mecánico por alertas en el arreglo RAID.';
                    break;
                case 'LAP00002':
                    $fecha = '2026-06-01';
                    $observacion = 'Mantenimiento correctivo: El teclado integrado no reconoce las teclas direccionales ni el bloque numérico.';
                    break;
                case 'DES00002':
                    $fecha = '2026-06-02';
                    $observacion = 'Mantenimiento preventivo: Soplado físico de fuente de poder, limpieza exterior y escaneo de malware.';
                    break;
                case 'IMP00002':
                    $fecha = '2026-06-03';
                    $observacion = 'Mantenimiento correctivo: Manchas negras intermitentes en el margen izquierdo de las hojas impresas.';
                    break;
                case 'SWI00001':
                    $fecha = '2026-06-04';
                    $observacion = 'Mantenimiento preventivo: Actualización de firmware del switch para mitigar fallas de desconexión.';
                    break;
                case 'LAP00003':
                    $fecha = '2026-06-05';
                    $observacion = 'Mantenimiento correctivo: La batería presenta degradación severa (no retiene carga). Requiere reemplazo.';
                    break;
                case 'PRO00001':
                    $fecha = '2026-06-08';
                    $observacion = 'Mantenimiento correctivo: Proyección opaca. Se solicita limpieza profunda de lentes ópticos.';
                    break;
            }

            DB::table('Mantenimientos')->insert([
                'ID_Equipo' => $id_asignado,
                'Fecha_Programada' => $fecha,
                'Estado_Mantenimiento' => 'Programado',
                'Observaciones' => $observacion,
                'ID_Tecnico' => $id_tecnico
            ]);
        }
    }
}