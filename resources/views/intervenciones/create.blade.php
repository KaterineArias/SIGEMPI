@extends('layouts.app')
@section('title', 'Registrar Intervención Técnica')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .form-container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            max-width: 700px;
            margin: 0 auto;
        }
        .meta-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            background: #f8fafc;
            border-left: 4px solid #f97316;
            padding: 1.25rem;
            border-radius: 4px 12px 12px 4px;
            margin-bottom: 2rem;
            font-size: 13px;
            line-height: 1.5;
        }
        .meta-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .meta-value {
            font-weight: 600;
            color: #1e293b;
            margin-top: 2px;
        }
        .form-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #f1f5f9;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #334155;
            font-size: 13.5px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: #fff;
            color: #1e293b;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }
        .form-control:disabled {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('body')
<div class="app-shell">

    {{-- BARRA LATERAL DE NAVEGACIÓN --}}
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
            Dashboard
        </a>

        <span class="nav-section-label">Mi trabajo</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Mis asignaciones
        </a>
        <a href="#" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Registrar intervención
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario', 'TE'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario', 'Técnico Oswaldo') }}</div>
                    <div class="user-role">{{ session('rol', 'Técnico') }}</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL DE LA PÁGINA --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Módulo de Cierre Operativo</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Ficha de Cierre de Mantenimiento</h1>
                <p>Completa el diagnóstico técnico detallado para asentar la solución en el historial y liberar el hardware.</p>
            </div>

            <div class="form-container">
                
                {{-- DESPLIEGUE DE ERRORES DE BASE DE DATOS --}}
                @if (session('error'))
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; color: #991b1b; font-size: 13px;">
                        <strong>Alerta del Servidor:</strong><br>{{ session('error') }}
                    </div>
                @endif

                {{-- RECUADRO INFORMATIVO DEL ELEMENTO DE HARDWARE --}}
                <div class="form-section-title">1. Información de la Orden de Trabajo</div>
                <div class="meta-info-grid">
                    <div>
                        <div class="meta-title">N° Orden Base</div>
                        <div class="meta-value">#{{ $mantenimiento->ID_Mantenimiento }}</div>
                    </div>
                    <div>
                        <div class="meta-title">Código Inventario</div>
                        <div class="meta-value">N° {{ $mantenimiento->Codigo_Inventario }}</div>
                    </div>
                    <div>
                        <div class="meta-title">Equipo Intervenido</div>
                        <div class="meta-value">{{ $mantenimiento->Marca }} {{ $mantenimiento->Modelo }}</div>
                    </div>
                    <div>
                        <div class="meta-title">Fecha Límite Programada</div>
                        <div class="meta-value" style="color: #c2410c;">{{ \Carbon\Carbon::parse($mantenimiento->Fecha_Programada)->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- FORMULARIO DE RECOPILACIÓN DE BITÁCORA --}}
                <form action="{{ route('intervenciones.store') }}" method="POST">
                    @csrf 
                    
                    <input type="hidden" name="ID_Mantenimiento" value="{{ $mantenimiento->ID_Mantenimiento }}">
                    <input type="hidden" name="ID_TecnicoIntervino" value="{{ session('id_user') ?? 1 }}">

                    <div class="form-section-title">2. Diagnóstico y Reporte Técnico Final</div>

                    {{-- Fecha y hora del registro de cierre visualizado --}}
                    <div class="form-group">
                        <label>Fecha y Hora de Cierre Automatizada</label>
                        <input type="text" class="form-control" disabled value="{{ \Carbon\Carbon::now()->format('d/m/Y g:i A') }} (Hora Servidor)">
                    </div>

                    {{-- Selector Obligatorio del Estado de Activos Informáticos --}}
                    <div class="form-group">
                        <label for="ID_EstadoEquipo">Estatus Operativo Final del Hardware *</label>
                        <select name="ID_EstadoEquipo" id="ID_EstadoEquipo" class="form-control" required>
                            <option value="2" {{ $mantenimiento->ID_EstadoEquipo == 2 ? 'selected' : '' }}>Activo (Reparado / Operativo en Producción)</option>
                            <option value="1" {{ $mantenimiento->ID_EstadoEquipo == 1 ? 'selected' : '' }}>Dañado (Fuera de Servicio / Requiere Reparación Mayor)</option>
                            <option value="3" {{ $mantenimiento->ID_EstadoEquipo == 3 ? 'selected' : '' }}>En Bodega (Reserva Técnica de la Institución)</option>
                        </select>
                    </div>

                    {{-- Acción Realizada --}}
                    <div class="form-group">
                        <label for="Accion_Realizada">Acción Realizada *</label>
                        <input type="text" name="Accion_Realizada" id="Accion_Realizada" value="{{ old('Accion_Realizada') }}" required 
                               class="form-control" placeholder="Ej: Reemplazo de disco duro por SSD, limpieza física interna y reinstalación de sistema operativo.">
                    </div>

                    {{-- Observaciones Técnicas --}}
                    <div class="form-group">
                        <label for="Observaciones_Tecnicas">Observaciones y Diagnóstico Técnico Detallado *</label>
                        <textarea name="Observaciones_Tecnicas" id="Observaciones_Tecnicas" rows="5" required class="form-control" 
                                  placeholder="Detalla minuciosamente los hallazgos del diagnóstico, las pruebas de rendimiento ejecutadas o recomendaciones críticas para el próximo ciclo preventivo..."></textarea>
                    </div>

                    {{-- ACCIONES DE ENVÍO --}}
                    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:2rem; border-top: 1px solid #e5e7eb; padding-top: 1.5rem;">
                        <a href="{{ route('dashboard.tecnico') }}" class="btn" style="background:#e2e8f0; color:#334155; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 13.5px; font-weight: 500;">Cancelar</a>
                        <button type="submit" class="btn btn-primary" style="background:#f97316; border-color:#f97316; padding: 10px 18px; font-size: 13.5px; color:#fff; font-weight: 600; border-radius: 8px; cursor: pointer;">Finalizar e Inyectar Bitácora</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection