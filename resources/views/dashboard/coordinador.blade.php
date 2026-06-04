@extends('layouts.app')
@section('title', 'Dashboard Coordinador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Estilos optimizados para el Modal de Auditoría SIGEMPI */
        .row-clickable { cursor: pointer; transition: background 0.2s ease; }
        .row-clickable:hover { background-color: var(--theme-hover, #f1f5f9) !important; }
        
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center; z-index: 9999;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        
        .modal-card {
            background: #fff; width: 100%; max-width: 600px; border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            transform: scale(0.9); transition: all 0.3s ease; overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .modal-overlay.active .modal-card { transform: scale(1); }
        
        .modal-header {
            background: #0f172a; color: #fff; padding: 1.25rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .modal-close { background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; transition: color 0.2s; }
        .modal-close:hover { color: #fff; }
        
        .modal-body { padding: 1.5rem; font-size: 14px; color: #334155; line-height: 1.6; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .info-item { border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .info-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; }
        .info-value { font-weight: 600; color: #1e293b; margin-top: 2px; }
        
        .obs-box { background: #f8fafc; border-left: 4px solid #da6714; padding: 12px 16px; border-radius: 4px 8px 8px 4px; margin-top: 12px; }
    </style>
@endpush

@section('body')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md);">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>
        <a href="{{ route('dashboard.coordinador') }}" class="nav-link active">
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
                <div class="user-avatar">{{ strtoupper(substr(session('usuario', 'CO'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Coordinador') }}</div>
                    <div class="user-role">{{ session('rol', 'Coordinador') }}</div>
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
            <span class="topbar-title">Dashboard</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('mantenimientos.index') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo mantenimiento
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Bienvenido, {{ session('usuario', 'Coordinador') }}</h1>
                <p>Resumen general del parque informático — {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY') }}</p>
            </div>

            {{-- GRID KPIs --}}
            <div class="kpi-grid">
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'total_equipos']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div>
                    <div class="kpi-value">{{ $stats['total_equipos'] ?? 0 }}</div>
                    <div class="kpi-label">Total de equipos</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'equipos_activos']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <div class="kpi-value">{{ $stats['equipos_activos'] ?? 0 }}</div>
                    <div class="kpi-label">Equipos activos</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'equipos_danados']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon red"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="kpi-value">{{ $stats['equipos_danados'] ?? 0 }}</div>
                    <div class="kpi-label">Equipos dañados</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'total_tecnicos']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
                    <div class="kpi-value">{{ $stats['total_tecnicos'] ?? 0 }}</div>
                    <div class="kpi-label">Técnicos activos</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'este_mes']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon orange"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    <div class="kpi-value">{{ $stats['mant_este_mes'] ?? 0 }}</div>
                    <div class="kpi-label">Mantenimientos este mes</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'pendientes']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
                    <div class="kpi-value">{{ $stats['mant_programados'] ?? 0 }}</div>
                    <div class="kpi-label">Pendientes</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'completados']) }}" class="kpi-card" style="text-decoration:none; color:inherit;">
                    <div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                    <div class="kpi-value">{{ $stats['mant_completados'] ?? 0 }}</div>
                    <div class="kpi-label">Completados</div>
                </a>

                {{-- SEMÁFORO SLA --}}
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'vencidos']) }}" class="kpi-card" style="text-decoration:none; color:inherit; border-left: 5px solid #ef4444;">
                    <div class="kpi-icon red"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                    <div class="kpi-value" style="color:#ef4444;">{{ $stats['vencidos'] ?? 0 }}</div>
                    <div class="kpi-label" style="font-weight:bold;">SLA: Vencidos (Rojo)</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'criticos']) }}" class="kpi-card" style="text-decoration:none; color:inherit; border-left: 5px solid #f97316;">
                    <div class="kpi-icon orange"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="kpi-value" style="color:#f97316;">{{ $stats['criticos'] ?? 0 }}</div>
                    <div class="kpi-label" style="font-weight:bold;">SLA: Críticos &lt; 24h (Naranja)</div>
                </a>
                <a href="{{ route('dashboard.coordinador', ['filtro' => 'a_tiempo']) }}" class="kpi-card" style="text-decoration:none; color:inherit; border-left: 5px solid #22c55e;">
                    <div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                    <div class="kpi-value" style="color:#22c55e;">{{ $stats['a_tiempo'] ?? 0 }}</div>
                    <div class="kpi-label" style="font-weight:bold;">SLA: A Tiempo (Verde)</div>
                </a>
            </div>

            {{-- CABECERA FILTROS --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; flex-wrap:wrap; gap:12px; margin-top: 2rem;">
                <p class="section-title" style="margin:0; font-weight:bold;">
                    @if(request('filtro'))
                        Listado: {{ ucwords(str_replace('_', ' ', request('filtro'))) }}
                    @else
                        Próximos mantenimientos programados
                    @endif
                </p>
                <form method="GET" action="{{ route('dashboard.coordinador') }}" style="display:flex; align-items:center; gap:8px;">
                    @if(request('filtro')) <input type="hidden" name="filtro" value="{{ request('filtro') }}"> @endif
                    <select name="tecnico_id" onchange="this.form.submit()" style="padding:7px 12px; border-radius:9px; border:1px solid #ddd; font-size:13px; background:#fff; cursor:pointer; min-width:180px;">
                        <option value="">— Todos los técnicos —</option>
                        @foreach($tecnicos as $t)
                            <option value="{{ $t->ID_User }}" {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>{{ $t->Usuario }}</option>
                        @endforeach
                    </select>
                    @if(request('filtro') || request('tecnico_id'))
                        <a href="{{ route('dashboard.coordinador') }}" style="font-size:13px; color:#da6714; text-decoration:none; padding:7px 12px; border:1px solid #da6714; border-radius:9px; background:#fff; font-weight:bold;">✕ Limpiar Filtros</a>
                    @endif
                </form>
            </div>

            {{-- TABLA DATA --}}
            <div class="table-wrapper">
                @if($proximos->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <p>No se encontraron registros para este filtro corporativo.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            @if($esFiltroTecnico ?? false)
                                <tr>
                                    <th style="width: 50px; text-align: center;">#</th>
                                    <th>Nombre del Técnico</th>
                                    <th>Correo Institucional</th>
                                    <th>Fecha de Registro</th>
                                    <th>Estado Administrativo</th>
                                    <th>Fecha de Baja</th>
                                </tr>
                            @elseif($esFiltroEquipo ?? false)
                                <tr>
                                    <th style="width: 50px; text-align: center;">#</th>
                                    <th>Código Inv.</th>
                                    <th>Tipo Hardware</th>
                                    <th>Marca y Modelo</th>
                                    <th>Ubicación Corporativa</th>
                                    <th>Estado de Operación</th>
                                </tr>
                            @elseif($filtro === 'completados')
                                <tr>
                                    <th style="width: 50px; text-align: center;">#</th>
                                    <th>Código Inv.</th>
                                    <th>Tipo Hardware</th>
                                    <th>Ubicación Corporativa</th>
                                    <th>Técnico Asignado</th>
                                    <th>Fecha Programada</th>
                                    <th>Fecha de Cierre</th>
                                    <th style="text-align: center;">¿A Tiempo?</th>
                                </tr>
                            @else
                                <tr>
                                    <th style="width: 50px; text-align: center;">#</th>
                                    <th>Código Inv.</th>
                                    <th>Tipo Hardware</th>
                                    <th>Ubicación Corporativa</th>
                                    <th>Técnico Asignado</th>
                                    <th>Fecha Programada</th>
                                    <th>Estado Actual</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($proximos as $m)
                                @if($esFiltroTecnico ?? false)
                                    <tr>
                                        <td style="text-align: center; color: var(--text-muted); font-weight: bold;">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $m->Usuario }}</strong></td>
                                        <td><span style="color:#0284c7; font-weight:500;">{{ $m->Correo_User }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($m->Fecha_CreacionUser)->format('d/m/Y g:i A') }}</td>
                                        <td>
                                            <span class="badge" style="background: {{ strtolower($m->Estado_Nombre) === 'activo' ? '#d1fae5; color:#065f46;' : '#fee2e2; color:#991b1b;' }}">
                                                {{ $m->Estado_Nombre }}
                                            </span>
                                        </td>
                                        <td style="color:#dc2626; font-weight: 500;">
                                            {{ isset($m->Fecha_BajaUser) ? \Carbon\Carbon::parse($m->Fecha_BajaUser)->format('d/m/Y') : '—' }}
                                        </td>
                                    </tr>
                                @elseif($esFiltroEquipo ?? false)
                                    <tr class="{{ $filtro === 'equipos_danados' ? 'row-clickable' : '' }}" 
                                        @if($filtro === 'equipos_danados')
                                            data-modal-trigger 
                                            data-id="{{ $m->ID_Mantenimiento ?? 'N/A' }}"
                                            data-inv="N° {{ $m->Codigo_Inventario }}"
                                            data-hardware="{{ $m->Nombre_Tipo }} ({{ $m->Marca }} - {{ $m->Modelo }})"
                                            data-ubicacion="{{ $m->Nombre_Edificio }} — {{ $m->Nombre_DepartamentoInst }}"
                                            data-tecnico="Historial de Reportes"
                                            data-programada="{{ isset($m->Fecha_Falla_Real) ? \Carbon\Carbon::parse($m->Fecha_Falla_Real)->format('d/m/Y') : 'Fecha no registrada' }}"
                                            data-cierre="Declarado Inoperante"
                                            data-atiempo="HARDWARE CRÍTICO — RETIRADO DE OPERACIÓN"
                                            data-observaciones="{{ $m->Detalle_Falla ?? 'El equipo fue diagnosticado con daño severo en hardware. Se dio de baja operativa en el inventario por inoperabilidad irreversible.' }}"
                                        @endif>
                                        <td style="text-align: center; color: var(--text-muted); font-weight: bold;">{{ $loop->iteration }}</td>
                                        <td><strong>N° {{ $m->Codigo_Inventario }}</strong></td>
                                        <td><span class="badge" style="background:#f3f4f6; color:#1f2937;">{{ $m->Nombre_Tipo }}</span></td>
                                        <td>{{ $m->Marca }} — {{ $m->Modelo }}</td>
                                        <td class="td-muted">{{ $m->Nombre_Edificio }} — {{ $m->Nombre_DepartamentoInst }}</td>
                                        <td>
                                            <span class="badge" style="background: {{ $m->ID_Estado == 2 ? '#d1fae5; color:#065f46;' : '#fee2e2; color:#991b1b;' }}">
                                                {{ $m->ID_Estado == 2 ? 'Activo' : 'Dañado' }}
                                            </span>
                                        </td>
                                    </tr>
                                @elseif($filtro === 'completados')
                                    @php
                                        $meta = strtotime($m->Fecha_Reprogramacion ?? $m->Fecha_Programada);
                                        $cierre = isset($m->Fecha_Cierre) ? strtotime($m->Fecha_Cierre) : null;
                                        
                                        if (is_null($cierre)) {
                                            $SLA_Texto = 'N/A';
                                            $SLA_BadgeColor = 'background: #e2e8f0; color: #475569;';
                                            $SLA_ModalTexto = 'Sin registro oficial (Fecha de cierre vacía)';
                                        } else {
                                            $esATiempo = ($cierre <= ($meta + 86399));
                                            $SLA_Texto = $esATiempo ? 'SÍ' : 'NO';
                                            $SLA_BadgeColor = $esATiempo ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;';
                                            $SLA_ModalTexto = $esATiempo ? 'SÍ (Dentro del plazo estipulado)' : 'NO (Fuera del margen del SLA)';
                                        }
                                    @endphp
                                    <tr class="row-clickable" data-modal-trigger 
                                        data-id="{{ $m->ID_Mantenimiento ?? 'N/A' }}"
                                        data-inv="N° {{ $m->Codigo_Inventario }}"
                                        data-hardware="{{ $m->Nombre_Tipo }} ({{ $m->Marca }} - {{ $m->Modelo }})"
                                        data-ubicacion="{{ $m->Nombre_Edificio }} — {{ $m->Nombre_DepartamentoInst }}"
                                        data-tecnico="{{ $m->Tecnico }}"
                                        data-programada="{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}"
                                        data-cierre="{{ isset($m->Fecha_Cierre) ? \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y g:i A') : 'No registrada' }}"
                                        data-atiempo="{{ $SLA_ModalTexto }}"
                                        data-observaciones="{{ $m->Observaciones ?? 'El técnico no ingresó comentarios adicionales sobre la intervención.' }}">
                                        
                                        <td style="text-align: center; font-weight: bold;">{{ $loop->iteration }}</td>
                                        <td><strong>N° {{ $m->Codigo_Inventario }}</strong></td>
                                        <td><span class="badge" style="background:#e0f2fe; color:#0369a1;">{{ $m->Nombre_Tipo }}</span></td>
                                        <td class="td-muted">{{ $m->Nombre_Edificio }}</td>
                                        <td><strong>{{ $m->Tecnico }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                        <td style="color:#047857; font-weight: 500;">
                                            {{ isset($m->Fecha_Cierre) ? \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y g:i A') : '—' }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="{{ $SLA_BadgeColor }} font-weight: bold;">
                                                {{ $SLA_Texto }}
                                            </span>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td style="text-align: center; color: var(--text-muted); font-weight: bold;">{{ $loop->iteration }}</td>
                                        <td><strong>N° {{ $m->Codigo_Inventario }}</strong></td>
                                        <td><span class="badge" style="background:#e0f2fe; color:#0369a1;">{{ $m->Nombre_Tipo }} ({{ $m->Marca }} - {{ $m->Modelo }})</span></td>
                                        <td class="td-muted">{{ $m->Nombre_Edificio }} — {{ $m->Nombre_DepartamentoInst }}</td>
                                        <td><strong>{{ $m->Tecnico }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-{{ strtolower($m->Nombre_EstadoMantenimiento ?? 'programado') }}">
                                                {{ $m->Nombre_EstadoMantenimiento ?? 'Programado' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- 📄 VENTANA EMERGENTE INTERACTIVA: FICHA TÉCNICA DE AUDITORÍA --}}
<div id="auditModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Ficha de Auditoría — Registro <span id="m_id"></span>
            </h3>
            <button class="modal-close" id="closeModal">✕</button>
        </div>
        <div class="modal-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Código Inventario</div>
                    <div class="info-value" id="m_inv"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Hardware Intervenido</div>
                    <div class="info-value" id="m_hardware"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ubicación Física</div>
                    <div class="info-value" id="m_ubicacion"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Auditor / Técnico</div>
                    <div class="info-value" id="m_tecnico"></div>
                </div>
                <div class="info-item">
                    <div class="info-label" id="label_fecha">Fecha Programada</div>
                    <div class="info-value" id="m_programada"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Estatus Operativo</div>
                    <div class="info-value" id="m_cierre" style="color:#047857;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div class="info-label">Detalle de Control</div>
                <div id="m_atiempo" style="font-weight: bold; margin-top: 4px;"></div>
            </div>

            <div>
                <div class="info-label">Diagnóstico & Observaciones Técnicas de Justificación</div>
                <div class="obs-box" id="m_observaciones"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('[data-modal-trigger]');
        const modal = document.getElementById('auditModal');
        const closeBtn = document.getElementById('closeModal');

        rows.forEach(row => {
            row.addEventListener('click', function() {
                document.getElementById('m_id').innerText = this.dataset.id !== 'N/A' ? '#MANT-' + this.dataset.id : '— CONTROL GENERAL';
                document.getElementById('m_inv').innerText = this.dataset.inv;
                document.getElementById('m_hardware').innerText = this.dataset.hardware;
                
                // =================================================================
                // 🛑 UNICA LÍNEA CORREGIDA: SANEAMIENTO DIRECTO DE CARACTERES ROTOS 
                // Intercepta "Impresi?n" o variantes corruptas y fuerza "Impresión"
                // =================================================================
                let stringUbicacion = this.dataset.ubicacion;
                let stringCorregido = stringUbicacion.replace(/Impresi\?n|Impresi&oacute;n|Impresin/g, 'Impresión');
                
                document.getElementById('m_ubicacion').innerText = stringCorregido;
                document.getElementById('m_tecnico').innerText = this.dataset.tecnico;
                document.getElementById('m_programada').innerText = this.dataset.programada;
                document.getElementById('m_cierre').innerText = this.dataset.cierre;
                document.getElementById('m_observaciones').innerText = this.dataset.observaciones;
                
                const atiempoContainer = document.getElementById('m_atiempo');
                atiempoContainer.innerText = this.dataset.atiempo;
                
                const estatusContainer = document.getElementById('m_cierre');
                const labelFecha = document.getElementById('label_fecha');
                
                if(this.dataset.cierre.includes('Declarado')) {
                    labelFecha.innerText = "Fecha de Baja";
                    estatusContainer.style.color = '#dc2626'; 
                    atiempoContainer.style.color = '#991b1b';
                } else {
                    labelFecha.innerText = "Fecha Programada";
                    estatusContainer.style.color = '#047857';
                    if(this.dataset.atiempo.includes('SÍ')) {
                        atiempoContainer.style.color = '#065f46';
                    } else if(this.dataset.atiempo.includes('Sin registro')) {
                        atiempoContainer.style.color = '#475569';
                    } else {
                        atiempoContainer.style.color = '#991b1b';
                    }
                }

                modal.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
    });
</script>
@endsection