{{-- sistemas/abms/partials/subform_list.blade.php --}}
<div class="mb-3">
<form action="{{ route('sistemas.abms.preview', ['modelo' => $modelo]) }}" method="GET">
        
        <input type="hidden" name="accion" value="agregar_subform">
        <div class="row g-2">
            <div class="col-md-3"><input name="nuevo_subform[modelo]" class="form-control form-control-sm" placeholder="Modelo hijo"></div>
            <div class="col-md-2"><input name="nuevo_subform[foreign_key]" class="form-control form-control-sm" placeholder="Foreign key"></div>
            <div class="col-md-2"><input name="nuevo_subform[nombre]" class="form-control form-control-sm" placeholder="Nombre técnico"></div>
            <div class="col-md-2"><input name="nuevo_subform[titulo]" class="form-control form-control-sm" placeholder="Título visible"></div>
            <div class="col-md-1">
                <select name="nuevo_subform[view_type]" class="form-select form-select-sm">
                    <option value="inline">Inline</option>
                    <option value="modal">Modal</option>
                    <option value="tab">Tab</option>
                </select>
            </div>
            <div class="col-md-1">
                <select name="nuevo_subform[modo]" class="form-select form-select-sm">
                    <option value="inline">Inline</option>
                    <option value="modal">Modal</option>
                    <option value="tab">Tab</option>
                </select>
            </div>
            <div class="col-md-1"><button type="submit" class="btn btn-primary btn-sm w-100">➕</button></div>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th>Modelo</th>
                <th>Nombre</th>
                <th>FK</th>
                <th>View Type</th>
                <th>Título</th>
                <th>Modo</th>
                <th>Orden</th>
                <th>Cargar Campos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subformularios as $index => $sub)
                <tr>
                    <td>{{ $sub['modelo'] }}</td>
                    <td>{{ $sub['nombre'] }}</td>
                    <td>{{ $sub['foreign_key'] }}</td>
                    <td>{{ $sub['view_type'] }}</td>
                    <td>{{ $sub['titulo'] }}</td>
                    <td>{{ $sub['modo'] }}</td>
                    <td><input type="number" name="subformularios[{{ $index }}][orden]" value="{{ $sub['orden'] ?? $index }}" class="form-control form-control-sm" style="width: 70px;"></td>
                    <td>
                        <form action="{{ url('sistemas/abms/preview/' . $modelo) }}" method="GET" class="d-inline">
                            <input type="hidden" name="subform_index" value="{{ $index }}">
                            <input type="hidden" name="modelo_hijo" value="{{ $sub['modelo'] }}">
                            <button type="submit" class="btn btn-sm btn-secondary">🔄</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
