@extends('layouts.app')
@section('title', 'Editar Equipo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('body')
<div class="app-shell">

    @include('partials.sidebar')

    <div class="main-content">
        <header class="topbar">
            <span class="topbar-title">Editar Equipo</span>
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
                <a href="{{ route('equipos.index') }}"
                   style="color:var(--color-text-muted);text-decoration:none">Equipos</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span style="color:var(--color-text);font-weight:500">
                    {{ $equipo->Codigo_Inventario }}
                </span>
            </nav>

            <div class="page-heading">
                <h1>Editar equipo</h1>
                <p>Modifica los datos del equipo registrado</p>
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
                <form method="POST" action="{{ route('equipos.update', $equipo->ID_Equipo) }}">
                    @csrf
                    @method('PUT')

                    {{-- Código de inventario --}}
                    <div class="form-field">
                        <label for="Codigo_Inventario">
                            Código de inventario <span style="color:#ef4444">*</span>
                        </label>
                        <input type="text" id="Codigo_Inventario" name="Codigo_Inventario"
                               value="{{ old('Codigo_Inventario', $equipo->Codigo_Inventario) }}"
                               style="text-transform:uppercase"
                               required>
                        @error('Codigo_Inventario')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="form-field">
                        <label for="ID_Tipo">
                            Tipo de equipo <span style="color:#ef4444">*</span>
                        </label>
                        <select id="ID_Tipo" name="ID_Tipo" required>
                            <option value="">— Selecciona un tipo —</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->ID_Tipo }}"
                                    {{ old('ID_Tipo', $equipo->ID_Tipo) == $tipo->ID_Tipo ? 'selected' : '' }}>
                                    {{ $tipo->Nombre_Tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Tipo')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Marca y Modelo --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div class="form-field">
                            <label for="Marca">Marca</label>
                            <input type="text" id="Marca" name="Marca"
                                   value="{{ old('Marca', $equipo->Marca) }}"
                                   placeholder="Ej. Lenovo, HP, Dell">
                            @error('Marca')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-field">
                            <label for="Modelo">Modelo</label>
                            <input type="text" id="Modelo" name="Modelo"
                                   value="{{ old('Modelo', $equipo->Modelo) }}"
                                   placeholder="Ej. ThinkPad E14">
                            @error('Modelo')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Ubicación --}}
                    <div class="form-field">
                        <label for="ID_Ubicacion">
                            Ubicación <span style="color:#ef4444">*</span>
                        </label>
                        <select id="ID_Ubicacion" name="ID_Ubicacion" required>
                            <option value="">— Selecciona una sede —</option>
                            @foreach($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion->ID_Ubicacion }}"
                                    {{ old('ID_Ubicacion', $equipo->ID_Ubicacion) == $ubicacion->ID_Ubicacion ? 'selected' : '' }}>
                                    {{ $ubicacion->NombreSede }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Ubicacion')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="form-field">
                        <label for="ID_Estado">
                            Estado <span style="color:#ef4444">*</span>
                        </label>
                        <select id="ID_Estado" name="ID_Estado" required>
                            <option value="">— Selecciona un estado —</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->ID_Estado }}"
                                    {{ old('ID_Estado', $equipo->ID_Estado) == $estado->ID_Estado ? 'selected' : '' }}>
                                    {{ $estado->Estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('ID_Estado')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions" style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2">
                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                            </svg>
                            Guardar cambios
                        </button>
                        <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>

                </form>
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