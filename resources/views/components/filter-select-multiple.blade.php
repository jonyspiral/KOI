@php
    $valores = request()->input($campo, []);
    if (!is_array($valores)) $valores = [$valores];
@endphp

<select name="{{ $campo }}[]" multiple class="form-control form-control-sm">
    @foreach ($opciones as $val => $label)
        <option value="{{ $val }}" {{ in_array($val, $valores) ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>

