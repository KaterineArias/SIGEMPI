@extends('layouts.app')

@section('title', 'Mis Asignaciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64" style="object-fit:contain;border-radius:var(--radius-md)">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        <span class="nav-section-label">Principal</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <span class="nav-section-label">Mi Trabajo</span>
        <a href="{{ route('mantenimientos.mis-asignaciones') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            Mis asignaciones
        </a>
        <a href="{{ route('mantenimientos.create') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Registrar intervención
        </a>
        <a href="{{ route('equipos.index') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Ver equipos
        </a>

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
            <span class="topbar-title">Mis Asignaciones</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Registrar intervención
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Mis Asignaciones</h1>
                <p>Historial completo de tus intervenciones técnicas</p>
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
                @if($asignaciones->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <p>No tienes asignaciones registradas.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Tipo</th>
                                <th>Ubicación</th>
                                <th>Fecha programada</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asignaciones as $a)
                            <tr>
                                <td><strong>{{ $a->equipo->Codigo_Inventario }}</strong></td>
                                <td>{{ $a->equipo->Tipo }}</td>
                                <td class="td-muted">{{ $a->equipo->Ubicacion }}</td>
                                <td class="td-muted">
                                    {{ \Carbon\Carbon::parse($a->Fecha_Programada)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ match($a->Estado_Mantenimiento) {
                                        'Completado'   => 'green',
                                        'Cancelado'    => 'red',
                                        'Reprogramado' => 'yellow',
                                        default        => 'blue'
                                    } }}">
                                        {{ $a->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td>
                                    @if(in_array($a->Estado_Mantenimiento, ['Programado', 'Reprogramado']))
                                        <button
                                            class="btn btn-primary"
                                            style="padding:var(--space-1) var(--space-3);font-size:var(--text-xs)"
                                            onclick="abrirModal({{ $a->ID_Mantenimiento }}, '{{ addslashes($a->equipo->Codigo_Inventario) }}', '{{ addslashes($a->Observaciones ?? '') }}')">
                                            Completar
                                        </button>
                                    @else
                                        <span style="font-size:var(--text-xs);color:var(--color-text-faint)">—</span>
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

{{-- MODAL Completar --}}
<div id="modal-completar" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
    <div style="background:var(--color-surface-2);border:1px solid var(--color-border);border-radius:var(--radius-xl);padding:var(--space-8);width:100%;max-width:480px;box-shadow:var(--color-shadow)">

        <h2 style="font-size:var(--text-lg);font-weight:700;margin-bottom:var(--space-1)">Completar intervención</h2>
        <p id="modal-subtitulo" style="font-size:var(--text-sm);color:var(--color-text-muted);margin-bottom:var(--space-6)"></p>

        <form id="form-completar" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="estado" value="Completado">

            <div class="form-field">
                <label for="modal-observaciones">Observaciones</label>
                <textarea
                    id="modal-observaciones"
                    name="Observaciones"
                    rows="4"
                    maxlength="1000"
                    placeholder="Describe el trabajo realizado..."></textarea>
                <span style="font-size:11px;color:var(--color-text-muted);text-align:right">
                    <span id="modal-char-count">0</span>/1000
                </span>
            </div>

            <div style="display:flex;gap:var(--space-3);margin-top:var(--space-6)">
                <button type="submit" class="btn btn-primary" style="flex:1">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Marcar como completado
                </button>
                <button type="button" class="btn btn-ghost" onclick="cerrarModal()" style="flex:1">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function abrirModal(id, codigo, observaciones) {
        const modal = document.getElementById('modal-completar');
        document.getElementById('modal-subtitulo').textContent = 'Equipo: ' + codigo;
        document.getElementById('form-completar').action = '/mantenimientos/' + id + '/estado';
        const textarea = document.getElementById('modal-observaciones');
        textarea.value = observaciones;
        document.getElementById('modal-char-count').textContent = observaciones.length;
        modal.style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modal-completar').style.display = 'none';
    }

    // Cerrar al hacer click fuera del modal
    document.getElementById('modal-completar').addEventListener('click', function(e) {
        if (e.target === this) cerrarModal();
    });

    // Contador de caracteres del modal
    const textarea = document.getElementById('modal-observaciones');
    const counter  = document.getElementById('modal-char-count');
    textarea.addEventListener('input', () => counter.textContent = textarea.value.length);
</script>
@endpush

@endsection