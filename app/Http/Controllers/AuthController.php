<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

        $user = DB::table('Users')
            ->where('Usuario', $request->usuario)
            ->first();

        if (!$user || !Hash::check($request->password, $user->Password_Hash)) {
            return back()
                ->withInput(['usuario' => $request->usuario])
                ->withErrors(['login' => 'Usuario o contraseña incorrectos.']);
        }

        session([
            'usuario'  => $user->Usuario,
            'rol'      => $user->Rol,
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