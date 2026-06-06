<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\TipoEquipo;
use App\Models\EstadoEquipo;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        if (session('rol') === 'Tecnico') {
            return $this->indexTecnico();
        }

        $q      = $request->q;
        $estado = $request->estado;
        $tipo   = $request->tipo;

        $equipos = Equipo::with(['tipo', 'estado', 'ubicacion'])
            ->when($q, function ($query) use ($q) {
                $query->where('Codigo_Inventario', 'like', "%{$q}%")
                    ->orWhere('Marca', 'like', "%{$q}%")
                    ->orWhere('Modelo', 'like', "%{$q}%")
                    ->orWhereHas('ubicacion', fn($q2) => $q2->where('NombreSede', 'like', "%{$q}%"))
                    ->orWhereHas('tipo', fn($q2) => $q2->where('Nombre_Tipo', 'like', "%{$q}%"));
            })
            ->when($estado, function ($query) use ($estado) {
                $query->whereHas('estado', fn($q2) => $q2->where('Estado', $estado));
            })
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('ID_Tipo', $tipo);
            })
            ->orderBy('Codigo_Inventario')
            ->paginate(15)
            ->withQueryString();

        $tipos   = TipoEquipo::orderBy('Nombre_Tipo')->get();
        $estados = EstadoEquipo::orderBy('Estado')->get();

        return view('equipos.index', compact('equipos', 'tipos', 'estados'));
    }

    public function create()
    {
        $tipos     = TipoEquipo::orderBy('Nombre_Tipo')->get();
        $estados   = EstadoEquipo::orderBy('Estado')->get();
        $ubicaciones = Ubicacion::orderBy('NombreSede')->get();

        return view('equipos.create', compact('tipos', 'estados', 'ubicaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Codigo_Inventario' => 'required|string|max:50|unique:Equipos,Codigo_Inventario',
            'ID_Tipo'           => 'required|exists:Tipos_Equipo,ID_Tipo',
            'ID_Estado'         => 'required|exists:Estado_Equipo,ID_Estado',
            'ID_Ubicacion'      => 'required|exists:Ubicacion,ID_Ubicacion',
            'Marca'             => 'nullable|string|max:100',
            'Modelo'            => 'nullable|string|max:100',
        ], [
            'Codigo_Inventario.required' => 'El código de inventario es obligatorio.',
            'Codigo_Inventario.unique'   => 'Ya existe un equipo con ese código.',
            'ID_Tipo.required'           => 'Selecciona un tipo de equipo.',
            'ID_Estado.required'         => 'Selecciona un estado.',
            'ID_Ubicacion.required'      => 'Selecciona una ubicación.',
        ]);

        DB::table('Equipos')->insert([
            'Codigo_Inventario' => strtoupper(trim($request->Codigo_Inventario)),
            'ID_Tipo'           => $request->ID_Tipo,
            'ID_Estado'         => $request->ID_Estado,
            'ID_Ubicacion'      => $request->ID_Ubicacion,
            'Marca'             => $request->Marca  ? trim($request->Marca)  : null,
            'Modelo'            => $request->Modelo ? trim($request->Modelo) : null,
        ]);

        return redirect()->route('equipos.index')
                        ->with('success', 'Equipo registrado correctamente.');
    }

    public function show(string $id)
    {
        // TODO: mostrar detalle del equipo
        return view('equipos.show');
    }

    public function edit(string $id)
    {
        $equipo      = DB::table('Equipos')->where('ID_Equipo', $id)->first();

        if (!$equipo) abort(404);

        $tipos       = TipoEquipo::orderBy('Nombre_Tipo')->get();
        $estados     = EstadoEquipo::orderBy('Estado')->get();
        $ubicaciones = Ubicacion::orderBy('NombreSede')->get();

        return view('equipos.edit', compact('equipo', 'tipos', 'estados', 'ubicaciones'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'Codigo_Inventario' => 'required|string|max:50|unique:Equipos,Codigo_Inventario,' . $id . ',ID_Equipo',
            'ID_Tipo'           => 'required|exists:Tipos_Equipo,ID_Tipo',
            'ID_Estado'         => 'required|exists:Estado_Equipo,ID_Estado',
            'ID_Ubicacion'      => 'required|exists:Ubicacion,ID_Ubicacion',
            'Marca'             => 'nullable|string|max:100',
            'Modelo'            => 'nullable|string|max:100',
        ], [
            'Codigo_Inventario.required' => 'El código de inventario es obligatorio.',
            'Codigo_Inventario.unique'   => 'Ya existe otro equipo con ese código.',
            'ID_Tipo.required'           => 'Selecciona un tipo de equipo.',
            'ID_Estado.required'         => 'Selecciona un estado.',
            'ID_Ubicacion.required'      => 'Selecciona una ubicación.',
        ]);

        DB::table('Equipos')->where('ID_Equipo', $id)->update([
            'Codigo_Inventario' => strtoupper(trim($request->Codigo_Inventario)),
            'ID_Tipo'           => $request->ID_Tipo,
            'ID_Estado'         => $request->ID_Estado,
            'ID_Ubicacion'      => $request->ID_Ubicacion,
            'Marca'             => $request->Marca  ? trim($request->Marca)  : null,
            'Modelo'            => $request->Modelo ? trim($request->Modelo) : null,
        ]);

        return redirect()->route('equipos.index')
                        ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        // Dar de baja = cambiar estado, no borrar el registro
        $idBaja = DB::table('Estado_Equipo')
                    ->where('Estado', 'De Baja')
                    ->value('ID_Estado');

        DB::table('Equipos')->where('ID_Equipo', $id)->update([
            'ID_Estado' => $idBaja,
        ]);

        return redirect()->route('equipos.index')
                        ->with('success', 'Equipo dado de baja correctamente.');
    }

    // Equipos (técnico)
    public function indexTecnico()
    {
        $q = request('q');

        $equipos = Equipo::with(['tipo', 'estado', 'ubicacion'])
            ->when($q, function ($query) use ($q) {
                $query->where('Codigo_Inventario', 'like', "%{$q}%")
                    ->orWhere('Marca', 'like', "%{$q}%")
                    ->orWhere('Modelo', 'like', "%{$q}%")
                    ->orWhereHas('ubicacion', function ($query) use ($q) {
                        $query->where('NombreSede', 'like', "%{$q}%");
                    })
                    ->orWhereHas('tipo', function ($query) use ($q) {
                        $query->where('Nombre_Tipo', 'like', "%{$q}%");
                    });
            })
            ->orderBy('Codigo_Inventario')
            ->paginate(15)
            ->withQueryString(); // ← mantiene el ?q= al paginar

        return view('equipos.tecnico.index', compact('equipos'));
    }

    public function historial($id)
    {
        $equipo = Equipo::with(['tipo', 'estado', 'ubicacion'])->findOrFail($id);

        $mantenimientos = DB::table('Mantenimientos')
            ->join('Users', 'Mantenimientos.ID_Tecnico', '=', 'Users.ID_User')
            ->join('Catalogo_EstadoMantenimiento',
                'Mantenimientos.ID_EstadoMantenimiento', '=',
                'Catalogo_EstadoMantenimiento.ID_EstadoMantenimiento')
            ->where('Mantenimientos.ID_Equipo', $id)
            ->orderBy('Mantenimientos.Fecha_Programada', 'desc')
            ->select(
                'Mantenimientos.*',
                'Users.Usuario as Tecnico',
                'Catalogo_EstadoMantenimiento.Nombre_EstadoMantenimiento as Estado_Mantenimiento'
            )
            ->get();

        return view('equipos.tecnico.historial', compact('equipo', 'mantenimientos'));
    }
}