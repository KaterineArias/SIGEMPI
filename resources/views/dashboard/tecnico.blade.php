@extends('layouts.app')
@section('title', 'Bandeja Técnica Operativa')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .kpi-grid { 
            display: flex !important; 
            gap: 12px; 
            margin-bottom: 1.5rem; 
            width: 100%;
        }
        .kpi-link-wrapper { 
            text-transform: none; 
            text-decoration: none; 
            color: inherit; 
            flex: 1 1 0px !important; 
            min-width: 160px;
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
            cursor: pointer; 
        }
        .kpi-link-wrapper:hover { transform: translateY(-3px); }
        
        .kpi-card {
            height: 100% !important;
            box-sizing: border-box !important;
        }
        .kpi-card.kpi-active-filter { 
            border: 2px solid #da6714 !important; 
            background: #fffbf7; 
            box-shadow: 0 10px 15px -3px rgba(218, 103, 20, 0.12) !important; 
        }

        /* 👑 FUENTES E INTERFAZ INSTITUCIONAL COMPACTA DE PRIMER MUNDO */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        th {
            font-size: 11px !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #64748b;
            padding: 10px 12px !important;
            font-weight: 700;
        }
        td {
            padding: 10px 12px !important;
            font-size: 12px !important;
            color: #334155;
            vertical-align: middle;
            white-space: nowrap; /* Evita rupturas artificiales de línea */
        }
        .inv-code-text {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #0f172a;
            letter-spacing: -0.2px;
        }
        .hardware-subtext {
            display: block; 
            font-size: 11px !important; 
            color: #64748b; 
            margin-top: 1px;
            font-weight: 400;
        }
        .badge {
            font-size: 10px !important;
            padding: 3px 8px !important;
            font-weight: 600 !important;
            border-radius: 4px !important;
        }

        .btn-action-atender {
            padding: 5px 12px; 
            background: #da6714; 
            color: #fff; 
            border-radius: 5px;
            font-size: 11px; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 4px; 
            border: none; 
            cursor: pointer; 
            transition: background 0.15s;
        }
        .btn-action-atender:hover { background: #b04f0d; }
        
        .modal-action-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px);
            display: flex; align-items: center; justify-content: center; z-index: 99999;
            opacity: 0; visibility: hidden; transition: all 0.25s ease-in-out;
        }
        .modal-action-overlay.active { opacity: 1; visibility: visible; }
        
        .m-act-header { background: #0f172a; color: #fff; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .m-act-header h3 { margin: 0; font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .m-act-close { background: transparent; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer; transition: color 0.2s; }
        .m-act-close:hover { color: #fff; }
        
        .m-act-body { padding: 1.5rem; }
        .form-group-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px; }
        .form-ctrl-field { display: flex; flex-direction: column; gap: 4px; }
        .form-ctrl-field label { font-size: 11px; text-transform: uppercase; color: #475569; font-weight: 700; }
        .form-ctrl-field input, .form-ctrl-field select, .form-ctrl-field textarea {
            padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; color: #1e293b; font-family: inherit; background: #fff;
        }
        .form-ctrl-field input:disabled { background: #f8fafc; color: #64748b; cursor: not-allowed; }
        .form-ctrl-field textarea { resize: vertical; min-height: 80px; }
        .m-act-footer { padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; }
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
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Mis Asignaciones
        </a>
        <span class="nav-section-label">Operaciones</span>
        <a href="{{ route('mantenimientos.index-tecnico') }}" class="nav-link">
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

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Módulo de Intervención y Cierre Técnico de Campo</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Hola, {{ session('usuario', 'Técnico') }}</h1>
                <p>{{ $fechaTextoEncabezado ?? 'Bandeja integral de control de mantenimiento e intervenciones — Entorno Operativo' }}</p>
            </div>

            @php 
                $allVars = get_defined_vars();
                $statsGrid = $allVars['panelStats'] ?? $allVars['panelstats'] ?? [
                    'pendientes' => 0, 'este_mes' => 0, 'cerrados' => 0, 'vencidos' => 0, 'criticos' => 0, 'seguros' => 0
                ];
                $loopData = $allVars['misAsignacionesPendientes'] ?? collect();
                $activeFilter = $allVars['filtro'] ?? 'Asignados';
                $searchQuery = $allVars['search'] ?? '';
            @endphp

            <div class="kpi-grid">
                <a href="{{ route('dashboard.tecnico', ['filtro' => 'Asignados', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'Asignados' ? 'kpi-active-filter' : '' }}">
                        <div class="kpi-icon orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
                        <div class="kpi-value">{{ $statsGrid['pendientes'] }}</div>
                        <div class="kpi-label">Mis asignaciones pendientes</div>
                    </div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'TodoMes', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'TodoMes' ? 'kpi-active-filter' : '' }}">
                        <div class="kpi-icon teal"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="kpi-value">{{ $statsGrid['este_mes'] }}</div>
                        <div class="kpi-label">Asignadas este mes</div>
                    </div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'Completados', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'Completados' ? 'kpi-active-filter' : '' }}">
                        <div class="kpi-icon green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 4 12 14.01 9 11.01"/><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/></svg></div>
                        <div class="kpi-value">{{ $statsGrid['cerrados'] }}</div>
                        <div class="kpi-label">Mis mantenimientos cerrados</div>
                    </div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'Vencidos', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'Vencidos' ? 'kpi-active-filter' : '' }}" style="border-bottom: 4px solid #ef4444;">
                        <div class="kpi-value" style="color: #ef4444;">{{ $statsGrid['vencidos'] }}</div>
                        <div class="kpi-label">Alertas: Vencidas (Rojo)</div>
                    </div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'Criticos', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'Criticos' ? 'kpi-active-filter' : '' }}" style="border-bottom: 4px solid #f97316;">
                        <div class="kpi-value" style="color: #f97316;">{{ $statsGrid['criticos'] }}</div>
                        <div class="kpi-label">Alertas: Críticas Hoy (Naranja)</div>
                    </div>
                </a>

                <a href="{{ route('dashboard.tecnico', ['filtro' => 'Seguros', 'search' => $searchQuery]) }}" class="kpi-link-wrapper">
                    <div class="kpi-card {{ $activeFilter === 'Seguros' ? 'kpi-active-filter' : '' }}" style="border-bottom: 4px solid #22c55e;">
                        <div class="kpi-value" style="color: #22c55e;">{{ $statsGrid['seguros'] }}</div>
                        <div class="kpi-label">Alertas: Margen Seguro (Verde)</div>
                    </div>
                </a>
            </div>

            {{-- BUSCADOR REACTIVO --}}
            <div style="background:#fff; padding:12px 16px; border-radius:8px; border:1px solid #e2e8f0; margin-bottom:1rem; display:flex; align-items:center; gap:12px;">
                <form method="GET" action="{{ route('dashboard.tecnico') }}" style="display:flex; gap:8px; margin:0; flex-grow:1;">
                    <input type="hidden" name="filtro" value="{{ $activeFilter }}">
                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="🔍 Filtrar listado actual por código de inventario..." style="padding:6px 12px; border-radius:6px; border:1px solid #cbd5e1; font-size:13px; flex-grow:1;">
                    <button type="submit" class="btn btn-primary" style="padding:6px 16px; font-size:13px; border-radius:6px;">Filtrar Grilla</button>
                    @if(isset($searchQuery) && $searchQuery !== '')
                        <a href="{{ route('dashboard.tecnico', ['filtro' => $activeFilter]) }}" style="padding:6px 12px; border:1px solid #cbd5e1; background:#fff; text-decoration:none; color:#64748b; font-size:13px; border-radius:6px; display:inline-flex; align-items:center;">Limpiar</a>
                    @endif
                </form>
            </div>

            <div style="font-size: 13px; font-weight: bold; color: #475569; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                Listado Filtrado: <span style="color:#da6714;">{{ $activeFilter === 'Asignados' ? 'Pendientes de Cierre' : ($activeFilter === 'TodoMes' ? 'Asignadas este mes' : $activeFilter) }}</span>
            </div>

            <div class="table-wrapper">
                @if($loopData->isEmpty())
                    <div class="empty-state" style="padding: 4rem 2rem; text-align: center; color: #64748b; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <p style="font-size: 14px; font-weight: 500; margin: 0;">No se registraron órdenes asociadas en este segmento operativo.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">#</th>
                                <th>CÓDIGO INV.</th>
                                <th>HARDWARE ASOCIADO</th>
                                <th>UBICACIÓN SEDE DESTINO</th>
                                <th>FECHA PROGRAMADA</th>
                                @if($activeFilter === 'Completados' || (isset($loopData[0]) && $loopData[0]->ID_EstadoMantenimiento == 2))
                                    <th>FECHA CIERRE</th>
                                @endif
                                <th style="text-align: center;">ESTADO</th>
                                <th style="text-align: center;">{{ $activeFilter === 'Completados' || (isset($loopData[0]) && $loopData[0]->ID_EstadoMantenimiento == 2) ? '¿A TIEMPO?' : 'INTERVENCION' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loopData as $index => $row)
                                <tr @if($row->ID_EstadoMantenimiento == 2) style="cursor:pointer;" onclick="consultarFichaBitacora('{{ $row->ID_Mantenimiento }}')" @endif>
                                    <td align="center" style="color:#94a3b8; font-weight:bold;">{{ $index + 1 }}</td>
                                    <td><span class="inv-code-text">{{ $row->Codigo_Inventario }}</span></td>
                                    <td>
                                        <span class="badge" style="background:#e0f2fe; color:#0369a1;">{{ $row->Nombre_Tipo }}</span>
                                        <span class="hardware-subtext">{{ $row->Marca }} — {{ $row->Modelo }}</span>
                                    </td>
                                    <td style="color:#475569;">{{ $row->UbicacionFisicaSede }}</td>
                                    <td style="font-weight:600;">{{ \Carbon\Carbon::parse($row->Fecha_Programada)->format('d/m/Y') }}</td>
                                    
                                    @if($row->ID_EstadoMantenimiento == 2)
                                        <td style="color:#16a34a; font-weight:600;">{{ $row->Fecha_Cierre ? \Carbon\Carbon::parse($row->Fecha_Cierre)->format('d/m/Y g:i A') : 'No Registrada' }}</td>
                                    @endif

                                    <td style="text-align: center;">
                                        @if($row->ID_EstadoMantenimiento == 2)
                                            <span class="badge" style="background:#d1fae5; color:#065f46;">COMPLETADO</span>
                                        @else
                                            <span class="badge" style="background: #fef3c7; color: #92400e; text-transform: uppercase;">{{ $row->Nombre_EstadoMantenimiento }}</span>
                                        @endif
                                    </td>

                                    <td style="text-align: center;">
                                        @if($row->ID_EstadoMantenimiento == 2)
                                            @php $colorSLA = isset($row->es_a_tempo) && $row->es_a_tempo ? 'background:#d1fae5; color:#065f46;' : 'background:#fee2e2; color:#991b1b;'; @endphp
                                            <span class="badge" style="{{ $colorSLA }}">{{ isset($row->es_a_tempo) && $row->es_a_tempo ? 'SÍ' : 'NO' }}</span>
                                        @else
                                            <button type="button" class="btn-action-atender" 
                                                    onclick="event.stopPropagation(); abrirModalIntervencion(
                                                        '{{ addslashes($row->ID_Mantenimiento) }}', 
                                                        '{{ addslashes($row->Codigo_Inventario) }}', 
                                                        '{{ addslashes($row->Nombre_Tipo) }}', 
                                                        '{{ addslashes($row->UbicacionFisicaSede) }}', 
                                                        '{{ \Carbon\Carbon::parse($row->Fecha_Programada)->format('d/m/Y') }}'
                                                    )">
                                                Atender Orden
                                            </button>
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

