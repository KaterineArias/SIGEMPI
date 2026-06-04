<aside class="sidebar">
    <div class="sidebar-logo">
        <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
            <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64"
                 style="object-fit:contain;border-radius:var(--radius-md);">
            <span class="brand">SIGE<span>MPI</span></span>
        </div>
    </div>

    <span class="nav-section-label">Principal</span>
    <a href="{{ session('rol') === 'Coordinador' ? route('dashboard.coordinador') : route('dashboard.tecnico') }}"
       class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
    </a>

    <span class="nav-section-label">Gestión</span>
    <a href="{{ route('equipos.index') }}"
       class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        Equipos
    </a>

    @if(session('rol') === 'Coordinador')
    <a href="{{ route('usuarios.index') }}"
       class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Usuarios
    </a>
    @endif

    <a href="{{ route('mantenimientos.index') }}"
       class="nav-link {{ request()->routeIs('mantenimientos.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        Mantenimientos
    </a>

    @if(session('rol') === 'Coordinador')
    <span class="nav-section-label">Reportes</span>
    <a href="{{ route('reportes.index') }}"
       class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Reportes
    </a>
    @endif

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(session('usuario'), 0, 2)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ session('usuario') }}</div>
                <div class="user-role">{{ session('rol') }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:var(--space-3)">
            @csrf
            <button type="submit" class="btn btn-ghost"
                    style="width:100%;justify-content:center;font-size:var(--text-xs)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>