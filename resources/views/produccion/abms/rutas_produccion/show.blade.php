@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h2 class="mb-4">Ruta: {{ $ruta->nombre_ruta ?? $ruta->cod_ruta }}</h2>

    <p><strong>Código:</strong> {{ $ruta->cod_ruta }}</p>
    <p><strong>Descripción:</strong> {{ $ruta->descripcion ?? '-' }}</p>

    <hr>
    <h4>Pasos de la Ruta</h4>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>Sección</th>
                <th>Orden</th>
                <th>Tiempo Estimado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ruta->pasos as $paso)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $paso->cod_seccion }}</td>
                    <td>{{ $paso->orden }}</td>
                    <td>{{ $paso->tiempo_estimado ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay pasos asignados a esta ruta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('produccion.abms.rutas_produccion.index') }}" class="btn btn-secondary">⬅️ Volver</a>
</div>
@endsection
