@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✏️ Editar registro de {{ $modelo }}</h2>

    <form action="{{ route('__NOMBRE_RUTA__.update', $registro[$primaryKey]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                @foreach ($campos as $campo => $config)
    @php
        $inputType = $config['input_type'] ?? 'text';
        $label = $config['label'] ?? ucfirst(str_replace('_', ' ', $campo));
        $value = old($campo,
            isset($registro[$campo]) ? $registro[$campo] :
            (is_object($registro) && isset($registro->$campo) ? $registro->$campo : '')
        );

        // Formatear fechas si el input es tipo date
        if ($inputType === 'date') {
            if ($value instanceof \Carbon\Carbon) {
                $value = $value->format('Y-m-d');
            } elseif (is_string($value) && strtotime($value)) {
                $value = date('Y-m-d', strtotime($value));
            }
        }

        $isAutonumerico = $inputType === 'autonumerico';
        $isTextarea = $inputType === 'textarea';
        $isCheckbox = $inputType === 'checkbox';
        $isSelectList = $inputType === 'select_list';
        $isSelect = $inputType === 'select';
        $isHidden = $inputType === 'hidden';
        $isDecimal = $inputType === 'decimal';
        $isMoneda = $inputType === 'moneda';
        $inputId = 'input_' . $campo;
        $selectOptions = $opciones["{$campo}_opciones"] ?? collect();
    @endphp

    @if ($isHidden)
        <input type="hidden" name="{{ $campo }}" value="{{ $value }}">
    @else
        <div class="col-md-6 mb-3">
            <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>

            @if ($isCheckbox)
                <input type="hidden" name="{{ $campo }}" value="N">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="{{ $campo }}" id="{{ $inputId }}" value="S"
                           {{ $value === 'S' ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $inputId }}">Sí</label>
                </div>

            @elseif ($isSelect)
                <select class="form-select" name="{{ $campo }}" id="{{ $inputId }}" {{ empty($config['nullable']) ? 'required' : '' }}>
                    <option value="">Seleccione una opción</option>
                    @foreach ($selectOptions as $opt)
                        <option value="{{ $opt->{$config['referenced_column']} }}"
                            {{ $value == $opt->{$config['referenced_column']} ? 'selected' : '' }}>
                            {{ $opt->{$config['referenced_label']} }}
                        </option>
                    @endforeach
                </select>

            @elseif ($isSelectList && !empty($config['select_list_data']))
                @php $opcionesList = explode(',', $config['select_list_data']); @endphp
                <select class="form-select" name="{{ $campo }}" id="{{ $inputId }}" {{ empty($config['nullable']) ? 'required' : '' }}>
                    <option value="">Seleccione una opción</option>
                    @foreach ($opcionesList as $opcion)
                        @php [$texto, $val] = array_map('trim', array_pad(explode('=', $opcion, 2), 2, $opcion)); @endphp
                        <option value="{{ $val }}" {{ $value == $val ? 'selected' : '' }}>{{ $texto }}</option>
                    @endforeach
                </select>

            @elseif ($isAutonumerico)
                <input type="text" class="form-control" name="{{ $campo }}" id="{{ $inputId }}" value="{{ $value }}" readonly>

            @elseif ($isTextarea)
                <textarea class="form-control" name="{{ $campo }}" id="{{ $inputId }}" rows="3">{{ $value }}</textarea>

            @elseif ($inputType === 'number' || $isDecimal || $isMoneda)
                <input type="number" step="any" class="form-control {{ $isMoneda ? 'input-moneda' : '' }}"
                    name="{{ $campo }}" id="{{ $inputId }}" value="{{ $value }}" {{ empty($config['nullable']) ? 'required' : '' }}>

            @elseif ($inputType === 'date')
                <input type="date" class="form-control" name="{{ $campo }}" id="{{ $inputId }}" value="{{ $value }}" {{ empty($config['nullable']) ? 'required' : '' }}>

            @else
                <input type="{{ $inputType }}" class="form-control" name="{{ $campo }}" id="{{ $inputId }}" value="{{ $value }}" {{ empty($config['nullable']) ? 'required' : '' }}>
            @endif
        </div>
    @endif
@endforeach

                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
            <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
        </div>
    </form>
</div>
@endsection
