{{-- 📄 Componente: select-multiple.blade.php --}}
<th>
    <select name="{{ $campo }}[]" class="form-control form-control-sm select2" multiple>
        @foreach ($opciones as $valor => $texto)
            <option value="{{ $valor }}" {{ collect(request($campo))->contains($valor) ? 'selected' : '' }}>
                {{ $texto }}
            </option>
        @endforeach
    </select>
</th>

