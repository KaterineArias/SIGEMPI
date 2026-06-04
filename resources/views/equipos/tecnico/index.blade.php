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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Equipos</h1>
                <p>Consulta el listado de equipos registrados</p>
            </div>

            {{-- Buscador --}}
            <form method="GET" action="{{ route('equipos.index') }}" style="margin-bottom:20px">
                <div style="display:flex;gap:10px;max-width:480px;align-items:center">
                    <div class="form-field" style="flex:1;margin-bottom:0">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Buscar por código, marca o ubicación...">
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    @if(request('q'))
                        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Limpiar</a>
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
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipos as $equipo)
                            <tr>
                                <td><span class="badge badge-info">{{ $equipo->Codigo_Inventario }}</span></td>
                                <td>{{ $equipo->tipo->Nombre_Tipo ?? '—' }}</td>
                                <td>{{ $equipo->Marca ?? '—' }}</td>
                                <td>{{ $equipo->Modelo ?? '—' }}</td>
                                <td>{{ $equipo->ubicacion->NombreSede ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('equipos.historial', $equipo->ID_Equipo) }}"
                                    class="btn btn-secondary btn-sm">
                                    Ver historial
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No se encontraron equipos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div style="margin-top:16px;display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--color-text-muted)">
                <span>Mostrando {{ $equipos->firstItem() }}–{{ $equipos->lastItem() }} de {{ $equipos->total() }} equipos</span>
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