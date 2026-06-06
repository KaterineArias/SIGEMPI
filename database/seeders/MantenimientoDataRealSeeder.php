<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MantenimientoDataRealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buscamos el ID real de tu usuario técnico en la base de datos
        $user = DB::table('Users')
            ->whereRaw('LOWER(TRIM(Usuario)) = ?', ['tec_oswaldo'])
            ->first();

        // Si no se encuentra, tomamos el primer usuario técnico de respaldo con Rol 2
        $idTecnico = $user ? $user->ID_User : DB::table('Users')->where('ID_Rol', 2)->value('ID_User');

        if ($idTecnico) {
            // 2. Vinculamos de forma masiva los equipos de la demo al técnico Oswaldo
            $actualizados = DB::table('Mantenimientos')
                ->whereIn('ID_Equipo', function($query) {
                    $query->select('ID_Equipo')
                        ->from('Equipos')
                        ->where('Codigo_Inventario', 'LIKE', 'MINSAL-2026-%');
                })
                ->update(['ID_Tecnico' => $idTecnico]);

            $this->command->info("¡Base de datos sincronizada con éxito! Se asociaron {$actualizados} órdenes al técnico ID: {$idTecnico}.");
        } else {
            $this->command->error("Alerta: No se localizó un usuario con Rol Técnico en la tabla Users.");
        }
    }
}
