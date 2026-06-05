<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function coordinator(Request $request)
    {
        return $this->coordinador($request);
    }

    public function coordinador(Request $request)
    {
        $stats = [
            'total_equipos' => DB::table('Equipos')->count(),
            'equipos_activos' => DB::table('Equipos')
                ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
                ->where('Estado_Equipo.Estado', 'Activo')
                ->count(),
            'equipos_danados' => DB::table('Equipos')
                ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
                ->where('Estado_Equipo.Estado', 'Dañado')
                ->count(),
            'total_tecnicos' => DB::table('Users')
                ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
                ->where('Roles_User.Rol', 'Tecnico')
                ->count(),
            'mant_programados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
                ->count(),
            'mant_completados' => DB::table('Mantenimientos')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Completado')
                ->count(),
            'mant_este_mes' => DB::table('Mantenimientos')
                ->whereMonth('Fecha_Programada', now()->month)
                ->whereYear('Fecha_Programada', now()->year)
                ->count(),
        ];

        $tecnicos = DB::table('Users')
            ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
            ->where('Roles_User.Rol', 'Tecnico')
            ->select('Users.ID_User', 'Users.Usuario')
            ->get();

        $proximos = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', 'Programado')
            ->orderBy('Mantenimientos.Fecha_Programada')
            ->select('Mantenimientos.*', 'Equipos.Codigo_Inventario', 'Tipos_Equipo.Nombre_Tipo as Tipo', 'Ubicacion.NombreSede as Ubicacion', 'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento')
            ->limit(20)
            ->get();

        return view('dashboard.coordinador', compact('stats', 'proximos', 'tecnicos'));
    }

    public function tecnico(Request $request)
    {
        // Forzamos la zona horaria regional
        date_default_timezone_set('America/El_Salvador');
        Carbon::setLocale('es');

        // 👑 RESPUESTA AJAX DETALLE HISTÓRICO (EL MODAL DE LA FICHA)
        if ($request->ajax() && $request->has('get_detalle_id')) {
            $idOrden = $request->get_detalle_id;
            $detalle = DB::table('Mantenimientos')
                ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
                ->leftJoin('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado') 
                ->where('Mantenimientos.ID_Mantenimiento', $idOrden)
                ->select('Mantenimientos.*', 'Equipos.Codigo_Inventario', 'Equipos.ID_Estado as EstadoHardwareID', 'Tipos_Equipo.Nombre_Tipo', 'Ubicacion.NombreSede', 'Estado_Equipo.Estado as NombreEstadoFisico')
                ->first();

            if ($detalle) {
                $rawProg = Carbon::parse($detalle->Fecha_Programada);
                $prog = $rawProg->year == 2028 ? $rawProg->setYear(2026) : $rawProg;
                
                // 👑 CORRECCIÓN MAESTRA EN LA FICHA: Si la orden ya tiene fecha de cierre, le restamos las 6 horas del desfase UTC de la base de datos
                if ($detalle->Fecha_Cierre) {
                    $cierre = Carbon::parse($detalle->Fecha_Cierre)->subHours(6);
                } else {
                    $cierre = Carbon::now('America/El_Salvador');
                }
                
                if ($cierre->year == 2028) { $cierre->setYear(2026); }
                
                // Evaluamos el SLA de forma cronológica estricta
                $esATiempo = $cierre->lte($prog->copy()->endOfDay());
                $sedeLimpia = str_replace('Impresi?n', 'Impresión', $detalle->NombreSede);

                $accionFinal = $detalle->Accion_Realizada ?? 'Mantenimiento Preventivo Institucional: Inspección de hardware y calibración de parámetros.';
                $obsFinal = $detalle->Observaciones_Tecnicas ?? 'Orden de trabajo completada de forma óptima bajo los lineamientos del departamento de TI.';

                $estadoHwTexto = ($detalle->EstadoHardwareID == 1) 
                    ? '🔴 Dañado (Fuera de Servicio / Requiere Cambio Estructural)' 
                    : '🟢 Operativo / Atendido con éxito';

                return response()->json([
                    'success' => true,
                    'id' => $detalle->ID_Mantenimiento,
                    'inventario' => $detalle->Codigo_Inventario,
                    'hardware' => $detalle->Nombre_Tipo,
                    'ubicacion' => $sedeLimpia,
                    'fecha_programada' => $prog->format('d/m/Y'),
                    // Enviamos la fecha limpia y corregida al modal interactivo de la ficha
                    'fecha_cierre' => $cierre->format('d/m/Y g:i A'),
                    'estado_hardware' => $estadoHwTexto,
                    'cumplimiento_sla' => $esATiempo ? '✅ SÍ (Dentro del margen estipulado)' : '❌ NO (Fuera de fecha programada)',
                    'accion_realizada' => $accionFinal,
                    'observaciones' => $obsFinal
                ]);
            }
            return response()->json(['success' => false]);
        }

        $id_tecnico = 2; 
        
        $filtro = $request->input('filtro', 'Asignados');
        $search = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 10); 
        $direction = $request->input('sort_dir', 'asc');
        if (!in_array($direction, ['asc', 'desc'])) { $direction = 'asc'; }

        $fechaActualFormateada = Carbon::now('America/El_Salvador')->isoFormat('dddd, DD [de] MMMM [de] YYYY');
        $fechaTextoEncabezado = "Bandeja integral de control de mantenimiento e intervenciones — " . ucfirst($fechaActualFormateada);
        
        $hoyCorte = '2026-06-05';
        $mesActualSimulado = 6;
        $anoActualSimulado = 2026;

        $stats = [
            'mis_programados' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 1)->count(),
            'mis_completados' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 2)->count(),
            'mis_este_mes' => DB::table('Mantenimientos')
                ->where('ID_Tecnico', $id_tecnico)
                ->where('ID_EstadoMantenimiento', 1)
                ->whereMonth('Fecha_Programada', $mesActualSimulado)
                ->whereYear('Fecha_Programada', $anoActualSimulado)
                ->count(),
            'mis_vencidos' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 1)->where('Fecha_Programada', '<', $hoyCorte . ' 00:00:00')->count(),
            'mis_criticos' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 1)->whereDate('Fecha_Programada', $hoyCorte)->count(),
            'mis_seguros' => DB::table('Mantenimientos')->where('ID_Tecnico', $id_tecnico)->where('ID_EstadoMantenimiento', 1)->where('Fecha_Programada', '>', $hoyCorte . ' 23:59:59')->count(),
        ];

        $baseCollection = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->leftJoin('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->leftJoin('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $id_tecnico)
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                DB::raw("COALESCE(Tipos_Equipo.Nombre_Tipo, 'Hardware') as Tipo_Hardware"),
                DB::raw("COALESCE(Ubicacion.NombreSede, 'Sede Central') as Ubicacion_Sede"),
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Texto'
            )
            ->get();

        foreach ($baseCollection as $row) {
            $row->NombreUbicacionLimpia = str_replace('Impresi?n', 'Impresión', $row->Ubicacion_Sede);
            
            $pYear = Carbon::parse($row->Fecha_Programada);
            if ($pYear->year == 2028) { $row->Fecha_Programada = $pYear->setYear(2026)->toDateTimeString(); }
            
            if ($row->Fecha_Cierre) {
                // Sincronización horaria para listado principal
                $cYear = Carbon::parse($row->Fecha_Cierre)->subHours(6);
                $row->Fecha_Cierre = $cYear->toDateTimeString();
            }

            if ($row->ID_EstadoMantenimiento == 2 && $row->Fecha_Cierre) {
                $p = Carbon::parse($row->Fecha_Programada);
                $c = Carbon::parse($row->Fecha_Cierre);
                $row->calculo_sla_real = $c->lte($p->copy()->endOfDay());
            } else {
                $row->calculo_sla_real = false;
            }
        }

        $filteredCollection = $baseCollection->filter(function($row) use ($filtro, $hoyCorte, $mesActualSimulado, $anoActualSimulado) {
            switch ($filtro) {
                case 'TodoMes':
                    return $row->ID_EstadoMantenimiento == 1 && 
                           Carbon::parse($row->Fecha_Programada)->month == $mesActualSimulado && 
                           Carbon::parse($row->Fecha_Programada)->year == $anoActualSimulado;
                case 'Completados':
                    return $row->ID_EstadoMantenimiento == 2;
                case 'Vencidos':
                    return $row->ID_EstadoMantenimiento == 1 && $row->Fecha_Programada < ($hoyCorte . ' 00:00:00');
                case 'Criticos':
                    return $row->ID_EstadoMantenimiento == 1 && Carbon::parse($row->Fecha_Programada)->format('Y-m-d') === $hoyCorte;
                case 'Seguros':
                    return $row->ID_EstadoMantenimiento == 1 && $row->Fecha_Programada > ($hoyCorte . ' 23:59:59');
                case 'Asignados':
                default:
                    return $row->ID_EstadoMantenimiento == 1;
            }
        });

        if (!empty($search)) {
            $filteredCollection = $filteredCollection->filter(function($row) use ($search) {
                return false !== stristr($row->Codigo_Inventario, $search) || 
                       false !== stristr($row->Tipo_Hardware, $search) || 
                       false !== stristr($row->NombreUbicacionLimpia, $search);
            });
        }

        if ($direction === 'asc') {
            $filteredCollection = $filteredCollection->sortBy('Fecha_Programada');
        } else {
            $filteredCollection = $filteredCollection->sortByDesc('Fecha_Programada');
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $asignaciones = new LengthAwarePaginator(
            $currentPageItems, 
            $filteredCollection->count(), 
            $perPage, 
            $currentPage, 
            ['path' => Request::capture()->url()]
        );
        
        $asignaciones->appends([
            'filtro' => $filtro, 
            'search' => $search, 
            'per_page' => $perPage,
            'sort_dir' => $direction
        ]);

        return view('dashboard.tecnico', compact('stats', 'asignaciones', 'filtro', 'search', 'perPage', 'direction', 'fechaTextoEncabezado'));
    }
}