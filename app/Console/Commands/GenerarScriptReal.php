<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GenerarScriptReal extends Command
{
    protected $signature = 'db:generar-script';
    protected $description = 'Genera el script SQL de creación real para usuarios de Windows';

    public function handle()
    {
        $this->info('Mapeando la estructura real desde el contenedor de Docker...');
        
        // 1. Obtener todas las tablas reales de tu base de datos
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE' AND TABLE_NAME NOT IN ('sysdiagrams')");
        
        $sqlContenido = "-- ==========================================================\n";
        $sqlContenido .= "-- SCRIPT DE BASE DE DATOS REAL PARA SQL SERVER (WINDOWS)\n";
        $sqlContenido .= "-- Proyecto: SIGEMPI\n";
        $sqlContenido .= "-- Generado automáticamente puro código desde el entorno local\n";
        $sqlContenido .= "-- ==========================================================\n\n";
        $sqlContenido .= "CREATE DATABASE SIGEMPI_DB;\nGO\nUSE SIGEMPI_DB;\nGO\n\n";

        foreach ($tables as $table) {
            $tableName = $table->TABLE_NAME;
            $sqlContenido .= "-- ----------------------------------------------------------\n";
            $sqlContenido .= "-- Estructura para la tabla: {$tableName}\n";
            $sqlContenido .= "-- ----------------------------------------------------------\n";
            $sqlContenido .= "CREATE TABLE [dbo].[{$tableName}] (\n";

            // 2. Obtener las columnas reales de cada tabla
            $columns = DB::select("
                SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = ?
            ", [$tableName]);

            $colLines = [];
            foreach ($columns as $col) {
                $line = "    [{$col->COLUMN_NAME}] " . strtoupper($col->DATA_TYPE);
                
                if ($col->CHARACTER_MAXIMUM_LENGTH == -1) {
                    $line .= "(MAX)";
                } elseif ($col->CHARACTER_MAXIMUM_LENGTH) {
                    $line .= "({$col->CHARACTER_MAXIMUM_LENGTH})";
                }

                if ($col->IS_NULLABLE === 'NO') {
                    $line .= " NOT NULL";
                } else {
                    $line .= " NULL";
                }

                if ($col->COLUMN_DEFAULT) {
                    $line .= " DEFAULT " . $col->COLUMN_DEFAULT;
                }

                $colLines[] = $line;
            }

            $sqlContenido .= implode(",\n", $colLines);
            $sqlContenido .= "\n);\nGO\n\n";
        }

        // 3. Escribir y sobreescribir el archivo .sql del proyecto
        File::put(base_path('database/QueryBD-SIGEMPI.sql'), $sqlContenido);

        $this->info('¡Éxito! El script estructural completo con los CREATE TABLE reales ha sido inyectado en database/QueryBD-SIGEMPI.sql.');
    }
}