<div class="table-responsive p-3">
    <table class="table table-bordered table-sm table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Campo</th>
                <th>Label</th>
                <th>Tipo Input</th>
                <th>Default</th>
                <th>Orden</th>
                <th class="text-center">Incluir</th>
                <th class="text-center">Sync</th>
                <th class="text-center">Nullable</th>
                <th class="text-center">Readonly</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($camposSub as $campo => $meta)
                <tr>
                    <td><code>{{ $campo }}</code></td>
                    <td>
                        <input type="text"
                               name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][label]"
                               value="{{ $meta['label'] ?? ucfirst(str_replace('_',' ', $campo)) }}"
                               class="form-control form-control-sm">
                    </td>
                    <td>
                        <select name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][input_type]"
                                class="form-select form-select-sm">
                            @foreach (['text','number','decimal','moneda','date','checkbox','textarea','select','select_list','email','url','color','tel','file','password','hidden','autonumerico'] as $tipo)
                                <option value="{{ $tipo }}" @selected(($meta['input_type'] ?? 'text') === $tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text"
                               name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][default]"
                               value="{{ $meta['default'] ?? '' }}"
                               class="form-control form-control-sm">
                    </td>
                    <td>
                        <input type="number"
                               name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][orden]"
                               value="{{ $meta['orden'] ?? 0 }}"
                               class="form-control form-control-sm" style="width:80px">
                    </td>
                    <td class="text-center">
                    <input type="hidden"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][incluir]"
                        value="0">
                    <input type="checkbox"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][incluir]"
                        value="1" @checked(!empty($meta['incluir']))>
                </td>
                <td class="text-center">
                    <input type="hidden"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][sync]"
                        value="0">
                    <input type="checkbox"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][sync]"
                        value="1" @checked(!empty($meta['sync']))>
                </td>
                <td class="text-center">
                    <input type="hidden"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][nullable]"
                        value="0">
                    <input type="checkbox"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][nullable]"
                        value="1" @checked(!empty($meta['nullable']))>
                </td>
                <td class="text-center">
                    <input type="hidden"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][readonly]"
                        value="0">
                    <input type="checkbox"
                        name="subformularios[{{ $indexSubform }}][campos][{{ $campo }}][readonly]"
                        value="1" @checked(!empty($meta['readonly']))>
                </td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
