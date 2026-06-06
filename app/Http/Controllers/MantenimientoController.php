<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\User;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $tecnicosQuery = User::join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
                             ->where('Roles_User.Rol', 'Tecnico')
                             ->select('Users.ID_User', 'Users.Usuario');

        $tecnicos = $tecnicosQuery->get();

        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento',
                   'Mantenimientos.ID_EstadoMantenimiento', '=',
                   'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion',
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento'
            );

        if (session('rol') === 'Tecnico') {
            $query->where('Mantenimientos.ID_Tecnico', session('id_user'));
        } elseif ($request->filled('tecnico_id')) {
            $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
        }

        if ($request->filled('estado')) {
            $query->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento', $request->estado);
        }

        $mantenimientos = $query->orderBy('Mantenimientos.Fecha_Programada')->get();

        return view('mantenimientos.index', compact('mantenimientos', 'tecnicos'));
    }

    public function create()
    {
        $equipos = DB::table('Equipos')
            ->join('Estado_Equipo', 'Equipos.ID_Estado', '=', 'Estado_Equipo.ID_Estado')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->where('Estado_Equipo.Estado', '!=', 'De Baja')             
            ->select(
                'Equipos.ID_Equipo',
                'Equipos.Codigo_Inventario',
                'Equipos.Marca', 
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion'
            )
            ->get();

        $tecnicos = DB::table('Users')
            ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
            ->where('Roles_User.Rol', 'Tecnico')
            ->select('Users.ID_User', 'Users.Usuario')
            ->get();

        return view('mantenimientos.create', compact('equipos', 'tecnicos'));
    }

    public function store(Request $request)
    {
        if (session('rol') === 'Tecnico') {
            $request->merge(['ID_Tecnico' => session('id_user')]);
        }

        $request->validate([
            'ID_Equipo'        => 'required|exists:Equipos,ID_Equipo',
            'ID_Tecnico'       => 'required|exists:Users,ID_User',
            'Fecha_Programada' => 'required|date|after_or_equal:today',
        ], [
            'ID_Equipo.required'              => 'Selecciona un equipo.',
            'ID_Tecnico.required'             => 'Selecciona un técnico.',
            'Fecha_Programada.required'       => 'La fecha es obligatoria.',
            'Fecha_Programada.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
        ]);

        $idEstado = DB::table('Catalogo_EstadoMantenimiento')
                    ->where('Nombre_EstadoMantenimiento', 'Programado')
                    ->value('ID_EstadoMantenimiento');

        // insertGetId para obtener el ID recién creado
        $idMantenimiento = DB::table('Mantenimientos')->insertGetId([
            'ID_Equipo'              => $request->ID_Equipo,
            'ID_Tecnico'             => $request->ID_Tecnico,
            'Fecha_Programada'       => $request->Fecha_Programada,
            'ID_EstadoMantenimiento' => $idEstado,
            'Fecha_Ingreso'          => now(),
        ]);

        // Registro inicial en historial
        DB::table('Historial_Cambios_Estado')->insert([
            'ID_Mantenimiento'   => $idMantenimiento,
            'ID_EstadoAnterior'  => $idEstado,
            'ID_EstadoNuevo'     => $idEstado,
            'ID_TecnicoAnterior' => $request->ID_Tecnico,
            'ID_TecnicoNuevo'    => $request->ID_Tecnico,
            'ID_UsuarioModifico' => session('id_user'),
            'Fecha_Cambio'       => now(),
            'Motivo_Cambio'      => 'Mantenimiento programado',
        ]);

        $ruta = session('rol') === 'Tecnico'
            ? 'dashboard.tecnico'
            : 'mantenimientos.index';

        return redirect()->route($ruta)
                        ->with('success', 'Mantenimiento programado correctamente.');
    }

    public function misAsignaciones()
    {
        $asignaciones = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Catalogo_EstadoMantenimiento',
                'Mantenimientos.ID_EstadoMantenimiento', '=',
                'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', session('id_user'))
            ->whereIn('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                    ['Programado', 'Reprogramado'])   // ← solo pendientes
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento',
                'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento'
            )
            ->orderByRaw("CASE Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento
                WHEN 'Programado'   THEN 1
                WHEN 'Reprogramado' THEN 2
                ELSE 3 END")
            ->orderBy('Mantenimientos.Fecha_Programada')
            ->get();

        return view('mantenimientos.mis-asignaciones', compact('asignaciones'));
    }

    public function cambiarEstado(Request $request, Mantenimiento $mantenimiento)
    {
        $nuevoEstado = $request->estado;
        $rol         = session('rol');

        $estadosCoordinador = ['Reprogramado', 'Cancelado'];
        $estadosTecnico     = ['Completado'];

        if ($rol === 'Coordinador' && !in_array($nuevoEstado, $estadosCoordinador)) {
            return back()->withErrors(['estado' => 'Acción no permitida.']);
        }

        if ($rol === 'Tecnico') {
            if (!in_array($nuevoEstado, $estadosTecnico)) {
                return back()->withErrors(['estado' => 'Acción no permitida.']);
            }
            if ($mantenimiento->ID_Tecnico !== session('id_user')) {
                abort(403);
            }
        }

        $estadoActual = DB::table('Catalogo_EstadoMantenimiento')
            ->where('ID_EstadoMantenimiento', $mantenimiento->ID_EstadoMantenimiento)
            ->value('Nombre_EstadoMantenimiento');

        if (in_array($estadoActual, ['Completado', 'Cancelado'])) {
            return back()->withErrors(['estado' => 'Este mantenimiento ya no puede modificarse.']);
        }

        $idNuevoEstado = DB::table('Catalogo_EstadoMantenimiento')
            ->where('Nombre_EstadoMantenimiento', $nuevoEstado)
            ->value('ID_EstadoMantenimiento');

        $data = ['ID_EstadoMantenimiento' => $idNuevoEstado];

        if ($nuevoEstado === 'Reprogramado') {
            $request->validate([
                'Fecha_Programada' => 'required|date|after_or_equal:today',
            ]);
            $data['Fecha_Programada']     = $request->Fecha_Programada;
            $data['Fecha_Reprogramacion'] = now();
        }

        if ($nuevoEstado === 'Completado') {
            $request->validate([
                'Accion_Realizada'       => 'required|string|max:500',
                'Observaciones_Tecnicas' => 'nullable|string|max:1000',
            ], [
                'Accion_Realizada.required' => 'Debes describir la acción realizada.',
                'Accion_Realizada.max'      => 'Máximo 500 caracteres.',
            ]);

            $data['Fecha_Cierre'] = now();

            DB::table('Mantenimiento_Detalle')->insert([
                'ID_Mantenimiento'       => $mantenimiento->ID_Mantenimiento,
                'ID_TecnicoIntervino'    => session('id_user'),
                'Fecha_Registro'         => now(),
                'Accion_Realizada'       => $request->Accion_Realizada,
                'Observaciones_Tecnicas' => $request->Observaciones_Tecnicas,
            ]);
        }

        // Registrar cambio en historial
        DB::table('Historial_Cambios_Estado')->insert([
            'ID_Mantenimiento'   => $mantenimiento->ID_Mantenimiento,
            'ID_EstadoAnterior'  => $mantenimiento->ID_EstadoMantenimiento,
            'ID_EstadoNuevo'     => $idNuevoEstado,
            'ID_TecnicoAnterior' => $mantenimiento->ID_Tecnico,
            'ID_TecnicoNuevo'    => $mantenimiento->ID_Tecnico,
            'ID_UsuarioModifico' => session('id_user'),
            'Fecha_Cambio'       => now(),
            'Motivo_Cambio'      => $request->Motivo_Cambio ?? null,
        ]);

        DB::table('Mantenimientos')
            ->where('ID_Mantenimiento', $mantenimiento->ID_Mantenimiento)
            ->update($data);

        $ruta = $rol === 'Tecnico'
            ? 'mantenimientos.mis-asignaciones'
            : 'mantenimientos.index';

        return redirect()->route($ruta)
                        ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function historialCambios(Mantenimiento $mantenimiento)
    {
        $historial = DB::table('Historial_Cambios_Estado')
            ->join('Catalogo_EstadoMantenimiento as EA',
                'Historial_Cambios_Estado.ID_EstadoAnterior', '=', 'EA.ID_EstadoMantenimiento')
            ->join('Catalogo_EstadoMantenimiento as EN',
                'Historial_Cambios_Estado.ID_EstadoNuevo', '=', 'EN.ID_EstadoMantenimiento')
            ->join('Users',
                'Historial_Cambios_Estado.ID_UsuarioModifico', '=', 'Users.ID_User')
            ->where('Historial_Cambios_Estado.ID_Mantenimiento', $mantenimiento->ID_Mantenimiento)
            ->orderBy('Historial_Cambios_Estado.Fecha_Cambio')
            ->select(
                'EA.Nombre_EstadoMantenimiento as Estado_Anterior',
                'EN.Nombre_EstadoMantenimiento as Estado_Nuevo',
                'Users.Usuario as Modificado_Por',
                'Historial_Cambios_Estado.Fecha_Cambio',
                'Historial_Cambios_Estado.Motivo_Cambio'
            )
            ->get();

        return response()->json($historial);
    }

    public function auditoria(Request $request)
    {
        $query = DB::table('Historial_Cambios_Estado')
            ->join('Mantenimientos',
                'Historial_Cambios_Estado.ID_Mantenimiento', '=', 'Mantenimientos.ID_Mantenimiento')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Catalogo_EstadoMantenimiento as EA',
                'Historial_Cambios_Estado.ID_EstadoAnterior', '=', 'EA.ID_EstadoMantenimiento')
            ->join('Catalogo_EstadoMantenimiento as EN',
                'Historial_Cambios_Estado.ID_EstadoNuevo', '=', 'EN.ID_EstadoMantenimiento')
            ->join('Users',
                'Historial_Cambios_Estado.ID_UsuarioModifico', '=', 'Users.ID_User');

        if ($request->filled('tecnico_id')) {
            $query->where('Mantenimientos.ID_Tecnico', $request->tecnico_id);
        }
        if ($request->filled('estado_nuevo')) {
            $query->where('EN.Nombre_EstadoMantenimiento', $request->estado_nuevo);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('Historial_Cambios_Estado.Fecha_Cambio', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('Historial_Cambios_Estado.Fecha_Cambio', '<=', $request->fecha_hasta);
        }

        $cambios = $query
            ->orderBy('Historial_Cambios_Estado.Fecha_Cambio', 'desc')
            ->select(
                'Historial_Cambios_Estado.*',
                'Equipos.Codigo_Inventario',
                'EA.Nombre_EstadoMantenimiento as Estado_Anterior',
                'EN.Nombre_EstadoMantenimiento as Estado_Nuevo',
                'Users.Usuario as Modificado_Por'
            )
            ->paginate(20)
            ->withQueryString();

        $tecnicos = DB::table('Users')
            ->join('Roles_User', 'Users.ID_Rol', '=', 'Roles_User.ID_Rol')
            ->where('Roles_User.Rol', 'Tecnico')
            ->select('Users.ID_User', 'Users.Usuario')
            ->get();

        return view('mantenimientos.auditoria', compact('cambios', 'tecnicos'));
    }

    public function historialCierres(Request $request)
    {
        $query = DB::table('Mantenimientos')
            ->join('Equipos', 'Mantenimientos.ID_Equipo', '=', 'Equipos.ID_Equipo')
            ->join('Tipos_Equipo', 'Equipos.ID_Tipo', '=', 'Tipos_Equipo.ID_Tipo')
            ->join('Ubicacion', 'Equipos.ID_Ubicacion', '=', 'Ubicacion.ID_Ubicacion')
            ->join('Catalogo_EstadoMantenimiento',
                'Mantenimientos.ID_EstadoMantenimiento', '=',
                'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Tecnico', session('id_user'))
            ->whereIn('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                    ['Completado', 'Cancelado']);

        if ($request->filled('estado')) {
            $query->where('Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento',
                        $request->estado);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('Mantenimientos.Fecha_Cierre', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('Mantenimientos.Fecha_Cierre', '<=', $request->fecha_hasta);
        }

        $cierres = $query
            ->orderBy('Mantenimientos.Fecha_Cierre', 'desc')
            ->select(
                'Mantenimientos.*',
                'Equipos.Codigo_Inventario',
                'Tipos_Equipo.Nombre_Tipo as Tipo',
                'Ubicacion.NombreSede as Ubicacion',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento'
            )
            ->get();

        // Contadores para tarjetas resumen
        $totalCierres    = $cierres->count();
        $totalCompletado = $cierres->where('Estado_Mantenimiento', 'Completado')->count();
        $totalCancelado  = $cierres->where('Estado_Mantenimiento', 'Cancelado')->count();

        // Detalles por mantenimiento para el modal
        $detalles = DB::table('Mantenimiento_Detalle')
            ->whereIn('ID_Mantenimiento', $cierres->pluck('ID_Mantenimiento'))
            ->orderBy('Fecha_Registro', 'desc')
            ->get()
            ->groupBy('ID_Mantenimiento');

        return view('mantenimientos.historial-cierres', compact(
            'cierres', 'totalCierres', 'totalCompletado', 'totalCancelado', 'detalles'
        ));
    }

    public function show(Mantenimiento $mantenimiento) {}
    public function edit(Mantenimiento $mantenimiento) {}
    public function update(Request $request, Mantenimiento $mantenimiento) {}
    public function destroy(Mantenimiento $mantenimiento) {}
}