{{-- MODAL INTERACTIVO INSTITUCIONAL GENERAL --}}
<div id="intervencionModalHot" class="modal-action-overlay">
    <div class="modal-card" style="width: 100%; max-width: 650px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);">
        <form method="POST" id="formIntervencionDinamico" action="">
            @csrf
            @method('PUT')
            
            <div class="m-act-header">
                <h3 id="modal_titulo_dinamico">Mi Bitácora de Cierre — Mantenimiento</h3>
                <button type="button" class="m-act-close" onclick="cerrarModalIntervencion()">✕</button>
            </div>
            
            <div class="m-act-body">
                <div class="form-group-row">
                    <div class="form-ctrl-field">
                        <label>ID Registro Orden</label>
                        <input type="text" id="modal_display_id" disabled>
                    </div>
                    <div class="form-ctrl-field">
                        <label>Código Inventario Hardware</label>
                        <input type="text" id="modal_display_inv" disabled>
                    </div>
                </div>
                
                <div class="form-group-row">
                    <div class="form-ctrl-field">
                        <label>Hardware / Componente</label>
                        <input type="text" id="modal_display_hardware" disabled>
                    </div>
                    <div class="form-ctrl-field">
                        <label>Fecha de Programación</label>
                        <input type="text" id="modal_display_fecha" disabled>
                    </div>
                </div>

                <div class="form-ctrl-field" style="margin-bottom: 12px;">
                    <label>Sede Física / Ubicación Destino</label>
                    <input type="text" id="modal_display_ubicacion" disabled>
                </div>

                <hr style="border:0; border-top:1px solid #e2e8f0; margin:15px 0;">

                <div id="seccion_campos_editables">
                    <div class="form-ctrl-field" style="margin-bottom: 12px;">
                        <label>Estatus Operativo Final del Hardware <span style="color:#ef4444;">*</span></label>
                        <select name="ID_EstadoEquipo" id="modal_input_estado" required style="font-weight: 600; color:#0f172a;">
                            <option value="2">🟢 Operativo (Activo / En Servicio / Reparado)</option>
                            <option value="1">🔴 Dañado (Fuera de Servicio / Requiere Cambio Estructural)</option>
                        </select>
                    </div>

                    <div class="form-ctrl-field" style="margin-bottom: 12px;">
                        <label>Acción Técnica Realizada <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="Accion_Realizada" id="modal_input_accion" required placeholder="Ej: Limpieza de inyectores y calibración de rodillos.">
                    </div>

                    <div class="form-ctrl-field">
                        <label>Diagnóstico & Observaciones de Justificación <span style="color:#ef4444;">*</span></label>
                        <textarea name="Observaciones_Tecnicas" id="modal_input_observaciones" required placeholder="Describe detalladamente los hallazgos técnicos encontrados..."></textarea>
                    </div>
                </div>

                <div id="seccion_vista_ficha_historica" style="display: none; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px;">
                    <div style="margin-bottom: 8px;"><b>Fecha Real de Cierre:</b> <span id="ficha_txt_cierre"></span></div>
                    <div style="margin-bottom: 8px;"><b>Estado del Hardware Post-Intervención:</b> <span id="ficha_txt_estado_hw"></span></div>
                    <div style="margin-bottom: 8px;"><b>Cumplimiento de SLA Estipulado:</b> <span id="ficha_txt_sla"></span></div>
                    <div style="margin-bottom: 8px;"><b>Acción Técnica Ejecutada:</b> <div id="ficha_txt_accion" style="background:#fff; padding:6px; border:1px solid #cbd5e1; border-radius:4px; margin-top:4px; font-weight:600;"></div></div>
                    <div><b>Observaciones y Hallazgos Registrados:</b> <div id="ficha_txt_observaciones" style="background:#fff; padding:6px; border:1px solid #cbd5e1; border-radius:4px; margin-top:4px;"></div></div>
                </div>
            </div>

            <div class="m-act-footer">
                <button type="button" class="btn btn-ghost" onclick="cerrarModalIntervencion()">Cerrar</button>
                <button type="submit" id="btn_modal_guardar_accion" class="btn btn-primary" style="padding: 8px 20px; background:#da6714; color:#fff; font-size:13px; font-weight:bold; border-radius:6px; border:none; cursor:pointer;">
                    💾 Guardar y Cierre Orden
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalIntervencion(id, inv, hardware, ubicacion, fecha) {
        document.getElementById('modal_titulo_dinamico').innerText = "Mi Bitácora de Cierre — Mantenimiento #" + id;
        document.getElementById('seccion_campos_editables').style.display = "block";
        document.getElementById('seccion_vista_ficha_historica').style.display = "none";
        document.getElementById('btn_modal_guardar_accion').style.display = "block";

        const baseRoute = "{{ url('mantenimientos') }}/" + id;
        document.getElementById('formIntervencionDinamico').setAttribute('action', baseRoute);
        
        document.getElementById('modal_display_id').value = "MANT-" + id;
        document.getElementById('modal_display_inv').value = inv;
        document.getElementById('modal_display_hardware').value = hardware;
        document.getElementById('modal_display_ubicacion').value = ubicacion;
        document.getElementById('modal_display_fecha').value = fecha;
        
        document.getElementById('intervencionModalHot').classList.add('active');
    }

    function consultarFichaBitacora(id) {
        fetch("{{ route('dashboard.tecnico') }}?get_detalle_id=" + id, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('modal_titulo_dinamico').innerText = "Mi Bitácora de Cierre — Mantenimiento #" + data.id;
                document.getElementById('modal_display_id').value = "MANT-" + data.id;
                document.getElementById('modal_display_inv').value = data.inventario;
                document.getElementById('modal_display_hardware').value = data.hardware;
                document.getElementById('modal_display_ubicacion').value = data.ubicacion;
                document.getElementById('modal_display_fecha').value = data.fecha_programada;

                document.getElementById('seccion_campos_editables').style.display = "none";
                document.getElementById('btn_modal_guardar_accion').style.display = "none";
                document.getElementById('seccion_vista_ficha_historica').style.display = "block";

                document.getElementById('ficha_txt_cierre').innerText = data.fecha_cierre;
                document.getElementById('ficha_txt_estado_hw').innerText = data.estado_hardware;
                document.getElementById('ficha_txt_sla').innerText = data.cumplimiento_sla;
                document.getElementById('ficha_txt_accion').innerText = data.accion_realizada;
                document.getElementById('ficha_txt_observaciones').innerText = data.observaciones;

                document.getElementById('intervencionModalHot').classList.add('active');
            }
        });
    }

    function cerrarModalIntervencion() {
        document.getElementById('intervencionModalHot').classList.remove('active');
    }

    window.addEventListener('click', function(e) {
        const modalOverlay = document.getElementById('intervencionModalHot');
        if (e.target === modalOverlay) {
            cerrarModalIntervencion();
        }
    });
</script>
@endsection