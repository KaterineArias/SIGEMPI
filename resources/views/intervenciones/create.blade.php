@extends('layouts.app')
@section('title', 'Registrar Intervención')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    {{-- BARRA LATERAL --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64"
                    style="object-fit:contain;border-radius:var(--radius-md);">
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
        <a href="{{ route('equipos.index') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Ver equipos
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

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Módulo de Intervenciones</span>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Registrar Intervención Técnica</h1>
                <p>Completa el informe técnico para cerrar el mantenimiento asignado.</p>
            </div>

            <div style="background: white; border: 1px solid var(--border-color, #e5e7eb); padding: var(--space-5); border-radius: var(--radius-lg); max-width: 650px; margin: 0 auto; box-shadow: var(--shadow-md);">
                
                {{-- Ficha Técnica del Equipo --}}
                <div style="background: #f8fafc; padding: var(--space-3); border-radius: var(--radius-md); margin-bottom: var(--space-4); border-left: 4px solid #f97316; font-size: var(--text-sm); line-height: 1.6;">
                    <p style="margin: 0; color: #475569;">
                        <strong>Orden de Mantenimiento:</strong> #{{ $mantenimiento->ID_Mantenimiento }} <br>
                        <strong>Hardware Asignado:</strong> Código Inv. N° {{ $mantenimiento->Codigo_Inventario }} ({{ $mantenimiento->Marca }} {{ $mantenimiento->Modelo }}) <br>
                        <strong>Fecha Límite Programada:</strong> {{ \Carbon\Carbon::parse($mantenimiento->Fecha_Programada)->format('d/m/Y') }}
                    </p>
                </div>

                <form action="{{ route('intervenciones.store') }}" method="POST" style="display: flex; flex-direction: column; gap: var(--space-4);">
                    @csrf 
                    
                    {{-- Parámetros ocultos de control de llaves para el Backend --}}
                    <input type="hidden" name="ID_Mantenimiento" value="{{ $mantenimiento->ID_Mantenimiento }}">
                    <input type="hidden" name="ID_TecnicoIntervino" value="{{ session('id_user') }}">

                    {{-- 1. Acción Realizada --}}
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:var(--space-1); font-size:var(--text-sm);">Acción Realizada *</label>
                        <input type="text" name="Accion_Realizada" required placeholder="Ej: Mantenimiento preventivo completo, limpieza de hardware y cambio de pasta térmica."
                               style="width:100%; padding:var(--space-2); border-radius:var(--radius-md); border:1px solid var(--border-color, #ccc);"
                               maxlength="500">
                    </div>

                    {{-- 2. Observaciones Técnicas Detalladas --}}
                    <div>
                        <label style="display:block; font-weight:600; margin-bottom:var(--space-1); font-size:var(--text-sm);">Observaciones Técnicas *</label>
                        <textarea name="Observaciones_Tecnicas" rows="6" required placeholder="Detalla los hallazgos encontrados en el diagnóstico, componentes sustituidos o recomendaciones para el próximo ciclo..."
                                  style="width:100%; padding:var(--space-2); border-radius:var(--radius-md); border:1px solid var(--border-color, #ccc); resize:vertical;"
                                  maxlength="1000"></textarea>
                    </div>

                    {{-- Botones de Control --}}
                    <div style="display:flex; justify-content:flex-end; gap:var(--space-2); margin-top:var(--space-2); border-top: 1px solid var(--border-color, #e5e7eb); padding-top: var(--space-3);">
                        <a href="{{ route('dashboard.tecnico') }}" class="btn" style="background:#e2e8f0; color:#334155; padding: 8px 16px; border-radius: var(--radius-md); text-decoration: none; font-size: var(--text-sm);">Cancelar</a>
                        <button type="submit" class="btn btn-primary" style="background:#f97316; border-color:#f97316; padding: 8px 16px; font-size: var(--text-sm);">Finalizar e Inyectar Bitácora</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection