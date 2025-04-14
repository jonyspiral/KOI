@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">👁️ Detalle de Marca</h2>

    <div class="card mb-4">
        <div class="card-body">
            <dl class="row">
                @foreach ($campos as $campo => $meta)
                    @php
                        $tipo = $meta['input_type'] ?? 'text';
                        $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
                        $valor = $registro->$campo ?? null;
                    @endphp

                    @if (!empty($meta['incluir']) && $tipo !== 'hidden')
                        <dt class="col-sm-3">{{ $label }}</dt>
                        <dd class="col-sm-9">
                            @if ($tipo === 'checkbox')
                                <span class="badge bg-{{ $valor === 'S' ? 'success' : 'secondary' }}">
                                    {{ $valor === 'S' ? 'Sí' : 'No' }}
                                </span>
                            @elseif ($tipo === 'select' && !empty($meta['referenced_table']) && !empty($meta['referenced_label']))
                                @php
                                    $tabla = $meta['referenced_table'];
                                    $columna = $meta['referenced_column'] ?? 'id';
                                    $labelFk = $meta['referenced_label'];
                                    $texto = \DB::table($tabla)->where($columna, $valor)->value($labelFk);
                                @endphp
                                {{ $texto ?? $valor }}
                            @elseif ($tipo === 'select_list' && !empty($meta['select_list_data']))
                                @php
                                    $opciones = collect(explode(',', $meta['select_list_data']))->mapWithKeys(function ($item) {
                                        [$texto, $val] = array_pad(explode('=', $item, 2), 2, $item);
                                        return [$val => $texto];
                                    });
                                @endphp
                                {{ $opciones[$valor] ?? $valor }}
                            @else
                                {{ $valor ?? '—' }}
                            @endif
                        </dd>
                    @endif
                @endforeach
            </dl>
        </div>
    </div>

    <a href="{{ route('produccion.abms.marcas.index') }}" class="btn btn-secondary">⬅️ Volver</a>
    <a href="{{ route('produccion.abms.marcas.edit', $registro[$primaryKey]) }}" class="btn btn-primary">✏️ Editar</a>
</div>
@endsection
