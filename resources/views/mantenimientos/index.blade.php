@push('styles')
<link rel="stylesheet" href="{{ asset('css/mantenimientos.css') }}">
@endpush

@extends('layouts.app')

@section('title', 'Mantenimientos')

@section('body')

<div class="container">
    @if(session('success'))

    <div class="alert-success">
        {{ session('success') }}
    </div>

    @endif

    <div class="page-header">

        <h1 class="page-title">
            Programación de Mantenimientos
        </h1>

        <a
            href="{{ route('mantenimientos.create') }}"
            class="btn btn-primary"
        >
            + Nuevo mantenimiento
        </a>

    </div>

    <div class="card">

        <form
            method="GET"
            action="{{ route('mantenimientos.index') }}"
            class="filter-form"
        >

            <select name="tecnico" class="form-control">

                <option value="">
                    -- Filtrar por técnico --
                </option>

                @foreach($tecnicos as $tec)

                    <option
                        value="{{ $tec->ID_User }}"
                        {{ request('tecnico') == $tec->ID_User ? 'selected' : '' }}
                    >
                        {{ $tec->Usuario }}
                    </option>

                @endforeach

            </select>

            <button class="btn btn-primary">
                Filtrar
            </button>

        </form>

    </div>

    <div class="card">

        <table class="table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Equipo</th>
                    <th>Tipo</th>
                    <th>Técnico</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

                @forelse($mantenimientos as $m)

                    <tr>

                        <td>{{ $m->ID_Mantenimiento }}</td>

                        <td>{{ $m->Codigo_Inventario }}</td>

                        <td>{{ $m->Tipo }}</td>

                        <td>{{ $m->Tecnico }}</td>

                        <td>{{ $m->Fecha_Programada }}</td>

                        <td>

                            <span class="badge
                                @if($m->Estado_Mantenimiento == 'Programado')
                                    badge-programado
                                @elseif($m->Estado_Mantenimiento == 'Completado')
                                    badge-completado
                                @elseif($m->Estado_Mantenimiento == 'Cancelado')
                                    badge-cancelado
                                @elseif($m->Estado_Mantenimiento == 'Reprogramado')
                                    badge-reprogramado
                                @endif
                            ">

                                {{ $m->Estado_Mantenimiento }}

                            </span>

                        </td>

                        <td style="display:flex; gap:10px;">

                            <a
                                href="{{ route('mantenimientos.edit', $m->ID_Mantenimiento) }}"
                                class="btn btn-primary"
                            >
                                Editar
                            </a>

                            <form
                                action="{{ route('mantenimientos.destroy', $m->ID_Mantenimiento) }}"
                                method="POST"
                                onsubmit="return confirm('¿Eliminar mantenimiento?')"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-danger"
                                >
                                    Eliminar
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">
                            No hay mantenimientos registrados
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection