<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('usuario')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario'  => 'required|string',
            'password' => 'required|string',
        ], [
            'usuario.required'  => 'El usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 1. Buscamos el usuario en la tabla definitiva
        $user = DB::table('Users')
            ->where('Usuario', $request->usuario)
            ->first();

        // 2. Validación directa compatible con las credenciales de tu base de datos
        if (!$user || $request->password !== $user->Password_Hash) {
            return back()
                ->withInput(['usuario' => $request->usuario])
                ->withErrors(['login' => 'Usuario o contraseña incorrectos.']);
        }

        // 3. Mapeamos el ID_Rol numérico a la palabra de sesión esperada por tus middlewares
        $nombreRol = match ((int)$user->ID_Rol) {
            1 => 'Coordinador',
            2 => 'Tecnico',
            default => 'Invitado'
        };

        // 4. Inyectamos las variables de control en la sesión global de Laravel
        session([
            'usuario'  => $user->Usuario,
            'rol'      => $nombreRol, // Guarda 'Tecnico' o 'Coordinador' de manera compatible
            'id_user'  => $user->ID_User,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}