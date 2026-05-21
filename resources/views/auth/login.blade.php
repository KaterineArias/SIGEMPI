@extends('layouts.app')
@section('title', 'Iniciar sesión')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-wrapper">

    {{-- Panel izquierdo --}}
    <div class="login-brand">
        <div class="login-brand-accent"></div>
        <div class="login-brand-logo" style="flex-direction:column;align-items:center;gap:var(--space-4)">
            <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="100" height="100">
            <h1>SIGEMPI</h1>
        </div>
        <p>Sistema de Gestión de Mantenimiento del Parque Informático</p>
        <div class="login-dots">
            <span></span><span></span><span></span>
        </div>
    </div>

    {{-- Panel derecho --}}
    <div class="login-form-panel">
        <div class="login-box">

            <div class="login-header">
                <h2>Bienvenido/a</h2>
                <p>Ingresa tus credenciales para continuar</p>
            </div>

            {{-- Error general --}}
            @if ($errors->has('login'))
                <div class="alert alert-error">
                    <strong>{{ $errors->first('login') }}</strong>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                {{-- Usuario --}}
                <div class="form-field">
                    <label class="form-label" for="usuario">Usuario</label>
                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        value="{{ old('usuario') }}"
                        class="form-input {{ $errors->has('usuario') ? 'input-error' : '' }}"
                        placeholder="Tu nombre de usuario"
                        autocomplete="username"
                        autofocus
                        required
                    >
                    @error('usuario')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div class="form-field">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                            placeholder="Tu contraseña"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pass" onclick="togglePassword()" aria-label="Mostrar contraseña">
                            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    Iniciar sesión
                </button>
            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
            <line x1="1" y1="1" x2="23" y2="23"/>`;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>`;
    }
}
</script>
@endpush