@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠 Campos para el modelo: {{ $modelo }}</h2>

    {{-- Formulario principal para guardar todo el ABM --}}
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
        @csrf
        <input type="hidden" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="namespace" value="{{ $namespace }}">
        <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">
        <input type="hidden" name="primary_key" value="{{ $primary_key }}">
        <input type="hidden" name="primary_key_sql" value="{{ implode(',', $primary_key_sql ?? []) }}">

        {{-- Tabla de Campos del modelo principal --}}
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Orden</th>
                        <th>Campo</th>
                        <th>Tipo SQL</th>
                        <th>Label</th>
                        <th>Tipo Input</th>
                        <th>Default</th>
                        <th class="text-center">Incluir</th>
                        <th class="text-center">Sync</th>
                        <th class="text-center">Nullable</th>
                        <th class="text-center">Readonly</th>
                        <th>Tabla FK</th>
                        <th>Columna FK</th>
                        <th>Label FK</th>
                        <th>Valores</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $modelSql = "\\App\\Models\\Sql\\{$modelo}";
                        $fieldsMeta = method_exists($modelSql, 'fieldsMeta') ? $modelSql::fieldsMeta() : [];
                    @endphp
                    @foreach ($fields as $campo => $meta)
                        @php $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo)); @endphp
                        <tr>
                            <td><input type="number" name="campos[{{ $campo }}][orden]" class="form-control form-control-sm" value="{{ $meta['orden'] ?? 0 }}" style="width: 60px;"></td>
                            <td>{{ $campo }}</td>
                            <td class="text-muted">{{ $fieldsMeta[$campo]['type'] ?? 'n/a' }}</td>
                            <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                            <td>
                                <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                                    @foreach (['text','number','decimal','moneda','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                                        <option value="{{ $tipo }}" @selected(($meta['input_type'] ?? 'text') === $tipo)>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $meta['default'] ?? '' }}"></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked(!empty($meta['incluir']))></td>
                            <td class="text-center"><input type="hidden" name="campos[{{ $campo }}][sync]" value="0"><input type="checkbox" name="campos[{{ $campo }}][sync]" value="1" @checked(!empty($meta['sync']))></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked(!empty($meta['nullable']))></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][readonly]" value="1" @checked(!empty($meta['readonly']))></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $meta['referenced_table'] ?? '' }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $meta['referenced_column'] ?? 'id' }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $meta['referenced_label'] ?? 'nombre' }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $meta['select_list_data'] ?? '' }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('sistemas.abms.partials.form_config')
        @include('sistemas.abms.partials.menu_config')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">📏 Guardar configuración</button>
        </div>
    </form> {{-- cierre del formulario principal --}}

    {{-- Formulario para agregar nuevo subformulario (fuera del form principal) --}}
    <hr class="my-4">
    <h5>🧱 Subformularios</h5>

    <form action="{{ route('sistemas.abms.preview', ['modelo' => $modelo]) }}" method="POST" class="mb-3">
        @csrf
        <input type="hidden" name="accion" value="agregar_subform">
        <div class="row g-2 mb-2">
            <div class="col-md-3">
                <input name="nuevo_subform[modelo]" class="form-control form-control-sm" placeholder="🧩 Modelo hijo">
            </div>
            <div class="col-md-2">
                <input name="nuevo_subform[foreign_key]" class="form-control form-control-sm" placeholder="🔗 Foreign key">
            </div>
            <div class="col-md-2">
                <input name="nuevo_subform[nombre]" class="form-control form-control-sm" placeholder="📛 Nombre técnico">
            </div>
            <div class="col-md-2">
                <input name="nuevo_subform[titulo]" class="form-control form-control-sm" placeholder="📋 Título visible">
            </div>
            <div class="col-md-1">
                <select name="nuevo_subform[view_type]" class="form-select form-select-sm">
                    <option value="inline">Inline</option>
                    <option value="modal">Modal</option>
                    <option value="tab">Tab</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="nuevo_subform[modo]" class="form-select form-select-sm">
                    <option value="inline">Inline</option>
                    <option value="modal">Modal</option>
                    <option value="tab">Tab</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">➕</button>
            </div>
        </div>
    </form>

    {{-- Tabla de subformularios existentes --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
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
                            <form action="{{ route('sistemas.abms.preview', ['modelo' => $modelo]) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="subform_index" value="{{ $index }}">
                                <input type="hidden" name="modelo_hijo" value="{{ $sub['modelo'] }}">
                                <button type="submit" class="btn btn-sm btn-secondary">🔄</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if (!empty($camposSubform))
    <h5 class="mt-4">🧬 Campos del subformulario <strong>{{ $modeloHijo }}</strong></h5>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Campo</th>
                    <th>Label</th>
                    <th>Tipo Input</th>
                    <th class="text-center">Incluir</th>
                    <th class="text-center">Sync</th>
                    <th class="text-center">Nullable</th>
                    <th class="text-center">Readonly</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($camposSubform as $campo => $meta)
                    <tr>
                        <td>{{ $campo }}</td>
                        <td>
                            <input name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][label]"
                                class="form-control form-control-sm"
                                value="{{ $meta['label'] }}">
                        </td>
                        <td>
                            <input name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][input_type]"
                                class="form-control form-control-sm"
                                value="{{ $meta['input_type'] }}">
                        </td>
                        <td class="text-center">
                            <input type="checkbox"
                                name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][incluir]"
                                value="1" {{ !empty($meta['incluir']) ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox"
                                name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][sync]"
                                value="1" {{ !empty($meta['sync']) ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox"
                                name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][nullable]"
                                value="1" {{ !empty($meta['nullable']) ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="checkbox"
                                name="subformularios[{{ $subformIndex }}][campos][{{ $campo }}][readonly]"
                                value="1" {{ !empty($meta['readonly']) ? 'checked' : '' }}>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

    </div>
</div>
@endsection
