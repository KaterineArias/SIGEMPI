<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimientos - SIGEMPI</title>
    
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <h1>Programación de Mantenimientos</h1>

    <form action="{{ route('mantenimientos.index') }}" method="GET" style="margin-bottom: 20px;">
        <label for="tecnico_id">Filtrar por Técnico:</label>
        <select name="tecnico_id" onchange="this.form.submit()">
            <option value="">Todos los técnicos</option>
            @foreach($tecnicos as $tecnico)
                <option value="{{ $tecnico->ID_User }}" {{ request('tecnico_id') == $tecnico->ID_User ? 'selected' : '' }}>
                    {{ $tecnico->Usuario }}
                </option>
            @endforeach
        </select>
    </form>

    <a href="{{ route('mantenimientos.create') }}" class="btn">Programar Nuevo Mantenimiento</a>

    <table>
        <thead>
            <tr>
                <th>Equipo (Viñeta)</th>
                <th>Técnico Asignado</th>
                <th>Fecha Programada</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mantenimientos as $mant)
            <tr>
                <td>{{ $mant->equipo->Codigo_Inventario }} - {{ $mant->equipo->Tipo }}</td>
                <td>{{ $mant->tecnico->Usuario }}</td>
                <td>{{ $mant->Fecha_Programada }}</td>
                <td>{{ $mant->Estado_Mantenimiento }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">No hay mantenimientos programados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>