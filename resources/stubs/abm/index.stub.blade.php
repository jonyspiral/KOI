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
                    {{-- 🧩 Encabezados dinámicos según configuración de campos --}}
                    @foreach ($columnas as $col)
                        @php $tipo = $campos[$col]['input_type'] ?? 'text'; @endphp
                        @if (!empty($campos[$col]['incluir']) && $tipo !== 'hidden')
                            @php
                                $headerLabel = $campos[$col]['label'] ?? ucfirst(str_replace('_', ' ', $col));
                            @endphp
                            <th>{{ $headerLabel }}</th>
                        @endif
                    @endforeach
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                {{-- 🔁 Listado principal de registros --}}
                @foreach ($registros as $registro)
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

                        {{-- 🛠️ Acciones por registro --}}
                        <td>
                            <a href="{{ route('__NOMBRE_RUTA__.edit', $registro->id) }}" class="btn btn-sm btn-primary">✏️</a>
                            <form action="{{ route('__NOMBRE_RUTA__.destroy', $registro->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>

                    {{-- ✅ Subformularios INLINE generados dinámicamente para cada registro padre --}}
                    @if (!empty($subformularios))
                        @foreach ($subformularios as $sub)
                            @if ($sub['modo'] === 'inline')
                                <tr>
                                    <td colspan="{{ count($columnas) + 1 }}">
                                        <x-koi-subformulario
                                            :modelo="$sub['modelo']"
                                            :tabla="$sub['tabla']"
                                            :foreign_key="$sub['foreign_key']"
                                            :valor_foreign="$registro->{$sub['foreign_key']}"
                                            :modo="$sub['modo']"
                                            :titulo="$sub['titulo'] ?? 'Subformulario'"
                                        />
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
