@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <h2 class="mb-4">Listado de ColoresPorArticulo</h2>

    @php

    $columnasOrdenadas = collect($columnas)
        ->filter(fn($col) => !empty($campos[$col]['incluir']) && ($campos[$col]['input_type'] ?? '') !== 'hidden')
        ->sortBy(fn($col) => $campos[$col]['orden'] ?? 0)
        ->toArray();

        $formViewType = 'modal';
    @endphp

    @if ($formViewType === 'modal')
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalCreate">➕ Nuevo</button>
    @else
        <a href="{{ route('produccion.abms.colores_por_articulo.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>
    @endif

    <form action="{{ route('produccion.abms.colores_por_articulo.index') }}" method="GET" class="mb-3 d-flex flex-wrap gap-2">
        <div class="input-group">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar...">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('produccion.abms.colores_por_articulo.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>
        <div class="input-group" style="max-width: 160px;">
            <input type="number" name="por_pagina" min="10" max="500" value="{{ request('por_pagina', $perPage ?? 100) }}" class="form-control" placeholder="x página" title="Cantidad por página">
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    @foreach ($columnasOrdenadas as $col)
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
    @php
        $eliminado = $registro->sync_status === 'D';
    @endphp

    <tr x-data="{ showSubform: false }" class="{{ $eliminado ? 'fila-eliminada' : '' }}">
    @foreach ($columnasOrdenadas as $col)

            @php
                $meta = $campos[$col] ?? [];
                $tipo = $meta['input_type'] ?? 'text';
                $valor = $registro->$col;
            @endphp
            @if (!empty($meta['incluir']) && $tipo !== 'hidden')
                <td class="{{ $eliminado ? 'text-muted' : '' }}">
                    @if (!empty($meta['is_boolean']))
                        <input type="checkbox" disabled {{ in_array($valor, ['S', '1', 1]) ? 'checked' : '' }}>
                    @else
                        {{ $valor }}
                    @endif
                </td>
            @endif
        @endforeach

        <td class="text-end">
            <div class="d-flex gap-1 justify-content-end">
                @if (!$eliminado)

                <button 
                    type="button" 
                    class="btn btn-sm btn-primary" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalEdit_{{ $registro[$primaryKey] }}">
                    ✏️
                </button>

                
               
                    <form action="{{ route('produccion.abms.colores_por_articulo.destroy', $registro[$primaryKey]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                @endif

                @if ($eliminado)
                <form action="{{ route('produccion.abms.colores_por_articulo.restaurar', $registro->{$primaryKey}) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success">♻️ Restaurar</button>
                </form>

                    <span class="badge bg-secondary mt-1">🗃 Eliminado</span>
                @endif

                <button @click="showSubform = !showSubform" type="button" class="btn btn-sm btn-outline-secondary">
                    <span x-show="!showSubform">👁️</span>
                    <span x-show="showSubform">🙈</span>
                </button>
            </div>
        </td>
    </tr>

    <tr x-show="showSubform">
        <td colspan="{{ count($columnas) + 1 }}">
            @if (!empty($subformularios))
                @foreach ($subformularios as $sub)
                    @if ($sub['modo'] === 'inline')
                        <x-koi-subformulario :registro="$registro" :subform="$sub" :rutaBase="basename($sub['carpeta_vistas'])" />
                    @endif
                @endforeach
            @endif
        </td>
    </tr>
@endforeach
</tbody>


        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            {{ $registros->links('pagination::bootstrap-4') }}
        </div>
        <div class="text-muted small">
            {{ $registros->firstItem() }} a {{ $registros->lastItem() }} de {{ $registros->total() }} resultados
        </div>
    </div>

    @php


    
    $defaults = [];

    foreach ($campos as $campo => $meta) {
        $defaults[$campo] = $meta['default'] ?? '';

        if (($meta['input_type'] ?? null) === 'autonumerico' && empty($defaults[$campo])) {
            try {
                $modeloSql = "\\App\\Models\\Sql\\{$modelo}";
                $defaults[$campo] = $modeloSql::max($campo) + 1;
            } catch (\Throwable $e) {
                $defaults[$campo] = 1;
            }
        }
    }
@endphp
    @if ($formViewType === 'modal')
        @include('produccion/abms/colores_por_articulo.create-modal', ['registro' => []])
    @endif
    @if ($formViewType === 'modal')
    @foreach ($registros as $registro)
    @php
        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? '') === 'date' && !empty($registro->$campo)) {
                $registro->$campo = \Carbon\Carbon::parse($registro->$campo)->format('Y-m-d');
            }
        }
    @endphp
        @include('produccion/abms/colores_por_articulo.edit-modal', ['registro' => $registro])
    @endforeach
    @endif

</div>
@endsection
