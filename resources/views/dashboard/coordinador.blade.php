@extends('layouts.app')
@section('title', 'Dashboard Coordinador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=coordinador_master_v100') }}">
    <style>
        .badge-status-maint { font-size: 11px !important; padding: 4px 10px !important; font-weight: 600 !important; border-radius: 4px !important; display: inline-block; text-transform: uppercase; }
        .bg-programado { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .bg-completado { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        
        .table-wrapper table { width: 100%; border-collapse: collapse; font-family: system-ui, -apple-system, sans-serif; }
        .table-wrapper th { background: #f8fafc; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .table-wrapper td { padding: 12px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; white-space: nowrap; }
        .font-code { font-weight: 600; color: #0f172a; }
        .sub-text-muted { display: block; font-size: 11px; color: #64748b; margin-top: 2px; }
        .filter-select-premium { padding: 7px 12px; border-radius: 9px; border: 1px solid #cbd5e1; font-size: 13px; background: #fff; cursor: pointer; font-weight: 600; color: #1e293b; height: 34px; }
        .btn-filter-submit { background: #0f172a; color: #fff; padding: 6px 14px; border-radius: 9px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; height: 34px; }
    </style>
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Panel de Control Gerencial — Coordinación TI</span>
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
                <h1>Bienvenido, {{ session('usuario') }}</h1>
                <p>Resumen general del parque informático institucional — {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>

            {{-- KPIs DINÁMICOS --}}
            <div class="kpi-grid">
                <div class="kpi-card"><div class="kpi-icon teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg></div><div class="kpi-value">{{ $stats['total_equipos'] ?? 0 }}</div><div class="kpi-label">Total de Equipos</div></div>
                <div class="kpi-card"><div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div class="kpi-value">{{ $stats['equipos_activos'] ?? 0 }}</div><div class="kpi-label">Equipos Activos</div></div>
                <div class="kpi-card"><div class="kpi-icon red"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><div class="kpi-value">{{ $stats['equipos_danados'] ?? 0 }}</div><div class="kpi-label">Equipos Dañados</div></div>
                <div class="kpi-card"><div class="kpi-icon blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="kpi-value">{{ $stats['total_tecnicos'] ?? 0 }}</div><div class="kpi-label">Técnicos Activos</div></div>
                <div class="kpi-card"><div class="kpi-icon orange"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="kpi-value">{{ $stats['mant_este_mes'] ?? 0 }}</div><div class="kpi-label">Mantenimientos Mes</div></div>
                <div class="kpi-card"><div class="kpi-icon teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div><div class="kpi-value">{{ $stats['mant_programados'] ?? 0 }}</div><div class="kpi-label">Pendientes</div></div>
                <div class="kpi-card"><div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div><div class="kpi-value">{{ $stats['mant_completados'] ?? 0 }}</div><div class="kpi-label">Completados</div></div>
            </div>

            {{-- FORMULARIO DE FILTROS TOTALMENTE INTERACTIVO --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; flex-wrap:wrap; gap:12px;">
                <p class="section-title" style="margin:0; font-weight: 700; color: #0f172a;">Monitoreo de Órdenes de Trabajo</p>

                <form method="GET" action="{{ url('dashboard/coordinador') }}" id="formFiltrosCoordinador" style="display:flex; align-items:center; gap:8px;">
                    
                    {{-- Selector de Estado --}}
                    <select name="estado_filtro" id="estado_filtro" class="filter-select-premium">
                        <option value="1" {{ request('estado_filtro', 1) == 1 ? 'selected' : '' }}>📋 Ver Órdenes Programadas (Pendientes)</option>
                        <option value="2" {{ request('estado_filtro') == 2 ? 'selected' : '' }}>✅ Ver Órdenes Completadas (Cerradas)</option>
                    </select>

                    {{-- Selector de Técnico --}}
                    <select name="tecnico_id" id="tecnico_id" class="filter-select-premium" style="min-width:200px;">
                        <option value="">💼 — Todos los Técnicos —</option>
                        @isset($tecnicos)
                            @foreach($tecnicos as $t)
                                <option value="{{ $t->ID_User }}" {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>👤 {{ $t->Usuario }}</option>
                            @endforeach
                        @endisset
                    </select>

                    <button type="submit" class="btn-filter-submit">Filtrar</button>

                    @if(request('tecnico_id') || request('estado_filter') != 1)
                        <a href="{{ url('dashboard/coordinador') }}" style="font-size:13px; color:#64748b; text-decoration:none; padding:6px 12px; border:1px solid #cbd5e1; border-radius:9px; background:#fff; font-weight:600; display:inline-flex; align-items:center; gap:4px; height:34px; box-sizing:border-box;">✕ Limpiar</a>
                    @endif
                </form>
            </div>
            
            {{-- GRILLA PRINCIPAL --}}
            <div class="table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow:hidden;">
                @if(!isset($proximos) || $proximos->isEmpty())
                    <div class="empty-state" style="padding: 4rem; text-align: center; color: #64748b;">
                        <p style="margin:0; font-weight:500;">No se registran órdenes en este estado para los filtros seleccionados.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código Inv.</th>
                                <th>Tipo Hardware</th>
                                <th>Ubicación Sede Destino</th>
                                <th>Técnico Asignado</th>
                                <th>Fecha Programada</th>
                                <th style="text-align: center;">Estado de Orden</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proximos as $m)
                            <tr>
                                <td><span class="font-code">{{ $m->Codigo_Inventario }}</span></td>
                                <td>
                                    <span class="badge" style="background:#f1f5f9; color:#334155; font-weight:600; padding:3px 8px; border-radius:4px;">{{ $m->Nombre_Tipo ?? 'Hardware' }}</span>
                                    <span class="sub-text-muted">{{ $m->Marca ?? '' }} {{ $m->Modelo ?? '' }}</span>
                                </td>
                                <td class="td-muted">
                                    {{ isset($m->Nombre_Edificio) ? str_replace(['Impresi?n', 'Impresi&oacute;n', 'Impresin'], 'Impresión', $m->Nombre_Edificio) : 'Sede Central' }}
                                    <span class="sub-text-muted">{{ $m->Nombre_DepartamentoInst ?? 'General' }}</span>
                                </td>
                                <td style="font-weight: 600; color: #1e293b;">
                                    {{ $m->NombreTecnicoResponsable ?? 'Sin asignar' }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                <td align="center">
                                    @php
                                        $estadoFmt = $m->Nombre_EstadoMantenimiento ?? 'Programado';
                                        $claseCSS = ($m->ID_EstadoMantenimiento == 2) ? 'bg-completado' : 'bg-programado';
                                    @endphp
                                    <span class="badge-status-maint {{ $claseCSS }}">{{ $estadoFmt }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if(isset($proximos) && !$proximos->isEmpty() && method_exists($proximos, 'links'))
                <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                    {{ $proximos->appends(request()->query())->links() }}
                </div>
            @endif
        </main>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT NATIVO PARA PASAR FILTROS AL INSTANTE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formFiltrosCoordinador');
        const selectEstado = document.getElementById('estado_filtro');
        const selectTecnico = document.getElementById('tecnico_id');

        if(selectEstado) {
            selectEstado.addEventListener('change', function() { form.submit(); });
        }
        if(selectTecnico) {
            selectTecnico.addEventListener('change', function() { form.submit(); });
        }
    });
</script>
@endsection