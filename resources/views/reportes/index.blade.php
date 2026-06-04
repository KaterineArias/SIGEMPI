@extends('layouts.app')
@section('title', 'Historial de Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
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
    </style>
@endpush

@section('body')
<div class="app-shell">

    {{-- ── SIDEBAR ADAPTATIVO POR ROL (SOLUCIÓN ANOMALÍA MENÚ) ── --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md);">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        @if(session('rol') === 'Coordinador')
            {{-- Menú exclusivo para el Coordinador --}}
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.coordinador') }}" class="nav-link">Dashboard</a>
            <span class="nav-section-label">Gestión</span>
            <a href="{{ route('equipos.index') }}" class="nav-link">Equipos</a>
            <a href="{{ route('usuarios.index') }}" class="nav-link">Usuarios</a>
            <a href="{{ route('mantenimientos.index') }}" class="nav-link active">Mantenimientos</a>
            <span class="nav-section-label">Reportes</span>
            <a href="{{ route('reportes.index') }}" class="nav-link">Reportes</a>
        @else
            {{-- Menú limpio exclusivo para ti (Perfil Técnico) --}}
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.tecnico') }}" class="nav-link">Mis Asignaciones</a>
            <span class="nav-section-label">Operaciones</span>
            <a href="{{ route('mantenimientos.index') }}" class="nav-link active">Ver Historial</a>
        @endif

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario', 'TE'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Usuario') }}</div>
                    <div class="user-role">{{ session('rol', 'Técnico') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
                @csrf
                <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center;font-size:11px;">Cerrar sesión</button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Módulo de Consulta Histórica</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Programación de Mantenimientos</h1>
                <p>Gestión y seguimiento de intervenciones técnicas históricas generales</p>
            </div>

            {{-- FILTRADO Y BUSCADOR DE INVENTARIO --}}
            <div style="background:#fff; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:1.5rem; display:flex; align-items:center; gap:12px;">
                <form method="GET" action="{{ route('mantenimientos.index') }}" style="display:flex; gap:8px; margin:0; flex-grow:1; flex-wrap:wrap;">
                    <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Buscar por código de inventario o marca..." style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; flex-grow:1; min-width:250px;">
                    
                    <select name="tecnico_id" onchange="this.form.submit()" style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; background:#fff; cursor:pointer;">
                        <option value="">— Filtrar por Técnico —</option>
                        @foreach($tecnicos as $t)
                            <option value="{{ $t->ID_User }}" {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>{{ $t->Usuario }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:13px; border-radius:6px;">Buscar</button>
                    @if($search || request('tecnico_id'))
                        <a href="{{ route('mantenimientos.index') }}" style="padding:6px 12px; border:1px solid #cbd5e1; background:#fff; text-decoration:none; color:#64748b; font-size:13px; border-radius:6px; display:inline-flex; align-items:center;">Limpiar</a>
                    @endif
                </form>
            </div>

            {{-- TABLA DATA INTERACTIVA --}}
            <div class="table-wrapper">
                @if($mantenimientos->isEmpty())
                    <div class="empty-state">
                        <p>No se encontraron registros en la bitácora general.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>EQUIPO</th>
                                <th>TÉCNICO ASIGNADO</th>
                                <th>FECHA PROGRAMADA</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mantenimientos as $mant)
                                @php
                                    $meta = strtotime($mant->Fecha_Reprogramacion ?? $mant->Fecha_Programada);
                                    $cierre = isset($mant->Fecha_Cierre) ? strtotime($mant->Fecha_Cierre) : null;
                                    $SLA_ModalTexto = is_null($cierre) ? 'Pendiente (Sin registro de cierre)' : (($cierre <= ($meta + 86399)) ? 'SÍ (Dentro del plazo del SLA)' : 'NO (Fuera del margen del SLA)');
                                    
                                    $txtEstadoEquipo = 'Activo (Reparado / Operativo)';
                                    if(isset($mant->equipo->ID_Estado) && $mant->equipo->ID_Estado == 1){
                                        $txtEstadoEquipo = 'Dañado (Fuera de Servicio / Declarado Inoperante)';
                                    }
                                @endphp
                                <tr class="row-clickable" data-modal-trigger
                                    data-id="{{ $mant->ID_Mantenimiento }}"
                                    data-inv="N° {{ $mant->equipo->Codigo_Inventario }}"
                                    data-hardware="{{ $mant->equipo->Tipo }} ({{ $mant->equipo->Marca }} - {{ $mant->equipo->Modelo }})"
                                    data-ubicacion="{{ $mant->equipo->Ubicacion }}"
                                    data-tecnico="{{ $mant->tecnico->Usuario }}"
                                    data-programada="{{ \Carbon\Carbon::parse($mant->Fecha_Programada)->format('d/m/Y') }}"
                                    data-cierre="{{ isset($mant->Fecha_Cierre) ? \Carbon\Carbon::parse($mant->Fecha_Cierre)->format('d/m/Y g:i A') : '—' }}"
                                    data-atiempo="{{ $SLA_ModalTexto }}"
                                    data-estatusequipo="{{ $txtEstadoEquipo }}"
                                    data-accion="{{ $mant->Accion_Realizada ?? 'Mantenimiento preventivo estándar aplicado.' }}"
                                    data-observaciones="{{ $mant->Observaciones_Tecnicas ?? 'El técnico no ingresó comentarios adicionales.' }}">
                                    
                                    <td>
                                        <strong>{{ $mant->equipo->Codigo_Inventario }}</strong>
                                        <span class="td-muted" style="display:block; font-size:11px;">{{ $mant->equipo->Tipo }}</span>
                                    </td>
                                    <td>{{ $mant->tecnico->Usuario }}</td>
                                    <td>{{ \Carbon\Carbon::parse($mant->Fecha_Programada)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge" style="background: {{ $mant->Estado_Mantenimiento === 'Completado' ? '#d1fae5; color:#065f46;' : '#e0f2fe; color:#0369a1;' }}">
                                            {{ $mant->Estado_Mantenimiento }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        {{ $mantenimientos->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- 📄 FICHA INTERACTIVA DE AUDITORÍA HISTÓRICA --}}
<div id="techAuditModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Ficha de Auditoría — Registro #MANT-<span id="m_id"></span></h3>
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
                <div class="info-item" style="grid-column: span 2;">
                    <div class="info-label">Ubicación Física Inst.</div>
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
                    <div class="info-label">Fecha de Cierre</div>
                    <div class="info-value" id="m_cierre"></div>
                </div>
                <div class="info-item" style="grid-column: span 2; background: #faf5ff; border-left: 3px solid #a855f7; padding: 6px 12px;">
                    <div class="info-label" style="color: #6b21a8;">Estatus Operativo Final del Hardware</div>
                    <div class="info-value" id="m_estatusequipo" style="color: #581c87;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <div class="info-label">Control de Tiempos (SLA)</div>
                <div id="m_atiempo" style="font-weight: bold; margin-top: 4px;"></div>
            </div>
            <div>
                <div class="info-label">Acción Realizada</div>
                <div class="action-box" id="m_accion"></div>
            </div>
            <div>
                <div class="info-label">Diagnóstico & Observaciones Registradas</div>
                <div class="obs-box" id="m_observaciones"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('[data-modal-trigger]');
        const modal = document.getElementById('techAuditModal');
        const closeBtn = document.getElementById('closeModal');

        rows.forEach(row => {
            row.addEventListener('click', function() {
                document.getElementById('m_id').innerText = this.dataset.id;
                document.getElementById('m_inv').innerText = this.dataset.inv;
                document.getElementById('m_hardware').innerText = this.dataset.hardware;
                document.getElementById('m_ubicacion').innerText = this.dataset.ubicacion;
                document.getElementById('m_tecnico').innerText = this.dataset.tecnico;
                document.getElementById('m_programada').innerText = this.dataset.programada;
                document.getElementById('m_cierre').innerText = this.dataset.cierre;
                document.getElementById('m_estatusequipo').innerText = this.dataset.estatusequipo;
                document.getElementById('m_accion').innerText = this.dataset.accion;
                document.getElementById('m_observaciones').innerText = this.dataset.observaciones;

                const atiempoContainer = document.getElementById('m_atiempo');
                atiempoContainer.innerText = this.dataset.atiempo;
                if(this.dataset.atiempo.includes('SÍ')) {
                    atiempoContainer.style.color = '#065f46';
                } else if(this.dataset.atiempo.includes('Pendiente')) {
                    atiempoContainer.style.color = '#475569';
                } else {
                    atiempoContainer.style.color = '#991b1b';
                }

                modal.classList.add('active');
            });
        });

        closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('active'); });
    });
</script>
@endsection