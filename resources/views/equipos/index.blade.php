@extends('layouts.app')
@section('title', 'Equipos')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Equipos</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </header>

        <main class="page-body">

            <div class="page-heading" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div>
                    <h1>Equipos</h1>
                    <p>Registro, consulta y gestión de equipos informáticos</p>
                </div>
                <a href="{{ route('equipos.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Nuevo equipo
                </a>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" action="{{ route('equipos.index') }}"
                  style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;align-items:flex-end">

                <div class="form-field" style="flex:2;min-width:200px;margin-bottom:0">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Buscar por código, marca, modelo o ubicación...">
                </div>

                <div class="form-field" style="flex:1;min-width:150px;margin-bottom:0">
                    <select name="tipo">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->ID_Tipo }}"
                                {{ request('tipo') == $t->ID_Tipo ? 'selected' : '' }}>
                                {{ $t->Nombre_Tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-field" style="flex:1;min-width:150px;margin-bottom:0">
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        @foreach($estados as $e)
                            <option value="{{ $e->Estado }}"
                                {{ request('estado') == $e->Estado ? 'selected' : '' }}>
                                {{ $e->Estado }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Filtrar</button>

                @if(request('q') || request('tipo') || request('estado'))
                    <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif

            </form>

            {{-- Tabla --}}
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Marca / Modelo</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipos as $equipo)
                            <tr>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $equipo->Codigo_Inventario }}
                                    </span>
                                </td>
                                <td>{{ $equipo->tipo->Nombre_Tipo ?? '—' }}</td>
                                <td>
                                    {{ $equipo->Marca ?? '—' }}
                                    @if($equipo->Modelo)
                                        <span style="color:var(--color-text-muted);font-size:12px">
                                            · {{ $equipo->Modelo }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $equipo->ubicacion->NombreSede ?? '—' }}</td>
                                <td>
                                    @php
                                        $est = $equipo->estado->Estado ?? '—';
                                        $badgeClass = match($est) {
                                            'Activo'      => 'badge-success',
                                            'En Reparación' => 'badge-warning',
                                            'De Baja'     => 'badge-danger',
                                            default       => 'badge-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $est }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                                        <a href="{{ route('equipos.historial', $equipo->ID_Equipo) }}"
                                           class="btn btn-secondary btn-sm">
                                            Historial
                                        </a>
                                        <a href="{{ route('equipos.edit', $equipo->ID_Equipo) }}"
                                           class="btn btn-secondary btn-sm">
                                            Editar
                                        </a>
                                        @if($est !== 'De Baja')
                                            <form method="POST"
                                                  action="{{ route('equipos.destroy', $equipo->ID_Equipo) }}"
                                                  onsubmit="return confirm('¿Dar de baja este equipo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Dar de baja
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No se encontraron equipos.
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
                    Mostrando {{ $equipos->firstItem() }}–{{ $equipos->lastItem() }}
                    de {{ $equipos->total() }} equipos
                </span>
                <div style="display:flex;gap:6px">
                    @if($equipos->onFirstPage())
                        <span class="btn btn-secondary btn-sm" style="opacity:.4;cursor:default">← Anterior</span>
                    @else
                        <a href="{{ $equipos->previousPageUrl() }}" class="btn btn-secondary btn-sm">← Anterior</a>
                    @endif

                    @if($equipos->hasMorePages())
                        <a href="{{ $equipos->nextPageUrl() }}" class="btn btn-secondary btn-sm">Siguiente →</a>
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
    if (t) t.addEventListener('click', () => {
        d = d === 'dark' ? 'light' : 'dark';
        r.setAttribute('data-theme', d);
        localStorage.setItem('theme', d);
    });
})();
</script>
@endpush