@extends('layouts.app')
@section('title', 'Nueva contraseña')

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
                <h2>Nueva contraseña</h2>
                <p>Ingresa y confirma tu nueva contraseña.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:var(--space-5)">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.post', $token) }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="password">Nueva contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password"
                               class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
                               placeholder="Mínimo 8 caracteres" autofocus>
                        <button type="button" class="toggle-pass"
                                onclick="togglePass('password','eye1')"
                                aria-label="Mostrar contraseña">
                            <svg id="eye1" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-input"
                               placeholder="Repite la contraseña">
                        <button type="button" class="toggle-pass"
                                onclick="togglePass('password_confirmation','eye2')"
                                aria-label="Mostrar contraseña">
                            <svg id="eye2" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    Guardar nueva contraseña
                </button>
            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
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