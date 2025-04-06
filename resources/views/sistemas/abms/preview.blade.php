@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠 Configurar campos para el modelo: {{ $modelo }}</h2>
    <form action="{{ route('sistemas.abms.configurar') }}" method="POST">
        @csrf

        <input type="hidden" name="modelo" value="{{ $modelo }}">
        <input type="hidden" name="namespace" value="{{ $namespace }}">
        <input type="hidden" name="carpeta_vistas" value="{{ $carpeta_vistas }}">

        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Campo</th> 
                        <th>Label</th>
                        <th>Tipo Input</th>
                        <th>Default</th>
                        <th>Incluir</th>
                        <th>Nullable</th>
                        <th>Tabla FK</th>
                        <th>Columna FK</th>
                        <th>Label FK</th>
                        <th>Valores (select_list / checkbox)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fields as $campo => $meta)
                        @php
                            $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
                            $inputType = $meta['input_type'] ?? 'text';
                            $default = $meta['default'] ?? '';
                            $incluir = $meta['incluir'] ?? false;
                            $nullable = $meta['nullable'] ?? false;
                            $referenced_table = $meta['referenced_table'] ?? '';
                            $referenced_column = $meta['referenced_column'] ?? 'id';
                            $referenced_label = $meta['referenced_label'] ?? 'nombre';
                            $select_list_data = $meta['select_list_data'] ?? '';
                        @endphp
                        <tr>
                            <td>{{ $campo }}</td>
                            <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                            <td>
                                <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                                    @foreach (['text','number','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                                        <option value="{{ $tipo }}" @selected($inputType === $tipo)>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $default }}"></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked($incluir)></td>
                            <td class="text-center"><input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked($nullable)></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $referenced_table }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $referenced_column }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $referenced_label }}"></td>
                            <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $select_list_data }}"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <input type="checkbox" name="force_controlador" value="1"> Reemplazar controlador existente

        <div class="mt-3">
            <label><input type="checkbox" name="timestamps" value="1" @checked(session('timestamps', true))> Usar timestamps</label><br>
            <label><input type="checkbox" name="sincronizable" value="1" @checked(session('sincronizable', false))> Es sincronizable</label>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary">💾 Guardar configuración</button>
        </div>
    </form>
</div>
@endsection
