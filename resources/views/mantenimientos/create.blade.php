@extends('layouts.app')

@section('title', 'Registrar intervención')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div style="display:flex;flex-direction:column;align-items:center;gap:var(--space-2);padding-bottom:var(--space-2)">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SIGEMPI" width="64" height="64"
                    style="object-fit:contain;border-radius:var(--radius-md);">
                <span class="brand">SIGE<span>MPI</span></span>
            </div>
        </div>

        <span class="nav-section-label">Principal</span>
        <a href="{{ route('dashboard.tecnico') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <span class="nav-section-label">Mi trabajo</span>
        <a href="{{ route('mantenimientos.mis-asignaciones') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
            Mis asignaciones
        </a>
        <a href="{{ route('mantenimientos.create') }}" class="nav-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Registrar intervención
        </a>
        <a href="{{ route('equipos.index') }}" class="nav-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <path d="M8 21h8M12 17v4"/>
            </svg>
            Ver equipos
        </a>

        <div class="sidebar-footer">
            <div class="user-chip">
                <div class="user-avatar">{{ strtoupper(substr(session('usuario'), 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ session('usuario') }}</div>
                    <div class="user-role">{{ session('rol') }}</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <a href="{{ route('dashboard.tecnico') }}" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M19 12H5M12 5l-7 7 7 7"/>
                    </svg>
                    Volver al dashboard
                </a>
                <h1 class="page-title">Registrar intervención</h1>
            </div>
        </header>

        {{-- ALERT DE ÉXITO --}}
        @if(session('success'))
        <div class="alert alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- FORMULARIO --}}
        <div class="form-wrapper">
            <div class="form-card">
                <div class="form-card-header">
                    <div class="form-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="form-card-title">Nueva intervención</h2>
                        <p class="form-card-subtitle">Completa los datos para registrar el mantenimiento</p>
                    </div>
                </div>

                <form action="{{ route('mantenimientos.store') }}" method="POST" class="form-body">
                    @csrf

                    {{-- EQUIPO --}}
                    <div class="field-group">
                        <label class="field-label" for="ID_Equipo">
                            Equipo
                            <span class="required-dot">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select name="ID_Equipo" id="ID_Equipo"
                                class="field-input field-select @error('ID_Equipo') is-error @enderror"
                                required>
                                <option value="">— Seleccionar equipo —</option>
                                @foreach($equipos as $equipo)
                                    <option value="{{ $equipo->ID_Equipo }}"
                                        {{ old('ID_Equipo') == $equipo->ID_Equipo ? 'selected' : '' }}>
                                        {{ $equipo->Codigo_Inventario }} — {{ $equipo->Marca }} {{ $equipo->Modelo }}
                                        ({{ $equipo->Tipo }})
                                    </option>
                                @endforeach
                            </select>
                            <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                        @error('ID_Equipo')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- TÉCNICO --}}
                    <div class="field-group">
                        <label class="field-label" for="ID_Tecnico">
                            Técnico asignado
                            <span class="required-dot">*</span>
                        </label>
                        <div class="select-wrapper">
                            <select name="ID_Tecnico" id="ID_Tecnico"
                                class="field-input field-select @error('ID_Tecnico') is-error @enderror"
                                required>
                                <option value="">— Seleccionar técnico —</option>
                                @foreach($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->ID_User }}"
                                        {{ old('ID_Tecnico') == $tecnico->ID_User ? 'selected' : '' }}>
                                        {{ $tecnico->Usuario }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                        @error('ID_Tecnico')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- DOS COLUMNAS: FECHA + ESTADO --}}
                    <div class="field-row">
                        <div class="field-group">
                            <label class="field-label" for="Fecha_Programada">
                                Fecha programada
                                <span class="required-dot">*</span>
                            </label>
                            <input type="date" name="Fecha_Programada" id="Fecha_Programada"
                                class="field-input @error('Fecha_Programada') is-error @enderror"
                                value="{{ old('Fecha_Programada') }}"
                                min="{{ date('Y-m-d') }}"
                                required>
                            @error('Fecha_Programada')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label class="field-label">Estado</label>
                            <div class="status-badge-display">
                                <span class="status-pill status-programado">Programado</span>
                                <span class="status-hint">Se asigna automáticamente</span>
                            </div>
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="field-group">
                        <label class="field-label" for="Observaciones">Observaciones</label>
                        <textarea name="Observaciones" id="Observaciones"
                            class="field-input field-textarea @error('Observaciones') is-error @enderror"
                            rows="4"
                            maxlength="1000"
                            placeholder="Describe el problema o el trabajo a realizar...">{{ old('Observaciones') }}</textarea>
                        <div class="char-counter">
                            <span id="char-count">0</span>/1000 caracteres
                        </div>
                        @error('Observaciones')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ACCIONES --}}
                    <div class="form-actions">
                        <a href="{{ route('dashboard.tecnico') }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            Guardar intervención
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</div>

