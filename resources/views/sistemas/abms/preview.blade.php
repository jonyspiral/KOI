{{-- preview.blade.php v3 - Sofía - ABM Creator avanzado --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠️ Configurar campos del formulario para el modelo: {{ $modelo }}</h2>

    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
        @csrf
        <input type="hidden" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="namespace" value="{{ $namespace }}">
        <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Campo</th>
                    <th>Tipo</th>
                    <th>Nullable</th>
                    <th>Default</th>
                    <th>Visible</th>
                    <th>Input Type</th>
                    <th>Boolean?</th>
                    <th>Max+1?</th>
                    <th>Es Foreign?</th>
                    <th>Tabla FK</th>
                    <th>Campo FK</th>
                    <th>Label FK</th>
                    <th>Incluir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fields as $campo => $meta)
                @php
                    $isId = $campo === 'id';
                    $isNumeric = in_array($meta['type'], ['int', 'bigint', 'tinyint', 'smallint']);
                    $isBool = isset($meta['type']) && in_array(strtolower($meta['type']), ['bit', 'bool', 'boolean', 'tinyint']);
                @endphp
                <tr>
                    <td>{{ $campo }}</td>
                    <td>{{ $meta['type'] ?? '-' }}</td>
                    <td>{{ $meta['nullable'] ? 'Sí' : 'No' }}</td>

                    {{-- Default editable --}}
                    <td>
                        <input type="text" name="campos[{{ $campo }}][default]" class="form-control"
                               value="{{ $meta['default'] ?? '' }}">
                    </td>

                    {{-- Visible --}}
                    <td>
                        <input type="checkbox" name="campos[{{ $campo }}][visible]" value="1"
                               {{ $isId ? '' : 'checked' }} {{ $isId ? 'disabled' : '' }}>
                    </td>

                    {{-- Input Type --}}
                    <td>
                        <select name="campos[{{ $campo }}][input_type]" class="form-select">
                            <option value="text">text</option>
                            <option value="number" {{ $isNumeric ? 'selected' : '' }}>number</option>
                            <option value="date">date</option>
                            <option value="select" {{ !empty($meta['foreign']) ? 'selected' : '' }}>select</option>
                            <option value="textarea">textarea</option>
                            <option value="checkbox" {{ $isBool ? 'selected' : '' }}>checkbox</option>
                            <option value="hidden" {{ $isId ? 'selected' : '' }}>hidden</option>
                        </select>
                    </td>

                    {{-- Boolean --}}
                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][is_boolean]" value="1"
                               {{ $isBool ? 'checked' : '' }}>
                    </td>

                    {{-- Max +1 --}}
                    <td class="text-center">
                        <input type="checkbox" name="campos[{{ $campo }}][auto_increment_plus]" value="1">
                    </td>

                    {{-- Foreign --}}
                    <td>
                        <input type="checkbox" name="campos[{{ $campo }}][foreign]" value="1"
                               {{ !empty($meta['foreign']) ? 'checked' : '' }}>
                    </td>

                    {{-- Tabla FK --}}
                    <td>
                        <input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control"
                               value="{{ $meta['referenced_table'] ?? '' }}">
                    </td>

                    {{-- Campo FK --}}
                    <td>
                        <input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control"
                               value="{{ $meta['referenced_column'] ?? 'id' }}">
                    </td>

                    {{-- Label FK --}}
                    <td>
                        <input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control"
                               value="{{ $meta['referenced_label'] ?? 'nombre' }}">
                    </td>

                    {{-- Incluir --}}
                    <td>
                        <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" checked>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="agregar_ruta" id="agregarRuta" value="1" checked>
              <label class="form-check-label" for="agregarRuta">
                  🧭 Agregar automáticamente la ruta al archivo <code>web.php</code>
              </label>
        </div>


        <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="force_controlador" id="forceControlador" value="1">
              <label class="form-check-label" for="forceControlador">
                  🔁 Sobrescribir controlador existente
            </label>
        </div>


        <div class="form-check mb-4">

              <input class="form-check-input" type="checkbox" name="generar_request" id="generarRequest" value="1" checked>
              <label class="form-check-label" for="generarRequest">
              🛡️ Generar clase de validación automática (FormRequest)
              </label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">👉 Continuar con la generación del ABM</button>
        <a href="{{ route('sistemas.abms.crear') }}" class="btn btn-secondary">⬅️ Volver</a>
    </form>
</div>
@endsection