@extends('layouts.app')
@section('title', 'Historial — ' . $usuario->Usuario)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Historial de cambios</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('usuarios.edit', $usuario->ID_User) }}" class="btn btn-secondary">
                    ← Volver a editar
                </a>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    Listado
                </a>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Historial — {{ $usuario->Usuario }}</h1>
                <p>Registro de todos los cambios realizados sobre esta cuenta</p>
            </div>

            {{-- Tabs --}}
            <div style="display:flex;gap:4px;margin-bottom:24px;border-bottom:2px solid var(--color-border);">
                <button onclick="showTab('estado')" id="tab-estado"
                        class="tab-btn tab-active">
                    Cambios de estado
                    @if($historialEstado->count() > 0)
                        <span class="badge badge-blue" style="margin-left:6px">{{ $historialEstado->count() }}</span>
                    @endif
                </button>
                <button onclick="showTab('rol')" id="tab-rol" class="tab-btn">
                    Cambios de rol
                    @if($historialRol->count() > 0)
                        <span class="badge badge-blue" style="margin-left:6px">{{ $historialRol->count() }}</span>
                    @endif
                </button>
                <button onclick="showTab('usuario')" id="tab-usuario" class="tab-btn">
                    Cambios de usuario
                    @if($historialUsuario->count() > 0)
                        <span class="badge badge-blue" style="margin-left:6px">{{ $historialUsuario->count() }}</span>
                    @endif
                </button>
            </div>

            {{-- Tab: Estado --}}
            <div id="panel-estado">
                <div class="table-wrapper">
                    @if($historialEstado->isEmpty())
                        <div class="empty-state">
                            <p>Sin cambios de estado registrados.</p>
                        </div>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Estado anterior</th>
                                    <th>Estado nuevo</th>
                                    <th>Realizado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historialEstado as $h)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($h->Fecha)->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge badge-red">{{ $h->EstadoAnterior }}</span></td>
                                    <td><span class="badge badge-green">{{ $h->EstadoNuevo }}</span></td>
                                    <td>{{ $h->RealizadoPor }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Tab: Rol --}}
            <div id="panel-rol" style="display:none">
                <div class="table-wrapper">
                    @if($historialRol->isEmpty())
                        <div class="empty-state">
                            <p>Sin cambios de rol registrados.</p>
                        </div>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Rol anterior</th>
                                    <th>Rol nuevo</th>
                                    <th>Realizado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historialRol as $h)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($h->Fecha)->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge badge-gray">{{ $h->RolAnterior }}</span></td>
                                    <td><span class="badge badge-blue">{{ $h->RolNuevo }}</span></td>
                                    <td>{{ $h->RealizadoPor }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Tab: Usuario --}}
            <div id="panel-usuario" style="display:none">
                <div class="table-wrapper">
                    @if($historialUsuario->isEmpty())
                        <div class="empty-state">
                            <p>Sin cambios de nombre de usuario registrados.</p>
                        </div>
                    @else
                        <table>
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario anterior</th>
                                    <th>Usuario nuevo</th>
                                    <th>Realizado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($historialUsuario as $h)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($h->Fecha)->format('d/m/Y H:i') }}</td>
                                    <td><span style="font-family:monospace;font-size:13px">{{ $h->ValorAnterior }}</span></td>
                                    <td><span style="font-family:monospace;font-size:13px">{{ $h->ValorNuevo }}</span></td>
                                    <td>{{ $h->RealizadoPor }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
.tab-btn {
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    background: none;
    cursor: pointer;
    color: var(--color-text-muted);
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: color 0.15s, border-color 0.15s;
    display: inline-flex;
    align-items: center;
}
.tab-btn:hover { color: var(--color-text); }
.tab-active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}
</style>
@endpush

@push('scripts')
<script>
function showTab(name) {
    ['estado','rol','usuario'].forEach(t => {
        document.getElementById('panel-' + t).style.display = t === name ? 'block' : 'none';
        document.getElementById('tab-' + t).classList.toggle('tab-active', t === name);
    });
}
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