@extends('layouts.app')

@section('title', 'Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR dinámico según rol --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md)">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        @if(session('rol') === 'Coordinador')
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Usuarios
            </a>
            <a href="{{ route('mantenimientos.index') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Mantenimientos
            </a>
            <span class="nav-section-label">Reportes</span>
            <a href="{{ route('reportes.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Reportes
            </a>
        @else
            <span class="nav-section-label">Principal</span>
            <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <span class="nav-section-label">Mi Trabajo</span>
            <a href="{{ route('mantenimientos.mis-asignaciones') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                Mis asignaciones
            </a>
            <a href="{{ route('mantenimientos.create') }}" class="nav-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Registrar intervención
            </a>
            <a href="{{ route('equipos.index') }}" class="nav-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Ver equipos
            </a>
        @endif

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario') }}</div>
                    <div class="user-role">{{ session('rol') }}</div>
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

    {{-- MAIN --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Mantenimientos</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo mantenimiento
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Programación de Mantenimientos</h1>
                <p>Gestión y seguimiento de intervenciones técnicas</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fce7f3;border:1px solid #f9a8d4;color:#9d174d;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="table-wrapper">

                @if(session('rol') === 'Coordinador')
                <form action="{{ route('mantenimientos.index') }}" method="GET"
                      style="padding:12px 16px;border-bottom:1px solid var(--color-border);display:flex;align-items:center;gap:10px;">
                    <label style="font-size:12px;font-weight:600;color:var(--color-text)">Técnico:</label>
                    <select name="tecnico_id" onchange="this.form.submit()"
                            style="padding:6px 10px;border:1px solid var(--color-border);border-radius:7px;font-size:12px;font-family:inherit;background:var(--color-bg);color:var(--color-text);outline:none;">
                        <option value="">Todos</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->ID_User }}"
                                {{ request('tecnico_id') == $tecnico->ID_User ? 'selected' : '' }}>
                                {{ $tecnico->Usuario }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif

                @if($mantenimientos->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <p>No hay mantenimientos programados.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Técnico asignado</th>
                                <th>Fecha programada</th>
                                <th>Estado</th>
                                @if(session('rol') === 'Coordinador')
                                    <th>Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mantenimientos as $mant)
                            <tr>
                                <td>
                                    <strong>{{ $mant->equipo->Codigo_Inventario }}</strong>
                                    <span class="td-muted"> — {{ $mant->equipo->Tipo }}</span>
                                </td>
                                <td>{{ $mant->tecnico->Usuario }}</td>
                                <td class="td-muted">
                                    {{ \Carbon\Carbon::parse($mant->Fecha_Programada)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ match($mant->Estado_Mantenimiento) {
                                        'Completado'   => 'green',
                                        'Cancelado'    => 'red',
                                        'Reprogramado' => 'yellow',
                                        default        => 'blue'
                                    } }}">
                                        {{ $mant->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                @if(session('rol') === 'Coordinador')
                                <td>
                                    @if(in_array($mant->Estado_Mantenimiento, ['Programado', 'Reprogramado']))
                                        <div style="display:flex;gap:var(--space-2)">
                                            {{-- Reprogramar --}}
                                            <button
                                                class="btn btn-ghost"
                                                style="font-size:var(--text-xs);padding:var(--space-1) var(--space-3);border:1px solid var(--color-border)"
                                                onclick="abrirReprogramar({{ $mant->ID_Mantenimiento }}, '{{ addslashes($mant->equipo->Codigo_Inventario) }}', '{{ $mant->Fecha_Programada }}', '{{ addslashes($mant->Observaciones ?? '') }}')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                Reprogramar
                                            </button>
                                            {{-- Cancelar --}}
                                            <button
                                                class="btn btn-ghost"
                                                style="font-size:var(--text-xs);padding:var(--space-1) var(--space-3);border:1px solid var(--color-border);color:#a12c7b"
                                                onclick="abrirCancelar({{ $mant->ID_Mantenimiento }}, '{{ addslashes($mant->equipo->Codigo_Inventario) }}')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                                Cancelar
                                            </button>
                                        </div>
                                    @else
                                        <span style="font-size:var(--text-xs);color:var(--color-text-faint)">—</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </main>
    </div>
</div>

{{-- Reprogramar --}}
<div id="modal-reprogramar" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
    <div style="background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--radius-xl);padding:var(--space-8);width:100%;max-width:480px;box-shadow:var(--color-shadow)">
        <h2 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-1)">Reprogramar mantenimiento</h2>
        <p id="reprogramar-subtitulo" style="font-size:var(--text-sm);color:var(--color-text-muted);margin-bottom:var(--space-6)"></p>

        <form id="form-reprogramar" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="estado" value="Reprogramado">

            <div class="form-field" style="margin-bottom:var(--space-4)">
                <label for="reprogramar-fecha">Nueva fecha programada *</label>
                <input type="date" id="reprogramar-fecha" name="Fecha_Programada"
                       min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-field">
                <label for="reprogramar-obs">Observaciones</label>
                <textarea id="reprogramar-obs" name="Observaciones" rows="3"
                          maxlength="1000"
                          placeholder="Motivo de la reprogramación..."></textarea>
                <span style="font-size:11px;color:var(--color-text-muted);text-align:right">
                    <span id="reprogramar-chars">0</span>/1000
                </span>
            </div>

            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-6)">
                <button type="submit" class="btn btn-primary" style="flex:1">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Confirmar reprogramación
                </button>
                <button type="button" class="btn btn-ghost" onclick="cerrarReprogramar()" style="flex:1">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- Cancelar --}}
