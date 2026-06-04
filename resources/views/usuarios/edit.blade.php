@extends('layouts.app')
@section('title', 'Editar usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Editar usuario</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('usuarios.historial', $usuario->ID_User) }}" class="btn btn-secondary">
                    Ver historial
                </a>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Editar — {{ $usuario->Usuario }}</h1>
                <p>Modifica usuario, correo, rol o estado. La contraseña no se puede editar aquí.</p>
            </div>

            <div class="form-card" style="max-width:500px">

                @if($errors->any())
                    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;
                                padding:10px 14px;border-radius:8px;font-size:13px;">
                        <strong>Corrige los siguientes errores:</strong>
                        <ul style="margin:6px 0 0 16px;padding:0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('usuarios.update', $usuario->ID_User) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-field">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario"
                               value="{{ old('usuario', $usuario->Usuario) }}"
                               maxlength="50" required>
                        @error('usuario')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                               value="{{ old('correo', $usuario->Correo_User) }}"
                               maxlength="100" required>
                        @error('correo')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="id_rol">Rol</label>
                        <select id="id_rol" name="id_rol" required>
                            <option value="">— Selecciona un rol —</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->ID_Rol }}"
                                    {{ old('id_rol', $usuario->ID_Rol) == $rol->ID_Rol ? 'selected' : '' }}>
                                    {{ $rol->Rol }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rol')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="id_estado">Estado</label>
                        <select id="id_estado" name="id_estado" required
                            {{ $usuario->ID_User === session('id_user') ? 'disabled' : '' }}>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->ID_EstadoUsuario }}"
                                    {{ old('id_estado', $usuario->ID_EstadoUsuario) == $estado->ID_EstadoUsuario ? 'selected' : '' }}>
                                    {{ $estado->Estado }}
                                </option>
                            @endforeach
                        </select>
                        @if($usuario->ID_User === session('id_user'))
                            <span style="font-size:11px;color:#6b7280">
                                No puedes cambiar tu propio estado.
                            </span>
                        @endif
                        @error('id_estado')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Si el select está disabled, el valor no se envía — lo enviamos oculto --}}
                    @if($usuario->ID_User === session('id_user'))
                        <input type="hidden" name="id_estado" value="{{ $usuario->ID_EstadoUsuario }}">
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const t = document.querySelector('[data-theme-toggle]');
    const r = document.documentElement;
    let d = localStorage.getItem('theme') || 'light';
    r.setAttribute('data-theme', d);
    if (t) t.addEventListener('click', () => {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();
</script>
@endpush