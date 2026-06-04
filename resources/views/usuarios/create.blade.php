@extends('layouts.app')
@section('title', 'Nuevo usuario')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Nuevo usuario</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Nuevo usuario</h1>
                <p>Completa los datos para crear la cuenta</p>
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

                <form method="POST" action="{{ route('usuarios.store') }}">
                    @csrf

                    <div class="form-field">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario"
                               value="{{ old('usuario') }}"
                               placeholder="Ej. jperez" maxlength="50" required>
                        @error('usuario')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                               value="{{ old('correo') }}"
                               placeholder="correo@institución.gob.sv" maxlength="100" required>
                        @error('correo')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña temporal</label>
                        <input type="password" id="password" name="password"
                               placeholder="Mínimo 8 caracteres" required>
                        <span style="font-size:11px;color:#6b7280">
                            El usuario deberá cambiarla en su primer inicio de sesión.
                        </span>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Repite la contraseña" required>
                    </div>

                    <div class="form-field">
                        <label for="id_rol">Rol</label>
                        <select id="id_rol" name="id_rol" required>
                            <option value="">— Selecciona un rol —</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->ID_Rol }}"
                                    {{ old('id_rol') == $rol->ID_Rol ? 'selected' : '' }}>
                                    {{ $rol->Rol }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rol')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Crear usuario</button>
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