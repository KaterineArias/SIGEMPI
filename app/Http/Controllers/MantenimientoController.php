<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class MantenimientoController extends Controller
{
    /**
     * VISTA DEL COORDINADOR GENERAL
     */
    public function index(Request $request)
    {
        $tecnicoId = $request->query('tecnico_id');

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento');

        if (!empty($tecnicoId)) {
            $query->where('Mantenimientos.ID_Tecnico', $tecnicoId);
        }

        $proximos = $query->orderBy('Mantenimientos.Fecha_Programada', 'desc')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca',
                'Equipos.Modelo',
                'Equipos.ID_Estado',
                'Tipos_Equipo.Nombre_Tipo',
                'Ubicacion.NombreSede as Nombre_Edificio',
                'Municipio.NombreMunicipio as Nombre_DepartamentoInst',
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento'
            )
            ->paginate(15);

        $tecnicos = DB::table('Users')->where('ID_Rol', 2)->get();
        
        $stats = [
            'total_equipos'    => DB::table('Equipos')->count(),
            'equipos_activos'  => DB::table('Equipos')->where('ID_Estado', 2)->count(),
            'equipos_danados'  => DB::table('Equipos')->where('ID_Estado', 1)->count(),
            'total_tecnicos'   => DB::table('Users')->where('ID_Rol', 2)->count(),
            'mant_programados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->count(),
            'mant_completados' => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 2)->count(),
            'mant_este_mes'    => DB::table('Mantenimientos')->whereMonth('Fecha_Programada', 6)->count(),
            'vencidos'         => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('Fecha_Programada', '<', '2026-06-04')->count(),
            'criticos'         => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('Fecha_Programada', '2026-06-04')->count(),
            'a_tiempo'         => DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('Fecha_Programada', '>', '2026-06-04')->count(),
        ];

        return view('dashboard.coordinador', compact('proximos', 'tecnicos', 'stats'));
    }

    /**
     * 💾 HISTORIAL PRIVADO DEL TÉCNICO LOGUEADO (Exportación Word ajustada para forzar UNA SOLA PÁGINA)
     */
    public function historialTecnico(Request $request)
    {
        $usuarioLogueado = session('usuario') ?? 'tec_oswaldo';
        $search = $request->query('search', '');
        $tipoHardware = $request->query('tipo');
        $exportar = $request->query('exportar');

        $userRow = DB::table('Users')->where('Usuario', $usuarioLogueado)->first();
        $idUser = $userRow ? $userRow->ID_User : 2;

        // OBTENCIÓN BASE PARA CONTADORES Y EXPORTACIONES COMPLETAS
        $totalRegistrosBaseQuery = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->join('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $idUser)
            ->where('Mantenimientos.ID_EstadoMantenimiento', 2);

        if (!empty($tipoHardware) && $tipoHardware !== 'Todos') {
            $totalRegistrosBaseQuery->where('Tipos_Equipo.Nombre_Tipo', '=', $tipoHardware);
        }
        if (!empty($search)) {
            $totalRegistrosBaseQuery->where('Equipos.Codigo_Inventario', 'LIKE', '%' . $search . '%');
        }

        $todosLosRegistrosCerrados = $totalRegistrosBaseQuery->orderBy('Mantenimientos.Fecha_Cierre', 'desc')
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

            $meta = strtotime($reg->Fecha_Reprogramacion ?? $reg->Fecha_Programada);
            $cierre = isset($reg->Fecha_Cierre) ? strtotime($reg->Fecha_Cierre) : null;
            
            $reg->es_a_tiempo = ($cierre && ($cierre <= ($meta + 86399)));
            
            if ($reg->es_a_tiempo) {
                $contadorATiempo++;
            }
            if ($reg->ID_EstadoEquipo == 2) {
                $contadorEquiposOperativos++;
            }

            $reg->Sla_Texto_Inyectado = $reg->es_a_tiempo ? 'SI (Dentro del plazo estipulado)' : 'NO (Fuera del margen del SLA)';
            $reg->EstadoHardwareTexto = ($reg->ID_EstadoEquipo == 2) ? 'Operativo (Activo / En Servicio)' : 'Dañado (Fuera de Servicio)';
            
            return $reg;
        });

        $porcentajeSlaReal = $contadorTotal > 0 ? round(($contadorATiempo / $contadorTotal) * 100) : 100;

        // 👑 MAQUETACIÓN ULTRA COMPACTA EVITANDO EL DESBORDE DE image_e0e542.png
        if (!empty($exportar)) {
            $filename = "Reporte_Bitacora_" . date('Ymd');

            if ($exportar === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
                $fp = fopen('php://output', 'w');
                fputcsv($fp, ['ID Orden', 'Inventario', 'Hardware / Tipo', 'Ubicacion / Sede', 'Fecha Cierre', 'Accion Ejecutada']);
                foreach ($todosLosRegistrosCerrados as $row) {
                    fputcsv($fp, [$row->ID_Mantenimiento, $row->Codigo_Inventario, $row->Nombre_Tipo, $row->UbicacionFisicaSede, $row->Fecha_Cierre, $row->Accion_Realizada]);
                }
                fclose($fp);
                exit;
            }

            header("Content-Disposition: attachment; filename=\"$filename." . ($exportar === 'excel' ? 'xls' : 'doc') . "\"");
            header("Content-Type: application/vnd.ms-" . ($exportar === 'excel' ? 'excel' : 'word'));

            echo "<html><head><meta charset='utf-8'>
            <style>
                @page { margin: 1.2cm 1.2cm 1.2cm 1.2cm; }
                body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; font-size: 11px; line-height: 1.2; }
                .h-title { font-size: 20px; font-weight: bold; color: #0f172a; margin-bottom: 2px; text-transform: uppercase; }
                .h-sub { font-size: 10px; color: #64748b; margin-bottom: 6px; }
                .meta-table { font-size: 12px; margin-bottom: 12px; }
                .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
                .data-table th { background: #0f172a; color: #ffffff; padding: 7px; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; }
                .data-table td { padding: 6px; border: 1px solid #cbd5e1; color: #334155; vertical-align: middle; }
                .f-container { width: 100%; margin-top: 45px; font-size: 12px; }
                .f-block { width: 45%; text-align: center; vertical-align: top; }
            </style>
            </head><body>";

            echo "<div class='h-title'>SIGEMPI — HOJA DE BITÁCORA CONSOLIDADA</div>";
            echo "<div class='h-sub'>Sistema de Gestión de Parque Informático</div>";
            echo "<hr style='border:0; border-top: 2px solid #0f172a; margin-top:2px; margin-bottom: 8px;'>";

            echo "<div class='meta-table'>
                    <b>Técnico Operador Responsable:</b> {$usuarioLogueado}<br>
                    <b>Fecha de Generación del Reporte:</b> " . date('d/m/Y h:i A') . "<br>
                    <b>Segmentación de Hardware:</b> " . (!empty($tipoHardware) ? $tipoHardware : 'Todos los Equipos') . "
                  </div>";

            echo "<table class='data-table'>
                    <thead>
                        <tr>
                            <th style='width:10%;'>ID Orden</th>
                            <th style='width:18%;'>Inventario</th>
                            <th style='width:15%;'>Hardware / Tipo</th>
                            <th>Ubicación / Sede</th>
                            <th style='width:18%;'>Fecha Cierre</th>
                            <th style='width:22%;'>Acción Ejecutada</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($todosLosRegistrosCerrados as $row) {
                echo "<tr>
                        <td align='center'>MANT-{$row->ID_Mantenimiento}</td>
                        <td><b>{$row->Codigo_Inventario}</b></td>
                        <td>{$row->Nombre_Tipo}</td>
                        <td>{$row->UbicacionFisicaSede}</td>
                        <td align='center'>{$row->Fecha_Cierre}</td>
                        <td>" . ($row->Accion_Realizada ?? 'Diagnóstico de equipo') . "</td>
                      </tr>";
            }
            echo "</tbody></table>";

            // Reducción drástica del espacio superior (`margin-top`) para evitar que salte a la página 2
            echo "<table class='f-container' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td class='f-block' align='center'>
                            <div style='border-top: 1px solid #1e293b; width: 80%; padding-top: 4px; margin-top:30px;'>
                                <b>Firma de Técnico Operador</b><br>
                                <span style='font-size:10px; color:#64748b;'>{$usuarioLogueado}</span>
                            </div>
                        </td>
                        <td style='width: 10%;'>&nbsp;</td>
                        <td class='f-block' align='center'>
                            <div style='border-top: 1px solid #1e293b; width: 80%; padding-top: 4px; margin-top:30px;'>
                                <b>Firma de Recibido Conforme</b><br>
                                <span style='font-size:10px; color:#64748b;'>Encargado de Laboratorio / Sede</span>
                            </div>
                        </td>
                    </tr>
                  </table>";

            echo "</body></html>";
            exit;
        }

        // CONSULTA PAGINADA PARA PANTALLA WEB
        $queryGrid = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->join('Mantenimiento_Detalle', 'Mantenimientos.ID_Mantenimiento', '=', 'Mantenimiento_Detalle.ID_Mantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $idUser)
            ->where('Mantenimientos.ID_EstadoMantenimiento', 2);

        if (!empty($tipoHardware) && $tipoHardware !== 'Todos') {
            $queryGrid->where('Tipos_Equipo.Nombre_Tipo', '=', $tipoHardware);
        }
        if (!empty($search)) {
            $queryGrid->where('Equipos.Codigo_Inventario', 'LIKE', '%' . $search . '%');
        }

        $misMantenimientosPasados = $queryGrid->orderBy('Mantenimientos.Fecha_Cierre', 'desc')
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
            ->paginate(15);

        $misMantenimientosPasados->getCollection()->transform(function($reg) {
            $edificioLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_Edificio);
            $deptoLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_DepartamentoInst);
            $reg->UbicacionFisicaSede = $edificioLimpio . ' — ' . $deptoLimpio;

            $meta = strtotime($reg->Fecha_Reprogramacion ?? $reg->Fecha_Programada);
            $cierre = isset($reg->Fecha_Cierre) ? strtotime($reg->Fecha_Cierre) : null;
            
            $reg->es_a_tiempo = ($cierre && ($cierre <= ($meta + 86399)));
            $reg->Sla_Texto_Inyectado = $reg->es_a_tiempo ? 'SI (Dentro del plazo estipulado)' : 'NO (Fuera del margen del SLA)';
            $reg->EstadoHardwareTexto = ($reg->ID_EstadoEquipo == 2) ? 'Operativo (Activo / En Servicio)' : 'Dañado (Fuera de Servicio)';
            
            return $reg;
        });

        $kpis = [
            'total'      => $contadorTotal,
            'operativos' => $contadorEquiposOperativos,
            'sla_rate'   => $porcentajeSlaReal
        ];

        return view('dashboard.historial_tecnico', compact('misMantenimientosPasados', 'search', 'kpis'));
    }

    /**
     * 📋 VISTA DE ASIGNACIONES ACTIVAS PENDIENTES
     */
    public function dashboardTecnico(Request $request)
    {
        $usuarioLogueado = session('usuario') ?? 'tec_oswaldo';
        $filtro = $request->query('filtro', 'Asignados');
        $search = $request->query('search', '');

        $userRow = DB::table('Users')->where('Usuario', $usuarioLogueado)->first();
        $idUser = $userRow ? $userRow->ID_User : 2;

        $cantAsignadosPendientes = DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('ID_Tecnico', $idUser)->count();
        $cantAsignadosMes = DB::table('Mantenimientos')->whereMonth('Fecha_Programada', 6)->where('ID_Tecnico', $idUser)->count();
        $cantCerrados = DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 2)->where('ID_Tecnico', $idUser)->count();
        
        $cantVencidos = DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('ID_Tecnico', $idUser)->where('Fecha_Programada', '<', '2026-06-04')->count();
        $cantCriticos = DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('ID_Tecnico', $idUser)->where('Fecha_Programada', '=', '2026-06-04')->count();
        $cantSeguros = DB::table('Mantenimientos')->where('ID_EstadoMantenimiento', 1)->where('ID_Tecnico', $idUser)->where('Fecha_Programada', '>', '2026-06-04')->count();

        $panelStats = [
            'pendientes' => $cantAsignadosPendientes,
            'este_mes'   => $cantAsignadosMes,
            'cerrados'   => $cantCerrados,
            'vencidos'   => $cantVencidos,
            'criticos'   => $cantCriticos,
            'seguros'    => $cantSeguros
        ];

        $queryGrid = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Municipio', 'Ubicacion.ID_Municipio', '=', 'Municipio.ID_Municipio')
            ->join('Catalogo_EstadoMantenimiento', 'Mantenimientos.ID_EstadoMantenimiento', '=', 'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', $idUser);

        if ($filtro === 'Completados') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 2);
        } elseif ($filtro === 'Vencidos') {
            $queryGrid->where('Mantenimientos.ID_EstadoMantenimiento', 1)->where('Mantenimientos.Fecha_Programada', '<', '2026-06-04');
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
            )
            ->get();

        $datosGridMapeados = $registrosRaw->map(function($reg) {
            $edificioLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_Edificio);
            $deptoLimpio = str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $reg->Nombre_DepartamentoInst);
            $reg->UbicacionFisicaSede = $edificioLimpio . ' — ' . $deptoLimpio;

            $meta = strtotime($reg->Fecha_Reprogramacion ?? $reg->Fecha_Programada);
            $cierre = isset($reg->Fecha_Cierre) ? strtotime($reg->Fecha_Cierre) : null;
            $reg->es_a_tempo = ($cierre && ($cierre <= ($meta + 86399)));
            
            return $reg;
        });

        return view('dashboard.tecnico', [
            'misAsignacionesPendientes'   => $datosGridMapeados,
            'panelStats'                  => $panelStats,
            'filtro'                      => $filtro,
            'search'                      => $search
        ]);
    }

    public function edit($id)
    {
        $mantenimiento = DB::table('Mantenimientos')->where('ID_Mantenimiento', $id)->first();
        if (!$mantenimiento) {
            return redirect()->route('dashboard.tecnico')->with('error', 'La orden de trabajo no existe.');
        }
        return redirect()->route('intervenciones.create', ['id_mantenimiento' => $id]);
    }

    public function update(Request $request, $id)
    {
        $comentarios = $request->input('Observaciones_Tecnicas') ?? $request->input('Observaciones') ?? 'Sin observaciones registradas.';
        $accion = $request->input('Accion_Realizada') ?? 'Mantenimiento Correctivo aplicado en Sede';
        $estadoEquipo = $request->input('ID_EstadoEquipo') ?? 2; 

        DB::beginTransaction();
        try {
            $mantenimiento = DB::table('Mantenimientos')->where('ID_Mantenimiento', $id)->first();
            if (!$mantenimiento) {
                return redirect()->route('dashboard.tecnico')->with('error', 'La orden no existe.');
            }

            $fechaCierreActual = now()->format('Y-m-d H:i:s');

            DB::statement("UPDATE Mantenimientos SET ID_EstadoMantenimiento = 2, Fecha_Cierre = ? WHERE ID_Mantenimiento = ?", [$fechaCierreActual, $id]);
            DB::statement("UPDATE Equipos SET ID_Estado = ? WHERE ID_Equipo = ?", [$estadoEquipo, $mantenimiento->ID_Equipo]);

            $idTecnico = session('id_user') ?? $mantenimiento->ID_Tecnico ?? 2;
            DB::table('Mantenimiento_Detalle')->where('ID_Mantenimiento', $id)->delete();
            DB::table('Mantenimiento_Detalle')->insert([
                'ID_Mantenimiento'       => $id,
                'ID_TecnicoIntervino'    => $idTecnico,
                'Fecha_Registro'         => now(),
                'Accion_Realizada'       => $accion,
                'Observaciones_Tecnicas' => $comentarios
            ]);

            DB::commit();
            return redirect()->route('dashboard.tecnico', ['filtro' => 'Asignados'])->with('success', '¡Mantenimiento cerrado con éxito!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.tecnico')->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }
}