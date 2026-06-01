@extends('layouts.app')
@section('title', 'Reportería y Métricas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .report-section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: var(--space-5, 20px);
            margin-top: var(--space-4, 16px);
        }
        .report-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius-lg, 12px);
            padding: var(--space-4, 16px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .report-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 8px;
        }
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Usuarios
        </a>
        <a href="{{ route('mantenimientos.index') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Mantenimientos
        </a>

        <span class="nav-section-label">Reportes</span>
        <a href="{{ route('reportes.index') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Reportes
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">CO</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Coordinador') }}</div>
                    <div class="user-role">Coordinador</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Reportería y Analítica Avanzada</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Métricas Estratégicas</h1>
                <p>Análisis en tiempo real de la infraestructura e incidencias a nivel nacional.</p>
            </div>

            {{-- 1. REPORTE DE ALERTAS: INVENTARIO CRÍTICO (Toda la pantalla) --}}
            <p class="section-title">🚨 Alertas de Equipos Dañados o de Baja</p>
            <div class="table-wrapper" style="margin-bottom: 25px;">
                @if($equiposCriticos->isEmpty())
                    <div class="empty-state">
                        <p>No existen equipos reportados en estado crítico actualmente. ¡Parque limpio!</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código Inv.</th>
                                <th>Tipo Hardware</th>
                                <th>Modelo / Marca</th>
                                <th>Ubicación Corporativa</th>
                                <th>Estado de Alerta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equiposCriticos as $eq)
                            <tr>
                                <td><strong>N° {{ $eq->Codigo_Inventario }}</strong></td>
                                <td>{{ $eq->Tipo }}</td>
                                <td>{{ $eq->Marca }} — {{ $eq->Modelo }}</td>
                                <td class="td-muted">{{ $eq->Sede }} ({{ $eq->Area_Interna }})</td>
                                <td>
                                    <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;">
                                        {{ $eq->Estado_Actual }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- CUADRÍCULA PARA REPORTES AGRUPADOS (Lado a Lado) --}}
            <div class="report-section-grid">
                
                {{-- REPORTE 2: DENSIDAD GEOGRÁFICA (EL SALVADOR REAL) --}}
                <div class="report-card">
                    <div class="report-card-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        Densidad de Incidencias Geográficas
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Departamento</th>
                                    <th>Municipio</th>
                                    <th style="text-align: center;">Mantenimientos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reporteGeografico as $geo)
                                <tr>
                                    <td><strong>{{ $geo->Departamento }}</strong></td>
                                    <td class="td-muted">{{ $geo->Municipio }}</td>
                                    <td style="text-align: center;"><span class="badge badge-programado">{{ $geo->Total_Mantenimientos }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- REPORTE 3: RENDIMIENTO TÉCNICO --}}
                <div class="report-card">
                    <div class="report-card-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                        Productividad y Carga de Técnicos
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Usuario Técnico</th>
                                    <th style="text-align: center;">Completados</th>
                                    <th style="text-align: center;">Pendientes</th>
                                    <th style="text-align: center;">Carga Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productividadTecnicos as $tec)
                                <tr>
                                    <td><strong>{{ $tec->Tecnico }}</strong></td>
                                    <td style="text-align: center;"><span class="badge badge-completado">{{ $tec->Completados }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-programado">{{ $tec->Pendientes }}</span></td>
                                    <td style="text-align: center;"><strong>{{ $tec->Total_Asignados }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>
@endsection