{{-- Estilos específicos de esta vista --}}
<style>
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--color-text-muted, #888);
    text-decoration: none;
    margin-bottom: 4px;
    transition: color 0.15s;
}
.back-link:hover { color: var(--color-primary, #2a7d6f); }

.page-title {
    font-size: 22px;
    font-weight: 600;
    color: var(--color-text, #1a1a1a);
    margin: 0;
}

.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 14px;
    margin: 0 2rem 1.5rem;
}
.alert-success {
    background: #e6f7f2;
    color: #1a6b57;
    border: 1px solid #b3e0d4;
}

.form-wrapper {
    padding: 0 2rem 2rem;
}

.form-card {
    background: #ffffff;
    border: 1px solid #e8e6e0;
    border-radius: 16px;
    max-width: 680px;
    overflow: hidden;
}

.form-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 1.5rem 1.75rem;
    border-bottom: 1px solid #f0ede8;
    background: #fafaf8;
}

.form-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #e6f4f1;
    color: #2a7d6f;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.form-card-title {
    font-size: 17px;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 2px;
}

.form-card-subtitle {
    font-size: 13px;
    color: #888;
    margin: 0;
}

.form-body {
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.field-label {
    font-size: 13px;
    font-weight: 500;
    color: #444;
    display: flex;
    align-items: center;
    gap: 4px;
}

.required-dot {
    color: #e05a3a;
    font-size: 16px;
    line-height: 1;
}

.field-input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 9px;
    font-size: 14px;
    color: #1a1a1a;
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
    font-family: inherit;
}

.field-input:focus {
    outline: none;
    border-color: #2a7d6f;
    box-shadow: 0 0 0 3px rgba(42, 125, 111, 0.12);
}

.field-input.is-error {
    border-color: #e05a3a;
    box-shadow: 0 0 0 3px rgba(224, 90, 58, 0.1);
}

.select-wrapper {
    position: relative;
}

.field-select {
    appearance: none;
    padding-right: 36px;
    cursor: pointer;
}

.select-arrow {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #999;
}

.field-textarea {
    resize: vertical;
    min-height: 100px;
    line-height: 1.6;
}

.char-counter {
    font-size: 12px;
    color: #aaa;
    text-align: right;
}

.field-error {
    font-size: 12px;
    color: #e05a3a;
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-badge-display {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border: 1px solid #e8e6e0;
    border-radius: 9px;
    background: #fafaf8;
    height: 40px;
    box-sizing: border-box;
}

.status-pill {
    font-size: 12px;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

.status-programado {
    background: #e8f0fe;
    color: #2d5fc4;
}

.status-hint {
    font-size: 12px;
    color: #aaa;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    padding-top: 0.5rem;
    border-top: 1px solid #f0ede8;
    margin-top: 0.25rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 20px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.15s;
    font-family: inherit;
}

.btn-primary {
    background: #2a7d6f;
    color: #fff;
}
.btn-primary:hover { background: #236860; }

.btn-ghost {
    background: transparent;
    color: #666;
    border: 1px solid #ddd;
}
.btn-ghost:hover { background: #f5f4f0; }
</style>

{{-- Contador de caracteres --}}
<script>
    const textarea = document.getElementById('Observaciones');
    const counter = document.getElementById('char-count');
    if (textarea && counter) {
        const update = () => counter.textContent = textarea.value.length;
        textarea.addEventListener('input', update);
        update();
    }
</script>

@endsection
