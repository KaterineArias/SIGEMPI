<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Lista todos los usuarios
    public function index()
    {
        $usuarios = User::orderBy('Fecha_Creacion', 'desc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    // Muestra el formulario de crear
    public function create()
    {
        return view('usuarios.create');
    }

    // Guarda el nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'Usuario'   => 'required|string|max:50|unique:Users,Usuario',
            'Rol'       => 'required|in:Coordinador,Tecnico',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'Usuario'       => $request->Usuario,
            'Rol'           => $request->Rol,
            'Password_Hash' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    // Muestra el formulario de editar
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    // Actualiza el usuario
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'Usuario' => 'required|string|max:50|unique:Users,Usuario,' . $usuario->ID_User . ',ID_User',
            'Rol'     => 'required|in:Coordinador,Tecnico',
            'password'=> 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'Usuario' => $request->Usuario,
            'Rol'     => $request->Rol,
        ];

        if ($request->filled('password')) {
            $data['Password_Hash'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Elimina el usuario
    public function destroy(User $usuario)
    {
        if ($usuario->Usuario === session('usuario')) {
        return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}