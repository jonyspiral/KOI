@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Listado de __MODELO__</h2>

    <a href="{{ route('__NOMBRE_RUTA__.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>
   
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
                                @php
                                    $valor = $registro->$col;
                                    $meta = $campos[$col] ?? [];
                                    $isBoolean = !empty($meta['is_boolean']);
                                @endphp

                                @if ($isBoolean)
                                    <input type="checkbox" disabled {{ in_array($valor, ['S', '1', 1]) ? 'checked' : '' }}>
                                @else
                                    {{ $valor }}
                                @endif
                                </td>
                            @endif
                        @endforeach
                        <td>
                            <a href="{{ route('__NOMBRE_RUTA__.edit', $registro->id) }}" class="btn btn-sm btn-primary">✏️</a>
                            <form action="{{ route('__NOMBRE_RUTA__.destroy', $registro->id) }}" method="POST" class="d-inline">
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
