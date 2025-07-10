<div class="row">
    <div class="col-md-4 mb-3">
        <label for="sync_status" class="form-label">Estado de Sincronización</label>
        <select name="sync_status" id="sync_status" class="form-select">
            <option value="">(sin estado)</option>
            <option value="N" {{ old('sync_status', $articulo->sync_status) == 'N' ? 'selected' : '' }}>Nuevo</option>
            <option value="U" {{ old('sync_status', $articulo->sync_status) == 'U' ? 'selected' : '' }}>Actualizado</option>
            <option value="D" {{ old('sync_status', $articulo->sync_status) == 'D' ? 'selected' : '' }}>Eliminado</option>
            <option value="S" {{ old('sync_status', $articulo->sync_status) == 'S' ? 'selected' : '' }}>Sincronizado</option>
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label for="fecha_ultima_modificacion" class="form-label">Última Modificación</label>
        <input type="text" class="form-control" id="fecha_ultima_modificacion" name="fecha_ultima_modificacion"
               value="{{ old('fecha_ultima_modificacion', $articulo->fecha_ultima_modificacion) }}" readonly>
    </div>

    <div class="col-md-4 mb-3">
        <label for="autor_ultima_modificacion" class="form-label">Autor</label>
        <input type="text" class="form-control" id="autor_ultima_modificacion" name="autor_ultima_modificacion"
               value="{{ old('autor_ultima_modificacion', $articulo->autor_ultima_modificacion) }}" readonly>
    </div>
</div>
