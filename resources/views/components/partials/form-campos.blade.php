@php
    $registro = $registro ?? [];
    // 👉 Si hay fechas, formatearlas a Y-m-d para que los input type="date" las interpreten bien
    foreach ($campos as $campo => $meta) {
        if (($meta['input_type'] ?? '') === 'date') {
            $valor = $registro[$campo] ?? null;

            if (!empty($valor) && strtotime($valor)) {
                $registro[$campo] = date('Y-m-d', strtotime($valor));
            }
        }
    }
    // 🔢 Ordenar campos por 'orden' si está definido
    uksort($campos, fn($a, $b) => ($campos[$a]['orden'] ?? 0) <=> ($campos[$b]['orden'] ?? 0));
@endphp
@foreach ($campos as $campo => $meta)
@php
    $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
    $tipo = $meta['input_type'] ?? 'text';
    $esOculto = $tipo === 'hidden';
    $esSelectList = $tipo === 'select_list';
    $esSelect = $tipo === 'select';
    $esDecimal = $tipo === 'decimal';   
    $esMoneda = $tipo === 'moneda';
    $inputId = 'input_' . $campo;
    $readonly = !empty($meta['readonly']);
    $required = empty($meta['nullable']) && !$readonly;

    // 🧠 Valor con prioridad: old > registro > default (incluso si default es false)
    $valor = old($campo);
    if ($valor === null) {
        $valor = $registro[$campo] ?? ($registro->$campo ?? ($defaults[$campo] ?? null));
    }

    // 🧩 Ajuste para checkbox (controlar 'S' / 'N')
    if ($tipo === 'checkbox') {
    $checkedValue = $meta['checkbox_checked_value'] ?? 'S';
    $uncheckedValue = $meta['checkbox_unchecked_value'] ?? 'N';

    if (is_bool($valor)) {
        $valor = $valor ? $checkedValue : $uncheckedValue;
    }
}

    
@endphp


    @if (!$esOculto)
        <div class="mb-3">
            <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>
    @endif

    @if ($campo === 'id')
        <input type="text" name="{{ $campo }}" class="form-control" value="{{ $valor }}" readonly>
        
        @elseif ($tipo === 'autonumerico')
    <input 
        type="number" 
        name="{{ $campo }}" 
        id="{{ $inputId }}" 
        class="form-control" 
        value="{{ $valor ?? ($defaults[$campo] ?? '') }}" 
        readonly
    >


    @elseif ($tipo === 'textarea')
        <textarea name="{{ $campo }}" id="{{ $inputId }}" class="form-control" rows="3"
            @if($readonly) readonly @endif @if($required) required @endif>{{ $valor }}</textarea>

        @elseif ($esSelect && !empty($meta['referenced_table']))
        <select name="{{ $campo }}" id="{{ $inputId }}" class="form-select"
            @if($readonly) disabled @endif @if($required) required @endif>
            <option value="">Seleccione una opción</option>
            @foreach ($opciones["{$campo}_opciones"] ?? [] as $op)
                <option value="{{ $op->{$meta['referenced_column']} }}"
                    {{ $valor == $op->{$meta['referenced_column']} ? 'selected' : '' }}>
                    {{ $op->{$meta['referenced_label']} }}
                </option>
            @endforeach
        </select>

        @elseif ($esSelectList && !empty($meta['select_list_data']))
        @php
            $lista = explode(',', $meta['select_list_data']);
        @endphp
        <select name="{{ $campo }}" id="{{ $inputId }}" class="form-select"
            @if($readonly) disabled @endif @if($required) required @endif>
            <option value="">Seleccione una opción</option>
            @foreach ($lista as $opcion)
                @php
                    [$texto, $valorOption] = array_map('trim', array_pad(explode('=', $opcion, 2), 2, $opcion));
                @endphp
                <option value="{{ $valorOption }}" {{ $valor == $valorOption ? 'selected' : '' }}>
                    {{ $texto }}
                </option>
            @endforeach
        </select>
   
        @elseif ($tipo === 'checkbox')
        <input type="hidden" name="{{ $campo }}" value="{{ $uncheckedValue }}">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="{{ $campo }}" id="{{ $inputId }}"
                value="{{ $checkedValue }}" {{ $valor == $checkedValue ? 'checked' : '' }}
                @if($readonly) disabled @endif>
            <label class="form-check-label" for="{{ $inputId }}">Sí</label>
        </div>

    @elseif (in_array($tipo, ['text', 'email', 'url', 'color', 'password']))
        <input type="{{ $tipo }}" name="{{ $campo }}" id="{{ $inputId }}" class="form-control"
            value="{{ $valor }}" @if($readonly) readonly @endif @if($required) required @endif>

            @elseif ($tipo === 'number')
        <input type="number" name="{{ $campo }}" id="{{ $inputId }}" class="form-control"
            value="{{ $valor }}" step="any" @if($readonly) readonly @endif @if($required) required @endif>

        @elseif ($tipo === 'date')
        <input type="date" name="{{ $campo }}" id="{{ $inputId }}" class="form-control"
            value="{{ $valor }}" @if($readonly) readonly @endif @if($required) required @endif>

            @elseif ($esDecimal || $esMoneda)
        <input type="number" name="{{ $campo }}" id="{{ $inputId }}"
            class="form-control {{ $esMoneda ? 'input-moneda' : '' }}" value="{{ $valor }}" step="0.01"
            @if($readonly) readonly @endif @if($required) required @endif>

            @elseif ($tipo === 'file')
        <input type="file" name="{{ $campo }}" id="{{ $inputId }}" class="form-control"
            @if($required) required @endif>
        @if (!empty($valor))
            <div class="form-text">Archivo actual: <code>{{ basename($valor) }}</code></div>
        @endif
        
        @else
        <input type="text" name="{{ $campo }}" id="{{ $inputId }}" class="form-control"
            value="{{ $valor }}" @if($readonly) readonly @endif @if($required) required @endif>
    @endif

    @if (!$esOculto)
        </div>
    @else
        <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
    @endif
@endforeach