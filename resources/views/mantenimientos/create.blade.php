@extends('layouts.app')
@section('title', 'Nuevo Mantenimiento')

@php
$equiposJson = $equipos->map(function($e) {
    return [
        'id'        => $e->ID_Equipo,
        'codigo'    => $e->Codigo_Inventario,
        'tipo'      => $e->Tipo,
        'marca'     => $e->Marca ?? '',
        'ubicacion' => $e->Ubicacion,
    ];
});
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .search-wrapper { position: relative; }

        .search-input {
            width: 100%;
            padding: 9px 36px 9px 12px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-bg);
            color: var(--color-text);
            font-size: 14px;
            box-sizing: border-box;
        }
        .search-input:focus { outline: none; border-color: var(--color-primary); }

        .search-icon {
            position: absolute;
            right: 10px; 
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-text-muted);
            pointer-events: none;
        }

        .results-list {
            position: absolute;
            z-index: 50;
            width: 100%;
            max-height: 240px;
            overflow-y: auto;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            margin-top: 4px;
            box-shadow: 0 4px 16px rgba(0,0,0,.12);
            display: none;
        }

        .result-item {
            padding: 10px 14px;
            cursor: pointer;
            font-size: 13px;
            border-bottom: 1px solid var(--color-border);
            transition: background .15s;
        }
        .result-item:last-child { border-bottom: none; }
        .result-item:hover { background: var(--color-primary-light, #e0e7ff); }
        .result-item .item-code { font-weight: 600; color: var(--color-text); }
        .result-item .item-meta { color: var(--color-text-muted); font-size: 12px; margin-top: 2px; }
        .result-empty { padding: 12px 14px; font-size: 13px; color: var(--color-text-muted); }

        .equipo-card {
            display: none;
            margin-top: 10px;
            padding: 12px 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-primary);
            border-radius: var(--radius-md);
            font-size: 13px;
            gap: 10px;
            align-items: flex-start;
        }
        .equipo-card.visible { display: flex; }
        .equipo-card .card-icon { color: var(--color-primary); flex-shrink: 0; margin-top: 2px; }
        .equipo-card .card-body { flex: 1; }
        .equipo-card .card-title { font-weight: 600; color: var(--color-text); }
        .equipo-card .card-sub { color: var(--color-text-muted); margin-top: 2px; }
        .equipo-card .card-clear {
            background: none; border: none; cursor: pointer;
            color: var(--color-text-muted); padding: 0; flex-shrink: 0;
        }
        .equipo-card .card-clear:hover { color: #ef4444; }
    </style>
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Nuevo Mantenimiento</span>
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
                <span style="color:var(--color-text);font-weight:500">Nuevo</span>
            </nav>

            <div class="page-heading">
                <h1>Programar mantenimiento</h1>
                <p>Busca el equipo, asigna un técnico y una fecha</p>
            </div>

            @if($errors->any())
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-card" style="max-width:560px">
                <form method="POST" action="{{ route('mantenimientos.store') }}">
                    @csrf

                    {{-- ── Búsqueda de equipo ── --}}
                    <div class="form-field">
                        <label>Equipo <span style="color:#ef4444">*</span></label>

                        <input type="hidden" name="ID_Equipo" id="ID_Equipo"
                               value="{{ old('ID_Equipo') }}">

                        <div class="search-wrapper">
                            <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input type="text" id="equipo-search" class="search-input"
                                   placeholder="Busca por código, tipo o ubicación..."
                                   autocomplete="off">
                            <div class="results-list" id="results-list"></div>
                        </div>

                        <div class="equipo-card" id="equipo-card">
                            <div class="card-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0
                                             l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91
                                             a2.12 2.12 0 0 1-3-3l6.91-6.91
                                             a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                            </div>
                            <div class="card-body">
                                <div class="card-title" id="card-title"></div>
                                <div class="card-sub" id="card-sub"></div>
                            </div>
                            <button type="button" class="card-clear" id="card-clear" title="Cambiar equipo">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        @error('ID_Equipo')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ── Técnico ── --}}
                    <div class="form-field">
                        <label for="ID_Tecnico">
                            Técnico responsable <span style="color:#ef4444">*</span>
                        </label>
                        <select id="ID_Tecnico" name="ID_Tecnico" required>
                            <option value="">— Selecciona un técnico —</option>
                            @foreach($tecnicos as $tecnico)
                                <option value="{{ $tecnico->ID_User }}"
                                    {{ old('ID_Tecnico') == $tecnico->ID_User ? 'selected' : '' }}>
                                    {{ $tecnico->Usuario }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Tecnico')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ── Fecha ── --}}
                    <div class="form-field">
                        <label for="Fecha_Programada">
                            Fecha programada <span style="color:#ef4444">*</span>
                        </label>
                        <input type="date" id="Fecha_Programada" name="Fecha_Programada"
                               value="{{ old('Fecha_Programada') }}"
                               min="{{ date('Y-m-d') }}" required>
                        @error('Fecha_Programada')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions" style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Programar mantenimiento
                        </button>
                        <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
const EQUIPOS = {!! json_encode($equiposJson) !!};

const searchInput = document.getElementById('equipo-search');
const resultsList = document.getElementById('results-list');
const hiddenInput = document.getElementById('ID_Equipo');
const equipoCard  = document.getElementById('equipo-card');
const cardTitle   = document.getElementById('card-title');
const cardSub     = document.getElementById('card-sub');
const cardClear   = document.getElementById('card-clear');

// Restaurar selección si Laravel regresa con old() tras error de validación
(function restoreOld() {
    const oldId = hiddenInput.value;
    if (!oldId) return;
    const eq = EQUIPOS.find(function(e) { return e.id == oldId; });
    if (eq) showCard(eq);
})();

searchInput.addEventListener('focus', function () {
    renderList(this.value.trim().toLowerCase());
});

searchInput.addEventListener('input', function () {
    renderList(this.value.trim().toLowerCase());
});

function renderList(q) {
    var matches = q.length === 0
        ? EQUIPOS.slice(0, 10)   // sin texto → muestra los primeros 10
        : EQUIPOS.filter(function(e) {
            return e.codigo.toLowerCase().includes(q) ||
                   e.tipo.toLowerCase().includes(q)   ||
                   e.marca.toLowerCase().includes(q)  ||
                   e.ubicacion.toLowerCase().includes(q);
          }).slice(0, 8);

    resultsList.innerHTML = '';

    if (matches.length === 0) {
        resultsList.innerHTML = '<div class="result-empty">Sin resultados para "' + q + '"</div>';
    } else {
        matches.forEach(function(eq) {
            var div = document.createElement('div');
            div.className = 'result-item';
            div.innerHTML =
                '<div class="item-code">' + eq.codigo + ' — ' + eq.tipo + '</div>' +
                '<div class="item-meta">' + (eq.marca ? eq.marca + ' · ' : '') + eq.ubicacion + '</div>';
            div.addEventListener('click', function() { selectEquipo(eq); });
            resultsList.appendChild(div);
        });
    }

    resultsList.style.display = 'block';
}

function selectEquipo(eq) {
    hiddenInput.value = eq.id;
    showCard(eq);
    searchInput.value = '';
    closeList();
}

function showCard(eq) {
    cardTitle.textContent = eq.codigo + ' — ' + eq.tipo;
    cardSub.textContent   = (eq.marca ? eq.marca + ' · ' : '') + eq.ubicacion;
    equipoCard.classList.add('visible');
    searchInput.style.display = 'none';
}

cardClear.addEventListener('click', function() {
    hiddenInput.value = '';
    equipoCard.classList.remove('visible');
    searchInput.style.display = '';
    searchInput.value = '';
    searchInput.focus();
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-wrapper')) closeList();
});

function closeList() {
    resultsList.style.display = 'none';
}

// Tema
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