@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <h2 class="mb-4">Listado de __MODELO__</h2>

    {{-- ➕ Botón para crear nuevo registro --}}
    <a href="{{ route('__NOMBRE_RUTA__.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>

    {{-- 🔍 Formulario de búsqueda --}}
    <form action="{{ route('__NOMBRE_RUTA__.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar...">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    {{-- 📋 Tabla de registros --}}
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    @foreach ($columnas as $col)
                        @php $tipo = $campos[$col]['input_type'] ?? 'text'; @endphp
                        @if (!empty($campos[$col]['incluir']) && $tipo !== 'hidden')
                            @php
                                $headerLabel = $campos[$col]['label'] ?? ucfirst(str_replace('_', ' ', $col));
                            @endphp
                            <th>{{ $headerLabel }}</th>
                        @endif
                    @endforeach
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($registros as $registro)
                    <tbody x-data="{ showSubform: false }">
                        <tr>
                            @foreach ($columnas as $col)
                                @php
                                    $meta = $campos[$col] ?? [];
                                    $tipo = $meta['input_type'] ?? 'text';
                                @endphp

                                @if (!empty($meta['incluir']) && $tipo !== 'hidden')
                                    @php
                                        $valor = $registro->$col;
                                        $isBoolean = !empty($meta['is_boolean']);
                                        $isSelect = $tipo === 'select';
                                        $isSelectList = $tipo === 'select_list';
                                    @endphp
                                    <td>
                                        @if ($isBoolean)
                                            <input type="checkbox" disabled {{ in_array($valor, ['S', '1', 1]) ? 'checked' : '' }}>
                                        @elseif ($isSelect && !empty($meta['referenced_table']) && !empty($meta['referenced_label']))
                                            @php
                                                $tabla = $meta['referenced_table'];
                                                $columna = $meta['referenced_column'] ?? 'id';
                                                $label = $meta['referenced_label'];
                                                $texto = \DB::table($tabla)->where($columna, $valor)->value($label);
                                            @endphp
                                            {{ $texto ?? $valor }}
                                        @elseif ($isSelectList && !empty($meta['select_list_data']))
                                            @php
                                                $opciones = collect(explode(',', $meta['select_list_data']))->mapWithKeys(function ($item) {
                                                    [$texto, $val] = array_pad(explode('=', $item, 2), 2, $item);
                                                    return [$val => $texto];
                                                });
                                            @endphp
                                            {{ $opciones[$valor] ?? $valor }}
                                        @else
                                            {{ $valor }}
                                        @endif
                                    </td>
                                @endif
                            @endforeach

                            {{-- 🛠️ Acciones con subformulario toggle --}}
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('__NOMBRE_RUTA__.edit', $registro->id) }}" class="btn btn-sm btn-primary">✏️</a>

                                    <form action="{{ route('__NOMBRE_RUTA__.destroy', $registro->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                    </form>

                                    <button @click="showSubform = true" x-show="!showSubform" type="button" class="btn btn-sm btn-outline-success">➕</button>

                                    <button @click="showSubform = !showSubform" type="button" class="btn btn-sm btn-outline-secondary">
                                        <span x-show="!showSubform">👁️</span>
                                        <span x-show="showSubform">🙈</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Subformulario inline condicional --}}
                        <tr x-show="showSubform">
                            <td colspan="{{ count($columnas) + 1 }}">
                                @if (!empty($subformularios))
                                    @foreach ($subformularios as $sub)
                                        @if ($sub['modo'] === 'inline')
                                            <x-koi-subformulario
                                                :registro="$registro"
                                                :subform="$sub"
                                                :rutaBase="basename($sub['carpeta_vistas'])"
                                            />
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    </tbody>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
