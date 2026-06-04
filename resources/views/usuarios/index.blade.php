@extends('layouts.app')
@section('title', 'Usuarios')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Usuarios</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo usuario
                </a>
            </div>
        </header>

        <main class="page-body">

            <div class="page-heading">
                <h1>Usuarios</h1>
                <p>Gestión de cuentas del sistema</p>
            </div>

            {{-- Mensajes --}}
            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:10px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->has('estado'))
                <div class="alert alert-error" style="margin-bottom:16px;">
                    {{ $errors->first('estado') }}
                </div>
            @endif

            {{-- Búsqueda y filtros --}}
            <form method="GET" action="{{ route('usuarios.index') }}"
                  style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:20px;">

                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por usuario o correo..."
                       style="padding:7px 12px;border-radius:9px;border:1px solid var(--color-border);
                              font-size:13px;background:var(--color-surface-2);min-width:220px;outline:none;">

                <select name="rol" onchange="this.form.submit()"
                        style="padding:7px 12px;border-radius:9px;border:1px solid var(--color-border);
                               font-size:13px;background:var(--color-surface-2);cursor:pointer;">
                    <option value="">— Todos los roles —</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->ID_Rol }}" {{ request('rol') == $r->ID_Rol ? 'selected' : '' }}>
                            {{ $r->Rol }}
                        </option>
                    @endforeach
                </select>

                <select name="estado" onchange="this.form.submit()"
                        style="padding:7px 12px;border-radius:9px;border:1px solid var(--color-border);
                               font-size:13px;background:var(--color-surface-2);cursor:pointer;">
                    <option value="">— Todos los estados —</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->ID_EstadoUsuario }}" {{ request('estado') == $e->ID_EstadoUsuario ? 'selected' : '' }}>
                            {{ $e->Estado }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm">Buscar</button>

                @if(request()->hasAny(['buscar','rol','estado']))
                    <a href="{{ route('usuarios.index') }}"
                       style="font-size:13px;color:var(--color-text-muted);text-decoration:none;
                              padding:7px 10px;border:1px solid var(--color-border);
                              border-radius:9px;background:var(--color-surface-2);">
                        ✕ Limpiar
                    </a>
                @endif
            </form>

            {{-- Tabla --}}
            <div class="table-wrapper">
                @if($usuarios->isEmpty())
                    <div class="empty-state">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <p>No se encontraron usuarios.</p>
                    </div>
                @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $u)
                        <tr>
                            <td>{{ $u->ID_User }}</td>
                            <td><span style="font-family:monospace;font-size:13px">{{ $u->Usuario }}</span></td>
                            <td>{{ $u->Correo_User }}</td>
                            <td>
                                <span class="badge {{ $u->esCoordinador() ? 'badge-blue' : 'badge-gray' }}">
                                    {{ $u->rol->Rol }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $u->estaActivo() ? 'badge-green' : 'badge-red' }}">
                                    {{ $u->estadoUsuario->Estado }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">

                                    {{-- Editar --}}
                                    <a href="{{ route('usuarios.edit', $u->ID_User) }}"
                                    class="btn btn-sm btn-primary">
                                        Editar
                                    </a>

                                    {{-- Historial --}}
                                    <a href="{{ route('usuarios.historial', $u->ID_User) }}"
                                    class="btn btn-sm btn-secondary">
                                        Historial
                                    </a>

                                    {{-- Activar / Desactivar --}}
                                    @if($u->ID_User !== session('id_user'))
                                        <form method="POST"
                                            action="{{ route('usuarios.estado', $u->ID_User) }}"
                                            style="display:inline">
                                            @csrf
                                            @method('PATCH')
                                            @if($u->estaActivo())
                                                <input type="hidden" name="id_estado" value="{{ $idInactivo }}">
                                                <button type="submit" class="btn btn-sm btn-danger btn-desactivar"
                                                        onclick="return confirm('¿Desactivar a {{ $u->Usuario }}?')">
                                                    Desactivar
                                                </button>
                                            @else
                                                <input type="hidden" name="id_estado" value="{{ $idActivo }}">
                                                <button type="submit" class="btn btn-sm btn-success btn-activar">
                                                    Activar
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span style="font-size:12px;color:var(--color-text-muted)">Tu cuenta</span>
                                    @endif

                                </div>
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
@endsection

@push('scripts')
<script>
(function(){
    const t = document.querySelector('[data-theme-toggle]');
    const r = document.documentElement;
    let d = localStorage.getItem('theme') || 'light';
    r.setAttribute('data-theme', d);
    if (t) {
        t.addEventListener('click', () => {
            d = d === 'dark' ? 'light' : 'dark';
            r.setAttribute('data-theme', d);
            localStorage.setItem('theme', d);
            t.innerHTML = d === 'dark'
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
        });
    }
})();
</script>
@endpush