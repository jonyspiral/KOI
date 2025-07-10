<div class="row">
    <div class="col-md-6 mb-3">
        <label for="ml_denominacion" class="form-label">Título Mercado Libre</label>
        <input type="text" class="form-control" id="ml_denominacion" name="ml_denominacion" value="{{ old('ml_denominacion', $articulo->ml_denominacion) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="mlibre_precio" class="form-label">Precio Mercado Libre</label>
        <input type="number" step="0.01" class="form-control" id="mlibre_precio" name="mlibre_precio" value="{{ old('mlibre_precio', $articulo->mlibre_precio) }}">
    </div>

    <div class="col-md-12 mb-3">
        <label for="ml_description" class="form-label">Descripción Mercado Libre</label>
        <textarea class="form-control" id="ml_description" name="ml_description" rows="3">{{ old('ml_description', $articulo->ml_description) }}</textarea>
    </div>

    <div class="col-md-6 mb-3">
        <label for="ecommerce_name" class="form-label">Nombre en Eshop</label>
        <input type="text" class="form-control" id="ecommerce_name" name="ecommerce_name" value="{{ old('ecommerce_name', $articulo->ecommerce_name) }}">
    </div>

    <div class="col-md-6 mb-3">
        <label for="ecommerce_price1" class="form-label">Precio Eshop</label>
        <input type="number" step="0.01" class="form-control" id="ecommerce_price1" name="ecommerce_price1" value="{{ old('ecommerce_price1', $articulo->ecommerce_price1) }}">
    </div>

    <div class="col-12 mb-3">
        <label for="imagenes" class="form-label">Imágenes</label>
        <input type="file" name="imagenes[]" id="imagenes" class="form-control" multiple>
        {{-- Mostrar miniaturas si ya tiene --}}
        @if (!empty($articulo->imagenes))
            <div class="mt-2 d-flex flex-wrap gap-2">
                @foreach (explode(',', $articulo->imagenes) as $img)
                    <img src="{{ asset('storage/articulos/' . trim($img)) }}" height="80" alt="img" class="border rounded">
                @endforeach
            </div>
        @endif
    </div>
</div>
