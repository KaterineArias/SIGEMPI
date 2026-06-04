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
    public function index()
    {
        if (session('rol') === 'Tecnico') {
            return $this->indexTecnico();
        }

        // Vista del coordinador — la que ya tenías
        return view('equipos.index'); // o lo que tengas para el coordinador
    }

    public function create()
    {
        // TODO: cargar catálogos necesarios (tipos, ubicaciones, estados, etc.)
        return view('equipos.create');
    }

    public function store(Request $request)
    {
        // TODO: validar y guardar equipo
        return redirect()->route('equipos.index')
                         ->with('success', 'Equipo creado. [pendiente implementar]');
    }

    public function show(string $id)
    {
        // TODO: mostrar detalle del equipo
        return view('equipos.show');
    }

    public function edit(string $id)
    {
        // TODO: cargar equipo y catálogos para edición
        return view('equipos.edit');
    }

    public function update(Request $request, string $id)
    {
        // TODO: validar y actualizar equipo
        return redirect()->route('equipos.index')
                         ->with('success', 'Equipo actualizado. [pendiente implementar]');
    }

    public function destroy(string $id)
    {
        // TODO: eliminar o desactivar equipo
        return redirect()->route('equipos.index')
                         ->with('success', 'Equipo eliminado. [pendiente implementar]');
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