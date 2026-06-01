@extends('layouts.app')
@section('title', 'Dashboard Técnico')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64"
                    style="object-fit:contain;border-radius:var(--radius-md);">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        <span class="nav-section-label">Principal</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>

        <span class="nav-section-label">Mi Trabajo</span>
        <a href="{{ route('dashboard.tecnico', ['filtro' => 'pendientes']) }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><polyline points="12 6 12 12 16 14"/></svg>
            Mis asignaciones
        </a>
        <a href="{{ route('dashboard.tecnico', ['filtro' => 'completados']) }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 4 12 14.01 9 11.01"/><path d="M22 11v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Historial de Cierre
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar" style="background: var(--teal-600);">{{ strtoupper(substr(session('usuario', 'TE'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Técnico') }}</div>
                    <div class="user-role">{{ session('rol', 'Tecnico') }}</div>
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

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Panel Técnico de Campo</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Hola, {{ session('usuario') }}</h1>
                <p>Estas son tus asignaciones de soporte técnico — {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>

            {{-- KPIs CLIQUEABLES DEL TÉCNICO --}}
            <div class="kpi-grid">
                <a href="{{ route('dashboard.tecnico', ['filtro' => 'pendientes']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_programados'] ?? 0 }}</div>
                    <div class="kpi-label">Pendientes asignados</div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'completados']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_completados'] ?? 0 }}</div>
                    <div class="kpi-label">Completados en total</div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'este_mes']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_este_mes'] ?? 0 }}</div>
                    <div class="kpi-label">Asignados este mes</div>
                </a>
            </div>

            {{-- TITULO DINÁMICO DE LA TABLA --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; margin-top:2rem;">
                <p class="section-title" style="margin:0; font-weight:bold;">
                    @if(request('filtro'))
                        Listado de Órdenes: {{ ucwords(request('filtro')) }}
                    @else
                        Mis próximas asignaciones pendientes
                    @endif
                </p>
                
                @if(request('filtro'))
                    <a href="{{ route('dashboard.tecnico') }}" style="font-size:12px; color:#da6714; text-decoration:none; padding:6px 12px; border:1px solid #da6714; border-radius:8px; font-weight:bold; background:#fff;">
                        ✕ Ver Asignaciones Actuales
                    </a>
                @endif
            </div>

            {{-- TABLA DE ASIGNACIONES --}}
            <div class="table-wrapper">
                @if($asignaciones->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p>No tienes mantenimientos registrados en esta sección.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código Inv.</th>
                                <th>Hardware</th>
                                <th>Ubicación Sede</th>
                                <th>Fecha Programada</th>
                                <th>Estado Mantenimiento</th>
                                @if(!request('filtro') || request('filtro') === 'pendientes')
                                    <th>Acción</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asignaciones as $m)
                            <tr>
                                <td><strong>N° {{ $m->Codigo_Inventario }}</strong></td>
                                <td><span class="badge" style="background:#e0f2fe; color:#0369a1;">{{ $m->Nombre_Tipo }} ({{ $m->Marca }} - {{ $m->Modelo }})</span></td>
                                <td class="td-muted">{{ $m->Nombre_Edificio }} — {{ $m->Nombre_DepartamentoInst }}</td>
                                <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge" style="background: {{ $m->ID_EstadoMantenimiento == 2 ? '#d1fae5; color:#065f46;' : '#ffedd5; color:#c2410c;' }}">
                                        {{ $m->Nombre_EstadoMantenimiento }}
                                    </span>
                                </td>
                                {{-- Solo mostramos el botón de intervenir si la orden está pendiente --}}
                                @if(!request('filtro') || request('filtro') === 'pendientes')
                                    <td>
                                       <a href="{{ route('intervenciones.create', ['id_mantenimiento' => $m->ID_Mantenimiento]) }}" class="btn btn-primary" style="background:#da6714; border-color:#da6714; padding:5px 10px; font-size:12px;">
                                            Intervenir
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