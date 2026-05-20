@extends('layouts.app')

@section('title', 'Editar mantenimiento')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/mantenimientos.css') }}">
@endpush

@section('body')

<div class="container">

    <div class="page-header">

        <h1 class="page-title">
            Editar mantenimiento
        </h1>

    </div>

    <div class="card">

        <form
            method="POST"
            action="{{ route('mantenimientos.update', $mantenimiento->ID_Mantenimiento) }}"
        >

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Equipo</label>

                <select
                    name="ID_Equipo"
                    class="form-control"
                    required
                >

                    @foreach($equipos as $equipo)

                        <option
                            value="{{ $equipo->ID_Equipo }}"
                            {{ $mantenimiento->ID_Equipo == $equipo->ID_Equipo ? 'selected' : '' }}
                        >
                            {{ $equipo->Codigo_Inventario }}
                        </option>

                    @endforeach

                </select>

            </div>

            <br>

            <div class="form-group">

                <label>Técnico</label>

                <select
                    name="ID_Tecnico"
                    class="form-control"
                    required
                >

                    @foreach($tecnicos as $tec)

                        <option
                            value="{{ $tec->ID_User }}"
                            {{ $mantenimiento->ID_Tecnico == $tec->ID_User ? 'selected' : '' }}
                        >
                            {{ $tec->Usuario }}
                        </option>

                    @endforeach

                </select>

            </div>

            <br>

            <div class="form-group">

                <label>Fecha programada</label>

                <input
                    type="date"
                    name="Fecha_Programada"
                    class="form-control"
                    value="{{ $mantenimiento->Fecha_Programada }}"
                    required
                >

            </div>

            <br>

            <div class="form-group">

                <label>Estado</label>

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

            <br>

            <div class="form-group">

                <label>Observaciones</label>

                <textarea
                    name="Observaciones"
                    class="form-control"
                    rows="4"
                >{{ $mantenimiento->Observaciones }}</textarea>

            </div>

            <br>

            <button class="btn btn-primary">
                Actualizar mantenimiento
            </button>

        </form>

    </div>

</div>

@endsection