@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠 Campos para el modelo: {{ $modelo }}</h2>

    {{-- Formulario principal para guardar todo el ABM, incluyendo subformularios --}}
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
        @csrf
        <input type="hidden" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="namespace" value="{{ $namespace }}">
        <input type="hidden" name="carpeta_vistas" value="{{ str_replace('\\', '/', $carpeta_vistas) }}">
        <input type="hidden" name="primary_key" value="{{ $primary_key }}">
        <input type="hidden" name="primary_key_sql" value="{{ implode(',', $primary_key_sql ?? []) }}">
        @if (!empty($camposSubform))
            <input type="hidden" name="subform_index" value="{{ $subformIndex }}">
            <input type="hidden" name="modelo_hijo" value="{{ $modeloHijo }}">
        @endif

        {{-- Campos del modelo padre --}}
        @include('sistemas.abms.partials.form_fields')

        {{-- Configuración y menú --}}
        @include('sistemas.abms.partials.form_config')
        @include('sistemas.abms.partials.menu_config')

        {{-- Tabla de subformularios existentes --}}
        @php
            $subformUrlBase = url('sistemas/abms/preview/' . $modelo);
        @endphp
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Modelo</th>
                        <th>Nombre</th>
                        <th>FK</th>
                        <th>View Type</th>
                        <th>Título</th>
                        <th>Modo</th>
                        <th>Orden</th>
                        <th>Cargar Campos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subformularios as $index => $sub)
                        <tr>
                            <td>{{ $sub['modelo'] }}</td>
                            <td>{{ $sub['nombre'] }}</td>
                            <td>{{ $sub['foreign_key'] }}</td>
                            <td>{{ $sub['view_type'] }}</td>
                            <td>{{ $sub['titulo'] }}</td>
                            <td>{{ $sub['modo'] }}</td>
                            <td><input type="number" name="subformularios[{{ $index }}][orden]" value="{{ $sub['orden'] ?? $index }}" class="form-control form-control-sm" style="width: 70px;"></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="window.location.href='{{ $subformUrlBase }}?subform_index={{ $index }}&modelo_hijo={{ $sub['modelo'] }}'">🔄</button>

                                {{-- Hidden fields necesarios para persistencia --}}
                                <input type="hidden" name="subformularios[{{ $index }}][modelo]" value="{{ $sub['modelo'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][foreign_key]" value="{{ $sub['foreign_key'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][nombre]" value="{{ $sub['nombre'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][titulo]" value="{{ $sub['titulo'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][view_type]" value="{{ $sub['view_type'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][modo]" value="{{ $sub['modo'] }}">
                                <input type="hidden" name="subformularios[{{ $index }}][carpeta_vistas]" value="{{ str_replace('\\', '/', $sub['carpeta_vistas'] ?? $carpeta_vistas) }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Subformulario actual, si se están cargando sus campos --}}
        @if (!empty($camposSubform))
            <h5 class="mt-4">🧬 Campos del subformulario <strong>{{ $modeloHijo }}</strong></h5>
            <input type="hidden" name="subformularios[{{ $subformIndex }}][modelo]" value="{{ $modeloHijo }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][foreign_key]" value="{{ $subformularios[$subformIndex]['foreign_key'] ?? '' }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][nombre]" value="{{ $subformularios[$subformIndex]['nombre'] ?? '' }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][titulo]" value="{{ $subformularios[$subformIndex]['titulo'] ?? '' }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][view_type]" value="{{ $subformularios[$subformIndex]['view_type'] ?? 'inline' }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][modo]" value="{{ $subformularios[$subformIndex]['modo'] ?? 'inline' }}">
            <input type="hidden" name="subformularios[{{ $subformIndex }}][carpeta_vistas]" value="{{ str_replace('\\', '/', $subformularios[$subformIndex]['carpeta_vistas'] ?? $carpeta_vistas) }}">

            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Campo</th>
                            <th>Label</th>
                            <th>Tipo Input</th>
                            <th class="text-center">Incluir<br><input type="checkbox" class="toggle-col" data-clase="incluir"></th>
                            <th class="text-center">Sync<br><input type="checkbox" class="toggle-col" data-clase="sync"></th>
                            <th class="text-center">Nullable<br><input type="checkbox" class="toggle-col" data-clase="nullable"></th>
                            <th class="text-center">Readonly<br><input type="checkbox" class="toggle-col" data-clase="readonly"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (is_iterable($camposSubform) && count($camposSubform))
                            @foreach ($camposSubform as $campo => $meta)
                                <tr>
                                    <td>{{ $campo }}</td>
                                    <td>
                                        <input name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][label]"
                                               class="form-control form-control-sm"
                                               value="{{ data_get($meta, 'label', ucfirst(str_replace('_', ' ', $campo))) }}">
                                    </td>
                                    <td>
                                        <input name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][input_type]"
                                               class="form-control form-control-sm"
                                               value="{{ data_get($meta, 'input_type', 'text') }}">
                                    </td>
                                    @foreach (['incluir', 'sync', 'nullable', 'readonly'] as $prop)
                                        <td class="text-center">
                                            <input type="hidden" name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][{{ $prop }}]" value="0">
                                            <input type="checkbox"
                                                   class="toggle-{{ $prop }}"
                                                   name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][{{ $prop }}]"
                                                   value="1"
                                                 {{ (isset($meta[$prop]) && ($meta[$prop] === true || $meta[$prop] === '1' || $meta[$prop] === 1)) ? 'checked' : '' }}>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted">⚠ No se cargaron campos del subformulario. Usá el botón 🔄 para cargarlos.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Nuevo botón con ruta específica para subformularios --}}
            <div class="mt-3">
                <button formaction="{{ route('sistemas.abms.guardar_subformulario') }}" formmethod="POST" class="btn btn-success">📂 Guardar subformulario</button>
            </div>
        @endif

        {{-- Botón de guardado general (modelo y subformulario incluidos) --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">🗓 Guardar todo y generar archivos</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-col').forEach(master => {
        master.addEventListener('change', function () {
            const clase = this.dataset.clase;
            document.querySelectorAll('input.toggle-' + clase).forEach(cb => {
                cb.checked = master.checked;
            });
        });
    });
});
</script>
@endpush
