@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🔍 Filtro de SKU Variantes</h2>

    <form method="GET" action="{{ route('sku.sku_variantes.index') }}" class="row g-3 mb-4">
        @foreach(['sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo', 'familia', 'color', 'talle', 'precio'] as $campo)
            <div class="col-md-2">
                <input type="text" name="{{ $campo }}" value="{{ request($campo) }}" class="form-control" placeholder="{{ ucfirst(str_replace('_', ' ', $campo)) }}">
            </div>
        @endforeach
        <div class="col-md-2">
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
                    @foreach(['sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo', 'familia', 'color', 'talle', 'precio', 'stock', 'stock_ecommerce', 'stock_2da', 'stock_fulfillment'] as $campo)
                        <th>
                            <a href="{{ route('sku.sku_variantes.index', array_merge(request()->all(), ['sort' => $campo, 'dir' => request('dir') === 'asc' ? 'desc' : 'asc'])) }}">
                                {{ ucfirst(str_replace('_', ' ', $campo)) }}
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $r)
                <tr>
                    <td>{{ $r->sku }}</td>
                    <td>{{ $r->var_sku }}</td>
                    <td>{{ $r->ml_name }}</td>
                    <td>{{ $r->cod_articulo }}</td>
                    <td>{{ $r->cod_color_articulo }}</td>
                    <td>{{ $r->familia }}</td>
                    <td>{{ $r->color }}</td>
                    <td>{{ $r->talle }}</td>
                    <td>${{ number_format($r->precio, 2) }}</td>
                    <td>{{ $r->stock }}</td>
                    <td>{{ $r->stock_ecommerce }}</td>
                    <td>{{ $r->stock_2da }}</td>
                    <td>{{ $r->stock_fulfillment }}</td>
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
