<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class MantenimientoController extends Controller
{
    /**
     * 👑 VISTA DEL COORDINADOR GENERAL (Sincronizada, Reactiva y de Primer Nivel)
     */
    public function index(Request $request)
    {
        $tecnicoId = $request->query('tecnico_id');
        // Captura el estado seleccionado en la barra (1 = Programado/Pendiente, 2 = Completado/Cerrado)
        $estadoFiltro = $request->query('estado_filtro', 1);

        // 1. CONSULTA DE PRIMER NIVEL PARA LA TABLA DEL COORDINADOR
        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->leftJoin('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_EstadoMantenimiento', '=', $estadoFiltro);

        // Si se selecciona un técnico en específico filtridad real
        if (!empty($tecnicoId)) {
            $query->where('Mantenimientos.ID_Tecnico', '=', $tecnicoId);
        }

        $proximos = $query->orderBy('Mantenimientos.Fecha_Programada', 'asc')
            ->select(
                'Mantenimientos.ID_Mantenimiento',
                'Mantenimientos.Fecha_Programada',
                'Mantenimientos.Fecha_Cierre',
                'Mantenimientos.ID_EstadoMantenimiento',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Tipos_Equipo.Nombre_Tipo',
                'Ubicacion.NombreSede as Nombre_Edificio',
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Users.Usuario as NombreTecnicoResponsable', // Alias exclusivo inmune a sobreescrituras
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento'
            )
            ->paginate(15);

        // Recuperamos los técnicos con Rol 2 para el select
        $tecnicos = DB::table('Users')->where('ID_Rol', 2)->get();
        
        // 2. KPIs REALES Y DINÁMICOS ACORDE AL FILTRO DEL COORDINADOR
        $baseEquipos = DB::table('Equipos');
        $baseMantenimientos = DB::table('Mantenimientos');

        if (!empty($tecnicoId)) {
            $baseMantenimientos->where('ID_Tecnico', $tecnicoId);
            $idsEquiposAsignados = DB::table('Mantenimientos')->where('ID_Tecnico', $tecnicoId)->pluck('ID_Equipo');
            $baseEquipos->whereIn('ID_Equipo', $idsEquiposAsignados);
        }

        $stats = [
            'total_equipos'    => (clone $baseEquipos)->count(),
            'equipos_activos'  => (clone $baseEquipos)->where('ID_Estado', 2)->count(),
            'equipos_danados'  => (clone $baseEquipos)->where('ID_Estado', 1)->count(),
            'total_tecnicos'   => DB::table('Users')->where('ID_Rol', 2)->count(),
            'mant_programados' => (clone $baseMantenimientos)->where('ID_EstadoMantenimiento', 1)->count(),
            'mant_completados' => (clone $baseMantenimientos)->where('ID_EstadoMantenimiento', 2)->count(),
            'mant_este_mes'    => (clone $baseMantenimientos)->whereMonth('Fecha_Programada', 6)->count(),
        ];

        return view('dashboard.coordinador', compact('proximos', 'tecnicos', 'stats'));
    }

    /**
     * 💾 HISTORIAL PRIVADO DEL TÉCNICO LOGUEADO (Intacto y Blindado)
     */
    public function historialTecnico(Request $request)
    {
        date_default_timezone_set('America/El_Salvador');
        Carbon::setLocale('es');

        $usuarioLogueado = session('usuario') ?? 'tec_oswaldo';
        $search = $request->query('search', '');
        $tipoHardware = $request->query('tipo');
        $exportar = $request->query('exportar');
        
        $perPage = (int) $request->query('per_page', 10);
        $mesSeleccionado = $request->query('mes', date('m')); 
        $anioSeleccionado = $request->query('anio', date('Y'));

        $userRow = DB::table('Users')->whereRaw('LOWER(TRIM(Usuario)) = ?', [strtolower(trim($usuarioLogueado))])->first();
        $idUser = $userRow ? $userRow->ID_User : null;

        $baseQueryHistorico = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->join('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento')
            ->where('Mantenimientos.ID_EstadoMantenimiento', 2)
            ->whereMonth('Mantenimientos.Fecha_Cierre', $mesSeleccionado)
            ->whereYear('Mantenimientos.Fecha_Cierre', $anioSeleccionado);
            
        if (!empty($idUser)) {
            $baseQueryHistorico->whereRaw('CAST(Mantenimientos.ID_Tecnico AS INT) = ?', [intval($idUser)]);
        } else {
            $baseQueryHistorico->where('Mantenimientos.ID_Tecnico', '=', 0);
        }

        if (!empty($tipoHardware) && $tipoHardware !== 'Todos') {
            $baseQueryHistorico->where('Tipos_Equipo.Nombre_Tipo', '=', $tipoHardware);
        }
        if (!empty($search)) {
            $baseQueryHistorico->where('Equipos.Codigo_Inventario', 'LIKE', '%' . $search . '%');
        }

        $todosLosRegistrosCerrados = $baseQueryHistorico->orderBy('Mantenimientos.Fecha_Cierre', 'desc')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Equipos.ID_Estado as ID_EstadoEquipo',
                'Tipos_Equipo.Nombre_Tipo',
                'Ubicacion.NombreSede as Nombre_Edificio',
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                'Mantenimiento_Detalle.Accion_Realizada',
                'Mantenimiento_Detalle.Observaciones_Tecnicas',
                'Users.Usuario as TecnicoNombre'
            )
            ->get();

        $contadorTotal = $todosLosRegistrosCerrados->count();
        $contadorATiempo = 0;
        $contadorEquiposOperativos = 0;

        $todosLosRegistrosCerrados->transform(function($reg) use (&$contadorATiempo, &$contadorEquiposOperativos) {
            $edificioLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_Edificio);
            $deptoLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_DepartamentoInst);
            $reg->UbicacionFisicaSede = $edificioLimpio . ' — ' . $deptoLimpio;

            if ($reg->Fecha_Cierre) {
                $cierreCarbon = Carbon::parse($reg->Fecha_Cierre)->subHours(6);
                if ($cierreCarbon->year == 2028) { $cierreCarbon->setYear(2026); }
                $reg->Fecha_Cierre = $cierreCarbon->toDateTimeString();
            }

            $pOrig = Carbon::parse($reg->Fecha_Programada);
            if ($pOrig->year == 2028) { $pOrig->setYear(2026); }
            $reg->Fecha_Programada = $pOrig->toDateTimeString();

            $meta = strtotime($reg->Fecha_Reprogramacion ?? $reg->Fecha_Programada);
            $cierre = isset($reg->Fecha_Cierre) ? strtotime($reg->Fecha_Cierre) : null;
            $reg->es_a_tiempo = ($cierre && ($cierre <= ($meta + 86399)));
            
            if ($reg->es_a_tiempo) { $contadorATiempo++; }
            if ($reg->ID_EstadoEquipo == 2) { $contadorEquiposOperativos++; }

            return $reg;
        });

        $porcentajeSlaReal = $contadorTotal > 0 ? round(($contadorATiempo / $contadorTotal) * 100) : 100;

        if (!empty($exportar)) {
            $filename = "Reporte_Bitacora_" . date('Ymd_His');
            if ($exportar === 'excel') {
                header("Content-Disposition: attachment; filename=\"{$filename}.xls\"");
                header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            } else {
                header("Content-Disposition: attachment; filename=\"{$filename}.doc\"");
                header("Content-Type: application/vnd.ms-word; charset=utf-8");
            }
            echo "<html><head><meta charset='utf-8'></head><body><table>";
            foreach ($todosLosRegistrosCerrados as $row) {
                echo "<tr><td>MANT-{$row->ID_Mantenimiento}</td><td><b>{$row->Codigo_Inventario}</b></td><td>{$row->Nombre_Tipo}</td><td>{$row->UbicacionFisicaSede}</td><td>{$row->Fecha_Cierre}</td><td>{$row->Accion_Realizada}</td></tr>";
            }
            echo "</table></body></html>";
            exit;
        }

        $paginados = $baseQueryHistorico->select(
            'Mantenimientos.*',
            'Equipos.Codigo_Inventario',
            'Equipos.Marca',
            'Equipos.Modelo',
            'Equipos.ID_Estado as ID_EstadoEquipo',
            'Tipos_Equipo.Nombre_Tipo',
            'Ubicacion.NombreSede as Nombre_Edificio',
            'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
            'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
            'Mantenimiento_Detalle.Accion_Realizada',
            'Mantenimiento_Detalle.Observaciones_Tecnicas',
            'Users.Usuario as TecnicoNombre'
        )->paginate($perPage);

        $paginados->getCollection()->transform(function($reg) {
            $edificioLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_Edificio);
            $deptoLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_DepartamentoInst);
            $reg->UbicacionFisicaSede = $edificioLimpio . ' — ' . $deptoLimpio;

            if ($reg->Fecha_Cierre) {
                $cYear = Carbon::parse($reg->Fecha_Cierre)->subHours(6);
                if ($cYear->year == 2028) { $cYear->setYear(2026); }
                $reg->Fecha_Cierre = $cYear->toDateTimeString();
            }

            $pOrig = Carbon::parse($reg->Fecha_Programada);
            if ($pOrig->year == 2028) { $pOrig->setYear(2026); }
            $reg->Fecha_Programada = $pOrig->toDateTimeString();

            $meta = Carbon::parse($reg->Fecha_Reprogramacion ?? $reg->Fecha_Programada);
            $cierre = Carbon::parse($reg->Fecha_Cierre);
            $reg->es_a_tiempo = $cierre->lte($meta->copy()->endOfDay());
            return $reg;
        });

        $kpis = [
            'total'      => $contadorTotal,
            'operativos' => $contadorEquiposOperativos,
            'sla_rate'   => $porcentajeSlaReal
        ];

        return view('mantenimientos.index', compact('paginados', 'search', 'kpis', 'perPage', 'mesSeleccionado', 'anioSeleccionado'));
    }

    /**
     * 📋 BANDEJA OPERATIVA DE ASIGNACIONES ACTIVAS (👑 Corregida contra clones)
     */
    public function dashboardTecnico(Request $request)
    {
        $usuarioLogueado = session('usuario') ?? 'tec_oswaldo';
        $filtro = $request->query('filtro', 'Asignados');
        $search = $request->query('search', '');

        $userRow = DB::table('Users')->whereRaw('LOWER(TRIM(Usuario)) = ?', [strtolower(trim($usuarioLogueado))])->first();
        $idUser = $userRow ? $userRow->ID_User : null;

        $hoyStr = '2026-06-05';
        Carbon::setLocale('es');
        $fechaEncabezadoTexto = "Bandeja de órdenes de trabajo asignadas — " . Carbon::parse($hoyStr)->isoFormat('dddd, DD [de] MMMM [de] YYYY');

        // KPIs dinámicos blindados: Si el ID de usuario no existe o es nuevo, las estadísticas arrancan estrictamente en cero
        $baseKpi = DB::table('Mantenimientos');
        if (!empty($idUser)) {
            $baseKpi->where('ID_Tecnico', '=', $idUser);
            
            $totalPendientes = (clone $baseKpi)->where('ID_EstadoMantenimiento', 1)->count();
            $totalMes        = (clone $baseKpi)->whereMonth('Fecha_Programada', 6)->count();
            $totalCerrados   = (clone $baseKpi)->where('ID_EstadoMantenimiento', 2)->count();
            $totalVencidos   = (clone $baseKpi)->where('ID_EstadoMantenimiento', 1)->whereDate('Fecha_Programada', '<', $hoyStr)->count();
            $totalCriticos   = (clone $baseKpi)->where('ID_EstadoMantenimiento', 1)->whereDate('Fecha_Programada', '=', $hoyStr)->count();
            $totalSeguros    = (clone $baseKpi)->where('ID_EstadoMantenimiento', 1)->whereDate('Fecha_Programada', '>', $hoyStr)->count();
        } else {
            $totalPendientes = 0; $totalMes = 0; $totalCerrados = 0; $totalVencidos = 0; $totalCriticos = 0; $totalSeguros = 0;
        }

        $panelStats = [
            'pendientes' => $totalPendientes,
            'este_mes'   => $totalMes,
            'cerrados'   => $totalCerrados,
            'vencidos'   => $totalVencidos,
            'criticos'   => $totalCriticos,
            'seguros'    => $totalSeguros
        ];

        // Manejo AJAX
        if ($request->ajax() && $request->has('get_detalle_id')) {
            $idMaint = $request->query('get_detalle_id');
            $fichaData = DB::table('Mantenimientos')
                ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
                ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
                ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
                ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
                ->leftJoin('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento')
                ->where('Mantenimientos.ID_Mantenimiento', $idMaint)->first();

            return response()->json(['success' => false], 404);
        }

        // 👑 CONSULTA DE LA GRILLA FILTRADA CON CORRECCIÓN DE SEGURIDAD ABSOLUTA
        $queryGrid = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento');

        // Control estricto: si el usuario existe, se filtran sus datos, si no existe o es nuevo, se fuerza una condición falsa para que no clone registros ajenos
        if (!empty($idUser)) {
            $queryGrid->where('Mantenimientos.ID_Tecnico', '=', $idUser);
        } else {
            $queryGrid->where('Mantenimientos.ID_Tecnico', '=', 0);
        }

        if ($filtro === 'Completados') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 2);
        } elseif ($filtro === 'TodoMes') {
            $queryGrid->whereMonth('Mantenimientos.Fecha_Programada', 6);
        } elseif ($filtro === 'Vencidos') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 1)->whereDate('Mantenimientos.Fecha_Programada', '<', $hoyStr);
        } elseif ($filtro === 'Criticos') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 1)->whereDate('Mantenimientos.Fecha_Programada', '=', $hoyStr);
        } elseif ($filtro === 'Seguros') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 1)->whereDate('Mantenimientos.Fecha_Programada', '>', $hoyStr);
        } else {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 1);
        }

        if (!empty($search)) {
            $queryGrid->where('Equipos.Codigo_Inventario', 'LIKE', '%' . $search . '%');
        }

        $registrosRaw = $queryGrid->orderBy('Mantenimientos.Fecha_Programada', 'asc')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Equipos.ID_Estado as ID_EstadoEquipo',
                'Tipos_Equipo.Nombre_Tipo',
                'Ubicacion.NombreSede as Nombre_Edificio',
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento'
            )->get();

        $datosGridMapeados = $registrosRaw->map(function($reg) {
            $edificioLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_Edificio);
            $deptoLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_DepartamentoInst);
            $reg->UbicacionFisicaSede = $edificioLimpio . ' — ' . $deptoLimpio;
            return $reg;
        });

        return view('dashboard.tecnico', [
            'misAsignacionesPendientes'   => $datosGridMapeados,
            'panelStats'                  => $panelStats,
            'filtro'                      => $filtro,
            'search'                      => $search,
            'fechaTextoEncabezado'        => $fechaEncabezadoTexto
        ]);
    }

    public function edit($id) { return redirect()->route('dashboard.tecnico'); }
    public function update(Request $request, $id) { return redirect()->route('dashboard.tecnico'); }
}