@if ($meta['input_type'] === 'select' && !empty($meta['referenced_table']))
    @php
        $options = DB::table($meta['referenced_table'])->pluck(
            $meta['referenced_label'] ?? 'nombre',
            $meta['referenced_column'] ?? 'id'
        );
        $selected = old($campo, $registro->$campo ?? $meta['default'] ?? '');
    @endphp
    <select name="{{ $campo }}" class="form-select form-select-sm" {{ empty($meta['nullable']) ? 'required' : '' }}>
        <option value="">Seleccione...</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" @selected($selected == $value)>{{ $label }}</option>
        @endforeach
    </select>

@elseif ($meta['input_type'] === 'select_list' && !empty($meta['select_list_data']))
    @php
        $parsed = collect();
        foreach (explode(',', $meta['select_list_data']) as $item) {
            [$label, $value] = array_map('trim', explode('=', $item));
            $parsed->put($value, $label);
        }
        $selected = old($campo, $meta['default'] ?? '');
    @endphp
    <select name="{{ $campo }}" class="form-select form-select-sm" {{ empty($meta['nullable']) ? 'required' : '' }}>
        <option value="">Seleccione...</option>
        @foreach ($parsed as $value => $label)
            <option value="{{ $value }}" @selected($selected == $value)>{{ $label }}</option>
        @endforeach
    </select>

@elseif ($meta['input_type'] === 'checkbox')
    <div class="form-check">
        <input type="hidden" name="{{ $campo }}" value="N">
        <input type="checkbox" name="{{ $campo }}" value="S" class="form-check-input" @checked(old($campo, $meta['default'] ?? '') === 'S')>
  
    </div>

@else
    <input
        type="{{ $meta['input_type'] ?? 'text' }}"
        name="{{ $campo }}"
        value="{{ old($campo, $meta['default'] ?? '') }}"
        class="form-control form-control-sm"
        {{ empty($meta['nullable']) ? 'required' : '' }}
    >
@endif
