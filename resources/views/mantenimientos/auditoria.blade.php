@extends('layouts.app')
@section('title', 'Auditoría de Cambios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Auditoría</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </header>

        <main class="page-body">

            <nav style="display:flex;align-items:center;gap:6px;font-size:13px;
                        color:var(--color-text-muted);margin-bottom:20px">
                <a href="{{ route('mantenimientos.index') }}"
                   style="color:var(--color-text-muted);text-decoration:none">Mantenimientos</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span style="color:var(--color-text);font-weight:500">Auditoría</span>
            </nav>

            <div class="page-heading">
                <h1>Auditoría de cambios</h1>
                <p>Historial completo de cambios de estado en mantenimientos</p>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('mantenimientos.auditoria') }}"
                  style="margin-bottom:20px">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">

                    <div class="form-field" style="margin-bottom:0;min-width:180px">
                        <label>Técnico</label>
                        <select name="tecnico_id">
                            <option value="">Todos</option>
                            @foreach($tecnicos as $t)
                                <option value="{{ $t->ID_User }}"
                                    {{ request('tecnico_id') == $t->ID_User ? 'selected' : '' }}>
                                    {{ $t->Usuario }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:0;min-width:160px">
                        <label>Estado nuevo</label>
                        <select name="estado_nuevo">
                            <option value="">Todos</option>
                            @foreach(['Programado','Reprogramado','Completado','Cancelado'] as $e)
                                <option value="{{ $e }}"
                                    {{ request('estado_nuevo') === $e ? 'selected' : '' }}>
                                    {{ $e }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-field" style="margin-bottom:0">
                        <label>Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>

                    <div class="form-field" style="margin-bottom:0">
                        <label>Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Filtrar</button>

                    @if(request()->hasAny(['tecnico_id','estado_nuevo','fecha_desde','fecha_hasta']))
                        <a href="{{ route('mantenimientos.auditoria') }}" class="btn btn-secondary">Limpiar</a>
                    @endif

                </div>
            </form>

            {{-- Tabla --}}
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Cambio</th>
                            <th>Modificado por</th>
                            <th>Fecha</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cambios as $c)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $c->Codigo_Inventario }}</span>
                                </td>
                                <td>
                                    <span style="font-size:12px;color:var(--color-text-muted)">
                                        {{ $c->Estado_Anterior }}
                                    </span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2"
                                         style="vertical-align:middle;margin:0 4px">
                                        <line x1="5" y1="12" x2="19" y2="12"/>
                                        <polyline points="12 5 19 12 12 19"/>
                                    </svg>
                                    @php
                                        $bc = match($c->Estado_Nuevo) {
                                            'Completado'   => 'badge-success',
                                            'Cancelado'    => 'badge-danger',
                                            'Reprogramado' => 'badge-warning',
                                            default        => 'badge-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $bc }}">{{ $c->Estado_Nuevo }}</span>
                                </td>
                                <td>{{ $c->Modificado_Por }}</td>
                                <td style="font-size:13px;white-space:nowrap">
                                    {{ \Carbon\Carbon::parse($c->Fecha_Cambio)->format('d/m/Y H:i') }}
                                </td>
                                <td style="font-size:13px;color:var(--color-text-muted);max-width:220px">
                                    {{ $c->Motivo_Cambio ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No hay cambios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div style="margin-top:16px;display:flex;align-items:center;
                        justify-content:space-between;font-size:13px;color:var(--color-text-muted)">
                <span>
                    Mostrando {{ $cambios->firstItem() }}–{{ $cambios->lastItem() }}
                    de {{ $cambios->total() }} registros
                </span>
                <div style="display:flex;gap:6px">
                    @if($cambios->onFirstPage())
                        <span class="btn btn-secondary btn-sm" style="opacity:.4;cursor:default">← Anterior</span>
                    @else
                        <a href="{{ $cambios->previousPageUrl() }}" class="btn btn-secondary btn-sm">← Anterior</a>
                    @endif
                    @if($cambios->hasMorePages())
                        <a href="{{ $cambios->nextPageUrl() }}" class="btn btn-secondary btn-sm">Siguiente →</a>
                    @else
                        <span class="btn btn-secondary btn-sm" style="opacity:.4;cursor:default">Siguiente →</span>
                    @endif
                </div>
            </div>

        </main>
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
    if (t) t.addEventListener('click', function() {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();
</script>
@endpush