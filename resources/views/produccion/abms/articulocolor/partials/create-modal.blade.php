<div class="modal fade" id="modalNuevoArticulo" tabindex="-1" aria-labelledby="modalNuevoArticuloLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('articulocolor.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoArticuloLabel">Nuevo Artículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="cod_articulo" class="form-label">Código</label>
            <input type="text" class="form-control" name="cod_articulo" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="denom_articulo" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="denom_articulo" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="unidad" class="form-label">Unidad</label>
            <input type="text" class="form-control" name="unidad">
          </div>
          <div class="col-md-6 mb-3">
            <label for="vigente" class="form-label">Vigente</label>
            <select class="form-select" name="vigente">
              <option value="S">Sí</option>
              <option value="N">No</option>
            </select>
          </div>
          <div class="col-md-12 mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
           <textarea class="form-control" name="descripcion" rows="3"></textarea>

          </div>
          <div class="col-md-12 mb-3">
            <label for="cod_familia_producto" class="form-label">Familia</label>
            <select class="form-select" name="cod_familia_producto">
              @foreach ($familias as $familia)
                <option value="{{ $familia->id }}">{{ $familia->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>
