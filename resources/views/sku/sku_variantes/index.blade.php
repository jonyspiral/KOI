@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🔍 Filtro de SKU Variantes</h2>
<form method="GET" action="{{ route('sku.sku_variantes.index') }}" class="row g-3 mb-4">
    
            <input type="text" name="sku" value="{{ request('sku') }}" class="form-control" placeholder="SKU">
        </div>
        <div class="col-md-3">
            <input type="text" name="var_sku" value="{{ request('var_sku') }}" class="form-control" placeholder="Var SKU">
        </div>
        <div class="col-md-2">
            <input type="text" name="cod_articulo" value="{{ request('cod_articulo') }}" class="form-control" placeholder="Artículo">
        </div>
        <div class="col-md-2">
            <input type="text" name="cod_color_articulo" value="{{ request('cod_color_articulo') }}" class="form-control" placeholder="Color Artículo">
        </div>
        <div class="col-md-2">
            <input type="text" name="ml_name" value="{{ request('ml_name') }}" class="form-control" placeholder="ML Name">
        </div>
        <div class="col-md-3">
            <input type="text" name="color" value="{{ request('color') }}" class="form-control" placeholder="Color Descriptivo">
        </div>
        <div class="col-md-2">
            <input type="text" name="talle" value="{{ request('talle') }}" class="form-control" placeholder="Talle">
        </div>
        <div class="col-md-2">
            <input type="text" name="precio" value="{{ request('precio') }}" class="form-control" placeholder="Precio">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">🔎 Buscar</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('sku.sku_variantes.index') }}" class="btn btn-secondary w-100">🧹 Limpiar</a>

        </div>
    </form>

    @if($registros->count())
        <table class="table table-striped table-hover table-sm">
            <thead>
                <tr>
                    <th>Var SKU</th>
                    <th>SKU</th>
                    <th>Artículo</th>
                    <th>Color</th>
                    <th>Talle</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $r)
                <tr>
                    <td>{{ $r->var_sku }}</td>
                    <td>{{ $r->sku }}</td>
                    <td>{{ $r->cod_articulo }}</td>
                    <td>{{ $r->cod_color_articulo }} - {{ $r->color }}</td>
                    <td>{{ $r->talle }}</td>
                    <td>${{ $r->precio }}</td>
                    <td>{{ $r->stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $registros->links() }}
    @else
        <div class="alert alert-warning">No se encontraron resultados.</div>
    @endif
</div>
@endsection
