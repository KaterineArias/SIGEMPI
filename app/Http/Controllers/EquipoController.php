<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipo::query();

        if ($request->filled('tipo')) {
            $query->where('Tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }

        $equipos = $query->orderBy('Codigo_Inventario')->get();

        return view('equipos.index', compact('equipos'));
    }

    public function create()
    {
        // Por implementar
    }

    public function store(Request $request)
    {
        // Por implementar
    }

    public function edit(Equipo $equipo)
    {
        // Por implementar
    }

    public function update(Request $request, Equipo $equipo)
    {
        // Por implementar
    }

    public function destroy(Equipo $equipo)
    {
        // Por implementar
    }
}