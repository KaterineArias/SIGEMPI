<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Login
    public function showLogin()
    {
        if (session('usuario')) {
            return session('rol') === 'Coordinador'
                ? redirect()->route('dashboard.coordinador')
                : redirect()->route('dashboard.tecnico');
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

        $user = User::with(['rol', 'estadoUsuario'])
                    ->where('Usuario', $request->usuario)
                    ->first();

        // Credenciales incorrectas
        if (!$user || !Hash::check($request->password, $user->Password_Hash)) {
            return back()
                ->withInput(['usuario' => $request->usuario])
                ->withErrors(['login' => 'Usuario o contraseña incorrectos.']);
        }

        // Verificar que el usuario esté Activo
        if (!$user->estaActivo()) {
            return back()
                ->withInput(['usuario' => $request->usuario])
                ->withErrors(['login' => 'Tu cuenta está inactiva. Contacta al coordinador.']);
        }

        session([
            'usuario'  => $user->Usuario,
            'rol'      => $user->rol->Rol,
            'id_user'  => $user->ID_User,
            'id_rol'   => $user->ID_Rol,
            'correo'   => $user->Correo_User,
        ]);

        return $user->esCoordinador()
            ? redirect()->route('dashboard.coordinador')
            : redirect()->route('dashboard.tecnico');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }

    // Reset de contraseña
    public function showSolicitarReset()
    {
        return view('auth.solicitar-reset');
    }

    public function solicitarReset(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email'    => 'Ingresa un correo válido.',
        ]);

        $user = User::where('Correo_User', $request->correo)->first();

        // Si no existe el correo, mostramos el mismo mensaje para no revelar qué correos están registrados
        if (!$user) {
            return back()->with('reset_link', null)
                         ->with('not_found', true);
        }

        // Invalidar tokens anteriores del usuario
        PasswordResetToken::where('ID_User', $user->ID_User)
                          ->where('usado', false)
                          ->update(['usado' => true]);

        // Generar token nuevo — válido por 30 minutos
        $token = Str::random(64);

        PasswordResetToken::create([
            'ID_User'    => $user->ID_User,
            'token'      => $token,
            'usado'      => false,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $link = route('password.reset.form', ['token' => $token]);

        // Como no hay servidor de correo, mostramos el link en pantalla
        return back()->with('reset_link', $link)
                     ->with('reset_usuario', $user->Usuario);
    }

    public function showResetForm(string $token)
    {
        $tokenRecord = PasswordResetToken::where('token', $token)->first();

        if (!$tokenRecord || !$tokenRecord->esValido()) {
            return redirect()->route('login')
                             ->withErrors(['login' => 'El enlace de recuperación es inválido o ya fue usado.']);
        }

        return view('auth.reset-password', compact('token'));
    }

    public function resetPassword(Request $request, string $token)
    {
        $tokenRecord = PasswordResetToken::where('token', $token)->first();

        if (!$tokenRecord || !$tokenRecord->esValido()) {
            return redirect()->route('login')
                             ->withErrors(['login' => 'El enlace de recuperación es inválido o ya fue usado.']);
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'Mínimo 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Actualizar contraseña
        $user = User::find($tokenRecord->ID_User);
        $user->Password_Hash = Hash::make($request->password);
        $user->save();

        // Marcar token como usado — solo puede usarse una vez
        $tokenRecord->usado = true;
        $tokenRecord->save();

        return redirect()->route('login')
                         ->with('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }
}