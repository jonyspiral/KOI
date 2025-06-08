@extends('layouts.app')

@section('content')
<div class="container">
    <h3>✏️ Editar Publicación: {{ $publicacion->ml_id }}</h3>

    @if (isset($publicacion->raw_json['permalink']))
        <a href="{{ $publicacion->raw_json['permalink'] }}" class="btn btn-info mb-3" target="_blank">
            🌐 Ver en Mercado Libre
        </a>
    @endif

    <form method="POST" action="{{ route('mlibre.publicaciones.update', $publicacion->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="ml_name">Título</label>
            <input type="text" name="ml_name" value="{{ old('ml_name', $publicacion->ml_name) }}" class="form-control">
            @error('ml_name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="ml_description">Descripción</label>
            <textarea name="ml_description" rows="4" class="form-control">{{ old('ml_description', $publicacion->ml_description) }}</textarea>
            @error('ml_description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="ml_reference">Agrupador (ml_reference)</label>
            <input type="text" name="ml_reference" value="{{ old('ml_reference', $publicacion->ml_reference) }}" class="form-control">
            @error('ml_reference') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="mlibre_precio">Precio</label>
            <input type="number" step="0.01" name="mlibre_precio" value="{{ old('mlibre_precio', $publicacion->mlibre_precio) }}" class="form-control">
            @error('mlibre_precio') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group mb-3">
            <label for="mlibre_stock">Stock</label>
            <input type="number" name="mlibre_stock" value="{{ old('mlibre_stock', $publicacion->mlibre_stock) }}" class="form-control">
            @error('mlibre_stock') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">💾 Guardar</button>
        <a href="{{ route('mlibre.publicaciones.index') }}" class="btn btn-secondary">Volver</a>
    </form>

    @if ($publicacion->variantes->count())
        <div class="mt-4">
            <h5>🔻 Variantes de esta publicación</h5>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Color</th>
                        <th>Talle</th>
                        <th>Precio</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($publicacion->variantes as $var)
                        <tr>
                            <td>{{ $var->sku_ }}</td>
                            <td>{{ $var->color }}</td>
                            <td>{{ $var->talle }}</td>
                            <td>{{ $var->precio }}</td>
                            <td>{{ $var->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        <h5>🔍 JSON original</h5>
        <pre class="bg-light p-3" style="max-height: 300px; overflow: auto;">{{ json_encode($publicacion->raw_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>
@endsection

