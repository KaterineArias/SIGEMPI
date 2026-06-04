@extends('layouts.app')
@section('title', 'Mi Historial de Trabajo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Estilos institucionales en pantalla para la tabla interactiva y modal seguro */
        .row-clickable { cursor: pointer; transition: background 0.2s ease; }
        .row-clickable:hover { background-color: #f1f5f9 !important; }
        
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center; z-index: 9999;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        
        .modal-card { 
            background: #fff; width: 100%; max-width: 600px; border-radius: 16px; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e2e8f0; 
        }
        .modal-header { background: #0f172a; color: #fff; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .modal-header h3 { margin: 0; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .modal-close { background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; transition: color 0.2s; }
        .modal-close:hover { color: #fff; }
        
        .modal-body { padding: 1.5rem; font-size: 14px; color: #334155; line-height: 1.6; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .info-item { border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .info-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .info-value { font-weight: 600; color: #1e293b; margin-top: 2px; }
        .obs-box { background: #f8fafc; border-left: 4px solid #da6714; padding: 12px 16px; border-radius: 4px; margin-top: 12px; }
        .action-box { background: #f0fdf4; border-left: 4px solid #22c55e; padding: 12px 16px; border-radius: 4px; margin-top: 12px; }

        /* Píldoras de filtrado de hardware */
        .filter-tabs { display: flex; gap: 8px; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .filter-btn { 
            padding: 8px 16px; border-radius: 20px; border: 1px solid #cbd5e1; 
            background: #fff; color: #64748b; font-size: 13px; font-weight: 600; 
            text-decoration: none; transition: all 0.2s ease; 
        }
        .filter-btn.active { background: #da6714; color: #fff; border-color: #da6714; }

        /* Botón PDF en grilla */
        .btn-pdf { 
            padding: 4px 10px; border-radius: 4px; background: #fee2e2; color: #991b1b; 
            font-size: 11px; font-weight: bold; text-decoration: none; display: inline-flex; 
            align-items: center; gap: 4px; border: none; cursor: pointer; transition: background 0.15s;
        }
        .btn-pdf:hover { background: #fca5a5; }

        /* COMPONENTE SELECT-DROPDOWN MULTI-REPORTE */
        .dropdown-container { position: relative; display: inline-block; }
        .dropdown-trigger {
            padding: 7px 16px; border-radius: 6px; background: #1e293b; color: #fff;
            font-size: 13px; font-weight: bold; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;
        }
        .dropdown-trigger:hover { background: #0f172a; }
        .dropdown-menu {
            position: absolute; right: 0; top: 110%; background: #fff;
            min-width: 210px; border-radius: 8px; border: 1px solid #e2e8f0;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 100;
            display: none; flex-direction: column; overflow: hidden;
        }
        .dropdown-menu.show { display: flex; }
        .dropdown-item {
            padding: 10px 14px; font-size: 13px; color: #334155; text-decoration: none;
            display: flex; align-items: center; gap: 8px; transition: background 0.15s; cursor: pointer; border: none; background: transparent; text-align: left; width: 100%;
        }
        .dropdown-item:hover { background: #f1f5f9; color: #0f172a; }
        .dropdown-item.excel-opt:hover { background: #f0fdf4; color: #166534; }
        .dropdown-item.word-opt:hover { background: #eff6ff; color: #1e40af; }

        #printArea { display: none; }
        @media print {
            body, .app-shell, .main-content, .page-body { 
                display: block !important; 
                background: #fff !important; 
                padding: 0 !important; 
                margin: 0 !important; 
            }
            body * { display: none !important; }
            
            #printArea, #printArea * { display: block !important; }
            #printArea { 
                display: block !important; 
                position: absolute !important; left: 0 !important; top: 0 !important; width: 18.5cm !important; 
                font-family: 'Segoe UI', Arial, sans-serif !important; color: #000 !important; background: #fff !important;
            }
            .p-header { display: flex !important; justify-content: space-between !important; align-items: center !important; border-bottom: 3px solid #0f172a !important; padding-bottom: 10px !important; margin-bottom: 20px !important; }
            .p-title { font-size: 22px !important; font-weight: bold !important; color: #0f172a !important; text-transform: uppercase !important; }
            .p-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 15px !important; margin-bottom: 25px !important; }
            .p-box { border: 1px solid #cbd5e1 !important; padding: 10px !important; border-radius: 6px !important; background: #f8fafc !important; }
            .p-label { font-size: 10px !important; text-transform: uppercase !important; color: #475569 !important; font-weight: bold !important; }
            .p-value { font-size: 14px !important; font-weight: 600 !important; margin-top: 2px !important; color: #0f172a !important; }
            .p-section-title { font-size: 12px !important; text-transform: uppercase !important; color: #0f172a !important; font-weight: bold !important; margin-bottom: 6px !important; border-bottom: 1px solid #cbd5e1 !important; padding-bottom: 4px !important; }
            .p-text-block { border-left: 4px solid #da6714 !important; background: #f8fafc !important; padding: 12px !important; font-size: 13px !important; line-height: 1.5 !important; border-radius: 0 6px 6px 0 !important; margin-bottom: 20px !important; }
            
            .p-firmas-container {
                display: block !important;
                width: 100% !important;
                margin-top: 90px !important;
                page-break-inside: avoid !important;
                clear: both !important;
            }
            .p-firma-block {
                display: inline-block !important;
                float: left !important;
                width: 8.2cm !important;
                text-align: center !important;
                border-top: 1px solid #000000 !important;
                padding-top: 10px !important;
            }
            .p-firma-spacer {
                display: inline-block !important;
                float: left !important;
                width: 2.1cm !important;
                height: 1px !important;
            }
        }
    </style>
@endpush

@section('body')
<div class="app-shell">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md);">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        <span class="nav-section-label">Principal</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Mis Asignaciones
        </a>
        <span class="nav-section-label">Operaciones</span>
        <a href="{{ route('mantenimientos.index-tecnico') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Ver Historial
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario', 'TE'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Técnico') }}</div>
                    <div class="user-role">{{ session('rol', 'Técnico') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center;font-size:11px;">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content" id="mainContentArea">
        <header class="topbar">
            <span class="topbar-title">Módulo de Consulta Técnica Personal</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Mi Historial de Órdenes Cerradas</h1>
                <p>Consulta completa de tus intervenciones ejecutadas y validadas en el parque informático</p>
            </div>

            {{-- TARJETAS KPI --}}
            <div class="kpi-grid" style="margin-bottom: 1.5rem;">
                <div class="kpi-card">
                    <div class="kpi-icon teal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
                    <div class="kpi-value">{{ isset($kpis['total']) ? $kpis['total'] : 0 }}</div>
                    <div class="kpi-label">Órdenes Atendidas Totales</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 4 12 14.01 9 11.01"/><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg></div>
                    <div class="kpi-value" style="color: #16a34a;">{{ isset($kpis['operativos']) ? $kpis['operativos'] : 0 }}</div>
                    <div class="kpi-label">Equipos Operativos en Sede</div>
                </div>
                <div class="kpi-card" style="border-left: 5px solid #22c55e;">
                    <div class="kpi-icon orange"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                    <div class="kpi-value" style="color: #da6714;">{{ isset($kpis['sla_rate']) ? $kpis['sla_rate'] : 100 }}%</div>
                    <div class="kpi-label">Eficiencia Cumplimiento SLA</div>
                </div>
            </div>

            <div class="filter-tabs">
                <a href="{{ route('mantenimientos.index-tecnico', ['search' => $search, 'tipo' => 'Todos']) }}" class="filter-btn {{ (request('tipo') == 'Todos' || request('tipo') == '') ? 'active' : '' }}">Todos</a>
                <a href="{{ route('mantenimientos.index-tecnico', ['search' => $search, 'tipo' => 'Plotter']) }}" class="filter-btn {{ request('tipo') == 'Plotter' ? 'active' : '' }}">Plotters</a>
                <a href="{{ route('mantenimientos.index-tecnico', ['search' => $search, 'tipo' => 'Laptop']) }}" class="filter-btn {{ request('tipo') == 'Laptop' ? 'active' : '' }}">Laptops</a>
                <a href="{{ route('mantenimientos.index-tecnico', ['search' => $search, 'tipo' => 'Impresora']) }}" class="filter-btn {{ request('tipo') == 'Impresora' ? 'active' : '' }}">Impresoras</a>
                <a href="{{ route('mantenimientos.index-tecnico', ['search' => $search, 'tipo' => 'Escritorio']) }}" class="filter-btn {{ request('tipo') == 'Escritorio' ? 'active' : '' }}">Desktop</a>
            </div>

            <div style="background:#fff; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:1.5rem; display:flex; align-items:center; gap:12px;">
                <form method="GET" action="{{ route('mantenimientos.index-tecnico') }}" id="searchForm" style="display:flex; gap:8px; margin:0; flex-grow:1;">
                    @if(request('tipo')) <input type="hidden" name="tipo" id="currentTipo" value="{{ request('tipo') }}"> @endif
                    <input type="text" name="search" id="currentSearch" value="{{ $search }}" placeholder="🔍 Buscar por código de inventario de hardware..." style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; flex-grow:1;">
                    <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:13px; border-radius:6px;">Buscar Equipo</button>
                    @if($search || (request('tipo') && request('tipo') !== 'Todos'))
                        <a href="{{ route('mantenimientos.index-tecnico') }}" style="padding:6px 12px; border:1px solid #cbd5e1; background:#fff; text-decoration:none; color:#64748b; font-size:13px; border-radius:6px; display:inline-flex; align-items:center;">Limpiar</a>
                    @endif
                </form>

                <div class="dropdown-container">
                    <button type="button" class="dropdown-trigger" id="exportMenuBtn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Exportar Bitácora
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="dropdown-menu" id="exportDropdownMenu">
                        <button type="button" class="dropdown-item excel-opt" onclick="ejecutarExportacion('excel')">🟢 Formato Excel (.XLS)</button>
                        <button type="button" class="dropdown-item word-opt" onclick="ejecutarExportacion('word')">🔵 Formato Word (.DOC)</button>
                        <button type="button" class="dropdown-item" onclick="ejecutarExportacion('csv')">⚪ Formato Plano (.CSV)</button>
                    </div>
                </div>
            </div>

            <div class="table-wrapper">
                @if($misMantenimientosPasados->isEmpty())
                    <div class="empty-state">
                        <p>No registras órdenes completadas asociadas a tu perfil o criterio de búsqueda.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>EQUIPO / HARDWARE</th>
                                <th>UBICACIÓN SEDE INST.</th>
                                <th>FECHA PROGRAMADA</th>
                                <th>FECHA DE CIERRE</th>
                                <th style="text-align: center;">¿A TIEMPO?</th>
                                <th style="text-align: center;">REPORTE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($misMantenimientosPasados as $m)
                                @php
                                    $badgeColor = $m->es_a_tiempo ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;';
                                    $textoVisualSLA = $m->es_a_tiempo ? 'SÍ' : 'NO';
                                @endphp
                                <tr class="row-clickable" data-modal-trigger
                                    data-id="{{ $m->ID_Mantenimiento }}"
                                    data-inv="N° {{ $m->Codigo_Inventario }}"
                                    data-hardware="{{ $m->Nombre_Tipo }} ({{ $m->Marca }} — {{ $m->Modelo }})"
                                    data-ubicacion="{{ $m->UbicacionFisicaSede }}"
                                    data-programada="{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}"
                                    data-cierre="{{ \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y g:i A') }}"
                                    data-atiempo="{{ $m->Sla_Texto_Inyectado }}"
                                    data-tecnico="{{ $m->TecnicoNombre }}"
                                    data-estadohw="{{ $m->EstadoHardwareTexto }}"
                                    data-accion="{{ $m->Accion_Realizada ?? 'Mantenimiento Correctivo aplicado estándar.' }}"
                                    data-observaciones="{{ $m->Observaciones_Tecnicas ?? 'Sin comentarios adicionales.' }}">
                                    
                                    <td>
                                        <strong>{{ $m->Codigo_Inventario }}</strong>
                                        <span class="td-muted" style="display:block; font-size:11px;">{{ $m->Nombre_Tipo }}</span>
                                    </td>
                                    <td class="td-muted">{{ $m->UbicacionFisicaSede }}</td>
                                    <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                    <td style="color:#047857; font-weight:500;">{{ \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y g:i A') }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge" style="{{ $badgeColor }} font-weight: bold; text-transform: uppercase;">{{ $textoVisualSLA }}</span>
                                    </td>
                                    <td style="text-align: center;" onclick="event.stopPropagation();">
                                        <button class="btn-pdf" onclick="imprimirHojaServicio('{{ $m->ID_Mantenimiento }}', '{{ $m->Codigo_Inventario }}', '{{ $m->Nombre_Tipo }} ({{ $m->Marca }} - {{ $m->Modelo }})', '{{ $m->UbicacionFisicaSede }}', '{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}', '{{ \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y g:i A') }}', '{{ $m->Sla_Texto_Inyectado }}', '{{ $m->Accion_Realizada ?? 'Correctivo Estándar' }}', '{{ $m->Observaciones_Tecnicas ?? 'Sin comentarios adicionales.' }}')">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            PDF
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        {{ $misMantenimientosPasados->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- MODAL INTERACTIVO RÁPIDO --}}
<div id="techAuditModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Mi Bitácora de Cierre — Mantenimiento #<span id="m_id"></span></h3>
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
                    <div class="info-label">Ubicación Destino</div>
                    <div class="info-value" id="m_ubicacion"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Técnico Operador</div>
                    <div class="info-value" id="m_tecnico"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha Programada</div>
                    <div class="info-value" id="m_programada"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha y Hora de Cierre</div>
                    <div class="info-value" id="m_cierre" style="color:#047857;"></div>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <div class="info-label" style="color:#6b21a8; font-weight:700;">Estatus Operativo Final del Hardware</div>
                <div class="action-box" style="background: #faf5ff; border-left: 4px solid #a855f7; color: #6b21a8; font-weight: 600; padding:10px 14px; border-radius:4px;">
                    <span id="m_estadohw"></span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <div class="info-label" style="color:#b91c1c; font-weight:700;">Cumplimiento del SLA Personal</div>
                <div class="info-value" id="m_atiempo" style="color: #b91c1c; font-size:15px; font-weight:700; margin-top:2px;"></div>
            </div>

            <div style="margin-bottom: 20px;">
                <div class="info-label">Acción Realizada</div>
                <div class="action-box" id="m_accion"></div>
            </div>
            
            <div>
                <div class="info-label">Observaciones y Diagnóstico Técnico Detallado</div>
                <div class="obs-box" id="m_observaciones"></div>
            </div>
        </div>
    </div>
</div>

{{-- CONTENEDOR PARA IMPRESIÓN --}}
<div id="printArea">
    <div class="p-header">
        <div>
            <div class="p-title">SIGEMPI — Hoja de Servicio Técnico</div>
            <div style="font-size:12px; color:#475569; margin-top:2px;">Sistema de Gestión de Parque Informático</div>
        </div>
        <div style="text-align: right; font-size: 11px; font-weight: bold; color: #da6714;">
            ORDEN DE CONTROL: #MANT-<span id="p_id"></span>
        </div>
    </div>

    <div class="p-grid">
        <div class="p-box">
            <div class="p-section-title">Datos del Hardware Intervenido</div>
            <div style="margin-bottom: 8px;"><div class="p-label">Código de Inventario Institucional</div><div class="p-value" id="p_inv"></div></div>
            <div><div class="p-label">Hardware / Componente Modelo</div><div class="p-value" id="p_hardware"></div></div>
        </div>
        <div class="p-box">
            <div class="p-section-title">Control Cronológico y Ubicación</div>
            <div style="margin-bottom: 8px;"><div class="p-label">Sede / Ubicación Física</div><div class="p-value" id="p_ubicacion"></div></div>
            <div style="display: flex; justify-content: space-between;">
                <div><div class="p-label">Fecha Programada</div><div class="p-value" id="p_programada"></div></div>
                <div><div class="p-label">Fecha de Cierre Oficial</div><div class="p-value" id="p_cierre"></div></div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 25px;">
        <div class="p-section-title">Métricas de Cumplimiento Técnico (SLA)</div>
        <div class="p-box" style="background: #f8fafc; border-left: 4px solid #da6714;">
            <div class="p-label">¿Se solventó la orden dentro del tiempo estipulado?</div>
            <div class="p-value" id="p_atiempo_print" style="color: #0f172a; font-size: 13px;"></div>
        </div>
    </div>

    <div><div class="p-section-title">Detalle de la Acción Técnico Ejecutada</div><div class="p-text-block" id="p_accion"></div></div>
    <div><div class="p-section-title">Diagnóstico Final y Observaciones de Soporte</div><div class="p-text-block" id="p_observaciones"></div></div>

    <div style="margin-top: 40px; background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 6px; font-size: 11px; color: #475569; line-height: 1.4;">
        <strong>Declaración de Conformidad:</strong> La presente hoja de servicio hace constar de manera legal y técnica que el hardware descrito fue intervenido siguiendo los protocolos de control de TI establecidos.
    </div>

    <div class="p-firmas-container">
        <div class="p-firma-block">
            <span style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: bold; color: #000000; display: block; margin-bottom: 2px;">Firma del Técnico Operador</span>
            <span id="p_user_signature" style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #475569; display: block;"></span>
        </div>
        <div class="p-firma-spacer"></div>
        <div class="p-firma-block">
            <span style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; font-weight: bold; color: #000000; display: block; margin-bottom: 2px;">Firma de Recibido Conforme</span>
            <span style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #475569; display: block;">Encargado de Área / Sede</span>
        </div>
    </div>
</div>

<script>
    function ejecutarExportacion(formato) {
        const searchInput = document.getElementById('currentSearch') ? document.getElementById('currentSearch').value : '';
        // Obtenemos de forma limpia el valor del tipo de hardware activo en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const tipoInput = urlParams.get('tipo') || '';
        
        let url = "{{ route('mantenimientos.index-tecnico') }}?exportar=" + formato;
        if (searchInput) url += "&search=" + encodeURIComponent(searchInput);
        if (tipoInput) url += "&tipo=" + encodeURIComponent(tipoInput);
        
        window.location.href = url;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('[data-modal-trigger]');
        const modal = document.getElementById('techAuditModal');
        const closeBtn = document.getElementById('closeModal');
        const dropBtn = document.getElementById('exportMenuBtn');
        const dropMenu = document.getElementById('exportDropdownMenu');

        if(dropBtn) {
            dropBtn.addEventListener('click', function(e) { e.stopPropagation(); dropMenu.classList.toggle('show'); });
        }
        document.addEventListener('click', () => { if (dropMenu) dropMenu.classList.remove('show'); });

        rows.forEach(row => {
            row.addEventListener('click', function() {
                document.getElementById('m_id').innerText = this.dataset.id;
                document.getElementById('m_inv').innerText = this.dataset.inv;
                document.getElementById('m_hardware').innerText = this.dataset.hardware;
                document.getElementById('m_ubicacion').innerText = this.dataset.ubicacion;
                document.getElementById('m_programada').innerText = this.dataset.programada;
                document.getElementById('m_cierre').innerText = this.dataset.cierre;
                document.getElementById('m_accion').innerText = this.dataset.accion;
                document.getElementById('m_observaciones').innerText = this.dataset.observaciones;
                
                document.getElementById('m_tecnico').innerText = this.dataset.tecnico;
                document.getElementById('m_estadohw').innerText = this.dataset.estadohw;
                document.getElementById('m_atiempo').innerText = this.dataset.atiempo;
                
                modal.classList.add('active');
            });
        });

        if(closeBtn) closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
    });

    function imprimirHojaServicio(id, inv, hardware, ubicacion, programada, cierre, atiempo, accion, observaciones) {
        document.getElementById('p_id').innerText = id;
        document.getElementById('p_inv').innerText = inv;
        document.getElementById('p_hardware').innerText = hardware;
        document.getElementById('p_ubicacion').innerText = ubicacion;
        document.getElementById('p_programada').innerText = programada;
        document.getElementById('p_cierre').innerText = cierre;
        document.getElementById('p_atiempo_print').innerText = atiempo;
        document.getElementById('p_accion').innerText = accion;
        document.getElementById('p_observaciones').innerText = observaciones;
        document.getElementById('p_user_signature').innerText = "{{ session('usuario', 'Técnico Operador') }}";
        
        window.print();
    }
</script>
@endsection