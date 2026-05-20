@extends('layouts.app')

@section('title', 'Nuevo mantenimiento')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mantenimientos.css') }}">
@endpush

@section('body')

<div class="container">

    <div class="page-header">

        <h1 class="page-title">
            Programar mantenimiento
        </h1>

    </div>

    <div class="card">

        <h2 class="card-title">
            Información del mantenimiento
        </h2>

        <form
            method="POST"
            action="{{ route('mantenimientos.store') }}"
        >

            @csrf

            <div class="form-grid">

                {{-- EQUIPO --}}
                <div class="form-group">

                    <label class="form-label">
                        Equipo
                    </label>

                    <select
                        name="ID_Equipo"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Seleccione un equipo
                        </option>

                        @foreach($equipos as $equipo)

                            <option value="{{ $equipo->ID_Equipo }}">

                                {{ $equipo->Codigo_Inventario }}
                                —
                                {{ $equipo->Tipo }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- TECNICO --}}
                <div class="form-group">

                    <label class="form-label">
                        Técnico asignado
                    </label>

                    <select
                        name="ID_Tecnico"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Seleccione un técnico
                        </option>

                        @foreach($tecnicos as $tec)

                            <option value="{{ $tec->ID_User }}">
                                {{ $tec->Usuario }}
                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- FECHA --}}
                <div class="form-group">

                    <label class="form-label">
                        Fecha programada
                    </label>

                    <input
                        type="date"
                        name="Fecha_Programada"
                        class="form-control"
                        required
                    >

                </div>

                {{-- ESTADO --}}
                <div class="form-group">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        name="Estado_Mantenimiento"
                        class="form-control"
                    >

                        <option value="Programado">
                            Programado
                        </option>

                        <option value="Completado">
                            Completado
                        </option>

                        <option value="Cancelado">
                            Cancelado
                        </option>

                        <option value="Reprogramado">
                            Reprogramado
                        </option>

                    </select>

                </div>

                {{-- OBSERVACIONES --}}
                <div class="form-group form-group-full">

                    <label class="form-label">
                        Observaciones
                    </label>

                    <textarea
                        name="Observaciones"
                        rows="5"
                        class="form-control"
                        placeholder="Escriba observaciones del mantenimiento..."
                    ></textarea>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar mantenimiento
                </button>

                <a
                    href="{{ route('mantenimientos.index') }}"
                    class="btn btn-secondary"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

@endsection