@props(['campo', 'meta', 'sub'])

@php
    $inputType = $meta['input_type'] ?? 'text';
    $valor = $sub->$campo ?? '';
    $primaryKey = $meta['primary_key'] ?? $sub->getKeyName(); // fallback si no llega desde la vista
    $formId = "edit-form-{$sub->{$primaryKey}}";
@endphp

@if ($inputType === 'select' && !empty($meta['referenced_table']))
    @php
        $options = \DB::table($meta['referenced_table'])->pluck(
            $meta['referenced_label'] ?? 'nombre',
            $meta['referenced_column'] ?? 'id'
        );
    @endphp
    <select name="{{ $campo }}" class="form-select form-select-sm" form="{{ $formId }}" {{ empty($meta['nullable']) ? 'required' : '' }}>
        <option value="">Seleccione...</option>
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected($valor == $key)>{{ $label }}</option>
        @endforeach
    </select>

@elseif ($inputType === 'select_list' && !empty($meta['select_list_data']))
    @php
        $parsed = collect();
        foreach (explode(',', $meta['select_list_data']) as $item) {
            [$label, $value] = array_map('trim', explode('=', $item));
            $parsed->put($value, $label);
        }
    @endphp
    <select name="{{ $campo }}" class="form-select form-select-sm" form="{{ $formId }}" {{ empty($meta['nullable']) ? 'required' : '' }}>
        <option value="">Seleccione...</option>
        @foreach ($parsed as $key => $label)
            <option value="{{ $key }}" @selected($valor == $key)>{{ $label }}</option>
        @endforeach
    </select>

@elseif ($inputType === 'checkbox')
    @php
        $checkedValue = $meta['checkbox_checked_value'] ?? 'S';
        $uncheckedValue = $meta['checkbox_unchecked_value'] ?? 'N';
    @endphp
    <div class="form-check">
        <input type="hidden" name="{{ $campo }}" value="{{ $uncheckedValue }}" form="{{ $formId }}">
        <input type="checkbox"
               name="{{ $campo }}"
               value="{{ $checkedValue }}"
               class="form-check-input"
               form="{{ $formId }}"
               @checked($valor === $checkedValue)>
    </div>

@else
    <input
        type="{{ $inputType }}"
        name="{{ $campo }}"
        value="{{ $valor }}"
        class="form-control form-control-sm"
        form="{{ $formId }}"
        {{ empty($meta['nullable']) ? 'required' : '' }}
    >
@endif
