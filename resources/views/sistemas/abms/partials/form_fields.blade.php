{{-- sistemas/abms/partials/form_fields.blade.php --}}
<div class="table-responsive mb-4">
 

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Orden</th>
                <th>Campo</th>
                <th>Tipo SQL</th>
                <th>Label</th>
                <th>Tipo Input</th>
                <th>Default</th>
                <th class="text-center">Incluir</th>
                <th class="text-center">Sync</th>
                <th class="text-center">Nullable</th>
                <th class="text-center">Readonly</th>
                <th>Tabla FK</th>
                <th>Columna FK</th>
                <th>Label FK</th>
                <th>Valores</th>
            </tr>
        </thead>
        <tbody>
            @php
                $modelSql = "\\App\\Models\\Sql\\{$modelo}";
                $fieldsMeta = method_exists($modelSql, 'fieldsMeta') ? $modelSql::fieldsMeta() : [];
            @endphp
            @if (!empty($fields))
                @foreach ($fields as $campo => $meta)
                    @php
                        $label = $meta['label'] ?? ucfirst(str_replace('_', ' ', $campo));
                        $typeSql = $fieldsMeta[$campo]['type'] ?? 'n/a';
                    @endphp
                    <tr>
                        <td><input type="number" name="campos[{{ $campo }}][orden]" class="form-control form-control-sm" value="{{ $meta['orden'] ?? 0 }}" style="width: 60px;"></td>
                        <td>{{ $campo }}</td>
                        <td class="text-muted">{{ $typeSql }}</td>
                        <td><input type="text" name="campos[{{ $campo }}][label]" class="form-control form-control-sm" value="{{ $label }}"></td>
                        <td>
                            <select name="campos[{{ $campo }}][input_type]" class="form-select form-select-sm">
                                @foreach ([
                                    'text','number','decimal','moneda','date','checkbox','textarea','select','select_list',
                                    'hidden','email','password','file','color','url','tel','autonumerico'
                                ] as $tipo)
                                    <option value="{{ $tipo }}" @selected(($meta['input_type'] ?? 'text') === $tipo)> {{ $tipo }} </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="campos[{{ $campo }}][default]" class="form-control form-control-sm" value="{{ $meta['default'] ?? '' }}"></td>
                        <td class="text-center">
                            <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" @checked(!empty($meta['incluir']))>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="campos[{{ $campo }}][sync]" value="0">
                            <input type="checkbox" name="campos[{{ $campo }}][sync]" value="1" @checked(!empty($meta['sync']))>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="campos[{{ $campo }}][nullable]" value="1" @checked(!empty($meta['nullable']))>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="campos[{{ $campo }}][readonly]" value="1" @checked(!empty($meta['readonly']))>
                        </td>
                        <td><input type="text" name="campos[{{ $campo }}][referenced_table]" class="form-control form-control-sm" value="{{ $meta['referenced_table'] ?? '' }}"></td>
                        <td><input type="text" name="campos[{{ $campo }}][referenced_column]" class="form-control form-control-sm" value="{{ $meta['referenced_column'] ?? 'id' }}"></td>
                        <td><input type="text" name="campos[{{ $campo }}][referenced_label]" class="form-control form-control-sm" value="{{ $meta['referenced_label'] ?? 'nombre' }}"></td>
                        <td><input type="text" name="campos[{{ $campo }}][select_list_data]" class="form-control form-control-sm" value="{{ $meta['select_list_data'] ?? '' }}"></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="14" class="text-center text-muted">⚠️ No se encontraron campos definidos en el JSON.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
