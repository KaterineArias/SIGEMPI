@extends('layouts.app')
@section('title', 'Dashboard Coordinador')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- MAIN --}}
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
                <h1>Bienvenido, {{ session('usuario') }}</h1>
                <p>Resumen general del parque informático — {{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>

            {{-- KPIs --}}
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['total_equipos'] }}</div>
                    <div class="kpi-label">Total de equipos</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['equipos_activos'] }}</div>
                    <div class="kpi-label">Equipos activos</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon red">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['equipos_danados'] }}</div>
                    <div class="kpi-label">Equipos dañados</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['total_tecnicos'] }}</div>
                    <div class="kpi-label">Técnicos activos</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mant_este_mes'] }}</div>
                    <div class="kpi-label">Mantenimientos este mes</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mant_programados'] }}</div>
                    <div class="kpi-label">Pendientes</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mant_completados'] }}</div>
                    <div class="kpi-label">Completados</div>
                </div>
            </div>

            {{-- Próximos mantenimientos --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; flex-wrap:wrap; gap:12px;">
    <p class="section-title" style="margin:0">Próximos mantenimientos programados</p>

    <form method="GET" action="{{ route('dashboard.coordinador') }}"
          style="display:flex; align-items:center; gap:8px;">
        <select name="tecnico_id"
            onchange="this.form.submit()"
            style="padding:7px 12px; border-radius:9px; border:1px solid #ddd;
                   font-size:13px; background:#fff; cursor:pointer; min-width:180px;">
            <option value="">— Todos los técnicos —</option>
            @foreach($tecnicos as $t)
                <option value="{{ $t->ID_User }}"
                    {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>
                    {{ $t->Usuario }}
                </option>
            @endforeach
        </select>

        @if(request('tecnico_id'))
            <a href="{{ route('dashboard.coordinador') }}"
               style="font-size:13px; color:#888; text-decoration:none; padding:7px 10px;
                      border:1px solid #ddd; border-radius:9px; background:#fff;">
                ✕ Limpiar
            </a>
        @endif
    </form>
</div>
            <div class="table-wrapper">
                @if($proximos->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <p>No hay mantenimientos programados próximamente.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Ubicación</th>
                                <th>Técnico</th>
                                <th>Fecha programada</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proximos as $m)
                            <tr>
                                <td><strong>{{ $m->Codigo_Inventario }}</strong></td>
                                <td>{{ $m->Tipo }}</td>
                                <td class="td-muted">{{ $m->Ubicacion }}</td>
                                <td>{{ $m->Tecnico }}</td>
                                <td>{{ \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge
                                        @switch($m->Estado_Mantenimiento)
                                            @case('Programado')  badge-blue   @break
                                            @case('Completado')  badge-green  @break
                                            @case('Reprogramado') badge-gray  @break
                                            @case('Cancelado')   badge-red    @break
                                            @default             badge-gray
                                        @endswitch
                                    ">
                                        {{ $m->Estado_Mantenimiento }}
                                    </span>
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
@endsection