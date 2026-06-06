@extends('layouts.app')
@section('title', 'Mantenimientos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .timeline { list-style: none; padding: 0; margin: 0; position: relative; }
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px; top: 0; bottom: 0;
            width: 2px;
            background: var(--color-border);
        }
        .timeline-item {
            position: relative;
            padding: 0 0 20px 40px;
        }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot {
            position: absolute;
            left: 8px; top: 3px;
            width: 16px; height: 16px;
            border-radius: 50%;
            border: 2px solid var(--color-surface);
            background: var(--color-primary);
        }
        .timeline-dot.dot-success  { background: #22c55e; }
        .timeline-dot.dot-danger   { background: #ef4444; }
        .timeline-dot.dot-warning  { background: #f59e0b; }
        .timeline-dot.dot-info     { background: #3b82f6; }
        .timeline-meta {
            font-size: 11px;
            color: var(--color-text-muted);
            margin-top: 2px;
        }
        .timeline-motivo {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 4px;
            font-style: italic;
        }
    </style>
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Mantenimientos</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
                <a href="{{ route('mantenimientos.auditoria') }}" class="btn btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Auditoría
                </a>
                <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nuevo mantenimiento
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Mantenimientos</h1>
                <p>Gestión y seguimiento de mantenimientos</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" action="{{ route('mantenimientos.index') }}" style="margin-bottom:20px">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

                    <div class="form-field" style="margin-bottom:0;min-width:180px">
                        <label>Técnico</label>
                        <select name="tecnico_id">
                            <option value="">Todos los técnicos</option>
                            @foreach($tecnicos as $t)
                                <option value="{{ $t->ID_User }}"
                                    {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>
                                    {{ $t->Usuario }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:0;min-width:160px">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            @foreach(['Programado','Reprogramado','Completado','Cancelado'] as $estado)
                                <option value="{{ $estado }}"
                                    {{ request('estado') === $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Filtrar</button>

                    @if(request()->hasAny(['tecnico_id','estado']))
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif

                </div>
            </form>

            {{-- Tabla --}}
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Ubicación</th>
                            <th>Técnico</th>
                            <th>Fecha Programada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $m)
                            <tr>
                                <td><span class="badge badge-info">{{ $m->Codigo_Inventario }}</span></td>
                                <td>{{ $m->Tipo }}</td>
                                <td>{{ $m->Ubicacion }}</td>
                                <td>{{ $m->Tecnico }}</td>
                                <td>
                                    {{ $m->Fecha_Programada
                                        ? \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($m->Estado_Mantenimiento) {
                                            'Completado'   => 'badge-success',
                                            'Cancelado'    => 'badge-danger',
                                            'Reprogramado' => 'badge-warning',
                                            default        => 'badge-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $m->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap">

                                        {{-- Historial de cambios --}}
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="abrirHistorial({{ $m->ID_Mantenimiento }}, '{{ addslashes($m->Codigo_Inventario) }}')">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            Historial
                                        </button>

                                        @if(!in_array($m->Estado_Mantenimiento, ['Completado','Cancelado']))
                                            {{-- Reprogramar --}}
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                    onclick="abrirReprogramar({{ $m->ID_Mantenimiento }}, '{{ addslashes($m->Codigo_Inventario) }}', '{{ $m->Fecha_Programada }}')">
                                                Reprogramar
                                            </button>
                                            {{-- Cancelar --}}
                                            <form method="POST"
                                                  action="{{ route('mantenimientos.cambiarEstado', $m->ID_Mantenimiento) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="Cancelado">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¿Cancelar este mantenimiento?')">
                                                    Cancelar
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No hay mantenimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

{{-- ── Modal Historial de Cambios ── --}}
<div id="modal-historial"
     style="display:none;position:fixed;inset:0;z-index:1000;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:var(--color-surface);border:1px solid var(--color-border);
                border-radius:var(--radius-md);padding:28px;width:100%;max-width:480px;
                max-height:85vh;overflow-y:auto;box-shadow:0 8px 32px rgba(0,0,0,.18);margin:16px">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
            <h2 style="font-size:16px;font-weight:600;margin:0">Historial de cambios</h2>
            <button onclick="cerrarHistorial()"
                    style="background:none;border:none;cursor:pointer;
                           color:var(--color-text-muted);font-size:20px;line-height:1">✕</button>
        </div>
        <p id="historial-subtitulo"
           style="font-size:13px;color:var(--color-text-muted);margin-bottom:20px"></p>

        <div id="historial-body">
            <div style="text-align:center;padding:24px;color:var(--color-text-muted);font-size:13px">
                Cargando...
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Reprogramar ── --}}
<div id="modal-reprogramar"
     style="display:none;position:fixed;inset:0;z-index:1000;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:var(--color-surface);border:1px solid var(--color-border);
                border-radius:var(--radius-md);padding:28px;width:100%;max-width:440px;
                box-shadow:0 8px 32px rgba(0,0,0,.18);margin:16px">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:16px;font-weight:600;margin:0">Reprogramar mantenimiento</h2>
            <button onclick="cerrarReprogramar()"
                    style="background:none;border:none;cursor:pointer;
                           color:var(--color-text-muted);font-size:20px;line-height:1">✕</button>
        </div>

        <p id="reprogramar-subtitulo"
           style="font-size:13px;color:var(--color-text-muted);margin-bottom:20px"></p>

        <form id="form-reprogramar" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="estado" value="Reprogramado">

            <div class="form-field">
                <label for="Fecha_Programada">Nueva fecha <span style="color:#ef4444">*</span></label>
                <input type="date" id="Fecha_Programada" name="Fecha_Programada"
                       min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-actions" style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="cerrarReprogramar()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Tema ──
(function(){
    const t = document.querySelector('[data-theme-toggle]');
    const r = document.documentElement;
    let d = localStorage.getItem('theme') || 'light';
    r.setAttribute('data-theme', d);
    if (t) t.addEventListener('click', function() {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();

// ── Modal Reprogramar ──
function abrirReprogramar(id, codigo, fechaActual) {
    document.getElementById('reprogramar-subtitulo').textContent = 'Equipo: ' + codigo;
    document.getElementById('form-reprogramar').action = '/mantenimientos/' + id + '/estado';
    if (fechaActual) {
        document.getElementById('Fecha_Programada').value = fechaActual.substring(0, 10);
    }
    document.getElementById('modal-reprogramar').style.display = 'flex';
}
function cerrarReprogramar() {
    document.getElementById('modal-reprogramar').style.display = 'none';
}
document.getElementById('modal-reprogramar').addEventListener('click', function(e) {
    if (e.target === this) cerrarReprogramar();
});

// ── Modal Historial ──
const dotClass = {
    'Completado':   'dot-success',
    'Cancelado':    'dot-danger',
    'Reprogramado': 'dot-warning',
    'Programado':   'dot-info',
};

function abrirHistorial(id, codigo) {
    document.getElementById('historial-subtitulo').textContent = 'Equipo: ' + codigo;
    document.getElementById('historial-body').innerHTML =
        '<div style="text-align:center;padding:24px;color:var(--color-text-muted);font-size:13px">Cargando...</div>';
    document.getElementById('modal-historial').style.display = 'flex';

    fetch('/mantenimientos/' + id + '/historial-cambios')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.length === 0) {
                document.getElementById('historial-body').innerHTML =
                    '<p style="text-align:center;color:var(--color-text-muted);font-size:13px;padding:16px 0">Sin cambios registrados.</p>';
                return;
            }

            var html = '<ul class="timeline">';
            data.forEach(function(item) {
                var dc = dotClass[item.Estado_Nuevo] || 'dot-info';
                var fecha = new Date(item.Fecha_Cambio).toLocaleString('es-SV', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
                html += '<li class="timeline-item">';
                html += '<div class="timeline-dot ' + dc + '"></div>';
                html += '<div style="font-size:13px;font-weight:600;color:var(--color-text)">'
                      + item.Estado_Anterior + ' → ' + item.Estado_Nuevo + '</div>';
                html += '<div class="timeline-meta">' + fecha + ' · ' + item.Modificado_Por + '</div>';
                if (item.Motivo_Cambio) {
                    html += '<div class="timeline-motivo">"' + item.Motivo_Cambio + '"</div>';
                }
                html += '</li>';
            });
            html += '</ul>';
            document.getElementById('historial-body').innerHTML = html;
        })
        .catch(function() {
            document.getElementById('historial-body').innerHTML =
                '<p style="color:#ef4444;font-size:13px">Error al cargar el historial.</p>';
        });
}

function cerrarHistorial() {
    document.getElementById('modal-historial').style.display = 'none';
}
document.getElementById('modal-historial').addEventListener('click', function(e) {
    if (e.target === this) cerrarHistorial();
});
</script>
@endpush