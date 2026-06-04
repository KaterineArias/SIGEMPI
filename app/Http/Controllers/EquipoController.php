<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        // TODO: implementar listado con filtros/paginación
        $equipos = collect(); // reemplazar con Equipo::all() o query builder

        return view('equipos.index', compact('equipos'));
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
}