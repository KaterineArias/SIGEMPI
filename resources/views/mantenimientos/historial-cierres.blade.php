@extends('layouts.app')
@section('title', 'Historial de Cierres')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Historial de Cierres</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Historial de Cierres</h1>
                <p>Registro de mantenimientos completados y cancelados</p>
            </div>

            {{-- Tarjetas resumen --}}
            <div class="kpi-grid" style="margin-bottom:24px">
                <div class="kpi-card">
                    <div class="kpi-icon teal">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="kpi-value">{{ $totalCierres }}</div>
                    <div class="kpi-label">Total cierres</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="kpi-value">{{ $totalCompletado }}</div>
                    <div class="kpi-label">Completados</div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-icon orange">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <div class="kpi-value">{{ $totalCancelado }}</div>
                    <div class="kpi-label">Cancelados</div>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('mantenimientos.historial-cierres') }}"
                  style="margin-bottom:20px">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

                    <div class="form-field" style="margin-bottom:0;min-width:160px">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="Completado" {{ request('estado') === 'Completado' ? 'selected' : '' }}>
                                Completado
                            </option>
                            <option value="Cancelado" {{ request('estado') === 'Cancelado' ? 'selected' : '' }}>
                                Cancelado
                            </option>
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:0;min-width:160px">
                        <label>Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>

                    <div class="form-field" style="margin-bottom:0;min-width:160px">
                        <label>Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Filtrar</button>

                    @if(request()->hasAny(['estado', 'fecha_desde', 'fecha_hasta']))
                        <a href="{{ route('mantenimientos.historial-cierres') }}"
                           class="btn btn-secondary">Limpiar</a>
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
                            <th>Fecha Programada</th>
                            <th>Fecha Cierre</th>
                            <th>Estado</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cierres as $c)
                            <tr>
                                <td><span class="badge badge-info">{{ $c->Codigo_Inventario }}</span></td>
                                <td>{{ $c->Tipo }}</td>
                                <td>{{ $c->Ubicacion }}</td>
                                <td>
                                    {{ $c->Fecha_Programada
                                        ? \Carbon\Carbon::parse($c->Fecha_Programada)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td>
                                    {{ $c->Fecha_Cierre
                                        ? \Carbon\Carbon::parse($c->Fecha_Cierre)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td>
                                    <span class="badge {{ $c->Estado_Mantenimiento === 'Completado' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $c->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($detalles[$c->ID_Mantenimiento]))
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="verDetalle({{ $c->ID_Mantenimiento }})">
                                            Ver detalle
                                        </button>
                                    @else
                                        <span style="font-size:12px;color:var(--color-text-muted)">Sin detalle</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No hay cierres registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

{{-- Modal detalle --}}
<div id="modal-detalle"
     style="display:none;position:fixed;inset:0;z-index:1000;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:var(--color-surface);border:1px solid var(--color-border);
                border-radius:var(--radius-md);padding:28px;width:100%;max-width:520px;
                box-shadow:0 8px 32px rgba(0,0,0,.18);margin:16px">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:16px;font-weight:600;margin:0">Detalle del mantenimiento</h2>
            <button onclick="cerrarDetalle()"
                    style="background:none;border:none;cursor:pointer;
                           color:var(--color-text-muted);font-size:20px;line-height:1">✕</button>
        </div>

        <div id="detalle-contenido"></div>

    </div>
</div>

{{-- Datos de detalles para JS --}}
<script>
    const detallesData = @json($detalles);
</script>

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

function verDetalle(idMantenimiento) {
    const detalles = detallesData[idMantenimiento];
    if (!detalles || detalles.length === 0) return;

    let html = '';
    detalles.forEach((d, i) => {
        html += `
            <div style="border:1px solid var(--color-border);border-radius:var(--radius-md);
                        padding:14px;${i > 0 ? 'margin-top:12px' : ''}">
                <div style="font-size:11px;color:var(--color-text-muted);margin-bottom:8px">
                    Registrado el ${new Date(d.Fecha_Registro).toLocaleDateString('es-SV')}
                </div>
                <div style="margin-bottom:10px">
                    <div style="font-size:12px;font-weight:600;margin-bottom:4px">Acción realizada</div>
                    <div style="font-size:13px">${d.Accion_Realizada ?? '—'}</div>
                </div>
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:4px">Observaciones técnicas</div>
                    <div style="font-size:13px;color:var(--color-text-muted)">${d.Observaciones_Tecnicas ?? '—'}</div>
                </div>
            </div>`;
    });

    document.getElementById('detalle-contenido').innerHTML = html;
    document.getElementById('modal-detalle').style.display = 'flex';
}

function cerrarDetalle() {
    document.getElementById('modal-detalle').style.display = 'none';
}

document.getElementById('modal-detalle').addEventListener('click', function(e) {
    if (e.target === this) cerrarDetalle();
});
</script>
@endpush