@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <h2 class="mb-4">Listado de RutasProduccion</h2>
    <a href="{{ route('produccion.abms.rutas_produccion.create') }}" class="btn btn-success mb-3">➕ Nuevo</a>

    {{-- 🔍 Formulario de búsqueda --}}
    <form action="{{ route('produccion.abms.rutas_produccion.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar...">
            <button type="submit" class="btn btn-outline-primary">Buscar</button>
            @if(request('buscar'))
                <a href="{{ route('produccion.abms.rutas_produccion.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                @foreach ($registros as $registro)
    <tbody x-data="{ open: false }">
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

            <td>
                <button class="btn btn-sm btn-outline-primary" @click="open = !open">
                    <span x-show="!open">👁️ Ver Pasos</span>
                    <span x-show="open">🙈 Ocultar Pasos</span>
                </button>
                <a href="{{ route('produccion.abms.rutas_produccion.edit', $registro->id) }}" class="btn btn-sm btn-primary">✏️</a>
                <form action="{{ route('produccion.abms.rutas_produccion.destroy', $registro->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                </form>
            </td>
        </tr>

        <tr x-show="open" x-cloak>
    <td colspan="{{ count($columnas) + 1 }}">
        <div class="d-flex justify-content-end mb-2"> 

       <!-- aca empieza el codigo del create que se despliega -->
            <div x-data="{ showForm: false }">
    <button @click="showForm = !showForm" class="btn btn-sm btn-success mb-2">
        <span x-show="!showForm">➕ Agregar Paso</span>
        <span x-show="showForm">🙈 Cancelar</span>
    </button>

    <div x-show="showForm" x-cloak class="border p-3 mb-3 bg-light">
        
        <form action="{{ route('produccion.abms.pasos_rutas_produccion.store') }}" method="POST" class="row g-2">
            @csrf
            <!-- cod_ruta oculto -->
            <input type="hidden" name="cod_ruta" value="{{ $registro->cod_ruta }}">

            <div class="col-md-2">
                <label class="form-label">Cod Paso</label>
                <input type="number" name="cod_paso" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Sub Paso</label>
                <input type="number" name="sub_paso" class="form-control" value="0">
            </div>

            <div class="col-md-3">
                <label class="form-label">Sección</label>
                <select name="cod_seccion" class="form-select" required>
                    <option value="">Seleccione</option>
                    @foreach(\DB::table('secciones_produccion')->get() as $sec)
                        <option value="{{ $sec->cod_seccion }}">{{ $sec->denom_seccion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Ejecución</label>
                <select name="ejecucion" class="form-select" required>
                    <option value="">Seleccione</option>
                    <option value="1">Interna</option>
                    <option value="2">Externa</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="anulado" value="S" class="form-check-input" id="anulado_{{ $registro->cod_ruta }}">
                    <label class="form-check-label" for="anulado_{{ $registro->cod_ruta }}">Anulado</label>
                </div>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="tiene_subordinadas" value="S" class="form-check-input" id="tiene_{{ $registro->cod_ruta }}">
                    <label class="form-check-label" for="tiene_{{ $registro->cod_ruta }}">Sub.</label>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Jerarquía</label>
                <input type="text" name="jerarquia_seccion" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Subordinada de Sección</label>
                <input type="text" name="subordinada_de_seccion" class="form-control">
            </div>

            <div class="col-md-12 d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-success">💾 Guardar Paso</button>
            </div>
        </form>
    </div>
</div>

        </div>
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>Cod Ruta</th>
                    <th>Cod Paso</th>
                    <th>Sub Paso</th>
                    <th>Cod Sección</th>
                    <th>Ejecución</th>
                    <th>Anulado</th>
                    <th>Jerarquía Sección</th>
                    <th>Tiene Subordinadas</th>
                    <th>Subordinada de Sección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach (\App\Models\PasosRutasProduccion::where('cod_ruta', $registro->cod_ruta)->get() as $paso)
                    <tr>
                        <td>{{ $paso->cod_ruta }}</td>
                        <td>{{ $paso->cod_paso }}</td>
                        <td>{{ $paso->sub_paso }}</td>
                        <td>
                            @php
                                $seccion = \DB::table('secciones_produccion')
                                    ->where('cod_seccion', $paso->cod_seccion)
                                    ->value('denom_seccion');
                            @endphp
                            {{ $seccion ?? $paso->cod_seccion }}
                        </td>
                        <td>{{ $paso->ejecucion }}</td>
                        <td>
                            <input type="checkbox" disabled {{ in_array($paso->anulado, ['S', '1', 1]) ? 'checked' : '' }}>
                        </td>
                        <td>{{ $paso->jerarquia_seccion }}</td>
                        <td>
                            <input type="checkbox" disabled {{ in_array($paso->tiene_subordinadas, ['S', '1', 1]) ? 'checked' : '' }}>
                        </td>
                        <td>{{ $paso->subordinada_de_seccion }}</td>
                        <td>
                            <a href="{{ route('produccion.abms.pasos_rutas_produccion.edit', $paso->id) }}"
                               class="btn btn-sm btn-primary">✏️</a>

                            <form action="{{ route('produccion.abms.pasos_rutas_produccion.destroy', $paso->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </td>
</tr>

    </tbody>
@endforeach


</tbody>




        </table>
    </div>
</div>
@endsection