<div id="modal-cancelar" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
    <div style="background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--radius-xl);padding:var(--space-8);width:100%;max-width:480px;box-shadow:var(--color-shadow)">
        <h2 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-1);color:#a12c7b">Cancelar mantenimiento</h2>
        <p id="cancelar-subtitulo" style="font-size:var(--text-sm);color:var(--color-text-muted);margin-bottom:var(--space-6)"></p>

        <form id="form-cancelar" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="estado" value="Cancelado">

            <div class="form-field">
                <label for="cancelar-obs">Motivo de cancelación</label>
                <textarea id="cancelar-obs" name="Observaciones" rows="3"
                          maxlength="1000"
                          placeholder="Describe el motivo..."></textarea>
                <span style="font-size:11px;color:var(--color-text-muted);text-align:right">
                    <span id="cancelar-chars">0</span>/1000
                </span>
            </div>

            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-6)">
                <button type="submit" class="btn btn-primary" style="flex:1;background:#a12c7b">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Confirmar cancelación
                </button>
                <button type="button" class="btn btn-ghost" onclick="cerrarCancelar()" style="flex:1">Volver</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ── Reprogramar ──
    function abrirReprogramar(id, codigo, fecha, obs) {
        document.getElementById('reprogramar-subtitulo').textContent = 'Equipo: ' + codigo;
        document.getElementById('form-reprogramar').action = '/mantenimientos/' + id + '/estado';
        document.getElementById('reprogramar-fecha').value = fecha;
        const ta = document.getElementById('reprogramar-obs');
        ta.value = obs;
        document.getElementById('reprogramar-chars').textContent = obs.length;
        document.getElementById('modal-reprogramar').style.display = 'flex';
    }
    function cerrarReprogramar() {
        document.getElementById('modal-reprogramar').style.display = 'none';
    }
    document.getElementById('modal-reprogramar').addEventListener('click', function(e) {
        if (e.target === this) cerrarReprogramar();
    });
    document.getElementById('reprogramar-obs').addEventListener('input', function() {
        document.getElementById('reprogramar-chars').textContent = this.value.length;
    });

    // ── Cancelar ──
    function abrirCancelar(id, codigo) {
        document.getElementById('cancelar-subtitulo').textContent = '¿Estás seguro de cancelar el mantenimiento del equipo ' + codigo + '?';
        document.getElementById('form-cancelar').action = '/mantenimientos/' + id + '/estado';
        document.getElementById('cancelar-obs').value = '';
        document.getElementById('cancelar-chars').textContent = '0';
        document.getElementById('modal-cancelar').style.display = 'flex';
    }
    function cerrarCancelar() {
        document.getElementById('modal-cancelar').style.display = 'none';
    }
    document.getElementById('modal-cancelar').addEventListener('click', function(e) {
        if (e.target === this) cerrarCancelar();
    });
    document.getElementById('cancelar-obs').addEventListener('input', function() {
        document.getElementById('cancelar-chars').textContent = this.value.length;
    });
</script>
@endpush

@endsection