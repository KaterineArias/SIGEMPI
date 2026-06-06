@extends('layouts.app')
@section('title', 'SIGEMPI — Historial Operativo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css?v=historial_premium_10.0') }}">
    <style>
        .kpi-grid { display: flex !important; gap: 12px; margin-bottom: 1.5rem; width: 100%; }
        .kpi-link-wrapper { text-transform: none; text-decoration: none; color: inherit; flex: 1 1 0px !important; min-width: 160px; }
        
        table { width: 100%; border-collapse: collapse; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        th { font-size: 11px !important; letter-spacing: 0.5px; text-transform: uppercase; color: #64748b; padding: 10px 12px !important; font-weight: 700; }
        td { padding: 10px 12px !important; font-size: 12px !important; color: #334155; vertical-align: middle; white-space: nowrap; }
        .fila-cliqueable-ficha { cursor: pointer; transition: background 0.15s; }
        .fila-cliqueable-ficha:hover { background: #f8fafc !important; }
        
        .inv-code-text { font-size: 12px !important; font-weight: 600 !important; color: #0f172a; }
        .hardware-subtext { display: block; font-size: 11px !important; color: #64748b; margin-top: 1px; }
        .badge { font-size: 10px !important; padding: 3px 8px !important; font-weight: 600 !important; border-radius: 4px !important; }
        
        .btn-export-premium { padding: 6px 12px; background: #16a34a; color: #fff; font-size: 12px; font-weight: 600; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; cursor: pointer; transition: background 0.15s; }
        .btn-export-premium:hover { background: #15803d; }
        .btn-export-blue { background: #0284c7; }
        .btn-export-blue:hover { background: #0369a1; }

        .premium-pagination-container { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px; font-size: 13px; color: #475569; }
        .pag-select-wrapper select, .filter-select-premium { padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; color: #1e293b; background: #fff; cursor: pointer; font-weight: 600; height: 32px; box-sizing: border-box; }
        .premium-nav-buttons { display: flex; gap: 6px; }
        .premium-nav-btn { padding: 6px 12px; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; color: #334155; font-weight: 600; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; }
        .premium-nav-btn.active { background: #da6714; border-color: #da6714; color: #fff; }
        .premium-nav-btn.disabled { color: #cbd5e1; border-color: #f1f5f9; background: #f8fafc; cursor: not-allowed; }

        .btn-clear-premium { padding: 6px 14px; background: #64748b; color: #ffffff; font-size: 13px; font-weight: 600; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; border: 1px solid #475569; cursor: pointer; transition: background 0.15s; height: 32px; box-sizing: border-box; }
        .btn-clear-premium:hover { background: #475569; color: #ffffff; }

        /* 👑 ALERTA COMPARTIDA UX: Banner institucional del Mes en Curso */
        .badge-period-banner { background: #f0fdf4; border: 1px dashed #22c55e; padding: 10px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; color: #166534; display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }

        .modal-action-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); display: flex; align-items: center; justify-content: center; z-index: 99999; opacity: 0; visibility: hidden; transition: all 0.25s ease-in-out; }
        .modal-action-overlay.active { opacity: 1; visibility: visible; }
        .m-act-header { background: #0f172a; color: #fff; padding: 1.25rem 1.5rem; }
        .m-act-body { padding: 1.5rem; }
        .form-group-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px; }
        .form-ctrl-field { display: flex; flex-direction: column; gap: 4px; }
        .form-ctrl-field label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 700; }
        .form-ctrl-field input, .form-ctrl-field textarea { padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; background: #f8fafc; color: #1e293b; }
        .m-act-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; }

        @media print {
            body * { visibility: hidden; }
            #historialModalHot, #historialModalHot * { visibility: visible; }
            #historialModalHot { position: absolute; left: 0; top: 0; width: 100%; }
            .m-act-header, .m-act-footer { display: none !important; }
            .modal-card { box-shadow: none !important; border: none !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
            input, textarea { border: none !important; background: transparent !important; color: #000 !important; padding: 4px 0 !important; font-size: 14px !important; }
            .form-ctrl-field label { color: #475569 !important; font-size: 10px !important; }
            hr { border-top: 1px solid #94a3b8 !important; }
        }
    </style>
@endpush

@section('body')
<div class="app-shell">
    
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;padding-bottom:8px">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>
        <span class="nav-section-label">Principal</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Mis Asignaciones
        </a>
        <span class="nav-section-label">Operaciones</span>
        <a href="{{ url('/dashboard/tecnico/mantenimientos') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Ver Historial
        </a>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Módulo de Historial e Inspección de Mantenimientos Cerrados</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Repositorio Histórico de Intervenciones</h1>
                <p>Auditoría completa de cierres de órdenes preventivas del operador técnico</p>
            </div>

            <div class="kpi-grid">
                <div class="kpi-link-wrapper">
                    <div class="kpi-card">
                        <div class="kpi-icon teal"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg></div>
                        <div class="kpi-value">{{ $kpis['total'] }}</div>
                        <div class="kpi-label">Total Mantenimientos Ejecutados</div>
                    </div>
                </div>
                <div class="kpi-link-wrapper">
                    <div class="kpi-card">
                        <div class="kpi-icon green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
                        <div class="kpi-value">{{ $kpis['operativos'] }}</div>
                        <div class="kpi-label">Equipos en Estado Operativo</div>
                    </div>
                </div>
                <div class="kpi-link-wrapper">
                    <div class="kpi-card">
                        <div class="kpi-icon orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                        <div class="kpi-value">{{ $kpis['sla_rate'] }}%</div>
                        <div class="kpi-label">Índice Eficiencia Cumplimiento SLA</div>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO CON FILTRO DE TEXTO, HARDWARE Y VIAJE EN EL TIEMPO MENSUAL --}}
            <div style="background:#fff; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap: wrap;">
                <form method="GET" action="{{ url('/dashboard/tecnico/mantenimientos') }}" id="formFiltrosHistorial" style="display:flex; gap:10px; margin:0; flex-grow:1; align-items:center; flex-wrap: wrap;">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    
                    <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Buscar por código de inventario..." style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; width: 200px; height: 32px; box-sizing: border-box;">
                    
                    <button type="submit" class="btn btn-primary" style="padding:0 16px; font-size:13px; border-radius:6px; height: 32px; font-weight: 600;">Filtrar</button>
                    
                    @if(!empty($search) || (request('tipo') && request('tipo') !== 'Todos') || request('mes') || request('anio'))
                        <a href="{{ url('/dashboard/tecnico/mantenimientos?per_page=' . $perPage) }}" class="btn-clear-premium">Limpiar Filtros</a>
                    @endif

                    <div style="border-left: 1px solid #cbd5e1; height: 24px; margin: 0 2px;"></div>

                    {{-- Selector de Hardware --}}
                    <select name="tipo" class="filter-select-premium" onchange="document.getElementById('formFiltrosHistorial').submit()">
                        <option value="Todos" {{ request('tipo') == 'Todos' ? 'selected' : '' }}>🖥️ Todo el Hardware</option>
                        <option value="Servidor" {{ request('tipo') == 'Servidor' ? 'selected' : '' }}>💾 Servidores</option>
                        <option value="Desktop" {{ request('tipo') == 'Desktop' ? 'selected' : '' }}>🖥️ Desktops</option>
                        <option value="Laptop" {{ request('tipo') == 'Laptop' ? 'selected' : '' }}>💻 Laptops</option>
                    </select>

                    <div style="border-left: 1px solid #cbd5e1; height: 24px; margin: 0 2px;"></div>

                    {{-- 👑 SELECTOR DE MESES PASADOS (UX REQUERIDA) --}}
                    <select name="mes" class="filter-select-premium" onchange="document.getElementById('formFiltrosHistorial').submit()">
                        <option value="01" {{ $mesSeleccionado == '01' ? 'selected' : '' }}>Enero</option>
                        <option value="02" {{ $mesSeleccionado == '02' ? 'selected' : '' }}>Febrero</option>
                        <option value="03" {{ $mesSeleccionado == '03' ? 'selected' : '' }}>Marzo</option>
                        <option value="04" {{ $mesSeleccionado == '04' ? 'selected' : '' }}>Abril</option>
                        <option value="05" {{ $mesSeleccionado == '05' ? 'selected' : '' }}>Mayo</option>
                        <option value="06" {{ $mesSeleccionado == '06' ? 'selected' : '' }}>Junio</option>
                        <option value="07" {{ $mesSeleccionado == '07' ? 'selected' : '' }}>Julio</option>
                        <option value="08" {{ $mesSeleccionado == '08' ? 'selected' : '' }}>Agosto</option>
                        <option value="09" {{ $mesSeleccionado == '09' ? 'selected' : '' }}>Septiembre</option>
                        <option value="10" {{ $mesSeleccionado == '10' ? 'selected' : '' }}>Octubre</option>
                        <option value="11" {{ $mesSeleccionado == '11' ? 'selected' : '' }}>Noviembre</option>
                        <option value="12" {{ $mesSeleccionado == '12' ? 'selected' : '' }}>Diciembre</option>
                    </select>

                    {{-- Selector de Año --}}
                    <select name="anio" class="filter-select-premium" onchange="document.getElementById('formFiltrosHistorial').submit()">
                        <option value="2026" {{ $anioSeleccionado == '2026' ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $anioSeleccionado == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ $anioSeleccionado == '2024' ? 'selected' : '' }}>2024</option>
                    </select>
                </form>

                <div style="display:flex; gap:8px;">
                    <a href="{{ url('/dashboard/tecnico/mantenimientos?exportar=excel&search='.$search.'&tipo='.request('tipo').'&mes='.$mesSeleccionado.'&anio='.$anioSeleccionado) }}" class="btn-export-premium">📥 Exportar Excel</a>
                    <a href="{{ url('/dashboard/tecnico/mantenimientos?exportar=word&search='.$search.'&tipo='.request('tipo').'&mes='.$mesSeleccionado.'&anio='.$anioSeleccionado) }}" class="btn-export-premium btn-export-blue">📥 Exportar Word</a>
                </div>
            </div>

            {{-- 👑 BANNER INFORMATIVO COMPARTIDO: Indica si es el mes en curso o una consulta histórica --}}
            @php
                $esMesActual = ($mesSeleccionado == date('m') && $anioSeleccionado == date('Y'));
                $nombreMeses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
            @endphp
            
            <div class="badge-period-banner" style="{{ $esMesActual ? 'background:#f0fdf4; border-color:#22c55e; color:#166534;' : 'background:#fef3c7; border-color:#d97706; color:#92400e;' }}">
                @if($esMesActual)
                    <span>📅</span> <b>Periodo Activo:</b> Mostrando exclusivamente auditoría del mes en curso ({{ $nombreMeses[$mesSeleccionado] }} {{ $anioSeleccionado }}).
                @else
                    <span>🔍</span> <b>Consulta Histórica:</b> Inspeccionando registros cerrados del mes de <b>{{ $nombreMeses[$mesSeleccionado] }} del año {{ $anioSeleccionado }}</b>.
                @endif
            </div>

            <div class="table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #e2e8f0; border-bottom:none; overflow:hidden; border-radius: 8px 8px 0 0;">
                @if($paginados->isEmpty())
                    <div style="padding: 4rem; text-align: center; color: #64748b;">
                        <p style="margin: 0; font-weight: 500;">No se registran órdenes de mantenimiento ejecutadas en el periodo seleccionado.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                <th style="width: 60px; text-align: center;">#</th>
                                <th>CÓDIGO INV.</th>
                                <th>HARDWARE ASOCIADO</th>
                                <th>UBICACIÓN SEDE DESTINO</th>
                                <th>FECHA PROGRAMADA</th>
                                <th>FECHA EJECUCIÓN (CIERRE)</th>
                                <th style="text-align: center;">¿A TIEMPO? (SLA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paginados as $index => $row)
                                <tr class="fila-cliqueable-ficha" onclick="verFichaHistorialExclusiva('{{ $row->ID_Mantenimiento }}')">
                                    <td align="center" style="font-weight:bold; color:#94a3b8;">{{ $paginados->firstItem() + $index }}</td>
                                    <td><span class="inv-code-text">{{ $row->Codigo_Inventario }}</span></td>
                                    <td>
                                        <span class="badge" style="background:#e0f2fe; color:#0369a1;">{{ $row->Nombre_Tipo }}</span>
                                        <span class="hardware-subtext">{{ $row->Marca }} — {{ $row->Modelo }}</span>
                                    </td>
                                    <td style="color:#475569;">{{ $row->UbicacionFisicaSede }}</td>
                                    <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($row->Fecha_Programada)->format('d/m/Y') }}</td>
                                    <td style="color:#16a34a; font-weight:600;">{{ \Carbon\Carbon::parse($row->Fecha_Cierre)->format('d/m/Y g:i A') }}</td>
                                    <td align="center">
                                        @php 
                                            $colorSLA = $row->es_a_tiempo 
                                                ? 'background:#d1fae5; color:#065f46; border:1px solid #a7f3d0;' 
                                                : 'background:#fee2e2; color:#991b1b; border:1px solid #fecaca;'; 
                                        @endphp
                                        <span class="badge" style="{{ $colorSLA }}">{{ $row->es_a_tiempo ? 'SÍ' : 'NO' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if(!$paginados->isEmpty())
                <div class="premium-pagination-container">
                    <div class="pag-select-wrapper">
                        Ver 
                        <select id="perPageSelectHistorial" onchange="cambiarFilasHistorial(this.value)">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        registros por página. (Mostrando del {{ $paginados->firstItem() }} al {{ $paginados->lastItem() }} de {{ $paginados->total() }})
                    </div>

                    <div class="premium-nav-buttons">
                        @if($paginados->onFirstPage())
                            <span class="premium-nav-btn disabled">◀ Anterior</span>
                        @else
                            <a href="{{ $paginados->appends(['search' => $search, 'tipo' => request('tipo'), 'mes' => $mesSeleccionado, 'anio' => $anioSeleccionado, 'per_page' => $perPage])->previousPageUrl() }}" class="premium-nav-btn">◀ Anterior</a>
                        @endif

                        @foreach($paginados->getUrlRange(max(1, $paginados->currentPage() - 1), min($paginados->lastPage(), $paginados->currentPage() + 1)) as $page => $url)
                            <a href="{{ $url }}&search={{ $search }}&tipo={{ request('tipo') }}&mes={{ $mesSeleccionado }}&anio={{ $anioSeleccionado }}&per_page={{ $perPage }}" class="premium-nav-btn {{ $page == $paginados->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if($paginados->hasMorePages())
                            <a href="{{ $paginados->appends(['search' => $search, 'tipo' => request('tipo'), 'mes' => $mesSeleccionado, 'anio' => $anioSeleccionado, 'per_page' => $perPage])->nextPageUrl() }}" class="premium-nav-btn">Siguiente ▶</a>
                        @else
                            <span class="premium-nav-btn disabled">Siguiente ▶</span>
                        @endif
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

{{-- MODAL DE LECTURA DE INTERVENCIÓN HISTÓRICA COMPLETAMENTE AISLADO --}}
<div id="historialModalHot" class="modal-action-overlay">
    <div class="modal-card" style="width:100%; max-width:650px; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);">
        <div class="m-act-header">
            <h3 style="margin:0; font-size:1.25rem; font-weight:600;">Consulta Histórica Auditable de Trabajo</h3>
        </div>
        <div class="m-act-body">
            <div class="form-group-row">
                <div class="form-ctrl-field"><label>ID Mantenimiento</label><input type="text" id="h_id" disabled></div>
                <div class="form-ctrl-field"><label>Código Inventario Hardware</label><input type="text" id="h_inv" disabled></div>
            </div>
            <div class="form-group-row">
                <div class="form-ctrl-field"><label>Hardware / Componente</label><input type="text" id="h_hw" disabled></div>
                <div class="form-ctrl-field"><label>Fecha y Hora Ejecución Cierre</label><input type="text" id="h_cierre" disabled style="font-weight:bold; color:#16a34a;"></div>
            </div>
            <div class="form-group-row">
                <div class="form-ctrl-field"><label>Sede Física / Destino</label><input type="text" id="h_sede" disabled></div>
                <div class="form-ctrl-field"><label>Fecha Programación Planificada</label><input type="text" id="h_programada" disabled></div>
            </div>
            <div class="form-group-row">
                <div class="form-ctrl-field" style="grid-column: span 2;"><label>Cumplimiento SLA Estipulado</label><input type="text" id="h_sla" disabled style="font-weight:bold;"></div>
            </div>
            <hr style="border:0; border-top:1px solid #e2e8f0; margin:15px 0;">
            <div class="form-ctrl-field" style="margin-bottom:12px;"><label>Acción Técnica Registrada</label><input type="text" id="h_accion" disabled style="font-weight: 600;"></div>
            <div class="form-ctrl-field"><label>Diagnóstico & Observaciones de Justificación</label><textarea id="h_obs" disabled rows="3"></textarea></div>
        </div>
        <div class="m-act-footer" style="gap:10px;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('historialModalHot').classList.remove('active')">Cerrar Consulta</button>
            <button type="button" class="btn-modal-print" style="padding: 8px 18px; background: #0284c7; color: #fff; font-size: 13px; font-weight: bold; border-radius: 6px; border: none; cursor: pointer;" onclick="window.print()">🖨️ Imprimir Acta de Conformidad</button>
        </div>
    </div>
</div>

<script>
    function cambiarFilasHistorial(cantidad) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', cantidad);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function verFichaHistorialExclusiva(id) {
        fetch("{{ route('dashboard.tecnico') }}?get_detalle_id=" + id, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('h_id').value = "MANT-" + data.id;
                document.getElementById('h_inv').value = data.inventario;
                document.getElementById('h_hw').value = data.hardware;
                document.getElementById('h_sede').value = data.ubicacion;
                document.getElementById('h_cierre').value = data.fecha_cierre;
                document.getElementById('h_sla').value = data.cumplimiento_sla;
                document.getElementById('h_programada').value = data.fecha_programada;
                
                const localAccion = localStorage.getItem('accion_m_' + data.id);
                const localObs = localStorage.getItem('obs_m_' + data.id);
                
                document.getElementById('h_accion').value = localAccion ? localAccion : data.accion_realizada;
                document.getElementById('h_obs').value = localObs ? localObs : data.observaciones;

                document.getElementById('historialModalHot').classList.add('active');
            }
        });
    }
</script>
@endsection