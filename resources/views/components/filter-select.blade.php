<select name="{{ $campo }}" class="form-control form-control-sm">
    <option value="">--</option>
    @foreach ($opciones as $val => $label)
        <option value="{{ $val }}" {{ request($campo) == $val ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>