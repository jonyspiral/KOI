<div class="row">
    <div class="col-md-4 mb-3">
        <label for="precio_lista" class="form-label">Precio Lista</label>
        <input type="number" step="0.01" class="form-control" id="precio_lista" name="precio_lista" value="{{ old('precio_lista', $articulo->precio_lista) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="precio_lista_mayor" class="form-label">Precio Mayorista</label>
        <input type="number" step="0.01" class="form-control" id="precio_lista_mayor" name="precio_lista_mayor" value="{{ old('precio_lista_mayor', $articulo->precio_lista_mayor) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="precio_distribuidor" class="form-label">Precio Distribuidor</label>
        <input type="number" step="0.01" class="form-control" id="precio_distribuidor" name="precio_distribuidor" value="{{ old('precio_distribuidor', $articulo->precio_distribuidor) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="cod_rubro_iva" class="form-label">Rubro IVA</label>
        <select name="cod_rubro_iva" id="cod_rubro_iva" class="form-select">
            @foreach($rubros_iva as $rubro)
                <option value="{{ $rubro->id }}" {{ old('cod_rubro_iva', $articulo->cod_rubro_iva) == $rubro->id ? 'selected' : '' }}>
                    {{ $rubro->descripcion }}
                </option>
            @endforeach
        </select>
    </div>
</div>
