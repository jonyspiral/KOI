@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Listado de FamiliasProducto</h2>

    <a href="{{ route('produccion.abms.familias_producto.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>
   
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    @foreach ($columnas as $col)
                        @if (!empty($campos[$col]['incluir']))
                            <th>{{ $col }}</th>
                        @endif
                    @endforeach
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registros as $registro)
                    <tr>
                        @foreach ($columnas as $col)
                            @if (!empty($campos[$col]['incluir']))
                                <td>
                                    @if(isset($campos[$col]['tipo']) && $campos[$col]['tipo'] === 'boolean')
                                        {{ $registro->$col === 'S' ? '✅' : '✖️' }}
                                    @else
                                        {{ $registro->$col }}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        <td>
                            <a href="{{ route('produccion.abms.familias_producto.edit', $registro->id) }}" class="btn btn-sm btn-primary">✏️</a>
                            <form action="{{ route('produccion.abms.familias_producto.destroy', $registro->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
