{{-- 🌟 Parcial: Campos del ABM --}}

<table class="table table-bordered table-sm">
    <thead class="table-light">
        <tr>
            <th>Campo</th>
            <th>Tipo SQL</th>
            <th>Label</th>
            <th>Tipo Input</th>
            <th>Default</th>
            <th class="text-center">
                Incluir
                <button type="button" class="btn btn-sm btn-outline-secondary btn-toggle-incluir ms-2"
                        onclick="toggleIncluir(this)" title="Activar/Desactivar todos los incluir">
                    🔁
                </button>
            </th>
            <th>Sync</th>
            <th>Nullable</th>
            <th>Tabla FK</th>
            <th>Columna FK</th>
            <th>Label FK</th>
            <th>Valores</th>
            <th>Tab</th>
        </tr>
    </thead>
    <tbody>
        @php
            $modelSql = "\\App\\Models\\Sql\\{$modelo}";
            $fieldsMeta = method_exists($modelSql, 'fieldsMeta') ? $modelSql::fieldsMeta() : [];
        @endphp

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
                <td class="text-muted">{{ $fieldsMeta[$campo]['type'] ?? 'n/a' }}</td>
                <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                <td>
                    <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                        @foreach (['text','number','decimal','moneda','date','checkbox','textarea','select','select_list','hidden','email','password','file','color','url','tel','autonumerico'] as $tipo)
                            <option value="{{ $tipo }}" @selected($inputType === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $default }}"></td>
                <td class="text-center">
                    <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked($incluir)>
                </td>
                <td class="text-center">
                    <input type="hidden" name="campos[{{ $campo }}][sync]" value="0">
                    <input type="checkbox" name="campos[{{ $campo }}][sync]" value="1" @checked(!empty($meta['sync']))>
                </td>
                <td class="text-center">
                    <input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked($nullable)>
                </td>
                <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $referenced_table }}"></td>
                <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $referenced_column }}"></td>
                <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $referenced_label }}"></td>
                <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $select_list_data }}"></td>
                <td>
                    <select name="campos[{{ $campo }}][tab]" class="form-select form-select-sm">
                        <option value="general" {{ (old("campos.$campo.tab", $meta['tab'] ?? 'general') == 'general') ? 'selected' : '' }}>General</option>
                        <option value="tecnicos" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'tecnicos') ? 'selected' : '' }}>Atributos Técnicos</option>
                        <option value="produccion" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'produccion') ? 'selected' : '' }}>Producción</option>
                        <option value="ecommerce" {{ (old("campos.$campo.tab", $meta['tab'] ?? '') == 'ecommerce') ? 'selected' : '' }}>E-commerce</option>
                    </select>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
