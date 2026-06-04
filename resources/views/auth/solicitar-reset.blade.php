@extends('layouts.app')
@section('title', 'Recuperar contraseña')

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
                <h2>Recuperar contraseña</h2>
                <p>Ingresa tu correo</p>
            </div>

            {{-- Enlace generado exitosamente --}}
            @if(session('reset_link'))
                <div style="background:#dcfce7;border:1px solid #86efac;border-radius:var(--radius-md);padding:var(--space-4);margin-bottom:var(--space-6)">
                    <p style="font-size:var(--text-sm);color:#166534;font-weight:600;margin-bottom:var(--space-2)">
                        Enlace generado para <strong>{{ session('reset_usuario') }}</strong>
                    </p>
                    <p style="font-size:var(--text-xs);color:#166534;margin-bottom:var(--space-3)">
                        Copia y abre este enlace — solo puede usarse una vez y expira en 30 minutos:
                    </p>
                    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:var(--radius-sm);padding:var(--space-3);word-break:break-all">
                        <a href="{{ session('reset_link') }}"
                           style="font-size:var(--text-xs);color:#166534;text-decoration:underline">
                            {{ session('reset_link') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Correo no encontrado --}}
            @if(session('not_found'))
                <div class="alert alert-error" style="margin-bottom:var(--space-5)">
                    No encontramos una cuenta asociada a ese correo.
                </div>
            @endif

            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:var(--space-5)">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.solicitar.post') }}">
                @csrf

                <div class="form-field">
                    <label class="form-label" for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo"
                           value="{{ old('correo') }}"
                           class="form-input {{ $errors->has('correo') ? 'input-error' : '' }}"
                           placeholder="correo@institución.gob.sv"
                           autocomplete="email"
                           autofocus>
                    @error('correo')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-login">
                    Generar enlace de recuperación
                </button>
            </form>

            <a href="{{ route('login') }}"
               style="display:block;text-align:center;margin-top:var(--space-5);font-size:var(--text-sm);color:var(--color-text-muted);text-decoration:none;">
                ← Volver al inicio de sesión
            </a>

        </div>
    </div>

</div>
@endsection