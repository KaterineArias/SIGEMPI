<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RolUser;
use App\Models\EstadoUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Listado con búsqueda y filtros
    public function index(Request $request)
    {
        $query = User::with(['rol', 'estadoUsuario']);

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function($sub) use ($q) {
                $sub->where('Usuario', 'like', "%{$q}%")
                    ->orWhere('Correo_User', 'like', "%{$q}%");
            });
        }

        if ($request->filled('rol')) {
            $query->where('ID_Rol', $request->rol);
        }

        if ($request->filled('estado')) {
            $query->where('ID_EstadoUsuario', $request->estado);
        }

        $usuarios   = $query->orderBy('Fecha_CreacionUser', 'desc')->get();
        $roles      = RolUser::all();
        $estados    = EstadoUsuario::all();
        $idActivo   = EstadoUsuario::where('Estado', 'Activo')->value('ID_EstadoUsuario');
        $idInactivo = EstadoUsuario::where('Estado', 'Inactivo')->value('ID_EstadoUsuario');

        return view('usuarios.index', compact('usuarios', 'roles', 'estados', 'idActivo', 'idInactivo'));
    }

    // Formulario crear
    public function create()
    {
        $roles = RolUser::all();
        return view('usuarios.create', compact('roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'usuario'  => 'required|string|max:50|unique:Users,Usuario',
            'correo'   => 'required|email|max:100|unique:Users,Correo_User',
            'password' => 'required|string|min:8|confirmed',
            'id_rol'   => 'required|exists:Roles_User,ID_Rol',
        ], [
            'usuario.required'   => 'El usuario es obligatorio.',
            'usuario.unique'     => 'Ese nombre de usuario ya está en uso.',
            'correo.required'    => 'El correo es obligatorio.',
            'correo.email'       => 'Ingresa un correo válido.',
            'correo.unique'      => 'Ese correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'Mínimo 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'id_rol.required'    => 'Selecciona un rol.',
            'id_rol.exists'      => 'El rol seleccionado no es válido.',
        ]);

        $estadoActivo = EstadoUsuario::where('Estado', 'Activo')
                                     ->value('ID_EstadoUsuario');

        User::create([
            'Usuario'          => $request->usuario,
            'Correo_User'      => $request->correo,
            'Password_Hash'    => Hash::make($request->password),
            'ID_Rol'           => $request->id_rol,
            'ID_EstadoUsuario' => $estadoActivo,
            'Primer_Login'     => true,
        ]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    // Formulario editar
    public function edit(User $usuario)
    {
        $roles   = RolUser::all();
        $estados = EstadoUsuario::all();
        return view('usuarios.edit', compact('usuario', 'roles', 'estados'));
    }

    // Actualizar usuario
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'usuario' => "required|string|max:50|unique:Users,Usuario,{$usuario->ID_User},ID_User",
            'correo'  => "required|email|max:100|unique:Users,Correo_User,{$usuario->ID_User},ID_User",
            'id_rol'  => 'required|exists:Roles_User,ID_Rol',
            'id_estado' => 'required|exists:Estado_Usuario,ID_EstadoUsuario',
        ], [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique'   => 'Ese nombre de usuario ya está en uso.',
            'correo.required'  => 'El correo es obligatorio.',
            'correo.email'     => 'Ingresa un correo válido.',
            'correo.unique'    => 'Ese correo ya está registrado.',
            'id_rol.required'  => 'Selecciona un rol.',
            'id_estado.required' => 'Selecciona un estado.',
        ]);

        $coordinadorId = session('id_user');

        // Auditoría cambio de rol
        if ($usuario->ID_Rol != $request->id_rol) {
            DB::table('Auditoria_CambioRol_Users')->insert([
                'ID_User'       => $usuario->ID_User,
                'ID_UserAccion' => $coordinadorId,
                'ID_RolAnterior'=> $usuario->ID_Rol,
                'ID_RolNuevo'   => $request->id_rol,
                'Fecha'         => now(),
            ]);
        }

        // Auditoría cambio de estado
        if ($usuario->ID_EstadoUsuario != $request->id_estado) {
            DB::table('Auditoria_CambioEstado_Users')->insert([
                'ID_User'          => $usuario->ID_User,
                'ID_UserAccion'    => $coordinadorId,
                'ID_EstadoAnterior'=> $usuario->ID_EstadoUsuario,
                'ID_EstadoNuevo'   => $request->id_estado,
                'Fecha'            => now(),
            ]);
        }

        // Auditoría cambio de nombre de usuario
        if ($usuario->Usuario !== $request->usuario) {
            DB::table('Auditoria_Actualizacion_Users')->insert([
                'ID_User'       => $usuario->ID_User,
                'ID_UserAccion' => $coordinadorId,
                'ValorAnterior' => $usuario->Usuario,
                'ValorNuevo'    => $request->usuario,
                'Fecha'         => now(),
            ]);
        }

        // Guardar cambios
        $usuario->Usuario          = $request->usuario;
        $usuario->Correo_User      = $request->correo;
        $usuario->ID_Rol           = $request->id_rol;
        $usuario->ID_EstadoUsuario = $request->id_estado;
        $usuario->save();

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$usuario->Usuario} actualizado correctamente.");
    }

    // Cambiar estado desde el index (Activo ↔ Inactivo)
    public function cambiarEstado(Request $request, User $usuario)
    {
        $request->validate([
            'id_estado' => 'required|exists:Estado_Usuario,ID_EstadoUsuario',
        ]);

        if ($usuario->ID_User === session('id_user')) {
            return back()->withErrors(['estado' => 'No puedes cambiar tu propio estado.']);
        }

        $coordinadorId = session('id_user');

        // Auditoría
        DB::table('Auditoria_CambioEstado_Users')->insert([
            'ID_User'          => $usuario->ID_User,
            'ID_UserAccion'    => $coordinadorId,
            'ID_EstadoAnterior'=> $usuario->ID_EstadoUsuario,
            'ID_EstadoNuevo'   => $request->id_estado,
            'Fecha'            => now(),
        ]);

        $usuario->ID_EstadoUsuario = $request->id_estado;
        $usuario->save();

        $nuevoEstado = EstadoUsuario::find($request->id_estado)->Estado;

        return redirect()->route('usuarios.index')
                         ->with('success', "Usuario {$usuario->Usuario} marcado como {$nuevoEstado}.");
    }

    // Ver formulario de perfil
    public function perfilForm()
    {
        $usuario = User::find(session('id_user'));
        return view('perfil.index', compact('usuario'));
    }

    //  Guardar cambios de perfil 
    public function perfilUpdate(Request $request)
    {
        $request->validate([
            'password_actual'   => 'required|string',
            'password'          => 'required|string|min:8|confirmed',
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
            'password.required'        => 'La nueva contraseña es obligatoria.',
            'password.min'             => 'Mínimo 8 caracteres.',
            'password.confirmed'       => 'Las contraseñas no coinciden.',
        ]);

        $usuario = User::find(session('id_user'));

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->password_actual, $usuario->Password_Hash)) {
            return back()
                ->withErrors(['password_actual' => 'La contraseña actual es incorrecta.'])
                ->withInput();
        }

        $usuario->Password_Hash = Hash::make($request->password);
        $usuario->Primer_Login  = false;
        $usuario->Password_Changed_At = now();
        $usuario->save();

        return redirect()->route('perfil.form')
                        ->with('success', 'Contraseña actualizada correctamente.');
    }

    // Historial consolidado
    public function historial(User $usuario)
    {
        // Historial de cambios de estado
        $historialEstado = DB::table('Auditoria_CambioEstado_Users as a')
            ->join('Estado_Usuario as ea', 'a.ID_EstadoAnterior', '=', 'ea.ID_EstadoUsuario')
            ->join('Estado_Usuario as en', 'a.ID_EstadoNuevo', '=', 'en.ID_EstadoUsuario')
            ->join('Users as u', 'a.ID_UserAccion', '=', 'u.ID_User')
            ->where('a.ID_User', $usuario->ID_User)
            ->orderBy('a.Fecha', 'desc')
            ->select(
                'a.Fecha',
                'ea.Estado as EstadoAnterior',
                'en.Estado as EstadoNuevo',
                'u.Usuario as RealizadoPor'
            )
            ->get();

        // Historial de cambios de rol
        $historialRol = DB::table('Auditoria_CambioRol_Users as a')
            ->join('Roles_User as ra', 'a.ID_RolAnterior', '=', 'ra.ID_Rol')
            ->join('Roles_User as rn', 'a.ID_RolNuevo', '=', 'rn.ID_Rol')
            ->join('Users as u', 'a.ID_UserAccion', '=', 'u.ID_User')
            ->where('a.ID_User', $usuario->ID_User)
            ->orderBy('a.Fecha', 'desc')
            ->select(
                'a.Fecha',
                'ra.Rol as RolAnterior',
                'rn.Rol as RolNuevo',
                'u.Usuario as RealizadoPor'
            )
            ->get();

        // Historial de cambios de nombre de usuario
        $historialUsuario = DB::table('Auditoria_Actualizacion_Users as a')
            ->join('Users as u', 'a.ID_UserAccion', '=', 'u.ID_User')
            ->where('a.ID_User', $usuario->ID_User)
            ->orderBy('a.Fecha', 'desc')
            ->select(
                'a.Fecha',
                'a.ValorAnterior',
                'a.ValorNuevo',
                'u.Usuario as RealizadoPor'
            )
            ->get();

        return view('usuarios.historial', compact(
            'usuario',
            'historialEstado',
            'historialRol',
            'historialUsuario'
        ));
    }
}