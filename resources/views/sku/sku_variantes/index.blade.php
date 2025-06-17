@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🔍 Filtro de SKU Variantes</h2>

    <form method="GET" action="{{ route('sku.sku_variantes.index') }}" class="row g-3 mb-4">

    {{-- Campos de texto --}}
    @foreach(['sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo', 'familia', 'color', 'talle', 'precio'] as $campo)
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
                    @foreach([
                        'Agrupador', 'var_sku', 'Titulo', 'articulo', 'code color',
                        'Familia', 'color', 'talle', 'precio', 'stock Ml', 'stock Tienda',
                        'Stock 2da', 'Full', 'id', 'Tipo producto',
                        'id', 'linea'
                    ] as $campo)
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
                    <td>{{ $r->stock }}</td>
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
        <td colspan="9">Totales:</td>
        <td>{{ $totales->stock_total ?? 0 }}</td>
        <td>{{ $totales->stock_total ?? 0 }}</td>
        <td>{{ $totales->stock_2da_total ?? 0 }}</td>
        <td>{{ $totales->stock_fulfillment_total ?? 0 }}</td>
        <td colspan="4"></td> {{-- para completar columnas si agregaste nuevas --}}
    </tr>
</tfoot>
        </table>

        {{ $registros->links() }}
    @else
        <div class="alert alert-warning">No se encontraron resultados.</div>
    @endif
</div>
@endsection
@push('scripts')
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
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
