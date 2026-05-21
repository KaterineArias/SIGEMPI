@extends('layouts.app')

@section('title', 'Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md)">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

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
        <a href="{{ route('usuarios.index') }}" class="nav-link active">
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
            <span class="topbar-title">Usuarios</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo usuario
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Usuarios del sistema</h1>
                <p>Gestión de accesos y roles</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-wrapper">
                @if($usuarios->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <p>No hay usuarios registrados aún.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Fecha de creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $u)
                            <tr>
                                <td class="td-muted">{{ $loop->iteration }}</td>
                                <td><strong>{{ $u->Usuario }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $u->Rol === 'Coordinador' ? 'blue' : 'orange' }}">
                                        {{ $u->Rol }}
                                    </span>
                                </td>
                                <td class="td-muted">{{ \Carbon\Carbon::parse($u->Fecha_Creacion)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('usuarios.edit', $u->ID_User) }}" class="btn btn-secondary" style="padding:var(--space-1) var(--space-3);font-size:var(--text-xs)">
                                        Editar
                                    </a>
                                    @if($u->Usuario !== session('usuario'))
                                        <form method="POST" action="{{ route('usuarios.destroy', $u->ID_User) }}" style="display:inline" onsubmit="return confirm('¿Eliminar usuario {{ $u->Usuario }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding:var(--space-1) var(--space-3);font-size:var(--text-xs)">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size:var(--text-xs);color:var(--color-text-muted);padding:var(--space-1) var(--space-3)">(tú)</span>
                                    @endif
                                </td>
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