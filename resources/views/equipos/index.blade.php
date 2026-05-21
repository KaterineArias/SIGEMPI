@extends('layouts.app')

@section('title', 'Equipos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR dinámico según rol --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md)">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        @if(session('rol') === 'Coordinador')
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.coordinador') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <span class="nav-section-label">Gestión</span>
            <a href="{{ route('equipos.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Equipos
            </a>
            <a href="{{ route('usuarios.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Usuarios
            </a>
            <a href="{{ route('mantenimientos.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Mantenimientos
            </a>
            <span class="nav-section-label">Reportes</span>
            <a href="{{ route('reportes.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Reportes
            </a>
        @else
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <span class="nav-section-label">Mi Trabajo</span>
            <a href="{{ route('mantenimientos.mis-asignaciones') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Mis asignaciones
            </a>
            <a href="{{ route('mantenimientos.create') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Registrar intervención
            </a>
            <a href="{{ route('equipos.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Ver equipos
            </a>
        @endif

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
            <span class="topbar-title">Equipos</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                {{-- Botón nuevo equipo solo para coordinador --}}
                @if(session('rol') === 'Coordinador')
                    <a href="{{ route('equipos.create') }}" class="btn btn-primary">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Nuevo equipo
                    </a>
                @endif
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Equipos registrados</h1>
                <p>Inventario de equipos de la institución</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtro por tipo y estado --}}
            <form action="{{ route('equipos.index') }}" method="GET"
                  style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap;">
                <select name="tipo" onchange="this.form.submit()"
                        style="padding:6px 10px;border:1px solid var(--color-border);border-radius:7px;font-size:12px;font-family:inherit;background:var(--color-bg);color:var(--color-text);outline:none;">
                    <option value="">Todos los tipos</option>
                    @foreach(['Escritorio','Laptop','Servidor','Impresora','Plotter'] as $tipo)
                        <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                            {{ $tipo }}
                        </option>
                    @endforeach
                </select>
                <select name="estado" onchange="this.form.submit()"
                        style="padding:6px 10px;border:1px solid var(--color-border);border-radius:7px;font-size:12px;font-family:inherit;background:var(--color-bg);color:var(--color-text);outline:none;">
                    <option value="">Todos los estados</option>
                    @foreach(['Activo','Dañado','Inactivo','Bodega','De Baja'] as $estado)
                        <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                            {{ $estado }}
                        </option>
                    @endforeach
                </select>
                @if(request('tipo') || request('estado'))
                    <a href="{{ route('equipos.index') }}"
                       style="font-size:12px;color:var(--color-text-muted);text-decoration:underline">
                        Limpiar filtros
                    </a>
                @endif
            </form>

            <div class="table-wrapper">
                @if($equipos->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        <p>No hay equipos registrados.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Marca / Modelo</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                @if(session('rol') === 'Coordinador')
                                    <th>Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipos as $equipo)
                            <tr>
                                <td><strong>{{ $equipo->Codigo_Inventario }}</strong></td>
                                <td>{{ $equipo->Tipo }}</td>
                                <td>
                                    {{ $equipo->Marca ?? '—' }}
                                    @if($equipo->Modelo)
                                        <span class="td-muted"> / {{ $equipo->Modelo }}</span>
                                    @endif
                                </td>
                                <td class="td-muted">{{ $equipo->Ubicacion }}</td>
                                <td>
                                    <span class="badge badge-{{ match($equipo->Estado) {
                                        'Activo'   => 'green',
                                        'Dañado'   => 'red',
                                        'Inactivo' => 'yellow',
                                        'Bodega'   => 'orange',
                                        'De Baja'  => 'red',
                                        default    => 'blue'
                                    } }}">
                                        {{ $equipo->Estado }}
                                    </span>
                                </td>
                                @if(session('rol') === 'Coordinador')
                                <td>
                                    <a href="{{ route('equipos.edit', $equipo->ID_Equipo) }}"
                                       class="btn btn-secondary"
                                       style="padding:var(--space-1) var(--space-3);font-size:var(--text-xs)">
                                        Editar
                                    </a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection