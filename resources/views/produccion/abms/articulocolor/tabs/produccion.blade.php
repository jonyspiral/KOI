<div class="row">
    <div class="col-md-6 mb-3">
        <label for="cod_ruta" class="form-label">Ruta de Producción</label>
        <select name="cod_ruta" id="cod_ruta" class="form-select">
            @foreach($rutas as $ruta)
                <option value="{{ $ruta->id }}" {{ old('cod_ruta', $articulo->cod_ruta) == $ruta->id ? 'selected' : '' }}>
                    {{ $ruta->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="cod_rango" class="form-label">Rango de Talles</label>
        <select name="cod_rango" id="cod_rango" class="form-select">
            @foreach($rangos as $rango)
                <option value="{{ $rango->id }}" {{ old('cod_rango', $articulo->cod_rango) == $rango->id ? 'selected' : '' }}>
                    {{ $rango->descripcion }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="cod_horma" class="form-label">Horma</label>
        <select name="cod_horma" id="cod_horma" class="form-select">
            @foreach($hormas as $horma)
                <option value="{{ $horma->id }}" {{ old('cod_horma', $articulo->cod_horma) == $horma->id ? 'selected' : '' }}>
                    {{ $horma->descripcion }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="cod_marca" class="form-label">Marca</label>
        <select name="cod_marca" id="cod_marca" class="form-select">
            @foreach($marcas as $marca)
                <option value="{{ $marca->id }}" {{ old('cod_marca', $articulo->cod_marca) == $marca->id ? 'selected' : '' }}>
                    {{ $marca->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="forma_comercializacion" class="form-label">Forma de Comercialización</label>
        <input type="text" class="form-control" id="forma_comercializacion" name="forma_comercializacion" value="{{ old('forma_comercializacion', $articulo->forma_comercializacion) }}">
    </div>
</div>
