<div class="row">
    <div class="col-md-4 mb-3">
        <label for="cod_articulo" class="form-label">Código</label>
        <input type="text" class="form-control" id="cod_articulo" name="cod_articulo" value="{{ old('cod_articulo', $articulo->cod_articulo) }}" required>
    </div>

    <div class="col-md-8 mb-3">
        <label for="denom_articulo" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="denom_articulo" name="denom_articulo" value="{{ old('denom_articulo', $articulo->denom_articulo) }}" required>
    </div>

    <div class="col-md-4 mb-3">
        <label for="unidad" class="form-label">Unidad</label>
        <input type="text" class="form-control" id="unidad" name="unidad" value="{{ old('unidad', $articulo->unidad) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="vigente" class="form-label">¿Vigente?</label>
        <select name="vigente" id="vigente" class="form-select">
            <option value="S" {{ old('vigente', $articulo->vigente) == 'S' ? 'selected' : '' }}>Sí</option>
            <option value="N" {{ old('vigente', $articulo->vigente) == 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label for="cod_familia_producto" class="form-label">Familia</label>
        <select name="cod_familia_producto" id="cod_familia_producto" class="form-select">
            @foreach($familias as $familia)
                <option value="{{ $familia->id }}" {{ old('cod_familia_producto', $articulo->cod_familia_producto) == $familia->id ? 'selected' : '' }}>
                    {{ $familia->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $articulo->descripcion) }}</textarea>
    </div>
</div>
