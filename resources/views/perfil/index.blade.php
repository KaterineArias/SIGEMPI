@extends('layouts.app')
@section('title', 'Mi perfil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Mi perfil</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Mi perfil</h1>
                <p>Mantén tu contraseña segura y actualizada</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="form-card" style="max-width:480px">
                {{-- Info de solo lectura --}}
                <div style="display:flex;gap:16px;align-items:center;
                            padding:16px;background:var(--color-bg);
                            border:1px solid var(--color-border);border-radius:var(--radius-md);
                            margin-bottom:8px">
                    <div class="user-avatar" style="width:48px;height:48px;font-size:18px;
                                                    border-radius:50%;background:var(--color-primary);
                                                    color:#fff;display:flex;align-items:center;
                                                    justify-content:center;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($usuario->Usuario, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:15px">{{ $usuario->Usuario }}</div>
                        <div style="font-size:12px;color:var(--color-text-muted)">{{ $usuario->Correo_User }}</div>
                        <div style="font-size:12px;color:var(--color-text-muted)">{{ $usuario->rol->Rol }}</div>
                    </div>
                </div>

                @if(session('success'))
                    <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;
                                padding:10px 14px;border-radius:8px;font-size:13px;">
                        {{ session('success') }}
                    </div>
                @endif

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

                <form method="POST" action="{{ route('perfil.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-field">
                        <label for="password_actual" style="display:flex;justify-content:space-between;align-items:center">
                            <span>Contraseña actual</span>
                            <span style="font-size:11px;color:var(--color-text-muted);font-weight:400">
                                @if($usuario->Password_Changed_At)
                                    🔒 Último cambio: {{ \Carbon\Carbon::parse($usuario->Password_Changed_At)->format('d/m/Y') }}
                                @else
                                    🔒 Nunca ha sido cambiada
                                @endif
                            </span>
                        </label>
                        <input type="password" id="password_actual" name="password_actual"
                            placeholder="Ingresa tu contraseña actual" required>
                        @error('password_actual')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="password">Nueva contraseña</label>
                        <input type="password" id="password" name="password"
                            placeholder="Mínimo 8 caracteres" required>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repite la nueva contraseña" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
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