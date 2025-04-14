@php
    $registro = $registro ?? [];
@endphp

@foreach ($campos as $campo => $meta)
    @php
        $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
        $tipo = $meta['input_type'] ?? 'text';
        $esOculto = $tipo === 'hidden';
        $esSelectList = $tipo === 'select_list';
        $esSelect = $tipo === 'select';
        $inputId = 'input_' . $campo;

        $valor = old($campo,
            isset($registro[$campo]) ? $registro[$campo] :
            (is_object($registro) && isset($registro->$campo) ? $registro->$campo :
            ($defaults[$campo] ?? ''))
        );
    @endphp

    @if (!$esOculto)
        <div class="mb-3">
            <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>
    @endif

    @if ($campo === 'id')
        <input type="text" name="{{ $campo }}" class="form-control" value="{{ $valor }}" readonly>

    @elseif ($tipo === 'textarea')
        <textarea name="{{ $campo }}" id="{{ $inputId }}" class="form-control" rows="3">{{ $valor }}</textarea>

    @elseif ($esSelect && !empty($meta['referenced_table']))
        <select name="{{ $campo }}" id="{{ $inputId }}" class="form-select">
            <option value="">Seleccione una opción</option>
            @foreach ($opciones["{$campo}_opciones"] ?? [] as $op)
                <option value="{{ $op->{$meta['referenced_column']} }}"
                    {{ $valor == $op->{$meta['referenced_column']} ? 'selected' : '' }}>
                    {{ $op->{$meta['referenced_label']} }}
                </option>
            @endforeach
        </select>

    @elseif ($esSelectList && !empty($meta['select_list_data']))
        @php $lista = explode(',', $meta['select_list_data']); @endphp
        <select name="{{ $campo }}" id="{{ $inputId }}" class="form-select">
            <option value="">Seleccione una opción</option>
            @foreach ($lista as $opcion)
                @php [$text, $val] = array_pad(explode('=', $opcion, 2), 2, $opcion); @endphp
                <option value="{{ $val }}" {{ $valor == $val ? 'selected' : '' }}>{{ $text }}</option>
            @endforeach
        </select>

    @elseif ($tipo === 'checkbox')
        <input type="hidden" name="{{ $campo }}" value="{{ $meta['checkbox_unchecked_value'] ?? 'N' }}">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="{{ $campo }}" id="{{ $inputId }}"
                value="{{ $meta['checkbox_checked_value'] ?? 'S' }}"
                {{ $valor === ($meta['checkbox_checked_value'] ?? 'S') ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $inputId }}">Sí</label>
        </div>

    @else
        <input type="{{ $tipo }}" name="{{ $campo }}" id="{{ $inputId }}" class="form-control" value="{{ $valor }}">
    @endif

    @if (!$esOculto)
        </div>
    @else
        <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
    @endif
@endforeach
