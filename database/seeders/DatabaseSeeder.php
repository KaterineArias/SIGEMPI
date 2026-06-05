<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UserSeeder::class,
            EstadoSeeder::class,
            TipoSeeder::class,
            SedeSeeder::class,
            MunicipioSeeder::class,
            EquipoSeeder::class,
            EstadoMantenimientoSeeder::class,
            // 👑 INTEGRACIÓN DE NIVEL INTERNACIONAL PARA REPORTERÍA
            // Este seeder inyecta los 100 mantenimientos distribuidos de marzo a julio
            MantenimientoAnaliticoSeeder::class,
        ]);
    }
}