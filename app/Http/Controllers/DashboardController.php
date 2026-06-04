<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard del Coordinador: Control Global de la Flota y Órdenes con Semáforo de Tiempos
     */
    public function coordinador(Request $request)
    {
        // --- INICIO CÁLCULO SEMÁFORO (REGLA DE NEGOCIO SLA) ---
        $mantenimientosActivosGlobales = DB::table('Mantenimientos')
            ->whereIn('ID_EstadoMantenimiento', [1, 3]) // 1 = Programado, 3 = Reprogramado
            ->get();

        $vencidos = 0;   // Rojo
        $criticos = 0;   // Naranja (Día de control activo)
        $aTiempo = 0;    // Verde

        foreach ($mantenimientosActivosGlobales as $mant) {
            $fechaMetaStr = $mant->Fecha_Reprogramacion ?? $mant->Fecha_Programada;
            $fechaMetaLimpia = date('Y-m-d', strtotime($fechaMetaStr));
            
            // SINCRONIZACIÓN REAL: Conectado estrictamente al 03 de Junio de 2026
            $fechaHoySistema = '2026-06-03'; 

            if ($fechaMetaLimpia === $fechaHoySistema) {
                $criticos++;
            } elseif (strtotime($fechaMetaLimpia) < strtotime($fechaHoySistema)) {
                $vencidos++;
            } else {
                $aTiempo++;
            }
        }
        // --- FIN CÁLCULO SEMÁFORO ---

        $stats = [
            'total_equipos'    => DB::table('Equipos')->count(),
            'equipos_activos'  => DB::table('Equipos')->where('ID_Estado', 2)->count(),   
            'equipos_danados'  => DB::table('Equipos')->where('ID_Estado', 1)->count(),   
            'total_tecnicos'   => DB::table('Users')->where('ID_Rol', 2)->count(),         
            'mant_programados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->count(), 
            'mant_completados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 2)->count(), 
            'mant_este_mes'    => DB::table('Mantenimientos')
                                    ->whereMonth('Fecha_Programada', 6)
                                    ->whereYear('Fecha_Programada', 2026)
                                    ->count(),
            'vencidos'         => $vencidos,
            'criticos'         => $criticos,
            'a_tiempo'         => $aTiempo,
        ];

        $tecnicos = DB::table('Users')->where('ID_Rol', 2)->get();
        $filtro = $request->query('filtro');
        $tecnicoId = $request->query('tecnico_id');

        $esFiltroEquipo = in_array($filtro, ['total_equipos', 'equipos_activos', 'equipos_danados']);
        $esFiltroTecnico = ($filtro === 'total_tecnicos');

        if ($esFiltroTecnico) {
            $usuariosObtenidos = DB::table('Users')
                ->where('ID_Rol', 2) 
                ->select('Users.ID_User', 'Users.Usuario', 'Users.Correo_User', 'Users.Fecha_CreacionUser', 'Users.ID_EstadoUsuario')
                ->get();

            foreach ($usuariosObtenidos as $u) {
                $u->Estado_Nombre = ($u->ID_EstadoUsuario == 1) ? 'Activo' : 'Inactivo';
                $u->Fecha_BajaUser = null; 
            }
            $proximos = $usuariosObtenidos;

        } elseif ($esFiltroEquipo) {
            $query = DB::table('Equipos')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
                ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
                ->join('Departamento', 'Municipio.ID_Departamento', '=', 'Departamento.ID_Departamento')
                ->leftJoin('Mantenimientos', 'Equipos.ID_Equipo', '=', 'Mantenimientos.ID_Equipo')
                ->leftJoin('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento');

            if ($filtro === 'equipos_activos') {
                $query->where('Equipos.ID_Estado', 2);
            } elseif ($filtro === 'equipos_danados') {
                $query->where('Equipos.ID_Estado', 1);
            }

            $proximos = $query->select(
                'Equipos.*',
                'Equipos.ID_Estado as Estado_Equipo',
                'Tipos_Equipo.Nombre_Tipo',
                'Ubicacion.NombreSede as Nombre_Edificio',
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Mantenimientos.ID_Mantenimiento',
                'Mantenimientos.Fecha_Programada as Fecha_Falla_Real', 
                'Mantenimiento_Detalle.Observaciones_Tecnicas as Detalle_Falla'
            )->get();

            $proximos = $proximos->unique('ID_Equipo');

        } else {
            $query = DB::table('Mantenimientos')
                ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
                ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
                ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
                ->leftJoin('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                // CORRECCIÓN COORDINADOR: Enlace robusto a la bitácora relacional real
                ->leftJoin('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento');

            if ($filtro) {
                if ($filtro === 'este_mes') {
                    $query->whereMonth('Mantenimientos.Fecha_Programada', 6)->whereYear('Mantenimientos.Fecha_Programada', 2026);
                } elseif ($filtro === 'pendientes') {
                    $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
                } elseif ($filtro === 'completados') {
                    $query->where('Mantenimientos.ID_EstadoMantenimiento', 2);
                }
            } else {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
            }

            if ($request->filled('tecnico_id')) {
                $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
            }

            $mantenimientosObtenidos = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')
                ->select(
                    'Mantenimientos.*',
                    'Equipos.Codigo_Inventario',
                    'Equipos.Marca',
                    'Equipos.Modelo',
                    'Equipos.ID_Estado as ID_EstadoEquipo', // Mapeado de estatus de hardware global
                    'Tipos_Equipo.Nombre_Tipo',
                    'Ubicacion.NombreSede as Nombre_Edificio',
                    'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                    'Users.Usuario as Tecnico',
                    'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                    // Sincronización exacta de variables para la vista del Coordinador
                    'Mantenimiento_Detalle.Accion_Realizada',
                    'Mantenimiento_Detalle.Observaciones_Tecnicas'
                )
                ->get();

            if (in_array($filtro, ['vencidos', 'criticos', 'a_tiempo'])) {
                $proximos = $mantenimientosObtenidos->filter(function($m) use ($filtro) {
                    if (!in_array($m->ID_EstadoMantenimiento, [1, 3])) return false;
                    $fechaMetaStr = $m->Fecha_Reprogramacion ?? $m->Fecha_Programada;
                    $fechaMetaLimpia = date('Y-m-d', strtotime($fechaMetaStr));
                    $fechaHoySistema = '2026-06-03'; 

                    if ($filtro === 'vencidos') { return strtotime($fechaMetaLimpia) < strtotime($fechaHoySistema); }
                    elseif ($filtro === 'criticos') { return $fechaMetaLimpia === $fechaHoySistema; }
                    elseif ($filtro === 'a_tiempo') { return strtotime($fechaMetaLimpia) > strtotime($fechaHoySistema); }
                    return true;
                })->take(20);
            } else {
                $proximos = $mantenimientosObtenidos->take(20);
            }
        }

        return view('dashboard.coordinador', compact('stats', 'proximos', 'tecnicos', 'esFiltroEquipo', 'esFiltroTecnico', 'filtro'));
    }

    /**
     * Dashboard del Técnico: Mapeo de Bitácora Relacional para Órdenes Sincronizadas
     */
    public function tecnico(Request $request)
    {
        $id_tecnico = session('id_user');
        $hoy = '2026-06-03'; 

        $misMantenimientosActivos = DB::table('Mantenimientos')
            ->where('ID_Tecnico', $id_tecnico)
            ->whereIn('ID_EstadoMantenimiento', [1, 3])
            ->get();

        $misVencidos = 0; $misCriticos = 0; $misATiempo = 0;

        foreach ($misMantenimientosActivos as $mant) {
            $fechaMetaStr = $mant->Fecha_Reprogramacion ?? $mant->Fecha_Programada;
            $fechaMetaLimpia = date('Y-m-d', strtotime($fechaMetaStr));

            if ($fechaMetaLimpia === $hoy) {
                $misCriticos++;
            } elseif (strtotime($fechaMetaLimpia) < strtotime($hoy)) {
                $misVencidos++;
            } else {
                $misATiempo++;
            }
        }

        $stats = [
            'mis_programados' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 1)->count(),
            'mis_completados' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 2)->count(),
            'mis_este_mes'    => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->whereMonth('Fecha_Programada', 6)->whereYear('Fecha_Programada', 2026)->count(),
            'mis_vencidos'    => $misVencidos,
            'mis_criticos'    => $misCriticos,
            'mis_a_tiempo'    => $misATiempo,
        ];

        $filtro = $request->query('filtro');

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->leftJoin('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $id_tecnico);

        if ($filtro) {
            if ($filtro === 'completados') {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 2);
            } elseif ($filtro === 'este_mes') {
                $query->whereMonth('Mantenimientos.Fecha_Programada', 6)->whereYear('Mantenimientos.Fecha_Programada', 2026);
            } elseif ($filtro === 'pendientes') {
                $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
            }
        } else {
            $query->where('Mantenimientos.ID_EstadoMantenimiento', 1);
        }

        $misMantenimientosObtenidos = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')
            ->select(
                'Mantenimientos.*', 
                'Mantenimientos.Fecha_Cierre',
                'Equipos.Codigo_Inventario', 
                'Equipos.Marca', 
                'Equipos.Modelo', 
                'Equipos.ID_Estado as ID_EstadoEquipo',
                'Tipos_Equipo.Nombre_Tipo', 
                'Ubicacion.NombreSede as Nombre_Edificio', 
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                'Mantenimiento_Detalle.Accion_Realizada',
                'Mantenimiento_Detalle.Observaciones_Tecnicas'
            )
            ->get();

        if (in_array($filtro, ['mis_vencidos', 'mis_criticos', 'mis_a_tiempo'])) {
            $asignaciones = $misMantenimientosObtenidos->filter(function($m) use ($filtro, $hoy) {
                if (!in_array($m->ID_EstadoMantenimiento, [1, 3])) return false;
                $fechaMetaStr = $m->Fecha_Reprogramacion ?? $m->Fecha_Programada;
                $fechaMetaLimpia = date('Y-m-d', strtotime($fechaMetaStr));

                if ($filtro === 'mis_vencidos') { return strtotime($fechaMetaLimpia) < strtotime($hoy); }
                elseif ($filtro === 'mis_criticos') { return $fechaMetaLimpia === $hoy; }
                elseif ($filtro === 'mis_a_tiempo') { return strtotime($fechaMetaLimpia) > strtotime($hoy); }
                return true;
            })->take(30);
        } else {
            $asignaciones = $misMantenimientosObtenidos->take(30);
        }

        return view('dashboard.tecnico', compact('stats', 'asignaciones', 'filtro'));
    }
}