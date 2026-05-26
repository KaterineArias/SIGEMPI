@extends('layouts.app')

@section('title', 'Nuevo mantenimiento')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mantenimientos.css') }}">
@endpush

@section('body')

<div class="container">

    <div class="page-header">

        <h1 class="page-title">
            Programar mantenimiento
        </h1>

    </div>

    <div class="card">

        <h2 class="card-title">
            Información del mantenimiento
        </h2>

        <form
            method="POST"
            action="{{ route('mantenimientos.store') }}"
        >

            @csrf

            <div class="form-grid">

                {{-- EQUIPO --}}
                <div class="form-group">

                    <label class="form-label">
                        Equipo
                    </label>

                    <select
                        name="ID_Equipo"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Seleccione un equipo
                        </option>

                        @foreach($equipos as $equipo)

                            <option value="{{ $equipo->ID_Equipo }}">

                                {{ $equipo->Codigo_Inventario }}
                                —
                                {{ $equipo->Tipo }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- TECNICO --}}
                <div class="form-group">

                    <label class="form-label">
                        Técnico asignado
                    </label>

                    <select
                        name="ID_Tecnico"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Seleccione un técnico
                        </option>

                        @foreach($tecnicos as $tec)

                            <option value="{{ $tec->ID_User }}">
                                {{ $tec->Usuario }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- FECHA --}}
                <div class="form-group">

                    <label class="form-label">
                        Fecha programada
                    </label>

                    <input
                        type="date"
                        name="Fecha_Programada"
                        class="form-control"
                        required
                    >

                </div>

                {{-- ESTADO --}}
                <div class="form-group">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        name="Estado_Mantenimiento"
                        class="form-control"
                    >

                        <option value="Programado">
                            Programado
                        </option>

                        <option value="Completado">
                            Completado
                        </option>

                        <option value="Cancelado">
                            Cancelado
                        </option>

                        <option value="Reprogramado">
                            Reprogramado
                        </option>

                    </select>

                </div>

                {{-- OBSERVACIONES --}}
                <div class="form-group form-group-full">

                    <label class="form-label">
                        Observaciones
                    </label>

                    <textarea
                        name="Observaciones"
                        rows="5"
                        class="form-control"
                        placeholder="Escriba observaciones del mantenimiento..."
                    ></textarea>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar mantenimiento
                </button>

                <a
                    href="{{ route('mantenimientos.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

@endsection
@extends('layouts.app')

@section('title', 'Registrar intervención')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md)">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        <div>
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.coordinador') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <span class="nav-section-label">Gestión</span>
            <a href="{{ route('equipos.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Equipos
            </a>
            <a href="{{ route('usuarios.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Usuarios
            </a>
            <a href="{{ route('mantenimientos.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Mantenimientos
            </a>

            <span class="nav-section-label">Reportes</span>
            <a href="{{ route('reportes.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Reportes
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario') }}</div>
                    <div class="user-role">{{ session('rol') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:var(--space-3)">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center;font-size:var(--text-xs)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Nuevo Mantenimiento</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Registrar intervención</h1>
                <p>Completa los datos para programar el mantenimiento</p>
            </div>

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('mantenimientos.store') }}" method="POST" style="max-width:640px">
                @csrf

                <div class="form-card">

                    {{-- Equipo --}}
                    <div class="form-field">
                        <label for="ID_Equipo">Equipo *</label>
                        <select id="ID_Equipo" name="ID_Equipo">
                            <option value="">— Seleccionar equipo —</option>
                            @foreach($equipos as $equipo)
                                <option value="{{ $equipo->ID_Equipo }}"
                                    {{ old('ID_Equipo') == $equipo->ID_Equipo ? 'selected' : '' }}>
                                    {{ $equipo->Codigo_Inventario }} — {{ $equipo->Marca }} {{ $equipo->Modelo }} ({{ $equipo->Tipo }})
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Equipo')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Técnico --}}
                    <div class="form-field">
                        <label for="ID_Tecnico">Técnico asignado *</label>
                        <select id="ID_Tecnico" name="ID_Tecnico">
                            <option value="">— Seleccionar técnico —</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->ID_User }}"
                                    {{ old('ID_Tecnico') == $tecnico->ID_User ? 'selected' : '' }}>
                                    {{ $tecnico->Usuario }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Tecnico')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Fecha + Estado en dos columnas --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
                        <div class="form-field">
                            <label for="Fecha_Programada">Fecha programada *</label>
                            <input
                                type="date"
                                id="Fecha_Programada"
                                name="Fecha_Programada"
                                value="{{ old('Fecha_Programada') }}"
                                min="{{ date('Y-m-d') }}"
                            >
                            @error('Fecha_Programada')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label>Estado</label>
                            <div style="display:flex;align-items:center;gap:8px;padding:8px 11px;border:1px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-bg);height:38px;">
                                <span class="badge badge-blue">Programado</span>
                                <span style="font-size:11px;color:var(--color-text-muted)">Se asigna automáticamente</span>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="form-field">
                        <label for="Observaciones">Observaciones</label>
                        <textarea
                            id="Observaciones"
                            name="Observaciones"
                            rows="4"
                            maxlength="1000"
                            placeholder="Describe el problema o el trabajo a realizar..."
                        >{{ old('Observaciones') }}</textarea>
                        <span style="font-size:11px;color:var(--color-text-muted);text-align:right">
                            <span id="char-count">0</span>/1000
                        </span>
                        @error('Observaciones')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Guardar intervención
                        </button>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>

                </div>
            </form>
        </main>
    </div>

</div>

@push('scripts')
<script>
    const textarea = document.getElementById('Observaciones');
    const counter  = document.getElementById('char-count');
    if (textarea && counter) {
        const update = () => counter.textContent = textarea.value.length;
        textarea.addEventListener('input', update);
        update();
    }
</script>
@endpush

@endsection