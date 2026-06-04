@extends('layouts.app')
@section('title', 'Dashboard Técnico')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    {{-- MAIN --}}
    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Dashboard</span>
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
                <h1>Hola, {{ session('usuario') }}</h1>
                <p>Estas son tus asignaciones — {{ \Carbon\Carbon::now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- KPIs --}}
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_programados'] }}</div>
                    <div class="kpi-label">Pendientes asignados</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_completados'] }}</div>
                    <div class="kpi-label">Completados en total</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="kpi-value">{{ $stats['mis_este_mes'] }}</div>
                    <div class="kpi-label">Asignados este mes</div>
                </div>
            </div>

            {{-- Tabla de asignaciones --}}
            <p class="section-title">Mis próximas asignaciones</p>
            <div class="table-wrapper">
                @if($asignaciones->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <p>No tienes mantenimientos asignados por ahora.</p>
                    </div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
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
                                <td><strong>{{ $a->Codigo_Inventario }}</strong></td>
                                <td>{{ $a->Tipo }}</td>
                                <td class="td-muted">{{ $a->Ubicacion }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->Fecha_Programada)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($a->Estado_Mantenimiento) }}">
                                        {{ $a->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-primary"
                                        style="padding:var(--space-1) var(--space-3);font-size:var(--text-xs)"
                                        onclick="abrirModal({{ $a->ID_Mantenimiento }}, '{{ addslashes($a->Codigo_Inventario) }}', '{{ addslashes($a->Observaciones ?? '') }}')">
                                        Registrar
                                    </button>
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
                <textarea id="modal-observaciones" name="Observaciones" rows="4"
                          maxlength="1000"
                          placeholder="Describe el trabajo realizado..."
                          style="padding:var(--space-2) var(--space-3);border:1px solid var(--color-border);
                                 border-radius:var(--radius-md);font-size:var(--text-sm);
                                 font-family:inherit;background:var(--color-bg);
                                 color:var(--color-text);outline:none;resize:vertical;width:100%"></textarea>
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

@endsection

@push('scripts')
<script>
(function(){
    const t = document.querySelector('[data-theme-toggle]');
    const r = document.documentElement;
    let d = localStorage.getItem('theme') || 'light';
    r.setAttribute('data-theme', d);
    if (t) t.addEventListener('click', () => {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();

function abrirModal(id, codigo, observaciones) {
    document.getElementById('modal-subtitulo').textContent = 'Equipo: ' + codigo;
    document.getElementById('form-completar').action = '/mantenimientos/' + id + '/estado';
    const textarea = document.getElementById('modal-observaciones');
    textarea.value = observaciones;
    document.getElementById('modal-char-count').textContent = observaciones.length;
    document.getElementById('modal-completar').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-completar').style.display = 'none';
}

document.getElementById('modal-completar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

document.getElementById('modal-observaciones').addEventListener('input', function() {
    document.getElementById('modal-char-count').textContent = this.value.length;
});
</script>
@endpush