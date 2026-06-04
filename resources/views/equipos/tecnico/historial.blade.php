@extends('layouts.app')
@section('title', 'Historial — ' . $equipo->Codigo_Equipo)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Historial de mantenimientos</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">

            {{-- Botón volver --}}
            {{-- Breadcrumb --}}
            <nav style="display:flex;align-items:center;gap:6px;font-size:13px;
                        color:var(--color-text-muted);margin-bottom:20px">
                <a href="{{ route('equipos.index') }}"
                style="color:var(--color-text-muted);text-decoration:none">
                Equipos
                </a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span style="color:var(--color-text);font-weight:500">
                    {{ $equipo->Codigo_Inventario }}
                </span>
            </nav>

            {{-- Info del equipo --}}
            <div style="background:var(--color-surface);border:1px solid var(--color-border);
                        border-radius:var(--radius-md);padding:20px;margin-bottom:24px">
                <div class="page-heading" style="margin-bottom:12px">
                    <h1>{{ $equipo->Codigo_Equipo }}</h1>
                    <p>{{ $equipo->Tipo_Equipo }} — {{ $equipo->Marca }} {{ $equipo->Modelo }}</p>
                </div>
                <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:var(--color-text-muted)">
                    <span>📍 <strong>Ubicación:</strong> {{ $equipo->Ubicacion }}</span>
                    <span>🔧 <strong>Total mantenimientos:</strong> {{ $mantenimientos->count() }}</span>
                    <span>📅 <strong>Último mantenimiento:</strong>
                        {{ $mantenimientos->first()
                            ? \Carbon\Carbon::parse($mantenimientos->first()->Fecha_Programada)->format('d/m/Y')
                            : 'Sin registros' }}
                    </span>
                </div>
            </div>

            {{-- Historial --}}
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Técnico</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mantenimientos as $m)
                            <tr>
                                <td>{{ $m->Fecha_Programada ? \Carbon\Carbon::parse($m->Fecha_Programada)->format('d/m/Y') : '—' }}</td>
                                <td>{{ $m->usuario->Usuario ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $m->Estado_Mantenimiento === 'Completado' ? 'badge-success' : 
                                                        ($m->Estado_Mantenimiento === 'Cancelado'  ? 'badge-danger'  : 'badge-warning') }}">
                                        {{ $m->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td style="max-width:320px;white-space:normal">
                                    {{ $m->Observaciones ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    Este equipo no tiene mantenimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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