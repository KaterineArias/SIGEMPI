@extends('layouts.app')
@section('title', 'Reportes')

@php
$equiposJson = $equipos->map(function($e) {
    return [
        'id'        => $e->ID_Equipo,
        'codigo'    => $e->Codigo_Inventario,
        'tipo'      => $e->Tipo,
        'marca'     => $e->Marca ?? '',
    ];
});
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .resumen-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 16px;
            text-align: center;
        }
        .resumen-card .rc-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--color-text);
            line-height: 1;
        }
        .resumen-card .rc-label {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 4px;
        }
        .resumen-card.rc-completado  { border-top: 3px solid #22c55e; }
        .resumen-card.rc-programado  { border-top: 3px solid #3b82f6; }
        .resumen-card.rc-reprogramado{ border-top: 3px solid #f59e0b; }
        .resumen-card.rc-cancelado   { border-top: 3px solid #ef4444; }
        .resumen-card.rc-total       { border-top: 3px solid var(--color-primary); }

        .detalle-row { display: none; background: var(--color-bg); }
        .detalle-row td { padding: 12px 16px; font-size: 13px; }
        .detalle-box {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            font-size: 13px;
        }
        .detalle-box strong { color: var(--color-text); display:block; margin-bottom:4px; }
        .detalle-box p { color: var(--color-text-muted); margin: 0; }

        /* buscador equipo */
        .search-wrapper { position: relative; }
        .search-input {
            width: 100%;
            padding: 9px 36px 9px 12px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-bg);
            color: var(--color-text);
            font-size: 13px;
            box-sizing: border-box;
        }
        .search-input:focus { outline: none; border-color: var(--color-primary); }
        .search-icon {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted); pointer-events: none;
        }
        .results-list {
            position: absolute; z-index: 50; width: 100%;
            max-height: 200px; overflow-y: auto;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            margin-top: 4px;
            box-shadow: 0 4px 16px rgba(0,0,0,.12);
            display: none;
        }
        .result-item {
            padding: 9px 14px; cursor: pointer;
            font-size: 13px; border-bottom: 1px solid var(--color-border);
        }
        .result-item:last-child { border-bottom: none; }
        .result-item:hover { background: var(--color-primary-light, #e0e7ff); }
        .result-item .item-code { font-weight: 600; }
        .result-item .item-meta { color: var(--color-text-muted); font-size: 12px; }
        .result-empty { padding: 10px 14px; font-size: 13px; color: var(--color-text-muted); }
        .equipo-tag {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--color-surface);
            border: 1px solid var(--color-primary);
            border-radius: var(--radius-md);
            padding: 6px 10px; font-size: 13px;
        }
        .equipo-tag .tag-clear {
            background: none; border: none; cursor: pointer;
            color: var(--color-text-muted); padding: 0; line-height: 1;
        }
        .equipo-tag .tag-clear:hover { color: #ef4444; }
    </style>
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Reportes</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </header>

        <main class="page-body">

            <div class="page-heading">
                <h1>Reportes de mantenimiento</h1>
                <p>Consulta actividades por período, equipo o técnico</p>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('reportes.index') }}"
                  style="margin-bottom:24px">

                <input type="hidden" name="equipo_id" id="equipo_id"
                       value="{{ request('equipo_id') }}">

                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end">

                    {{-- Período desde --}}
                    <div class="form-field" style="margin-bottom:0;min-width:150px">
                        <label>Desde</label>
                        <input type="date" name="fecha_desde"
                               value="{{ request('fecha_desde') }}">
                    </div>

                    {{-- Período hasta --}}
                    <div class="form-field" style="margin-bottom:0;min-width:150px">
                        <label>Hasta</label>
                        <input type="date" name="fecha_hasta"
                               value="{{ request('fecha_hasta') }}">
                    </div>

                    {{-- Técnico --}}
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

                    {{-- Estado --}}
                    <div class="form-field" style="margin-bottom:0;min-width:150px">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            @foreach(['Programado','Reprogramado','Completado','Cancelado'] as $e)
                                <option value="{{ $e }}"
                                    {{ request('estado') === $e ? 'selected' : '' }}>
                                    {{ $e }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Equipo con buscador --}}
                    <div class="form-field" style="margin-bottom:0;min-width:220px">
                        <label>Equipo</label>
                        <div id="equipo-selector">
                            @if(request('equipo_id'))
                                @php
                                    $eqSel = $equipos->firstWhere('ID_Equipo', request('equipo_id'));
                                @endphp
                                @if($eqSel)
                                    <div class="equipo-tag" id="equipo-tag">
                                        <span>{{ $eqSel->Codigo_Inventario }} · {{ $eqSel->Tipo }}</span>
                                        <button type="button" class="tag-clear" id="tag-clear">✕</button>
                                    </div>
                                @endif
                            @else
                                <div class="search-wrapper" id="equipo-search-wrapper">
                                    <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input type="text" id="equipo-search" class="search-input"
                                           placeholder="Buscar equipo..." autocomplete="off">
                                    <div class="results-list" id="results-list"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Generar reporte
                    </button>

                    @if($hayFiltros)
                        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif

                </div>
            </form>

            @if($hayFiltros)

                {{-- Tarjetas resumen --}}
                <div class="resumen-grid">
                    <div class="resumen-card rc-total">
                        <div class="rc-number">{{ $resumen['total'] }}</div>
                        <div class="rc-label">Total</div>
                    </div>
                    <div class="resumen-card rc-completado">
                        <div class="rc-number">{{ $resumen['completados'] }}</div>
                        <div class="rc-label">Completados</div>
                    </div>
                    <div class="resumen-card rc-programado">
                        <div class="rc-number">{{ $resumen['programados'] }}</div>
                        <div class="rc-label">Programados</div>
                    </div>
                    <div class="resumen-card rc-reprogramado">
                        <div class="rc-number">{{ $resumen['reprogramados'] }}</div>
                        <div class="rc-label">Reprogramados</div>
                    </div>
                    <div class="resumen-card rc-cancelado">
                        <div class="rc-number">{{ $resumen['cancelados'] }}</div>
                        <div class="rc-label">Cancelados</div>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Equipo</th>
                                <th>Tipo</th>
                                <th>Ubicación</th>
                                <th>Técnico</th>
                                <th>Fecha programada</th>
                                <th>Fecha cierre</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mantenimientos as $m)
                                <tr style="cursor:pointer"
                                    onclick="toggleDetalle({{ $m->ID_Mantenimiento }})">
                                    <td style="width:32px;text-align:center">
                                        <svg id="chevron-{{ $m->ID_Mantenimiento }}"
                                             width="14" height="14" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2"
                                             style="transition:transform .2s;color:var(--color-text-muted)">
                                            <polyline points="9 18 15 12 9 6"/>
                                        </svg>
                                    </td>
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
                                        {{ $m->Fecha_Cierre
                                            ? \Carbon\Carbon::parse($m->Fecha_Cierre)->format('d/m/Y')
                                            : '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $bc = match($m->Estado) {
                                                'Completado'   => 'badge-success',
                                                'Cancelado'    => 'badge-danger',
                                                'Reprogramado' => 'badge-warning',
                                                default        => 'badge-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $bc }}">{{ $m->Estado }}</span>
                                    </td>
                                </tr>
                                {{-- Fila detalle expandible --}}
                                <tr class="detalle-row" id="detalle-{{ $m->ID_Mantenimiento }}">
                                    <td colspan="8">
                                        @if($m->Accion_Realizada)
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                                <div class="detalle-box">
                                                    <strong>Acción realizada</strong>
                                                    <p>{{ $m->Accion_Realizada }}</p>
                                                </div>
                                                @if($m->Observaciones_Tecnicas)
                                                    <div class="detalle-box">
                                                        <strong>Observaciones técnicas</strong>
                                                        <p>{{ $m->Observaciones_Tecnicas }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <p style="color:var(--color-text-muted);font-size:13px;margin:0">
                                                Sin detalle registrado.
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        style="text-align:center;color:var(--color-text-muted);padding:32px">
                                        No se encontraron mantenimientos con los filtros aplicados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else
                {{-- Estado vacío inicial --}}
                <div class="empty-state" style="margin-top:48px">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <p>Aplica al menos un filtro para generar el reporte.</p>
                </div>
            @endif

        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
const EQUIPOS = {!! json_encode($equiposJson) !!};

// ── Buscador de equipo ──
var searchInput  = document.getElementById('equipo-search');
var resultsList  = document.getElementById('results-list');
var hiddenInput  = document.getElementById('equipo_id');
var tagClear     = document.getElementById('tag-clear');

if (searchInput) {
    searchInput.addEventListener('focus', function() { renderList(''); });
    searchInput.addEventListener('input', function() { renderList(this.value.trim().toLowerCase()); });
}

if (tagClear) {
    tagClear.addEventListener('click', function() {
        hiddenInput.value = '';
        var wrapper = document.getElementById('equipo-selector');
        wrapper.innerHTML =
            '<div class="search-wrapper" id="equipo-search-wrapper">' +
            '<svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>' +
            '<input type="text" id="equipo-search" class="search-input" placeholder="Buscar equipo..." autocomplete="off">' +
            '<div class="results-list" id="results-list"></div>' +
            '</div>';
        var newSearch = document.getElementById('equipo-search');
        resultsList   = document.getElementById('results-list');
        newSearch.addEventListener('focus', function() { renderList(''); });
        newSearch.addEventListener('input', function() { renderList(this.value.trim().toLowerCase()); });
        newSearch.focus();
    });
}

function renderList(q) {
    var rl = document.getElementById('results-list');
    if (!rl) return;
    var matches = q.length === 0
        ? EQUIPOS.slice(0, 10)
        : EQUIPOS.filter(function(e) {
            return e.codigo.toLowerCase().includes(q) ||
                   e.tipo.toLowerCase().includes(q)   ||
                   e.marca.toLowerCase().includes(q);
          }).slice(0, 8);

    rl.innerHTML = '';
    if (matches.length === 0) {
        rl.innerHTML = '<div class="result-empty">Sin resultados.</div>';
    } else {
        matches.forEach(function(eq) {
            var div = document.createElement('div');
            div.className = 'result-item';
            div.innerHTML =
                '<div class="item-code">' + eq.codigo + ' · ' + eq.tipo + '</div>' +
                '<div class="item-meta">' + (eq.marca || '') + '</div>';
            div.addEventListener('click', function() {
                document.getElementById('equipo_id').value = eq.id;
                var selector = document.getElementById('equipo-selector');
                selector.innerHTML =
                    '<div class="equipo-tag" id="equipo-tag">' +
                    '<span>' + eq.codigo + ' · ' + eq.tipo + '</span>' +
                    '<button type="button" class="tag-clear" id="tag-clear">✕</button>' +
                    '</div>';
                document.getElementById('tag-clear').addEventListener('click', function() {
                    document.getElementById('equipo_id').value = '';
                    selector.innerHTML =
                        '<div class="search-wrapper"><svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>' +
                        '<input type="text" id="equipo-search" class="search-input" placeholder="Buscar equipo..." autocomplete="off">' +
                        '<div class="results-list" id="results-list"></div></div>';
                    var ns = document.getElementById('equipo-search');
                    ns.addEventListener('focus', function() { renderList(''); });
                    ns.addEventListener('input', function() { renderList(this.value.trim().toLowerCase()); });
                    ns.focus();
                });
            });
            rl.appendChild(div);
        });
    }
    rl.style.display = 'block';
}

document.addEventListener('click', function(e) {
    var rl = document.getElementById('results-list');
    if (rl && !e.target.closest('.search-wrapper')) rl.style.display = 'none';
});

// ── Filas expandibles ──
function toggleDetalle(id) {
    var row     = document.getElementById('detalle-' + id);
    var chevron = document.getElementById('chevron-' + id);
    var visible = row.style.display === 'table-row';
    row.style.display     = visible ? 'none' : 'table-row';
    chevron.style.transform = visible ? '' : 'rotate(90deg)';
}

// ── Tema ──
(function(){
    var t = document.querySelector('[data-theme-toggle]');
    var r = document.documentElement;
    var d = localStorage.getItem('theme') || 'light';
    r.setAttribute('data-theme', d);
    if (t) t.addEventListener('click', function() {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();
</script>
@endpush