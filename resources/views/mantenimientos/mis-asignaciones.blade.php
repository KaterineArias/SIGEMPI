@extends('layouts.app')
@section('title', 'Mis Asignaciones')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Mis Asignaciones</span>
            <div class="topbar-actions">
                <button class="theme-toggle" data-theme-toggle aria-label="Cambiar tema">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
            </div>
        </header>

        <main class="page-body">
            <div class="page-heading">
                <h1>Mis Asignaciones</h1>
                <p>Mantenimientos asignados a ti</p>
            </div>

            @if(session('success'))
                <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;
                            padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <p style="font-size:13px;color:var(--color-text-muted);margin-bottom:12px">
                {{ $asignaciones->count() }} {{ $asignaciones->count() === 1 ? 'asignación pendiente' : 'asignaciones pendientes' }}
            </p>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Ubicación</th>
                            <th>Fecha Programada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asignaciones as $a)
                            @php $vencida = \Carbon\Carbon::parse($a->Fecha_Programada)->isPast(); @endphp
                            <tr style="{{ $vencida ? 'background:#fef2f2' : '' }}">
                                <td><span class="badge badge-info">{{ $a->Codigo_Inventario }}</span>
                                    @if($vencida)
                                        <span style="font-size:11px;color:#dc2626;display:block;margin-top:2px">
                                            Fecha vencida
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $a->Tipo }}</td>
                                <td>{{ $a->Ubicacion }}</td>
                                <td>
                                    {{ $a->Fecha_Programada
                                        ? \Carbon\Carbon::parse($a->Fecha_Programada)->format('d/m/Y')
                                        : '—' }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($a->Estado_Mantenimiento) {
                                            'Completado'   => 'badge-success',
                                            'Cancelado'    => 'badge-danger',
                                            'Reprogramado' => 'badge-warning',
                                            default        => 'badge-info',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $a->Estado_Mantenimiento }}
                                    </span>
                                </td>
                                <td>
                                    @if(!in_array($a->Estado_Mantenimiento, ['Completado', 'Cancelado']))
                                        <button type="button" class="btn btn-success btn-sm"
                                                onclick="abrirModal({{ $a->ID_Mantenimiento }})">
                                            ✓ Completar
                                        </button>
                                    @else
                                        <span style="font-size:12px;color:var(--color-text-muted)">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:32px">
                                    No tienes mantenimientos asignados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>

{{-- Modal completar mantenimiento --}}
<div id="modal-completar"
     style="display:none;position:fixed;inset:0;z-index:1000;
            background:rgba(0,0,0,.45);align-items:center;justify-content:center">
    <div style="background:var(--color-surface);border:1px solid var(--color-border);
                border-radius:var(--radius-md);padding:28px;width:100%;max-width:480px;
                box-shadow:0 8px 32px rgba(0,0,0,.18);margin:16px">

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:16px;font-weight:600;margin:0">Completar mantenimiento</h2>
            <button onclick="cerrarModal()"
                    style="background:none;border:none;cursor:pointer;
                           color:var(--color-text-muted);font-size:20px;line-height:1">✕</button>
        </div>

        <form id="form-completar" method="POST" action="">
            @csrf
            @method('PATCH')
            <input type="hidden" name="estado" value="Completado">

            <div class="form-field">
                <label for="Accion_Realizada">
                    Acción realizada <span style="color:#ef4444">*</span>
                </label>
                <textarea id="Accion_Realizada" name="Accion_Realizada"
                          rows="3" maxlength="500" required
                          placeholder="Describe qué se realizó en este mantenimiento..."></textarea>
                <span style="font-size:11px;color:var(--color-text-muted)">Máximo 500 caracteres</span>
            </div>

            <div class="form-field">
                <label for="Observaciones_Tecnicas">Observaciones técnicas</label>
                <textarea id="Observaciones_Tecnicas" name="Observaciones_Tecnicas"
                          rows="3" maxlength="1000"
                          placeholder="Observaciones adicionales (opcional)..."></textarea>
            </div>

            <div class="form-actions" style="display:flex;gap:10px;justify-content:flex-end">
                <button type="button" onclick="cerrarModal()"
                        class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar y completar</button>
            </div>
        </form>
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

function abrirModal(idMantenimiento) {
    const url = `/mantenimientos/${idMantenimiento}/estado`;
    document.getElementById('form-completar').action = url;
    document.getElementById('Accion_Realizada').value = '';
    document.getElementById('Observaciones_Tecnicas').value = '';
    const modal = document.getElementById('modal-completar');
    modal.style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-completar').style.display = 'none';
}

// Cerrar al hacer clic fuera del modal
document.getElementById('modal-completar').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endpush