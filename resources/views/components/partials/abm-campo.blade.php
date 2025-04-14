@php
    $tipo = $meta['input_type'] ?? 'text';
    $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
    $valor = $valor ?? '';
@endphp

<div class="col-md-3">
    <label class="form-label">{{ $label }}</label>

    @if ($tipo === 'select' && !empty($meta['referenced_table']))
        <select name="{{ $campo }}" class="form-select" required>
            <option value="">Seleccione</option>
            @foreach(\DB::table($meta['referenced_table'])->get() as $item)
                <option value="{{ $item->{$meta['referenced_column'] ?? 'id'} }}"
                    {{ $valor == $item->{$meta['referenced_column'] ?? 'id'} ? 'selected' : '' }}>
                    {{ $item->{$meta['referenced_label'] ?? 'nombre'} }}
                </option>
            @endforeach
        </select>

    @elseif ($tipo === 'select_list' && !empty($meta['select_list_data']))
        @php
            $opciones = collect(explode(',', $meta['select_list_data']))->mapWithKeys(function ($item) {
                [$texto, $val] = array_pad(explode('=', $item, 2), 2, $item);
                return [$val => $texto];
            });
        @endphp
        <select name="{{ $campo }}" class="form-select">
            @foreach ($opciones as $val => $texto)
                <option value="{{ $val }}" {{ $valor == $val ? 'selected' : '' }}>{{ $texto }}</option>
            @endforeach
        </select>

    @elseif ($tipo === 'checkbox')
        <div class="form-check mt-2">
            <input type="checkbox" name="{{ $campo }}" value="S" class="form-check-input"
                   {{ $valor == 'S' ? 'checked' : '' }} id="chk_{{ $campo }}">
            <label class="form-check-label" for="chk_{{ $campo }}">✔</label>
        </div>

    @else
        <input type="{{ $tipo }}" name="{{ $campo }}" class="form-control" value="{{ $valor }}">
    @endif
</div>
