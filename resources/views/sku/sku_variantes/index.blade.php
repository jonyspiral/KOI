@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">🔍 Filtro de SKU Variantes</h2>

    <form method="GET" action="{{ route('sku.sku_variantes.index') }}" class="row g-3 mb-4">

    @php
        $camposTexto = [
            'sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo',
            'familia', 'color', 'talle', 'precio',
            'ml_price', 'eshop_price', 'segunda_price'
        ];
    @endphp

    @foreach($camposTexto as $campo)
        <div class="col-md-2">
            <input type="text" name="{{ $campo }}" value="{{ request($campo) }}" class="form-control" placeholder="{{ ucfirst(str_replace('_', ' ', $campo)) }}">
        </div>
    @endforeach

    <div class="col-md-3">
        <select name="id_tipo_producto_stock[]" class="form-select select2" multiple>
            @foreach($tiposProducto as $key => $value)
                <option value="{{ $key }}" {{ collect(request('id_tipo_producto_stock'))->contains($key) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select name="cod_linea[]" class="form-select select2" multiple>
            @foreach($lineasProducto as $key => $value)
                <option value="{{ $key }}" {{ collect(request('cod_linea'))->contains($key) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">🔎 Buscar</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('sku.sku_variantes.index') }}" class="btn btn-secondary w-100">🧹 Limpiar</a>
    </div>
    <div class="col-md-2">
        <a href="{{ route('sku.sku_variantes.index', ['reset' => 1]) }}" class="btn btn-danger w-100">❌ Borrar filtros guardados</a>
    </div>
    </form>

    <form method="GET" action="{{ route('sku.sku_variantes.exportar') }}">
        @foreach(request()->all() as $key => $value)
            @if(is_array($value))
                @foreach($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <button type="submit" class="btn btn-success">📥 Exportar a Excel</button>
    </form>

    @if($registros->count())
        <table class="table table-striped table-hover table-sm">
            <thead>
                <tr>
                    @php
                        $headers = [
                            'sku' => 'Agrupador',
                            'var_sku' => 'Var SKU',
                            'ml_name' => 'Título',
                            'cod_articulo' => 'Artículo',
                            'cod_color_articulo' => 'Code Color',
                            'familia' => 'Familia',
                            'color' => 'Color',
                            'talle' => 'Talle',
                            'precio' => 'Precio',
                            'ml_price' => 'ML $',
                            'eshop_price' => 'Eshop $',
                            'segunda_price' => '2da $',
                            'stock' => 'Stock 1ra',
                            'stock_ecommerce' => 'Stock Tienda',
                            'stock_2da' => 'Stock 2da',
                            'stock_fulfillment' => 'Full',
                            'id_tipo_producto_stock' => 'Tipo producto ID',
                            'tipo_producto' => 'Tipo producto',
                            'cod_linea' => 'Línea',
                            'linea' => 'Nombre Línea',
                        ];
                    @endphp
                    @foreach($headers as $key => $label)
                        <th>
                            <a href="{{ route('sku.sku_variantes.index', array_merge(request()->all(), ['sort' => $key, 'dir' => request('dir') === 'asc' ? 'desc' : 'asc'])) }}">
                                {{ $label }}
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
                    <td>${{ number_format($r->ml_price, 2) }}</td>
                    <td>${{ number_format($r->eshop_price, 2) }}</td>
                    <td>${{ number_format($r->segunda_price, 2) }}</td>
                    <td>{{ $r->stock }}</td>
                    <td>{{ $r->stock_ecommerce }}</td>
                    <td>{{ $r->stock_2da }}</td>
                    <td>{{ $r->stock_fulfillment }}</td>
                    <td>{{ $r->id_tipo_producto_stock }}</td>
                    <td>{{ $r->tipoProductoStock->denom_tipo_producto ?? '-' }}</td>
                    <td>{{ $r->cod_linea }}</td>
                    <td>{{ $r->lineaProducto->denom_linea ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        <tfoot class="table-light fw-bold">
    <tr>
        <td colspan="12">Totales:</td>
        <td>{{ $totales['total'] }}</td>
        <td>{{ $totales['ecommerce_total'] }}</td>
        <td>{{ $totales['segunda_total'] }}</td>
        <td>{{ $totales['fulfillment_total'] }}</td>
        <td colspan="4"></td>
    </tr>
</tfoot>
        </table>

        {{ $registros->links() }}
    @else
        <div class="alert alert-warning">No se encontraron resultados.</div>
    @endif
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccionar...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
