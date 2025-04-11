@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <h2 class="mb-4">Listado de PasosRutasProduccion</h2>

    {{-- 🔍 Formulario de búsqueda y cantidad por página --}}
    <form action="{{ route('produccion.abms.pasos_rutas_produccion.index') }}" method="GET" class="mb-3 d-flex flex-wrap gap-2">
        <div class="input-group">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar...">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('produccion.abms.pasos_rutas_produccion.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>

        <div class="input-group" style="max-width: 160px;">
            <input type="number" name="por_pagina" min="10" max="500" value="{{ request('por_pagina', $perPage ?? 100) }}" class="form-control" placeholder="x página" title="Cantidad por página">
        </div>
    </form>

    {{-- ➕ Crear nuevo --}}
    <a href="{{ route('produccion.abms.pasos_rutas_produccion.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>

    {{-- 📋 Tabla --}}
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    @foreach ($columnas as $col)
                        @php $tipo = $campos[$col]['input_type'] ?? 'text'; @endphp
                        @if (!empty($campos[$col]['incluir']) && $tipo !== 'hidden')
                            <th>{{ $campos[$col]['label'] ?? ucfirst(str_replace('_', ' ', $col)) }}</th>
                        @endif
                    @endforeach
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($registros as $registro)
                <tr>
                    @foreach ($columnas as $col)
                        @php
                            $meta = $campos[$col] ?? [];
                            $tipo = $meta['input_type'] ?? 'text';
                            $valor = $registro->$col;
                        @endphp
                        @if (!empty($meta['incluir']) && $tipo !== 'hidden')
                            <td>
                                @if (!empty($meta['is_boolean']))
                                    <input type="checkbox" disabled {{ in_array($valor, ['S', '1', 1]) ? 'checked' : '' }}>
                                @else
                                    {{ $valor }}
                                @endif
                            </td>
                        @endif
                    @endforeach

                    {{-- Acciones --}}
                    <td class="text-end">
                        <a href="{{ route('produccion.abms.pasos_rutas_produccion.edit', $registro[$primaryKey]) }}" class="btn btn-sm btn-primary">✏️</a>

                        <form action="{{ route('produccion.abms.pasos_rutas_produccion.destroy', $registro[$primaryKey]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 🔄 Paginación --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                {{ $registros->links('pagination::bootstrap-4') }}
            </div>
            <div class="text-muted small">
                {{ $registros->firstItem() }} a {{ $registros->lastItem() }} de {{ $registros->total() }} resultados
            </div>
        </div>
    </div>
</div>
@endsection
